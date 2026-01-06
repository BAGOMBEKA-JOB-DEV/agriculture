<template>
  <div class="space-y-1">
    <label v-if="label" :for="id" class="block text-sm font-medium leading-6 text-gray-900">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    <div class="relative mt-2 rounded-md ">
      <input
        :id="id"
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        :readonly="readonly"
        :autocomplete="autocomplete"
        :class="[
          'block w-full rounded-md border-0 py-1.5 text-gray-900 p-2  ring-1 ring-gray-300 placeholder:text-gray-400  focus:ring-indigo-600 sm:text-sm sm:leading-6',
          {
            'bg-gray-50 cursor-not-allowed': disabled,
            'bg-gray-100': readonly,
            'ring-red-300 focus:ring-red-600': error,
            'pr-10': icon
          }
        ]"
        @input="$emit('update:modelValue', $event.target.value)"
        @blur="$emit('blur', $event)"
        @focus="$emit('focus', $event)"
      />
      <div v-if="icon" class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
        <component :is="icon" class="h-5 w-5 text-gray-400" aria-hidden="true" />
      </div>
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
    default: () => `input-${Math.random().toString(36).substr(2, 9)}`
  },
  label: {
    type: String,
    default: ''
  },
  modelValue: {
    type: [String, Number],
    default: ''
  },
  type: {
    type: String,
    default: 'text'
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
  error: {
    type: [String, Boolean],
    default: false
  },
  hint: {
    type: String,
    default: ''
  },
  autocomplete: {
    type: String,
    default: 'off'
  },
  icon: {
    type: Object,
    default: null
  }
})

defineEmits(['update:modelValue', 'blur', 'focus'])
</script>
