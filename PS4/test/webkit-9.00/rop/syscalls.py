import sys

data = sys.stdin.read().split('\n')

for i, l in enumerate(data):
    if l.split('\t', 2)[-1].strip() == 'syscall':
        if data[i-1].split('\t', 2)[-1].strip() != 'mov    %rcx,%r10': continue
        ll = data[i-2]
        lls = ll.split('\t', 2)[-1].strip()
        if not lls.startswith('mov    $') or not lls.endswith(',%rax'): continue
        try: syscno = int(lls[8:-5], 16)
        except ValueError: continue
        addr = int(ll.split(':', 1)[0], 16)
        print('var sys_%d_addr = libkernel_base + 0x%x;'%(syscno, addr))
