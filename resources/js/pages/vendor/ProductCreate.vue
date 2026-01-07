<template>
  <div class="min-h-screen bg-gray-50/50 pb-12">
    <div class="bg-agri-forest py-16 relative overflow-hidden">
        <div class="absolute inset-0 bg-agri-earth/5 opacity-20"></div>
        <div class="container mx-auto px-6 relative z-10 flex flex-col md:flex-row md:items-end md:justify-between">
            <div>
                <h1 class="text-4xl font-black text-white mb-2 tracking-tighter">
                   Acquisition <span class="text-agri-earth">Listing</span>
                </h1>
                <p class="text-agri-earth/70 font-medium">Register a new commodity asset for global marketplace distribution.</p>
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
      <div class="max-w-4xl mx-auto bg-white rounded-[2.5rem] shadow-2xl border border-gray-100 overflow-hidden">
        <form @submit.prevent="handleSubmit" class="p-10 md:p-16 space-y-12">
          <div v-if="error" class="bg-red-50 border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-xl font-bold text-sm animate-shake">
            {{ error }}
          </div>

          <!-- Section: Trade Basics -->
          <div class="space-y-8">
            <h2 class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-4">Asset Specification</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="md:col-span-2 group">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 group-focus-within:text-agri-forest transition-colors">Commodity Title</label>
                    <input v-model="form.name" type="text" required class="w-full px-6 py-4 bg-gray-50 border border-transparent rounded-2xl focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest transition-all outline-none font-black text-gray-900" placeholder="e.g., Premium Grade-A White Maize">
                </div>

                <div class="group">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 group-focus-within:text-agri-forest transition-colors">Category Classification</label>
                    <select v-model="form.product_category_id" required class="w-full px-6 py-4 bg-gray-50 border border-transparent rounded-2xl focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest transition-all outline-none font-bold text-gray-900 appearance-none">
                        <option value="" disabled>Select Sector</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                </div>

                <div class="group">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 group-focus-within:text-agri-forest transition-colors">Origin/Location</label>
                    <input v-model="form.location" type="text" required class="w-full px-6 py-4 bg-gray-50 border border-transparent rounded-2xl focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest transition-all outline-none font-bold text-gray-900" placeholder="e.g., Nakuru, Kenya">
                </div>
            </div>
          </div>

          <!-- Section: Volume and Financials -->
          <div class="space-y-8">
            <h2 class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-4">Volume & Financials</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="group">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 group-focus-within:text-agri-forest transition-colors">Base Unit</label>
                    <input v-model="form.unit" type="text" required class="w-full px-6 py-4 bg-gray-50 border border-transparent rounded-2xl focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest transition-all outline-none font-bold text-gray-900" placeholder="e.g., Tons, Bags, Kg">
                </div>

                <div class="group">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 group-focus-within:text-agri-forest transition-colors">Batch Floor (MOQ)</label>
                    <input v-model="form.min_order" type="number" step="0.01" required class="w-full px-6 py-4 bg-gray-50 border border-transparent rounded-2xl focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest transition-all outline-none font-bold text-gray-900" placeholder="Minimum Quantity">
                </div>

                <div class="group">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 group-focus-within:text-agri-forest transition-colors">Entry Price ($)</label>
                    <input v-model="form.price" type="number" step="0.01" required class="w-full px-6 py-4 bg-gray-50 border border-transparent rounded-2xl focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest transition-all outline-none font-bold text-gray-900" placeholder="Per Unit Cost">
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
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Deploy Comprehensive Visual Assets</p>
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
                        Exhaustive visual documentation increases buyer confidence and reduces trade friction. We recommend at least <span class="font-black text-agri-forest">3 distinct angles</span> of the commodity.
                    </p>
                </div>
            </div>
          </div>

          <!-- Section: Qualitative Brief -->
          <div class="space-y-8">
            <h2 class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-4">Qualitative Brief</h2>
            
            <div class="group">
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 group-focus-within:text-agri-forest transition-colors">Asset Description (Technical Specs)</label>
                <textarea v-model="form.description" rows="6" required class="w-full px-6 py-4 bg-gray-50 border border-transparent rounded-2xl focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest transition-all outline-none font-medium text-sm text-gray-900 resize-none" placeholder="Provide detailed specifications, moisture content, certifications..."></textarea>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex flex-col sm:flex-row justify-end space-y-4 sm:space-y-0 sm:space-x-6 pt-10 border-t border-gray-50">
            <router-link to="/vendor/products" class="px-10 py-5 rounded-2xl bg-gray-100 text-gray-500 font-black uppercase text-xs tracking-widest hover:bg-gray-200 transition-all text-center">Abort Acquisition</router-link>
            <button type="submit" :disabled="loading" class="px-12 py-5 rounded-2xl bg-agri-forest text-white font-black uppercase text-xs tracking-widest hover:bg-agri-lush transition-all shadow-xl shadow-agri-forest/20 disabled:opacity-50">
                <span v-if="loading" class="flex items-center justify-center">
                    <svg class="animate-spin h-4 w-4 mr-3 text-white" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Executing...
                </span>
                <span v-else>Broadcast to Marketplace</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const categories = ref([]);
const loading = ref(false);
const error = ref('');
const imagesPreviews = ref([]);

const form = ref({
  name: '',
  product_category_id: '',
  unit: 'Tons',
  min_order: '',
  price: '',
  location: '',
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
    console.error('Error fetching categories:', err);
  }
};

const handleSubmit = async () => {
  loading.value = true;
  error.value = '';

  try {
    await axios.post('/api/products', form.value);
    if (window.notify) window.notify.success('Asset Broadcasted', `${form.value.name} is now live in the global marketplace.`);
    router.push('/vendor/products');
  } catch (err) {
    error.value = err.response?.data?.message || 'Transaction failed. Please verify all technical specifications.';
    if (window.notify) window.notify.error('Broadcast Error', error.value);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchCategories();
});
</script>

<style scoped>
.tracking-tighter {
  letter-spacing: -2px;
}
@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-5px); }
  75% { transform: translateX(5px); }
}
.animate-shake {
  animation: shake 0.3s ease-in-out;
}
</style>
