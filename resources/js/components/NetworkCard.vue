<template>
    <div class="bg-white w-[350px] rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 group flex flex-col h-full">
        <div class="flex items-start justify-between mb-6">
            <!-- Avatar & Profile Intro -->
            <div class="flex items-center space-x-4">
                <div class="relative flex-shrink-0">
                    <div class="w-14 h-14 rounded-2xl bg-gray-50 border border-gray-100 overflow-hidden flex items-center justify-center text-lg font-bold text-gray-400">
                        <img v-if="user.avatar_url" :src="user.avatar_url" class="w-full h-full object-cover">
                        <span v-else>{{ user.name.charAt(0) }}</span>
                    </div>
                    <div v-if="user.status === 'active'" class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-agri-lush border-2 border-white shadow-sm" title="Active now"></div>
                </div>
                <div class="min-w-0">
                    <h3 class="text-base font-bold text-gray-900 truncate group-hover:text-agri-forest transition-colors">{{ user.name }}</h3>
                    <p class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="truncate">{{ user.location || 'Global' }}</span>
                    </p>
                </div>
            </div>

            <!-- Role Badge -->
            <div class="flex-shrink-0">
                <span class="px-3 py-1 rounded-full bg-gray-50 border border-gray-100 text-[10px] font-bold uppercase tracking-wider text-gray-500">
                    {{ user.roles?.[0]?.name || 'Member' }}
                </span>
            </div>
        </div>

        <!-- Details -->
        <div class="flex-1 mb-6">
            <div class="flex flex-wrap gap-2">
                <span v-if="user.seller_profile?.specialization" class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-600 text-[10px] font-bold">
                    {{ user.seller_profile.specialization }}
                </span>
                <span v-if="user.verified_at" class="px-2.5 py-1 rounded-lg bg-agri-lush/10 text-agri-lush text-[10px] font-bold flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Verified
                </span>
            </div>
        </div>

        <!-- Actions -->
        <div class="grid grid-cols-2 gap-3 pt-4 border-t border-gray-50">
            <button 
                @click="$emit('view', user)"
                class="w-full py-2.5 rounded-xl border border-gray-100 text-gray-600 font-bold text-xs hover:bg-gray-50 transition-colors"
            >
                View Profile
            </button>
            <button 
                @click="$emit('message', user)"
                class="w-full py-2.5 rounded-xl bg-agri-forest text-white font-bold text-xs hover:bg-agri-lush transition-colors shadow-sm"
            >
                Message
            </button>
        </div>
    </div>
</template>

<script setup>
defineProps({
    user: {
        type: Object,
        required: true
    }
})

defineEmits(['message', 'view'])
</script>
