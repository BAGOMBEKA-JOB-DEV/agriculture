<template>
  <div class="relative">
    <button
      @click="toggleDropdown"
      class="relative p-2 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-md"
      :class="{ 'text-indigo-600': hasNewMessages }"
    >
      <span class="sr-only">Messages</span>
      <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
      </svg>
      <span
        v-if="unreadMessageCount > 0"
        class="absolute -top-1 -right-1 h-5 w-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center"
      >
        {{ unreadMessageCount > 9 ? '9+' : unreadMessageCount }}
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
        <h3 class="text-sm font-semibold text-gray-900">Messages</h3>
        <button
          @click="closeDropdown"
          class="text-gray-400 hover:text-gray-600 transition-colors"
        >
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Messages List -->
      <div class="max-h-96 overflow-y-auto">
        <div
          v-for="message in messages"
          :key="message.id"
          class="flex items-start space-x-3 p-3 hover:bg-gray-50 transition-colors cursor-pointer border-b border-gray-100 last:border-b-0"
          :class="{ 'bg-blue-50 border-l-4 border-blue-500': !message.read }"
        >
          <div class="flex-shrink-0">
            <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
              <span class="text-xs font-medium text-gray-700">{{ message.sender.charAt(0).toUpperCase() }}</span>
            </div>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
              <p class="text-xs font-medium text-gray-900 truncate">{{ message.sender }}</p>
              <p class="text-xs text-gray-500">{{ message.time }}</p>
            </div>
            <p class="text-xs text-gray-600 truncate">{{ message.subject }}</p>
            <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ message.preview }}</p>
          </div>
          <div v-if="!message.read" class="flex-shrink-0">
            <div class="h-1.5 w-1.5 bg-blue-500 rounded-full"></div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="messages.length === 0" class="text-center py-6">
          <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
          </svg>
          <h3 class="mt-2 text-xs font-medium text-gray-900">No messages</h3>
          <p class="mt-1 text-xs text-gray-500">You don't have any messages yet.</p>
        </div>
      </div>

      <!-- Footer -->
      <div class="border-t border-gray-200 p-3">
        <button class="w-full text-center text-xs text-indigo-600 hover:text-indigo-500 font-medium">
          View All Messages
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="js">
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  unreadMessageCount: {
    type: Number,
    default: 0
  },
  hasNewMessages: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close'])

const isOpen = ref(false)
const messages = ref([])
const isMobile = ref(false)

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
}

const closeDropdown = () => {
  isOpen.value = false
  emit('close')
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
  loadMessages()
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  window.removeEventListener('resize', checkMobile)
})

// Dummy data
const dummyMessages = [
  {
    id: 1,
    sender: 'John Doe',
    subject: 'Project Update',
    preview: 'The project deadline has been extended to next week. Please review the changes.',
    time: '2m ago',
    read: false
  },
  {
    id: 2,
    sender: 'Sarah Wilson',
    subject: 'Task Assignment',
    preview: 'You have been assigned to work on the new feature implementation.',
    time: '1h ago',
    read: false
  },
  {
    id: 3,
    sender: 'Mike Johnson',
    subject: 'Meeting Reminder',
    preview: 'Don\'t forget about the team meeting scheduled for tomorrow at 10 AM.',
    time: '3h ago',
    read: true
  },
  {
    id: 4,
    sender: 'Emily Davis',
    subject: 'Code Review',
    preview: 'Please review the pull request I submitted for the authentication module.',
    time: '1d ago',
    read: true
  },
  {
    id: 5,
    sender: 'Alex Chen',
    subject: 'Bug Report',
    preview: 'Found a critical bug in the user registration flow. Needs immediate attention.',
    time: '2d ago',
    read: true
  }
]

const loadMessages = () => {
  messages.value = dummyMessages
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
