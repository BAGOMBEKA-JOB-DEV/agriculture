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
      class="fixed inset-x-0 top-[65px] sm:absolute sm:inset-auto sm:right-0 sm:mt-3 w-full sm:w-96 bg-white sm:rounded-2xl shadow-xl ring-1 ring-black ring-opacity-5 z-[70] overflow-hidden transition-all"
      @click.stop
    >
      <!-- Header -->
      <div class="flex items-center justify-between p-5 border-b border-gray-100 bg-white">
        <div>
          <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Direct Messages</h3>
          <p class="text-[10px] font-medium text-gray-400 mt-0.5">Secure marketplace transmissions</p>
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

      <!-- Messages List -->
      <div class="max-h-[32rem] overflow-y-auto divide-y divide-gray-50">
        <div
          v-for="convo in messages"
          :key="convo.last_message.id"
          @click="openConversation(convo.user.id)"
          class="flex items-start space-x-4 p-4 hover:bg-gray-50/80 transition-colors cursor-pointer group"
          :class="{ 'bg-agri-sand/5': isUnread(convo) }"
        >
          <div class="flex-shrink-0 relative">
            <div class="h-11 w-11 rounded-xl bg-agri-sand/20 text-agri-forest flex items-center justify-center font-semibold shadow-sm group-hover:bg-agri-forest group-hover:text-white transition-all overflow-hidden">
              <img v-if="convo.user.avatar_url" :src="convo.user.avatar_url" class="w-full h-full object-cover">
              <span v-else>{{ convo.user.name.charAt(0).toUpperCase() }}</span>
            </div>
            <div v-if="isUnread(convo)" class="absolute -top-0.5 -right-0.5 h-3 w-3 bg-agri-forest border-2 border-white rounded-full"></div>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
              <p class="text-sm font-semibold text-gray-900 truncate group-hover:text-agri-forest transition-colors">{{ convo.user.name }}</p>
              <p class="text-[10px] font-medium text-gray-400">{{ formatTime(convo.last_message.created_at) }}</p>
            </div>
            <p class="text-xs text-gray-500 mt-0.5 line-clamp-1 leading-relaxed" :class="{ 'text-gray-900 font-medium': isUnread(convo) }">
                <span v-if="convo.last_message.sender_id === authUser?.id" class="text-gray-400 font-bold mr-1">You:</span>
                {{ convo.last_message.content || 'Attachment' }}
            </p>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="messages.length === 0" class="text-center py-12 px-6">
            <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
            </div>
            <h3 class="text-sm font-semibold text-gray-900">No transmissions</h3>
            <p class="mt-1 text-xs text-gray-500 leading-relaxed">Your message history is currently empty. Start a new conversation to connect.</p>
        </div>
      </div>

      <!-- Footer -->
      <div class="bg-gray-50/50 p-4 border-t border-gray-100">
        <button @click="router.push('/messages')" class="w-full py-2 text-center text-xs font-semibold text-agri-forest hover:text-agri-lush transition-colors">
          View all transmissions
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

const authUser = authStore.user

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
}

const closeDropdown = () => {
  isOpen.value = false
  emit('close')
}

const isUnread = (convo) => {
    return !convo.last_message.read_at && convo.last_message.sender_id !== authUser?.id
}

const formatTime = (date) => {
    if (!date) return ''
    const d = new Date(date)
    const now = new Date()
    const diff = now - d
    
    if (diff < 86400000 && d.getDate() === now.getDate()) {
        return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
    }
    return d.toLocaleDateString([], { month: 'short', day: 'numeric' })
}

const openConversation = (userId) => {
    closeDropdown()
    router.push({ path: '/messages', query: { userId: userId } })
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

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
