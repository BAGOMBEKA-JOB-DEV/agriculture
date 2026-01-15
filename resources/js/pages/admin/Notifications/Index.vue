<template>
  <div class="min-h-screen bg-gray-50/50 pb-12">
    <!-- Notifications Component for Global Alerts -->
    <Notification ref="notificationRef" />

    <!-- Header Section -->
    <div class="bg-white border-b border-gray-100 px-8 py-8 mb-8">
      <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
          <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Broadcast Center</h1>
            <p class="mt-2 text-sm font-medium text-gray-500">Manage system-wide announcements and alerts within the ecosystem.</p>
          </div>
          <div class="flex items-center space-x-3">
             <router-link
              to="/admin/notifications/create"
              class="inline-flex items-center justify-center px-6 py-3 border border-transparent shadow-lg shadow-agri-forest/20 text-sm font-bold rounded-xl text-white bg-agri-forest hover:bg-agri-lush transform hover:-translate-y-0.5 transition-all duration-200"
            >
              <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              New Announcement
            </router-link>
          </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
          <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center justify-between group hover:border-agri-forest/30 transition-all">
            <div>
              <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Broadcasts</p>
              <h3 class="text-3xl font-black text-gray-900">{{ pagination.total || 0 }}</h3>
            </div>
            <div class="h-12 w-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
            </div>
          </div>
          
          <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center justify-between group hover:border-agri-forest/30 transition-all">
            <div>
              <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Critical Alerts</p>
              <h3 class="text-3xl font-black text-gray-900">{{ criticalCount }}</h3>
            </div>
            <div class="h-12 w-12 rounded-xl bg-red-50 flex items-center justify-center text-red-600 group-hover:bg-red-600 group-hover:text-white transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
            </div>
          </div>

          <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center justify-between group hover:border-agri-forest/30 transition-all">
            <div>
              <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Last Broadcast</p>
              <h3 class="text-lg font-bold text-gray-900 truncate max-w-[150px]">{{ lastBroadcastDate }}</h3>
            </div>
            <div class="h-12 w-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-colors">
               <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-8">
      <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Loading State -->
        <div v-if="loading" class="p-24 text-center">
          <svg class="animate-spin h-10 w-10 text-agri-forest mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <p class="text-sm font-medium text-gray-500">Retrieving broadcast history...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="announcements.length === 0" class="p-24 text-center">
          <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-3xl bg-gray-50 mb-6">
            <svg class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
          </div>
          <h3 class="text-xl font-bold text-gray-900">No announcements yet</h3>
          <p class="mt-2 text-gray-500 max-w-sm mx-auto">Get started by broadcasting your first system-wide announcement to all users.</p>
          <div class="mt-8">
            <router-link
              to="/admin/notifications/create"
              class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-sm font-bold rounded-xl text-white bg-agri-forest hover:bg-agri-lush transition-colors"
            >
              Create Announcement
            </router-link>
          </div>
        </div>

        <!-- Table -->
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-100">
            <thead>
              <tr class="bg-gray-50/50">
                <th scope="col" class="px-8 py-5 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Subject</th>
                <th scope="col" class="px-8 py-5 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">Priority</th>
                <th scope="col" class="px-8 py-5 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">Sent By</th>
                <th scope="col" class="px-8 py-5 text-right text-xs font-bold text-gray-400 uppercase tracking-widest">Date</th>
                <th scope="col" class="relative px-8 py-5">
                  <span class="sr-only">Actions</span>
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-50">
              <tr v-for="announcement in announcements" :key="announcement.id" class="group hover:bg-agri-sand/10 transition-colors duration-200">
                <td class="px-8 py-5 whitespace-nowrap">
                  <div class="flex flex-col">
                    <span class="text-sm font-bold text-gray-900 group-hover:text-agri-forest transition-colors">{{ announcement.subject }}</span>
                    <span class="text-xs text-gray-500 truncate max-w-xs mt-0.5">{{ stripHtml(announcement.message) }}</span>
                  </div>
                </td>
                <td class="px-8 py-5 whitespace-nowrap text-center">
                    <span :class="[
                      'px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full border',
                      priorityClass(announcement.priority)
                    ]">
                      {{ capitalize(announcement.priority) }}
                    </span>
                </td>
                <td class="px-8 py-5 whitespace-nowrap text-sm text-gray-500 text-center">
                  <div class="flex items-center justify-center">
                    <div class="h-6 w-6 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-500 mr-2 overflow-hidden shadow-sm">
                        <img v-if="announcement.creator?.avatar_url" :src="announcement.creator.avatar_url" class="w-full h-full object-cover">
                        <span v-else>{{ announcement.creator ? announcement.creator.name.charAt(0) : '?' }}</span>
                    </div>
                    {{ announcement.creator ? announcement.creator.name : 'Unknown' }}
                  </div>
                </td>
                <td class="px-8 py-5 whitespace-nowrap text-right text-sm text-gray-500 font-medium">
                  {{ formatDate(announcement.created_at) }}
                </td>
                <td class="px-8 py-5 whitespace-nowrap text-right text-sm font-medium">
                  <router-link 
                    :to="`/admin/notifications/${announcement.id}/edit`" 
                    class="text-gray-400 hover:text-agri-forest transition-colors p-2 rounded-lg hover:bg-gray-50 inline-flex"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                  </router-link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        <div v-if="pagination.total > pagination.per_page" class="bg-gray-50/50 px-8 py-5 border-t border-gray-100 flex items-center justify-between">
            <div>
              <p class="text-xs font-medium text-gray-500">
                Showing <span class="font-bold text-gray-900">{{ pagination.from }}</span> to <span class="font-bold text-gray-900">{{ pagination.to }}</span> of <span class="font-bold text-gray-900">{{ pagination.total }}</span> results
              </p>
            </div>
            <div>
              <nav class="relative z-0 inline-flex rounded-xl shadow-sm -space-x-px" aria-label="Pagination">
                <button 
                  @click="fetchAnnouncements(pagination.current_page - 1)"
                  :disabled="pagination.current_page === 1"
                  class="relative inline-flex items-center px-3 py-2 rounded-l-xl border border-gray-200 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <span class="sr-only">Previous</span>
                  <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L8.414 10l4.293 4.293a1 1 0 01-1.414 1.414l-5-5a1 1 0 010-1.414l5-5a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                </button>
                <button
                  v-for="page in pagination.last_page"
                  :key="page"
                  @click="fetchAnnouncements(page)"
                  :class="[
                    page === pagination.current_page ? 'z-10 bg-agri-forest border-agri-forest text-white' : 'bg-white border-gray-200 text-gray-500 hover:bg-gray-50',
                    'relative inline-flex items-center px-4 py-2 border text-xs font-bold'
                  ]"
                >
                  {{ page }}
                </button>
                <button 
                  @click="fetchAnnouncements(pagination.current_page + 1)"
                  :disabled="pagination.current_page === pagination.last_page"
                  class="relative inline-flex items-center px-3 py-2 rounded-r-xl border border-gray-200 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <span class="sr-only">Next</span>
                  <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                  </svg>
                </button>
              </nav>
            </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import Notification from '../../../components/Notification.vue';

const notificationRef = ref(null);
const announcements = ref([]);
const loading = ref(true);
const pagination = ref({});

const criticalCount = computed(() => {
    // This is a rough client-side estimate based on current page, ideally should come from backend
    // Since we don't have a stats endpoint yet, we'll just count present items or return '-'
    // To do it properly we would need an API change.
    // For now, let's just count from the current page to show *something* or maybe filtering
    return announcements.value.filter(a => a.priority === 'critical').length;
});

const lastBroadcastDate = computed(() => {
    if (announcements.value.length > 0) {
        return formatDateShort(announcements.value[0].created_at);
    }
    return 'N/A';
});

const fetchAnnouncements = async (page = 1) => {
  loading.value = true;
  try {
    const response = await axios.get(`/api/admin/notifications?page=${page}`);
    announcements.value = response.data.data;
    pagination.value = {
        current_page: response.data.current_page,
        last_page: response.data.last_page,
        per_page: response.data.per_page,
        total: response.data.total,
        from: response.data.from,
        to: response.data.to
    };
  } catch (error) {
    console.error('Error fetching announcements:', error);
    notificationRef.value?.notify.error('Error', 'Failed to fetch announcements.');
  } finally {
    loading.value = false;
  }
};

const priorityClass = (priority) => {
  switch (priority) {
    case 'critical':
      return 'bg-red-50 text-red-700 border-red-100';
    case 'high':
      return 'bg-orange-50 text-orange-700 border-orange-100';
    case 'normal':
      return 'bg-blue-50 text-blue-700 border-blue-100';
    default:
      return 'bg-gray-50 text-gray-700 border-gray-100';
  }
};

const capitalize = (s) => {
  if (typeof s !== 'string') return '';
  return s.charAt(0).toUpperCase() + s.slice(1);
};

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

const formatDateShort = (dateString) => {
  const options = { month: 'short', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

const stripHtml = (html) => {
   let tmp = document.createElement("DIV");
   tmp.innerHTML = html;
   return tmp.textContent || tmp.innerText || "";
};

onMounted(() => {
  fetchAnnouncements();
});
</script>
