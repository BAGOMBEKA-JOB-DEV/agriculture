<template>
  <transition name="fade">
    <div 
      v-if="show" 
      :class="[
        'relative px-4 py-4 rounded-2xl flex items-start space-x-3 border shadow-sm transition-all',
        typeClasses[type] || typeClasses.error
      ]"
    >
      <!-- Icon -->
      <div class="mt-0.5 shrink-0">
        <slot name="icon">
          <svg v-if="type === 'error'" class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <svg v-else-if="type === 'warning'" class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <svg v-else class="w-5 h-5 text-agri-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </slot>
      </div>

      <!-- Content -->
      <div class="flex-1 pr-8">
        <h4 v-if="title" class="text-sm font-black mb-1 uppercase tracking-wider">{{ title }}</h4>
        <div class="text-sm font-medium leading-relaxed">
          <slot>{{ message }}</slot>
        </div>
      </div>

      <!-- Close Button -->
      <button 
        v-if="closable" 
        @click="close"
        class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors p-1"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
  </transition>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: true
  },
  type: {
    type: String,
    default: 'error',
    validator: (value) => ['error', 'warning', 'info', 'success'].includes(value)
  },
  title: String,
  message: String,
  closable: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['update:show', 'close'])

const typeClasses = {
  error: 'bg-red-50 border-red-100 text-red-900',
  warning: 'bg-amber-50 border-amber-100 text-amber-900',
  info: 'bg-agri-sand/10 border-agri-sand/20 text-agri-forest',
  success: 'bg-emerald-50 border-emerald-100 text-emerald-900'
}

const close = () => {
  emit('update:show', false)
  emit('close')
}

// Auto-close logic can be added here if needed via another prop
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
