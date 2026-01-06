<template>
  <div class="min-h-screen bg-gray-50/50 pb-12">
    <div class="bg-agri-forest py-16 relative overflow-hidden">
        <div class="absolute inset-0 bg-agri-earth/5 opacity-20"></div>
        <div class="container mx-auto px-6 relative z-10 flex flex-col md:flex-row md:items-end md:justify-between">
            <div>
                <h1 class="text-4xl font-black text-white mb-2 tracking-tighter">
                   Market <span class="text-agri-earth">Moderation</span>
                </h1>
                <p class="text-agri-earth/70 font-medium">Verify asset compliance and maintain global trade integrity.</p>
            </div>
            <div class="mt-6 md:mt-0">
                <select v-model="statusFilter" @change="fetchProducts" class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl px-6 py-3.5 text-white font-black uppercase text-[10px] tracking-widest outline-none cursor-pointer">
                    <option value="" class="text-gray-900">All Asset Statuses</option>
                    <option value="pending" class="text-gray-900">Pending Verification</option>
                    <option value="available" class="text-gray-900">Active Trade</option>
                    <option value="rejected" class="text-gray-900">Rejected / Compliance Issue</option>
                </select>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-6 -mt-8 relative z-20">
      <div v-if="loading" class="flex flex-col items-center justify-center py-24 bg-white rounded-[3rem] shadow-xl border border-gray-100">
        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-agri-forest"></div>
        <p class="mt-4 text-agri-forest font-black uppercase text-xs tracking-widest">Scanning Market Repositories...</p>
      </div>

      <div v-else class="grid grid-cols-1 gap-8">
        <div v-for="product in products" :key="product.id" class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 p-10 flex flex-col lg:flex-row lg:items-center justify-between group hover:border-agri-earth/30 transition-all duration-500">
          <div class="flex-1 lg:pr-12">
            <div class="flex items-center space-x-3 mb-4">
              <span class="px-3 py-1 rounded-full bg-agri-sand text-agri-forest text-[10px] font-black uppercase tracking-widest">
                {{ product.category }}
              </span>
              <span :class="getStatusClasses(product.status)" class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest inline-block shadow-sm">
                {{ product.status }}
              </span>
            </div>
            
            <h3 class="text-3xl font-black text-gray-900 mb-2 tracking-tight group-hover:text-agri-forest transition-colors">{{ product.name }}</h3>
            <div class="flex items-center text-gray-400 mb-6 space-x-2">
                <div class="w-6 h-6 rounded-lg bg-gray-50 flex items-center justify-center text-xs font-black text-gray-400">
                    {{ product.seller?.name?.charAt(0) }}
                </div>
                <span class="text-xs font-bold uppercase tracking-wide">Produced by: {{ product.seller?.name || 'Authorized Producer' }}</span>
            </div>
            
            <p class="text-gray-500 font-medium leading-relaxed mb-8 max-w-2xl">{{ product.description }}</p>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
              <div>
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Trade Price</span>
                <p class="text-sm font-black text-agri-forest">${{ product.price }}<span class="text-gray-400 font-bold">/{{ product.unit }}</span></p>
              </div>
              <div>
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Inventory</span>
                <p class="text-sm font-black text-gray-900">Total Asset Pool</p>
              </div>
              <div>
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Provenance</span>
                <p class="text-sm font-black text-gray-900 italic">{{ product.location }}</p>
              </div>
              <div>
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Batch Floor</span>
                <p class="text-sm font-black text-gray-900">{{ product.min_order }} {{ product.unit }}</p>
              </div>
            </div>
          </div>

          <div v-if="product.status === 'pending'" class="mt-10 lg:mt-0 lg:w-64 flex flex-col space-y-4">
            <button @click="approveProduct(product)" class="w-full bg-agri-forest text-white py-4 rounded-2xl font-black uppercase text-xs tracking-widest hover:bg-agri-lush transition-all shadow-xl shadow-agri-forest/20">
              Authorize Listing
            </button>
            <button @click="rejectProduct(product)" class="w-full bg-red-50 text-red-600 py-4 rounded-2xl font-black uppercase text-xs tracking-widest hover:bg-red-100 transition-all border border-red-100">
              Refuse/Revoke
            </button>
          </div>
        </div>

        <div v-if="products.length === 0" class="text-center py-24 bg-white rounded-[3rem] shadow-xl border border-gray-100">
          <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-300">
             <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
             </svg>
          </div>
          <p class="text-gray-400 font-black uppercase tracking-widest">Ledger Clear: No assets awaiting moderation</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const products = ref([]);
const loading = ref(true);
const statusFilter = ref('pending');

const fetchProducts = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/admin/products', {
      params: { status: statusFilter.value }
    });
    products.value = response.data.data || response.data;
  } catch (error) {
    console.error('Error fetching products:', error);
  } finally {
    loading.value = false;
  }
};

const approveProduct = async (product) => {
  if (confirm(`Authorize commodity listing: ${product.name}?`)) {
    try {
      await axios.post(`/api/admin/products/${product.id}/approve`);
      if (window.notify) window.notify.success('Authorization Granted', `${product.name} is now live.`);
      fetchProducts();
    } catch (error) {
      if (window.notify) window.notify.error('Ledger Error', 'Authorization failed.');
    }
  }
};

const rejectProduct = async (product) => {
  if (confirm(`Refuse commodity listing: ${product.name}? This will purge the record.`)) {
    try {
      await axios.post(`/api/admin/products/${product.id}/reject`);
      if (window.notify) window.notify.success('Listing Refused', 'Compliance report generated.');
      fetchProducts();
    } catch (error) {
       if (window.notify) window.notify.error('Ledger Error', 'Rejection protocol failed.');
    }
  }
};

const getStatusClasses = (status) => {
    switch (status) {
        case 'pending': return 'bg-yellow-50 text-yellow-600 border border-yellow-100';
        case 'available': return 'bg-agri-lush text-white';
        case 'rejected': return 'bg-red-50 text-red-600 border border-red-100';
        default: return 'bg-gray-50 text-gray-500';
    }
};

onMounted(() => {
  fetchProducts();
});
</script>

<style scoped>
.tracking-tighter {
  letter-spacing: -2px;
}
</style>
