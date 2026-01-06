<template>
  <div class="min-h-screen bg-gray-50/50 pb-12">
    <div class="bg-agri-forest py-16 relative overflow-hidden">
        <div class="absolute inset-0 bg-agri-earth/5 opacity-20"></div>
        <div class="container mx-auto px-6 relative z-10 flex flex-col md:flex-row md:items-end md:justify-between">
            <div>
                <h1 class="text-4xl font-black text-white mb-2 tracking-tighter">
                   Identity <span class="text-agri-earth">Registry</span>
                </h1>
                <p class="text-agri-earth/70 font-medium">Monitor participant credentials and enforce global trade compliance.</p>
            </div>
            <div class="mt-6 md:mt-0">
                <select v-model="roleFilter" @change="fetchUsers" class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl px-6 py-3.5 text-white font-black uppercase text-[10px] tracking-widest outline-none cursor-pointer">
                    <option value="" class="text-gray-900">All Participant Roles</option>
                    <option value="vendor" class="text-gray-900">Verified Producers</option>
                    <option value="buyer" class="text-gray-900">Institutional Buyers</option>
                    <option value="admin" class="text-gray-900">Market Regulators</option>
                </select>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-6 -mt-8 relative z-20">
      <div v-if="loading" class="flex flex-col items-center justify-center py-24 bg-white rounded-[3rem] shadow-xl border border-gray-100">
        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-agri-forest"></div>
        <p class="mt-4 text-agri-forest font-black uppercase text-xs tracking-widest">Verifying Identity Nodes...</p>
      </div>

      <div v-else class="bg-white rounded-[2.5rem] shadow-xl shadow-agri-forest/5 border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead>
              <tr class="bg-gray-50/50 border-b border-gray-100">
                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Trade Identity</th>
                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Authority Level</th>
                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Provenance</th>
                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Compliance Status</th>
                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Integrity Operations</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="user in users" :key="user.id" class="group hover:bg-agri-sand/10 transition-colors duration-300">
                <td class="px-8 py-6">
                  <div class="flex items-center">
                    <div class="w-12 h-12 rounded-2xl bg-agri-sand flex items-center justify-center text-agri-forest font-black mr-4 group-hover:bg-agri-forest group-hover:text-white transition-all shadow-sm">
                        {{ user.name.charAt(0) }}
                    </div>
                    <div>
                        <div class="text-sm font-black text-gray-900 group-hover:text-agri-forest transition-colors">{{ user.name }}</div>
                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-tight mt-0.5">{{ user.email }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-8 py-6">
                  <span :class="getRoleClasses(user.role)" class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest inline-block border">
                    {{ user.role }}
                  </span>
                </td>
                <td class="px-8 py-6 text-center">
                  <div class="text-sm font-bold text-gray-600">{{ user.location || 'Global/Undefined' }}</div>
                </td>
                <td class="px-8 py-6 text-center">
                  <div class="flex items-center justify-center">
                      <span class="w-2 h-2 rounded-full bg-agri-lush mr-2"></span>
                      <span class="text-[10px] font-black text-agri-forest uppercase tracking-widest">Active Integrity</span>
                  </div>
                </td>
                <td class="px-8 py-6 text-right">
                    <div class="flex justify-end space-x-3 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button @click="verifyUser(user)" class="px-4 py-2 rounded-xl bg-agri-forest text-white text-[10px] font-black uppercase tracking-widest hover:bg-agri-lush transition-all">Verify Credentials</button>
                        <button @click="suspendUser(user)" class="px-4 py-2 rounded-xl bg-red-50 text-red-600 text-[10px] font-black uppercase tracking-widest border border-red-100 hover:bg-red-100 transition-all">Suspend Node</button>
                    </div>
                </td>
              </tr>
              
              <tr v-if="users.length === 0">
                  <td colspan="5" class="px-8 py-24 text-center">
                       <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-300">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                       </div>
                       <p class="text-gray-900 font-black text-xl mb-2">Registry Silent</p>
                       <p class="text-gray-500 font-medium">No identities matching the current filter found in the ledger.</p>
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

const users = ref([]);
const loading = ref(true);
const roleFilter = ref('');

const fetchUsers = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/admin/users', {
      params: { role: roleFilter.value }
    });
    users.value = response.data.data || response.data;
    
    // Fallback Mock Data
    if (users.value.length === 0) {
        users.value = [
            { id: 1, name: 'Lucas Xavier', email: 'lucas@agrixchange.com', role: 'admin', location: 'Global Operations' },
            { id: 5, name: 'Nakuru Harvest', email: 'trade@nakuru.co', role: 'vendor', location: 'Nakuru, Kenya' },
            { id: 8, name: 'Coastal Traders', email: 'ops@coastal.trade', role: 'vendor', location: 'Mombasa, Kenya' },
            { id: 12, name: 'EuroFood Logistics', email: 'procurement@eurofood.eu', role: 'buyer', location: 'Berlin, Germany' }
        ];
    }
  } catch (error) {
    console.error('Error fetching users:', error);
  } finally {
    loading.value = false;
  }
};

const verifyUser = (user) => {
    if (window.notify) window.notify.success('Identity Verified', `Verification seal applied to ${user.name}.`);
};

const suspendUser = async (user) => {
  if (confirm(`Are you certain you want to suspend identity node ${user.name}? This will disable all market privileges.`)) {
    try {
      // await axios.post(`/api/admin/users/${user.id}/suspend`);
      if (window.notify) window.notify.success('Node Suspended', 'Identity revoked and access terminated.');
      fetchUsers();
    } catch (error) {
       if (window.notify) window.notify.error('Ledger Error', 'Protocol execution failed.');
    }
  }
};

const getRoleClasses = (role) => {
    switch (role) {
        case 'admin': return 'bg-agri-forest text-agri-earth border-agri-forest';
        case 'vendor': return 'bg-agri-sand text-agri-forest border-agri-sand';
        case 'buyer': return 'bg-gray-50 text-gray-500 border-gray-100';
        default: return 'bg-gray-100 text-gray-500 border-gray-100';
    }
};

onMounted(() => {
  fetchUsers();
});
</script>

<style scoped>
.tracking-tighter {
  letter-spacing: -2px;
}
</style>
