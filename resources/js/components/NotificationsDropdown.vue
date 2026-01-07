<template>
  <div class="relative">
    <button
      @click="toggleDropdown"
      class="relative p-2 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-md"
      :class="{ 'text-indigo-600': hasNewNotifications }"
    >
      <span class="sr-only">Notifications</span>
      <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM15 7v5H9v6H5V7h10z"></path>
      </svg>
      <span
        v-if="unreadNotificationCount > 0"
        class="absolute -top-1 -right-1 h-5 w-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center"
      >
        {{ unreadNotificationCount > 9 ? '9+' : unreadNotificationCount }}
      </span>
    </button>

    <!-- Dropdown Menu -->
    <div
      v-if="isOpen"
      class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50"
      :class="{ 'left-0 right-auto': isMobile }"
      @click.stop
    >
      <!-- Header -->
      <div class="flex items-center justify-between p-4 border-b border-gray-200">
        <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
        <button
          @click="closeDropdown"
          class="text-gray-400 hover:text-gray-600 transition-colors"
        >
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Notifications List -->
      <div class="max-h-96 overflow-y-auto">
        <div
          v-for="notification in notifications"
          :key="notification.id"
          class="flex items-start space-x-3 p-3 hover:bg-gray-50 transition-colors cursor-pointer border-b border-gray-100 last:border-b-0"
          :class="{ 'bg-blue-50 border-l-4 border-blue-500': !notification.read_at }"
          @click="markAsRead(notification)"
        >
          <div class="flex-shrink-0">
            <div class="h-8 w-8 rounded-full flex items-center justify-center" :class="getIconBg(notification.data.priority)">
              <component :is="getIcon(notification.data.priority)" class="h-4 w-4 text-white" />
            </div>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
              <p class="text-xs font-medium text-gray-900">{{ notification.data.subject }}</p>
              <p class="text-xs text-gray-500">{{ formatTime(notification.created_at) }}</p>
            </div>
            <p class="text-xs text-gray-600 mt-1">{{ notification.data.message }}</p>
          </div>
          <div v-if="!notification.read_at" class="flex-shrink-0">
            <div class="h-1.5 w-1.5 bg-blue-500 rounded-full"></div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="notifications.length === 0" class="text-center py-6">
          <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM15 7v5H9v6H5V7h10z"></path>
          </svg>
          <h3 class="mt-2 text-xs font-medium text-gray-900">No notifications</h3>
          <p class="mt-1 text-xs text-gray-500">You're all caught up!</p>
        </div>
      </div>

      <!-- Footer -->
      <div class="border-t border-gray-200 p-3">
        <div class="flex items-center justify-between">
          <button @click="markAllAsRead" class="text-xs text-gray-600 hover:text-gray-800 font-medium">
            Mark all as read
          </button>
          <router-link 
            to="/notifications" 
            @click="closeDropdown" 
            class="text-xs text-indigo-600 hover:text-indigo-500 font-medium"
          >
            View All Notifications
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="js">
import { ref, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

// Icons
const CheckCircleIcon = {
  template: '<svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>'
}

const ExclamationCircleIcon = {
  template: '<svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>'
}

const InformationCircleIcon = {
  template: '<svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>'
}

const props = defineProps({
  notifications: {
    type: Array,
    default: () => []
  },
  unreadNotificationCount: {
    type: Number,
    default: 0
  }
})

const emit = defineEmits(['close', 'refresh'])

const router = useRouter()

const isOpen = ref(false)
const isMobile = ref(false)

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
}

const closeDropdown = () => {
  isOpen.value = false
  emit('close')
}

const getIcon = (priority) => {
  switch (priority) {
    case 'success':
      return CheckCircleIcon
    case 'critical':
      return ExclamationCircleIcon
    case 'high':
      return ExclamationCircleIcon
    case 'normal':
    default:
      return InformationCircleIcon
  }
}

const getIconBg = (priority) => {
  switch (priority) {
    case 'success':
      return 'bg-green-500'
    case 'critical':
      return 'bg-red-500'
    case 'high':
      return 'bg-orange-500'
    case 'normal':
    default:
      return 'bg-blue-500'
  }
}

const formatTime = (date) => {
    if (!date) return ''
    const d = new Date(date)
    const now = new Date()
    const diff = Math.floor((now - d) / 60000) // minutes
    
    if (diff < 60) return `${diff}m ago`
    if (diff < 1440) return `${Math.floor(diff/60)}h ago`
    return `${Math.floor(diff/1440)}d ago`
}

const markAsRead = async (notification) => {
    if (!notification.read_at) {
        try {
            await axios.post(`/api/notifications/${notification.id}/read`);
            emit('refresh');
        } catch (error) {
            console.error('Error marking as read:', error);
        }
    }
    closeDropdown();
    router.push(`/notifications/${notification.id}`);
}



const markAllAsRead = async () => {
    try {
        await axios.post('/api/notifications/read-all');
        emit('refresh');
    } catch (error) {
        console.error('Error marking all as read:', error);
    }
}

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  const dropdown = event.target.closest('.relative')
  if (!dropdown) {
    closeDropdown()
  }
}

const checkMobile = () => {
  isMobile.value = window.innerWidth < 640 // sm breakpoint
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  window.addEventListener('resize', checkMobile)
  checkMobile()
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  window.removeEventListener('resize', checkMobile)
})
</script>
