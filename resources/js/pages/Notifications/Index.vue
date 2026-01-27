<template>
    <div class="min-h-screen bg-gray-50/50 pb-12">
        <!-- Header -->
        <div class="bg-white border-b border-gray-100 px-8 py-6">
            <div class="max-w-4xl mx-auto flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-black text-gray-900 tracking-tight">Inbox</h1>
                    <p class="text-sm font-medium text-gray-500 mt-1">Stay updated with system announcements and alerts.
                    </p>
                </div>
                <button @click="markAllRead"
                    class="text-sm font-bold text-agri-forest hover:text-agri-lush transition-colors">
                    Mark all read
                </button>
            </div>
        </div>

        <!-- Content -->
        <div class="max-w-4xl mx-auto px-6 py-8">

            <!-- Filters -->
            <div class="flex space-x-2 mb-6">
                <button @click="filter = 'all'" class="px-4 py-2 rounded-xl text-sm font-bold transition-all"
                    :class="filter === 'all' ? 'bg-agri-forest text-white shadow-lg shadow-agri-forest/20' : 'bg-white text-gray-500 hover:bg-gray-50'">All</button>
                <button @click="filter = 'unread'" class="px-4 py-2 rounded-xl text-sm font-bold transition-all"
                    :class="filter === 'unread' ? 'bg-agri-forest text-white shadow-lg shadow-agri-forest/20' : 'bg-white text-gray-500 hover:bg-gray-50'">Unread</button>
            </div>

            <!-- List -->
            <div class="space-y-4">
                <div v-if="loading" class="text-center py-12">
                    <svg class="animate-spin h-8 w-8 text-agri-forest mx-auto" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>

                <div v-else-if="filteredNotifications.length === 0"
                    class="text-center py-12 bg-white rounded-[2rem] border border-gray-100 border-dashed">
                    <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">No notifications found</h3>
                    <p class="text-gray-500 text-sm mt-1">When you receive alerts, they'll show up here.</p>
                </div>

                <div v-else v-for="notification in filteredNotifications" :key="notification.id"
                    @click="viewNotification(notification)"
                    class="bg-white p-6 rounded-2xl border transition-all cursor-pointer group hover:shadow-lg hover:shadow-agri-forest/5 hover:border-agri-forest/30"
                    :class="notification.read_at ? 'border-gray-100 opacity-75' : 'border-l-4 border-l-agri-forest border-y-gray-100 border-r-gray-100'">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-4 w-full">
                            <!-- Icon based on priority -->
                            <div class="mt-1 flex-shrink-0">
                                <span v-if="notification.data.priority === 'critical'"
                                    class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-red-100 text-red-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                        </path>
                                    </svg>
                                </span>
                                <span v-else-if="notification.data.priority === 'high'"
                                    class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-orange-100 text-orange-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                        </path>
                                    </svg>
                                </span>
                                <span v-else-if="notification.data.priority === 'success'"
                                    class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-green-100 text-green-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </span>
                                <span v-else
                                    class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </span>
                            </div>

                            <div class="flex-1 min-w-0">
                                <h3
                                    class="text-lg font-bold text-gray-900 group-hover:text-agri-forest transition-colors flex items-center gap-2">
                                    {{ notification.data.subject }}
                                    <span
                                        v-if="notification.data.content_details?.hidden_at || notification.data.content_details?.removed_at"
                                        class="text-[10px] uppercase font-black tracking-widest px-2 py-0.5 rounded-md bg-gray-100 text-gray-500">
                                        {{ notification.data.content_details.hidden_at ? 'Hidden' : 'Removed' }}
                                    </span>
                                </h3>
                                <div class="text-gray-600 mt-1 text-sm leading-relaxed"
                                    v-html="parseMessage(notification.data.message)"></div>

                                <!-- Rich Content: Context Card -->
                                <div v-if="notification.data.content_details && Object.keys(notification.data.content_details).length"
                                    class="mt-4 bg-gray-50 rounded-xl p-4 border border-gray-100">

                                    <!-- Comment specific view -->
                                    <div v-if="notification.data.content_details.comment_content">
                                        <div class="flex items-start gap-3 mb-3 pb-3 border-b border-gray-200/60">
                                            <div class="w-1 bg-gray-300 rounded-full self-stretch"></div>
                                            <div>
                                                <p
                                                    class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">
                                                    Your Comment</p>
                                                <p class="text-sm italic text-gray-700 font-medium">"{{
                                                    notification.data.content_details.comment_content }}"</p>
                                            </div>
                                        </div>
                                        <div v-if="notification.data.content_details.parent_post_snippet"
                                            class="text-xs text-gray-500 flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                            </svg>
                                            <span>On post by <strong class="text-gray-900">{{
                                                    notification.data.content_details.parent_post_author
                                                    }}</strong>:</span>
                                            <span class="italic max-w-md truncate">"{{
                                                notification.data.content_details.parent_post_snippet }}"</span>
                                        </div>
                                    </div>

                                    <!-- Post specific view (if snippet provided) -->
                                    <div v-else-if="notification.data.content_details.post_content">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">
                                            Post Content</p>
                                        <p class="text-sm text-gray-700 line-clamp-3">"{{
                                            notification.data.content_details.post_content }}"</p>
                                        <p class="text-[10px] text-gray-400 mt-2">Posted on {{
                                            formatTime(notification.data.content_details.post_date) }}</p>
                                    </div>
                                </div>

                                <!-- Image attachment -->
                                <div v-if="notification.data.image_url" class="mt-4">
                                    <img :src="notification.data.image_url"
                                        class="w-full max-w-md h-48 object-cover rounded-xl border border-gray-100 shadow-sm"
                                        alt="Attached content">
                                </div>


                            </div>
                        </div>
                        <div class="flex flex-col items-end space-y-2">
                            <span class="text-xs font-bold text-gray-400 whitespace-nowrap">{{
                                formatTime(notification.created_at) }}</span>
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

const parseMessage = (message) => {
    if (!message) return '';
    // Parse markdown links [text](url) -> <a href="url" ...>text</a>
    return message.replace(
        /\[([^\]]+)\]\(([^)]+)\)/g,
        '<a href="$2" class="text-agri-forest font-bold hover:underline relative z-10" target="_blank" onclick="event.stopPropagation()">$1</a>'
    );
};

onMounted(() => {
    fetchNotifications();
});
</script>
