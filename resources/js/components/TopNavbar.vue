<template>
  <nav class="bg-white/90 backdrop-blur-md border-b border-gray-100/50 sticky top-0 shrink-0 z-50 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between">
      <!-- Left side - Search bar -->
      <div class="flex-1 max-w-2xl relative">
        <div class="relative group">
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
            <!-- Loading Spinner -->
            <div v-if="isSearching">
              <svg class="animate-spin h-4 w-4 text-agri-forest" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </div>

            <!-- Clear Button -->
            <button 
              v-if="searchQuery" 
              @click="clearSearch"
              type="button"
              class="text-gray-400 hover:text-agri-forest p-1 rounded-full hover:bg-agri-forest/5 transition-all"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Search Results Overlay -->
        <transition
          enter-active-class="transition duration-200 ease-out"
          enter-from-class="transform scale-95 opacity-0 -translate-y-2"
          enter-to-class="transform scale-100 opacity-100 translate-y-0"
          leave-active-class="transition duration-150 ease-in"
          leave-from-class="transform scale-100 opacity-100 translate-y-0"
          leave-to-class="transform scale-95 opacity-0 -translate-y-2"
        >
          <div
            v-if="showResults && searchQuery.trim()"
            v-click-outside="() => showResults = false"
            class="absolute top-full left-0 right-0 mt-3 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden z-[60]"
          >
            <!-- Loading State Placeholder (Subtle) -->
            <div v-if="isSearching && searchResults.products.length === 0" class="p-8 text-center bg-white">
                <div class="animate-pulse flex flex-col items-center">
                    <div class="w-12 h-12 bg-gray-50 rounded-full mb-4"></div>
                    <div class="h-2 w-32 bg-gray-50 rounded mb-2"></div>
                    <div class="h-2 w-24 bg-gray-50 rounded"></div>
                </div>
            </div>

            <!-- Products Section -->
            <div v-else-if="searchResults.products.length > 0">
              <div class="px-5 py-3 bg-gray-50/50 border-b border-gray-100 flex items-center justify-between">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Global Listings</span>
                <span class="text-[10px] font-bold text-agri-forest">{{ searchResults.products.length }} Matches</span>
              </div>
              <div class="divide-y divide-gray-50">
                <router-link
                  v-for="product in searchResults.products"
                  :key="product.id"
                  :to="`/products/${product.id}`"
                  @click="showResults = false"
                  class="flex items-center px-5 py-4 hover:bg-agri-sand/10 transition-colors group"
                >
                  <div class="w-10 h-10 rounded-xl bg-agri-sand/30 flex items-center justify-center mr-4 group-hover:bg-agri-forest group-hover:text-white transition-all text-agri-forest">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-900 truncate">{{ product.name }}</p>
                    <p class="text-[10px] text-gray-500 font-medium">{{ product.category?.name || 'Unclassified' }}</p>
                  </div>
                  <div class="text-right ml-4">
                    <p class="text-xs font-black text-agri-forest">${{ product.price }}</p>
                  </div>
                </router-link>
              </div>

              <!-- Footer -->
              <div class="bg-gray-50/80 px-5 py-3 border-t border-gray-100">
                <router-link to="/products" class="text-[10px] font-black text-agri-forest hover:text-agri-lush uppercase tracking-widest block text-center">
                  Explore All Assets
                </router-link>
              </div>
            </div>

            <!-- Empty State -->
            <div v-else-if="!isSearching" class="p-10 text-center bg-white">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <h4 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-1">Zero Assets Found</h4>
                <p class="text-xs font-medium text-gray-400">No results found for "<span class="text-agri-forest font-bold">{{ searchQuery }}</span>"</p>
                <button @click="searchQuery = ''" class="mt-4 text-[10px] font-black text-gray-400 hover:text-agri-forest uppercase tracking-[0.2em] transition-colors">
                    Clear Discovery Filter
                </button>
            </div>
          </div>
        </transition>
      </div>

      <!-- Right side - Action icons -->
      <div class="flex items-center space-x-2 ml-8">
        <!-- Messages Dropdown -->
        <MessagesDropdown
          :unread-message-count="unreadMessageCount"
          :messages="messages"
          @close="closeMessagesDropdown"
        />

        <!-- Notifications Dropdown -->
        <NotificationsDropdown
          :unread-notification-count="unreadNotificationCount"
          :notifications="notifications"
          @close="closeNotificationsDropdown"
          @refresh="fetchNotifications"
        />

        <div class="h-6 w-px bg-gray-100 mx-2"></div>

        <!-- Settings -->
        <button
          @click="openSettings"
          class="p-2.5 text-gray-400 hover:text-agri-forest hover:bg-agri-forest/5 transition-all duration-300 rounded-xl"
        >
          <span class="sr-only">Settings</span>
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
        </button>

        <!-- Quick Actions Dropdown -->
        <div class="relative">
          <button
            @click="toggleQuickActions"
            class="flex items-center space-x-2 px-5 py-2.5 bg-agri-forest text-white rounded-xl hover:bg-agri-lush shadow-lg shadow-agri-forest/20 transition-all duration-300 group"
          >
            <svg class="h-4 w-4 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span class="text-xs font-black uppercase tracking-widest">Global Action</span>
          </button>

          <!-- Quick Actions Dropdown Menu -->
          <transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0 translate-y-2"
            enter-to-class="transform scale-100 opacity-100 translate-y-0"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="transform scale-100 opacity-100 translate-y-0"
            leave-to-class="transform scale-95 opacity-0 translate-y-2"
          >
            <div
              v-if="showQuickActions"
              class="absolute right-0 mt-3 w-64 bg-white rounded-2xl shadow-2xl ring-1 ring-black ring-opacity-5 z-50 py-3 border border-gray-100"
              @click.stop
            >
              <div class="px-5 py-2 mb-2">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] leading-none">Marketplace Nexus</p>
              </div>
              <router-link
                to="/vendor/products/create"
                @click="closeQuickActions"
                class="flex items-center px-5 py-3.5 text-sm text-gray-700 hover:bg-agri-sand/20 hover:text-agri-forest transition-colors group"
              >
                <div class="w-8 h-8 rounded-lg bg-agri-forest/5 flex items-center justify-center mr-3 text-agri-forest group-hover:bg-agri-forest group-hover:text-white transition-all">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                   </svg>
                </div>
                <span class="font-bold tracking-tight">Broadcast Listing</span>
              </router-link>
              <router-link
                to="/rfqs/create"
                @click="closeQuickActions"
                class="flex items-center px-5 py-3.5 text-sm text-gray-700 hover:bg-agri-sand/20 hover:text-agri-forest transition-colors group"
              >
                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center mr-3 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <span class="font-bold tracking-tight">Submit RFQ Request</span>
              </router-link>
            </div>
          </transition>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup lang="js">
import { ref, onMounted, onUnmounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import axios from 'axios'
import MessagesDropdown from './MessagesDropdown.vue'
import NotificationsDropdown from './NotificationsDropdown.vue'

const router = useRouter()
const authStore = useAuthStore()

const searchQuery = ref('')
const isSearching = ref(false)
const searchResults = ref({ products: [] })
const showResults = ref(false)

const showQuickActions = ref(false)
const unreadMessageCount = ref(0)
const unreadNotificationCount = ref(0)
const messages = ref([])
const notifications = ref([])
let pollingInterval = null
let debounceTimer = null

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
  showResults.value = true // Show results immediately (with current results or empty state)
  
  const currentQuery = searchQuery.value.trim()
  
  debounceTimer = setTimeout(async () => {
    // Prevent stale results if input was cleared during request
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
      // Small delay for perceived performance if results come back too fast
      isSearching.value = false
    }
  }, 400)
}

// Click outside directive (simple implementation)
const vClickOutside = {
  mounted(el, binding) {
    el.clickOutsideEvent = (event) => {
      if (!(el === event.target || el.contains(event.target))) {
        binding.value(event)
      }
    }
    document.addEventListener('click', el.clickOutsideEvent)
  },
  unmounted(el) {
    document.removeEventListener('click', el.clickOutsideEvent)
  }
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
</style>
