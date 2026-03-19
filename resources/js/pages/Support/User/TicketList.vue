<template>
    <div class="min-h-screen bg-gray-50/50 pb-20">
        <!-- Header Section -->
        <div class="bg-white border-b border-gray-100 pt-16 pb-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center space-x-4">
                        <router-link to="/support" class="p-2.5 bg-gray-50 rounded-xl text-gray-400 hover:text-agri-forest hover:bg-white border border-gray-100 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        </router-link>
                        <div>
                            <h1 class="text-2xl font-black text-gray-900 tracking-tight">My Support Requests</h1>
                            <p class="text-sm font-medium text-gray-500">Manage and track all your active and past tickets</p>
                        </div>
                    </div>
                    
                    <button @click="showNewTicketModal = true" class="px-6 py-3 bg-agri-forest text-white text-xs font-black uppercase tracking-widest rounded-xl shadow-lg shadow-agri-forest/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                        Open New Ticket
                    </button>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
            <!-- Filters & Search -->
            <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-8">
                <div class="flex items-center space-x-2 p-1 bg-white rounded-2xl border border-gray-100 shadow-sm">
                    <button v-for="status in ['all', 'open', 'pending', 'resolved', 'closed']" :key="status"
                        @click="filterStatus = status"
                        :class="[filterStatus === status ? 'bg-agri-forest text-white' : 'text-gray-500 hover:bg-gray-50']"
                        class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                        {{ status }}
                    </button>
                </div>
                
                <div class="relative w-full md:w-80 group">
                    <input v-model="searchQuery" type="text" placeholder="Search by subject or #ID..." 
                        class="w-full pl-12 pr-4 py-3 bg-white border border-gray-100 rounded-2xl text-sm focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest transition-all outline-none shadow-sm">
                    <svg class="w-5 h-5 absolute left-4 top-3 text-gray-300 group-focus-within:text-agri-forest transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            <!-- Ticket List -->
            <div v-if="loading" class="flex flex-col items-center justify-center py-20 space-y-4">
                <div class="w-12 h-12 border-4 border-agri-forest border-t-transparent rounded-full animate-spin"></div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest animate-pulse">Retrieving your requests...</p>
            </div>

            <div v-else-if="filteredTickets.length > 0" class="space-y-4">
                <div v-for="ticket in filteredTickets" :key="ticket.id" @click="$router.push(`/support/tickets/${ticket.id}`)"
                    class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md hover:border-agri-forest/20 transition-all cursor-pointer group">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="flex-1 space-y-2">
                            <div class="flex items-center space-x-3">
                                <span :class="getStatusBadgeClass(ticket.status)" class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-widest transition-colors">
                                    {{ ticket.status }}
                                </span>
                                <span class="text-xs font-mono text-gray-400">#{{ ticket.ticket_number }}</span>
                            </div>
                            <h3 class="text-base font-bold text-gray-900 group-hover:text-agri-forest transition-colors">{{ ticket.subject }}</h3>
                            <div class="flex items-center text-xs text-gray-500 space-x-4">
                                <span class="flex items-center"><svg class="w-3.5 h-3.5 mr-1.5 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>Created {{ formatDate(ticket.created_at) }}</span>
                                <span class="flex items-center"><svg class="w-3.5 h-3.5 mr-1.5 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>{{ ticket.category?.name || 'General' }}</span>
                            </div>
                        </div>
                        
                        <div class="flex flex-col items-end gap-3">
                            <div class="flex -space-x-2">
                                <div v-if="ticket.agent" class="w-8 h-8 rounded-full bg-agri-sand border-2 border-white flex items-center justify-center text-[10px] font-black text-agri-forest uppercase" :title="'Assigned to: ' + ticket.agent.name">
                                    {{ ticket.agent.name.charAt(0) }}
                                </div>
                                <div class="w-8 h-8 rounded-full bg-gray-50 border-2 border-white flex items-center justify-center text-[10px] font-black text-gray-400 uppercase">
                                    {{ ticket.customer?.name?.charAt(0) || 'U' }}
                                </div>
                            </div>
                            <div class="text-[10px] font-bold text-gray-300 uppercase tracking-widest group-hover:text-agri-forest transition-colors">
                                Click to View →
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pagination placeholder -->
                <div v-if="pagination.last_page > 1" class="flex justify-center mt-12 space-x-2">
                    <button v-for="page in pagination.last_page" :key="page" 
                        @click="fetchTickets(page)"
                        :class="[pagination.current_page === page ? 'bg-agri-forest text-white' : 'bg-white text-gray-500 border-gray-100']"
                        class="w-10 h-10 rounded-xl border font-bold text-xs transition-all hover:border-agri-forest">
                        {{ page }}
                    </button>
                </div>
            </div>

            <div v-else class="text-center py-20 bg-white rounded-[40px] border border-dashed border-gray-200">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                </div>
                <h3 class="text-lg font-black text-gray-900 mb-2">No matching requests found</h3>
                <p class="text-sm font-medium text-gray-500 mb-8 max-w-sm mx-auto">Try adjusting your filters or search keywords, or create a brand new ticket.</p>
                <button @click="showNewTicketModal = true" class="px-8 py-3 bg-agri-forest text-white text-xs font-black uppercase tracking-widest rounded-xl shadow-lg shadow-agri-forest/20 hover:scale-[1.05] transition-all">
                    Open Ticket
                </button>
            </div>
        </div>
        
        <!-- Reusing New Ticket Modal component or logic would be best, here we simulate placeholder -->
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const tickets = ref([])
const loading = ref(true)
const filterStatus = ref('all')
const searchQuery = ref('')
const pagination = ref({ current_page: 1, last_page: 1 })

const fetchTickets = async (page = 1) => {
    loading.value = true
    try {
        const response = await axios.get(`/api/support/tickets?page=${page}`)
        tickets.value = response.data.data
        pagination.value = {
            current_page: response.data.current_page,
            last_page: response.data.last_page
        }
    } catch (error) {
        console.error('Failed to fetch tickets:', error)
    } finally {
        loading.value = false
    }
}

const filteredTickets = computed(() => {
    return tickets.value.filter(ticket => {
        const matchesStatus = filterStatus.value === 'all' || ticket.status === filterStatus.value
        const matchesSearch = !searchQuery.value || 
            ticket.subject.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            ticket.ticket_number.toLowerCase().includes(searchQuery.value.toLowerCase())
        return matchesStatus && matchesSearch
    })
})

const getStatusBadgeClass = (status) => {
    switch (status) {
        case 'open': return 'bg-blue-50 text-blue-600 border border-blue-100'
        case 'pending': return 'bg-orange-50 text-orange-600 border border-orange-100'
        case 'resolved': return 'bg-green-50 text-green-600 border border-green-100'
        case 'closed': return 'bg-gray-50 text-gray-500 border border-gray-100'
        default: return 'bg-gray-50 text-gray-400'
    }
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

onMounted(() => fetchTickets())
</script>

<style scoped>
.bg-agri-forest { background-color: #155724; }
.text-agri-forest { color: #155724; }
.border-agri-forest { border-color: #155724; }
.bg-agri-sand { background-color: #f8f9fa; }
</style>
