<template>
  <div class="min-h-screen bg-gray-50/50 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header Section -->
      <div class="py-8 border-b border-gray-100 mb-8 flex flex-col md:flex-row md:items-end md:justify-between space-y-4 md:space-y-0">
        <div>
          <h1 class="text-3xl font-extrabold text-agri-forest tracking-tight">Buyer Dashboard</h1>
          <p class="mt-1 text-sm text-gray-500 font-medium">
            Welcome back, <span class="text-agri-lush font-bold">{{ authStore.user?.name }}</span>. Manage your procurement activities here.
          </p>
        </div>
        <div class="flex items-center space-x-3">
          <router-link to="/products" class="inline-flex items-center px-4 py-2 bg-agri-forest text-white rounded-xl text-sm font-semibold hover:bg-agri-lush shadow-md transition-all duration-300">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            Browse Products
          </router-link>
        </div>
      </div>

      <!-- Subscription Alert -->
      <transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="transform -translate-y-4 opacity-0"
        enter-to-class="transform translate-y-0 opacity-100"
      >
        <div v-if="!authStore.hasActiveSubscription" class="bg-agri-sand/20 border border-agri-earth/30 rounded-2xl p-6 mb-8 flex flex-col sm:flex-row items-center justify-between">
          <div class="flex items-center mb-4 sm:mb-0">
            <div class="p-3 bg-agri-earth/20 rounded-xl text-agri-earth mr-4">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div>
              <p class="font-bold text-agri-forest">Subscription Required</p>
              <p class="text-xs text-gray-500 font-medium">Unlock full pricing and direct vendor communication.</p>
            </div>
          </div>
          <router-link to="/subscriptions" class="bg-agri-earth text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:shadow-lg transition-all">
            Upgrade Membership
          </router-link>
        </div>
      </transition>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 group">
          <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Active RFQs</p>
            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
            </div>
          </div>
          <h3 class="text-3xl font-black text-gray-900">{{ stats.activeRfqs }}</h3>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 group">
          <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Unread Messages</p>
            <div class="p-2 bg-green-50 text-green-600 rounded-lg group-hover:bg-green-600 group-hover:text-white transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
              </svg>
            </div>
          </div>
          <h3 class="text-3xl font-black text-gray-900">{{ stats.unreadMessages }}</h3>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 group">
          <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Saved Products</p>
            <div class="p-2 bg-purple-50 text-purple-600 rounded-lg group-hover:bg-purple-600 group-hover:text-white transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
              </svg>
            </div>
          </div>
          <h3 class="text-3xl font-black text-gray-900">{{ stats.savedProducts }}</h3>
        </div>
      </div>

      <!-- Quick Actions & Activity -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
          <h2 class="text-xl font-bold text-agri-forest mb-6 tracking-tight">Procurement Actions</h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <router-link to="/products" class="flex flex-col p-5 bg-gray-50/50 hover:bg-agri-sand/20 rounded-2xl border border-transparent hover:border-agri-earth/20 transition-all group">
              <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-agri-forest mb-4 group-hover:bg-agri-forest group-hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
              </div>
              <span class="font-bold text-gray-900">Explore Items</span>
              <span class="text-xs text-gray-500 mt-1">Browse active marketplace</span>
            </router-link>
            
            <router-link to="/buyer/rfqs" class="flex flex-col p-5 bg-gray-50/50 hover:bg-agri-sand/20 rounded-2xl border border-transparent hover:border-agri-earth/20 transition-all group">
              <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-blue-600 mb-4 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
              </div>
              <span class="font-bold text-gray-900">Request Quote</span>
              <span class="text-xs text-gray-500 mt-1">Submit new requirements</span>
            </router-link>
          </div>
        </div>

        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-agri-forest tracking-tight">Supply Intelligence</h2>
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Live Updates</span>
          </div>
          <div class="space-y-6">
            <div class="flex items-start group">
              <div class="w-10 h-10 bg-agri-sand/20 rounded-xl flex items-center justify-center text-agri-earth shrink-0 mr-4 transition-transform group-hover:scale-110">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                  <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                </svg>
              </div>
              <div class="flex-1 border-b border-gray-50 pb-4">
                <div class="flex justify-between items-start">
                    <p class="text-sm font-bold text-gray-900">Premium Grains offer received</p>
                    <span class="text-[10px] font-bold text-gray-400">12m ago</span>
                </div>
                <p class="text-xs text-gray-500 mt-1">Vendor 'AgriCorp' responded to your Maize RFQ.</p>
              </div>
            </div>
            <div class="flex items-start group">
              <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center text-green-600 shrink-0 mr-4 transition-transform group-hover:scale-110">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                </svg>
              </div>
              <div class="flex-1">
                <div class="flex justify-between items-start">
                    <p class="text-sm font-bold text-gray-900">New arrivals: Organic Fertilizer</p>
                    <span class="text-[10px] font-bold text-gray-400">2h ago</span>
                </div>
                <p class="text-xs text-gray-500 mt-1">Matching your saved categories: Soil health.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../../stores/auth';

const authStore = useAuthStore();
const stats = ref({
  activeRfqs: 0,
  unreadMessages: 0,
  savedProducts: 0,
});

onMounted(() => {
  // TODO: Fetch actual real stats from API
  stats.value = {
    activeRfqs: 5,
    unreadMessages: 2,
    savedProducts: 12,
  };
});
</script>
