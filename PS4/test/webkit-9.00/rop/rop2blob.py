import sys, json

def prepass(l):
    labels = {}
    sp_offset = 0
    for i in l:
        if i.endswith(':'):
            labels[i[:-1]] = sp_offset
        elif i.startswith('db '):
            q = bytes(list(eval('('+i[3:]+')')))
            assert len(q) % 8 == 0
            sp_offset += len(q)
        elif not i.startswith('$$'): sp_offset += 8
    return labels

def parse_gadgets(l):
    gadgets = {}
    for i in l:
        if i.count(':') < 2: continue
        file, addr, gadget = i.split(':', 2)
        assert file.endswith('-gadgets.txt')
        file = file[:-12]
        try: addr = int(addr, 16)
        except ValueError: continue
        if '//' in gadget:
            gadget, binrepr = gadget.split('//', 1)
            binrepr = bytes.fromhex(binrepr.strip())
        else:
            binrepr = None
        gadget = gadget.strip()
        if gadget == 'ret': gadget = 'nop ; ret'
        if not gadget.endswith(' ; ret'): continue
        gadget = gadget[:-6].replace(' ptr ', ' ')
        if gadget == 'movsxd rax, edi' and binrepr != None and not binrepr.startswith(b'\x48'):
            addr -= 1
        gadgets[gadget] = (file, addr)
        for i in 'byte', 'word', 'dword', 'qword':
            gadget = gadget.replace(' '+i+' ', ' ')
            gadgets[gadget] = (file, addr)
    return gadgets

def final_pass(l, ls, gs):
    ans = []
    consts = ['0']
    js = []
    js_pre = []
    def push_gadget(g):
        try: g = int(g, 0)
        except ValueError:
            if '+' in g:
                a, b = g.rsplit('+', 1)
                try: b = int(b, 0)
                except ValueError:
                    s = g
                    o = 0
                else:
                    s = a
                    o = b
            else:
                s = g
                o = 0
        else:
            s = '0'
            o = g
        s = s.strip()
        if s not in consts:
            consts.append(s)
        ii = consts.index(s)
        ans.append(ii & 0xffffffff)
        ans.append((ii >> 32) & 0xffffffff)
        ans.append(o & 0xffffffff)
        ans.append((o >> 32) & 0xffffffff)
    start = '$$'
    sp_offset = 0
    start = True
    for i in l:
        if i.strip() and not i.startswith('$$'):
            start = False
        if i.endswith(':'):
            continue
        elif i.startswith('$$'):
            if start: js_pre.append(i[2:].replace('SP_OFFSET', str(sp_offset)))
            else: js.append(i[2:].replace('SP_OFFSET', str(sp_offset)))
        elif i == '$': pass
        elif i.startswith('$'):
            push_gadget(i[1:])
        elif i.startswith('dp '):
            offset = eval(i[3:], ls)
            push_gadget('ropchain+%d'%offset)
        elif i.startswith('dq '):
            data = eval(i[3:], ls)
            low32 = data & 0xffffffff
            high32 = (data >> 32) & 0xffffffff
            ans.extend((0, 0, low32, high32))
        elif i.startswith('db '):
            data = bytes(list(eval('('+i[3:]+')')))
            assert len(data) % 8 == 0
            for i in range(0, len(data), 8):
                q = int.from_bytes(data[i:i+8], 'little')
                ans.extend((0, 0, q & 0xffffffff, (q >> 32) & 0xffffffff))
            sp_offset += len(data)
            continue
        elif i in gs:
            file, offset = gs[i]
            push_gadget('%s_base+%d'%(file, offset))
        else:
            raise SyntaxError(i)
        sp_offset += 8
    assert 'pivot(ropchain);' not in js_pre
    if 'pivot(ropchain);' not in js:
        js.append('pivot(ropchain);')
    return ans, consts, '\n'.join(js_pre), '\n'.join(js)

def read_gadgets(f):
    with open(f) as file: return list(file)

def read_asm(f):
    return [i for i in (i.split('#', 1)[0].strip() for i in read_gadgets(f)) if i]

def main(f, g):
    asm = read_asm(f)
    gadgets = parse_gadgets(read_gadgets(g))
    labels = prepass(asm)
    return final_pass(asm, labels, gadgets)

if __name__ == '__main__':
    blob, consts, js_pre, js_code = main(*sys.argv[1:])
    print('var ropbuf = new Uint32Array(%s);'%json.dumps(blob))
    print('var ropconsts = %s;'%json.dumps(consts))
    print('var js_pre = %s;'%json.dumps(js_pre))
    print('var js_code = %s;'%json.dumps(js_code))
