<script setup lang="ts">
import { ref } from 'vue';

const chats = ref([
    { id: 1, name: 'João', lastMessage: 'Oi, tudo bem?', messages: [{ id: 1, text: 'Oi!', sent: true }] },
    { id: 2, name: 'Maria', lastMessage: 'Vamos nos encontrar?', messages: [{ id: 1, text: 'Claro!', sent: false }] },
]);

const selectedChat = ref(null);
const newMessage = ref('');
const userName = ref('');
const latitude = ref('');
const longitude = ref('');
const userConfirmed = ref(false);

function selectChat(chat) {
    selectedChat.value = chat;
}

function sendMessage() {
    if (newMessage.value.trim() && selectedChat.value) {
        selectedChat.value.messages.push({
            id: Date.now(),
            text: newMessage.value,
            sent: true
        });
        newMessage.value = '';
    }
}

function updatePosition() {
    if (latitude.value && longitude.value) {
        alert('Posição atualizada!');
    } else {
        alert('Por favor, preencha a latitude e longitude.');
    }
}

function confirmUser() {
    if (userName.value && latitude.value && longitude.value) {
        userConfirmed.value = true;
    } else {
        alert('Por favor, preencha todos os campos.');
    }
}
</script>

<template>
    <div class="chat-container">
        <aside class="chat-sidebar">
            <div class="user-info">
                <h2>Dados do Usuário</h2>
                <form @submit.prevent="userConfirmed ? updatePosition() : confirmUser()">
                    <input v-model="userName" placeholder="Seu nome" required :disabled="userConfirmed" />
                    <input v-model="latitude" placeholder="Latitude" required />
                    <input v-model="longitude" placeholder="Longitude" required />
                    <button class="confirm-btn">{{ userConfirmed ? 'Alterar posição' : 'Confirmar' }}</button>
                </form>

            </div>
            <!-- A lista de chats só é exibida após a confirmação -->
            <div v-if="userConfirmed" class="chat-list">
                <div v-for="chat in chats" :key="chat.id" class="chat-item" @click="selectChat(chat)">
                    <div class="chat-info">
                        <h3>{{ chat.name }}</h3>
                        <p>{{ chat.lastMessage }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <main class="chat-window" v-if="selectedChat">
            <div class="chat-header">
                <h2>{{ selectedChat.name }}</h2>
            </div>
            <div class="chat-messages">
                <div v-for="message in selectedChat.messages" :key="message.id"
                    :class="{ message: true, sent: message.sent, received: !message.sent }">
                    <p>{{ message.text }}</p>
                </div>
            </div>
            <div class="chat-input">
                <input v-model="newMessage" @keyup.enter="sendMessage" placeholder="Digite uma mensagem..." />
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
