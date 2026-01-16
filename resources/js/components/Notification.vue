<template>
  <div class="fixed w-100 top-4 right-4 z-50 space-y-2">
    <TransitionGroup name="notification">
      <div
        v-for="notification in notifications"
        :key="notification.id"
        :class="[
          'max-w-sm w-full bg-white shadow-2xl rounded-2xl pointer-events-auto border overflow-hidden transition-all duration-300',
          notification.type === 'success' ? 'border-green-100' : 
          notification.type === 'error' ? 'border-red-100' : 
          notification.type === 'access_denied' ? 'border-agri-forest/20 shadow-agri-forest/5' :
          notification.type === 'warning' ? 'border-yellow-100' : 'border-blue-100'
        ]"
      >
        <div v-if="notification.type === 'access_denied'" class="h-1.5 w-full bg-agri-forest/10 relative overflow-hidden">
            <div class="absolute inset-0 bg-agri-forest animate-pulse opacity-20"></div>
        </div>
        <div class="p-5">
          <div class="flex items-start">
            <div class="flex-shrink-0">
               <div v-if="notification.type === 'access_denied'" class="w-10 h-10 rounded-xl bg-agri-forest/10 flex items-center justify-center text-agri-forest">
                   <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
               </div>
              <component
                v-else
                :is="getIcon(notification.type)"
                class="h-6 w-6"
                :class="getIconColor(notification.type)"
              />
            </div>
            <div class="ml-4 w-0 flex-1 pt-0.5">
              <p class="text-[10px] font-black uppercase tracking-widest mb-1" :class="notification.type === 'access_denied' ? 'text-agri-forest' : 'text-gray-400'">
                {{ notification.type === 'access_denied' ? 'Permission Needed' : (notification.type === 'error' ? 'System Error' : 'Notice') }}
              </p>
              <p class="text-sm font-bold text-gray-900 leading-tight">
                {{ notification.title }}
              </p>
              <p class="mt-2 text-xs font-medium text-gray-500 leading-relaxed">
                {{ notification.message }}
              </p>
              
              <div v-if="notification.type === 'access_denied'" class="mt-4 pt-4 border-t border-gray-50 flex items-center justify-between">
                  <span class="text-[9px] font-black text-agri-forest/40 uppercase tracking-tighter">Support ID: {{ notification.id }}</span>
                  <button @click="removeNotification(notification.id)" class="text-[9px] font-black text-agri-forest uppercase tracking-widest hover:text-agri-lush transition-colors">Acknowledge</button>
              </div>
            </div>
            <div v-if="notification.type !== 'access_denied'" class="ml-4 flex-shrink-0 flex">
              <button
                @click="removeNotification(notification.id)"
                class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                <span class="sr-only">Close</span>
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </TransitionGroup>
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

const ExclamationIcon = {
  template: '<svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>'
}

const InformationCircleIcon = {
  template: '<svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>'
}

const notifications = ref([])
let notificationId = 0

const getIcon = (type) => {
  switch (type) {
    case 'success':
      return CheckCircleIcon
    case 'error':
      return ExclamationCircleIcon
    case 'warning':
      return ExclamationIcon
    case 'info':
    default:
      return InformationCircleIcon
  }
}

const getIconColor = (type) => {
  switch (type) {
    case 'success':
      return 'text-green-400'
    case 'error':
      return 'text-red-400'
    case 'warning':
      return 'text-yellow-400'
    case 'info':
    default:
      return 'text-blue-400'
  }
}

const addNotification = (type, title, message, duration = 120000) => {
  const id = ++notificationId
  notifications.value.push({
    id,
    type,
    title,
    message
  })

  if (duration > 0) {
    setTimeout(() => {
      removeNotification(id)
    }, duration)
  }

  return id
}

const removeNotification = (id) => {
  const index = notifications.value.findIndex(n => n.id === id)
  if (index > -1) {
    notifications.value.splice(index, 1)
  }
}

// Global notification functions
const notify = {
  success: (title, message, duration) => addNotification('success', title, message, duration),
  error: (title, message, duration) => addNotification('error', title, message, duration),
  warning: (title, message, duration) => addNotification('warning', title, message, duration),
  info: (title, message, duration) => addNotification('info', title, message, duration),
  accessDenied: (permission) => addNotification(
      'access_denied', 
      'Sorry!', 
      `You don't have permission to do this. Please contact support if you need more help getting this access.`, 
      0
  )
}

// Make notify available globally
if (typeof window !== 'undefined') {
  window.notify = notify
}

// Export for use in other components
defineExpose({
  addNotification,
  removeNotification,
  notify
})
</script>

<style scoped>
.notification-enter-active,
.notification-leave-active {
  transition: all 0.3s ease;
}

.notification-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.notification-leave-to {
  opacity: 0;
  transform: translateX(100%);
}

.notification-move {
  transition: transform 0.3s ease;
}
</style>
