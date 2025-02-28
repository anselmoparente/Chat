import express from 'express';
import { createServer } from 'http';
import { Server } from 'socket.io';

const app = express();
const server = createServer(app);

const io = new Server(server, {
    cors: { origin: "*" }
});

const users = new Map(); // Armazena as posições dos usuários

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

const updateUserLocation = (socket, { latitude, longitude }) => {
    users.set(socket.id, { latitude, longitude });
    console.log(`Usuário ${socket.id} atualizou sua posição: Lat ${latitude}, Lng ${longitude}`);
    io.emit('userLocationUpdate', { id: socket.id, latitude, longitude });
};

const getNearbyUsers = (socket, { latitude, longitude }) => {
    const nearbyUsers = [];
    users.forEach((position, id) => {
        if (id !== socket.id) {
            const distance = calculateDistance(latitude, longitude, position.latitude, position.longitude);
            if (distance < 200) {
                nearbyUsers.push({ id, latitude: position.latitude, longitude: position.longitude });
            }
        }
    });
    socket.emit('nearbyUsers', nearbyUsers);
};

const calculateDistance = (lat1, lon1, lat2, lon2) => {
    const R = 6371e3;
    const φ1 = (lat1 * Math.PI) / 180;
    const φ2 = (lat2 * Math.PI) / 180;
    const Δφ = ((lat2 - lat1) * Math.PI) / 180;
    const Δλ = ((lon2 - lon1) * Math.PI) / 180;

    const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
        Math.cos(φ1) * Math.cos(φ2) *
        Math.sin(Δλ / 2) * Math.sin(Δλ / 2);

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
};

io.on('connection', (socket) => {
    console.log('Novo usuário conectado:', socket.id);

    socket.on('joinPrivateRoom', (data) => joinPrivateRoom(socket, data));
    socket.on('privateMessage', (data) => sendPrivateMessage(socket, data));
    socket.on('updateLocation', (data) => updateUserLocation(socket, data));
    socket.on('getNearbyUsers', (data) => getNearbyUsers(socket, data));

    socket.on('disconnect', () => {
        users.delete(socket.id);
        console.log('Usuário desconectado:', socket.id);
    });
});

server.listen(3000, () => {
    console.log('Servidor rodando na porta 3000');
});
