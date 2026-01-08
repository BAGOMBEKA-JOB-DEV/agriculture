<template>
  <nav class="bg-white border-b border-gray-100 px-8 py-3 shrink-0 relative z-10 transition-all duration-300">
    <div class="max-w-screen-2xl mx-auto flex items-center justify-between">
      <!-- Left side - Search bar -->
      <div class="flex-1 max-w-2xl">
        <div class="relative group">
          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-agri-forest text-gray-400">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </div>
          <input
            type="text"
            placeholder="Search for products, vendors, or buyers..."
            class="block w-full pl-12 pr-4 py-2.5 bg-gray-50/50 border border-gray-100 rounded-2xl leading-5 transition-all duration-200 placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-agri-forest/10 focus:border-agri-forest focus:shadow-sm sm:text-sm"
            v-model="searchQuery"
            @input="handleSearch"
          >
        </div>
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
          class="p-2.5 text-gray-500 hover:text-agri-forest hover:bg-agri-sand/30 transition-all duration-200 rounded-xl"
        >
          <span class="sr-only">Settings</span>
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
        </button>

        <!-- Quick Actions Dropdown -->
        <div class="relative">
          <button
            @click="toggleQuickActions"
            class="flex items-center space-x-2 px-4 py-2 bg-agri-forest text-white rounded-xl hover:bg-agri-lush shadow-sm hover:shadow transition-all duration-200 group"
          >
            <svg class="h-5 w-5 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span class="text-sm font-semibold">Listing Action</span>
          </button>

          <!-- Quick Actions Dropdown Menu -->
          <transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
          >
            <div
              v-if="showQuickActions"
              class="absolute right-0 mt-3 w-64 bg-white rounded-2xl shadow-2xl ring-1 ring-black ring-opacity-5 z-50 py-2 border border-gray-100"
              @click.stop
            >
              <div class="px-4 py-2 mb-1">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">New Marketplace Entry</p>
              </div>
              <router-link
                to="/vendor/products/create"
                @click="closeQuickActions"
                class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-agri-sand/30 hover:text-agri-forest transition-colors"
              >
                <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center mr-3 text-green-600">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                   </svg>
                </div>
                <span class="font-medium">Add Product</span>
              </router-link>
              <router-link
                to="/rfqs/create"
                @click="closeQuickActions"
                class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-agri-sand/30 hover:text-agri-forest transition-colors"
              >
                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center mr-3 text-indigo-600">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <span class="font-medium">Submit RFQ</span>
              </router-link>
            </div>
          </transition>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup lang="js">
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import axios from 'axios'
import MessagesDropdown from './MessagesDropdown.vue'
import NotificationsDropdown from './NotificationsDropdown.vue'

const router = useRouter()
const authStore = useAuthStore()

const searchQuery = ref('')
const showQuickActions = ref(false)
const unreadMessageCount = ref(0)
const unreadNotificationCount = ref(0)
const messages = ref([])
const notifications = ref([])
const hasNewNotifications = ref(false)
let pollingInterval = null

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    console.log('Searching for:', searchQuery.value)
  }
}

const closeMessagesDropdown = () => {}
const closeNotificationsDropdown = () => {}

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
        
        // Calculate unread count (conversations with unread last message from others)
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
  // Poll every 30 seconds
  pollingInterval = setInterval(fetchNotificationCounts, 30000);
})

onUnmounted(() => {
  if (pollingInterval) clearInterval(pollingInterval);
})
</script>
