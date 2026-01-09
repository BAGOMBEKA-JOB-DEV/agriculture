<template>
  <!-- Global Navigation Protocol -->
  <Navigation />

  <div class="min-h-screen bg-[#F8FAFB] pb-24">
    <!-- Enterprise Marketplace Header -->
    <div class="bg-[#1A4D2E] pt-24 pb-32 relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10"></div>
        <div class="container mx-auto px-6 relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <span class="px-3 py-1 bg-[#EEF2E6] text-[#1A4D2E] text-[10px] font-black uppercase tracking-widest rounded-full">Global Trade Hub</span>
                        <div class="h-px w-12 bg-[#D4A373]"></div>
                        <span class="text-[#D4A373] text-[10px] font-bold uppercase tracking-widest">Live Inventory</span>
                    </div>
                    <h1 class="text-6xl font-black text-white mb-4 tracking-tighter leading-none">
                       Agricultural <br><span class="text-[#D4A373]">Exchange</span>
                    </h1>
                    <p class="text-white/60 font-medium max-w-xl text-lg">Access the world's most comprehensive registry of verified agricultural commodities and farm assets.</p>
                </div>
                
                <!-- Market Stats -->
                <div class="flex gap-12 border-l border-white/10 pl-12 h-20 items-center">
                    <div>
                        <p class="text-white font-black text-2xl">1,240+</p>
                        <p class="text-white/40 text-[10px] font-bold uppercase tracking-widest">Active Listings</p>
                    </div>
                    <div>
                        <p class="text-white font-black text-2xl">850</p>
                        <p class="text-white/40 text-[10px] font-bold uppercase tracking-widest">Verified Vendors</p>
                    </div>
                    <div>
                        <p class="text-white font-black text-2xl">$4.2M</p>
                        <p class="text-white/40 text-[10px] font-bold uppercase tracking-widest">Trade Volume</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Filter Protocol Bar -->
    <div class="container mx-auto px-6 -mt-16 relative z-20">
        <div class="bg-white rounded-[2rem] shadow-2xl p-4 lg:p-2 border border-gray-100 mb-12">
            <div class="flex flex-col lg:flex-row items-center gap-2">
                <!-- Search -->
                <div class="flex-1 w-full relative">
                    <input 
                      v-model="filters.search"
                      @input="debouncedSearch"
                      type="text" 
                      placeholder="Search commodities (e.g. Maize, Coffee)..." 
                      class="w-full bg-transparent px-12 py-5 text-gray-900 placeholder:text-gray-400 font-bold border-none focus:ring-0"
                    >
                    <svg class="absolute left-4 top-5 w-6 h-6 text-[#1A4D2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <div class="hidden lg:block h-10 w-px bg-gray-100"></div>

                <!-- Category -->
                <div class="w-full lg:w-64">
                    <select 
                      v-model="filters.category"
                      @change="fetchProducts"
                      class="w-full bg-transparent px-6 py-5 text-[#1A4D2E] font-black uppercase text-[11px] tracking-widest border-none focus:ring-0 cursor-pointer"
                    >
                      <option value="">All Categories</option>
                      <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                </div>

                <div class="hidden lg:block h-10 w-px bg-gray-100"></div>

                <!-- Price Range -->
                <div class="w-full lg:w-48 flex items-center px-4">
                    <input 
                      v-model="filters.min_price" 
                      @input="debouncedSearch"
                      type="number" 
                      placeholder="Min $" 
                      class="w-full bg-transparent py-5 text-center text-xs font-bold border-none focus:ring-0"
                    >
                    <span class="text-gray-300 font-bold">-</span>
                    <input 
                      v-model="filters.max_price" 
                      @input="debouncedSearch"
                      type="number" 
                      placeholder="Max $" 
                      class="w-full bg-transparent py-5 text-center text-xs font-bold border-none focus:ring-0"
                    >
                </div>

                <div class="hidden lg:block h-10 w-px bg-gray-100"></div>

                <!-- Location -->
                <div class="w-full lg:w-64 relative">
                    <input 
                      v-model="filters.location"
                      @input="debouncedSearch"
                      type="text" 
                      placeholder="Export Origin..." 
                      class="w-full bg-transparent px-10 py-5 text-right font-bold text-xs border-none focus:ring-0"
                    >
                    <svg class="absolute left-4 top-5 w-5 h-5 text-[#D4A373]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    </svg>
                </div>

                <!-- Reset -->
                <button 
                  @click="resetFilters"
                  class="bg-[#1A4D2E] text-white p-4 rounded-2xl hover:bg-[#D4A373] transition-all duration-300 shadow-lg"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Product Grid -->
        <div v-if="loading" class="flex flex-col items-center justify-center py-48 bg-white/50 rounded-[4rem] backdrop-blur-sm border border-white">
            <div class="animate-pulse relative">
                <div class="h-24 w-24 bg-[#1A4D2E]/10 rounded-full flex items-center justify-center">
                    <div class="h-16 w-16 border-4 border-t-[#D4A373] border-transparent rounded-full animate-spin"></div>
                </div>
            </div>
            <p class="mt-8 text-[#1A4D2E] font-black uppercase text-[10px] tracking-[0.3em] opacity-40">Decrypting Inventory Nodes...</p>
        </div>

        <div v-else-if="products.length === 0" class="text-center py-48 bg-white rounded-[4rem] shadow-sm border border-gray-100">
            <h3 class="text-2xl font-black text-gray-900 mb-2">No Matching Assets</h3>
            <p class="text-gray-500 font-medium italic">Adjust your verification protocols to see other commodities.</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
            <div 
              v-for="(product, index) in products" 
              :key="product.id"
              class="relative group"
            >
                <!-- Locked Paywall Overlay -->
                <div v-if="product.requires_subscription" class="absolute inset-0 z-40 backdrop-blur-md bg-white/20 rounded-[2.5rem] flex flex-col items-center justify-center p-8 text-center border-2 border-dashed border-[#D4A373]/30">
                    <div class="w-16 h-16 bg-[#1A4D2E] rounded-full flex items-center justify-center mb-6 shadow-2xl">
                        <svg class="w-8 h-8 text-[#D4A373]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h4 class="text-xl font-black text-[#1A4D2E] mb-2 leading-tight">Identity Verification Required</h4>
                    <p class="text-xs font-bold text-gray-500 mb-8 max-w-[180px]">Upgrade to a <span class="text-[#D4A373]">Trading Member</span> account to unlock full inventory details.</p>
                    <router-link to="/subscriptions" class="bg-[#1A4D2E] text-white px-8 py-3 rounded-full text-[10px] font-black uppercase tracking-widest hover:scale-105 transition-transform shadow-xl">Activate Membership</router-link>
                </div>

                <router-link 
                  :to="product.requires_subscription ? '/subscriptions' : `/marketplace/${product.id}`"
                  class="block bg-white rounded-[2.5rem] border border-gray-100 transition-all duration-700 h-full flex flex-col hover:shadow-2xl hover:shadow-[#1A4D2E]/5"
                  :class="{ 'opacity-50 grayscale pointer-events-none': product.requires_subscription }"
                >
                    <div class="h-64 bg-gray-100 rounded-t-[2.5rem] relative overflow-hidden">
                        <img 
                          v-if="product.images && product.images.length > 0" 
                          :src="product.images[0]" 
                          class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                        <div class="absolute bottom-6 left-6 flex space-x-2">
                             <span class="px-3 py-1 bg-white/90 backdrop-blur-sm rounded-full text-[9px] font-black text-[#1A4D2E] uppercase tracking-widest shadow-sm">
                                {{ product.category?.name || 'Unclassified' }}
                             </span>
                        </div>
                    </div>
                    
                    <div class="p-8 flex-1 flex flex-col">
                        <h3 class="text-xl font-black text-gray-900 group-hover:text-[#1A4D2E] transition-colors mb-2">{{ product.name }}</h3>
                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest flex items-center mb-10">
                            <svg class="w-3.5 h-3.5 mr-2 text-[#D4A373]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            {{ product.location || 'Global Logistics' }}
                        </p>

                        <div class="mt-auto pt-6 border-t border-gray-50 flex items-end justify-between">
                            <div>
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1.5 opacity-60">Listing Price</p>
                                <div class="text-2xl font-black text-[#1A4D2E] flex items-end">
                                    <span v-if="product.price">${{ product.price }}</span>
                                    <span v-else class="text-[#D4A373] text-sm uppercase">Locked</span>
                                    <span class="text-[10px] text-gray-400 ml-1.5 font-bold mb-1 opacity-50">/ {{ product.unit || 'MT' }}</span>
                                </div>
                            </div>
                            <div class="w-14 h-14 rounded-3xl bg-[#EEF2E6] flex items-center justify-center text-[#1A4D2E] group-hover:bg-[#1A4D2E] group-hover:text-white transition-all duration-500 shadow-sm transform group-hover:rotate-[360deg]">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </router-link>
            </div>
        </div>
    </div>
  </div>
  <Footer />
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Navigation from '../components/Navigation.vue';
import Footer from '../components/Footer.vue';

const products = ref([]);
const categories = ref([]);
const loading = ref(true);
const filters = ref({
  search: '',
  category: '',
  min_price: '',
  max_price: '',
  location: ''
});

const debounce = (fn, delay) => {
  let timeoutId;
  return (...args) => {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => fn(...args), delay);
  };
};

const fetchCategories = async () => {
    try {
        const response = await axios.get('/api/categories');
        categories.value = response.data;
    } catch (error) {
        console.error('Core protocol category sync error:', error);
    }
};

const fetchProducts = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/marketplace', { params: filters.value });
    products.value = response.data;
  } catch (error) {
    console.error('Trade inventory sync interrupted:', error);
    products.value = [];
  } finally {
    loading.value = false;
  }
};

const resetFilters = () => {
    filters.value = {
        search: '',
        category: '',
        min_price: '',
        max_price: '',
        location: ''
    };
    fetchProducts();
};

const debouncedSearch = debounce(() => {
  fetchProducts();
}, 400);

onMounted(() => {
  fetchCategories();
  fetchProducts();
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@100;400;700;900&display=swap');

* {
  font-family: 'Outfit', sans-serif;
}

.tracking-tighter {
  letter-spacing: -3px;
}

/* Custom Scrollbar for enterprise feel */
::-webkit-scrollbar {
  width: 8px;
}
::-webkit-scrollbar-track {
  background: #f1f1f1;
}
::-webkit-scrollbar-thumb {
  background: #1A4D2E;
  border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
  background: #D4A373;
}
</style>
