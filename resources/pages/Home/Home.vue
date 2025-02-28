<script setup lang="ts">
import axios from 'axios';
import { onMounted, ref } from 'vue';
import { User } from "../../models/user";
import { Mensagem } from '../../models/Mensagem';


const messages = ref<Mensagem[]>([]);
const users = ref<User[]>([]);
const userConfirmed = ref<User>();
const selectedUser = ref<User>();
const message = ref('');
const userName = ref('');
const latitude = ref('');
const longitude = ref('');

function selectUser(user: User) {
    selectedUser.value = user;

    getMessages();
}

function haversineDistance(lat1: string, lon1: string, lat2: string, lon2: string): number {
    const toRadians = (degrees: number) => degrees * (Math.PI / 180);

    const lat1Num = parseFloat(lat1);
    const lon1Num = parseFloat(lon1);
    const lat2Num = parseFloat(lat2);
    const lon2Num = parseFloat(lon2);

    const R = 6371000;
    const dLat = toRadians(lat2Num - lat1Num);
    const dLon = toRadians(lon2Num - lon1Num);

    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(toRadians(lat1Num)) * Math.cos(toRadians(lat2Num)) *
        Math.sin(dLon / 2) * Math.sin(dLon / 2);

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

async function confirmUser() {
    if (userName.value && latitude.value && longitude.value) {
        try {
            const response = await axios.post('/usuarios', {
                nome: userName.value,
                latitude: latitude.value,
                longitude: longitude.value
            });

            userConfirmed.value = User.fromJson(response.data.user);

            fetchUsers();
        } catch (error) {
            alert('Erro ao criar usuário. Tente novamente.' + error);
        }
    } else {
        alert('Por favor, preencha todos os campos.');
    }
}

async function updatePosition() {
    try {
        const response = await axios.put(`/usuario/${userConfirmed.value?.id}`, {
            nome: userName.value,
            latitude: latitude.value,
            longitude: longitude.value
        });

        userConfirmed.value = User.fromJson(response.data.user);

        fetchUsers();
        checkMessages();
    } catch (error) {
        alert('Erro ao atualizar posição. Tente novamente.');
    }
}

async function getMessages() {
    checkMessages();

    const response = await axios.get(`/mensagens/${userConfirmed.value?.id}`);

    messages.value = response.data.map((message: any) => Mensagem.fromJson(message));
}

async function sendMessage() {
    try {
        const distance: number = haversineDistance(userConfirmed.value?.latitude ?? '', userConfirmed.value?.longitude ?? '', selectedUser.value?.latitude ?? '', selectedUser.value?.longitude ?? '');
        await axios.post(`/mensagens`, {
            texto: message.value,
            usuario_envio_id: userConfirmed.value?.id,
            usuario_recebimento_id: selectedUser.value?.id,
            near: distance < 200 ? true : false,
        });

        getMessages();
    } catch (error) {
        alert('Erro ao enviar mensagem. Tente novamente.');
    }
}

async function checkMessages() {
    const aux: number[] = users.value.filter(user => {
        const distancia = haversineDistance(
            userConfirmed.value?.latitude ?? '',
            userConfirmed.value?.longitude ?? '',
            user.latitude,
            user.longitude
        );
        return distancia < 200;
    }).map(user => user.id);

    if (aux.length > 0) {
        updateMessages(aux)
    }

}

async function updateMessages(ids: number[]) {
    try {
        await axios.put(`/mensagens/${userConfirmed.value?.id}`, {
            usuarios_ids: ids,
        });

    } catch (error) {
        console.error(error);
    }
}

async function fetchUsers() {
    try {
        const response = await axios.get('/usuarios', {
            params: { nome: userName.value }
        });

        users.value = response.data.map((user: any) => User.fromJson(user)).filter(user => {
            const distancia = haversineDistance(
                userConfirmed.value?.latitude ?? '',
                userConfirmed.value?.longitude ?? '',
                user.latitude,
                user.longitude
            );
            return distancia < 200;
        });

        if (!users.value.some(user => user.id === selectedUser.value?.id)) {
            selectedUser.value = undefined;
        }
    } catch (error) {
        console.error('Erro ao buscar usuários:', error);
    }
}

onMounted(() => {
    setInterval(fetchUsers, 120000);
    setInterval(getMessages, 1000);
})
</script>

<template>
    <div class="chat-container">
        <aside class="chat-sidebar">
            <div class="user-info">
                <div class="user-info-header">
                    <h2>Dados do Usuário</h2>
                    <button @click="fetchUsers" :disabled="userConfirmed == null">
                        <span class="material-symbols-outlined">refresh</span>
                    </button>
                </div>
                <form @submit.prevent="userConfirmed ? updatePosition() : confirmUser()">
                    <input v-model="userName" placeholder="Seu nome" required :disabled="userConfirmed != null" />
                    <input v-model="latitude" placeholder="Latitude" required />
                    <input v-model="longitude" placeholder="Longitude" required />
                    <button class="confirm-btn">{{ userConfirmed ? 'Alterar posição' : 'Confirmar' }}</button>
                </form>

            </div>
            <!-- A lista de chats só é exibida após a confirmação -->
            <div v-if="userConfirmed" class="chat-list">
                <div v-for="user in users" :key="user.id" class="chat-item" @click="selectUser(user)">
                    <div class="chat-info">
                        <h3>{{ user.nome }}</h3>
                        <p>
                            {{
                                haversineDistance(
                                    userConfirmed.latitude, userConfirmed.longitude, user.latitude, user.longitude
                                ).toFixed(0)
                            }} m
                        </p>
                    </div>
                </div>
            </div>
        </aside>

        <main class="chat-window" v-if="selectedUser">
            <div class="chat-header">
                <h2>{{ selectedUser.nome }}</h2>
            </div>
            <div class="chat-messages">
                <div v-for="message in messages" :key="message.id"
                    :class="{ message: true, sent: message.usuario_envio_id == userConfirmed?.id, received: message.usuario_envio_id != userConfirmed?.id }">
                    <p>{{ message.texto }}</p>
                </div>
            </div>
            <div class="chat-input">
                <input v-model="message" @keyup.enter="sendMessage" placeholder="Digite uma mensagem..." />
            </div>
        </main>
    </div>
</template>

<style scoped>
:root {
    --primary-color: #4a90e2;
    --primary-color-dark: #357ABD;
    --secondary-color: #f5f5f5;
    --bg-color: #eaeaea;
    --sidebar-bg: #ffffff;
    --header-bg: #f7f7f7;
    --message-sent-bg: #dcf8c6;
    --message-received-bg: #ffffff;
    --text-color: #333;
    --border-color: #ddd;
}

* {
    box-sizing: border-box;
}

.chat-container {
    display: flex;
    height: 100vh;
    background: var(--bg-color);
    font-family: 'Nunito';
}

.chat-sidebar {
    width: 30%;
    background: var(--sidebar-bg);
    border-right: 1px solid var(--border-color);
    overflow-y: auto;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
    padding: 10px;
}

/* Estilização aprimorada para o formulário de dados do usuário */
.user-info {
    padding: 20px;
    background: var(--sidebar-bg);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.user-info-header {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
}


.user-info-header button {
    background: none;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    font-size: 24px;
    margin: 0;
    padding: 8px;
    height: 40px;
    width: 40px;
}

.user-info-header button:hover {
    background: orange;
    color: white;
}

.user-info-header button:disabled {
    cursor: default;
    background: none;
    color: black;
}

.user-info-header span {
    margin: 0;
    padding: 0;
}



.user-info h2 {
    margin-top: 0;
    margin-bottom: 15px;
    font-size: 1.2rem;
    color: var(--text-color);
}

.user-info input {
    display: block;
    width: 100%;
    margin-bottom: 10px;
    padding: 10px;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    font-size: 1rem;
}

.confirm-btn {
    background: #4A3333;
    border: 1px solid white;
    border-radius: 16px;
    color: white;
    cursor: pointer;
    font-size: 1rem;
    transition: background 0.3s;
    padding: 16px;
    width: 100%;
}

.confirm-btn:hover {
    background: white;
    border: 1px solid #4A3333;
    color: #4A3333;
}

.chat-list {
    margin-top: 10px;
}

.chat-item {
    padding: 15px;
    margin-bottom: 10px;
    border-radius: 8px;
    background: var(--secondary-color);
    transition: background 0.3s, color 0.3s;
    cursor: pointer;
}

.chat-item:hover {
    background: grey;
    color: white;
}

.chat-item:hover p {
    color: white;
    transition: color 0.3s;
}

.chat-info h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
}

.chat-info p {
    margin: 5px 0 0;
    font-size: 0.9rem;
    color: #666;
}

.chat-window {
    width: 70%;
    display: flex;
    flex-direction: column;
    background: #fff;
    position: relative;
}

.chat-header {
    background: #80B0AB;
    border-bottom: 1px solid var(--border-color);
    text-align: center;
    padding: 20px;
}

.chat-header h2 {
    color: white;
    font-size: 1.3rem;
    margin: 0;
}

.chat-messages {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    background: #DBD1B3;
}

.message {
    background-color: white;
    max-width: 50%;
    padding: 8px 16px;
    margin-bottom: 15px;
    border-radius: 20px;
    word-wrap: break-word;
    line-height: 1.4;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.sent {
    margin-left: auto;
    border-top-right-radius: 0;
}

.received {
    margin-right: auto;
    border-top-left-radius: 0;
}

.chat-input {
    background: #80B0AB;
    border-top: 1px solid var(--border-color);
    padding: 15px 20px;
}

.chat-input input {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid var(--border-color);
    border-radius: 25px;
    outline: none;
    font-size: 1rem;
    transition: border-color 0.3s;
}

.chat-input input:focus {
    border-color: var(--primary-color);
}
</style>
