<template>
  <div class="min-h-screen bg-gray-50/50 pb-12">
    <div class="bg-agri-forest py-16 relative overflow-hidden">
        <div class="absolute inset-0 bg-agri-earth/5 opacity-20"></div>
        <div class="container mx-auto px-6 relative z-10 flex flex-col md:flex-row md:items-end md:justify-between">
            <div>
                <h1 class="text-4xl font-black text-white mb-2 tracking-tighter">
                   Procurement <span class="text-agri-earth">Ledger</span>
                </h1>
                <p class="text-agri-earth/70 font-medium">Track your active negotiations and global sourcing requests.</p>
            </div>
            <div class="mt-6 md:mt-0">
                <button @click="showCreateModal = true" class="bg-agri-earth text-white px-8 py-3.5 rounded-2xl font-black hover:bg-agri-sand hover:text-agri-forest transition-all shadow-xl shadow-agri-earth/20 flex items-center">
                   <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                   </svg>
                   Initiate New RFQ
                </button>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-6 -mt-8 relative z-20">
      <div class="bg-white rounded-[2.5rem] shadow-xl shadow-agri-forest/5 border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead>
              <tr class="bg-gray-50/50 border-b border-gray-100">
                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Trade Specification</th>
                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Counterparty</th>
                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Volume</th>
                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Deadline</th>
                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Integrity Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="rfq in rfqs" :key="rfq.id" class="group hover:bg-agri-sand/10 transition-colors duration-300">
                <td class="px-8 py-6">
                  <div class="flex items-center">
                    <div class="w-10 h-10 rounded-xl bg-agri-sand flex items-center justify-center text-agri-forest font-black mr-4 group-hover:bg-agri-forest group-hover:text-white transition-all">
                        {{ rfq.product?.name?.charAt(0) || 'R' }}
                    </div>
                    <div>
                        <div class="text-sm font-black text-gray-900 group-hover:text-agri-forest transition-colors">{{ rfq.product?.name || 'Bulk Commodity' }}</div>
                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-tight mt-0.5">ID: #RFQ-{{ rfq.id }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-8 py-6">
                  <div class="text-sm font-bold text-gray-600">{{ rfq.seller?.name || 'Verified Vendor' }}</div>
                </td>
                <td class="px-8 py-6 text-center">
                  <div class="text-sm font-black text-agri-forest">{{ rfq.quantity }} {{ rfq.product?.unit || 'Units' }}</div>
                </td>
                <td class="px-8 py-6 text-center">
                  <div class="text-sm font-medium text-gray-500">{{ formatDate(rfq.preferred_date) }}</div>
                </td>
                <td class="px-8 py-6 text-right">
                  <span :class="getStatusClasses(rfq.status)" class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest inline-block shadow-sm">
                    {{ rfq.status }}
                  </span>
                </td>
              </tr>
              
              <tr v-if="rfqs.length === 0 && !loading">
                  <td colspan="5" class="px-8 py-20 text-center">
                       <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-300">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                       </div>
                       <p class="text-gray-900 font-black text-xl mb-2">No Active RFQs</p>
                       <p class="text-gray-500 font-medium">Initiate a request from the marketplace to start trading.</p>
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

const rfqs = ref([]);
const loading = ref(true);
const showCreateModal = ref(false);

const fetchRfqs = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/rfqs');
    rfqs.value = response.data;
    
    // Fallback Mock Data for UI demonstration
    if (rfqs.value.length === 0) {
        rfqs.value = [
            { 
                id: 1042, 
                product: { name: 'White Maize (Grade A)', unit: 'Tons' }, 
                seller: { name: 'Nakuru Harvest' }, 
                quantity: 100, 
                preferred_date: '2026-03-15', 
                status: 'pending' 
            },
            { 
                id: 1038, 
                product: { name: 'Raw Cashew Nuts', unit: 'Tons' }, 
                seller: { name: 'Coastal Traders' }, 
                quantity: 25, 
                preferred_date: '2026-02-28', 
                status: 'responded' 
            },
            { 
                id: 1024, 
                product: { name: 'Organic Soybeans', unit: 'Tons' }, 
                seller: { name: 'Mekong Agri' }, 
                quantity: 50, 
                preferred_date: '2026-01-20', 
                status: 'completed' 
            }
        ];
    }
  } catch (error) {
    console.error('Failed to fetch RFQs:', error);
  } finally {
    loading.value = false;
  }
};

const formatDate = (dateString) => {
    if (!dateString) return 'TBA';
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
};

const getStatusClasses = (status) => {
    switch (status) {
        case 'pending': return 'bg-yellow-50 text-yellow-600 border border-yellow-100';
        case 'responded': return 'bg-blue-50 text-blue-600 border border-blue-100';
        case 'completed': return 'bg-agri-lush text-white';
        case 'rejected': return 'bg-red-50 text-red-600 border border-red-100';
        default: return 'bg-gray-50 text-gray-500';
    }
};

onMounted(() => {
  fetchRfqs();
});
</script>

<style scoped>
.tracking-tighter {
  letter-spacing: -2px;
}
</style>
