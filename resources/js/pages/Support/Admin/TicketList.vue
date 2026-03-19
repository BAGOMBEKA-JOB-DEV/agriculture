<template>
    <div class="min-h-screen bg-gray-50/50 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="py-8 flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div>
                    <h1 class="text-3xl font-extrabold text-agri-forest tracking-tight">Ticket Queue</h1>
                    <p class="mt-1 text-sm text-gray-500 font-medium whitespace-nowrap">Manage and triage active support
                        requests.</p>
                </div>

                <div class="flex items-center space-x-3 w-full md:w-auto overflow-x-auto pb-2 md:pb-0">
                    <div class="relative group min-w-[200px]">
                        <input v-model="filters.search" type="text" placeholder="Search tickets..."
                            class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-agri-forest focus:border-transparent transition-all shadow-sm">
                        <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400 group-focus-within:text-agri-forest transition-colors"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>

                    <select v-model="filters.status"
                        class="bg-white border border-gray-200 rounded-xl text-sm px-4 py-2 focus:ring-2 focus:ring-agri-forest transition-all shadow-sm">
                        <option value="all">All Status</option>
                        <option value="open">Open</option>
                        <option value="pending">Pending</option>
                        <option value="resolved">Resolved</option>
                        <option value="closed">Closed</option>
                    </select>

                    <select v-model="filters.priority"
                        class="bg-white border border-gray-200 rounded-xl text-sm px-4 py-2 focus:ring-2 focus:ring-agri-forest transition-all shadow-sm">
                        <option value="all">All Priority</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
            </div>

            <!-- Tickets Table -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Ticket
                                </th>
                                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Customer
                                </th>
                                <th
                                    class="px-8 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-center">
                                    Priority</th>
                                <th
                                    class="px-8 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-center">
                                    SLA Status</th>
                                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Assigned
                                    To</th>
                                <th
                                    class="px-8 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-right">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr v-for="ticket in filteredTickets" :key="ticket.id"
                                class="group hover:bg-gray-50/80 transition-all duration-200">
                                <td class="px-8 py-5">
                                    <div class="flex items-center">
                                        <div
                                            :class="['w-2 h-10 rounded-full mr-4 flex-shrink-0', getStatusColor(ticket.status)]">
                                        </div>
                                        <div>
                                            <p
                                                class="text-sm font-bold text-gray-900 group-hover:text-agri-forest transition-colors">
                                                #{{ ticket.ticket_number }}: {{ ticket.subject }}</p>
                                            <div class="flex items-center mt-1 space-x-2">
                                                <span
                                                    class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{
                                                        ticket.category?.name || 'Inquiry' }}</span>
                                                <span class="text-gray-300">•</span>
                                                <span
                                                    class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{
                                                        formatTime(ticket.created_at) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-sm font-medium text-gray-700">
                                    <div class="flex items-center">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-agri-sand/30 flex items-center justify-center mr-2 text-[10px] font-black text-agri-forest">
                                            {{ ticket.customer?.name?.charAt(0) || 'U' }}
                                        </div>
                                        <span class="truncate max-w-[120px]">{{ ticket.customer?.name || 'Guest'
                                        }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <span
                                        :class="['px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest', getPriorityClass(ticket.priority)]">
                                        {{ ticket.priority }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <div v-if="ticket.due_date" class="flex flex-col items-center">
                                        <span
                                            :class="['text-[10px] font-black uppercase tracking-tighter', isOverdue(ticket.due_date) ? 'text-red-500' : 'text-gray-400']">
                                            {{ isOverdue(ticket.due_date) ? 'BREACHED' : 'DUE IN ' +
                                                getTimeRemaining(ticket.due_date) }}
                                        </span>
                                        <div class="w-16 h-1 mt-1 bg-gray-100 rounded-full overflow-hidden">
                                            <div :class="['h-full rounded-full', isOverdue(ticket.due_date) ? 'bg-red-500' : 'bg-agri-forest']"
                                                :style="{ width: getSlaProgress(ticket) + '%' }"></div>
                                        </div>
                                    </div>
                                    <span v-else class="text-[10px] font-bold text-gray-300">NO SLA Applied</span>
                                </td>
                                <td class="px-8 py-5 text-sm font-medium text-gray-600">
                                    <span v-if="ticket.assigned_agent" class="flex items-center text-xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-2"></span>
                                        {{ ticket.assigned_agent.name }}
                                    </span>
                                    <span v-else class="text-xs text-orange-400 font-bold italic">Unassigned</span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <router-link :to="`/support/admin/tickets/${ticket.id}`"
                                        class="inline-flex items-center px-3 py-1.5 bg-gray-50 hover:bg-agri-forest hover:text-white rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">
                                        Open
                                    </router-link>
                                </td>
                            </tr>

                            <!-- Empty State -->
                            <tr v-if="filteredTickets.length === 0">
                                <td colspan="6" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="p-6 bg-gray-50 rounded-full mb-4">
                                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                                </path>
                                            </svg>
                                        </div>
                                        <h3 class="text-sm font-bold text-gray-900">No tickets found</h3>
                                        <p class="text-xs text-gray-500 mt-1">Try adjusting your filters or search
                                            terms.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const tickets = ref([])
const filters = ref({
    search: '',
    status: 'all',
    priority: 'all'
})

const filteredTickets = computed(() => {
    return tickets.value.filter(t => {
        const matchesSearch = t.subject?.toLowerCase().includes(filters.value.search.toLowerCase()) ||
            t.ticket_number?.toLowerCase().includes(filters.value.search.toLowerCase())
        const matchesStatus = filters.value.status === 'all' || t.status === filters.value.status
        const matchesPriority = filters.value.priority === 'all' || t.priority === filters.value.priority
        return matchesSearch && matchesStatus && matchesPriority
    })
})

const fetchTickets = async () => {
    try {
        const response = await axios.get('/api/support/admin/tickets')
        tickets.value = response.data.data || response.data
    } catch (error) {
        console.error('Failed to fetch tickets:', error)
    }
}

const getStatusColor = (status) => {
    switch (status) {
        case 'open': return 'bg-blue-500'
        case 'pending': return 'bg-orange-500'
        case 'resolved': return 'bg-green-500'
        case 'closed': return 'bg-gray-400'
        default: return 'bg-gray-200'
    }
}

const getPriorityClass = (priority) => {
    switch (priority) {
        case 'urgent': return 'bg-red-100 text-red-600'
        case 'high': return 'bg-orange-100 text-orange-600'
        case 'medium': return 'bg-blue-100 text-blue-600'
        case 'low': return 'bg-gray-100 text-gray-500'
        default: return 'bg-gray-50 text-gray-400'
    }
}

const isOverdue = (date) => new Date(date) < new Date()

const getTimeRemaining = (date) => {
    const diff = new Date(date) - new Date()
    const hours = Math.floor(diff / (1000 * 60 * 60))
    if (hours < 1) return ' < 1H'
    return hours + 'H'
}

const getSlaProgress = (ticket) => {
    if (!ticket.due_date) return 0
    if (isOverdue(ticket.due_date)) return 100
    // Simple calculation: progress based on 24h cycle for demo
    const elapsed = (new Date() - new Date(ticket.created_at)) / (1000 * 60 * 60)
    return Math.min(100, Math.round((elapsed / 24) * 100))
}

const formatTime = (date) => {
    const d = new Date(date)
    return d.toLocaleDateString() + ' ' + d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

onMounted(fetchTickets)
</script>
