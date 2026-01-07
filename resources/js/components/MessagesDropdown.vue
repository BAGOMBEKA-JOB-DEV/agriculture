<template>
  <div class="relative">
    <button
      @click="toggleDropdown"
      class="relative p-2 text-gray-400 hover:text-agri-forest hover:bg-agri-sand/30 transition-all duration-200 rounded-xl"
      :class="{ 'text-agri-forest bg-agri-sand/30': isOpen }"
    >
      <span class="sr-only">Messages</span>
      <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
      </svg>
      <span
        v-if="unreadMessageCount > 0"
        class="absolute top-1 right-1 h-2.5 w-2.5 bg-red-500 rounded-full ring-2 ring-white"
      ></span>
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
          v-for="convo in messages"
          :key="convo.last_message.id"
          @click="openConversation(convo.user.id)"
          class="flex items-start space-x-3 p-3 hover:bg-gray-50 transition-colors cursor-pointer border-b border-gray-100 last:border-b-0 group"
          :class="{ 'bg-agri-sand/10': isUnread(convo) }"
        >
          <div class="flex-shrink-0">
            <div class="h-10 w-10 rounded-xl bg-agri-sand/50 text-agri-forest flex items-center justify-center font-bold">
              {{ convo.user.name.charAt(0) }}
            </div>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
              <p class="text-sm font-bold text-gray-900 truncate group-hover:text-agri-forest transition-colors">{{ convo.user.name }}</p>
              <p class="text-[10px] font-bold text-gray-400">{{ formatTime(convo.last_message.created_at) }}</p>
            </div>
            <p class="text-xs text-gray-500 mt-0.5 line-clamp-1 truncate" :class="{ 'font-semibold text-gray-700': isUnread(convo) }">
                <span v-if="convo.last_message.sender_id === authUser?.id" class="text-xs text-agri-forest font-bold mr-1">You:</span>
                {{ convo.last_message.content || '📎 Attachment' }}
            </p>
          </div>
          <div v-if="isUnread(convo)" class="flex-shrink-0 self-center">
            <div class="h-2 w-2 bg-agri-forest rounded-full"></div>
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
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const props = defineProps({
  messages: {
    type: Array,
    default: () => []
  },
  unreadMessageCount: {
    type: Number,
    default: 0
  }
})

const emit = defineEmits(['close'])

const router = useRouter()
const authStore = useAuthStore()
const isOpen = ref(false)
const isMobile = ref(false)

const authUser = authStore.user

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
}

const closeDropdown = () => {
  isOpen.value = false
  emit('close')
}

const isUnread = (convo) => {
    // If last message is not read AND sender is not me
    return !convo.last_message.read_at && convo.last_message.sender_id !== authUser?.id
}

const formatTime = (date) => {
    if (!date) return ''
    const d = new Date(date)
    const now = new Date()
    const diff = now - d
    
    // If less than 24h
    if (diff < 86400000 && d.getDate() === now.getDate()) {
        return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
    }
    return d.toLocaleDateString([], { month: 'short', day: 'numeric' })
}

const openConversation = (userId) => {
    closeDropdown()
    router.push({ path: '/messages', query: { userId: userId } })
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

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
