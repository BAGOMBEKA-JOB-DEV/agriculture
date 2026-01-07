<template>
  <div class="min-h-screen bg-gray-50/50 pb-12">
    <!-- Header -->
    <div class="bg-white border-b border-gray-100 px-8 py-6">
      <div class="max-w-4xl mx-auto">
        <button @click="$router.back()" class="flex items-center text-sm font-bold text-gray-500 hover:text-agri-forest transition-colors mb-4">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Inbox
        </button>
        <div v-if="notification" class="flex flex-col space-y-4">
            <div class="flex items-center space-x-3">
                 <span v-if="notification.data.priority === 'critical'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    Critical
                </span>
                <span v-else-if="notification.data.priority === 'high'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                    High
                </span>
                 <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    Info
                </span>
                <span class="text-sm font-bold text-gray-400">{{ formatTime(notification.created_at) }}</span>
            </div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">{{ notification.data.subject }}</h1>
        </div>
        <div v-else class="animate-pulse h-12 w-3/4 bg-gray-100 rounded-xl"></div>
      </div>
    </div>

    <!-- Content -->
    <div class="max-w-4xl mx-auto px-6 py-8">
      <div v-if="loading" class="space-y-4">
        <div class="h-4 bg-gray-100 rounded w-full animate-pulse"></div>
        <div class="h-4 bg-gray-100 rounded w-5/6 animate-pulse"></div>
        <div class="h-4 bg-gray-100 rounded w-4/6 animate-pulse"></div>
      </div>
      
      <div v-else-if="notification" class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 prose prose-green max-w-none">
        <div class="whitespace-pre-wrap font-medium text-gray-700 leading-relaxed">{{ notification.data.message }}</div>
      </div>
      
      <div v-else class="text-center py-12">
        <h3 class="text-lg font-bold text-gray-900">Notification not found</h3>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRoute } from 'vue-router';

const route = useRoute();
const notification = ref(null);
const loading = ref(true);

const fetchNotification = async () => {
    loading.value = true;
    try {
        const response = await axios.get(`/api/notifications/${route.params.id}`);
        notification.value = response.data;
        
        // Auto mark as read
        if (!notification.value.read_at) {
            await axios.post(`/api/notifications/${notification.value.id}/read`);
        }
    } catch (error) {
        console.error('Error fetching notification:', error);
    } finally {
        loading.value = false;
    }
};

const formatTime = (date) => {
    return new Date(date).toLocaleDateString(undefined, {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

onMounted(() => {
    fetchNotification();
});
</script>
