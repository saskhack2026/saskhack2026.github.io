// server.js - Simple WebSocket signaling server for mesh demo
const WebSocket = require('ws');
const http = require('http');
const fs = require('fs');

const PORT = 8080;

// Create HTTP server to serve the HTML
const server = http.createServer((req, res) => {
  if (req.url === '/' || req.url === '/index.html') {
    fs.readFile('mesh-client.html', (err, data) => {
      if (err) {
        res.writeHead(404);
        res.end('File not found');
        return;
      }
      res.writeHead(200, { 'Content-Type': 'text/html' });
      res.end(data);
    });
  } else {
    res.writeHead(404);
    res.end('Not found');
  }
});

// Create WebSocket server
const wss = new WebSocket.Server({ server });

// Store all connected clients
const clients = new Set();

wss.on('connection', (ws) => {
  console.log('New client connected. Total clients:', clients.size + 1);
  clients.add(ws);

  // Send welcome message
  ws.send(JSON.stringify({
    type: 'system',
    message: 'Connected to mesh network',
    clientCount: clients.size
  }));

  // Broadcast client count to all
  broadcastClientCount();

  // Handle incoming messages
  ws.on('message', (data) => {
    try {
      const message = JSON.parse(data);
      console.log('Received message:', message.type);

      // Broadcast to all OTHER clients (mesh relay)
      clients.forEach((client) => {
        if (client !== ws && client.readyState === WebSocket.OPEN) {
          client.send(data);
        }
      });
    } catch (error) {
      console.error('Error processing message:', error);
    }
  });

  // Handle disconnect
  ws.on('close', () => {
    clients.delete(ws);
    console.log('Client disconnected. Total clients:', clients.size);
    broadcastClientCount();
  });

  ws.on('error', (error) => {
    console.error('WebSocket error:', error);
    clients.delete(ws);
  });
});

function broadcastClientCount() {
  const countMessage = JSON.stringify({
    type: 'clientCount',
    count: clients.size
  });

  clients.forEach((client) => {
    if (client.readyState === WebSocket.OPEN) {
      client.send(countMessage);
    }
  });
}

server.listen(PORT, () => {
  console.log(`\n🌐 Mesh Network Server Running!`);
  console.log(`📱 Open on phones: http://YOUR-SERVER-IP:${PORT}`);
  console.log(`💻 Or locally: http://localhost:${PORT}\n`);
});
