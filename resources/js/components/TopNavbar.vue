<template>
  <nav class="ml-0 lg:-ml-5 bg-white/90 backdrop-blur-md border-b border-gray-100/50 sticky top-0 shrink-0 z-50 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
      <div v-if="!showMobileSearch" class="flex items-center justify-between">
        <!-- Left side - Search bar (Desktop) -->
        <div class="hidden md:flex flex-1 max-w-2xl relative">
          <div class="relative group w-full">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-agri-forest text-gray-400">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>
            <input
              type="text"
              placeholder="Search for products..."
              class="block w-full pl-12 pr-16 py-2.5 bg-gray-50/50 border border-gray-100 rounded-2xl leading-5 transition-all duration-200 placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest focus:shadow-sm sm:text-sm font-medium"
              v-model="searchQuery"
              @input="handleSearch"
              @focus="showResults = true"
            >
            
            <div class="absolute inset-y-0 right-4 flex items-center space-x-2">
              <div v-if="isSearching">
                <svg class="animate-spin h-4 w-4 text-agri-forest" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
              </div>

              <button v-if="searchQuery" @click="clearSearch" type="button" class="text-gray-400 hover:text-agri-forest p-1 rounded-full hover:bg-agri-forest/5 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
              </button>
            </div>
          </div>

          <!-- Desktop Search Overlay -->
          <SearchOverlay 
            v-if="showResults && searchQuery.trim()"
            :is-searching="isSearching"
            :search-results="searchResults"
            :search-query="searchQuery"
            @close="showResults = false"
            @clear="clearSearch"
          />
        </div>

        <!-- Mobile Brand/Space -->
        <div class="md:hidden flex items-center">
            <span class="text-agri-forest font-black tracking-[0.1em] text-xl">I<span class="text-gray-900">AM</span></span>
        </div>

        <!-- Right side - Action icons -->
        <div class="flex items-center space-x-1 md:space-x-2 ml-4">
          <!-- Mobile Search Trigger -->
          <button @click="openMobileSearch" class="md:hidden p-2.5 text-gray-400 hover:text-agri-forest transition-all rounded-xl">
             <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
          </button>

          <MessagesDropdown :unread-message-count="unreadMessageCount" :messages="messages" />
          <NotificationsDropdown :unread-notification-count="unreadNotificationCount" :notifications="notifications" @refresh="fetchNotifications" />
          
          <div class="h-6 w-px bg-gray-100 mx-1 md:mx-2"></div>

          <button @click="openSettings" class="p-2.5 text-gray-400 hover:text-agri-forest transition-all rounded-xl">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
          </button>

          <QuickActionsDropdown />
        </div>
      </div>

      <!-- Full-Width Mobile Search -->
      <div v-else class="flex items-center space-x-4 animate-in fade-in slide-in-from-top-2 duration-200">
        <div class="flex-1 relative">
           <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-agri-forest">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
          </div>
          <input
            ref="mobileSearchRef"
            type="text"
            placeholder="Search assets..."
            class="block w-full pl-12 pr-12 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl focus:outline-none focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest font-medium text-sm"
            v-model="searchQuery"
            @input="handleSearch"
          >
          <button v-if="searchQuery" @click="clearSearch" class="absolute inset-y-0 right-4 flex items-center text-gray-400">
             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        <button @click="closeMobileSearch" class="text-xs font-black text-gray-400 uppercase tracking-widest hover:text-agri-forest transition-colors">
            Cancel
        </button>
      </div>
      
      <!-- Mobile Search Overlay (Positioned for mobile) -->
      <div v-if="showMobileSearch && showResults && searchQuery.trim()" class="md:hidden">
         <SearchOverlay 
            :is-searching="isSearching"
            :search-results="searchResults"
            :search-query="searchQuery"
            is-mobile
            @close="closeMobileSearch"
            @clear="clearSearch"
          />
      </div>
    </div>
  </nav>
</template>

<script setup lang="js">
import { ref, onMounted, onUnmounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import axios from 'axios'
import MessagesDropdown from './MessagesDropdown.vue'
import NotificationsDropdown from './NotificationsDropdown.vue'
import SearchOverlay from './Navbar/SearchOverlay.vue'
import QuickActionsDropdown from './Navbar/QuickActionsDropdown.vue'

const router = useRouter()
const authStore = useAuthStore()

const searchQuery = ref('')
const isSearching = ref(false)
const searchResults = ref({ products: [] })
const showResults = ref(false)
const showMobileSearch = ref(false)
const mobileSearchRef = ref(null)

const showQuickActions = ref(false)
const unreadMessageCount = ref(0)
const unreadNotificationCount = ref(0)
const messages = ref([])
const notifications = ref([])
let pollingInterval = null
let debounceTimer = null

const openMobileSearch = () => {
  showMobileSearch.value = true
  nextTick(() => {
    mobileSearchRef.value?.focus()
  })
}

const closeMobileSearch = () => {
  showMobileSearch.value = false
  showResults.value = false
  searchQuery.value = ''
}

const clearSearch = () => {
  searchQuery.value = ''
  isSearching.value = false
  showResults.value = false
  searchResults.value = { products: [] }
}

const handleSearch = () => {
  clearTimeout(debounceTimer)
  
  if (!searchQuery.value.trim()) {
    isSearching.value = false
    showResults.value = false
    searchResults.value = { products: [] }
    return
  }

  isSearching.value = true
  showResults.value = true
  
  const currentQuery = searchQuery.value.trim()
  
  debounceTimer = setTimeout(async () => {
    if (!searchQuery.value.trim()) {
      isSearching.value = false
      return
    }

    try {
      const response = await axios.get(`/api/search?q=${encodeURIComponent(currentQuery)}`)
      searchResults.value = response.data
    } catch (error) {
      console.error('Search failed:', error)
    } finally {
      isSearching.value = false
    }
  }, 400)
}

const openSettings = () => {
  router.push('/settings')
}

const toggleQuickActions = () => {
  showQuickActions.value = !showQuickActions.value
}

const closeQuickActions = () => {
  showQuickActions.value = false
}

const fetchMessages = async () => {
    try {
        const response = await axios.get('/api/messages');
        messages.value = response.data;
        const unreadConvos = messages.value.filter(convo => 
            !convo.last_message.read_at && 
            convo.last_message.sender_id !== authStore.user?.id
        );
        unreadMessageCount.value = unreadConvos.length;
    } catch (error) {
        console.error('Error fetching navbar messages:', error);
    }
}

const fetchNotifications = async () => {
    try {
        const response = await axios.get('/api/notifications');
        notifications.value = response.data;
        unreadNotificationCount.value = notifications.value.filter(n => !n.read_at).length;
    } catch (error) {
        console.error('Error fetching notifications:', error);
    }
}

const fetchNotificationCounts = async () => {
  await Promise.all([
     fetchMessages(),
     fetchNotifications()
  ]);
}

onMounted(() => {
  fetchNotificationCounts();
  pollingInterval = setInterval(fetchNotificationCounts, 30000);
})

onUnmounted(() => {
  if (pollingInterval) clearInterval(pollingInterval);
  clearTimeout(debounceTimer)
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

nav {
  font-family: 'Inter', sans-serif;
}

.animate-in {
    animation-duration: 0.2s;
    animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
    animation-fill-mode: both;
}

@keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slide-in-from-top-2 {
    from { transform: translateY(-0.5rem); }
    to { transform: translateY(0); }
}

.fade-in { animation-name: fade-in; }
.slide-in-from-top-2 { animation-name: slide-in-from-top-2; }
</style>
