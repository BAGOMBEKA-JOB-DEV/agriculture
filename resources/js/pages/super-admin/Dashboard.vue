<template>
  <div class="min-h-screen bg-gray-50/50 pb-24">
    <!-- Governance Header -->
    <div class="bg-agri-forest py-20 relative overflow-hidden">
        <div class="absolute inset-0 bg-agri-earth/5 opacity-20"></div>
        <div class="container mx-auto px-6 relative z-10">
            <h1 class="text-5xl font-black text-white mb-4 tracking-tighter">
               Governance <span class="text-agri-earth">Nexus</span>
            </h1>
            <p class="text-agri-earth/70 font-medium max-w-xl">System-wide sovereignty and global market telemetry monitoring.</p>
        </div>
    </div>

    <div class="container mx-auto px-6 -mt-10 relative z-20">
      <!-- Telemetry Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
        <div v-for="(val, label) in stats_cards" :key="label" class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-gray-100 group hover:bg-agri-forest transition-all duration-500">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 group-hover:text-agri-earth transition-colors">{{ label.replace('_', ' ') }}</p>
            <p class="text-4xl font-black text-gray-900 group-hover:text-white transition-colors">{{ val }}</p>
        </div>
      </div>

      <!-- System Manifest -->
      <div class="bg-white rounded-[3rem] shadow-2xl border border-gray-100 overflow-hidden">
        <div class="px-10 py-8 border-b border-gray-50 flex justify-between items-center">
            <h2 class="text-xs font-black text-gray-400 uppercase tracking-widest">Global Participant Registry</h2>
            <div class="flex items-center space-x-4">
                <span class="px-4 py-1.5 rounded-full bg-agri-sand/20 text-agri-forest text-[10px] font-black uppercase tracking-widest">{{ users.length }} Active Nodes</span>
            </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-gray-50/50 text-left">
                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Identity</th>
                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Jurisdiction</th>
                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Protocol Role</th>
                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50/30 transition-colors">
                <td class="px-10 py-6">
                  <div class="flex items-center">
                    <div class="w-10 h-10 rounded-xl bg-agri-sand flex items-center justify-center text-agri-forest font-black mr-4 uppercase">
                      {{ user.name.charAt(0) }}
                    </div>
                    <div>
                      <p class="font-black text-gray-900">{{ user.name }}</p>
                      <p class="text-xs text-gray-400 font-medium">{{ user.email }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-10 py-6">
                  <span class="text-xs font-bold text-gray-600">{{ user.location || 'Global' }}</span>
                </td>
                <td class="px-10 py-6">
                  <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border"
                        :class="roleClass(user.role)">
                    {{ user.role }}
                  </span>
                </td>
                <td class="px-10 py-6">
                  <div class="flex items-center">
                     <div class="w-2 h-2 rounded-full mr-2" :class="user.email_verified_at ? 'bg-agri-lush' : 'bg-agri-earth'"></div>
                     <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">{{ user.email_verified_at ? 'Verified' : 'Pending' }}</span>
                  </div>
                </td>
                <td class="px-10 py-6 text-right">
                  <button 
                    v-if="user.role !== 'super_admin'"
                    @click="promoteUser(user.id)" 
                    class="text-agri-forest hover:text-agri-lush font-black uppercase text-[10px] tracking-widest transition-colors"
                  >
                    Promote to Super
                  </button>
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

const stats = ref({});
const users = ref([]);
const stats_cards = ref({
    total_participants: 0,
    total_vendors: 0,
    total_buyers: 0,
    trade_brokers: 0,
    market_assets: 0,
    pending_assets: 0,
    trade_requests: 0,
    system_revenue: '$0.00'
});

const roleClass = (role) => {
  switch (role) {
    case 'super_admin': return 'border-red-200 text-red-600 bg-red-50';
    case 'admin': return 'border-agri-forest/20 text-agri-forest bg-agri-forest/5';
    case 'vendor': return 'border-agri-earth/20 text-agri-earth bg-agri-earth/5';
    case 'buyer': return 'border-blue-200 text-blue-600 bg-blue-50';
    case 'trader': return 'border-agri-sand/50 text-agri-forest bg-agri-sand/20';
    default: return 'border-gray-200 text-gray-600 bg-gray-50';
  }
};

const fetchData = async () => {
  try {
    const [statsRes, usersRes] = await Promise.all([
      axios.get('/api/super-admin/stats'),
      axios.get('/api/super-admin/users')
    ]);
    stats.value = statsRes.data;
    users.value = usersRes.data;

    stats_cards.value = {
        total_participants: stats.value.total_users,
        total_vendors: stats.value.total_vendors,
        total_buyers: stats.value.total_buyers,
        trade_brokers: stats.value.total_traders,
        market_assets: stats.value.total_products,
        pending_assets: stats.value.pending_products,
        trade_requests: stats.value.total_rfqs,
        system_revenue: `$${parseFloat(stats.value.total_revenue || 0).toLocaleString()}`
    };
  } catch (err) {
    console.error('Governance Telemetry Failure:', err);
  }
};

const promoteUser = async (id) => {
    if (!confirm('Authorize Super Admin elevation for this participant?')) return;
    try {
        await axios.post(`/api/super-admin/users/${id}/promote`);
        if (window.notify) window.notify.success('Elevation Complete', 'Participant promoted to Super Admin role.');
        fetchData();
    } catch (err) {
        if (window.notify) window.notify.error('Elevation Failure', 'System-wide governance update failed.');
    }
};

onMounted(fetchData);
</script>

<style scoped>
.tracking-tighter {
  letter-spacing: -2px;
}
</style>
