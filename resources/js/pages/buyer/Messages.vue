<template>
  <div class="min-h-screen bg-gray-50/50 pb-12">
    <div class="bg-agri-forest py-16 relative overflow-hidden">
        <div class="absolute inset-0 bg-agri-earth/5 opacity-20"></div>
        <div class="container mx-auto px-6 relative z-10">
            <h1 class="text-4xl font-black text-white mb-2 tracking-tighter">
               Trade <span class="text-agri-earth">Discussions</span>
            </h1>
            <p class="text-agri-earth/70 font-medium">Direct secure communication with verified global counterparties.</p>
        </div>
    </div>

    <!-- Lightbox -->
    <div v-if="lightboxImage" class="fixed inset-0 z-[100] bg-black/90 flex items-center justify-center p-4" @click="lightboxImage = null">
        <img :src="lightboxImage" class="max-w-full max-h-full rounded-lg shadow-2xl" @click.stop>
        <button class="absolute top-4 right-4 text-white hover:text-gray-300" @click="lightboxImage = null">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <!-- Forward Modal -->
    <div v-if="showForwardModal" class="fixed inset-0 z-[100] bg-black/50 flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-3xl w-full max-w-md p-8 shadow-2xl">
            <h3 class="text-2xl font-black text-gray-900 mb-6">Forward to...</h3>
            <div class="space-y-2 max-h-[300px] overflow-y-auto mb-6">
                <button v-for="convo in conversations" :key="convo.user.id" 
                        @click="handleForward(convo.user.id)"
                        class="w-full flex items-center p-4 rounded-xl hover:bg-gray-50 transition-colors text-left group">
                    <div class="w-10 h-10 rounded-full bg-agri-forest/10 text-agri-forest flex items-center justify-center font-black mr-4 overflow-hidden group-hover:bg-agri-forest transition-colors">
                        <img v-if="convo.user.avatar_url" :src="convo.user.avatar_url" class="w-full h-full object-cover">
                        <span v-else class="group-hover:text-white">{{ convo.user.name.charAt(0) }}</span>
                    </div>
                    <span class="font-bold text-gray-800">{{ convo.user.name }}</span>
                </button>
            </div>
            <button @click="showForwardModal = false" class="w-full py-4 rounded-xl font-bold text-gray-500 hover:bg-gray-100 transition-colors">Cancel</button>
        </div>
    </div>

    <div class="container mx-auto px-6 -mt-8 relative z-20">
      <div class="bg-white rounded-[2.5rem] shadow-2xl border border-gray-100 flex h-[700px] overflow-hidden">
        <!-- Conversation List -->
        <div class="w-1/3 border-r border-gray-100 flex flex-col bg-gray-50/30">
          <div class="p-8 border-b border-gray-100 bg-white">
            <h2 class="text-xs font-black text-gray-400 uppercase tracking-widest">Active Channels</h2>
          </div>
          <div class="flex-1 overflow-y-auto">
            <div v-for="convo in conversations" 
                 :key="convo.user.id"
                 @click="selectConversation(convo.user.id)"
                 class="p-6 border-b border-gray-50 cursor-pointer hover:bg-white transition-all duration-300 relative group"
                 :class="{ 'bg-white shadow-inner': selectedParticipantId == convo.user.id }">
              <div v-if="selectedParticipantId == convo.user.id" class="absolute left-0 top-0 bottom-0 w-1.5 bg-agri-forest"></div>
              
              <div class="flex items-center mb-3">
                <div class="w-10 h-10 rounded-xl bg-agri-sand flex items-center justify-center text-agri-forest font-black mr-3 overflow-hidden">
                    <img v-if="convo.user.avatar_url" :src="convo.user.avatar_url" class="w-full h-full object-cover">
                    <span v-else>{{ convo.user.name.charAt(0) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-center">
                        <span class="font-black text-gray-900 truncate">{{ convo.user.name }}</span>
                        <span class="text-[10px] font-bold text-gray-400">{{ formatRecentDate(convo.last_message.created_at) }}</span>
                    </div>
                </div>
              </div>
              <p class="text-xs text-gray-500 font-medium truncate leading-relaxed">
                <span v-if="convo.last_message.sender_id === authStore.user?.id" class="text-agri-forest font-black">You:</span>
                {{ convo.last_message.message || convo.last_message.content }}
              </p>
            </div>
            
            <div v-if="conversations.length === 0" class="p-12 text-center text-gray-400">
                <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-gray-100">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                </div>
                <p class="text-xs font-black uppercase tracking-widest">No Active Channels</p>
            </div>
          </div>
        </div>

        <!-- Message View -->
        <div class="w-2/3 flex flex-col bg-white">
          <template v-if="selectedParticipantId">
            <!-- Header -->
            <div class="px-10 py-6 border-b border-gray-100 flex justify-between items-center bg-white sticky top-0 z-10">
              <div class="flex items-center">
                 <div class="w-10 h-10 rounded-xl bg-agri-forest text-agri-earth flex items-center justify-center font-black mr-4 overflow-hidden">
                     <img v-if="selectedParticipantAvatar" :src="selectedParticipantAvatar" class="w-full h-full object-cover">
                     <span v-else>{{ selectedParticipantName.charAt(0) }}</span>
                 </div>
                 <div>
                     <span class="font-black text-gray-900">{{ selectedParticipantName }}</span>
                     <p class="text-[10px] text-agri-lush font-black uppercase tracking-widest">Verified Counterparty</p>
                 </div>
              </div>
            </div>

            <!-- Messages -->
            <div class="flex-1 overflow-y-auto p-10 space-y-8 bg-gray-50/20" ref="messagesContainer">
              <div v-for="msg in activeMessages" :key="msg.id" 
                   class="flex flex-col mb-2"
                   :class="msg.sender_id === authStore.user?.id ? 'items-end' : 'items-start'">
                
                <div class="relative group max-w-[80%]">
                    <!-- Actions Trigger (Hover) -->
                    <div v-if="!msg.is_deleted" class="absolute top-2 opacity-0 group-hover:opacity-100 transition-opacity bg-white shadow-lg rounded-full p-1 z-20"
                         :class="msg.sender_id === authStore.user?.id ? 'right-full mr-2' : 'left-full ml-2'">
                        <button @click.stop="openActions(msg.id)" class="p-1 hover:bg-gray-100 rounded-full text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                        </button>
                        <!-- Dropdown -->
                        <div v-if="activeActionId === msg.id" class="absolute top-full mt-2 bg-white rounded-xl shadow-xl w-32 overflow-hidden py-1 z-50 text-xs font-bold"
                             :class="msg.sender_id === authStore.user?.id ? 'right-0' : 'left-0'">
                            <button @click="initiateForward(msg)" class="w-full text-left px-4 py-3 hover:bg-gray-50 flex items-center space-x-2 border-b border-gray-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                <span>Forward</span>
                            </button>
                            <button v-if="msg.sender_id === authStore.user?.id" @click="openDeleteModal(msg.id)" class="w-full text-left px-4 py-3 hover:bg-red-50 text-red-500 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                <span>Delete</span>
                            </button>
                        </div>
                    </div>

                    <div class="rounded-3xl px-8 py-5 shadow-sm"
                         :class="msg.is_deleted ? 'bg-gray-100 border border-gray-200 text-gray-500' : (msg.sender_id === authStore.user?.id ? 'bg-agri-forest text-white rounded-br-none' : 'bg-white text-gray-800 rounded-bl-none border border-gray-100')">
                      
                      <!-- Deleted State -->
                      <div v-if="msg.is_deleted" class="flex items-center space-x-2 italic text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                        <span>This message was deleted</span>
                      </div>

                      <!-- Content -->
                      <template v-else>
                          <!-- Attachments -->
                          <div v-if="msg.attachments && msg.attachments.length > 0" class="mb-3 space-y-2">
                            <div v-for="att in msg.attachments" :key="att.id">
                                <!-- Image -->
                                <div v-if="att.file_type.startsWith('image/')" class="relative group cursor-pointer" @click="openLightbox('/storage/' + att.file_path)">
                                    <img :src="'/storage/' + att.file_path" class="rounded-lg max-w-full h-auto" />
                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100">
                                        <span class="bg-black/50 text-white px-3 py-1 rounded-full text-xs backdrop-blur-sm">View</span>
                                    </div>
                                </div>
                                <!-- File -->
                                <div v-else class="flex items-center justify-between p-3 bg-black/5 rounded-xl hover:bg-black/10 transition-colors">
                                    <div class="flex items-center space-x-3 overflow-hidden">
                                        <div class="bg-white p-2 rounded-lg">
                                            <svg class="w-5 h-5 text-agri-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <span class="text-xs font-bold truncate">{{ att.file_name }}</span>
                                    </div>
                                    <a :href="'/storage/' + att.file_path" download class="text-agri-forest hover:text-agri-lush p-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    </a>
                                </div>
                            </div>
                          </div>
                          <p v-if="msg.content" class="text-sm font-medium leading-relaxed">{{ msg.content }}</p>
                      </template>
                    </div>
                    <div class="flex items-center justify-end mt-1 space-x-1.5 px-2">
                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">
                            {{ formatFullDate(msg.created_at) }}
                        </span>
                        <span v-if="msg.sender_id === authStore.user?.id">
                            <!-- Read: Double Blue -->
                            <svg v-if="msg.read_at" class="w-3 h-3 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M18 7l-1.41-1.41-6.34 6.34 1.41 1.41L18 7zm4.24-1.41L11.66 16.17 7.48 12l-1.41 1.41L11.66 19l12-12-1.42-1.41zM.41 13.41L6 19l1.41-1.41L1.83 12 .41 13.41z"/></svg>
                            <!-- Unread: Double Grey -->
                            <svg v-else class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M18 7l-1.41-1.41-6.34 6.34 1.41 1.41L18 7zm4.24-1.41L11.66 16.17 7.48 12l-1.41 1.41L11.66 19l12-12-1.42-1.41zM.41 13.41L6 19l1.41-1.41L1.83 12 .41 13.41z"/></svg>
                        </span>
                    </div>
                </div>
              </div>
            </div>

            <!-- Input -->
            <div class="p-8 border-t border-gray-100 bg-white">
              <!-- File Previews -->
              <div v-if="selectedFiles.length > 0" class="flex flex-wrap gap-2 mb-4">
                 <div v-for="(file, index) in selectedFiles" :key="index" class="relative group">
                    <div class="h-16 w-16 rounded-xl bg-gray-100 border border-gray-200 flex items-center justify-center overflow-hidden">
                        <img v-if="file.type.startsWith('image/')" :src="getObjectUrl(file)" class="h-full w-full object-cover">
                        <span v-else class="text-[10px] font-bold text-gray-500 uppercase">{{ file.name.split('.').pop() }}</span>
                    </div>
                    <button @click="removeFile(index)" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity shadow-sm">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                 </div>
              </div>

              <form @submit.prevent="sendMessage" class="flex items-center bg-gray-50 rounded-2xl p-2 focus-within:ring-4 focus-within:ring-agri-forest/5 transition-all">
                <!-- File Trigger -->
                <button type="button" @click="$refs.fileInput.click()" class="p-4 text-gray-400 hover:text-agri-forest transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                </button>
                <input type="file" ref="fileInput" multiple class="hidden" @change="handleFileSelect">

                <!-- Emoji Toggle -->
                <div class="relative">
                    <button type="button" @click.stop="showEmojiPicker = !showEmojiPicker" class="p-4 text-gray-400 hover:text-yellow-500 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </button>
                    <!-- Emoji Picker -->
                    <div v-if="showEmojiPicker" class="absolute bottom-full left-0 mb-4 z-50 shadow-2xl rounded-2xl overflow-hidden" @click.stop>
                        <EmojiPicker :native="true" @select="onSelectEmoji" :hide-search="false" :disable-skin-tones="true" :static-texts="{ placeholder: 'Search...' }" class="!h-[350px] !w-[300px]" />
                    </div>
                </div>

                <input v-model="newMessage" 
                       type="text" 
                       placeholder="Draft trade clarification..." 
                       class="flex-1 bg-transparent border-none px-2 py-4 focus:outline-none font-medium text-sm text-gray-900">
                <button type="submit" 
                        :disabled="!newMessage.trim() && selectedFiles.length === 0"
                        class="bg-agri-forest text-agri-earth w-12 h-12 rounded-xl flex items-center justify-center hover:bg-agri-lush hover:text-white disabled:opacity-30 transition-all shadow-lg">
                  <svg class="w-5 h-5 transform rotate-90" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
                  </svg>
                </button>
              </form>
            </div>
          </template>
          <div v-else class="flex-1 flex flex-col items-center justify-center text-gray-400 bg-gray-50/30">
            <div class="w-24 h-24 rounded-[3rem] bg-white shadow-sm flex items-center justify-center mb-8 text-gray-200">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
            </div>
            <p class="font-black uppercase text-[10px] tracking-widest text-gray-400">Select Negotiation Node</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <Modal
      :is-open="isDeleteModalOpen"
      title="Delete Message"
      message="Are you sure you want to delete this message for everyone? This action cannot be revoked."
      confirm-text="Delete for Everyone"
      cancel-text="Abort"
      type="danger"
      :loading="isDeleting"
      @confirm="deleteMessage"
      @cancel="isDeleteModalOpen = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';
import { useRoute } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import axios from 'axios';
import EmojiPicker from 'vue3-emoji-picker'
import 'vue3-emoji-picker/css'
import Modal from '../../components/Modal.vue';

const route = useRoute();
const authStore = useAuthStore();
const conversations = ref([]);
const activeMessages = ref([]);
const selectedParticipantId = ref(null);
const selectedParticipantName = ref('');
const selectedParticipantAvatar = ref('');
const newMessage = ref('');
const messagesContainer = ref(null);
const fileInput = ref(null);
const selectedFiles = ref([]);
const lightboxImage = ref(null);
const activeActionId = ref(null);
const showForwardModal = ref(false);
const messageToForward = ref(null);
const showEmojiPicker = ref(false);

const fetchConversations = async () => {
  try {
    const response = await axios.get('/api/messages');
    conversations.value = response.data;
  } catch (error) {
    console.error('Error fetching conversations:', error);
  }
};

const pollingInterval = ref(null);

const fetchMessages = async (participantId, silent = false) => {
  try {
    const response = await axios.get(`/api/messages/${participantId}`);
    // Check if new messages arrived to verify scroll position or minimal updates
    // For now, replacing the array is safe as Vue handles DOM diffing efficiently
    activeMessages.value = response.data;
    if (!silent) {
        scrollToBottom();
    }
  } catch (error) {
    console.error('Error fetching messages:', error);
    if (!silent) activeMessages.value = [];
  }
};

const markAsRead = async (participantId) => {
  try {
    await axios.post(`/api/messages/${participantId}/read`);
  } catch (error) {
    console.error('Error marking messages as read:', error);
  }
};

const startPolling = (participantId) => {
  stopPolling();
  pollingInterval.value = setInterval(() => {
    fetchMessages(participantId, true);
    // Mark as read while polling if we are "active"
    markAsRead(participantId);
    // Also update conversations list to see new notifications from others
    fetchConversations();
  }, 3000); // Poll every 3 seconds
};

const stopPolling = () => {
  if (pollingInterval.value) {
    clearInterval(pollingInterval.value);
    pollingInterval.value = null;
  }
};

const selectConversation = async (participantId) => {
  if (!participantId) return;
  selectedParticipantId.value = participantId;
  
  // Find name from conversations list
  const convo = conversations.value.find(c => c.user.id == participantId);
  if (convo) {
    selectedParticipantName.value = convo.user.name;
    selectedParticipantAvatar.value = convo.user.avatar_url;
  } else {
    selectedParticipantName.value = 'Trade Partner';
    selectedParticipantAvatar.value = '';
  }

  // Initial fetch
  await fetchMessages(participantId);
  await markAsRead(participantId);
  
  // Start swarming
  startPolling(participantId);
};

const handleFileSelect = (event) => {
  const files = Array.from(event.target.files);
  selectedFiles.value.push(...files);
  // Reset input so same files can be selected again if needed
  event.target.value = '';
};

const removeFile = (index) => {
  selectedFiles.value.splice(index, 1);
};

const getObjectUrl = (file) => {
  return URL.createObjectURL(file);
};

const onSelectEmoji = (emoji) => {
    newMessage.value += emoji.i;
    // Keep open for multiple emojis or close? WhatsApp keeps open.
};

const sendMessage = async () => {
  if ((!newMessage.value.trim() && selectedFiles.value.length === 0) || !selectedParticipantId.value) return;

  try {
    const formData = new FormData();
    formData.append('receiver_id', selectedParticipantId.value);
    formData.append('content', newMessage.value);
    
    selectedFiles.value.forEach(file => {
        formData.append('attachments[]', file);
    });

    const response = await axios.post('/api/messages', formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    });
    
    // Optimistic update
    activeMessages.value.push(response.data);
    newMessage.value = '';
    selectedFiles.value = [];
    scrollToBottom();
    fetchConversations();
  } catch (error) {
    console.error('Error sending message:', error);
  }
};

const scrollToBottom = () => {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
    }
  });
};

const formatRecentDate = (date) => {
  if (!date) return '';
  const d = new Date(date);
  return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

const formatFullDate = (date) => {
  if (!date) return '';
  return new Date(date).toLocaleString([], { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
};

// Lightbox & Actions
const openLightbox = (imagePath) => {
    lightboxImage.value = imagePath;
};

const openActions = (msgId) => {
    activeActionId.value = activeActionId.value === msgId ? null : msgId;
};

// Forwarding
const initiateForward = (msg) => {
    messageToForward.value = msg;
    showForwardModal.value = true;
    activeActionId.value = null;
};

const handleForward = async (targetUserId) => {
    if (!messageToForward.value) return;

    try {
        await axios.post(`/api/messages/${messageToForward.value.id}/forward`, {
            receiver_id: targetUserId
        });
        
        // If we are currently viewing the target conversation, refresh
        if (selectedParticipantId.value == targetUserId) {
            fetchMessages(targetUserId);
        } else {
            // Switch to that conversation
            selectConversation(targetUserId);
        }
    } catch (error) {
        console.error('Error forwarding message:', error);
    } finally {
        showForwardModal.value = false;
        messageToForward.value = null;
    }
};

const isDeleteModalOpen = ref(false);
const messageIdToDelete = ref(null);
const isDeleting = ref(false);

const openDeleteModal = (messageId) => {
    messageIdToDelete.value = messageId;
    isDeleteModalOpen.value = true;
};

const deleteMessage = async () => {
    if (!messageIdToDelete.value) return;
    
    isDeleting.value = true;
    try {
        await axios.delete(`/api/messages/${messageIdToDelete.value}`);
        // Refresh messages to show deleted state
        if (selectedParticipantId.value) {
            fetchMessages(selectedParticipantId.value, true);
        }
        isDeleteModalOpen.value = false;
    } catch (error) {
        console.error('Error deleting message:', error);
    } finally {
        isDeleting.value = false;
        messageIdToDelete.value = null;
    }
};

onMounted(async () => {
  await fetchConversations();
  
  if (route.query.userId) {
    selectConversation(route.query.userId);
  }

  // Close actions and emoji picker on click outside
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.group')) {
        activeActionId.value = null;
    }
    showEmojiPicker.value = false;
  });
});

onUnmounted(() => {
  stopPolling();
});
</script>

<style scoped>
::-webkit-scrollbar {
  width: 6px;
}
::-webkit-scrollbar-track {
  background: transparent;
}
::-webkit-scrollbar-thumb {
  background: rgba(0,0,0,0.05);
  border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
  background: rgba(0,0,0,0.1);
}
</style>
