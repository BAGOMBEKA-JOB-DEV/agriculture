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
        class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/20 p-4"
        @click="handleBackdropClick"
      >
        <Transition
          enter-active-class="transition-all duration-300 ease-out"
          leave-active-class="transition-all duration-200 ease-in"
          enter-from-class="opacity-0 scale-95 translate-y-4"
          enter-to-class="opacity-100 scale-100 translate-y-0"
          leave-from-class="opacity-100 scale-100 translate-y-0"
          leave-to-class="opacity-0 scale-95 translate-y-4"
        >
          <div
            v-if="isOpen"
            class="bg-white rounded-2xl shadow-2xl w-full max-h-[90vh] flex flex-col"
            :class="maxWidthClass"
            @click.stop
          >
            <!-- Header -->
            <div v-if="title" class="px-8 py-6 border-b border-gray-100 flex items-center justify-between">
              <div class="flex items-center space-x-4">
                 <div v-if="type === 'danger'" class="h-10 w-10 rounded-full bg-red-50 flex items-center justify-center text-red-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                 </div>
                 <div v-else-if="type === 'success'" class="h-10 w-10 rounded-full bg-green-50 flex items-center justify-center text-green-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                 </div>
                 <div v-else class="h-10 w-10 rounded-full bg-agri-forest/5 flex items-center justify-center text-agri-forest">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                 </div>
                 <div>
                    <h3 class="text-lg font-black text-gray-900 tracking-tight">{{ title }}</h3>
                    <p v-if="message" class="text-sm text-gray-500 font-medium">{{ message }}</p>
                 </div>
              </div>
              <button @click="cancel" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
              </button>
            </div>

            <!-- Content -->
            <div class="p-8 overflow-y-auto custom-scrollbar">
               <slot></slot>
            </div>

            <!-- Footer -->
            <div v-if="!hideFooter" class="bg-gray-50/50 px-8 py-6 border-t border-gray-100 flex justify-end space-x-3 rounded-b-2xl">
              <button
                v-if="cancelText"
                @click="cancel"
                class="px-6 py-3 text-sm font-bold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-agri-forest/30 transition-all"
                :disabled="loading"
              >
                {{ cancelText }}
              </button>

              <button
                v-if="confirmText"
                :type="formId ? 'submit' : 'button'"
                :form="formId || undefined"
                @click="!formId ? confirm() : null"
                :class="[
                    'px-6 py-3 text-sm font-bold text-white border border-transparent rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all shadow-lg',
                    type === 'danger' ? 'bg-red-600 hover:bg-red-700 focus:ring-red-500 shadow-red-500/20' : 'bg-agri-forest hover:bg-agri-lush focus:ring-agri-forest shadow-agri-forest/20'
                ]"
                :disabled="loading"
              >
                <span v-if="loading" class="inline-flex items-center">
                  <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Processing...
                </span>
                <span v-else>{{ confirmText }}</span>
              </button>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
defineProps({
  isOpen: { type: Boolean, default: false },
  title: { type: String, default: '' },
  message: { type: String, default: '' },
  confirmText: { type: String, default: '' },
  cancelText: { type: String, default: 'Cancel' },
  loading: { type: Boolean, default: false },
  type: { type: String, default: 'primary' }, // primary, danger, success
  hideFooter: { type: Boolean, default: false },
  maxWidthClass: { type: String, default: 'max-w-md' },
  formId: { type: String, default: '' } // Form ID to submit
})

const emit = defineEmits(['confirm', 'cancel'])

const handleBackdropClick = () => emit('cancel')
const confirm = () => emit('confirm')
const cancel = () => emit('cancel')
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: rgba(200, 200, 200, 0.5);
  border-radius: 20px;
}
</style>
