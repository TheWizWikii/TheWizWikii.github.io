import sys, threading, signal, os, time

print('Navigate the PS4 web browser to port 8080 on this PC')
print('(or just press OK if you are already on error screen)')
print('If you hit the error screen (again), press OK to resume download.')

if ',' in sys.argv[1]:
    idx = int(sys.argv[1].split(',')[0])
    offset0 = int(sys.argv[1].split(',')[1], 16)
else:
    idx = int(sys.argv[1])
    offset0 = 0

import client
from client import read_mem, read_ptr

def get_base():
    while True:
        try:
            some_func = read_ptr(read_ptr(read_ptr(client.tarea+0x18)))
            if idx < 0:
                got_func = some_func
            else:
                plt = some_func - 17100888
                plt_entry = plt + idx * 16
                q = read_mem(plt_entry, 6)
                assert q[:2] == b'\xff%', q
                got_entry = plt_entry + 6 + int.from_bytes(q[2:], 'little')
                got_func = read_ptr(got_entry)
            got_func += offset0
            return got_func
        except client.BrowserRestartedError: pass

data = b''

chunk_sz = 1

got_func = get_base()
first = True
while True:
    try: data += read_mem(got_func + len(data), chunk_sz)
    except client.BrowserRestartedError:
        if first:
            break
        first = True
        got_func = get_base()
        chunk_sz = min(2048, 4096 - (got_func + len(data)) % 4096)
        continue
    first = False
    if chunk_sz < 2048:
        chunk_sz *= 2
    print(len(data), end=' bytes loaded\r')

client.out_q.put((0, 1))
time.sleep(1)

if len(sys.argv) > 2:
    print('saving to', sys.argv[2])
    with open(sys.argv[2], 'wb') as file: file.write(data)
