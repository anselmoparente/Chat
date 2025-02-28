import express from 'express';
import { createServer } from 'http';
import { Server } from 'socket.io';

const app = express();
const server = createServer(app);

const io = new Server(server, {
    cors: { origin: "*" }
});

const generateRoomId = (user1, user2) => {
    return [user1, user2].sort().join('_');
};

const joinPrivateRoom = (socket, { user1, user2 }) => {
    const room = generateRoomId(user1, user2);
    socket.join(room);
    console.log(`Usuário ${socket.id} entrou na sala privada: ${room}`);
};

const sendPrivateMessage = (socket, { user1, user2, text }) => {
    const room = generateRoomId(user1, user2);
    console.log(`Mensagem na sala privada ${room} de ${socket.id}: ${text}`);
    io.to(room).emit('privateMessage', { id: socket.id, text });
};

io.on('connection', (socket) => {
    console.log('Novo usuário conectado:', socket.id);

    socket.on('joinPrivateRoom', (data) => joinPrivateRoom(socket, data));
    socket.on('privateMessage', (data) => sendPrivateMessage(socket, data));

    socket.on('disconnect', () => {
        console.log('Usuário desconectado:', socket.id);
    });
});

server.listen(3000, () => {
    console.log('Servidor rodando na porta 3000');
});
