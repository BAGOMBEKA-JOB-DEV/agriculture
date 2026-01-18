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

// Selection Matrix
const selectedIds = ref([]);
const isBulkProcessing = ref(false);
const showExportDropdown = ref(false);

const toggleSelectAll = () => {
  if (selectedIds.value.length === users.value.length) {
    selectedIds.value = [];
  } else {
    selectedIds.value = users.value.map(u => u.id);
  }
};

const handleBulkAction = async () => {
  if (selectedIds.value.length === 0 || !modalConfig.value.bulkAction) return;

  isProcessing.value = true;
  try {
    const response = await axios.post('/api/super-admin/users/bulk', {
      ids: selectedIds.value,
      action: modalConfig.value.bulkAction
    });

    if (window.notify) window.notify.success('Bulk Protocol Executed', response.data.message);
    selectedIds.value = [];
    isModalOpen.value = false;
    fetchData();
  } catch (error) {
    if (error.response?.status === 422) {
      modalConfig.value = {
        title: 'Cannot Delete User',
        message: error.response.data.message || 'This user cannot be deleted because they have associated data (trade requests, products, etc).',
        type: 'danger',
        confirmText: 'Understood',
        cancelText: '',
        isWarning: true
      };
      return;
    }
    if (window.notify) window.notify.error('Governance Failure', error.response?.data?.message || 'Bulk synchronization failed.');
  } finally {
    isProcessing.value = false;
  }
};

const openBulkActionModal = (action) => {
  const titles = {
    verify: 'Verify Users',
    suspend: 'Suspend Users',
    activate: 'Restore Access',
    delete: 'Delete Users'
  };

  const messages = {
    verify: `Are you sure you want to verify ${selectedIds.value.length} users? This will allow them to access the platform.`,
    suspend: `Are you sure you want to suspend ${selectedIds.value.length} selected users? This will block their access.`,
    activate: `Restore access for ${selectedIds.value.length} selected users?`,
    delete: `Are you sure you want to delete ${selectedIds.value.length} users? This action cannot be undone.`
  };

  modalConfig.value = {
    title: titles[action],
    message: messages[action],
    confirmText: action === 'delete' ? 'Delete Users' : (action === 'suspend' ? 'Suspend Users' : 'Confirm'),
    type: (action === 'suspend' || action === 'delete') ? 'danger' : 'success',
    bulkAction: action
  };
  isModalOpen.value = true;
};

const triggerExport = async (format) => {
  try {
    const response = await axios.post(`/api/super-admin/users/export/${format}`, filters);
    if (window.notify) window.notify.success('Export Initialized', response.data.message);
    showExportDropdown.value = false;
  } catch (error) {
    if (window.notify) window.notify.error('Export Error', 'Failed to initialize background generation.');
  }
};

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
      { label: 'Total Users', value: stats.value.total_users, icon: 'UsersIcon' },
      { label: 'Vendors', value: stats.value.total_vendors, icon: 'StoreIcon' },
      { label: 'Buyers', value: stats.value.total_buyers, icon: 'ShoppingBagIcon' },
      { label: 'Trade Volume', value: `$${(stats.value.total_revenue / 1000).toFixed(1)}k`, icon: 'CurrencyIcon' },
      { label: 'Traders', value: stats.value.total_traders, icon: 'TradersIcon' },
      { label: 'Super Admins', value: stats.value.total_super_admins, icon: 'ShieldIcon' },
      { label: 'Suspended', value: stats.value.total_suspended, icon: 'BanIcon' }
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
    title: 'Promote to Super Admin',
    message: `Are you sure you want to promote ${user.name} to Super Admin? This gives them full system access.`,
    confirmText: 'Promote User',
    type: 'success',
    action: 'promote',
    user: user
  };
  isModalOpen.value = true;
  activeDropdownId.value = null;
};

const openSuspendModal = (user) => {
  modalConfig.value = {
    title: 'Suspend User',
    message: `Are you sure you want to suspend ${user.name}? They will lose access to the platform.`,
    confirmText: 'Suspend User',
    type: 'danger',
    action: 'suspend',
    user: user
  };
  isModalOpen.value = true;
  activeDropdownId.value = null;
};

const openVerifyModal = (user) => {
  modalConfig.value = {
    title: 'Verify User',
    message: `Verify the identity of ${user.name}. This marks them as verified.`,
    confirmText: 'Verify User',
    type: 'success',
    action: 'verify',
    user: user
  };
  isModalOpen.value = true;
  activeDropdownId.value = null;
};

const openActivateModal = (user) => {
  modalConfig.value = {
    title: 'Restore Access',
    message: `Restore access for ${user.name}? They will be able to log in again.`,
    confirmText: 'Restore Access',
    type: 'success',
    action: 'activate',
    user: user
  };
  isModalOpen.value = true;
  activeDropdownId.value = null;
};

const openDeleteModal = (user) => {
  modalConfig.value = {
    title: 'Delete User',
    message: `Are you sure you want to delete ${user.name}? This action cannot be undone.`,
    confirmText: 'Delete User',
    type: 'danger',
    action: 'delete',
    user: user
  };
  isModalOpen.value = true;
  activeDropdownId.value = null;
};

const handleModalConfirm = async () => {
  if (modalConfig.value.bulkAction) {
    return handleBulkAction();
  }

  const { action, user } = modalConfig.value;
  const id = user?.id;
  if (!id) return;

  isProcessing.value = true;
  try {
    const method = action === 'delete' ? 'delete' : 'post';
    const url = `/api/super-admin/users/${id}${action === 'delete' ? '' : '/' + action}`;
    await axios[method](url);

    if (window.notify) {
      const actionLabels = {
        promote: 'User Promoted',
        suspend: 'User Suspended',
        verify: 'User Verified',
        activate: 'Access Restored',
        delete: 'User Deleted'
      };
      window.notify.success(actionLabels[action], 'Action completed successfully.');
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
    if (error.response?.status === 422) {
      modalConfig.value = {
        ...modalConfig.value,
        title: 'Cannot Delete User',
        message: error.response.data.message || 'This user cannot be deleted because they have associated data.',
        type: 'danger',
        confirmText: 'Understood',
        cancelText: ''
      };
      return;
    }
    if (window.notify) window.notify.error('Governance Error', error.response?.data?.message || 'Protocol synchronization failure.');
  } finally {
    isProcessing.value = false;
  }
};

const showUserModal = ref(false);
const showInviteModal = ref(false);
const showLogModal = ref(false);

const userForm = ref({ name: '', email: '', role: 'user', location: '' });
const inviteForm = ref({ emails: '' });
const userLogs = ref([]);
const editingUser = ref(null);

const openAddModal = () => {
  editingUser.value = null;
  userForm.value = { name: '', email: '', role: 'vendor', location: '' };
  showUserModal.value = true;
};

const openEditModal = (user) => {
  editingUser.value = user;
  userForm.value = { ...user };
  showUserModal.value = true;
  activeDropdownId.value = null;
};

const openInviteModal = () => {
  inviteForm.value.emails = '';
  showInviteModal.value = true;
};

const openLogModal = async (user) => {
  activeDropdownId.value = null;
  userLogs.value = [];
  showLogModal.value = true;
  try {
    const response = await axios.get(`/api/super-admin/users/${user.id}/activity-log`);
    userLogs.value = response.data;
  } catch {
    if (window.notify) window.notify.error('Log Error', 'Failed to fetch activity.');
  }
};

const saveUser = async () => {
  isProcessing.value = true;
  try {
    if (editingUser.value) {
      await axios.put(`/api/super-admin/users/${editingUser.value.id}`, userForm.value);
      if (window.notify) window.notify.success('User Updated', 'Profile details updated.');
    } else {
      await axios.post('/api/super-admin/users', userForm.value);
      if (window.notify) window.notify.success('User Created', 'New user added successfully.');
    }
    showUserModal.value = false;
    fetchData();
  } catch (error) {
    if (window.notify) window.notify.error('Error', error.response?.data?.message || 'Action failed.');
  } finally {
    isProcessing.value = false;
  }
};

const sendInvites = async () => {
  isProcessing.value = true;
  try {
    await axios.post('/api/super-admin/users/invite', { emails: inviteForm.value.emails });
    if (window.notify) window.notify.success('Invites Sent', 'Invitations queued for delivery.');
    showInviteModal.value = false;
  } catch (error) {
    if (window.notify) window.notify.error('Error', 'Failed to send invites.');
  } finally {
    isProcessing.value = false;
  }
};

const confirmImpersonate = (user) => {
  modalConfig.value = {
    title: 'Impersonate User',
    message: `View system as ${user.name}? This will be logged.`,
    confirmText: 'Impersonate',
    type: 'primary',
    action: 'impersonate',
    user: user
  };
  isModalOpen.value = true;
  activeDropdownId.value = null;
};

const confirmResetPassword = (user) => {
  modalConfig.value = {
    title: 'Reset Password',
    message: `Send password reset link to ${user.email}?`,
    confirmText: 'Send Link',
    type: 'primary',
    action: 'reset-password',
    user: user
  };
  isModalOpen.value = true;
  activeDropdownId.value = null;
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
              <div
                class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center border border-white/20 shadow-inner">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
              </div>
              <span class="text-sm font-semibold text-white/60 mb-2 block">Admin Dashboard</span>
            </div>
            <h1 class="text-4xl font-bold text-white tracking-tight mb-4">
              User <span class="text-agri-lush">Management</span>
            </h1>
            <p class="text-white/80 font-medium max-w-2xl text-lg leading-relaxed">Manage users, roles, and platform
              access.</p>
          </div>
          <div class="flex items-center space-x-4">
            <div
              class="px-8 py-4 bg-white/5 rounded-[2rem] border border-white/10 backdrop-blur-2xl flex items-center space-x-4 shadow-2xl">
              <div class="h-3 w-3 rounded-full bg-agri-lush animate-pulse shadow-[0_0_15px_rgba(132,204,22,1)]"></div>
              <div>
                <span class="block text-xs text-white/60 mb-0.5">System Status</span>
                <span class="text-sm font-bold text-white">Active</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Apex Telemetry Grid: Top-Most Executive Discovery -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-4 mt-12">
          <div v-for="card in stats_cards" :key="card.label"
            class="bg-white/10 backdrop-blur-2xl p-4 rounded-3xl border border-white/20 transition-all duration-500 group cursor-default relative overflow-hidden shadow-xl hover:-translate-y-1 hover:border-white/40 hover:shadow-agri-lush/10">
            <div
              class="absolute -top-10 -right-10 w-24 h-24 bg-agri-lush/5 rounded-full blur-xl transition-all duration-700 group-hover:bg-agri-lush/15 group-hover:scale-110">
            </div>

            <div class="relative z-10 flex flex-col items-start space-y-4">
              <div
                class="p-2.5 rounded-xl bg-agri-lush text-white border border-white/20 transition-all duration-700 shadow-lg group-hover:scale-105">
                <svg v-if="card.icon === 'UsersIcon'" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <svg v-if="card.icon === 'StoreIcon'" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <svg v-if="card.icon === 'ShoppingBagIcon'" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <svg v-if="card.icon === 'CurrencyIcon'" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <svg v-if="card.icon === 'TradersIcon'" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <svg v-if="card.icon === 'ShieldIcon'" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <svg v-if="card.icon === 'BanIcon'" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
              </div>
              <div>
                <h4 class="text-xs font-medium text-white/70 mb-1">{{
                  card.label }}</h4>
                <p
                  class="text-2xl font-bold text-white tracking-tight group-hover:scale-[1.05] transition-transform duration-500 origin-left">
                  {{ card.value }}</p>
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
              <svg class="h-5 w-5 text-gray-400 group-focus-within:text-agri-forest transition-colors duration-300"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input v-model="filters.q" type="text" placeholder="Search by name, email or ID..."
              class="block w-full pl-12 pr-4 py-4 bg-gray-50/50 border border-gray-100 rounded-3xl text-sm font-medium placeholder-gray-400 focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest/50 focus:bg-white transition-all duration-500">
          </div>

          <!-- Horizontal Filters -->
          <div class="flex flex-wrap items-center gap-3">
            <!-- Role Filter -->
            <div class="relative min-w-[160px]">
              <select v-model="filters.role"
                class="w-full pl-11 pr-8 py-3.5 bg-gray-50/50 border border-gray-100 rounded-2xl text-sm font-medium text-gray-600 appearance-none focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest/30 focus:bg-white transition-all duration-300 cursor-pointer">
                <option value="">All Roles</option>
                <option v-for="role in roles" :key="role.slug" :value="role.slug">
                  {{ role.name }}
                </option>
              </select>
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
              </div>
              <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-300">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>

            <!-- Status Filter -->
            <div class="relative min-w-[160px]">
              <select v-model="filters.status"
                class="w-full pl-11 pr-8 py-3.5 bg-gray-50/50 border border-gray-100 rounded-2xl text-sm font-medium text-gray-600 appearance-none focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest/30 focus:bg-white transition-all duration-300 cursor-pointer">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="suspended">Suspended</option>
                <option value="pending">Pending</option>
              </select>
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-300">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>

            <!-- Location Filter -->
            <div class="relative flex-1 sm:flex-none sm:min-w-[200px]">
              <input v-model="filters.location" type="text" placeholder="Filter by Location..."
                class="w-full pl-11 pr-4 py-3.5 bg-gray-50/50 border border-gray-100 rounded-2xl text-sm font-medium text-gray-600 placeholder-gray-400 focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest/30 focus:bg-white transition-all duration-300">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center space-x-2">
            </div>

            <button @click="resetFilters"
              class="px-5 py-3.5 bg-white border border-gray-100 rounded-2xl text-xs font-bold text-red-500 hover:bg-red-50 hover:border-red-100 hover:text-red-600 transition-all duration-300 active:scale-95 translate-all shadow-sm">
              Reset
            </button>

            <button @click="showAdvancedFilters = !showAdvancedFilters"
              :class="[showAdvancedFilters ? 'bg-gray-900 text-white border-gray-900 shadow-lg' : 'bg-white text-gray-400 border-gray-100']"
              class="p-3.5 rounded-2xl border transition-all duration-500 hover:shadow-md active:scale-95">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Extended Protocols -->
      <transition enter-active-class="transition duration-500 ease-out"
        enter-from-class="transform -translate-y-4 opacity-0" enter-to-class="transform translate-y-0 opacity-100"
        leave-active-class="transition duration-300 ease-in" leave-from-class="transform translate-y-0 opacity-100"
        leave-to-class="transform -translate-y-4 opacity-0">
        <div v-show="showAdvancedFilters" class="mt-6 pt-6 border-t border-gray-50 flex flex-wrap gap-8">
          <div class="min-w-[300px]">
            <label class="block text-xs font-bold text-gray-400 mb-3 pl-1">Verification Status</label>
            <div class="flex items-center space-x-2">
              <select v-model="filters.verified"
                class="flex-1 bg-gray-50/50 border border-gray-100 rounded-2xl px-5 py-3.5 text-sm font-medium text-gray-600 focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest/30 transition-all">
                <option value="">All Users</option>
                <option value="true">Verified Users</option>
                <option value="false">Unverified Users</option>
              </select>
            </div>
          </div>
        </div>
      </transition>
    </div>

    <div class="container mx-auto px-8 mt-12 mb-20">
      <div class="bg-white rounded-[3rem] border border-gray-100 overflow-hidden shadow-2xl shadow-gray-200/40">
        <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
          <h2 class="text-sm font-bold text-gray-900">User List</h2>
          <div class="flex items-center space-x-4">
            <!-- User Actions -->
            <button @click="openAddModal"
              class="px-5 py-2.5 bg-agri-forest text-white text-xs font-bold rounded-xl hover:bg-agri-lush transition-colors shadow-lg shadow-agri-forest/20 flex items-center">
              <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              Add User
            </button>
            <button @click="openInviteModal"
              class="px-5 py-2.5 bg-white text-gray-700 border border-gray-200 text-xs font-bold rounded-xl hover:bg-gray-50 transition-colors flex items-center">
              <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              Invite
            </button>
            <div class="h-6 w-px bg-gray-200 mx-2"></div>
            <!-- relocated Sovereign Export -->
            <div class="relative group">
              <button @click="showExportDropdown = !showExportDropdown"
                class="flex items-center space-x-2 px-4 py-2 bg-agri-forest/10 text-agri-forest hover:bg-agri-forest hover:text-white rounded-xl text-xs font-bold transition-all duration-300 active:scale-95">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span>Export</span>
              </button>

              <Transition enter-active-class="transition duration-200 ease-out"
                enter-from-class="transform scale-95 opacity-0 -translate-y-2"
                enter-to-class="transform scale-100 opacity-100 translate-y-0"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="transform scale-100 opacity-100 translate-y-0"
                leave-to-class="transform scale-95 opacity-0 -translate-y-2">
                <div v-if="showExportDropdown"
                  class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-2xl border border-gray-100 z-[70] py-2 overflow-hidden">
                  <button @click="triggerExport('excel')"
                    class="w-full flex items-center px-4 py-3 text-xs font-bold text-gray-600 hover:bg-gray-50 transition-colors space-x-3">
                    <div class="w-2 h-2 rounded-full bg-green-500"></div>
                    <span>Excel</span>
                  </button>
                  <button @click="triggerExport('csv')"
                    class="w-full flex items-center px-4 py-3 text-xs font-bold text-gray-600 hover:bg-gray-50 transition-colors space-x-3">
                    <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                    <span>CSV</span>
                  </button>
                  <button @click="triggerExport('pdf')"
                    class="w-full flex items-center px-4 py-3 text-xs font-bold text-gray-600 hover:bg-gray-50 transition-colors space-x-3">
                    <div class="w-2 h-2 rounded-full bg-red-500"></div>
                    <span>PDF</span>
                  </button>
                </div>
              </Transition>
            </div>

            <div class="flex items-center space-x-2 bg-white px-3 py-1.5 rounded-xl border border-gray-100 shadow-sm">
              <div class="w-2 h-2 rounded-full bg-agri-forest"></div>
              <span class="text-xs font-bold text-gray-500">{{ users.length }} Users</span>
            </div>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-gray-50/50 text-left border-b border-gray-100">
                <th class="px-8 py-6 w-16">
                  <input type="checkbox" :checked="selectedIds.length > 0 && selectedIds.length === users.length"
                    @change="toggleSelectAll"
                    class=" form-control rounded-lg w-4 h-4  border-2 border-gray-200 text-agri-forest focus:ring-agri-forest/20 focus:ring-offset-0 transition-all cursor-pointer bg-white accent-agri-forest shadow-sm">
                </th>
                <th class="px-8 py-6 text-xs font-bold text-gray-400 uppercase tracking-wider">User</th>
                <th class="px-8 py-6 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">
                  Location</th>
                <th class="px-8 py-6 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">
                  Role</th>
                <th class="px-8 py-6 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">
                  Status</th>
                <th class="px-8 py-6 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">
                  Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-if="loading">
                <td colspan="6" class="py-24 text-center">
                  <div class="relative inline-block">
                    <svg class="animate-spin h-12 w-12 text-agri-forest/20 mx-auto" xmlns="http://www.w3.org/2000/svg"
                      fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                      </path>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                      <div class="h-2 w-2 rounded-full bg-agri-forest animate-ping"></div>
                    </div>
                  </div>
                  <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mt-6">Loading Users...</p>
                </td>
              </tr>
              <tr v-else-if="users.length === 0" class="group transition-colors duration-500">
                <td colspan="6" class="py-32 text-center bg-gray-50/20">
                  <div class="relative inline-block">
                    <div
                      class="absolute -inset-8 bg-agri-forest/5 rounded-full blur-3xl group-hover:bg-agri-forest/10 transition-all duration-1000">
                    </div>
                    <div
                      class="relative h-24 w-24 bg-white border border-gray-100 rounded-[2.5rem] flex items-center justify-center text-gray-200 shadow-xl shadow-gray-200/50 mx-auto mb-8 group-hover:scale-110 group-hover:border-agri-forest/20 transition-all duration-700">
                      <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="0.8"
                          d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />
                      </svg>
                    </div>
                  </div>
                  <h3 class="text-xl font-bold text-gray-900 tracking-tight">No Results Found</h3>
                  <p class="text-sm font-medium text-gray-400 mt-2 max-w-[320px] mx-auto leading-relaxed">
                    No users match your search criteria.
                  </p>
                  <button @click="resetFilters"
                    class="mt-10 px-10 py-4 bg-white border border-gray-100 rounded-3xl text-sm font-bold text-agri-forest shadow-lg shadow-gray-200/50 hover:bg-agri-forest hover:text-white hover:border-agri-forest hover:-translate-y-1 transition-all duration-700 active:scale-95">
                    Reset Filters
                  </button>
                </td>
              </tr>
              <tr v-else v-for="user in users" :key="user.id"
                class="hover:bg-gray-50/50 transition-all duration-500 group/row"
                :class="{ 'bg-agri-forest/[0.03]': selectedIds.includes(user.id) }">
                <td class="px-8 py-7">
                  <input type="checkbox" :value="user.id" v-model="selectedIds"
                    class="w-4 h-5 rounded-lg border-2 border-gray-200 text-agri-forest focus:ring-agri-forest/20 focus:ring-offset-0 transition-all cursor-pointer bg-white accent-agri-forest shadow-sm hover:border-agri-forest/50">
                </td>
                <td class="px-8 py-7">
                  <div class="flex items-center space-x-5">
                    <div
                      class="w-14 h-14 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400 font-black uppercase text-lg shadow-sm group-hover/row:border-agri-forest/30 group-hover/row:bg-white transition-all duration-500">
                      {{ user.name.charAt(0) }}
                    </div>
                    <div>
                      <p class="font-bold text-gray-900 text-sm tracking-tight leading-none">{{ user.name }}</p>
                      <p class="text-xs text-gray-400 font-medium mt-1.5">{{ user.email }}
                      </p>
                    </div>
                  </div>
                </td>
                <td class="px-8 py-6 text-center">
                  <span class="text-xs font-bold text-gray-400">{{ user.location ||
                    'Global' }}</span>
                </td>
                <td class="px-8 py-6 text-center">
                  <span class="px-4 py-1.5 rounded-xl text-xs font-bold border shadow-sm" :class="roleClass(user.role)">
                    {{ user.role?.replace('_', ' ') }}
                  </span>
                </td>
                <td class="px-8 py-6 text-center">
                  <div class="flex items-center justify-center">
                    <template v-if="user.status === 'suspended'">
                      <div class="w-1.5 h-1.5 rounded-full mr-2 bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.5)]"></div>
                      <span class="text-xs font-bold text-red-500">Suspended</span>
                    </template>
                    <template v-else>
                      <div class="w-1.5 h-1.5 rounded-full mr-3"
                        :class="user.email_verified_at ? 'bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.5)]' : 'bg-yellow-500 shadow-[0_0_8px_rgba(234,179,8,0.5)]'">
                      </div>
                      <span class="text-xs font-bold text-gray-500">{{
                        user.email_verified_at ? 'Verified' : 'Pending' }}</span>
                    </template>
                  </div>
                </td>
                <td class="px-8 py-4 text-right">
                  <div class="flex items-center justify-end">
                    <div class="relative dropdown-trigger">
                      <button @click.stop="toggleDropdown(user.id)"
                        class="p-2.5 text-gray-400 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 rounded-xl transition-all duration-300 flex items-center space-x-2 group"
                        :class="{ 'bg-gray-900 text-white': activeDropdownId === user.id }">
                        <span class="text-[10px] font-black uppercase tracking-widest pl-1">Actions</span>
                        <svg class="w-4 h-4 transition-transform duration-300"
                          :class="{ 'rotate-180': activeDropdownId === user.id }" fill="none" viewBox="0 0 24 24"
                          stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                      </button>

                      <Transition enter-active-class="transition duration-200 ease-out"
                        enter-from-class="transform scale-95 opacity-0 -translate-y-2"
                        enter-to-class="transform scale-100 opacity-100 translate-y-0"
                        leave-active-class="transition duration-150 ease-in"
                        leave-from-class="transform scale-100 opacity-100 translate-y-0"
                        leave-to-class="transform scale-95 opacity-0 -translate-y-2">
                        <div v-if="activeDropdownId === user.id"
                          class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-2xl border border-gray-100 z-[60] py-2 overflow-hidden">
                          <!-- Global Inspection -->
                          <button @click="openInspector(user); activeDropdownId = null"
                            class="w-full flex items-center px-5 py-3.5 text-xs font-bold text-gray-600 hover:bg-gray-50 transition-colors space-x-3">
                            <svg class="w-4.5 h-4.5 text-gray-400" fill="none" viewBox="0 0 24 24"
                              stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <span>View Details</span>
                          </button>

                          <div class="h-px bg-gray-50 my-1 mx-4"></div>

                          <!-- Edit Profile -->
                          <button @click="openEditModal(user)"
                            class="w-full flex items-center px-5 py-2.5 text-xs font-bold text-gray-600 hover:bg-gray-50 transition-colors space-x-3">
                            <svg class="w-4.5 h-4.5 text-gray-400" fill="none" viewBox="0 0 24 24"
                              stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span>Edit User</span>
                          </button>

                          <!-- Activity Log -->
                          <button @click="openLogModal(user)"
                            class="w-full flex items-center px-5 py-2.5 text-xs font-bold text-gray-600 hover:bg-gray-50 transition-colors space-x-3">
                            <svg class="w-4.5 h-4.5 text-gray-400" fill="none" viewBox="0 0 24 24"
                              stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            <span>Activity Log</span>
                          </button>

                          <!-- Impersonate -->
                          <button @click="confirmImpersonate(user)"
                            class="w-full flex items-center px-5 py-2.5 text-xs font-bold text-gray-600 hover:bg-gray-50 transition-colors space-x-3">
                            <svg class="w-4.5 h-4.5 text-gray-400" fill="none" viewBox="0 0 24 24"
                              stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <span>Impersonate</span>
                          </button>

                          <!-- Reset Password -->
                          <button @click="confirmResetPassword(user)"
                            class="w-full flex items-center px-5 py-2.5 text-xs font-bold text-gray-600 hover:bg-gray-50 transition-colors space-x-3">
                            <svg class="w-4.5 h-4.5 text-gray-400" fill="none" viewBox="0 0 24 24"
                              stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                            <span>Reset Password</span>
                          </button>

                          <div class="h-px bg-gray-50 my-1 mx-4"></div>

                          <!-- Verify -->
                          <button @click="openVerifyModal(user); activeDropdownId = null"
                            :disabled="user.status === 'active' || !!user.email_verified_at"
                            class="w-full flex items-center px-5 py-3.5 text-xs font-bold transition-colors space-x-3 group/item disabled:opacity-40 disabled:cursor-not-allowed"
                            :class="user.status === 'active' || !!user.email_verified_at ? 'text-gray-300' : 'text-agri-forest hover:bg-agri-forest/5'">
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="flex flex-col items-start translate-y-0.5">
                              <span>Verify User</span>
                              <span v-if="user.status === 'active'"
                                class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter leading-none mt-1">Active</span>
                            </div>
                          </button>

                          <!-- Promote -->
                          <button v-if="!isSuperAdmin(user) && authStore.can('manage_users')"
                            @click="openPromoteModal(user.id); activeDropdownId = null"
                            class="w-full flex items-center px-5 py-3.5 text-xs font-bold text-agri-earth hover:bg-agri-earth/5 transition-colors space-x-3 group/item">
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            <div class="flex flex-col items-start translate-y-0.5">
                              <span>Promote User</span>
                              <span
                                class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter leading-none mt-1">Make
                                Super
                                Admin</span>
                            </div>
                          </button>

                          <!-- Suspend -->
                          <button
                            v-if="!isSuperAdmin(user) && user.status !== 'suspended' && authStore.can('manage_users')"
                            @click="openSuspendModal(user); activeDropdownId = null"
                            class="w-full flex items-center px-5 py-3.5 text-xs font-bold text-red-600 hover:bg-red-50 transition-colors space-x-3 group/item">
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                            <div class="flex flex-col items-start translate-y-0.5">
                              <span>Suspend User</span>
                              <span
                                class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter leading-none mt-1">Block
                                Access</span>
                            </div>
                          </button>

                          <!-- Restore -->
                          <button v-if="user.status === 'suspended'"
                            @click="openActivateModal(user); activeDropdownId = null"
                            class="w-full flex items-center px-5 py-3.5 text-xs font-bold text-agri-forest hover:bg-agri-forest/5 transition-colors space-x-3 group/item">
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="flex flex-col items-start translate-y-0.5">
                              <span>Restore Access</span>
                              <span
                                class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter leading-none mt-1">Unblock
                                User</span>
                            </div>
                          </button>

                          <!-- Delete -->
                          <button v-if="!isSuperAdmin(user) && authStore.can('manage_users')"
                            @click="openDeleteModal(user); activeDropdownId = null"
                            class="w-full flex items-center px-5 py-3.5 text-xs font-bold text-red-600 hover:bg-red-50 transition-colors space-x-3 group/item">
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <div class="flex flex-col items-start translate-y-0.5">
                              <span>Delete User</span>
                              <span
                                class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter leading-none mt-1">Permanently</span>
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
      <Transition enter-active-class="transition-opacity duration-300 ease-out"
        leave-active-class="transition-opacity duration-300 ease-in" enter-from-class="opacity-0"
        enter-to-class="opacity-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="isDrawerOpen" class="fixed inset-0 z-[100] overflow-hidden">
          <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="closeInspector"></div>

          <Transition enter-active-class="transform transition-transform duration-500 ease-out"
            leave-active-class="transform transition-transform duration-500 ease-in" enter-from-class="translate-x-full"
            enter-to-class="translate-x-0" leave-from-class="translate-x-0" leave-to-class="translate-x-full" appear>
            <div v-if="isDrawerOpen"
              class="absolute inset-y-0 right-0 max-w-xl w-full bg-white shadow-2xl flex flex-col">
              <!-- Drawer Header: Sovereign Identity -->
              <div class="px-8 py-16 border-b border-white/10 bg-agri-forest sticky top-0 z-20 overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32 blur-3xl"></div>

                <div class="relative z-10 flex items-start justify-between">
                  <div class="flex items-center space-x-8">
                    <div
                      class="w-24 h-24 rounded-[2.5rem] bg-white/10 flex items-center justify-center text-white font-bold text-4xl border border-white/20 shadow-2xl backdrop-blur-md uppercase">
                      {{ inspectedUser?.name?.charAt(0) }}
                    </div>
                    <div>
                      <div class="flex items-center space-x-2 mb-2">
                        <span
                          class="px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider bg-white/10 text-white/70 border border-white/10">User
                          Details</span>
                      </div>
                      <h2 class="text-4xl font-bold text-white tracking-tight">{{ inspectedUser?.name }}</h2>
                      <div class="flex items-center mt-4 space-x-3">
                        <span
                          class="px-4 py-1.5 rounded-xl text-xs font-bold uppercase tracking-wider border border-white/10 bg-white/5 text-white/90">
                          {{ inspectedUser?.role?.replace('_', ' ') }}
                        </span>
                        <div v-if="inspectedUser?.status === 'suspended'"
                          class="flex items-center px-4 py-1.5 bg-red-500/20 border border-red-500/30 rounded-xl">
                          <div class="w-2 h-2 rounded-full mr-2 bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.8)]"></div>
                          <span class="text-xs font-bold text-red-200 uppercase tracking-wider">Suspended</span>
                        </div>
                        <div v-else class="flex items-center px-4 py-1.5 bg-white/5 border border-white/10 rounded-xl">
                          <div class="w-2 h-2 rounded-full mr-2"
                            :class="inspectedUser?.email_verified_at ? 'bg-agri-lush shadow-[0_0_8px_rgba(132,204,22,0.8)]' : 'bg-yellow-400 shadow-[0_0_8px_rgba(250,204,21,0.8)]'">
                          </div>
                          <span class="text-xs font-bold text-white/70 uppercase tracking-wider">{{
                            inspectedUser?.email_verified_at ? 'Verified' : 'Unverified' }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <button @click="closeInspector"
                    class="p-4 bg-white/10 rounded-3xl text-white/60 hover:text-white hover:bg-white/20 transition-all group backdrop-blur-md border border-white/10">
                    <svg class="w-6 h-6 group-hover:rotate-90 transition-transform duration-700" fill="none"
                      stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Drawer Content -->
              <div class="flex-1 overflow-y-auto p-8 space-y-12">
                <!-- Node Telemetry Section -->
                <div>
                  <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-6">User Information</h4>
                  <div class="grid grid-cols-1 gap-4">
                    <div v-for="(val, label) in {
                      'User ID': '#' + inspectedUser?.id,
                      'Email': inspectedUser?.email,
                      'Location': inspectedUser?.location || 'Global',
                      'Joined Date': new Date(inspectedUser?.created_at).toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' })
                    }" :key="label"
                      class="flex justify-between items-center py-4 px-6 bg-gray-50/50 rounded-2xl border border-gray-50 transition-colors hover:bg-white hover:border-agri-forest/10 group">
                      <span
                        class="text-xs font-bold text-gray-400 uppercase tracking-tighter group-hover:text-agri-forest">{{
                          label }}</span>
                      <span class="text-sm font-bold text-gray-900">{{ val }}</span>
                    </div>
                  </div>
                </div>

                <!-- Compliance & Activity Section -->
                <div>
                  <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">Activity Status
                  </h4>
                  <div class="bg-gray-50 rounded-[2.5rem] p-10 border border-gray-100 relative overflow-hidden group">
                    <div
                      class="absolute top-0 right-0 w-32 h-32 bg-agri-forest/5 rounded-full -mr-16 -mt-16 transition-transform duration-700 group-hover:scale-150">
                    </div>
                    <ul class="space-y-6 relative z-10">
                      <li v-for="item in [
                        { label: 'Role Verified', status: 'Authorized' },
                        { label: 'Verification', status: inspectedUser?.email_verified_at ? 'Active' : 'Pending' },
                        { label: 'Trade History', status: 'Clean' }
                      ]" :key="item.label" class="flex items-center justify-between">
                        <div class="flex items-center text-xs font-bold text-gray-600 uppercase tracking-wide">
                          <svg class="w-4 h-4 text-agri-forest mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                          </svg>
                          {{ item.label }}
                        </div>
                        <span class="text-xs font-bold text-agri-forest/50 uppercase">{{ item.status }}</span>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>

              <!-- Drawer Footer -->
              <div class="p-8 border-t border-gray-100 bg-white space-y-4">
                <div class="flex space-x-4">
                  <button v-if="inspectedUser?.status === 'suspended'" @click="openActivateModal(inspectedUser)"
                    class="flex-1 bg-agri-forest text-white py-5 rounded-2xl font-bold uppercase text-[10px] tracking-widest hover:bg-agri-lush transition-all shadow-xl shadow-agri-forest/20 flex items-center justify-center">
                    Restore Access
                  </button>
                  <button v-else @click="openSuspendModal(inspectedUser)"
                    :disabled="isSuperAdmin(inspectedUser) || !authStore.can('manage_users')"
                    class="flex-1 bg-red-50 text-red-600 py-5 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-red-100 transition-all border border-red-100 flex items-center justify-center disabled:opacity-40 disabled:cursor-not-allowed">
                    Revoke Access
                  </button>
                </div>
                <button @click="openVerifyModal(inspectedUser)"
                  :disabled="inspectedUser?.status === 'active' || !!inspectedUser?.email_verified_at"
                  class="w-full py-5 bg-white border border-gray-100 text-gray-400 hover:text-agri-forest rounded-2xl font-bold uppercase text-[10px] tracking-widest transition-all shadow-sm disabled:opacity-20 disabled:cursor-not-allowed">
                  {{ inspectedUser?.status === 'active' || !!inspectedUser?.email_verified_at ? 'User Verified' :
                    'Verify User' }}
                </button>
                <button v-if="!isSuperAdmin(inspectedUser) && authStore.can('manage_users')"
                  @click="openPromoteModal(inspectedUser?.id)"
                  class="w-full py-4 bg-gray-50 text-gray-600 hover:bg-agri-earth hover:text-white rounded-2xl font-bold uppercase text-[10px] tracking-widest transition-all">
                  Promote User
                </button>
                <button v-if="!isSuperAdmin(inspectedUser) && authStore.can('manage_users')"
                  @click="openDeleteModal(inspectedUser)"
                  class="w-full py-4 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-2xl font-bold uppercase text-[10px] tracking-widest transition-all mt-4 border border-red-100 hover:border-red-600 shadow-sm">
                  Delete User
                </button>
              </div>
            </div>
          </Transition>
        </div>
      </Transition>
    </Teleport>

    <!-- Governance Action Modal -->
    <Modal :is-open="isModalOpen" :title="modalConfig.title" :message="modalConfig.message"
      :confirm-text="modalConfig.confirmText" :type="modalConfig.type" :loading="isProcessing"
      @confirm="handleModalConfirm" @cancel="isModalOpen = false" />
    <!-- Add/Edit User Modal -->
    <Modal :is-open="showUserModal" :title="editingUser ? 'Edit User' : 'Add New User'"
      :confirm-text="editingUser ? 'Save Changes' : 'Create User'" :loading="isProcessing"
      @cancel="showUserModal = false" form-id="user-form">
      <form id="user-form" @submit.prevent="saveUser" class="space-y-6">
        <div class="grid grid-cols-1 gap-6">
          <div>
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">Full Name</label>
            <input v-model="userForm.name" type="text"
              class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 font-bold text-gray-900 focus:ring-2 focus:ring-agri-forest/20 focus:border-agri-forest transition-all"
              required>
          </div>
          <div>
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">Email Address</label>
            <input v-model="userForm.email" type="email"
              class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 font-bold text-gray-900 focus:ring-2 focus:ring-agri-forest/20 focus:border-agri-forest transition-all"
              required>
          </div>
          <div>
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">Role</label>
            <select v-model="userForm.role" required
              class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 font-bold text-gray-900 focus:ring-2 focus:ring-agri-forest/20 focus:border-agri-forest transition-all">
              <option v-for="role in roles" :key="role.slug" :value="role.slug">
                {{ role.name }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">Location</label>
            <input v-model="userForm.location" type="text"
              class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 font-bold text-gray-900 focus:ring-2 focus:ring-agri-forest/20 focus:border-agri-forest transition-all">
          </div>
        </div>
      </form>
    </Modal>

    <!-- Invite Modal -->
    <Modal :is-open="showInviteModal" title="Invite Users" confirm-text="Send Invites" :loading="isProcessing"
      @cancel="showInviteModal = false" form-id="invite-form">
      <form id="invite-form" @submit.prevent="sendInvites" class="space-y-4">
        <p class="text-sm text-gray-500 font-medium">Enter email addresses separated by commas.</p>
        <textarea v-model="inviteForm.emails" rows="4" required
          class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 font-medium text-gray-900 focus:ring-2 focus:ring-agri-forest/20 focus:border-agri-forest transition-all"
          placeholder="email@example.com, another@example.com"></textarea>
      </form>
    </Modal>

    <!-- Activity Log Modal -->
    <Modal :is-open="showLogModal" title="User Activity Log" :hide-footer="true" @cancel="showLogModal = false"
      max-width-class="max-w-2xl">
      <div class="space-y-4 max-h-[60vh] overflow-y-auto">
        <div v-if="userLogs.length === 0" class="text-center py-8 text-gray-400 font-bold text-sm">No activity recorded.
        </div>
        <div v-else class="space-y-3">
          <div v-for="(log, index) in userLogs" :key="index"
            class="flex items-start p-4 bg-gray-50 rounded-xl border border-gray-100">
            <div class="flex-shrink-0 mt-1">
              <div class="w-2 h-2 rounded-full" :class="log.type === 'danger' ? 'bg-red-500' : 'bg-agri-forest'"></div>
            </div>
            <div class="ml-4 flex-1">
              <p class="text-xs font-bold text-gray-900 uppercase tracking-wide">{{ log.action }}</p>
              <p class="text-sm text-gray-600 font-medium mt-0.5">{{ log.description }}</p>
              <p class="text-[10px] text-gray-400 font-medium mt-1">{{ log.timestamp }}</p>
            </div>
          </div>
        </div>
      </div>
    </Modal>

    <!-- Sovereign Action Bar: Multi-Node Governance -->
    <Transition enter-active-class="transition duration-700 cubic-bezier(0.16, 1, 0.3, 1)"
      enter-from-class="translate-y-full opacity-0 scale-95" enter-to-class="translate-y-0 opacity-100 scale-100"
      leave-active-class="transition duration-500 cubic-bezier(0.7, 0, 0.84, 0)"
      leave-from-class="translate-y-0 opacity-100 scale-100" leave-to-class="translate-y-full opacity-0 scale-95">
      <div v-if="selectedIds.length > 0" class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[80] w-full max-w-5xl px-6">
        <div
          class="bg-gray-900/95 backdrop-blur-3xl px-8 py-5 rounded-[3rem] border border-white/20 shadow-[0_50px_100px_rgba(0,0,0,0.6)] flex items-center justify-between relative overflow-hidden group">

          <!-- Animated Mesh Glows -->
          <div
            class="absolute -top-24 -right-24 w-64 h-64 bg-agri-forest/20 rounded-full blur-[80px] group-hover:bg-agri-forest/30 transition-all duration-1000">
          </div>
          <div
            class="absolute -bottom-24 -left-24 w-64 h-64 bg-agri-earth/20 rounded-full blur-[80px] group-hover:bg-agri-earth/30 transition-all duration-1000">
          </div>

          <div class="relative z-10 flex items-center space-x-8">
            <div class="flex items-center space-x-3">
              <div class="flex -space-x-4">
                <div v-for="i in Math.min(selectedIds.length, 3)" :key="i"
                  class="w-10 h-10 rounded-full bg-agri-forest border-2 border-gray-900 flex items-center justify-center text-[10px] font-bold text-white shadow-xl">
                  {{users.find(u => u.id === selectedIds[i - 1])?.name.charAt(0) || 'N'}}
                </div>
                <div v-if="selectedIds.length > 3"
                  class="w-10 h-10 rounded-full bg-gray-800 border-2 border-gray-900 flex items-center justify-center text-[10px] font-bold text-gray-400 shadow-xl">
                  +{{ selectedIds.length - 3 }}
                </div>
              </div>
              <div class="h-10 w-px bg-white/10 mx-2"></div>
              <div>
                <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mb-0.5">Batch Actions
                </p>
                <h4 class="text-xl font-bold text-white tracking-tight leading-none">
                  {{ selectedIds.length }} User<span v-if="selectedIds.length > 1">s</span>
                  <span class="text-agri-lush animate-pulse ml-2 px-3 py-1 bg-agri-lush/10 rounded-lg">Selected</span>
                </h4>
              </div>
            </div>
          </div>

          <div class="relative z-10 flex items-center space-x-3">
            <!-- Verify Action -->
            <button @click="openBulkActionModal('verify')" :disabled="isProcessing"
              class="group/btn flex items-center space-x-3 px-6 py-4 bg-white/5 hover:bg-agri-forest text-white border border-white/10 hover:border-agri-forest rounded-2xl transition-all duration-500 disabled:opacity-50 active:scale-95 shadow-lg hover:shadow-agri-forest/40">
              <div
                class="w-8 h-8 rounded-xl bg-white/10 group-hover/btn:bg-white/20 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-agri-lush group-hover/btn:text-white" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <span class="text-[10px] font-bold uppercase tracking-widest">Verify Selected</span>
            </button>

            <!-- Suspend Action -->
            <button @click="openBulkActionModal('suspend')" :disabled="isProcessing"
              class="group/btn flex items-center space-x-3 px-6 py-4 bg-white/5 hover:bg-orange-600 text-white border border-white/10 hover:border-orange-600 rounded-2xl transition-all duration-500 disabled:opacity-50 active:scale-95 shadow-lg hover:shadow-orange-600/40">
              <div
                class="w-8 h-8 rounded-xl bg-white/10 group-hover/btn:bg-white/20 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-orange-400 group-hover/btn:text-white" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
              </div>
              <span class="text-[10px] font-bold uppercase tracking-widest">Suspend Selected</span>
            </button>

            <!-- Delete Action -->
            <button @click="openBulkActionModal('delete')" :disabled="isProcessing"
              class="group/btn flex items-center space-x-3 px-6 py-4 bg-red-500/10 hover:bg-red-600 text-white border border-red-500/20 hover:border-red-600 rounded-2xl transition-all duration-500 disabled:opacity-50 active:scale-95 shadow-lg hover:shadow-red-600/40">
              <div
                class="w-8 h-8 rounded-xl bg-red-500/20 group-hover/btn:bg-white/20 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-red-400 group-hover/btn:text-white" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </div>
              <span class="text-[10px] font-bold uppercase tracking-widest">Delete Selected</span>
            </button>

            <div class="h-10 w-px bg-white/10 mx-2"></div>

            <button @click="selectedIds = []"
              class="w-14 h-14 flex items-center justify-center bg-white/5 hover:bg-white/10 text-white/40 hover:text-white rounded-[1.25rem] border border-white/10 transition-all duration-500 group-hover:rotate-90">
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>
