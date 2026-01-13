<template>
  <div class="min-h-screen bg-gray-50/50 pb-12 font-sans">
    <!-- Header -->
    <div class="bg-white border-b border-gray-100 py-10">
        <div class="container mx-auto px-6">
            <div class="flex items-center justify-between">
                <div>
                   <h1 class="text-3xl font-black text-gray-900 tracking-tight mb-2">
                       Incoming <span class="text-agri-forest">Trade Requests</span>
                   </h1>
                   <p class="text-gray-500 font-medium">Manage and negotiate incoming supply requests from global buyers.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-6 -mt-8">
      <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-100/50 border border-gray-100 overflow-hidden">
        
        <!-- Loading State -->
        <div v-if="loading" class="p-20 text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-agri-forest mx-auto mb-4"></div>
            <p class="text-gray-400 font-medium">Synching ledger...</p>
        </div>

        <!-- RFQ Table -->
        <div v-else class="overflow-x-auto">
          <table class="w-full whitespace-nowrap">
            <thead>
              <tr class="bg-gray-50/50 border-b border-gray-100">
                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Requested Asset</th>
                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Buyer</th>
                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Volume</th>
                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Delivery Date</th>
                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Status</th>
                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="rfq in rfqs" :key="rfq.id" class="group hover:bg-agri-sand/10 transition-colors duration-300">
                <td class="px-8 py-6">
                  <div class="flex items-center">
                    <div class="w-10 h-10 rounded-xl bg-agri-sand flex items-center justify-center text-agri-forest font-black mr-4 group-hover:bg-agri-forest group-hover:text-white transition-all shadow-sm">
                        {{ rfq.product?.name?.charAt(0) || 'R' }}
                    </div>
                    <div>
                        <div class="text-sm font-black text-gray-900 group-hover:text-agri-forest transition-colors">{{ rfq.product?.name || 'Bulk Commodity' }}</div>
                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-tight mt-0.5">REF: #RFQ-{{ rfq.id }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-8 py-6">
                  <div class="text-sm font-bold text-gray-600">{{ rfq.buyer?.name || 'Verified Buyer' }}</div>
                </td>
                <td class="px-8 py-6 text-center">
                  <div class="text-sm font-black text-agri-forest">{{ rfq.quantity }} {{ rfq.product?.unit || 'Units' }}</div>
                </td>
                <td class="px-8 py-6 text-center">
                  <div class="text-sm font-medium text-gray-500">{{ formatDate(rfq.preferred_date) }}</div>
                </td>
                <td class="px-8 py-6 text-right">
                  <span :class="getStatusClasses(rfq.status)" class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest inline-block shadow-sm">
                    {{ rfq.status }}
                  </span>
                </td>
                <td class="px-8 py-6 text-right">
                    <button @click="viewNegotiation(rfq)" class="bg-agri-forest/10 hover:bg-agri-forest text-agri-forest hover:text-white px-4 py-2 rounded-lg text-xs font-black uppercase tracking-wider transition-all">
                        Negotiate
                    </button>
                </td>
              </tr>
              
              <!-- Empty State -->
              <tr v-if="rfqs.length === 0">
                  <td colspan="6" class="px-8 py-20 text-center">
                       <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-300">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                       </div>
                       <p class="text-gray-900 font-black text-xl mb-2">No Incoming Requests</p>
                       <p class="text-gray-500 font-medium">Your products are visible on the marketplace. Requests will appear here.</p>
                  </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Negotiation Drawer -->
    <div v-if="selectedRfq" class="relative z-50">
        <!-- Backdrop -->
        <transition 
            enter-active-class="ease-in-out duration-500" 
            enter-from-class="opacity-0" 
            enter-to-class="opacity-100" 
            leave-active-class="ease-in-out duration-500" 
            leave-from-class="opacity-100" 
            leave-to-class="opacity-0"
        >
            <div class="fixed inset-0 bg-agri-forest/30 backdrop-blur-sm transition-opacity" @click="selectedRfq = null"></div>
        </transition>

        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="absolute inset-0 overflow-hidden">
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                    <transition 
                        enter-active-class="transform transition ease-in-out duration-500 sm:duration-700" 
                        enter-from-class="translate-x-full" 
                        enter-to-class="translate-x-0" 
                        leave-active-class="transform transition ease-in-out duration-500 sm:duration-700" 
                        leave-from-class="translate-x-0" 
                        leave-to-class="translate-x-full"
                    >
                        <div v-if="selectedRfq" class="pointer-events-auto w-screen max-w-4xl">
                            <div class="flex h-full flex-col bg-white shadow-xl">
                                <!-- Header -->
                                <div class="bg-agri-forest px-6 py-6 relative overflow-hidden shrink-0">
                                    <div class="absolute inset-0 bg-agri-earth/10"></div>
                                    <div class="relative z-10">
                                        <div class="flex items-center justify-between mb-4">
                                            <h2 class="text-xl font-black text-white">Negotiation Room</h2>
                                            <button @click="selectedRfq = null" class="text-white/70 hover:text-white">
                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                            </button>
                                        </div>
                                        
                                        <!-- Quick Actions for Vendor -->
                                        <div v-if="selectedRfq.status === 'pending'" class="flex space-x-2">
                                            <button @click="updateStatus('accepted')" :disabled="processing" class="flex-1 bg-white/20 hover:bg-white/30 text-white py-2 rounded-lg text-xs font-black uppercase tracking-widest backdrop-blur-sm transition-all border border-white/10">
                                                Accept Request
                                            </button>
                                            <button @click="updateStatus('rejected')" :disabled="processing" class="flex-1 bg-red-500/20 hover:bg-red-500/40 text-red-100 hover:text-white py-2 rounded-lg text-xs font-black uppercase tracking-widest backdrop-blur-sm transition-all border border-red-400/30">
                                                Reject
                                            </button>
                                        </div>
                                        <div v-else class="bg-white/10 rounded-lg p-2 text-center border border-white/10">
                                            <span class="text-white text-xs font-bold uppercase tracking-widest">Trade Status: {{ selectedRfq.status }}</span>
                                        </div>
                                    </div>
                                </div>

                                    <!-- Messages Area -->
                                    <div class="flex-1 overflow-y-auto p-10 space-y-6 bg-gray-50/20" ref="messagesContainer" @scroll="handleScroll">
                                        
                                        <!-- Original Request Context (Fixed at top) -->
                                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 shrink-0 mb-4">
                                            <div class="flex items-center justify-between mb-3">
                                                <span class="text-[10px] uppercase tracking-widest font-black text-agri-forest">Initial Request</span>
                                                <span class="text-[10px] text-gray-400">{{ formatDate(selectedRfq.created_at) }}</span>
                                            </div>
                                            <p class="text-sm font-bold text-gray-900">{{ selectedRfq.product?.name }} - {{ selectedRfq.quantity }} {{ selectedRfq.product?.unit }}</p>
                                            <p class="text-xs text-gray-600 mt-1">Target Delivery: {{ formatDate(selectedRfq.preferred_date) }} to {{ selectedRfq.delivery_location }}</p>
                                            <p v-if="selectedRfq.additional_notes" class="text-xs text-gray-500 italic mt-2 border-t border-gray-100 pt-2">
                                                "{{ selectedRfq.additional_notes }}"
                                            </p>
                                        </div>

                                        <!-- Empty State -->
                                        <div v-if="messages.length === 0" class="flex flex-col items-center justify-center py-10 text-gray-400">
                                            <div class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center mb-4 shadow-sm">
                                                <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                            </div>
                                            <p class="text-xs font-black uppercase tracking-widest">No messages yet</p>
                                            <p class="text-xs text-gray-400 mt-1">Start the negotiation below</p>
                                        </div>

                                        <!-- Message Bubbles -->
                                        <template v-for="(msg, index) in messages" :key="msg.id">
                                            
                                            <!-- Date Divider -->
                                            <div v-if="shouldShowDateDivider(msg, index, messages)" class="flex justify-center my-6">
                                                <span class="bg-gray-100 text-gray-400 text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full">
                                                  {{ formatDateDivider(msg.created_at) }}
                                                </span>
                                            </div>

                                            <div class="flex flex-col mb-4"
                                                 :class="msg.sender_id === authStore.user.id ? 'items-end' : 'items-start'">
                                                
                                                <!-- Sender Name Label (above bubble) -->
                                                <div class="flex items-center mb-1.5 px-2" :class="msg.sender_id === authStore.user.id ? 'flex-row-reverse' : 'flex-row'">
                                                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-black mr-2 bg-agri-sand text-gray-600 ml-2" v-if="msg.sender_id !== authStore.user.id">
                                                        {{ msg.sender?.name?.charAt(0) || '?' }}
                                                    </div>
                                                    <!-- Only show name if meaningful change? keeping it simple for now -->
                                                    <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">
                                                        {{ msg.sender_id === authStore.user.id ? 'You' : msg.sender?.name }}
                                                    </span>
                                                </div>

                                                <!-- Message Bubble -->
                                                <div class="relative max-w-[70%]">
                                                    <div class="rounded-3xl px-8 py-5 shadow-sm"
                                                         :class="msg.sender_id === authStore.user.id 
                                                             ? 'bg-agri-forest text-white rounded-br-none' 
                                                             : 'bg-white text-gray-800 rounded-bl-none border border-gray-100'">
                                                        <p v-if="msg.message" class="text-sm font-medium leading-relaxed">{{ msg.message }}</p>

                                                        <!-- Attachments -->
                                                        <div v-if="msg.attachments && msg.attachments.length > 0" class="mt-3 space-y-2">
                                                            <div v-for="attachment in msg.attachments" :key="attachment.id">
                                                                <!-- Image Preview -->
                                                                <div v-if="attachment.file_type.startsWith('image/')" class="rounded-lg overflow-hidden">
                                                                    <img :src="`/storage/${attachment.file_path}`" :alt="attachment.file_name" class="max-w-xs rounded-lg">
                                                                    <a :href="`/storage/${attachment.file_path}`" download :class="msg.sender_id === authStore.user.id ? 'text-agri-earth' : 'text-agri-forest'" class="text-xs font-bold mt-1 inline-block hover:underline">
                                                                        Download {{ attachment.file_name }}
                                                                    </a>
                                                                </div>
                                                                
                                                                <!-- Document/File -->
                                                                <a v-else :href="`/storage/${attachment.file_path}`" download 
                                                                   class="flex items-center space-x-2 p-3 rounded-lg transition-colors"
                                                                   :class="msg.sender_id === authStore.user.id ? 'bg-white/10 hover:bg-white/20' : 'bg-gray-100 hover:bg-gray-200'">
                                                                    <svg class="w-5 h-5" :class="msg.sender_id === authStore.user.id ? 'text-agri-earth' : 'text-agri-forest'" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"/>
                                                                    </svg>
                                                                    <div class="flex-1 min-w-0">
                                                                        <p class="text-xs font-bold truncate" :class="msg.sender_id === authStore.user.id ? 'text-white' : 'text-gray-900'">{{ attachment.file_name }}</p>
                                                                        <p class="text-[10px]" :class="msg.sender_id === authStore.user.id ? 'text-agri-earth/70' : 'text-gray-500'">{{ formatFileSize(attachment.file_size) }}</p>
                                                                    </div>
                                                                    <svg class="w-4 h-4" :class="msg.sender_id === authStore.user.id ? 'text-agri-earth' : 'text-agri-forest'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                                    </svg>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Timestamp below bubble -->
                                                    <div class="flex items-center mt-1.5 space-x-1.5 px-2" :class="msg.sender_id === authStore.user.id ? 'justify-end' : 'justify-start'">
                                                        <span class="text-[9px] font-bold uppercase tracking-widest text-gray-400">
                                                            {{ formatFullDate(msg.created_at) }}
                                                        </span>
                                                        <!-- Read Receipt for own messages -->
                                                        <svg v-if="msg.sender_id === authStore.user.id && msg.is_read" 
                                                             class="w-3 h-3 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M18 7l-1.41-1.41-6.34 6.34 1.41 1.41L18 7zm4.24-1.41L11.66 16.17 7.48 12l-1.41 1.41L11.66 19l12-12-1.42-1.41zM.41 13.41L6 19l1.41-1.41L1.83 12 .41 13.41z"/>
                                                        </svg>
                                                        <svg v-else-if="msg.sender_id === authStore.user.id" 
                                                             class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M18 7l-1.41-1.41-6.34 6.34 1.41 1.41L18 7zm4.24-1.41L11.66 16.17 7.48 12l-1.41 1.41L11.66 19l12-12-1.42-1.41zM.41 13.41L6 19l1.41-1.41L1.83 12 .41 13.41z"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                <!-- Input Area -->
                                <div class="p-8 border-t border-gray-100 bg-white shrink-0">
                                    <form @submit.prevent="sendMessage" class="space-y-3">
                                        <!-- Selected Files Preview -->
                                        <div v-if="selectedFiles.length > 0" class="flex flex-wrap gap-2">
                                            <div v-for="(file, index) in selectedFiles" :key="index" class="flex items-center space-x-2 bg-gray-100 rounded-lg px-3 py-2">
                                                <svg class="w-4 h-4 text-agri-forest" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"/>
                                                </svg>
                                                <span class="text-xs font-medium text-gray-700 truncate max-w-[150px]">{{ file.name }}</span>
                                                <button type="button" @click="removeFile(index)" class="text-red-500 hover:text-red-700">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="flex items-center bg-gray-50 rounded-2xl p-2 focus-within:ring-4 focus-within:ring-agri-forest/5 transition-all">
                                            <input ref="fileInput" type="file" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.zip" @change="handleFileSelect" class="hidden">
                                            <button type="button" @click="$refs.fileInput.click()" class="text-gray-400 hover:text-agri-forest p-2 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                                </svg>
                                            </button>
                                            <input 
                                                v-model="newMessage"
                                                type="text" 
                                                placeholder="Draft negotiation response..." 
                                                class="flex-1 bg-transparent border-none px-4 py-4 focus:outline-none font-medium text-sm text-gray-900"
                                            >
                                            <button 
                                                type="submit" 
                                                :disabled="(!newMessage.trim() && selectedFiles.length === 0) || sending"
                                                class="bg-agri-forest text-agri-earth w-12 h-12 rounded-xl flex items-center justify-center hover:bg-agri-lush hover:text-white disabled:opacity-30 transition-all shadow-lg"
                                            >
                                                <svg class="w-5 h-5 transform rotate-90" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </transition>
                </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick, watch } from 'vue';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const rfqs = ref([]);
const loading = ref(true);
const selectedRfq = ref(null);
const messages = ref([]);
const newMessage = ref('');
const sending = ref(false);
const processing = ref(false);
const messagesContainer = ref(null);
const pollingInterval = ref(null);

const fetchRfqs = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/rfqs');
    // Filter for requests where current user is the SELLER
    const myId = authStore.user?.id;
    rfqs.value = response.data.filter(r => r.seller_id === myId);
  } catch (error) {
    console.error('Failed to fetch RFQs:', error);
  } finally {
    loading.value = false;
  }
};

const viewNegotiation = async (rfq) => {
    selectedRfq.value = rfq;
    await fetchMessages(rfq.id);
    startPolling(rfq.id);
};

const userScrolledUp = ref(false);

const handleScroll = (e) => {
    const { scrollTop, scrollHeight, clientHeight } = e.target;
    userScrolledUp.value = scrollHeight - scrollTop - clientHeight > 50;
};

const shouldShowDateDivider = (msg, index, allMessages) => {
    if (index === 0) return true;
    const prevMsg = allMessages[index - 1];
    const currentDate = new Date(msg.created_at).toDateString();
    const prevDate = new Date(prevMsg.created_at).toDateString();
    return currentDate !== prevDate;
};

const formatDateDivider = (dateString) => {
    const date = new Date(dateString);
    const today = new Date();
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);
    
    if (date.toDateString() === today.toDateString()) return 'Today';
    if (date.toDateString() === yesterday.toDateString()) return 'Yesterday';
    return date.toLocaleDateString([], { weekday: 'short', month: 'short', day: 'numeric' });
};

const fetchMessages = async (rfqId, silent = false) => {
    try {
        const response = await axios.get(`/api/rfqs/${rfqId}/messages`);
        messages.value = response.data;
        if (!silent || !userScrolledUp.value) {
            scrollToBottom();
        }
    } catch (error) {
        console.error('Failed to load messages:', error);
        if (!silent) messages.value = [];
    }
};

const startPolling = (rfqId) => {
    stopPolling();
    pollingInterval.value = setInterval(() => {
        fetchMessages(rfqId, true); // Silent refresh
    }, 3000); // Poll every 3 seconds
};

const stopPolling = () => {
    if (pollingInterval.value) {
        clearInterval(pollingInterval.value);
        pollingInterval.value = null;
    }
};

// Watch for drawer close to stop polling
watch(selectedRfq, (newVal) => {
    if (!newVal) {
        stopPolling();
    }
});

const selectedFiles = ref([]);
const fileInput = ref(null);

// ... existing code ...

const handleFileSelect = (event) => {
    const files = Array.from(event.target.files);
    selectedFiles.value = [...selectedFiles.value, ...files];
    event.target.value = ''; // Reset input
};

const removeFile = (index) => {
    selectedFiles.value.splice(index, 1);
};

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
};

const sendMessage = async () => {
    if (!newMessage.value.trim() && selectedFiles.value.length === 0) return;
    
    sending.value = true;
    try {
        const formData = new FormData();
        formData.append('message', newMessage.value);
        // Vendor always replies to the Buyer
        if (selectedRfq.value?.buyer_id) {
            formData.append('receiver_id', selectedRfq.value.buyer_id);
        }
        
        // Append files
        selectedFiles.value.forEach((file, index) => {
            formData.append(`attachments[${index}]`, file);
        });
        
        const response = await axios.post(`/api/rfqs/${selectedRfq.value.id}/messages`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        
        messages.value.push(response.data);
        newMessage.value = '';
        selectedFiles.value = [];
        scrollToBottom();
        
        // If status was 'pending', it might have auto-updated on backend
        if(selectedRfq.value.status === 'pending') {
             selectedRfq.value.status = 'responded';
        }
        fetchRfqs(); // Refresh list

    } catch (error) {
        console.error('Failed to send message:', error);
        if (window.notify) window.notify.error('Error', 'Message sending failed.');
    } finally {
        sending.value = false;
    }
};

const updateStatus = async (status) => {
    processing.value = true;
    try {
        await axios.put(`/api/rfqs/${selectedRfq.value.id}`, { status });
        selectedRfq.value.status = status;
        if (window.notify) window.notify.success('Updated', `Request ${status} successfully.`);
        fetchRfqs(); // Refresh list
    } catch (error) {
        console.error('Failed to update status:', error);
    } finally {
        processing.value = false;
    }
};

const scrollToBottom = () => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
    });
};

const formatDate = (dateString) => {
    if (!dateString) return 'TBA';
    return new Date(dateString).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

const formatTime = (dateString) => {
    return new Date(dateString).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

const formatFullDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleString([], { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
};

const getStatusClasses = (status) => {
    switch (status) {
        case 'pending': return 'bg-yellow-50 text-yellow-600 border border-yellow-100';
        case 'responded': return 'bg-blue-50 text-blue-600 border border-blue-100';
        case 'accepted': return 'bg-agri-lush text-white';
        case 'rejected': return 'bg-red-50 text-red-600 border border-red-100';
        default: return 'bg-gray-50 text-gray-500';
    }
};

onMounted(() => {
  fetchRfqs();
});

onUnmounted(() => {
    stopPolling();
});
</script>
