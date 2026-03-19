<template>
    <div class="min-h-screen bg-gray-50/50 pb-20">
        <div v-if="loading" class="flex items-center justify-center h-[80vh]">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-agri-forest"></div>
        </div>

        <div v-else-if="ticket" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
                <div class="flex items-center space-x-5">
                    <router-link to="/support"
                        class="p-2.5 bg-white rounded-2xl border border-gray-100 text-gray-400 hover:text-agri-forest shadow-sm transition-all hover:scale-105 active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </router-link>
                    <div>
                        <div class="flex items-center space-x-3 mb-1">
                            <span
                                class="px-2.5 py-1 bg-agri-forest/5 text-agri-forest text-[10px] font-black uppercase tracking-widest rounded-md border border-agri-forest/10">
                                #{{ ticket.ticket_number }}
                            </span>
                            <h1 class="text-2xl font-black text-gray-900 tracking-tight">{{ ticket.subject }}</h1>
                        </div>
                        <div class="flex items-center space-x-4 text-xs font-medium text-gray-400">
                            <span>Opened on {{ formatDate(ticket.created_at) }}</span>
                            <span class="w-1 h-1 rounded-full bg-gray-200"></span>
                            <span>Ref ID: <span class="font-bold text-gray-600">{{ ticket.id }}</span></span>
                        </div>
                    </div>
                </div>

                <div v-if="ticket.status !== 'closed'" class="flex items-center space-x-3">
                    <button @click="showCloseModal = true"
                        class="px-6 py-2.5 bg-white border border-gray-200 rounded-xl text-xs font-black uppercase tracking-wider text-gray-400 hover:bg-red-50 hover:text-red-600 hover:border-red-100 transition-all shadow-sm">
                        Close Ticket
                    </button>
                </div>
            </div>

            <!-- Global Reusable Modal -->
            <Modal :isOpen="showCloseModal" title="Close this Ticket?"
                message="This will mark the issue as resolved. This action cannot be undone." type="danger"
                confirmText="Yes, Close" cancelText="Keep Open" @confirm="confirmClose"
                @cancel="showCloseModal = false" />

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">
                <!-- Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Ticket Details Card -->
                    <div class="bg-white rounded-[32px] border border-gray-100 p-6 shadow-sm">
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Properties</h3>
                        <div class="space-y-4">
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Status</label>
                                <span
                                    :class="['inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border', getStatusBadgeStyle(ticket.status)]">
                                    <span class="w-1.5 h-1.5 rounded-full mr-2"
                                        :class="getStatusDotStyle(ticket.status)"></span>
                                    {{ ticket.status }}
                                </span>
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Category</label>
                                <div class="flex items-center space-x-2">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                            </path>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-bold text-gray-700">{{ ticket.category?.name || 'General'
                                        }}</span>
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Assigned
                                    Agent</label>
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-agri-forest flex items-center justify-center text-[10px] font-black text-white uppercase overflow-hidden border-2 border-white shadow-sm">
                                        {{ ticket.agent?.name?.charAt(0) || 'S' }}
                                    </div>
                                    <span class="text-sm font-bold text-gray-700">{{ ticket.agent?.name || 'Support Team' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description Mini-Card -->
                    <div class="bg-white rounded-[32px] border border-gray-100 p-6 shadow-sm">
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Original Issue
                        </h3>
                        <p
                            class="text-xs font-medium text-gray-500 leading-relaxed italic border-l-2 border-agri-sand pl-3">
                            "{{ ticket.description }}"
                        </p>
                    </div>

                    <!-- Timeline/History -->
                    <div class="bg-white rounded-[32px] border border-gray-100 p-6 shadow-sm pb-8">
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6">Activity
                            Timeline</h3>
                        <div class="relative space-y-6">
                            <div class="absolute left-3.5 top-0 bottom-0 w-px bg-gray-100"></div>
                            <div v-for="event in ticket.history?.slice(0, 3)" :key="event.id"
                                class="relative flex items-start space-x-4 pl-0.5">
                                <div
                                    class="relative z-10 w-7 h-7 rounded-full bg-white border border-gray-100 flex items-center justify-center text-agri-forest shadow-sm">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-900 leading-tight">{{
                                        formatAction(event.action) }}</p>
                                    <p class="text-[9px] font-bold text-gray-400 mt-0.5">{{ formatTime(event.created_at)
                                        }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chat Section -->
                <div class="lg:col-span-3">
                    <div
                        class="bg-white rounded-[40px] shadow-sm border border-gray-100 flex flex-col min-h-[600px] overflow-hidden">
                        <!-- Messages Area -->
                        <div class="flex-1 overflow-y-auto p-8 space-y-10" id="message-container">
                            <div v-for="message in visibleMessages" :key="message.id" class="flex"
                                :class="message.user_id === currentUser.id ? 'justify-end' : 'justify-start'">

                                <!-- Received Message Layout -->
                                <div v-if="message.user_id !== currentUser.id"
                                    class="flex max-w-[85%] items-end space-x-4">
                                    <div class="flex-shrink-0 mb-2">
                                        <div
                                            class="w-10 h-10 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center text-xs font-black text-agri-forest uppercase shadow-sm overflow-hidden">
                                            <img v-if="message.user?.avatar_url" :src="message.user.avatar_url"
                                                class="w-full h-full object-cover">
                                            <span v-else>{{ message.user?.name?.charAt(0) || 'S' }}</span>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-start translate-y-2">
                                        <div
                                            class="rounded-3xl px-8 py-5 bg-gray-50 text-gray-800 rounded-bl-none border border-gray-100 shadow-sm">
                                            <p class="text-sm font-medium leading-relaxed whitespace-pre-wrap">{{
                                                message.message }}</p>
                                        </div>
                                        <span
                                            class="text-[9px] font-black text-gray-300 uppercase tracking-widest mt-2 px-2">{{
                                                formatTime(message.created_at) }}</span>
                                    </div>
                                </div>

                                <!-- Sent Message Layout -->
                                <div v-else class="flex max-w-[85%] items-end space-x-4 justify-end">
                                    <div class="flex flex-col items-end translate-y-2">
                                        <div
                                            class="rounded-3xl px-8 py-5 bg-agri-forest text-white rounded-br-none shadow-lg shadow-agri-forest/20">
                                            <p class="text-sm font-medium leading-relaxed whitespace-pre-wrap">{{
                                                message.message }}</p>
                                        </div>
                                        <div class="flex items-center space-x-1.5 mt-2 px-2">
                                            <span
                                                class="text-[9px] font-black text-gray-300 uppercase tracking-widest">{{
                                                    formatTime(message.created_at) }}</span>
                                            <svg class="w-3 h-3 text-agri-forest" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 mb-2">
                                        <div
                                            class="w-10 h-10 rounded-2xl bg-white border-2 border-agri-forest/10 flex items-center justify-center text-xs font-black text-agri-forest uppercase shadow-sm overflow-hidden">
                                            <img v-if="currentUser.avatar_url" :src="currentUser.avatar_url"
                                                class="w-full h-full object-cover">
                                            <span v-else>Y</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Composer -->
                        <div v-if="ticket.status !== 'closed'" class="p-8 bg-white border-t border-gray-50">
                            <div class="relative group">
                                <textarea v-model="newMessage" placeholder="Reply to support..."
                                    class="w-full bg-gray-50/50 border border-gray-100 rounded-3xl p-6 text-sm font-medium focus:ring-4 focus:ring-agri-forest/5 focus:bg-white focus:border-agri-forest/20 transition-all min-h-[120px] resize-none outline-none"></textarea>
                                <div class="absolute bottom-5 right-5 flex items-center space-x-4">
                                    <button @click="sendMessage" :disabled="!newMessage.trim() || sending"
                                        class="p-4 bg-agri-forest text-white rounded-2xl transition-all shadow-xl shadow-agri-forest/30 hover:scale-110 active:scale-95 disabled:opacity-50 disabled:pointer-events-none group/send">
                                        <svg v-if="!sending" class="w-5 h-5 group-hover:rotate-12 transition-transform"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                        </svg>
                                        <div v-else
                                            class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin">
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div v-else class="p-10 bg-gray-50 text-center">
                            <div
                                class="w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto mb-3 border border-gray-100">
                                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                    </path>
                                </svg>
                            </div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">This ticket is
                                closed and read-only.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import Modal from '@/components/Modal.vue'

const route = useRoute()
const ticket = ref(null)
const messages = ref([])
const loading = ref(true)
const sending = ref(false)
const showCloseModal = ref(false)
const newMessage = ref('')
const currentUser = ref({ id: 0 })

const visibleMessages = computed(() => {
    return messages.value.filter(m => !m.is_internal)
})

const fetchTicketDetail = async () => {
    try {
        const response = await axios.get(`/api/support/tickets/${route.params.id}`)
        ticket.value = response.data
        messages.value = response.data.messages || []
        await nextTick()
        scrollToBottom()
    } catch (error) {
        console.error('Failed to fetch ticket:', error)
    } finally {
        loading.value = false
    }
}

const sendMessage = async () => {
    if (!newMessage.value.trim() || sending.value) return
    sending.value = true
    try {
        const response = await axios.post(`/api/support/tickets/${ticket.value.id}/messages`, {
            body: newMessage.value
        })
        messages.value.push(response.data)
        newMessage.value = ''
        await nextTick()
        scrollToBottom()
    } catch (error) {
        console.error('Failed to send message:', error)
    } finally {
        sending.value = false
    }
}

const confirmClose = async () => {
    showCloseModal.value = false
    try {
        await axios.post(`/api/support/tickets/${ticket.value.id}/close`)
        ticket.value.status = 'closed'
    } catch (error) {
        console.error('Failed to close ticket:', error)
    }
}

const scrollToBottom = () => {
    const container = document.getElementById('message-container')
    if (container) {
        container.scrollTop = container.scrollHeight
    }
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

const formatTime = (date) => {
    const d = new Date(date)
    return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

const formatAction = (action) => {
    return action.split('_').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ')
}

const getStatusBadgeStyle = (status) => {
    switch (status) {
        case 'open': return 'bg-blue-50 text-blue-600 border-blue-100'
        case 'pending': return 'bg-orange-50 text-orange-600 border-orange-100'
        case 'resolved': return 'bg-green-50 text-green-600 border-green-100'
        case 'closed': return 'bg-gray-50 text-gray-500 border-gray-200'
        default: return 'bg-gray-50 text-gray-400 border-gray-100'
    }
}

const getStatusDotStyle = (status) => {
    switch (status) {
        case 'open': return 'bg-blue-500'
        case 'pending': return 'bg-orange-500'
        case 'resolved': return 'bg-green-500'
        default: return 'bg-gray-300'
    }
}

onMounted(async () => {
    try {
        const userResp = await axios.get('/api/user')
        currentUser.value = userResp.data
    } catch (e) { }
    fetchTicketDetail()
})
</script>

<style scoped>
.bg-agri-forest {
    background-color: #155724;
}

.text-agri-forest {
    color: #155724;
}

.border-agri-forest {
    border-color: #155724;
}

.bg-agri-sand {
    background-color: #f8f9fa;
}

/* Scrollbar Styling */
#message-container::-webkit-scrollbar {
    width: 6px;
}

#message-container::-webkit-scrollbar-track {
    background: transparent;
}

#message-container::-webkit-scrollbar-thumb {
    background: #f1f1f1;
    border-radius: 10px;
}

#message-container::-webkit-scrollbar-thumb:hover {
    background: #e5e7eb;
}
</style>
