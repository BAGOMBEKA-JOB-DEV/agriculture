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
          :class="{ 'bg-blue-50 border-l-4 border-blue-500': !notification.read }"
        >
          <div class="flex-shrink-0">
            <div class="h-8 w-8 rounded-full flex items-center justify-center" :class="getIconBg(notification.type)">
              <component :is="getIcon(notification.type)" class="h-4 w-4 text-white" />
            </div>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
              <p class="text-xs font-medium text-gray-900">{{ notification.title }}</p>
              <p class="text-xs text-gray-500">{{ notification.time }}</p>
            </div>
            <p class="text-xs text-gray-600 mt-1">{{ notification.message }}</p>
            <div v-if="notification.action" class="mt-2">
              <button class="text-xs text-indigo-600 hover:text-indigo-500 font-medium">
                {{ notification.action }}
              </button>
            </div>
          </div>
          <div v-if="!notification.read" class="flex-shrink-0">
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
          <button class="text-xs text-gray-600 hover:text-gray-800 font-medium">
            Mark all as read
          </button>
          <button class="text-xs text-indigo-600 hover:text-indigo-500 font-medium">
            View All Notifications
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="js">
import { ref, onMounted, onUnmounted } from 'vue'

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

const UserIcon = {
  template: '<svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" /></svg>'
}

const props = defineProps({
  unreadNotificationCount: {
    type: Number,
    default: 0
  },
  hasNewNotifications: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close'])

const isOpen = ref(false)
const notifications = ref([])
const isMobile = ref(false)

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
}

const closeDropdown = () => {
  isOpen.value = false
  emit('close')
}

const getIcon = (type) => {
  switch (type) {
    case 'success':
      return CheckCircleIcon
    case 'error':
      return ExclamationCircleIcon
    case 'warning':
      return ExclamationCircleIcon
    case 'info':
    default:
      return InformationCircleIcon
  }
}

const getIconBg = (type) => {
  switch (type) {
    case 'success':
      return 'bg-green-500'
    case 'error':
      return 'bg-red-500'
    case 'warning':
      return 'bg-yellow-500'
    case 'info':
    default:
      return 'bg-blue-500'
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
  loadNotifications()
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  window.removeEventListener('resize', checkMobile)
})

// Dummy data
const dummyNotifications = [
  {
    id: 1,
    type: 'success',
    title: 'Task Completed',
    message: 'Your task "Implement user authentication" has been completed successfully.',
    time: '5m ago',
    read: false,
    action: 'View Task'
  },
  {
    id: 2,
    type: 'info',
    title: 'New Project Assigned',
    message: 'You have been assigned to the "Mobile App Development" project.',
    time: '1h ago',
    read: false,
    action: 'View Project'
  },
  {
    id: 3,
    type: 'warning',
    title: 'Deadline Approaching',
    message: 'The "Website Redesign" project deadline is approaching in 2 days.',
    time: '2h ago',
    read: true,
    action: 'View Details'
  },
  {
    id: 4,
    type: 'error',
    title: 'Build Failed',
    message: 'The latest deployment failed due to a syntax error in the main component.',
    time: '3h ago',
    read: true,
    action: 'View Logs'
  },
  {
    id: 5,
    type: 'info',
    title: 'Team Member Added',
    message: 'Sarah Johnson has been added to your team.',
    time: '1d ago',
    read: true,
    action: 'View Profile'
  }
]

const loadNotifications = () => {
  notifications.value = dummyNotifications
}
</script>
