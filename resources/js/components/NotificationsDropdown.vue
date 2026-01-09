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
      class="fixed inset-x-0 top-[65px] sm:absolute sm:inset-auto sm:right-0 sm:mt-3 w-full sm:w-96 bg-white sm:rounded-2xl shadow-xl ring-1 ring-black ring-opacity-5 z-[70] overflow-hidden transition-all"
      @click.stop
    >
      <!-- Header -->
      <div class="flex items-center justify-between p-5 border-b border-gray-100 bg-white">
        <div>
          <h3 class="text-sm font-semibold text-gray-900 tracking-tight">System Alerts</h3>
          <p class="text-[10px] font-medium text-gray-400 mt-0.5">Real-time marketplace intelligence</p>
        </div>
        <button
          @click="closeDropdown"
          class="p-2 text-gray-400 hover:text-agri-forest hover:bg-agri-sand/10 rounded-xl transition-all"
        >
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Notifications List -->
      <div class="max-h-[32rem] overflow-y-auto divide-y divide-gray-50">
        <div
          v-for="notification in notifications"
          :key="notification.id"
          class="flex items-start space-x-4 p-4 hover:bg-gray-50/80 transition-colors cursor-pointer group relative"
          :class="{ 'bg-agri-sand/5': !notification.read_at }"
          @click="markAsRead(notification)"
        >
          <div class="flex-shrink-0">
            <div class="h-10 w-10 rounded-xl flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform" :class="getIconBg(notification.data.priority)">
              <component :is="getIcon(notification.data.priority)" class="h-5 w-5 text-white" />
            </div>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
              <p class="text-xs font-semibold text-gray-900 tracking-tight">{{ notification.data.subject }}</p>
              <p class="text-[10px] font-medium text-gray-400 ml-2 whitespace-nowrap">{{ formatTime(notification.created_at) }}</p>
            </div>
            <p class="text-xs text-gray-500 mt-1 leading-relaxed line-clamp-2">{{ notification.data.message }}</p>
          </div>
          <div v-if="!notification.read_at" class="absolute left-0 top-0 bottom-0 w-1 bg-agri-forest"></div>
        </div>

        <!-- Empty State -->
        <div v-if="notifications.length === 0" class="text-center py-12 px-6">
            <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM15 7v5H9v6H5V7h10z"></path>
                </svg>
            </div>
            <h3 class="text-sm font-semibold text-gray-900">All caught up</h3>
            <p class="mt-1 text-xs text-gray-500 leading-relaxed">No new alerts found. Your nexus is fully synchronized.</p>
        </div>
      </div>

      <!-- Footer -->
      <div class="bg-gray-50/50 p-4 border-t border-gray-100 flex items-center justify-between">
        <button @click="markAllAsRead" class="text-[10px] font-semibold text-gray-500 hover:text-gray-900 transition-colors">
          Mark all as read
        </button>
        <router-link to="/notifications" @click="closeDropdown" class="text-[10px] font-semibold text-agri-forest hover:text-agri-lush transition-colors">
          Audit trail
        </router-link>
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

// Click outside handling
const handleClickOutside = (event) => {
  if (isOpen.value && !event.target.closest('.relative')) {
    closeDropdown()
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
