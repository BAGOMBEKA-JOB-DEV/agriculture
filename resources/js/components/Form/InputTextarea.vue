<template>
  <div>
    <label v-if="label" :for="id" class="block text-sm font-medium leading-6 text-gray-900">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    <div class="mt-2">
      <textarea
        :id="id"
        :value="modelValue"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        :readonly="readonly"
        :rows="rows"
        :maxlength="maxlength"
        :class="[
          'block w-full rounded-md border-0 py-1.5 text-gray-900 p-2 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400  focus:ring-indigo-600 sm:text-sm sm:leading-6',
          {
            'bg-gray-50 cursor-not-allowed': disabled,
            'bg-gray-100': readonly,
            'ring-red-300 focus:ring-red-600': error
          }
        ]"
        @input="$emit('update:modelValue', $event.target.value)"
        @blur="$emit('blur', $event)"
        @focus="$emit('focus', $event)"
      ></textarea>
    </div>
    <div v-if="maxlength" class="mt-2 flex justify-end">
      <span class="text-xs text-gray-500">
        {{ modelValue?.length || 0 }}/{{ maxlength }}
      </span>
    </div>
    <p v-if="error && typeof error === 'string'" class="mt-2 text-sm text-red-600">
      {{ error }}
    </p>
    <p v-if="hint && !error" class="mt-2 text-sm text-gray-500">
      {{ hint }}
    </p>
  </div>
</template>

<script setup>
defineProps({
  id: {
    type: String,
    default: () => `textarea-${Math.random().toString(36).substr(2, 9)}`
  },
  label: {
    type: String,
    default: ''
  },
  modelValue: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  readonly: {
    type: Boolean,
    default: false
  },
  rows: {
    type: Number,
    default: 3
  },
  maxlength: {
    type: Number,
    default: null
  },
  error: {
    type: [String, Boolean],
    default: false
  },
  hint: {
    type: String,
    default: ''
  }
})

defineEmits(['update:modelValue', 'blur', 'focus'])
</script>
