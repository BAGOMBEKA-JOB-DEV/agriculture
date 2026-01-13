<template>
  <div class="min-h-screen bg-gray-50/50 pb-12 font-sans">
    <!-- Header -->
    <div class="bg-agri-forest py-12 relative overflow-hidden shadow-xl">
        <div class="absolute inset-0 bg-agri-earth/5 opacity-20"></div>
        <div class="absolute -right-20 -top-20 w-96 h-96 bg-agri-lush/20 rounded-full blur-3xl"></div>
        <div class="container mx-auto px-6 relative z-10">
            <nav class="flex items-center text-sm font-medium text-white/60 mb-8 space-x-2">
                <router-link to="/dashboard" class="hover:text-white transition-colors">Dashboard</router-link>
                <span>/</span>
                <router-link to="/rfqs" class="hover:text-white transition-colors">My Requests</router-link>
                <span>/</span>
                <span class="text-white font-bold">Initiate RFQ</span>
            </nav>
            <h1 class="text-4xl font-black text-white mb-2 tracking-tighter">
               Initiate <span class="text-agri-earth">Trade Request</span>
            </h1>
            <p class="text-agri-earth/80 font-medium max-w-xl">Securely request quotes for commodities directly from verified producers.</p>
        </div>
    </div>

    <!-- Main Form -->
    <div class="container mx-auto px-6 -mt-10 relative z-20">
      <div class="bg-white rounded-[2rem] shadow-2xl shadow-agri-forest/10 border border-gray-100 overflow-hidden max-w-4xl mx-auto">
        
        <form @submit.prevent="submitRfq" class="p-8 md:p-12">
            
            <!-- Step 1: Asset Selection -->
            <div class="mb-12">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 rounded-xl bg-agri-forest text-white flex items-center justify-center font-black mr-4 shadow-lg shadow-agri-forest/20">1</div>
                    <div>
                        <h3 class="text-lg font-black text-gray-900 tracking-tight">Select Asset</h3>
                        <p class="text-xs text-gray-500 font-medium">Choose the community or product you wish to source.</p>
                    </div>
                </div>

                <div class="relative">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest px-1 mb-2">Target Product</label>
                    <select 
                        v-model="form.product_id" 
                        @change="onProductSelect"
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-agri-forest/10 focus:border-agri-forest outline-none transition-all font-bold text-gray-700 appearance-none"
                    >
                        <option value="" disabled>Select a product...</option>
                        <option v-for="product in products" :key="product.id" :value="product.id">
                            {{ product.name }} (Listed by {{ product.seller?.name }})
                        </option>
                    </select>
                    <div class="absolute right-6 top-[2.8rem] pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>

                <!-- Product Preview -->
                <transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0">
                    <div v-if="selectedProduct" class="mt-6 p-6 bg-agri-sand/20 rounded-2xl border border-agri-sand/50 flex items-start">
                        <div class="w-16 h-16 rounded-xl bg-white flex items-center justify-center text-2xl shadow-sm mr-5">
                            📦
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">{{ selectedProduct.name }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ selectedProduct.description }}</p>
                            <div class="mt-3 flex items-center space-x-4">
                                <span class="text-xs font-bold text-agri-forest bg-white px-3 py-1 rounded-lg border border-agri-forest/10">
                                    Unit: {{ selectedProduct.unit }}
                                </span>
                                <span class="text-xs font-bold text-agri-forest bg-white px-3 py-1 rounded-lg border border-agri-forest/10">
                                    Available: {{ selectedProduct.quantity || 'In Stock' }}
                                </span>
                                <span class="text-xs font-bold text-gray-500">
                                    Seller: {{ selectedProduct.seller?.name }}
                                </span>
                            </div>
                        </div>
                    </div>
                </transition>
            </div>

            <hr class="border-gray-100 mb-12" />

            <!-- Step 2: Trade Details -->
            <div class="mb-12" :class="{ 'opacity-50 pointer-events-none grayscale': !form.product_id }">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 rounded-xl bg-gray-100 text-gray-500 flex items-center justify-center font-black mr-4" :class="{ 'bg-agri-forest text-white shadow-lg shadow-agri-forest/20': form.product_id }">2</div>
                    <div>
                        <h3 class="text-lg font-black text-gray-900 tracking-tight">Trade Specs</h3>
                        <p class="text-xs text-gray-500 font-medium">Define your volume and delivery requirements.</p>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest px-1">Quantity Required</label>
                        <div class="relative">
                            <input 
                                v-model="form.quantity"
                                type="number" 
                                step="0.01"
                                class="w-full px-6 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-agri-forest/10 focus:border-agri-forest outline-none transition-all font-bold text-gray-700"
                                placeholder="0.00"
                            >
                            <div class="absolute right-6 top-1/2 -translate-y-1/2 text-xs font-black text-gray-400 uppercase">
                                {{ selectedProduct?.unit || 'UNITS' }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest px-1">Target Delivery</label>
                        <input 
                            v-model="form.preferred_date"
                            type="date" 
                            class="w-full px-6 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-agri-forest/10 focus:border-agri-forest outline-none transition-all font-bold text-gray-700"
                        >
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest px-1">Logistics & Delivery Point</label>
                        <input 
                            v-model="form.delivery_location"
                            type="text" 
                            class="w-full px-6 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-agri-forest/10 focus:border-agri-forest outline-none transition-all font-bold text-gray-700"
                            placeholder="e.g. Warehouse 4B, Industrial Area, Nairobi"
                        >
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest px-1">Additional Stipulations</label>
                        <textarea 
                            v-model="form.additional_notes"
                            rows="3"
                            class="w-full px-6 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-agri-forest/10 focus:border-agri-forest outline-none transition-all font-medium text-gray-700"
                            placeholder="Any specific quality requirements or packaging instructions..."
                        ></textarea>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                <router-link to="/rfqs" class="px-8 py-4 rounded-2xl font-bold text-gray-500 hover:bg-gray-50 transition-all">
                    Cancel
                </router-link>
                <button 
                    type="submit" 
                    :disabled="loading || !form.product_id"
                    class="px-10 py-4 bg-agri-forest text-white rounded-2xl font-black shadow-xl shadow-agri-forest/20 hover:bg-agri-lush hover:shadow-2xl hover:-translate-y-1 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                >
                    <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>{{ loading ? 'Processing...' : 'Submit Request' }}</span>
                </button>
            </div>

        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const loading = ref(false);
const products = ref([]);
const selectedProduct = ref(null);

const form = ref({
    product_id: '',
    quantity: '',
    delivery_location: '',
    preferred_date: '',
    additional_notes: ''
});

const fetchProducts = async () => {
    try {
        // Fetch all available products to select from
        // In a real app we might paginate or search, but for verification listing all is ok
        const response = await axios.get('/api/products');
        
        // Filter out own products if user is also a vendor
        const currentUserId = authStore.user?.id;
        products.value = response.data.filter(p => p.seller_id !== currentUserId);
        
    } catch (error) {
        console.error('Failed to fetch products:', error);
        if (window.notify) window.notify.error('Error', 'Could not load available assets.');
    }
};

const onProductSelect = () => {
    selectedProduct.value = products.value.find(p => p.id === form.value.product_id);
};

const submitRfq = async () => {
    if (!form.value.product_id) return;
    
    loading.value = true;
    try {
        await axios.post('/api/rfqs', form.value);
        if (window.notify) window.notify.success('Success', 'Trade Request Initiated Successfully');
        router.push('/rfqs');
    } catch (error) {
        console.error('RFQ Submit Error:', error);
        const msg = error.response?.data?.message || 'Failed to submit request';
        if (window.notify) window.notify.error('Submission Failed', msg);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchProducts();
});
</script>
