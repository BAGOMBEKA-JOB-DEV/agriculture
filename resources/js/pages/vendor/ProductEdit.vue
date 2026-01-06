<template>
  <div class="min-h-screen bg-gray-50/50 pb-12">
    <div class="bg-agri-forest py-16 relative overflow-hidden">
        <div class="absolute inset-0 bg-agri-earth/5 opacity-20"></div>
        <div class="container mx-auto px-6 relative z-10 flex flex-col md:flex-row md:items-end md:justify-between">
            <div>
                <h1 class="text-4xl font-black text-white mb-2 tracking-tighter">
                   Refine <span class="text-agri-earth">Asset</span>
                </h1>
                <p class="text-agri-earth/70 font-medium">Update specification and availability for your marketplace listing.</p>
            </div>
            <div class="mt-6 md:mt-0">
                <router-link to="/vendor/products" class="text-white opacity-60 hover:opacity-100 font-bold flex items-center transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Inventory
                </router-link>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-6 -mt-8 relative z-20">
      <div v-if="fetching" class="flex flex-col items-center justify-center py-24 bg-white rounded-[3rem] shadow-xl border border-gray-100">
        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-agri-forest"></div>
        <p class="mt-4 text-agri-forest font-black uppercase text-xs tracking-widest">Accessing Ledger Record...</p>
      </div>

      <div v-else class="max-w-4xl mx-auto bg-white rounded-[2.5rem] shadow-2xl border border-gray-100 overflow-hidden">
        <form @submit.prevent="handleSubmit" class="p-10 md:p-16 space-y-12">
          <div v-if="error" class="bg-red-50 border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-xl font-bold text-sm">
            {{ error }}
          </div>

          <!-- Section: Trade Basics -->
          <div class="space-y-8">
            <h2 class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-4">Asset Specification</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="md:col-span-2 group">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 group-focus-within:text-agri-forest transition-colors">Commodity Title</label>
                    <input v-model="form.name" type="text" required class="w-full px-6 py-4 bg-gray-50 border border-transparent rounded-2xl focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest transition-all outline-none font-black text-gray-900">
                </div>

                <div class="group">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 group-focus-within:text-agri-forest transition-colors">Category Classification</label>
                    <select v-model="form.category" required class="w-full px-6 py-4 bg-gray-50 border border-transparent rounded-2xl focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest transition-all outline-none font-bold text-gray-900 appearance-none">
                        <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                    </select>
                </div>

                <div class="group">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 group-focus-within:text-agri-forest transition-colors">Origin/Location</label>
                    <input v-model="form.location" type="text" required class="w-full px-6 py-4 bg-gray-50 border border-transparent rounded-2xl focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest transition-all outline-none font-bold text-gray-900">
                </div>
            </div>
          </div>

          <!-- Section: Volume and Financials -->
          <div class="space-y-8">
            <h2 class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-4">Volume & Financials</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="group">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 group-focus-within:text-agri-forest transition-colors">Base Unit</label>
                    <input v-model="form.unit" type="text" required class="w-full px-6 py-4 bg-gray-50 border border-transparent rounded-2xl focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest transition-all outline-none font-bold text-gray-900">
                </div>

                <div class="group">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 group-focus-within:text-agri-forest transition-colors">Batch Floor (MOQ)</label>
                    <input v-model="form.min_order" type="number" step="0.01" required class="w-full px-6 py-4 bg-gray-50 border border-transparent rounded-2xl focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest transition-all outline-none font-bold text-gray-900">
                </div>

                <div class="group">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 group-focus-within:text-agri-forest transition-colors">Entry Price ($)</label>
                    <input v-model="form.price" type="number" step="0.01" required class="w-full px-6 py-4 bg-gray-50 border border-transparent rounded-2xl focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest transition-all outline-none font-bold text-gray-900">
                </div>
            </div>
          </div>

          <!-- Section: Qualitative Brief -->
          <div class="space-y-8">
            <h2 class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-4">Qualitative Brief</h2>
            
            <div class="group">
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 group-focus-within:text-agri-forest transition-colors">Asset Description (Technical Specs)</label>
                <textarea v-model="form.description" rows="6" required class="w-full px-6 py-4 bg-gray-50 border border-transparent rounded-2xl focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest transition-all outline-none font-medium text-sm text-gray-900 resize-none"></textarea>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex flex-col sm:flex-row justify-end space-y-4 sm:space-y-0 sm:space-x-6 pt-10 border-t border-gray-50">
            <router-link to="/vendor/products" class="px-10 py-5 rounded-2xl bg-gray-100 text-gray-500 font-black uppercase text-xs tracking-widest hover:bg-gray-200 transition-all text-center">Abort Revision</router-link>
            <button type="submit" :disabled="loading" class="px-12 py-5 rounded-2xl bg-agri-forest text-white font-black uppercase text-xs tracking-widest hover:bg-agri-lush transition-all shadow-xl shadow-agri-forest/20 disabled:opacity-50">
                <span v-if="loading" class="flex items-center justify-center">
                    <svg class="animate-spin h-4 w-4 mr-3 text-white" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Synchronizing...
                </span>
                <span v-else>Update Marketplace Record</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const route = useRoute();
const categories = ref(['Grains', 'Fruits', 'Vegetables', 'Livestock', 'Coffee', 'Spices', 'Nuts']);
const loading = ref(false);
const fetching = ref(true);
const error = ref('');

const form = ref({
  name: '',
  category: '',
  unit: '',
  min_order: '',
  location: '',
  price: '',
  description: '',
  status: 'available'
});

const fetchProduct = async () => {
  fetching.value = true;
  try {
    const response = await axios.get(`/api/products/${route.params.id}`);
    const product = response.data;
    
    form.value = {
      name: product.name,
      category: product.category,
      unit: product.unit,
      min_order: product.min_order,
      location: product.location,
      price: product.price,
      description: product.description,
      status: product.status || 'available'
    };
  } catch (err) {
    error.value = 'Failed to load asset details from the ledger.';
  } finally {
    fetching.value = false;
  }
};

const handleSubmit = async () => {
  loading.value = true;
  error.value = '';

  try {
    await axios.put(`/api/products/${route.params.id}`, form.value);
    if (window.notify) window.notify.success('Asset Refined', 'The marketplace listing has been successfully updated.');
    router.push('/vendor/products');
  } catch (err) {
    error.value = err.response?.data?.message || 'Update failed. Connectivity or validation error.';
    if (window.notify) window.notify.error('Revision Refused', error.value);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchProduct();
});
</script>

<style scoped>
.tracking-tighter {
  letter-spacing: -2px;
}
</style>
