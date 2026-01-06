<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-opacity duration-300"
      leave-active-class="transition-opacity duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="isOpen"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
        @click="closeModal"
      >
        <Transition
          enter-active-class="transition-all duration-300"
          leave-active-class="transition-all duration-300"
          enter-from-class="opacity-0 scale-95 translate-y-4"
          enter-to-class="opacity-100 scale-100 translate-y-0"
          leave-from-class="opacity-100 scale-100 translate-y-0"
          leave-to-class="opacity-0 scale-95 translate-y-4"
        >
          <div
            v-if="isOpen"
            class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 max-h-[80vh] flex flex-col"
            @click.stop
          >
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
              <h3 class="text-lg font-semibold text-gray-900">Messages</h3>
              <button
                @click="closeModal"
                class="text-gray-400 hover:text-gray-600 transition-colors"
              >
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>

            <!-- Messages List -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4">
              <div
                v-for="message in messages"
                :key="message.id"
                class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer"
                :class="{ 'bg-blue-50 border-l-4 border-blue-500': !message.read }"
              >
                <div class="flex-shrink-0">
                  <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                    <span class="text-sm font-medium text-gray-700">{{ message.sender.charAt(0).toUpperCase() }}</span>
                  </div>
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ message.sender }}</p>
                    <p class="text-xs text-gray-500">{{ message.time }}</p>
                  </div>
                  <p class="text-sm text-gray-600 truncate">{{ message.subject }}</p>
                  <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ message.preview }}</p>
                </div>
                <div v-if="!message.read" class="flex-shrink-0">
                  <div class="h-2 w-2 bg-blue-500 rounded-full"></div>
                </div>
              </div>

              <!-- Empty State -->
              <div v-if="messages.length === 0" class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No messages</h3>
                <p class="mt-1 text-sm text-gray-500">You don't have any messages yet.</p>
              </div>
            </div>

            <!-- Footer -->
            <div class="border-t border-gray-200 p-4">
              <button class="w-full text-center text-sm text-indigo-600 hover:text-indigo-500 font-medium">
                View All Messages
              </button>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="js">
import { ref, onMounted } from 'vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close'])

const messages = ref([])

const closeModal = () => {
  emit('close')
}

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

onMounted(() => {
  messages.value = dummyMessages
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
