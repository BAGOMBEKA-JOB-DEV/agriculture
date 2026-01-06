<template>
  <div class="min-h-screen bg-gray-50/50 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header Section -->
      <div class="py-8 border-b border-gray-100 mb-8 flex flex-col md:flex-row md:items-end md:justify-between space-y-4 md:space-y-0">
        <div>
          <h1 class="text-3xl font-extrabold text-agri-forest tracking-tight">
            Market Intelligence
          </h1>
          <p class="mt-1 text-sm text-gray-500 font-medium">
            Welcome back, <span class="text-agri-lush font-bold">{{ user?.name || 'User' }}</span>! Here's the latest data from the International Agro Marketplace.
          </p>
        </div>
        <div class="flex items-center space-x-3">
          <button class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 shadow-sm transition-all duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Market Report
          </button>
          <router-link to="/products" class="inline-flex items-center px-4 py-2 bg-agri-forest text-white rounded-xl text-sm font-semibold hover:bg-agri-lush shadow-md hover:shadow-lg transition-all duration-300">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            Browse Marketplace
          </router-link>
        </div>
      </div>

      <!-- Stats Cards Row -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Products -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300 group">
          <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-agri-sand/50 rounded-xl text-agri-forest group-hover:bg-agri-forest group-hover:text-white transition-colors duration-300">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
              </svg>
            </div>
            <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-lg">+42 new</span>
          </div>
          <div>
            <p class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-1">Global Listings</p>
            <h3 class="text-2xl font-black text-gray-900">{{ stats.products }}</h3>
          </div>
        </div>

        <!-- Active RFQs -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300 group">
          <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-blue-50 rounded-xl text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            <span class="text-xs font-bold text-gray-400 bg-gray-50 px-2 py-1 rounded-lg">High Demand</span>
          </div>
          <div>
            <p class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-1">Active RFQs</p>
            <h3 class="text-2xl font-black text-gray-900">{{ stats.rfqs }}</h3>
          </div>
        </div>

        <!-- Verified Sellers -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300 group">
          <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-orange-50 rounded-xl text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-colors duration-300">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
              </svg>
            </div>
            <span class="text-xs font-bold text-orange-600 bg-orange-50 px-2 py-1 rounded-lg">Trust Level</span>
          </div>
          <div>
            <p class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-1">Verified Partners</p>
            <h3 class="text-2xl font-black text-gray-900">{{ stats.sellers }}</h3>
          </div>
        </div>

        <!-- Market Health -->
        <div class="bg-agri-forest rounded-2xl p-6 shadow-xl border border-agri-lush relative overflow-hidden">
          <div class="absolute -right-4 -bottom-4 opacity-10">
            <svg class="w-32 h-32 text-white" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"></path>
            </svg>
          </div>
          <div class="relative z-10 text-white">
            <p class="text-xs font-bold text-agri-earth uppercase tracking-widest mb-1 opacity-80">Market Volatility</p>
            <h3 class="text-3xl font-black mb-2">Stable</h3>
            <div class="w-full bg-agri-lush/50 rounded-full h-1.5 mt-2">
              <div class="bg-agri-earth h-1.5 rounded-full" style="width: 85%"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Market Visualizations -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Category Distribution -->
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 flex flex-col h-full">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-900 tracking-tight">Supply Distribution by Category</h3>
          </div>
          <div class="flex-1 min-h-[300px]">
            <MarketStatusChart :products="allProducts" />
          </div>
        </div>

        <!-- Price Trends -->
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 flex flex-col h-full">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-900 tracking-tight">RFQ Activity Distribution</h3>
          </div>
          <div class="flex-1 min-h-[300px]">
            <RfqDistributionChart :rfqs="allRfqs" />
          </div>
        </div>
      </div>

      <div class="mb-8">
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900 tracking-tight">Market Presence Trends</h3>
            </div>
            <div class="w-full min-h-[350px]">
                <MarketTrendChart :products="allProducts" :rfqs="allRfqs" />
            </div>
        </div>
      </div>

      <!-- Recent Market Activity -->
      <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-white sticky top-0">
          <div>
            <h3 class="text-lg font-bold text-gray-900 tracking-tight">Recent Global Listings</h3>
            <p class="text-xs font-medium text-gray-400 mt-0.5">Real-time pulse of the agricultural marketplace</p>
          </div>
          <router-link to="/products" class="text-sm font-bold text-agri-forest hover:text-agri-lush transition-colors">
            View Marketplace
          </router-link>
        </div>
        
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead>
              <tr class="bg-gray-50/50">
                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Commodity</th>
                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-center">Batch Size</th>
                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-center">Price (Est.)</th>
                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Origin</th>
                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-right">Details</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="product in recentProducts" :key="product.id" class="group hover:bg-agri-sand/10 transition-colors duration-200">
                <td class="px-8 py-5">
                  <div class="flex items-center">
                    <div class="w-10 h-10 rounded-xl bg-agri-sand/30 flex items-center justify-center text-agri-forest font-black mr-4 group-hover:bg-agri-forest group-hover:text-white transition-all duration-300">
                      {{ product.name.charAt(0).toUpperCase() }}
                    </div>
                    <div>
                      <p class="text-sm font-bold text-gray-900 group-hover:text-agri-forest transition-colors">{{ product.name }}</p>
                      <p class="text-xs text-gray-400 font-medium truncate max-w-[200px]">{{ product.category }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-8 py-5 text-center">
                  <span class="text-xs font-bold text-gray-700">{{ product.min_order }} {{ product.unit }}</span>
                </td>
                <td class="px-8 py-5 text-center">
                  <span class="text-xs font-black text-agri-forest">${{ product.price || 'Negotiable' }}</span>
                </td>
                <td class="px-8 py-5">
                  <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-agri-earth"></span>
                    <span class="text-xs font-medium text-gray-600">{{ product.location || 'Local Region' }}</span>
                  </div>
                </td>
                <td class="px-8 py-5 text-right">
                  <router-link
                    :to="`/products/${product.id}`"
                    class="p-2 text-gray-400 hover:text-agri-forest hover:bg-agri-sand/50 rounded-lg inline-flex transition-all"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                  </router-link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="js">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import MarketStatusChart from './Charts/MarketStatusChart.vue'
import RfqDistributionChart from './Charts/RfqDistributionChart.vue'
import MarketTrendChart from './Charts/MarketTrendChart.vue'

const user = ref(null)
const allProducts = ref([])
const allRfqs = ref([])
const stats = ref({
  products: 0,
  rfqs: 0,
  sellers: 0,
})
const recentProducts = ref([])

const fetchDashboardData = async () => {
  try {
    const [userResponse, productsResponse, rfqsResponse] = await Promise.all([
      axios.get('/api/user'),
      axios.get('/api/products'),
      axios.get('/api/rfqs'),
    ])
    
    user.value = userResponse.data
    allProducts.value = productsResponse.data
    allRfqs.value = rfqsResponse.data
    
    recentProducts.value = allProducts.value.slice(0, 5)
    stats.value.products = allProducts.value.length
    stats.value.rfqs = allRfqs.value.length
    
    // Calculate unique sellers from products
    const uniqueSellers = new Set(allProducts.value.map(p => p.seller_id))
    stats.value.sellers = uniqueSellers.size
    
    // Fill with sample data if empty to show UI and help user visualize
    if (allProducts.value.length === 0) {
        recentProducts.value = [
          { id: 1, name: 'White Maize', category: 'Grains', min_order: 10, unit: 'Tons', price: 280, location: 'Nakuru, Kenya' },
          { id: 2, name: 'Grade A Arabica', category: 'Coffee', min_order: 50, unit: 'Bags', price: 450, location: 'Sidamo, Ethiopia' },
          { id: 3, name: 'Raw Cashew Nuts', category: 'Nuts', min_order: 5, unit: 'Tons', price: 1200, location: 'Mtwara, Tanzania' },
        ]
        stats.value.products = 1240
        stats.value.rfqs = 86
        stats.value.sellers = 45
    }
  } catch (error) {
    console.error('Failed to fetch dashboard data:', error)
  }
}

onMounted(() => {
  fetchDashboardData()
})
</script>

<style scoped>
.tracking-tight {
  letter-spacing: -0.025em;
}
</style>
