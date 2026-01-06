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

    <div class="container mx-auto px-6 -mt-8 relative z-20">
      <div class="bg-white rounded-[2.5rem] shadow-2xl border border-gray-100 flex h-[700px] overflow-hidden">
        <!-- Conversation List -->
        <div class="w-1/3 border-r border-gray-100 flex flex-col bg-gray-50/30">
          <div class="p-8 border-b border-gray-100 bg-white">
            <h2 class="text-xs font-black text-gray-400 uppercase tracking-widest">Active Channels</h2>
          </div>
          <div class="flex-1 overflow-y-auto">
            <div v-for="(msgs, participantId) in conversations" 
                 :key="participantId"
                 @click="selectConversation(participantId)"
                 class="p-6 border-b border-gray-50 cursor-pointer hover:bg-white transition-all duration-300 relative group"
                 :class="{ 'bg-white shadow-inner': selectedParticipantId === participantId }">
              <div v-if="selectedParticipantId === participantId" class="absolute left-0 top-0 bottom-0 w-1.5 bg-agri-forest"></div>
              
              <div class="flex items-center mb-3">
                <div class="w-10 h-10 rounded-xl bg-agri-sand flex items-center justify-center text-agri-forest font-black mr-3">
                    {{ getParticipantName(msgs[0], participantId).charAt(0) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-center">
                        <span class="font-black text-gray-900 truncate">{{ getParticipantName(msgs[0], participantId) }}</span>
                        <span class="text-[10px] font-bold text-gray-400">{{ formatRecentDate(msgs[0].created_at) }}</span>
                    </div>
                </div>
              </div>
              <p class="text-xs text-gray-500 font-medium truncate leading-relaxed">
                {{ msgs[0].content }}
              </p>
            </div>
            
            <div v-if="Object.keys(conversations).length === 0" class="p-12 text-center text-gray-400">
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
                 <div class="w-10 h-10 rounded-xl bg-agri-forest text-agri-earth flex items-center justify-center font-black mr-4">
                     {{ selectedParticipantName.charAt(0) }}
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
                   class="flex flex-col"
                   :class="msg.sender_id === authStore.user?.id ? 'items-end' : 'items-start'">
                <div class="max-w-[80%] rounded-3xl px-8 py-5 shadow-sm"
                     :class="msg.sender_id === authStore.user?.id ? 'bg-agri-forest text-white rounded-br-none' : 'bg-white text-gray-800 rounded-bl-none border border-gray-100'">
                  <p class="text-sm font-medium leading-relaxed">{{ msg.content }}</p>
                </div>
                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-2 px-2">
                    {{ formatFullDate(msg.created_at) }}
                </span>
              </div>
            </div>

            <!-- Input -->
            <div class="p-8 border-t border-gray-100 bg-white">
              <form @submit.prevent="sendMessage" class="flex items-center bg-gray-50 rounded-2xl p-2 focus-within:ring-4 focus-within:ring-agri-forest/5 transition-all">
                <input v-model="newMessage" 
                       type="text" 
                       placeholder="Draft trade clarification..." 
                       class="flex-1 bg-transparent border-none px-6 py-4 focus:outline-none font-medium text-sm text-gray-900">
                <button type="submit" 
                        :disabled="!newMessage.trim()"
                        class="bg-agri-forest text-agri-earth w-12 h-12 rounded-xl flex items-center justify-center hover:bg-agri-lush hover:text-white disabled:opacity-30 transition-all shadow-lg">
                  <svg class="w-5 h-5 transform rotate-90" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
                  </svg>
                </button>
              </form>
            </div>
          </template>
          <div v-else class="flex-1 flex flex-col items-center justify-center text-gray-400 bg-gray-50/30">
            <div class="w-20 h-20 rounded-[2rem] bg-white shadow-sm flex items-center justify-center mb-6 text-gray-200">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
            </div>
            <p class="font-black uppercase text-xs tracking-widest">Select channel to view transcripts</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
import { useAuthStore } from '../../stores/auth';
import axios from 'axios';

const authStore = useAuthStore();
const conversations = ref({});
const activeMessages = ref([]);
const selectedParticipantId = ref(null);
const newMessage = ref('');
const messagesContainer = ref(null);

const fetchConversations = async () => {
  try {
    const response = await axios.get('/api/messages');
    conversations.value = response.data;
    
    // Fallback Mock Data
    if (Object.keys(conversations.value).length === 0) {
        const mockUser1 = { id: 5, name: 'Nakuru Harvest' };
        const mockUser2 = { id: 8, name: 'Coastal Traders' };
        conversations.value = {
            '5': [
                { id: 1, sender_id: 5, receiver_id: authStore.user?.id, content: 'We have received your RFQ for 100 tons of white maize.', created_at: new Date().toISOString(), sender: mockUser1, receiver: authStore.user },
                { id: 2, sender_id: authStore.user?.id, receiver_id: 5, content: 'Great. Can you confirm the moisture content?', created_at: new Date().toISOString(), sender: authStore.user, receiver: mockUser1 }
            ],
            '8': [
                { id: 3, sender_id: 8, receiver_id: authStore.user?.id, content: 'The cashew nut shipment is ready for inspection.', created_at: new Date(Date.now() - 3600000).toISOString(), sender: mockUser2, receiver: authStore.user }
            ]
        };
    }
  } catch (error) {
    console.error('Error fetching conversations:', error);
  }
};

const selectedParticipantName = computed(() => {
    if (!selectedParticipantId.value || !conversations.value || !conversations.value[selectedParticipantId.value]) return 'Unknown Node';
    const convo = conversations.value[selectedParticipantId.value];
    if (!convo || !convo[0]) return 'Unknown Node';
    const firstMsg = convo[0];
    const isSentByMe = firstMsg.sender_id === authStore.user?.id;
    return isSentByMe ? (firstMsg.receiver?.name || 'Receiver') : (firstMsg.sender?.name || 'Sender');
});

const getParticipantName = (msg, id) => {
    if (!msg) return 'Identity Undefined';
    const isSentByMe = msg.sender_id === authStore.user?.id;
    return isSentByMe ? (msg.receiver?.name || 'Trade Partner') : (msg.sender?.name || 'Partner');
};

const selectConversation = async (participantId) => {
  if (!participantId || !conversations.value || !conversations.value[participantId]) return;
  selectedParticipantId.value = participantId;
  const convo = conversations.value[participantId];
  activeMessages.value = [...convo].sort((a,b) => new Date(a.created_at) - new Date(b.created_at));
  scrollToBottom();
};

const sendMessage = async () => {
  if (!newMessage.value.trim() || !selectedParticipantId.value) return;

  try {
    const response = await axios.post('/api/messages', {
      receiver_id: selectedParticipantId.value,
      content: newMessage.value
    });
    
    // Optimistic update
    activeMessages.value.push(response.data);
    newMessage.value = '';
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
  return new Date(date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

const formatFullDate = (date) => {
  return new Date(date).toLocaleString([], { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
};

onMounted(() => {
  fetchConversations();
});
</script>

<style scoped>
.tracking-tighter {
  letter-spacing: -2px;
}
</style>
