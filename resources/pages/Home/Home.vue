<script setup lang="ts">
import { ref } from 'vue';

const chats = ref([
    { id: 1, name: 'João', lastMessage: 'Oi, tudo bem?', messages: [{ id: 1, text: 'Oi!', sent: true }] },
    { id: 2, name: 'Maria', lastMessage: 'Vamos nos encontrar?', messages: [{ id: 1, text: 'Claro!', sent: false }] }
]);

const selectedChat = ref(null);
const newMessage = ref('');

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

</script>

<template>
    <div class="chat-container">
        <aside class="chat-sidebar">
            <div class="chat-list">
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
    font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
}

.chat-sidebar {
    width: 30%;
    background: var(--sidebar-bg);
    border-right: 1px solid var(--border-color);
    overflow-y: auto;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
    padding: 10px;
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
    background: var(--primary-color);
    color: #fff;
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
    padding: 20px;
    background: var(--header-bg);
    border-bottom: 1px solid var(--border-color);
}

.chat-header h2 {
    margin: 0;
    font-size: 1.3rem;
    color: var(--text-color);
}

.chat-messages {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    background: #f0f0f0;
}

.message {
    max-width: 60%;
    padding: 10px 15px;
    margin-bottom: 15px;
    border-radius: 20px;
    word-wrap: break-word;
    line-height: 1.4;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.sent {
    background: var(--message-sent-bg);
    margin-left: auto;
    border-top-right-radius: 0;
}

.received {
    background: var(--message-received-bg);
    margin-right: auto;
    border-top-left-radius: 0;
}

.chat-input {
    padding: 15px 20px;
    border-top: 1px solid var(--border-color);
    background: var(--header-bg);
}

.chat-input input {
    width: 100%;
    padding: 12px 15px;
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