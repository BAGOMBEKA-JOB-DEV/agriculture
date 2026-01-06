<template>
  <div class="flex items-center justify-end gap-x-6 border-t border-gray-900/10 px-4 py-4 sm:px-8">
    <button
      v-if="showCancel"
      type="button"
      :disabled="loading"
      class="text-sm font-semibold leading-6 text-gray-900 bg-gray-400 cursor-pointer px-3 py-2 rounded-md hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed"
      @click="$emit('cancel')"
    >
      {{ cancelText }}
    </button>
    <button
      v-if="showSubmit"
      type="submit"
      :disabled="loading || disabled"
      class="rounded-md bg-indigo-600 px-3 py-2 cursor-pointer text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-50 disabled:cursor-not-allowed"
    >
      <span v-if="loading" class="inline-flex items-center">
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        {{ loadingText }}
      </span>
      <span v-else>{{ submitText }}</span>
    </button>
  </div>
</template>

<script setup>
defineProps({
  loading: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  showCancel: {
    type: Boolean,
    default: true
  },
  showSubmit: {
    type: Boolean,
    default: true
  },
  cancelText: {
    type: String,
    default: 'Cancel'
  },
  submitText: {
    type: String,
    default: 'Save'
  },
  loadingText: {
    type: String,
    default: 'Saving...'
  }
})

defineEmits(['cancel'])
</script>
