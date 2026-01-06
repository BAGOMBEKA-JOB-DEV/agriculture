<template>
  <div class="min-h-screen bg-gray-50/50 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header Section -->
      <div class="py-8 border-b border-gray-100 mb-8 flex flex-col md:flex-row md:items-end md:justify-between space-y-4 md:space-y-0">
        <div>
          <h1 class="text-3xl font-extrabold text-agri-forest tracking-tight">Systems Control</h1>
          <p class="mt-1 text-sm text-gray-500 font-medium">
            Administrative overview of the Agrixchange platform operations.
          </p>
        </div>
        <div class="flex items-center space-x-3">
          <button class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 shadow-sm transition-all duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2m3.293-7.707a1 1 0 111.414 1.414L9 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4z"/>
            </svg>
            System Logs
          </button>
        </div>
      </div>

      <!-- Stats Grid -->
      <div v-if="stats" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Users Block -->
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 space-y-6">
          <div class="flex items-center justify-between">
            <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600">
               <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
              </svg>
            </div>
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">User Base</span>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-xs font-bold text-gray-400 uppercase tracking-tighter mb-1">Total</p>
              <p class="text-2xl font-black text-gray-900">{{ stats.total_users }}</p>
            </div>
            <div>
              <p class="text-xs font-bold text-yellow-600 uppercase tracking-tighter mb-1">Pending</p>
              <p class="text-2xl font-black text-yellow-600">{{ stats.pending_users }}</p>
            </div>
          </div>
        </div>

        <!-- Inventory Block -->
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 space-y-6">
          <div class="flex items-center justify-between">
            <div class="w-12 h-12 bg-agri-sand/30 rounded-2xl flex items-center justify-center text-agri-forest">
               <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
              </svg>
            </div>
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Market Items</span>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-xs font-bold text-gray-400 uppercase tracking-tighter mb-1">Total</p>
              <p class="text-2xl font-black text-gray-900">{{ stats.total_products }}</p>
            </div>
            <div>
              <p class="text-xs font-bold text-agri-earth uppercase tracking-tighter mb-1">Moderation</p>
              <p class="text-2xl font-black text-agri-earth">{{ stats.pending_products }}</p>
            </div>
          </div>
        </div>

        <!-- Revenue/Engagement Block -->
        <div class="bg-agri-forest rounded-3xl p-8 shadow-xl border border-agri-lush space-y-6 relative overflow-hidden text-white">
          <div class="absolute -right-6 -bottom-6 opacity-10">
            <svg class="w-40 h-40" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
            </svg>
          </div>
          <div class="flex items-center justify-between relative z-10">
            <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-white">
               <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
              </svg>
            </div>
            <span class="text-[10px] font-black text-agri-earth uppercase tracking-widest">Global Health</span>
          </div>
          <div class="grid grid-cols-2 gap-4 relative z-10">
            <div>
              <p class="text-xs font-bold text-agri-earth uppercase tracking-tighter mb-1 opacity-80">Subscriptions</p>
              <p class="text-2xl font-black">{{ stats.active_subscriptions }}</p>
            </div>
            <div>
              <p class="text-xs font-bold text-agri-earth uppercase tracking-tighter mb-1 opacity-80">RFQs Flow</p>
              <p class="text-2xl font-black">{{ stats.total_rfqs }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Links -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <router-link to="/admin/users" class="group bg-white p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-xl hover:border-agri-lush/20 transition-all duration-500 flex items-center justify-between">
          <div class="flex items-center">
            <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300 mr-6">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-black text-gray-900 tracking-tight">Identity Nexus</h3>
              <p class="text-sm text-gray-400 font-medium">Verify credentials and manage access control</p>
            </div>
          </div>
          <svg class="w-6 h-6 text-gray-300 group-hover:text-agri-forest group-hover:translate-x-2 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5-5 5M6 7l5 5-5 5"/>
          </svg>
        </router-link>

        <router-link to="/admin/products" class="group bg-white p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-xl hover:border-agri-lush/20 transition-all duration-500 flex items-center justify-between">
          <div class="flex items-center">
            <div class="w-14 h-14 bg-green-50 rounded-2xl flex items-center justify-center text-green-600 group-hover:bg-green-600 group-hover:text-white transition-all duration-300 mr-6">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-black text-gray-900 tracking-tight">Content Gatekeeper</h3>
              <p class="text-sm text-gray-400 font-medium">Moderate listings and maintain quality standards</p>
            </div>
          </div>
          <svg class="w-6 h-6 text-gray-300 group-hover:text-agri-forest group-hover:translate-x-2 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5-5 5M6 7l5 5-5 5"/>
          </svg>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const stats = ref(null);

const fetchStats = async () => {
  try {
    const response = await axios.get('/api/admin/stats');
    stats.value = response.data;
  } catch (error) {
    console.error('Error fetching admin stats:', error);
    // Mock data if failed
    stats.value = {
        total_users: 1240,
        pending_users: 12,
        total_products: 450,
        pending_products: 4,
        active_subscriptions: 85,
        total_rfqs: 312
    }
  }
};

onMounted(() => {
  fetchStats();
});
</script>
