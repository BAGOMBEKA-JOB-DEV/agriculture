<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-4">
        <div class="flex items-start space-x-3 mb-4">
            <div
                class="h-10 w-10 rounded-full bg-agri-earth flex items-center justify-center overflow-hidden flex-shrink-0">
                <img v-if="user?.avatar_url" :src="user.avatar_url" class="w-full h-full object-cover">
                <span v-else class="text-sm font-bold text-agri-forest">{{ userInitial }}</span>
            </div>
            <button @click="$emit('open-modal')"
                class="flex-1 bg-gray-50 border border-gray-200 rounded-full px-4 py-2.5 text-left text-sm font-medium text-gray-500 hover:bg-gray-100 transition-colors">
                Start a post...
            </button>
        </div>

        <div class="flex items-center justify-between pt-1">
            <div class="flex items-center space-x-1">
                <button @click="$emit('open-modal', 'photo')"
                    class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors group">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    <span class="text-xs font-bold text-gray-500 group-hover:text-gray-700">Photo</span>
                </button>

                <button @click="$emit('open-modal', 'video')"
                    class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors group">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                        </path>
                    </svg>
                    <span class="text-xs font-bold text-gray-500 group-hover:text-gray-700">Video</span>
                </button>

                <button @click="$emit('open-modal', 'product')"
                    class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors group">
                    <svg class="w-5 h-5 text-agri-lush" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    <span class="text-xs font-bold text-gray-500 group-hover:text-gray-700">Product</span>
                </button>
            </div>

            <button @click="$emit('open-modal', 'article')"
                class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors group">
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 4v4h4"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h2m-2 4h6m-6 4h6">
                    </path>
                </svg>
                <span class="text-xs font-bold text-gray-500 group-hover:text-gray-700">Write article</span>
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    user: {
        type: Object,
        required: true
    }
})

const userInitial = computed(() => {
    return props.user?.name?.charAt(0)?.toUpperCase() || 'U'
})
</script>
