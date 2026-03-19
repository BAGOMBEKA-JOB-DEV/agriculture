<template>
    <div class="min-h-screen bg-gray-50/50">
        <div v-if="loading" class="flex items-center justify-center h-[80vh]">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-agri-forest"></div>
        </div>

        <div v-else-if="ticket" class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Breadcrumb & Actions -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                <div class="flex items-center space-x-4">
                    <router-link to="/support/admin/tickets"
                        class="p-2 bg-white rounded-xl border border-gray-100 text-gray-400 hover:text-agri-forest shadow-sm transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </router-link>
                    <div>
                        <div class="flex items-center space-x-2">
                            <span
                                class="text-[10px] font-black uppercase tracking-widest text-agri-forest bg-agri-sand/30 px-2 py-0.5 rounded">#{{
                                    ticket.ticket_number }}</span>
                            <h1 class="text-xl font-black text-gray-900 truncate max-w-[400px]">{{ ticket.subject }}
                            </h1>
                        </div>
                        <p class="text-xs font-medium text-gray-400 mt-0.5">Opened by <span
                                class="text-gray-600 font-bold">{{ ticket.customer?.name }}</span> • {{
                                    formatTime(ticket.created_at) }}</p>
                    </div>
                </div>

                <div class="flex items-center space-x-2 mt-4 md:mt-0">
                    <button @click="closeTicket"
                        class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-600 hover:bg-red-50 hover:text-red-600 transition-all shadow-sm">
                        Close Ticket
                    </button>
                    <div class="h-8 w-px bg-gray-200 mx-2"></div>
                    <select v-model="ticket.status" @change="updateTicketStatus"
                        class="bg-white border border-gray-200 rounded-xl text-xs font-bold px-4 py-2 focus:ring-2 focus:ring-agri-forest shadow-sm cursor-pointer">
                        <option value="open">Open</option>
                        <option value="pending">Pending</option>
                        <option value="resolved">Resolved</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Main Conversation Area -->
                <div class="lg:col-span-3 flex flex-col space-y-6">
                    <!-- Chat Thread -->
                    <div
                        class="bg-white rounded-3xl shadow-sm border border-gray-100 flex flex-col min-h-[600px] max-h-[800px] relative overflow-hidden">
                        <!-- Messages List -->
                        <div class="flex-1 overflow-y-auto p-8 space-y-8 scrollbar-hide" id="message-container">
                            <!-- Initial Description -->
                            <!-- Initial Description -->
                            <div class="flex flex-col mb-4 items-start">
                                <div class="relative group max-w-[85%]">
                                    <div
                                        class="rounded-3xl px-8 py-5 shadow-sm bg-white text-gray-800 rounded-bl-none border border-gray-100">
                                        <p class="text-sm font-medium leading-relaxed whitespace-pre-wrap">{{
                                            ticket.description }}</p>
                                    </div>
                                    <div class="flex items-center mt-1 space-x-1.5 px-2">
                                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">
                                            {{ ticket.customer?.name }} • {{ formatTime(ticket.created_at) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Messages Loop -->
                            <div v-for="message in messages" :key="message.id" class="mb-2">
                                <!-- Internal Note -->
                                <div v-if="message.is_internal"
                                    class="bg-yellow-50/50 -mx-8 px-8 py-4 border-y border-yellow-100/50 flex items-start space-x-4 mb-4">
                                    <div
                                        class="w-10 h-10 rounded-2xl bg-yellow-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div
                                            class="rounded-2xl p-6 shadow-sm bg-yellow-100/30 border border-yellow-200">
                                            <p class="text-sm leading-relaxed whitespace-pre-wrap font-medium">{{
                                                message.message }}</p>
                                        </div>
                                        <div class="mt-2 flex items-center space-x-2">
                                            <span
                                                class="text-[10px] font-black text-gray-400 tracking-wider uppercase">Internal
                                                Note</span>
                                            <span class="text-gray-300">•</span>
                                            <span class="text-[10px] font-medium text-gray-400">{{
                                                formatTime(message.created_at) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Regular Message -->
                                <div v-else class="flex mb-4"
                                    :class="message.user_id === currentUser.id ? 'justify-end' : 'justify-start'">

                                    <!-- Avatar (Received) -->
                                    <div v-if="message.user_id !== currentUser.id" class="flex-shrink-0 mr-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-agri-sand flex items-center justify-center text-[10px] font-black text-agri-forest border border-gray-100 uppercase overflow-hidden">
                                            <img v-if="message.user?.avatar_url" :src="message.user.avatar_url"
                                                class="w-full h-full object-cover">
                                            <span v-else>{{ message.user?.name?.charAt(0) || 'U' }}</span>
                                        </div>
                                    </div>

                                    <div class="relative group max-w-[75%] flex flex-col"
                                        :class="message.user_id === currentUser.id ? 'items-end' : 'items-start'">
                                        <div class="rounded-3xl px-8 py-5 shadow-sm"
                                            :class="message.user_id === currentUser.id ? 'bg-agri-forest text-white rounded-br-none' : 'bg-white text-gray-800 rounded-bl-none border border-gray-100'">
                                            <p class="text-sm font-medium leading-relaxed whitespace-pre-wrap">{{
                                                message.message }}</p>
                                        </div>
                                        <div class="flex items-center mt-1 space-x-1.5 px-2"
                                            :class="message.user_id === currentUser.id ? 'justify-end' : ''">
                                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">
                                                {{ message.user_id === currentUser.id ? 'You' : message.user?.name }} •
                                                {{ formatTime(message.created_at) }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Avatar (Sent) -->
                                    <div v-if="message.user_id === currentUser.id" class="flex-shrink-0 ml-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-agri-forest flex items-center justify-center text-[10px] font-black text-white border border-white/20 uppercase overflow-hidden">
                                            <img v-if="currentUser.avatar_url" :src="currentUser.avatar_url"
                                                class="w-full h-full object-cover">
                                            <span v-else>{{ currentUser.name?.charAt(0) || 'A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Composer Area -->
                        <div class="p-6 bg-white border-t border-gray-100">
                            <div class="flex items-center space-x-4 mb-4">
                                <button @click="composerMode = 'reply'"
                                    :class="['px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all', composerMode === 'reply' ? 'bg-agri-forest text-white shadow-md' : 'text-gray-400 hover:text-gray-600 bg-gray-50']">Reply</button>
                                <button @click="composerMode = 'note'"
                                    :class="['px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all', composerMode === 'note' ? 'bg-yellow-400 text-yellow-900 shadow-md' : 'text-gray-400 hover:text-gray-600 bg-gray-50']">Internal
                                    Note</button>
                            </div>
                            <div class="relative group">
                                <textarea v-model="newMessage"
                                    :placeholder="composerMode === 'reply' ? 'Type your reply...' : 'Type a private note for staff...'"
                                    class="w-full bg-gray-50/50 border border-gray-100 rounded-2xl p-6 text-sm focus:ring-2 focus:ring-agri-forest focus:bg-white transition-all min-h-[120px] resize-none"></textarea>
                                <div class="absolute bottom-4 right-4 flex items-center space-x-2">
                                    <button @click="sendMessage" :disabled="!newMessage.trim()"
                                        :class="['p-3 rounded-xl transition-all shadow-lg shadow-agri-forest/20', newMessage.trim() ? (composerMode === 'reply' ? 'bg-agri-forest text-white hover:scale-105' : 'bg-yellow-400 text-yellow-900 hover:scale-105') : 'bg-gray-200 text-gray-400 pointer-events-none']">
                                        <svg class="w-5 h-5 translate-x-0.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Details -->
                <div class="space-y-6">
                    <!-- Metadata Card -->
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6">Properties</h3>
                        <div class="space-y-6">
                            <div>
                                <label
                                    class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Priority</label>
                                <div
                                    :class="['inline-flex px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest', getPriorityClass(ticket.priority)]">
                                    {{ ticket.priority }}
                                </div>
                            </div>
                            <div>
                                <label
                                    class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">SLA
                                    Progress</label>
                                <div v-if="ticket.due_date">
                                    <p
                                        :class="['text-xs font-bold mb-2', isOverdue(ticket.due_date) ? 'text-red-500' : 'text-agri-forest']">
                                        {{ isOverdue(ticket.due_date) ? 'BREACHED' : 'Due in ' +
                                            getTimeRemaining(ticket.due_date) }}
                                    </p>
                                    <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                        <div :class="['h-full rounded-full', isOverdue(ticket.due_date) ? 'bg-red-500' : 'bg-agri-forest']"
                                            :style="{ width: getSlaProgress() + '%' }"></div>
                                    </div>
                                </div>
                                <div v-else class="text-xs font-bold text-gray-300 italic">No SLA applied</div>
                            </div>
                            <div>
                                <label
                                    class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Assigned
                                    Agent</label>
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-agri-sand flex items-center justify-center text-[10px] font-black text-agri-forest">
                                        {{ ticket.assigned_agent?.name?.charAt(0) || '?' }}
                                    </div>
                                    <span class="text-sm font-bold text-gray-700">{{ ticket.assigned_agent?.name ||
                                        'Unassigned' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- History/Timeline Card -->
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6">History</h3>
                        <div class="space-y-6 relative overflow-hidden">
                            <div v-for="log in history" :key="log.id"
                                class="relative pl-6 pb-2 border-l border-gray-100 last:border-0 last:pb-0">
                                <div
                                    class="absolute -left-1 top-0 w-2 h-2 rounded-full bg-agri-forest border border-white">
                                </div>
                                <p class="text-[11px] font-bold text-gray-700 leading-tight">{{ log.description ||
                                    `Field ${log.field} changed` }}</p>
                                <p class="text-[9px] font-medium text-gray-400 uppercase tracking-wider mt-1">{{
                                    formatTime(log.created_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const ticket = ref(null)
const messages = ref([])
const history = ref([])
const loading = ref(true)
const newMessage = ref('')
const composerMode = ref('reply')
const currentUser = ref({ id: 1, name: 'Agent' }) // Should come from store

const fetchTicketDetail = async () => {
    try {
        const response = await axios.get(`/api/support/admin/tickets/${route.params.id}`)
        ticket.value = response.data
        messages.value = response.data.messages || []
        history.value = response.data.history || []
        await nextTick()
        scrollToBottom()
    } catch (error) {
        console.error('Failed to fetch ticket:', error)
    } finally {
        loading.value = false
    }
}

const sendMessage = async () => {
    if (!newMessage.value.trim()) return
    try {
        const response = await axios.post(`/api/support/admin/tickets/${ticket.value.id}/messages`, {
            body: newMessage.value,
            is_internal: composerMode.value === 'note'
        })
        messages.value.push(response.data)
        newMessage.value = ''
        await nextTick()
        scrollToBottom()
    } catch (error) {
        console.error('Failed to send message:', error)
    }
}

const updateTicketStatus = async () => {
    try {
        await axios.post(`/api/support/admin/tickets/${ticket.value.id}/status`, {
            status: ticket.value.status
        })
    } catch (error) {
        console.error('Failed to update status:', error)
    }
}

const closeTicket = async () => {
    if (confirm('Are you sure you want to close this ticket?')) {
        ticket.value.status = 'closed'
        await updateTicketStatus()
    }
}

const scrollToBottom = () => {
    const container = document.getElementById('message-container')
    if (container) {
        container.scrollTop = container.scrollHeight
    }
}

// Utility functions
const formatTime = (date) => {
    const d = new Date(date)
    return d.toLocaleDateString() + ' ' + d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

const getPriorityClass = (priority) => {
    switch (priority) {
        case 'urgent': return 'bg-red-500 text-white shadow-lg shadow-red-500/20'
        case 'high': return 'bg-orange-500 text-white shadow-lg shadow-orange-500/20'
        case 'medium': return 'bg-blue-500 text-white shadow-lg shadow-blue-500/20'
        case 'low': return 'bg-agri-forest text-white shadow-lg shadow-agri-forest/20'
        default: return 'bg-gray-100 text-gray-500'
    }
}

const isOverdue = (date) => new Date(date) < new Date()
const getTimeRemaining = (date) => {
    const diff = new Date(date) - new Date()
    const hours = Math.floor(diff / (1000 * 60 * 60))
    return hours + 'H'
}
const getSlaProgress = () => {
    if (!ticket.value?.due_date) return 0
    const elapsed = (new Date() - new Date(ticket.value.created_at)) / (1000 * 60 * 60)
    return Math.min(100, Math.round((elapsed / 24) * 100))
}

onMounted(async () => {
    const userResp = await axios.get('/api/user')
    currentUser.value = userResp.data
    fetchTicketDetail()
})
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
