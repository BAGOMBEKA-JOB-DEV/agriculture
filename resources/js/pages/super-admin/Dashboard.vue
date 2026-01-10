<script setup>
import { ref, reactive, onMounted, onUnmounted, watch } from 'vue';
import axios from 'axios';
import Notification from '../../components/Notification.vue';
import Modal from '../../components/Modal.vue';

// UI State
const notificationRef = ref(null);
const loading = ref(true);
const stats = ref({});
const users = ref([]);
const roles = ref([]);
const stats_cards = ref([]);
const isDrawerOpen = ref(false);
const inspectedUser = ref(null);
const activeDropdownId = ref(null);

// Discovery Suite State
const showAdvancedFilters = ref(false);
const filters = reactive({
  q: '',
  role: '',
  status: '',
  location: '',
  verified: ''
});

const resetFilters = () => {
  filters.q = '';
  filters.role = '';
  filters.status = '';
  filters.location = '';
  filters.verified = '';
  fetchData();
};

const toggleDropdown = (userId) => {
  activeDropdownId.value =
    activeDropdownId.value === userId ? null : userId;
};


// Modal State
const isModalOpen = ref(false);
const isProcessing = ref(false);
const modalConfig = ref({
  title: '',
  message: '',
  confirmText: '',
  type: 'primary',
  action: null,
  data: null,
  userId: null,
  user: null
});

// Helper: Role Aesthetics
const roleClass = (role) => {
  const mapping = {
    super_admin: 'bg-indigo-50 text-indigo-700 border-indigo-100',
    admin: 'bg-blue-50 text-blue-700 border-blue-100',
    vendor: 'bg-emerald-50 text-emerald-700 border-emerald-100',
    buyer: 'bg-amber-50 text-amber-700 border-amber-100',
    trader: 'bg-purple-50 text-purple-700 border-purple-100'
  };
  return mapping[role] || 'bg-gray-50 text-gray-700 border-gray-100';
};

const fetchData = async () => {
  loading.value = true;
  try {
    const [statsRes, usersRes, rolesRes] = await Promise.all([
      axios.get('/api/super-admin/stats'),
      axios.get('/api/super-admin/users', { params: filters }),
      axios.get('/api/super-admin/roles')
    ]);
    stats.value = statsRes.data;
    users.value = usersRes.data;
    roles.value = rolesRes.data.roles || rolesRes.data;

    stats_cards.value = [
      { label: 'Total Participants', value: stats.value.total_users, icon: 'UsersIcon' },
      { label: 'Market Vendors', value: stats.value.total_vendors, icon: 'StoreIcon' },
      { label: 'Global Buyers', value: stats.value.total_buyers, icon: 'ShoppingBagIcon' },
      { label: 'Trade Volume', value: `$${(stats.value.total_revenue / 1000).toFixed(1)}k`, icon: 'CurrencyIcon' }
    ];
  } catch (err) {
    console.error('Nexus Synchronization Failure:', err);
    if (window.notify) window.notify.error('Data Error', 'Failed to load governance telemetry.');
  } finally {
    loading.value = false;
  }
};

// Custom Enterprise-Grade Debounce
const debounce = (fn, delay) => {
  let timeoutId;
  return (...args) => {
    if (timeoutId) clearTimeout(timeoutId);
    timeoutId = setTimeout(() => {
      fn(...args);
    }, delay);
  };
};

const debouncedFetch = debounce(fetchData, 500);

watch(() => filters.q, () => debouncedFetch());
watch(() => filters.location, () => debouncedFetch());
watch(() => [filters.role, filters.status, filters.verified], () => fetchData());

import { useAuthStore } from '../../stores/auth'; // Import Store
const authStore = useAuthStore(); // Init Store

// ... (existing code)



// Start of template changes for dynamic role checking
const isSuperAdmin = (user) => {
    return user?.roles?.some(r => r.slug === 'super_admin') || user?.role === 'super_admin';
};

const closeDropdowns = (e) => {
  if (!e.target.closest('.dropdown-trigger')) {
    activeDropdownId.value = null;
  }
};

const openInspector = (user) => {
  inspectedUser.value = user;
  isDrawerOpen.value = true;
  activeDropdownId.value = null;
};

const closeInspector = () => {
  isDrawerOpen.value = false;
  setTimeout(() => {
    inspectedUser.value = null;
  }, 300);
};

// Governance Actions
const openPromoteModal = (user) => {
  modalConfig.value = {
    title: 'Authorize Super Admin Elevation',
    message: `Are you sure you want to elevate ${user.name} to Super Admin? This grants system-wide sovereign access.`,
    confirmText: 'Confirm Elevation',
    type: 'success',
    action: 'promote',
    user: user
  };
  isModalOpen.value = true;
  activeDropdownId.value = null;
};

const openSuspendModal = (user) => {
  modalConfig.value = {
    title: 'Confirm Node Suspension',
    message: `Are you sure you want to suspend access for ${user.name}? This will revoke all marketplace privileges.`,
    confirmText: 'Confirm Suspension',
    type: 'danger',
    action: 'suspend',
    user: user
  };
  isModalOpen.value = true;
  activeDropdownId.value = null;
};

const openVerifyModal = (user) => {
  modalConfig.value = {
    title: 'Confirm Identity Verification',
    message: `Authorize identity verification for ${user.name}. This marks their node as verified in the registry.`,
    confirmText: 'Verify Identity',
    type: 'success',
    action: 'verify',
    user: user
  };
  isModalOpen.value = true;
  activeDropdownId.value = null;
};

const openActivateModal = (user) => {
  modalConfig.value = {
    title: 'Restore Participant Access',
    message: `Confirm restoration of account access for ${user.name}. All privileges will be re-established.`,
    confirmText: 'Restore Access',
    type: 'success',
    action: 'activate',
    user: user
  };
  isModalOpen.value = true;
  activeDropdownId.value = null;
};

const handleModalConfirm = async () => {
  const { action, user } = modalConfig.value;
  const id = user?.id;
  if (!id) return;

  isProcessing.value = true;
  try {
    const url = `/api/super-admin/users/${id}/${action}`;
    await axios.post(url);
    
    if (window.notify) {
       const actionLabels = { 
         promote: 'Authority Elevated', 
         suspend: 'Access Revoked', 
         verify: 'Identity Authorized', 
         activate: 'Access Restored' 
       };
       window.notify.success(actionLabels[action], 'Nexus Registry updated successfully.');
    }
    
    isModalOpen.value = false;
    fetchData();
    if (inspectedUser.value && inspectedUser.value.id === id) {
       // Refresh inspector data if open
       axios.get(`/api/super-admin/users`).then(res => {
          const updated = res.data.find(u => u.id === id);
          if (updated) inspectedUser.value = updated;
       });
    }
  } catch (error) {
    if (window.notify) window.notify.error('Governance Error', error.response?.data?.message || 'Protocol synchronization failure.');
  } finally {
    isProcessing.value = false;
  }
};

onMounted(() => {
  fetchData();
  window.addEventListener('click', closeDropdowns);
});

onUnmounted(() => {
  window.removeEventListener('click', closeDropdowns);
});
</script>

<template>
  <div class="min-h-screen bg-white pb-24">
    <Notification ref="notificationRef" />

    <!-- Governance Header: Sovereign Dark Branding -->
    <div class="bg-agri-forest py-20 relative overflow-hidden">
        <!-- Abstract Branding Accents -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -mr-32 -mt-32 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-agri-lush/10 rounded-full -ml-32 -mb-32 blur-2xl"></div>
        
        <div class="container mx-auto px-8 relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-12">
                <div>
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center border border-white/20 shadow-inner">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                        </div>
                        <span class="text-[11px] font-black text-white/40 uppercase tracking-[0.4em]">Nexus Governance Authority</span>
                    </div>
                    <h1 class="text-5xl font-black text-white tracking-tighter leading-none mb-4">
                       Global <span class="text-agri-lush">Executive</span> Registry
                    </h1>
                    <p class="text-white/60 font-medium max-w-2xl text-xl leading-relaxed">Centralized oversight for global participant telemetry, market compliance, and sovereign trade protocols.</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="px-8 py-4 bg-white/5 rounded-[2rem] border border-white/10 backdrop-blur-2xl flex items-center space-x-4 shadow-2xl">
                        <div class="h-3 w-3 rounded-full bg-agri-lush animate-pulse shadow-[0_0_15px_rgba(132,204,22,1)]"></div>
                        <div>
                            <span class="block text-[10px] font-black text-white/40 uppercase tracking-[0.2em] mb-0.5">Network Integrity</span>
                            <span class="text-xs font-black text-white uppercase tracking-widest">Sovereign Active</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Apex Telemetry Grid: Top-Most Executive Discovery -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mt-20">
                <div v-for="card in stats_cards" :key="card.label" 
                     class="bg-white/15 backdrop-blur-3xl p-8 rounded-[2.5rem] border border-white/30 transition-all duration-500 group cursor-default relative overflow-hidden shadow-2xl hover:-translate-y-2 hover:border-white/50 hover:shadow-agri-lush/20">
                    <div class="absolute -top-12 -right-12 w-32 h-32 bg-agri-lush/10 rounded-full blur-2xl transition-all duration-700 group-hover:bg-agri-lush/20 group-hover:scale-110"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-10">
                            <div class="p-4 rounded-2xl bg-agri-lush text-white border border-white/20 transition-all duration-700 shadow-[0_10px_40px_rgba(132,204,22,0.3)] group-hover:scale-110 group-hover:shadow-agri-lush/50">
                                <svg v-if="card.icon === 'UsersIcon'" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                <svg v-if="card.icon === 'StoreIcon'" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                <svg v-if="card.icon === 'ShoppingBagIcon'" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                <svg v-if="card.icon === 'CurrencyIcon'" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-[10px] font-black text-white uppercase tracking-[0.3em] mb-2">{{ card.label }}</h4>
                            <p class="text-5xl font-black text-white tracking-tighter leading-none group-hover:scale-[1.02] transition-transform duration-500 origin-left">{{ card.value }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Discovery Suite: High-Depth Control Center -->
    <div class="bg-white/80 border-b border-gray-100 py-6 sticky top-0 z-30 backdrop-blur-xl shadow-sm">
        <div class="container mx-auto px-8">
            <div class="flex flex-col xl:flex-row items-stretch xl:items-center gap-5">
                <!-- Global Search -->
                <div class="relative flex-1 group">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-agri-forest transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input 
                        v-model="filters.q"
                        type="text" 
                        placeholder="Identify participant by name or security credential..."
                        class="block w-full pl-12 pr-4 py-4 bg-gray-50/50 border border-gray-100 rounded-3xl text-sm font-semibold placeholder-gray-400 focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest/50 focus:bg-white transition-all duration-500"
                    >
                </div>

                <!-- Horizontal Filters -->
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Role Filter -->
                    <div class="relative min-w-[160px]">
                        <select v-model="filters.role" class="w-full pl-11 pr-8 py-3.5 bg-gray-50/50 border border-gray-100 rounded-2xl text-[11px] font-black text-gray-600 appearance-none focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest/30 focus:bg-white transition-all duration-300 cursor-pointer uppercase tracking-widest">
                            <option value="">All Authorities</option>
                            <option v-for="role in roles" :key="role.slug" :value="role.slug">
                                {{ role.name }}
                            </option>
                        </select>
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        </div>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-300">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="relative min-w-[160px]">
                        <select v-model="filters.status" class="w-full pl-11 pr-8 py-3.5 bg-gray-50/50 border border-gray-100 rounded-2xl text-[11px] font-black text-gray-600 appearance-none focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest/30 focus:bg-white transition-all duration-300 cursor-pointer uppercase tracking-widest">
                            <option value="">Registry Status</option>
                            <option value="active">Active</option>
                            <option value="suspended">Suspended</option>
                            <option value="pending">Pending</option>
                        </select>
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-300">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </div>
                    </div>

                    <!-- Location Filter -->
                    <div class="relative flex-1 sm:flex-none sm:min-w-[200px]">
                        <input v-model="filters.location" type="text" placeholder="Filter Jurisdiction..." class="w-full pl-11 pr-4 py-3.5 bg-gray-50/50 border border-gray-100 rounded-2xl text-[11px] font-black text-gray-600 placeholder-gray-400 focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest/30 focus:bg-white transition-all duration-300 uppercase tracking-widest">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-2">
                        <button 
                            @click="resetFilters"
                            class="px-5 py-3.5 bg-white border border-gray-100 rounded-2xl text-[10px] font-black text-red-500 uppercase tracking-widest hover:bg-red-50 hover:border-red-100 hover:text-red-600 transition-all duration-300 active:scale-95 translate-all shadow-sm"
                        >
                            Reset
                        </button>
                        
                        <button 
                            @click="showAdvancedFilters = !showAdvancedFilters"
                            :class="[showAdvancedFilters ? 'bg-gray-900 text-white border-gray-900 shadow-lg' : 'bg-white text-gray-400 border-gray-100']"
                            class="p-3.5 rounded-2xl border transition-all duration-500 hover:shadow-md active:scale-95"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" /></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Extended Protocols -->
            <transition
                enter-active-class="transition duration-500 ease-out"
                enter-from-class="transform -translate-y-4 opacity-0"
                enter-to-class="transform translate-y-0 opacity-100"
                leave-active-class="transition duration-300 ease-in"
                leave-from-class="transform translate-y-0 opacity-100"
                leave-to-class="transform -translate-y-4 opacity-0"
            >
                <div v-show="showAdvancedFilters" class="mt-6 pt-6 border-t border-gray-50 flex flex-wrap gap-8">
                    <div class="min-w-[300px]">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Identity Authorization Registry</label>
                        <div class="flex items-center space-x-2">
                             <select v-model="filters.verified" class="flex-1 bg-gray-50/50 border border-gray-100 rounded-2xl px-5 py-3.5 text-[11px] font-black text-gray-600 focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest/30 transition-all uppercase tracking-widest">
                                <option value="">Universal Node Status</option>
                                <option value="true">Verified Participants (Authorized)</option>
                                <option value="false">Unverified Nodes (Discovery Mode)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </div>

    <div class="container mx-auto px-8 mt-12 mb-20">
      <!-- System Manifest: Global Registry -->
      <div class="bg-white rounded-[3rem] border border-gray-100 overflow-hidden shadow-2xl shadow-gray-200/40">
        <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
            <h2 class="text-xs font-black text-gray-900 uppercase tracking-[0.2em]">Global Participant Registry</h2>
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2 bg-white px-3 py-1.5 rounded-xl border border-gray-100 shadow-sm">
                    <div class="w-2 h-2 rounded-full bg-agri-forest"></div>
                    <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">{{ users.length }} Active Nodes</span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-gray-50/50 text-left border-b border-gray-100">
                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Identity Node</th>
                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Jurisdiction</th>
                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Authority Role</th>
                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Protocol Status</th>
                <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">Moderation</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-if="loading">
                  <td colspan="5" class="py-24 text-center">
                      <div class="relative inline-block">
                          <svg class="animate-spin h-12 w-12 text-agri-forest/20 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                          <div class="absolute inset-0 flex items-center justify-center">
                              <div class="h-2 w-2 rounded-full bg-agri-forest animate-ping"></div>
                          </div>
                      </div>
                      <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mt-6">Syncing Nexus Registry...</p>
                  </td>
              </tr>
              <tr v-else-if="users.length === 0" class="group transition-colors duration-500">
                  <td colspan="5" class="py-32 text-center bg-gray-50/20">
                      <div class="relative inline-block">
                          <div class="absolute -inset-8 bg-agri-forest/5 rounded-full blur-3xl group-hover:bg-agri-forest/10 transition-all duration-1000"></div>
                          <div class="relative h-24 w-24 bg-white border border-gray-100 rounded-[2.5rem] flex items-center justify-center text-gray-200 shadow-xl shadow-gray-200/50 mx-auto mb-8 group-hover:scale-110 group-hover:border-agri-forest/20 transition-all duration-700">
                             <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="0.8" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />
                             </svg>
                          </div>
                      </div>
                      <h3 class="text-xl font-black text-gray-900 tracking-tight">Zero Results Protocol</h3>
                      <p class="text-xs font-medium text-gray-400 mt-2 max-w-[320px] mx-auto leading-relaxed">
                          No identity nodes match your current discovery parameters across all sovereign jurisdictions.
                      </p>
                      <button 
                        @click="resetFilters"
                        class="mt-10 px-10 py-4 bg-white border border-gray-100 rounded-3xl text-[10px] font-black text-agri-forest shadow-lg shadow-gray-200/50 uppercase tracking-[0.3em] hover:bg-agri-forest hover:text-white hover:border-agri-forest hover:-translate-y-1 transition-all duration-700 active:scale-95"
                      >
                        Reset Discovery Suite
                      </button>
                  </td>
              </tr>
              <tr v-else v-for="user in users" :key="user.id" class="hover:bg-gray-50/50 transition-all duration-500 group/row">
                <td class="px-8 py-7">
                  <div class="flex items-center space-x-5">
                    <div class="w-14 h-14 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400 font-black uppercase text-lg shadow-sm group-hover/row:border-agri-forest/30 group-hover/row:bg-white transition-all duration-500">
                      {{ user.name.charAt(0) }}
                    </div>
                    <div>
                      <p class="font-black text-gray-900 text-sm tracking-tight leading-none">{{ user.name }}</p>
                      <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter mt-1.5">{{ user.email }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-8 py-6 text-center">
                  <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ user.location || 'Global Repository' }}</span>
                </td>
                <td class="px-8 py-6 text-center">
                  <span class="px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest border shadow-sm"
                        :class="roleClass(user.role)">
                    {{ user.role?.replace('_', ' ') }}
                  </span>
                </td>
                <td class="px-8 py-6 text-center">
                  <div class="flex items-center justify-center">
                     <template v-if="user.status === 'suspended'">
                        <div class="w-1.5 h-1.5 rounded-full mr-2 bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.5)]"></div>
                        <span class="text-[10px] font-black text-red-500 uppercase tracking-widest">Revoked</span>
                     </template>
                     <template v-else>
                        <div class="w-1.5 h-1.5 rounded-full mr-3" :class="user.email_verified_at ? 'bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.5)]' : 'bg-yellow-500 shadow-[0_0_8px_rgba(234,179,8,0.5)]'"></div>
                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">{{ user.email_verified_at ? 'Verified Node' : 'Pending Node' }}</span>
                     </template>
                  </div>
                </td>
                <td class="px-8 py-4 text-right">
                  <div class="flex items-center justify-end">
                    <div class="relative dropdown-trigger">
                      <button 
                        @click.stop="toggleDropdown(user.id)"
                        class="p-2.5 text-gray-400 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 rounded-xl transition-all duration-300 flex items-center space-x-2 group"
                        :class="{ 'bg-gray-900 text-white': activeDropdownId === user.id }"
                      >
                         <span class="text-[10px] font-black uppercase tracking-widest pl-1">Actions</span>
                         <svg class="w-4 h-4 transition-transform duration-300" :class="{ 'rotate-180': activeDropdownId === user.id }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                         </svg>
                      </button>

                      <Transition
                        enter-active-class="transition duration-200 ease-out"
                        enter-from-class="transform scale-95 opacity-0 -translate-y-2"
                        enter-to-class="transform scale-100 opacity-100 translate-y-0"
                        leave-active-class="transition duration-150 ease-in"
                        leave-from-class="transform scale-100 opacity-100 translate-y-0"
                        leave-to-class="transform scale-95 opacity-0 -translate-y-2"
                      >
                        <div v-if="activeDropdownId === user.id" class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-2xl border border-gray-100 z-[60] py-2 overflow-hidden">
                          <!-- Global Inspection -->
                          <button 
                            @click="openInspector(user); activeDropdownId = null"
                            class="w-full flex items-center px-5 py-3.5 text-xs font-bold text-gray-600 hover:bg-gray-50 transition-colors space-x-3"
                          >
                            <svg class="w-4.5 h-4.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>Inspect Node</span>
                          </button>

                          <div class="h-px bg-gray-50 my-1 mx-4"></div>

                          <!-- Identity Authorization -->
                          <button 
                            @click="openVerifyModal(user); activeDropdownId = null"
                            :disabled="user.status === 'active' || !!user.email_verified_at"
                            class="w-full flex items-center px-5 py-3.5 text-xs font-bold transition-colors space-x-3 group/item disabled:opacity-40 disabled:cursor-not-allowed"
                            :class="user.status === 'active' || !!user.email_verified_at ? 'text-gray-300' : 'text-agri-forest hover:bg-agri-forest/5'"
                          >
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <div class="flex flex-col items-start translate-y-0.5">
                               <span>Authorize Identity</span>
                               <span v-if="user.status === 'active'" class="text-[9px] font-black uppercase tracking-tighter text-gray-400 leading-none mt-1">Already Active</span>
                            </div>
                          </button>

                          <!-- Authority Elevation -->
                          <button 
                            v-if="!isSuperAdmin(user) && authStore.can('manage_users')"
                            @click="openPromoteModal(user.id); activeDropdownId = null"
                            class="w-full flex items-center px-5 py-3.5 text-xs font-bold text-agri-earth hover:bg-agri-earth/5 transition-colors space-x-3 group/item"
                          >
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                            <div class="flex flex-col items-start translate-y-0.5">
                               <span>Elevate Authority</span>
                               <span class="text-[9px] font-black uppercase tracking-tighter text-gray-400 leading-none mt-1">Promote to Super Admin</span>
                            </div>
                          </button>

                          <!-- Access Revocation -->
                          <button 
                            v-if="!isSuperAdmin(user) && user.status !== 'suspended' && authStore.can('manage_users')"
                            @click="openSuspendModal(user); activeDropdownId = null"
                            class="w-full flex items-center px-5 py-3.5 text-xs font-bold text-red-600 hover:bg-red-50 transition-colors space-x-3 group/item"
                          >
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                            <div class="flex flex-col items-start translate-y-0.5">
                               <span>Revoke Access</span>
                               <span class="text-[9px] font-black uppercase tracking-tighter text-gray-400 leading-none mt-1">Suspend Workspace</span>
                            </div>
                          </button>

                          <!-- Access Restoration (Unrevoke) -->
                          <button 
                            v-if="user.status === 'suspended'"
                            @click="openActivateModal(user); activeDropdownId = null"
                            class="w-full flex items-center px-5 py-3.5 text-xs font-bold text-agri-forest hover:bg-agri-forest/5 transition-colors space-x-3 group/item"
                          >
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <div class="flex flex-col items-start translate-y-0.5">
                               <span>Restore Access</span>
                               <span class="text-[9px] font-black uppercase tracking-tighter text-gray-400 leading-none mt-1">Unrevoke Participant</span>
                            </div>
                          </button>
                        </div>
                      </Transition>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Sovereign Participant Drawer -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition-opacity duration-300 ease-out"
        leave-active-class="transition-opacity duration-300 ease-in"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="isDrawerOpen" class="fixed inset-0 z-[100] overflow-hidden">
          <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="closeInspector"></div>
          
          <Transition
            enter-active-class="transform transition-transform duration-500 ease-out"
            leave-active-class="transform transition-transform duration-500 ease-in"
            enter-from-class="translate-x-full"
            enter-to-class="translate-x-0"
            leave-from-class="translate-x-0"
            leave-to-class="translate-x-full"
            appear
          >
            <div v-if="isDrawerOpen" class="absolute inset-y-0 right-0 max-w-xl w-full bg-white shadow-2xl flex flex-col">
              <!-- Drawer Header: Sovereign Identity -->
              <div class="px-8 py-16 border-b border-white/10 bg-agri-forest sticky top-0 z-20 overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32 blur-3xl"></div>
                
                <div class="relative z-10 flex items-start justify-between">
                  <div class="flex items-center space-x-8">
                    <div class="w-24 h-24 rounded-[2.5rem] bg-white/10 flex items-center justify-center text-white font-black text-4xl border border-white/20 shadow-2xl backdrop-blur-md uppercase">
                      {{ inspectedUser?.name?.charAt(0) }}
                    </div>
                    <div>
                      <div class="flex items-center space-x-2 mb-2">
                        <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-[0.2em] bg-white/10 text-white/70 border border-white/10">Registry Node</span>
                      </div>
                      <h2 class="text-4xl font-black text-white tracking-tight">{{ inspectedUser?.name }}</h2>
                      <div class="flex items-center mt-4 space-x-3">
                        <span class="px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest border border-white/10 bg-white/5 text-white/90">
                          {{ inspectedUser?.role?.replace('_', ' ') }}
                        </span>
                        <div v-if="inspectedUser?.status === 'suspended'" class="flex items-center px-4 py-1.5 bg-red-500/20 border border-red-500/30 rounded-xl">
                           <div class="w-2 h-2 rounded-full mr-2 bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.8)]"></div>
                           <span class="text-[10px] font-black text-red-200 uppercase tracking-widest">Access Revoked</span>
                        </div>
                        <div v-else class="flex items-center px-4 py-1.5 bg-white/5 border border-white/10 rounded-xl">
                           <div class="w-2 h-2 rounded-full mr-2" :class="inspectedUser?.email_verified_at ? 'bg-agri-lush shadow-[0_0_8px_rgba(132,204,22,0.8)]' : 'bg-yellow-400 shadow-[0_0_8px_rgba(250,204,21,0.8)]'"></div>
                           <span class="text-[10px] font-black text-white/70 uppercase tracking-widest">{{ inspectedUser?.email_verified_at ? 'Verified Node' : 'Discovery Mode' }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <button @click="closeInspector" class="p-4 bg-white/10 rounded-3xl text-white/60 hover:text-white hover:bg-white/20 transition-all group backdrop-blur-md border border-white/10">
                    <svg class="w-6 h-6 group-hover:rotate-90 transition-transform duration-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                  </button>
                </div>
              </div>

              <!-- Drawer Content -->
              <div class="flex-1 overflow-y-auto p-8 space-y-12">
                <!-- Node Telemetry Section -->
                <div>
                   <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6">Nexus Node Telemetry</h4>
                   <div class="grid grid-cols-1 gap-4">
                      <div v-for="(val, label) in { 
                        'Registry ID': '#' + inspectedUser?.id, 
                        'Authentication Matrix': inspectedUser?.email,
                        'Sovereign Jurisdiction': inspectedUser?.location || 'Global Repository',
                        'Node Initialization': new Date(inspectedUser?.created_at).toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' })
                      }" :key="label" class="flex justify-between items-center py-4 px-6 bg-gray-50/50 rounded-2xl border border-gray-50 transition-colors hover:bg-white hover:border-agri-forest/10 group">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter group-hover:text-agri-forest">{{ label }}</span>
                        <span class="text-sm font-black text-gray-900">{{ val }}</span>
                      </div>
                   </div>
                </div>

                <!-- Compliance & Activity Section -->
                <div>
                  <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6">Market Compliance Pulse</h4>
                  <div class="bg-gray-50 rounded-[2.5rem] p-10 border border-gray-100 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-agri-forest/5 rounded-full -mr-16 -mt-16 transition-transform duration-700 group-hover:scale-150"></div>
                    <ul class="space-y-6 relative z-10">
                      <li v-for="item in [
                        { label: 'Role Authority Verification', status: 'Authorized' },
                        { label: 'Sovereign ID Protocol', status: inspectedUser?.email_verified_at ? 'Active' : 'Pending' },
                        { label: 'Trade History Registry', status: 'Clean' }
                      ]" :key="item.label" class="flex items-center justify-between">
                        <div class="flex items-center text-xs font-bold text-gray-600 uppercase tracking-wide">
                          <svg class="w-4 h-4 text-agri-forest mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                          {{ item.label }}
                        </div>
                        <span class="text-[10px] font-black text-agri-forest/50 uppercase">{{ item.status }}</span>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>

              <!-- Drawer Footer -->
              <div class="p-8 border-t border-gray-100 bg-white space-y-4">
                <div class="flex space-x-4">
                  <button 
                    v-if="inspectedUser?.status === 'suspended'"
                    @click="openActivateModal(inspectedUser)"
                    class="flex-1 bg-agri-forest text-white py-5 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-agri-lush transition-all shadow-xl shadow-agri-forest/20 flex items-center justify-center"
                  >
                    Restore Access
                  </button>
                  <button 
                    v-else
                    @click="openSuspendModal(inspectedUser)"
                    :disabled="isSuperAdmin(inspectedUser) || !authStore.can('manage_users')"
                    class="flex-1 bg-red-50 text-red-600 py-5 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-red-100 transition-all border border-red-100 flex items-center justify-center disabled:opacity-40 disabled:cursor-not-allowed"
                  >
                    Revoke Access
                  </button>
                </div>
                <button 
                  @click="openVerifyModal(inspectedUser)"
                  :disabled="inspectedUser?.status === 'active' || !!inspectedUser?.email_verified_at"
                  class="w-full py-5 bg-white border border-gray-100 text-gray-400 hover:text-agri-forest rounded-2xl font-black uppercase text-[10px] tracking-widest transition-all shadow-sm disabled:opacity-20 disabled:cursor-not-allowed"
                >
                  {{ inspectedUser?.status === 'active' || !!inspectedUser?.email_verified_at ? 'Identity Authorized' : 'Authorize Identity Node' }}
                </button>
                <button 
                  v-if="!isSuperAdmin(inspectedUser) && authStore.can('manage_users')"
                  @click="openPromoteModal(inspectedUser?.id)"
                  class="w-full py-4 bg-gray-50 text-gray-600 hover:bg-agri-earth hover:text-white rounded-2xl font-black uppercase text-[10px] tracking-widest transition-all"
                >
                  Elevate Authority
                </button>
              </div>
            </div>
          </Transition>
        </div>
      </Transition>
    </Teleport>

    <!-- Governance Action Modal -->
    <Modal
      :is-open="isModalOpen"
      :title="modalConfig.title"
      :message="modalConfig.message"
      :confirm-text="modalConfig.confirmText"
      :type="modalConfig.type"
      :loading="isProcessing"
      @confirm="handleModalConfirm"
      @cancel="isModalOpen = false"
    />
  </div>
</template>
