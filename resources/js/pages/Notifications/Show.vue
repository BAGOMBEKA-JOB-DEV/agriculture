<template>
  <div class="min-h-screen bg-gray-50/30 pb-20">
    <!-- Header: Ultra Slim & Slick -->
    <div class="bg-white border-b border-gray-100 px-8 py-5 sticky top-0 z-50">
      <div class="max-w-6xl mx-auto flex items-center justify-between">
        <div class="flex items-center gap-5">
          <button @click="$router.push('/notifications')"
            class="flex items-center justify-center w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:text-agri-forest hover:bg-gray-100 transition-all border border-gray-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
            </svg>
          </button>
          <div v-if="notification">
            <div class="flex items-center gap-3 mb-0.5">
              <span :class="priorityClasses"
                class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-widest border">
                {{ notification.data.priority }}
              </span>
              <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Case #{{
                notification.id.substring(0, 8) }}</span>
            </div>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight">{{ notification.data.subject }}</h1>
          </div>
        </div>
        <div class="text-right">
          <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Received</p>
          <p class="text-xs font-bold text-gray-900">{{ dayjs(notification?.created_at).format('MMM D, YYYY') }}</p>
        </div>
      </div>
    </div>

    <!-- Main View -->
    <div class="max-w-6xl mx-auto px-8 py-10">
      <div v-if="loading" class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <div class="lg:col-span-3 h-96 bg-white border border-gray-100 rounded-2xl animate-pulse"></div>
        <div class="h-96 bg-white border border-gray-100 rounded-2xl animate-pulse"></div>
      </div>

      <div v-else-if="notification" class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        <!-- Left: Statement & Content (3/4) -->
        <div class="lg:col-span-3 space-y-8">

          <!-- Official Statement -->
          <div class="bg-white rounded-2xl border border-gray-100 p-8">
            <h4 class="text-[9px] font-black text-agri-forest uppercase tracking-[0.2em] mb-4">Official Notification
            </h4>
            <div class="text-gray-600 font-medium leading-relaxed text-base"
              v-html="parseMessage(notification.data.message)"></div>
          </div>

          <!-- The Content Archive: Minimalist & High Contrast -->
          <div
            v-if="notification.data.content_details?.post_content || notification.data.content_details?.comment_content"
            class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div class="px-8 py-5 bg-gray-50/50 border-b border-gray-100 flex items-center justify-between">
              <h4 class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Enforcement Archive</h4>
              <span class="text-[10px] font-bold text-gray-400 font-mono">ID: #{{ notification.id.substring(0, 6)
                }}</span>
            </div>

            <div class="p-8 space-y-8">
              <div class="relative pl-6">
                <div class="absolute left-0 top-0 w-1 h-full bg-agri-forest/30 rounded-full"></div>
                <p class="text-xl font-serif italic text-gray-800 leading-relaxed">
                  "{{ notification.data.content_details.comment_content ||
                    notification.data.content_details.post_content }}"
                </p>
              </div>

              <!-- Minimal Post Stats Row -->
              <div
                class="flex items-center gap-4 text-[9px] font-black uppercase tracking-widest text-gray-400 pt-2 border-t border-gray-50">
                <div class="flex items-center gap-2">
                  <span class="text-gray-300">Timestamp:</span>
                  <span class="text-gray-600">{{ formatTimeShort(notification.data.content_details.post_date) }}</span>
                </div>
                <div class="h-3 w-px bg-gray-200"></div>
                <div class="flex items-center gap-2">
                  <span class="text-gray-300">Action:</span>
                  <span class="text-red-500">{{ notification.data.content_details.hidden_at ? 'Hidden' :
                    (notification.data.content_details.removed_at ? 'Removed' : 'Review') }}</span>
                </div>
              </div>

              <!-- Slick Media Preview -->
              <div v-if="notification.data.image_url" class="pt-4">
                <img :src="notification.data.image_url"
                  class="w-full h-auto rounded-xl border border-gray-100 object-cover max-h-[500px]" alt="Evidence">
              </div>
            </div>
          </div>

          <!-- Minimalist Action -->
          <div v-if="notification.data.action_url" class="flex justify-start">
            <a :href="notification.data.action_url"
              class="flex items-center gap-3 px-8 py-3 bg-agri-forest text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-agri-lush transition-all duration-300 shadow-agri-forest/5 hover:-translate-y-0.5">
              View Post Detail
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3">
                </path>
              </svg>
            </a>
          </div>
        </div>

        <!-- Right: Slick Metric Grid (1/4) -->
        <div class="space-y-6">

          <!-- Colored Metric Grid (Matching Screenshot Style) -->
          <div v-if="notification.data.content_details?.metrics" class="space-y-3">
            <h4 class="text-[9px] font-black text-gray-400 uppercase tracking-widest text-center mb-4">Post Analytics
            </h4>

            <!-- Blue: Reactions -->
            <div class="bg-blue-50/40 border border-blue-100 p-5 rounded-2xl text-center">
              <p class="text-3xl font-black text-blue-600 leading-none mb-1">{{
                notification.data.content_details.metrics.reactions }}</p>
              <p class="text-[9px] font-black text-blue-400 uppercase tracking-wider">Total Likes</p>
            </div>

            <!-- Purple: Comments -->
            <div class="bg-purple-50/40 border border-purple-100 p-5 rounded-2xl text-center">
              <p class="text-3xl font-black text-purple-600 leading-none mb-1">{{
                notification.data.content_details.metrics.comments }}</p>
              <p class="text-[9px] font-black text-purple-400 uppercase tracking-wider">Comments Received</p>
            </div>

            <!-- Red: Reports -->
            <div class="bg-red-50/40 border border-red-100 p-5 rounded-2xl text-center">
              <p class="text-3xl font-black text-red-600 leading-none mb-1">{{
                notification.data.content_details.metrics.reports }}</p>
              <p class="text-[9px] font-black text-red-500 uppercase tracking-wider">Reports</p>
            </div>
          </div>

          <!-- Author Mini Dossier (Clean/Slick) -->
          <div v-if="notification.data.content_details?.author"
            class="bg-white rounded-2xl border border-gray-100 p-6 space-y-6">
            <div class="flex items-center gap-3">
              <div
                class="w-10 h-10 rounded-lg bg-agri-forest/5 border border-agri-forest/10 flex items-center justify-center text-xs font-black text-agri-forest">
                {{ notification.data.content_details.author.name.charAt(0) }}
              </div>
              <div>
                <h5 class="text-xs font-bold text-gray-900 leading-none mb-1">{{
                  notification.data.content_details.author.name }}</h5>
                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Platform Member</p>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-4 border-t border-gray-50">
              <div class="bg-gray-50/50 p-3 rounded-xl border border-gray-100">
                <p class="text-[8px] font-black text-gray-400 uppercase tracking-tighter leading-none mb-1">Posts</p>
                <p class="text-sm font-black text-gray-900 leading-none">{{
                  notification.data.content_details.author.total_posts }}</p>
              </div>
              <div class="bg-gray-50/50 p-3 rounded-xl border border-gray-100 text-center">
                <p class="text-[8px] font-black text-gray-400 uppercase tracking-tighter leading-none mb-1">Joined</p>
                <p class="text-[10px] font-bold text-gray-900 leading-none truncate">{{
                  dayjs(notification.data.content_details.author.joined_at).format('MMM YY') }}</p>
              </div>
            </div>
          </div>

          <!-- Bottom Policy Hint (Slick) -->
          <div class="bg-gray-900 rounded-2xl p-6 text-white text-center">
            <p class="text-[8px] font-black uppercase tracking-[0.2em] opacity-40 mb-2">Platform Protocol</p>
            <p class="text-[10px] font-bold leading-relaxed opacity-80">This action is consistent with our AgriPulse
              Moderation Guidelines.</p>
          </div>

        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { useRoute } from 'vue-router';
import dayjs from 'dayjs';

const route = useRoute();
const notification = ref(null);
const loading = ref(true);

const fetchNotification = async () => {
  loading.value = true;
  try {
    const response = await axios.get(`/api/notifications/${route.params.id}`);
    notification.value = response.data;

    if (!notification.value.read_at) {
      await axios.post(`/api/notifications/${notification.value.id}/read`);
    }
  } catch (error) {
    console.error('Error fetching notification:', error);
  } finally {
    loading.value = false;
  }
};

const priorityClasses = computed(() => {
  const p = notification.value?.data.priority;
  if (p === 'critical') return 'bg-red-50 text-red-600 border-red-100';
  if (p === 'high') return 'bg-orange-50 text-orange-600 border-orange-100';
  if (p === 'success') return 'bg-green-50 text-green-600 border-green-100';
  return 'bg-blue-50 text-blue-600 border-blue-100';
});

const formatTimeShort = (date) => {
  if (!date) return '';
  return dayjs(date).format('MMM D, YYYY • h:mm A');
};

const parseMessage = (message) => {
  if (!message) return '';
  return message.replace(
    /\[([^\]]+)\]\(([^)]+)\)/g,
    '<a href="$2" class="text-agri-forest font-black hover:underline relative z-10" target="_blank">$1</a>'
  );
};

onMounted(() => {
  fetchNotification();
});
</script>

<style scoped>
.prose :deep(a) {
  color: #2D5B47;
  font-weight: 900;
  text-decoration: underline;
  text-decoration-thickness: 1.5px;
  text-underline-offset: 2px;
}
</style>
