import express from 'express';
import { createServer } from 'http';
import { Server } from 'socket.io';

const app = express();
const server = createServer(app);

const io = new Server(server, {
    cors: { origin: "*" }
});

io.on('connection', (socket) => {
    console.log('connection');

    socket.on('disconnect', (socket) => {
        console.log('disconnect');
    });
});

server.listen(3000, () => {
    console.log('Server is running');
});