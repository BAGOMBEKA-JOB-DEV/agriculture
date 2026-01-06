<template>
  <div class="min-h-screen bg-gray-50/50 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header Section -->
      <div class="py-8 border-b border-gray-100 mb-8 flex flex-col md:flex-row md:items-end md:justify-between space-y-4 md:space-y-0">
        <div>
          <h1 class="text-3xl font-extrabold text-agri-forest tracking-tight">Vendor Dashboard</h1>
          <p class="mt-1 text-sm text-gray-500 font-medium">
            Welcome back, <span class="text-agri-lush font-bold">{{ authStore.user?.name }}</span>. Monitor your sales and inventory here.
          </p>
        </div>
        <div class="flex items-center space-x-3">
          <router-link to="/vendor/products/create" class="inline-flex items-center px-4 py-2 bg-agri-forest text-white rounded-xl text-sm font-semibold hover:bg-agri-lush shadow-md transition-all duration-300">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add New Product
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
              <p class="font-bold text-agri-forest">Listing Restricted</p>
              <p class="text-xs text-gray-500 font-medium">Upgrade your subscription to list more products and access market analytics.</p>
            </div>
          </div>
          <router-link to="/subscriptions" class="bg-agri-earth text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:shadow-lg transition-all">
            Manage Subscription
          </router-link>
        </div>
      </transition>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 group">
          <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Products</p>
            <div class="p-2 bg-green-50 text-green-600 rounded-lg group-hover:bg-green-600 group-hover:text-white transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
              </svg>
            </div>
          </div>
          <h3 class="text-3xl font-black text-gray-900">{{ stats.totalProducts }}</h3>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 group">
          <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Active Listings</p>
            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
          </div>
          <h3 class="text-3xl font-black text-gray-900">{{ stats.activeProducts }}</h3>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 group">
          <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Pending RFQs</p>
            <div class="p-2 bg-yellow-50 text-yellow-600 rounded-lg group-hover:bg-yellow-600 group-hover:text-white transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
          </div>
          <h3 class="text-3xl font-black text-gray-900">{{ stats.pendingRfqs }}</h3>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 group">
          <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Unread Messages</p>
            <div class="p-2 bg-purple-50 text-purple-600 rounded-lg group-hover:bg-purple-600 group-hover:text-white transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
            </div>
          </div>
          <h3 class="text-3xl font-black text-gray-900">{{ stats.unreadMessages }}</h3>
        </div>
      </div>

      <!-- Quick Actions & Recent Activity -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
          <h2 class="text-xl font-bold text-agri-forest mb-6 tracking-tight">Management Suite</h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <router-link to="/vendor/products/create" class="flex flex-col p-5 bg-gray-50/50 hover:bg-agri-sand/20 rounded-2xl border border-transparent hover:border-agri-earth/20 transition-all group">
              <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-agri-forest mb-4 group-hover:bg-agri-forest group-hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
              </div>
              <span class="font-bold text-gray-900">Add Product</span>
              <span class="text-xs text-gray-500 mt-1">List new inventory items</span>
            </router-link>

            <router-link to="/vendor/products" class="flex flex-col p-5 bg-gray-50/50 hover:bg-agri-sand/20 rounded-2xl border border-transparent hover:border-agri-earth/20 transition-all group">
              <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-blue-600 mb-4 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
              </div>
              <span class="font-bold text-gray-900">Manage Stock</span>
              <span class="text-xs text-gray-500 mt-1">Edit or remove listings</span>
            </router-link>
          </div>
        </div>

        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-agri-forest tracking-tight">Sales Activity</h2>
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Real-time Feed</span>
          </div>
          <div class="space-y-6">
            <div class="flex items-start group">
              <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center text-green-600 shrink-0 mr-4 transition-transform group-hover:scale-110">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
              </div>
              <div class="flex-1 border-b border-gray-50 pb-4">
                <div class="flex justify-between items-start">
                    <p class="text-sm font-bold text-gray-900">Premium Maize Listing Approved</p>
                    <span class="text-[10px] font-bold text-gray-400">2h ago</span>
                </div>
                <p class="text-xs text-gray-500 mt-1">Your product is now live on the marketplace.</p>
              </div>
            </div>
            <div class="flex items-start group">
              <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 shrink-0 mr-4 transition-transform group-hover:scale-110">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                  <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                </svg>
              </div>
              <div class="flex-1">
                <div class="flex justify-between items-start">
                    <p class="text-sm font-bold text-gray-900">New RFQ Inquiry</p>
                    <span class="text-[10px] font-bold text-gray-400">5h ago</span>
                </div>
                <p class="text-xs text-gray-500 mt-1">A buyer has requested a quote for Bulk Fertilizer.</p>
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
  totalProducts: 0,
  activeProducts: 0,
  pendingRfqs: 0,
  unreadMessages: 0,
});

const fetchVendorData = async () => {
  try {
    const [productsResponse, rfqsResponse, messagesResponse] = await Promise.all([
      axios.get('/api/products?own=1'),
      axios.get('/api/rfqs'),
      axios.get('/api/messages'),
    ]);

    const products = productsResponse.data;
    const rfqs = rfqsResponse.data;
    const messages = messagesResponse.data;

    stats.value = {
      totalProducts: products.length,
      activeProducts: products.filter(p => p.status === 'available').length,
      pendingRfqs: rfqs.filter(r => r.status === 'pending').length,
      unreadMessages: Object.values(messages).flat().filter(m => !m.is_read && m.receiver_id === authStore.user.id).length,
    };
  } catch (error) {
    console.error('Error fetching vendor data:', error);
  }
};

onMounted(() => {
  fetchVendorData();
});
</script>
