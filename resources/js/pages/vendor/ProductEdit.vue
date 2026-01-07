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
                    <select v-model="form.product_category_id" required class="w-full px-6 py-4 bg-gray-50 border border-transparent rounded-2xl focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest transition-all outline-none font-bold text-gray-900 appearance-none">
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
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

          <!-- Section: Media Portfolio -->
          <div class="space-y-8">
            <h2 class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-4">Asset Portfolio (Visual Documentation)</h2>
            
            <div class="space-y-6">
                <div 
                    @click="$refs.fileInput.click()"
                    class="w-full h-48 bg-gray-50 border-2 border-dashed border-gray-200 rounded-[2.5rem] flex flex-col items-center justify-center cursor-pointer hover:bg-agri-forest/5 hover:border-agri-forest transition-all group"
                >
                    <input 
                        type="file" 
                        ref="fileInput" 
                        @change="handleImageUpload" 
                        accept="image/*" 
                        multiple
                        class="hidden"
                    >
                    <div class="text-center p-8">
                        <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center mx-auto mb-4 text-gray-300 group-hover:text-agri-forest transition-colors">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Update Visual Records</p>
                        <p class="text-[8px] text-gray-400 mt-1 uppercase font-bold tracking-tight">Select multiple images to provide exhaustive detail</p>
                    </div>
                </div>

                <div v-if="imagesPreviews.length > 0" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    <div 
                        v-for="(preview, index) in imagesPreviews" 
                        :key="index" 
                        class="relative aspect-square group"
                    >
                        <img :src="preview" class="w-full h-full object-cover rounded-[1.5rem] shadow-sm border border-gray-100">
                        <button 
                            @click.stop="removeImage(index)" 
                            type="button"
                            class="absolute -top-2 -right-2 w-8 h-8 bg-white shadow-lg rounded-xl flex items-center justify-center text-red-500 hover:bg-red-50 transition-all border border-red-50 opacity-0 group-hover:opacity-100 z-10"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        <div v-if="index === 0" class="absolute bottom-2 left-2 px-2 py-0.5 bg-agri-forest/80 text-white text-[8px] font-black uppercase tracking-tighter rounded-md backdrop-blur-sm">
                            Primary
                        </div>
                    </div>
                </div>

                <div v-if="imagesPreviews.length === 0" class="bg-agri-forest/5 rounded-[2rem] p-8 flex items-center space-x-6">
                    <div class="w-12 h-12 rounded-full bg-agri-forest/10 flex items-center justify-center text-agri-forest">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"></path>
                        </svg>
                    </div>
                    <p class="text-[11px] text-agri-forest/70 font-medium leading-relaxed">
                        Refining your asset's visual representation maintains high trust scores within the global trade Nexus. Clear, accurate imagery reduces mediation risks.
                    </p>
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
const categories = ref([]);
const loading = ref(false);
const fetching = ref(true);
const error = ref('');
const imagesPreviews = ref([]);

const form = ref({
  name: '',
  product_category_id: '',
  unit: '',
  min_order: '',
  location: '',
  price: '',
  description: '',
  images: [],
  status: 'available'
});

const handleImageUpload = (event) => {
  const files = Array.from(event.target.files);
  if (!files.length) return;

  files.forEach(file => {
    if (file.size > 10 * 1024 * 1024) {
      if (window.notify) window.notify.error('File Oversized', `Asset ${file.name} exceeds bandwidth limits.`);
      return;
    }

    const reader = new FileReader();
    reader.onload = (e) => {
      imagesPreviews.value.push(e.target.result);
      form.value.images.push(e.target.result);
    };
    reader.readAsDataURL(file);
  });
};

const removeImage = (index) => {
  imagesPreviews.value.splice(index, 1);
  form.value.images.splice(index, 1);
};

const fetchCategories = async () => {
  try {
    const response = await axios.get('/api/categories');
    categories.value = response.data;
  } catch (err) {
    console.error('Failed to load categories');
  }
};

const fetchProduct = async () => {
  fetching.value = true;
  try {
    const response = await axios.get(`/api/products/${route.params.id}`);
    const product = response.data;
    
    form.value = {
      name: product.name,
      product_category_id: product.product_category_id,
      unit: product.unit,
      min_order: product.min_order,
      location: product.location,
      price: product.price,
      description: product.description,
      images: product.images || [],
      status: product.status || 'available'
    };
    if (product.images && product.images.length > 0) {
      imagesPreviews.value = [...product.images];
    }
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
  fetchCategories();
  fetchProduct();
});
</script>

<style scoped>
.tracking-tighter {
  letter-spacing: -2px;
}
</style>
