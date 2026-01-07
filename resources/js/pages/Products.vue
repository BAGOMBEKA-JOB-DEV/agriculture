<template>
  <div class="min-h-screen bg-gray-50/50 pb-24">
    <!-- Forest Header with Advanced Filters -->
    <div class="bg-agri-forest py-20 relative overflow-hidden">
        <div class="absolute inset-0 bg-agri-earth/5 opacity-20"></div>
        <div class="container mx-auto px-6 relative z-10">
            <h1 class="text-5xl font-black text-white mb-4 tracking-tighter">
               Market <span class="text-agri-earth">Discovery</span>
            </h1>
            <p class="text-agri-earth/70 font-medium max-w-xl">Browse the global trade catalog of verified agricultural assets and premium harvest commodities.</p>
            
            <div class="mt-12 flex flex-col lg:flex-row gap-6">
                <!-- Advanced Search -->
                <div class="flex-1 relative group">
                    <input 
                      v-model="filters.search"
                      @input="debouncedSearch"
                      type="text" 
                      placeholder="Search asset registry (e.g. 'Arabica Coffee', 'Yellow Maize')..." 
                      class="w-full bg-white/10 backdrop-blur-md border border-white/20 rounded-[1.5rem] px-14 py-5 text-white placeholder:text-white/40 font-medium focus:outline-none focus:ring-4 focus:ring-agri-earth/20 transition-all shadow-2xl"
                    >
                    <div class="absolute left-5 top-5 text-agri-earth">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                <!-- Category Nexus -->
                <div class="lg:w-72">
                    <select 
                      v-model="filters.category"
                      @change="fetchProducts"
                      class="w-full bg-white/10 backdrop-blur-md border border-white/20 rounded-[1.5rem] px-8 py-5 text-white font-black uppercase text-[10px] tracking-widest outline-none cursor-pointer hover:bg-white/20 transition-all appearance-none"
                    >
                      <option value="" class="text-gray-900">All Asset Categories</option>
                      <option v-for="cat in categories" :key="cat.id" :value="cat.id" class="text-gray-900">
                        {{ cat.name }}
                      </option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-6 -mt-10 relative z-20">
      <div v-if="loading" class="flex flex-col items-center justify-center py-32 bg-white rounded-[3rem] shadow-xl border border-gray-100">
        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-agri-forest"></div>
        <p class="mt-4 text-agri-forest font-black uppercase text-xs tracking-widest">Scanning Market Nodes...</p>
      </div>

      <div v-else-if="!products || products.length === 0" class="flex flex-col items-center justify-center py-32 bg-white rounded-[3rem] shadow-xl border border-gray-100">
         <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-8 text-gray-200">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
         </div>
         <p class="text-gray-900 font-black text-2xl mb-2 italic">Registry Empty</p>
         <p class="text-gray-500 font-medium italic">No trade assets found matching the specified protocol filters.</p>
         <button @click="resetFilters" class="mt-8 text-agri-forest font-black uppercase text-xs tracking-widest hover:text-agri-lush transition-colors">Reset Global Filters</button>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <router-link 
          v-for="product in products" 
          :key="product.id"
          :to="`/products/${product.id}`"
          class="bg-white rounded-[2.5rem] shadow-xl shadow-agri-forest/5 border border-gray-100 overflow-hidden group hover:-translate-y-2 transition-all duration-500"
        >
          <div class="h-64 bg-gray-100 relative overflow-hidden">
            <img v-if="product.images && product.images.length > 0" :src="product.images[0]" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
            <div v-else class="w-full h-full bg-agri-sand/30 flex items-center justify-center text-agri-forest text-4xl">
                 <svg class="w-16 h-16 opacity-20" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                 </svg>
            </div>
            <div class="absolute top-6 left-6 px-4 py-1.5 rounded-full bg-white/90 backdrop-blur-md shadow-sm text-[10px] font-black uppercase tracking-widest text-agri-forest">
               {{ product.category?.name || 'Trade Asset' }}
            </div>
          </div>
          
          <div class="p-8">
             <div class="flex justify-between items-start mb-4">
                <h3 class="text-xl font-black text-gray-900 leading-tight group-hover:text-agri-forest transition">{{ product.name }}</h3>
             </div>
             <p class="text-xs text-gray-400 font-bold uppercase tracking-tight mb-6 flex items-center">
                <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                {{ product.location }}
             </p>
             
             <div class="pt-6 border-t border-gray-50 flex items-center justify-between">
                <div>
                   <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Market Value</p>
                   <p class="text-lg font-black text-agri-forest">
                     <span v-if="product.price">${{ product.price }} <span class="text-xs text-gray-400 font-bold">/ {{ product.unit }}</span></span>
                     <span v-else class="italic text-agri-earth">Contact Vendor</span>
                   </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center text-agri-forest group-hover:bg-agri-forest group-hover:text-white transition-all shadow-sm">
                   <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                   </svg>
                </div>
             </div>
          </div>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const products = ref([]);
const categories = ref([]);
const loading = ref(true);
const filters = ref({
  search: '',
  category: ''
});

// Simple debounce implementation
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
    console.error('Error fetching categories:', error);
  }
};

const fetchProducts = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/products', { params: filters.value });
    // Robustly handle different response formats
    products.value = response.data.data || response.data || [];
  } catch (error) {
    console.error('Error fetching products:', error);
    products.value = [];
  } finally {
    loading.value = false;
  }
};

const resetFilters = () => {
    filters.value.search = '';
    filters.value.category = '';
    fetchProducts();
};

const debouncedSearch = debounce(() => {
  fetchProducts();
}, 500);

onMounted(() => {
  fetchCategories();
  fetchProducts();
});
</script>

<style scoped>
.tracking-tighter {
  letter-spacing: -2px;
}
</style>
