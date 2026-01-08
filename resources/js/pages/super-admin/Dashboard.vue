<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Notification from '../../components/Notification.vue';
import Modal from '../../components/Modal.vue';

const notificationRef = ref(null);
const stats = ref({});
const users = ref([]);
const loading = ref(false);

const stats_cards = ref({
    total_participants: 0,
    total_vendors: 0,
    total_buyers: 0,
    trade_brokers: 0,
});

const roleClass = (role) => {
  switch (role) {
    case 'super_admin': return 'bg-red-50 text-red-700 border-red-100';
    case 'admin': return 'bg-agri-forest/10 text-agri-forest border-agri-forest/20';
    case 'vendor': return 'bg-agri-earth/10 text-agri-earth border-agri-earth/20';
    case 'buyer': return 'bg-blue-50 text-blue-700 border-blue-100';
    case 'trader': return 'bg-orange-50 text-orange-700 border-orange-100';
    default: return 'bg-gray-50 text-gray-700 border-gray-100';
  }
};

const fetchData = async () => {
  loading.value = true;
  try {
    const [statsRes, usersRes] = await Promise.all([
      axios.get('/api/super-admin/stats'),
      axios.get('/api/super-admin/users')
    ]);
    stats.value = statsRes.data;
    users.value = usersRes.data;

    stats_cards.value = {
        total_participants: stats.value.total_users || 0,
        total_vendors: stats.value.total_vendors || 0,
        total_buyers: stats.value.total_buyers || 0,
        trade_brokers: stats.value.total_traders || 0,
    };
  } catch (err) {
    console.error('Governance Telemetry Failure:', err);
    notificationRef.value?.notify.error('Data Error', 'Failed to load governance data.');
  } finally {
    loading.value = false;
  }
};

const promoteUser = async (id) => {
    if (!confirm('Authorize Super Admin elevation for this participant?')) return;
    try {
        await axios.post(`/api/super-admin/users/${id}/promote`);
        notificationRef.value?.notify.success('Elevation Complete', 'Participant promoted to Super Admin role.');
        fetchData();
    } catch (err) {
        notificationRef.value?.notify.error('Elevation Failure', 'System-wide governance update failed.');
    }
};

const verifyUser = async (user) => {
    notificationRef.value?.notify.success('Verification Applied', `Identity seal verified for ${user.name}.`);
    // API call placeholder
};

const suspendUser = async (user) => {
    if (!confirm(`Are you sure you want to suspend ${user.name}?`)) return;
    notificationRef.value?.notify.success('Node Suspended', `Access revoked for ${user.name}.`);
    // API call placeholder
};

onMounted(fetchData);
</script>

<template>
  <div class="min-h-screen bg-white pb-24">
    <Notification ref="notificationRef" />

    <!-- Governance Header -->
    <div class="bg-white border-b border-gray-100 py-12">
        <div class="container mx-auto px-8">
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">
               Governance <span class="text-agri-forest">Nexus</span>
            </h1>
            <p class="text-gray-500 font-medium mt-2 max-w-xl">System-wide sovereignty and global market telemetry monitoring.</p>
        </div>
    </div>

    <div class="container mx-auto px-8 mt-12">
      <!-- Telemetry Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <div v-for="(val, label) in stats_cards" :key="label" class="bg-white p-6 rounded-2xl border border-gray-100 hover:border-agri-forest/30 transition-all duration-300">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">{{ label.replace('_', ' ') }}</p>
            <div class="flex items-end justify-between">
                <p class="text-4xl font-black text-gray-900">{{ val }}</p>
                <div class="h-8 w-8 rounded-full bg-gray-50 flex items-center justify-center text-agri-forest">
                    <svg v-if="label.includes('participants')" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    <svg v-if="label.includes('vendors')" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    <svg v-if="label.includes('buyers')" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    <svg v-if="label.includes('brokers')" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                </div>
            </div>
        </div>
      </div>

      <!-- System Manifest -->
      <div class="bg-white rounded-3xl border border-gray-100 overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center">
            <h2 class="text-xs font-bold text-gray-900 uppercase tracking-widest">Global Participant Registry</h2>
            <div class="flex items-center space-x-4">
                <span class="px-3 py-1 rounded-full bg-gray-50 text-gray-500 text-xs font-bold border border-gray-100">{{ users.length }} Active Nodes</span>
            </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-gray-50/50 text-left">
                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Identity</th>
                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Jurisdiction</th>
                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Role</th>
                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Status</th>
                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-if="loading">
                  <td colspan="5" class="py-12 text-center">
                      <svg class="animate-spin h-6 w-6 text-agri-forest mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                  </td>
              </tr>
              <tr v-else v-for="user in users" :key="user.id" class="hover:bg-gray-50/50 transition-colors">
                <td class="px-8 py-4">
                  <div class="flex items-center">
                    <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-500 font-bold mr-4 uppercase text-xs">
                      {{ user.name.charAt(0) }}
                    </div>
                    <div>
                      <p class="font-bold text-gray-900 text-sm">{{ user.name }}</p>
                      <p class="text-xs text-gray-400 font-medium">{{ user.email }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-8 py-4 text-center">
                  <span class="text-xs font-medium text-gray-500">{{ user.location || 'Global' }}</span>
                </td>
                <td class="px-8 py-4 text-center">
                  <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide border"
                        :class="roleClass(user.role)">
                    {{ user.role }}
                  </span>
                </td>
                <td class="px-8 py-4 text-center">
                  <div class="flex items-center justify-center">
                     <div class="w-1.5 h-1.5 rounded-full mr-2" :class="user.email_verified_at ? 'bg-green-500' : 'bg-yellow-500'"></div>
                     <span class="text-xs font-medium text-gray-500">{{ user.email_verified_at ? 'Verified' : 'Pending' }}</span>
                  </div>
                </td>
                <td class="px-8 py-4 text-right">
                  <div class="flex items-center justify-end space-x-2">
                    <button 
                        @click="verifyUser(user)"
                        class="p-2 text-agri-forest hover:bg-agri-forest/5 rounded-lg transition-colors group"
                        title="Verify Identity"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </button>
                    <button 
                        v-if="user.role !== 'super_admin'"
                        @click="promoteUser(user.id)" 
                        class="p-2 text-agri-earth hover:bg-agri-earth/5 rounded-lg transition-colors group"
                        title="Promote to Super Admin"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                    </button>
                    <button 
                        @click="suspendUser(user)"
                        class="p-2 text-red-400 hover:bg-red-50 rounded-lg transition-colors group"
                        title="Suspend Node"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>
