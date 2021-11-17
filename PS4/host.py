import http.server
import socketserver
import socket
from http.server import HTTPServer, BaseHTTPRequestHandler, SimpleHTTPRequestHandler
import ssl


PORT = 8080
Handler = http.server.SimpleHTTPRequestHandler
hostname = socket.gethostname()
s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
s.connect(('10.255.255.255', 1))
IP = s.getsockname()[0]

with socketserver.TCPServer(("", PORT), Handler) as httpd:
    print("Https server up")
    print("Adress : https://", IP,":",PORT)
    httpd.socket = ssl.wrap_socket (httpd.socket,
    certfile='Hakkuraifu.pem', server_side=True)
    httpd.serve_forever()
