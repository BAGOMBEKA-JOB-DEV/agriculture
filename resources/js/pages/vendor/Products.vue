<template>
  <div class="min-h-screen bg-gray-50/50 pb-12">
    <div class="bg-agri-forest py-16 relative overflow-hidden">
        <div class="absolute inset-0 bg-agri-earth/5 opacity-20"></div>
        <div class="container mx-auto px-6 relative z-10 flex flex-col md:flex-row md:items-end md:justify-between">
            <div>
                <h1 class="text-4xl font-black text-white mb-2 tracking-tighter">
                   Inventory <span class="text-agri-earth">Control</span>
                </h1>
                <p class="text-agri-earth/70 font-medium">Manage your active commodity listings and global market presence.</p>
            </div>
            <div class="mt-6 md:mt-0">
                <router-link to="/vendor/products/create" class="bg-agri-earth text-white px-8 py-3.5 rounded-2xl font-black hover:bg-agri-sand hover:text-agri-forest transition-all shadow-xl shadow-agri-earth/20 flex items-center">
                   <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                   </svg>
                   New Acquisition Listing
                </router-link>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-6 -mt-8 relative z-20">
      <div class="bg-white rounded-[2.5rem] shadow-xl shadow-agri-forest/5 border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead>
              <tr class="bg-gray-50/50 border-b border-gray-100">
                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Commodity Name</th>
                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Category</th>
                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Price / Unit</th>
                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Integrity Status</th>
                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Operations</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="product in products" :key="product.id" class="group hover:bg-agri-sand/10 transition-colors duration-300">
                <td class="px-8 py-6">
                  <div class="flex items-center">
                    <div class="w-10 h-10 rounded-xl bg-agri-sand flex items-center justify-center text-agri-forest font-black mr-4 group-hover:bg-agri-forest group-hover:text-white transition-all">
                        {{ product.name?.charAt(0) || 'P' }}
                    </div>
                    <div>
                        <div class="text-sm font-black text-gray-900 group-hover:text-agri-forest transition-colors">{{ product.name }}</div>
                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-tight mt-0.5">{{ product.location || 'Distributed Region' }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-8 py-6">
                  <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-500 text-[10px] font-black uppercase tracking-widest">
                    {{ product.category }}
                  </span>
                </td>
                <td class="px-8 py-6 text-center">
                  <div class="text-sm font-black text-agri-forest" v-if="product.price">
                    ${{ product.price }}<span class="text-gray-400 text-[10px] font-bold">/{{ product.unit }}</span>
                  </div>
                  <div class="text-xs text-gray-400 italic font-medium" v-else>Negotiable</div>
                </td>
                <td class="px-8 py-6 text-center">
                  <span :class="product.status === 'available' ? 'bg-agri-lush text-white' : 'bg-yellow-50 text-yellow-600 border border-yellow-100'" class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest inline-block shadow-sm">
                    {{ product.status }}
                  </span>
                </td>
                <td class="px-8 py-6 text-right">
                    <div class="flex justify-end space-x-3">
                        <router-link 
                            :to="`/vendor/products/${product.id}/edit`" 
                            class="p-2 text-gray-400 hover:text-agri-forest hover:bg-agri-sand/50 rounded-lg transition-all"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </router-link>
                        <button 
                            @click="confirmDelete(product)" 
                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v2m3 4h.01"/>
                            </svg>
                        </button>
                    </div>
                </td>
              </tr>
              
              <tr v-if="products.length === 0">
                  <td colspan="5" class="px-8 py-20 text-center">
                       <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-300">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                       </div>
                       <p class="text-gray-900 font-black text-xl mb-2">Portfolio Empty</p>
                       <p class="text-gray-500 font-medium">Add your first product to start reaching global buyers.</p>
                  </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const products = ref([]);

const fetchProducts = async () => {
  try {
    const response = await axios.get('/api/products?own=1');
    products.value = response.data;
  } catch (error) {
    console.error('Error fetching products:', error);
  }
};

const confirmDelete = async (product) => {
  if (confirm(`Are you sure you want to permanently remove ${product.name} from the global marketplace?`)) {
    try {
      await axios.delete(`/api/products/${product.id}`);
      products.value = products.value.filter(p => p.id !== product.id);
      if (window.notify) window.notify.success('Asset Removed', `${product.name} has been purged from the ledger.`);
    } catch (error) {
      console.error('Error deleting product:', error);
      if (window.notify) window.notify.error('Purge Failed', 'Connectivity interference or authorization error.');
    }
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
