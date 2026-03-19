<template>
    <div class="min-h-screen bg-gray-50 font-sans">
        <!-- Enterprise Hero Section -->
        <div class="bg-[#155724] relative overflow-hidden pb-32">
            <!-- Abstract Texture Background -->
            <div class="absolute inset-0 opacity-10 pointer-events-none">
                <svg class="w-full h-full" fill="none" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <path d="M0 100 C 20 0 50 0 100 100 Z" fill="white" />
                </svg>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-12 relative z-10">
                <div class="text-center max-w-3xl mx-auto">
                    <h1 class="text-3xl font-bold text-white mb-2 tracking-tight">Welcome to Support</h1>
                    <p class="text-[#8FB397] text-lg mb-8 font-medium">Search for answers or track your requests.</p>

                    <!-- Mega Search Bar -->
                    <div class="relative group max-w-2xl mx-auto">
                        <input v-model="kbQuery" type="text" @keyup.enter="performSearch"
                            placeholder="Describe your issue..."
                            class="w-full pl-14 pr-6 py-4 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl text-white placeholder-gray-300 shadow-2xl focus:bg-white focus:text-gray-900 focus:placeholder-gray-400 focus:ring-4 focus:ring-[#155724]/30 transition-all duration-300 font-medium">
                        <svg class="w-6 h-6 absolute left-5 top-4.5 text-white/60 group-focus-within:text-[#155724] transition-colors duration-300"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>

                    <div class="mt-6 flex flex-wrap justify-center gap-2">
                        <span
                            class="text-xs font-medium text-white/40 uppercase tracking-widest mr-2 py-1">Popular:</span>
                        <button v-for="term in ['Reset Password', 'Billing', 'API Keys']" :key="term"
                            @click="kbQuery = term; performSearch()"
                            class="px-3 py-1 bg-white/5 hover:bg-white/10 border border-white/10 rounded-full text-xs font-medium text-white transition-colors">
                            {{ term }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24 relative z-20 pb-20">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                <!-- Left Column: Key Activities & Tickets (8 cols) -->
                <div class="lg:col-span-8 space-y-8">

                    <!-- Quick Actions Row -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <button @click="showNewTicketModal = true"
                            class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-[#155724]/20 transition-all group text-left">
                            <div
                                class="w-10 h-10 rounded-xl bg-[#155724]/5 text-[#155724] flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>
                            <span class="block text-sm font-bold text-gray-900">New Ticket</span>
                            <span class="block text-[10px] text-gray-400 font-medium mt-0.5">Submit request</span>
                        </button>

                        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                            <div
                                class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <span class="block text-sm font-bold text-gray-900">{{myTickets.filter(t => t.status ===
                                'pending').length}} Pending</span>
                            <span class="block text-[10px] text-gray-400 font-medium mt-0.5">Awaiting action</span>
                        </div>

                        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                            <div
                                class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                    </path>
                                </svg>
                            </div>
                            <span class="block text-sm font-bold text-gray-900">{{ myTickets.length }} Total</span>
                            <span class="block text-[10px] text-gray-400 font-medium mt-0.5">All tickets</span>
                        </div>

                        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                            <div
                                class="w-10 h-10 rounded-xl bg-gray-50 text-gray-500 flex items-center justify-center mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="block text-sm font-bold text-gray-900">{{myTickets.filter(t => t.status ===
                                'closed').length }} Closed</span>
                            <span class="block text-[10px] text-gray-400 font-medium mt-0.5">Completed</span>
                        </div>
                    </div>

                    <!-- Recent Tickets Timeline -->
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-lg font-bold text-gray-900 tracking-tight">Recent Activity</h2>
                            <router-link to="/support/tickets"
                                class="text-xs font-bold text-[#155724] hover:underline">View All History</router-link>
                        </div>

                        <div v-if="myTickets.length > 0" class="space-y-4">
                            <div v-for="ticket in myTickets.slice(0, 5)" :key="ticket.id"
                                class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden group">
                                <div class="flex items-start justify-between relative z-10">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <span :class="getStatusBadgeClass(ticket.status)"
                                                class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-widest shadow-sm">
                                                {{ ticket.status }}
                                            </span>
                                            <span class="text-xs text-gray-400 font-mono">#{{ ticket.ticket_number
                                            }}</span>
                                        </div>
                                        <h3
                                            class="text-base font-bold text-gray-900 group-hover:text-[#155724] transition-colors mb-1">
                                            <router-link :to="`/support/tickets/${ticket.id}`">{{ ticket.subject
                                            }}</router-link>
                                        </h3>
                                        <p class="text-xs text-gray-500 mb-4">Last updated {{
                                            formatTime(ticket.updated_at) }}</p>

                                        <!-- Visual Progress Bar -->
                                        <div
                                            class="w-full max-w-md bg-gray-100 h-1.5 rounded-full overflow-hidden flex">
                                            <div class="bg-[#155724] h-full rounded-full"
                                                :style="{ width: getProgressWidth(ticket.status) }"></div>
                                        </div>
                                        <div class="flex justify-between w-full max-w-md mt-1.5">
                                            <span :class="ticket.status === 'open' ? 'text-[#155724]' : 'text-gray-300'"
                                                class="text-[9px] font-bold uppercase tracking-wider">Submitted</span>
                                            <span
                                                :class="ticket.status === 'pending' ? 'text-orange-500' : 'text-gray-300'"
                                                class="text-[9px] font-bold uppercase tracking-wider">Processing</span>
                                            <span
                                                :class="ticket.status === 'resolved' ? 'text-[#155724]' : 'text-gray-300'"
                                                class="text-[9px] font-bold uppercase tracking-wider">Resolved</span>
                                        </div>
                                    </div>

                                    <div class="flex-shrink-0 ml-4">
                                        <router-link :to="`/support/tickets/${ticket.id}`"
                                            class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center text-gray-400 group-hover:text-[#155724] group-hover:border-[#155724] transition-colors bg-white">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </router-link>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-12 bg-white rounded-3xl border border-dashed border-gray-200">
                            <div
                                class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-sm font-bold text-gray-900 mb-1">No tickets yet</h3>
                            <p class="text-xs text-gray-500 mb-4">Create your first ticket to track your requests.</p>
                            <button @click="showNewTicketModal = true"
                                class="px-4 py-2 bg-[#155724] text-white text-xs font-bold uppercase tracking-widest rounded-lg hover:bg-[#155724]/90">Open
                                Ticket</button>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Knowledge Base & Help (4 cols) -->
                <div class="lg:col-span-4 space-y-8">

                    <!-- Premium Bento Grid Categories -->
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-agri-forest/5 rounded-full -mr-16 -mt-16 blur-2xl"></div>
                        <div class="flex items-center justify-between mb-8 relative z-10">
                            <h2 class="text-xl font-black text-gray-900 tracking-tight">Browse Help</h2>
                        </div>
                        <div class="grid grid-cols-2 gap-4 relative z-10">
                            <div v-for="category in kbCategories" :key="category.id"
                                @click="$router.push(`/support/kb/categories/${category.id}`)"
                                class="p-6 rounded-2xl border border-gray-100 bg-gray-50/30 hover:bg-white hover:border-agri-forest/30 hover:shadow-xl hover:-translate-y-1 transition-all cursor-pointer group text-center">
                                <div
                                    class="w-12 h-12 mx-auto rounded-xl bg-white shadow-sm flex items-center justify-center mb-4 text-agri-forest group-hover:scale-110 group-hover:bg-agri-forest group-hover:text-white transition-all duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            :d="getIconPath(category.icon || 'default')"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xs font-black text-gray-900 mb-1 leading-tight">{{ category.name }}</h3>
                                <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest opacity-60">{{
                                    category.articles_count }} Articles</p>
                            </div>
                        </div>
                    </div>

                    <!-- Top Articles Refined -->
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Trending Knowledge</h3>
                            <div class="h-px flex-1 bg-gray-100 ml-4"></div>
                        </div>
                        <div class="space-y-6">
                            <div v-for="article in featuredArticles" :key="article.id"
                                @click="$router.push(`/support/kb/articles/${article.id}`)"
                                class="group cursor-pointer">
                                <h4
                                    class="text-sm font-black text-gray-900 group-hover:text-agri-forest transition-colors line-clamp-1 mb-1.5">
                                    {{ article.title }}</h4>
                                <div class="flex items-center text-[10px] font-bold text-gray-400">
                                    <span class="text-agri-forest/60">{{ article.category?.name }}</span>
                                    <span class="mx-2 opacity-30">•</span>
                                    <span>{{ formatTime(article.updated_at) }}</span>
                                </div>
                            </div>
                        </div>
                        <router-link to="/support/kb"
                            class="w-full mt-8 py-3.5 flex items-center justify-center border-2 border-agri-forest/10 text-[10px] font-black uppercase tracking-widest text-agri-forest rounded-xl hover:bg-agri-forest hover:text-white hover:border-agri-forest transition-all duration-300">
                            Consult Full Library
                        </router-link>
                    </div>

                    <!-- Help CTA -->
                    <div class="bg-[#155724] rounded-3xl p-6 text-white text-center relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10">
                            <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold mb-2 relative z-10">Still need help?</h3>
                        <p class="text-[#8FB397] text-xs mb-6 relative z-10 leading-relaxed">Our support team is
                            available 24/7 to assist you with any issues.</p>
                        <button @click="startLiveChat"
                            class="w-full py-3 bg-white text-[#155724] text-xs font-black uppercase tracking-widest rounded-xl hover:bg-gray-50 transition-colors relative z-10">
                            Start Live Chat
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <!-- New Ticket Modal (Preserved Logic) -->
        <transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0 translate-y-4"
            enter-to-class="opacity-100 translate-y-0" leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-4">
            <div v-if="showNewTicketModal" class="fixed inset-0 z-[100] flex items-center justify-center px-4">
                <div class="absolute inset-0 bg-[#155724]/80 backdrop-blur-sm transition-opacity"
                    @click="showNewTicketModal = false"></div>
                <div
                    class="bg-white w-full max-w-xl rounded-3xl shadow-2xl relative z-10 overflow-hidden transform transition-all">
                    <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                        <h3 class="text-lg font-bold text-gray-900 tracking-tight">Open New Ticket</h3>
                        <button @click="showNewTicketModal = false"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="p-8 space-y-6">
                        <div>
                            <label
                                class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Subject</label>
                            <input v-model="newTicket.subject" type="text"
                                class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#155724] focus:bg-white transition-all outline-none"
                                placeholder="Briefly describe the issue">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Category</label>
                                <select v-model="newTicket.category_id"
                                    class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#155724] focus:bg-white transition-all outline-none">
                                    <option v-for="cat in kbCategories" :key="cat.id" :value="cat.id">{{ cat.name }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Priority</label>
                                <select v-model="newTicket.priority"
                                    class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#155724] focus:bg-white transition-all outline-none">
                                    <option value="low">Low</option>
                                    <option value="normal">Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label
                                class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Description</label>
                            <textarea v-model="newTicket.description" rows="5"
                                class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#155724] focus:bg-white transition-all resize-none outline-none"
                                placeholder="Provide detailed information..."></textarea>
                        </div>
                        <button @click="createTicket" :disabled="!isFormValid"
                            class="w-full py-4 bg-[#155724] text-white text-sm font-black uppercase tracking-widest rounded-xl shadow-lg shadow-[#155724]/20 hover:shadow-xl hover:scale-[1.01] active:scale-[0.99] transition-all disabled:opacity-50 disabled:pointer-events-none">
                            Submit Request
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const kbQuery = ref('')
const kbCategories = ref([])
const featuredArticles = ref([])
const myTickets = ref([])
const showNewTicketModal = ref(false)
const newTicket = ref({ subject: '', category_id: '', priority: 'normal', description: '' })
const isLoading = ref(true)

// Icon Mapping for consistent rendering without external components
const iconPaths = {
    'book-open': 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
    'users': 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
    'credit-card': 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
    'cpu': 'M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z',
    'box': 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
    'settings': 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
    'default': 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z'
}

const getIconPath = (iconName) => {
    return iconPaths[iconName] || iconPaths['default']
}

const isFormValid = computed(() => {
    return newTicket.value.subject && newTicket.value.category_id && newTicket.value.description
})

const fetchSupportData = async () => {
    isLoading.value = true
    try {
        const [catResp, ticketResp, articleResp] = await Promise.all([
            axios.get('/api/support/kb/categories'),
            axios.get('/api/support/tickets'),
            axios.get('/api/support/kb/articles?is_featured=1')
        ])
        kbCategories.value = catResp.data
        // Handle pagination structure if present, otherwise assume array
        myTickets.value = ticketResp.data.data || ticketResp.data

        featuredArticles.value = articleResp.data.data || articleResp.data

        // If no featured articles, fetch latest 4 articles
        if (featuredArticles.value.length === 0) {
            const latestResp = await axios.get('/api/support/kb/articles?limit=4')
            featuredArticles.value = latestResp.data.data || latestResp.data
        }
    } catch (error) {
        console.error('Failed to fetch support data:', error)
    } finally {
        isLoading.value = false
    }
}

const performSearch = async () => {
    if (!kbQuery.value.trim()) return
    // Navigate to a search view or just filter articles (here we'll simulate a search results view using the category view logic or similar)
    // For now, let's just alert or redirect to articles with a search param
    router.push({ path: '/support', query: { q: kbQuery.value } })
    // If we had a search page, we'd go there. For now, let's at least fetch search results if we stay.
    try {
        const res = await axios.get(`/api/support/kb/articles?search=${kbQuery.value}`)
        featuredArticles.value = res.data.data || res.data
    } catch (e) { }
}

const createTicket = async () => {
    try {
        const response = await axios.post('/api/support/tickets', newTicket.value)
        myTickets.value.unshift(response.data)
        showNewTicketModal.value = false
        newTicket.value = { subject: '', category_id: '', priority: 'medium', description: '' }
    } catch (error) {
        console.error('Failed to create ticket:', error)
    }
}

const startLiveChat = () => {
    // Navigate to AgriPulse Messages for "Live Chat" experience
    router.push('/messages')
}

const getStatusBadgeClass = (status) => {
    switch (status) {
        case 'open': return 'bg-blue-100 text-blue-700'
        case 'pending': return 'bg-orange-100 text-orange-700'
        case 'resolved': return 'bg-green-100 text-green-700'
        case 'closed': return 'bg-gray-100 text-gray-600'
        default: return 'bg-gray-100 text-gray-500'
    }
}

const getProgressWidth = (status) => {
    switch (status) {
        case 'open': return '30%'
        case 'pending': return '60%'
        case 'resolved': return '100%'
        case 'closed': return '100%'
        default: return '0%'
    }
}

const formatTime = (date) => {
    if (!date) return 'Just now'
    const d = new Date(date)
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

onMounted(fetchSupportData)
</script>

<style scoped>
.line-clamp-1 {
    display: -webkit-box;
    line-clamp: 1;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    line-clamp: 2;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
