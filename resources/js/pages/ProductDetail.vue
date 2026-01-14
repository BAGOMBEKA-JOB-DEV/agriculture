<template>
  <div class="min-h-screen bg-gray-50/50 pb-24">
    <!-- Breadcrumb Layer -->
    <div class="bg-white border-b border-gray-100">
      <div class="max-w-7xl mx-auto px-6 py-4 flex items-center space-x-2 text-[10px] font-black uppercase tracking-widest text-gray-400">
        <router-link to="/products" class="hover:text-agri-forest transition">Global Exchange</router-link>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
        <span class="text-agri-forest">{{ product?.category?.name }}</span>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-900">{{ product?.name }}</span>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-12">
      <!-- Loading State -->
      <div v-if="loading" class="flex flex-col items-center justify-center py-32 bg-white rounded-[3rem] shadow-xl border border-gray-100">
        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-agri-forest"></div>
        <p class="mt-4 text-agri-forest font-black uppercase text-xs tracking-widest">Visualizing Asset Registry...</p>
      </div>

      <!-- Main Product View -->
      <template v-else-if="product">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
          <!-- Visual Discovery Column -->
          <div class="lg:col-span-7 space-y-8">
            <div class="bg-white rounded-[3rem] shadow-2xl shadow-agri-forest/5 border border-gray-100 overflow-hidden relative group">
              <div 
                class="aspect-[4/3] relative cursor-zoom-in overflow-hidden"
                @click="openLightbox(activeImageIndex)"
              >
                <img 
                  v-if="product.images && product.images.length > 0" 
                  :src="product.images[activeImageIndex]" 
                  class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                >
                <div v-else class="w-full h-full bg-agri-sand/30 flex items-center justify-center text-agri-forest/20">
                  <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
                </div>
                
                <div class="absolute bottom-8 right-8 px-6 py-3 bg-black/50 backdrop-blur-md rounded-2xl text-[10px] font-black uppercase tracking-widest text-white flex items-center">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                  </svg>
                  Inspect High-Fidelity Detail
                </div>
              </div>

              <!-- Thumbnail Ribbon -->
              <div v-if="product.images && product.images.length > 1" class="p-8 bg-gray-50/50 border-t border-gray-100 flex gap-4 overflow-x-auto no-scrollbar">
                <button 
                  v-for="(img, idx) in product.images" 
                  :key="idx"
                  @click="activeImageIndex = idx"
                  class="flex-shrink-0 w-24 h-24 rounded-[1.5rem] overflow-hidden border-2 transition-all relative group/thumb"
                  :class="activeImageIndex === idx ? 'border-agri-forest shadow-lg' : 'border-transparent opacity-60 hover:opacity-100'"
                >
                  <img :src="img" class="w-full h-full object-cover transition-transform group-hover/thumb:scale-110">
                  <div v-if="activeImageIndex === idx" class="absolute inset-0 bg-agri-forest/10 backdrop-blur-[1px]"></div>
                </button>
              </div>
            </div>

            <!-- Technical Specs Layer -->
            <div class="bg-white rounded-[3rem] p-10 shadow-xl shadow-agri-forest/5 border border-gray-100">
              <h2 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-8 flex items-center">
                <span class="w-8 h-px bg-gray-100 mr-4"></span>
                Technical Compliance Matrix
              </h2>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-sm">
                  <div class="flex justify-between py-4 border-b border-gray-50">
                      <span class="text-gray-400 font-bold uppercase text-[10px] tracking-tight">Origin Ledger</span>
                      <span class="text-gray-900 font-black">{{ product.location }}</span>
                  </div>
                  <div class="flex justify-between py-4 border-b border-gray-50">
                      <span class="text-gray-400 font-bold uppercase text-[10px] tracking-tight">Standardization Unit</span>
                      <span class="text-gray-900 font-black">{{ product.unit }}</span>
                  </div>
                  <div class="flex justify-between py-4 border-b border-gray-50">
                      <span class="text-gray-400 font-bold uppercase text-[10px] tracking-tight">Acquisition Floor</span>
                      <span class="text-gray-900 font-black">{{ product.min_order }} {{ product.unit }}</span>
                  </div>
                  <div class="flex justify-between py-4 border-b border-gray-50">
                      <span class="text-gray-400 font-bold uppercase text-[10px] tracking-tight">Asset Classification</span>
                      <span class="text-gray-900 font-black">{{ product.category?.name }}</span>
                  </div>
              </div>

              <div class="mt-12">
                 <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6">Qualitative Brief</h3>
                 <p class="text-gray-600 leading-relaxed font-medium italic border-l-4 border-agri-forest/20 pl-6 py-2">
                   "{{ product.description }}"
                 </p>
              </div>
            </div>
          </div>

          <!-- Trade Negotiation Column -->
          <div class="lg:col-span-5 space-y-8">
            <div class="bg-agri-forest rounded-[3rem] p-12 text-white shadow-2xl relative overflow-hidden group">
              <div class="absolute -right-20 -top-20 w-64 h-64 bg-agri-earth/10 rounded-full blur-[100px] group-hover:bg-agri-earth/20 transition-all duration-700"></div>
              
              <div class="relative z-10">
                <span class="inline-block px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md text-[10px] font-black uppercase tracking-widest text-agri-earth mb-8">
                  Official Marketplace Entry
                </span>
                
                <h1 class="text-4xl font-black mb-4 tracking-tighter leading-tight">{{ product.name }}</h1>
                
                <div class="flex items-baseline mb-12">
                  <span class="text-5xl font-black text-agri-earth">${{ product.price }}</span>
                  <span class="text-white/40 font-bold ml-2 uppercase text-xs tracking-widest">/ {{ product.unit }}</span>
                </div>

                <div class="space-y-4 mb-12">
                  <button 
                    @click="initiateDiscussion"
                    class="w-full bg-agri-earth text-agri-forest py-6 rounded-[1.5rem] font-black uppercase text-xs tracking-widest hover:bg-white hover:scale-[1.02] active:scale-[0.98] transition-all shadow-xl"
                  >
                    Initiate Trade Discussion
                  </button>
                  <!-- <button 
                    @click="showRfqModal = true"
                    class="w-full bg-white/10 backdrop-blur-md border border-white/20 text-white py-6 rounded-[1.5rem] font-black uppercase text-xs tracking-widest hover:bg-white/20 transition-all"
                  >
                    Issue Formal RFQ
                  </button> -->
                </div>

                <!-- Vendor Metadata -->
                <div class="flex items-center p-6 bg-white/5 rounded-[2rem] border border-white/5">
                  <div class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center text-agri-forest font-black text-xl shadow-lg overflow-hidden">
                    <img v-if="product.seller?.avatar_url" :src="product.seller.avatar_url" class="w-full h-full object-cover">
                    <span v-else>{{ product.seller?.name?.charAt(0) }}</span>
                  </div>
                  <div class="ml-5">
                    <p class="text-[10px] font-black text-agri-earth uppercase tracking-widest mb-1">Authorized Vendor</p>
                    <p class="text-lg font-black">{{ product.seller?.name }}</p>
                  </div>
                  <div class="ml-auto">
                      <div class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center">
                          <svg class="w-5 h-5 text-agri-lush" fill="currentColor" viewBox="0 0 20 20">
                              <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.64.304 1.24.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                          </svg>
                      </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Trade Capabilities -->
            <div class="bg-white rounded-[3rem] p-10 shadow-xl shadow-agri-forest/5 border border-gray-100">
               <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-8 flex items-center">
                 <span class="w-8 h-px bg-gray-100 mr-4"></span>
                 Trade Capabilities
               </h3>
               <ul class="space-y-6">
                  <li class="flex items-start">
                      <div class="w-8 h-8 rounded-xl bg-agri-forest/5 flex items-center justify-center text-agri-forest mr-4 flex-shrink-0">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                      </div>
                      <div>
                          <p class="text-sm font-black text-gray-900">Global Logistics Support</p>
                          <p class="text-[11px] text-gray-400 font-medium">FOB, CIF, and EXW fulfillment capabilities provided by vendor.</p>
                      </div>
                  </li>
                  <li class="flex items-start">
                      <div class="w-8 h-8 rounded-xl bg-agri-forest/5 flex items-center justify-center text-agri-forest mr-4 flex-shrink-0">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                      </div>
                      <div>
                          <p class="text-sm font-black text-gray-900">Verified Quality Standards</p>
                          <p class="text-[11px] text-gray-400 font-medium">Adheres to regional agricultural grade requirements and phytosanitary metrics.</p>
                      </div>
                  </li>
                  <li class="flex items-start">
                      <div class="w-8 h-8 rounded-xl bg-agri-forest/5 flex items-center justify-center text-agri-forest mr-4 flex-shrink-0">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                      </div>
                      <div>
                          <p class="text-sm font-black text-gray-900">Secure Trade Escrow</p>
                          <p class="text-[11px] text-gray-400 font-medium">Transactions secured via the AgriXchange financial protocol.</p>
                      </div>
                  </li>
               </ul>
            </div>
          </div>
        </div>

        <!-- Lightbox Modal (Reduced Offset for Aesthetic Balance) -->
        <div v-if="showLightbox" class="fixed inset-0 lg:left-12 z-[99999] bg-black/95 backdrop-blur-xl flex flex-col items-center justify-center p-8 transition-all duration-500 lg:rounded-l-[3rem] overflow-hidden">
            <button 
              @click.stop="showLightbox = false"
              class="absolute top-12 right-12 w-16 h-16 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white transition-all group z-[100000]"
            >
              <svg class="w-8 h-8 group-hover:rotate-90 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>

            <div class="w-full max-w-6xl relative group">
                <div class="aspect-video relative overflow-hidden rounded-[4rem] shadow-[0_0_100px_rgba(0,0,0,0.5)] border border-white/10">
                    <img :src="product.images[lightboxIndex]" class="w-full h-full object-contain">
                </div>

                <!-- Navigation Controls -->
                <button 
                  v-if="product.images.length > 1"
                  @click="prevImage"
                  class="absolute left-8 top-1/2 -translate-y-1/2 w-20 h-20 bg-white/10 hover:bg-white rounded-full flex items-center justify-center text-white hover:text-agri-forest transition-all shadow-2xl backdrop-blur-md"
                >
                  <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button 
                  v-if="product.images.length > 1"
                  @click="nextImage"
                  class="absolute right-8 top-1/2 -translate-y-1/2 w-20 h-20 bg-white/10 hover:bg-white rounded-full flex items-center justify-center text-white hover:text-agri-forest transition-all shadow-2xl backdrop-blur-md"
                >
                  <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            <!-- Progress Indicator -->
            <div class="mt-12 flex space-x-4">
                <div 
                  v-for="(_, idx) in product.images" 
                  :key="idx"
                  class="h-1.5 rounded-full transition-all duration-500"
                  :class="lightboxIndex === idx ? 'w-12 bg-agri-earth' : 'w-4 bg-white/20'"
                ></div>
            </div>
        </div>

        <!-- RFQ Modal (Reduced Offset for Aesthetic Balance) -->
        <div v-if="showRfqModal" class="fixed inset-0 lg:left-12 z-[99998] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
          <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showRfqModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
              <div class="bg-white px-8 pt-10 pb-8 sm:p-10 sm:pb-10">
                <h3 class="text-2xl font-black text-gray-900 mb-2" id="modal-title">
                  Request For Quote
                </h3>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-8">Formal Trade Acquisition Pipeline</p>
                
                <div class="mt-4 flex flex-col gap-6">
                  <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Quantity Needed</label>
                    <div class="flex">
                      <input v-model="rfqForm.quantity" type="number" class="w-2/3 bg-gray-50 border-none rounded-l-2xl px-6 py-4 focus:ring-4 focus:ring-agri-forest/5 font-medium">
                      <span class="w-1/3 flex items-center justify-center bg-agri-sand/30 rounded-r-2xl text-[10px] font-black uppercase text-agri-forest tracking-tighter">{{ product.unit }}</span>
                    </div>
                  </div>
                  <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Delivery Location</label>
                    <input v-model="rfqForm.delivery_location" type="text" class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 focus:ring-4 focus:ring-agri-forest/5 font-medium" placeholder="City, Country or Port">
                  </div>
                  <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Required By</label>
                    <input v-model="rfqForm.preferred_delivery_date" type="date" class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 focus:ring-4 focus:ring-agri-forest/5 font-medium">
                  </div>
                  <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Technical Requirements</label>
                    <textarea v-model="rfqForm.message" rows="3" class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 focus:ring-4 focus:ring-agri-forest/5 font-medium" placeholder="Moisture content, packaging specifics, etc."></textarea>
                  </div>
                </div>
              </div>
              <div class="bg-gray-50 px-8 py-6 sm:px-10 flex flex-row-reverse gap-4">
                <button @click="submitRfq" type="button" class="flex-1 bg-agri-forest text-white py-4 rounded-xl font-black uppercase text-[10px] tracking-widest shadow-lg hover:bg-agri-lush transition-all">
                  Submit Acquisition Request
                </button>
                <button @click="showRfqModal = false" type="button" class="flex-1 bg-white border border-gray-100 text-gray-400 py-4 rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-gray-100 transition-all">
                  Cancel
                </button>
              </div>
            </div>
          </div>
        </div>
      </template>

      <!-- Discovery Failed UI (Single instance) -->
      <div v-else class="text-center py-32 bg-white rounded-[3rem] shadow-xl border border-gray-100">
          <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-8 mx-auto text-gray-200">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012 2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
          </div>
          <p class="text-gray-900 font-black text-2xl mb-2 italic">Discovery Failed</p>
          <p class="text-gray-500 font-medium italic">The specified trade asset does not exist in the global registry.</p>
          <router-link to="/products" class="mt-8 inline-block text-agri-forest font-black uppercase text-xs tracking-widest hover:text-agri-lush transition-colors">Return to Marketplace</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const product = ref(null);
const loading = ref(true);
const activeImageIndex = ref(0);
const showLightbox = ref(false);
const lightboxIndex = ref(0);
const showRfqModal = ref(false);
const rfqForm = ref({
  quantity: '',
  delivery_location: '',
  preferred_delivery_date: '',
  message: ''
});

const fetchProduct = async () => {
  try {
    const response = await axios.get(`/api/products/${route.params.id}`);
    product.value = response.data;
  } catch (error) {
    console.error('Error fetching product:', error);
  } finally {
    loading.value = false;
  }
};

const initiateDiscussion = () => {
  const targetUrl = `/messages?userId=${product.value.seller_id}`;
  
  if (!authStore.isAuthenticated) {
    router.push(`/login?redirect=${encodeURIComponent(targetUrl)}`);
    return;
  }
  
  router.push(targetUrl);
};

const openLightbox = (index) => {
  lightboxIndex.value = index;
  showLightbox.value = true;
};

const nextImage = () => {
    lightboxIndex.value = (lightboxIndex.value + 1) % product.value.images.length;
};

const prevImage = () => {
    lightboxIndex.value = (lightboxIndex.value - 1 + product.value.images.length) % product.value.images.length;
};

const submitRfq = async () => {
  try {
    await axios.post('/api/rfqs', {
      ...rfqForm.value,
      product_id: product.value.id,
      seller_id: product.value.seller_id
    });
    showRfqModal.value = false;
    router.push('/buyer/rfqs');
    if (window.notify) window.notify.success('RFQ Submitted', 'Your trade request has been dispatched to the vendor.');
  } catch (error) {
    if (window.notify) window.notify.error('Transmission Failure', error.response?.data?.message || 'Failed to submit RFQ');
  }
};

onMounted(() => {
  fetchProduct();
});
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
