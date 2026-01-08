<template>
  <div class="min-h-screen bg-gray-50/50 pb-12">
    <!-- Header -->
    <div class="bg-white border-b border-gray-100 px-8 py-6">
      <div class="max-w-4xl mx-auto flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Inbox</h1>
            <p class="text-sm font-medium text-gray-500 mt-1">Stay updated with system announcements and alerts.</p>
        </div>
        <button @click="markAllRead" class="text-sm font-bold text-agri-forest hover:text-agri-lush transition-colors">
            Mark all read
        </button>
      </div>
    </div>

    <!-- Content -->
    <div class="max-w-4xl mx-auto px-6 py-8">
      
      <!-- Filters -->
      <div class="flex space-x-2 mb-6">
        <button 
            @click="filter = 'all'" 
            class="px-4 py-2 rounded-xl text-sm font-bold transition-all"
            :class="filter === 'all' ? 'bg-agri-forest text-white shadow-lg shadow-agri-forest/20' : 'bg-white text-gray-500 hover:bg-gray-50'"
        >All</button>
        <button 
            @click="filter = 'unread'" 
            class="px-4 py-2 rounded-xl text-sm font-bold transition-all"
            :class="filter === 'unread' ? 'bg-agri-forest text-white shadow-lg shadow-agri-forest/20' : 'bg-white text-gray-500 hover:bg-gray-50'"
        >Unread</button>
      </div>

      <!-- List -->
      <div class="space-y-4">
        <div v-if="loading" class="text-center py-12">
            <svg class="animate-spin h-8 w-8 text-agri-forest mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        </div>

        <div v-else-if="filteredNotifications.length === 0" class="text-center py-12 bg-white rounded-[2rem] border border-gray-100 border-dashed">
            <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900">No notifications found</h3>
            <p class="text-gray-500 text-sm mt-1">When you receive alerts, they'll show up here.</p>
        </div>

        <div 
            v-else
            v-for="notification in filteredNotifications" 
            :key="notification.id"
            @click="viewNotification(notification)"
            class="bg-white p-6 rounded-2xl border transition-all cursor-pointer group hover:shadow-lg hover:shadow-agri-forest/5 hover:border-agri-forest/30"
            :class="notification.read_at ? 'border-gray-100 opacity-75' : 'border-l-4 border-l-agri-forest border-y-gray-100 border-r-gray-100'"
        >
            <div class="flex items-start justify-between">
                <div class="flex items-start space-x-4">
                    <div class="mt-1">
                        <span v-if="notification.data.priority === 'critical'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Critical
                        </span>
                        <span v-else-if="notification.data.priority === 'high'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                            High
                        </span>
                         <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Info
                        </span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 group-hover:text-agri-forest transition-colors">
                            {{ notification.data.subject }}
                        </h3>
                        <p class="text-gray-500 mt-1 line-clamp-2 text-sm">{{ notification.data.message }}</p>
                    </div>
                </div>
                <div class="flex flex-col items-end space-y-2">
                    <span class="text-xs font-bold text-gray-400 whitespace-nowrap">{{ formatTime(notification.created_at) }}</span>
                    <div v-if="!notification.read_at" class="h-2 w-2 rounded-full bg-agri-forest"></div>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const notifications = ref([]);
const loading = ref(true);
const filter = ref('all');

const filteredNotifications = computed(() => {
    if (filter.value === 'unread') {
        return notifications.value.filter(n => !n.read_at);
    }
    return notifications.value;
});

const fetchNotifications = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/notifications');
        notifications.value = response.data;
    } catch (error) {
        console.error('Error fetching notifications:', error);
    } finally {
        loading.value = false;
    }
};

const markAllRead = async () => {
    try {
        await axios.post('/api/notifications/read-all');
        await fetchNotifications();
    } catch (error) {
        console.error('Error marking all read:', error);
    }
};

const viewNotification = (notification) => {
    router.push(`/notifications/${notification.id}`);
};

const formatTime = (date) => {
    return new Date(date).toLocaleDateString(undefined, {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

onMounted(() => {
    fetchNotifications();
});
</script>
