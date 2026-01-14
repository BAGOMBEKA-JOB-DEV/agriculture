<template>
  <div class="min-h-screen bg-gray-50/50 pb-12">
    <!-- Components -->
    <Notification ref="notificationRef" />
    
    <!-- Add/Edit User Modal -->
    <Modal
      :is-open="showUserModal"
      :title="editingUser ? 'Update Identity Details' : 'Register New Identity'"
      :confirm-text="editingUser ? 'Save Changes' : 'Create Credentials'"
      :loading="modalLoading"
      @confirm="saveUser"
      @cancel="closeModals"
      max-width-class="max-w-2xl"
    >
        <form @submit.prevent="saveUser" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Legal Name</label>
                    <input v-model="userForm.name" type="text" class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 font-bold text-gray-900 focus:ring-2 focus:ring-agri-forest/20 focus:border-agri-forest transition-all" required>
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Email Address</label>
                    <input v-model="userForm.email" type="email" class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 font-bold text-gray-900 focus:ring-2 focus:ring-agri-forest/20 focus:border-agri-forest transition-all" required>
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">System Role</label>
                    <select v-model="userForm.role" class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 font-bold text-gray-900 focus:ring-2 focus:ring-agri-forest/20 focus:border-agri-forest transition-all">
                        <option v-for="role in roles" :key="role.slug" :value="role.slug">
                            {{ role.name }}
                        </option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Jurisdiction/Location</label>
                    <input v-model="userForm.location" type="text" class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 font-bold text-gray-900 focus:ring-2 focus:ring-agri-forest/20 focus:border-agri-forest transition-all">
                </div>
            </div>
        </form>
    </Modal>

    <!-- Invite User Modal -->
    <Modal
      :is-open="showInviteModal"
      title="Issue Access Invitations"
      confirm-text="Send Invites"
      :loading="modalLoading"
      @confirm="sendInvites"
      @cancel="closeModals"
    >
        <div class="space-y-4">
            <p class="text-sm text-gray-500 font-medium">Enter email addresses separated by commas to send bulk invitations.</p>
            <textarea v-model="inviteForm.emails" rows="4" class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 font-medium text-gray-900 focus:ring-2 focus:ring-agri-forest/20 focus:border-agri-forest transition-all" placeholder="email@example.com, another@example.com"></textarea>
        </div>
    </Modal>

    <!-- Activity Log Modal -->
    <Modal
      :is-open="showLogModal"
      title="Identity Activity Ledger"
      :hide-footer="true"
      @cancel="closeModals"
      max-width-class="max-w-3xl"
    >
        <div class="space-y-4">
            <div v-if="userLogs.length === 0" class="text-center py-8 text-gray-400 font-bold text-sm">No activity recorded for this identity.</div>
            <div v-else class="space-y-3">
                <div v-for="(log, index) in userLogs" :key="index" class="flex items-start p-3 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="flex-shrink-0 mt-1">
                        <div class="w-2 h-2 rounded-full" :class="log.type === 'danger' ? 'bg-red-500' : 'bg-agri-forest'"></div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-xs font-black text-gray-900 uppercase tracking-wide">{{ log.action }}</p>
                        <p class="text-sm text-gray-600 font-medium mt-0.5">{{ log.description }}</p>
                        <p class="text-[10px] text-gray-400 font-medium mt-1">{{ log.timestamp }}</p>
                    </div>
                </div>
            </div>
        </div>
    </Modal>

    <!-- Confirmation Modal (Generic) -->
    <Modal
        :is-open="showConfirmModal"
        :title="confirmTitle"
        :message="confirmMessage"
        :confirm-text="confirmActionText"
        :type="confirmType"
        :loading="modalLoading"
        @confirm="executeConfirmAction"
        @cancel="closeModals"
    />

    <!-- Header -->
    <div class="bg-white border-b border-gray-100 sticky top-0 z-30">
        <div class="container mx-auto px-6 py-6 md:py-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">
                       Identity <span class="text-agri-forest">Registry</span>
                    </h1>
                    <p class="text-gray-500 font-medium mt-1">Manage global participant credentials and access controls.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <button @click="openAddModal" class="px-5 py-2.5 bg-agri-forest text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-agri-lush transition-colors shadow-lg shadow-agri-forest/20 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                        Add Identity
                    </button>
                    <button @click="openInviteModal" class="px-5 py-2.5 bg-white text-gray-700 border border-gray-200 text-xs font-black uppercase tracking-widest rounded-xl hover:bg-gray-50 transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        Invite
                    </button>
                    <button @click="exportData" class="px-5 py-2.5 bg-white text-gray-700 border border-gray-200 text-xs font-black uppercase tracking-widest rounded-xl hover:bg-gray-50 transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        Export
                    </button>
                </div>
            </div>

            <!-- Toolbar & Filters -->
            <div class="mt-8 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center space-x-3 w-full md:w-auto">
                    <!-- Bulk Actions -->
                    <div v-if="selectedUsers.length > 0 && authStore.can('manage_users')" class="flex items-center space-x-2 animate-fade-in-up">
                        <span class="text-xs font-bold text-gray-500 bg-gray-100 px-3 py-1.5 rounded-lg">{{ selectedUsers.length }} Selected</span>
                        <div class="h-8 w-[1px] bg-gray-200 mx-2"></div>
                        <button @click="confirmBulkAction('activate')" class="text-xs font-bold text-green-600 hover:text-green-700 bg-green-50 hover:bg-green-100 px-3 py-1.5 rounded-lg transition-colors">Activate</button>
                        <button @click="confirmBulkAction('deactivate')" class="text-xs font-bold text-orange-600 hover:text-orange-700 bg-orange-50 hover:bg-orange-100 px-3 py-1.5 rounded-lg transition-colors">Deactivate</button>
                        <button @click="confirmBulkAction('delete')" class="text-xs font-bold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition-colors">Delete</button>
                    </div>
                </div>

                <div class="flex items-center space-x-4 w-full md:w-auto">
                     <div class="relative group">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 group-focus-within:text-agri-forest transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        <input v-model="searchQuery" type="text" placeholder="Search identities..." class="pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-sm font-medium focus:ring-2 focus:ring-agri-forest/20 focus:border-agri-forest transition-all outline-none w-full md:w-64">
                     </div>
                     <select v-model="roleFilter" class="px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold uppercase tracking-wide text-gray-600 focus:ring-2 focus:ring-agri-forest/20 focus:border-agri-forest outline-none cursor-pointer">
                        <option value="">All Roles</option>
                        <option v-for="role in roles" :key="role.slug" :value="role.slug">
                            {{ role.name }}
                        </option>
                     </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="container mx-auto px-6 py-8">
      <div class="bg-white rounded-[2rem] border border-gray-100 overflow-hidden shadow-sm">
        <div class="overflow-x-auto min-h-[500px]">
          <table class="w-full text-left">
            <thead>
              <tr class="bg-gray-50/50 border-b border-gray-100">
                <th class="px-6 py-5 w-16 text-center">
                    <input type="checkbox" :checked="selectAll" @change="toggleSelectAll" class="w-4 h-4 rounded border-gray-300 text-agri-forest focus:ring-agri-forest cursor-pointer">
                </th>
                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Identity</th>
                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Role</th>
                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Last Active</th>
                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Config</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-if="loading">
                  <td colspan="6" class="py-24 text-center">
                      <svg class="animate-spin h-8 w-8 text-agri-forest mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                  </td>
              </tr>
              <tr v-else-if="filteredUsers.length === 0">
                   <td colspan="6" class="py-24 text-center text-gray-500 font-medium">No identities found matching your criteria.</td>
              </tr>
              <tr v-else v-for="user in filteredUsers" :key="user.id" class="group hover:bg-agri-forest/[0.02] transition-colors">
                <td class="px-6 py-5 text-center">
                    <input type="checkbox" v-model="selectedUsers" :value="user.id" class="w-4 h-4 rounded border-gray-300 text-agri-forest focus:ring-agri-forest cursor-pointer">
                </td>
                <td class="px-6 py-5">
                  <div class="flex items-center">
                    <div :class="['w-10 h-10 rounded-xl flex items-center justify-center font-black mr-4 uppercase text-xs shadow-sm bg-white border overflow-hidden', getAvatarBorder(user.role)]">
                      <img v-if="user.avatar_url" :src="user.avatar_url" class="w-full h-full object-cover">
                      <span v-else :class="getAvatarText(user.role)">{{ user.name.charAt(0) }}</span>
                    </div>
                    <div>
                        <div class="text-sm font-bold text-gray-900 group-hover:text-agri-forest transition-colors">{{ user.name }}</div>
                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-tight mt-0.5">{{ user.email }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-5">
                  <span :class="getRoleClasses(user.role)" class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest inline-block border">
                    {{ user.role }}
                  </span>
                </td>
                <td class="px-6 py-5 text-center">
                   <span v-if="user.suspended_at" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-red-100 text-red-800">
                        Suspended
                   </span>
                   <span v-else-if="user.email_verified_at" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-green-100 text-green-800">
                        Active
                   </span>
                   <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-yellow-100 text-yellow-800">
                        Pending
                   </span>
                </td>
                <td class="px-6 py-5 text-center">
                    <span class="text-xs font-bold text-gray-500">{{ formatDate(user.updated_at) }}</span>
                </td>
                <td class="px-6 py-5 text-right relative">
                   <div class="relative inline-block text-left">
                        <button @click.stop="toggleDropdown(user.id)" class="p-2 text-gray-400 hover:text-agri-forest hover:bg-gray-50 rounded-lg transition-all">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" /></svg>
                        </button>
                        <!-- Dropdown Menu -->
                        <div v-show="activeDropdown === user.id" class="origin-top-right absolute right-0 mt-2 w-56 rounded-xl shadow-2xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 divide-y divide-gray-100">
                             <div class="py-1">
                                <button @click="openEditModal(user)" class="group flex w-full items-center px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 hover:text-agri-forest">
                                    <svg class="mr-3 h-4 w-4 text-gray-400 group-hover:text-agri-forest" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    Edit Profile
                                </button>
                                <button @click="openLogModal(user)" class="group flex w-full items-center px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 hover:text-agri-forest">
                                    <svg class="mr-3 h-4 w-4 text-gray-400 group-hover:text-agri-forest" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                                    View Activity Log
                                </button>
                             </div>
                             <div class="py-1">
                                <button @click="confirmImpersonate(user)" class="group flex w-full items-center px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 hover:text-agri-earth">
                                    <svg class="mr-3 h-4 w-4 text-gray-400 group-hover:text-agri-earth" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    Impersonate
                                </button>
                                <button @click="confirmResetPassword(user)" class="group flex w-full items-center px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 hover:text-blue-600">
                                    <svg class="mr-3 h-4 w-4 text-gray-400 group-hover:text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                                    Reset Password
                                </button>
                             </div>
                              <div v-if="authStore.can('manage_users')" class="py-1">
                                <button v-if="!user.suspended_at" @click="confirmAction('suspend', user)" class="group flex w-full items-center px-4 py-2 text-xs font-bold text-red-600 hover:bg-red-50">
                                    <svg class="mr-3 h-4 w-4 text-red-400 group-hover:text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                    Suspend Access
                                </button>
                                 <button v-else @click="confirmAction('activate', user)" class="group flex w-full items-center px-4 py-2 text-xs font-bold text-green-600 hover:bg-green-50">
                                    <svg class="mr-3 h-4 w-4 text-green-400 group-hover:text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Restore Access
                                </button>
                                <button @click="confirmAction('delete', user)" class="group flex w-full items-center px-4 py-2 text-xs font-bold text-gray-500 hover:bg-gray-100 hover:text-gray-800">
                                    <svg class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    Delete Identity
                                </button>
                              </div>
                        </div>
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

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import { useAuthStore } from '../../stores/auth';
import Notification from '../../components/Notification.vue';
import Modal from '../../components/Modal.vue';

const authStore = useAuthStore();
const notificationRef = ref(null);
const users = ref([]);
const loading = ref(true);
const modalLoading = ref(false);
const searchQuery = ref('');
const roleFilter = ref('');

// Modal States
const showUserModal = ref(false);
const showInviteModal = ref(false);
const showLogModal = ref(false);
const showConfirmModal = ref(false);

// Forms & Data
const roles = ref([]);

// Forms & Data
const editingUser = ref(null);
const userForm = ref({ name: '', email: '', role: 'user', location: '' });
const inviteForm = ref({ emails: '' });
const userLogs = ref([]);

// Confirmation State
const confirmTitle = ref('');
const confirmMessage = ref('');
const confirmActionText = ref('Confirm');
const confirmType = ref('primary');
const pendingAction = ref(null);
const pendingUser = ref(null);

// Bulk Actions
const selectedUsers = ref([]);
const activeDropdown = ref(null);

// Computed
const filteredUsers = computed(() => {
    let result = users.value;
    if (roleFilter.value) {
        result = result.filter(u => u.role === roleFilter.value);
    }
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(u => 
            u.name.toLowerCase().includes(query) || 
            u.email.toLowerCase().includes(query)
        );
    }
    return result;
});

const selectAll = computed(() => {
    return filteredUsers.value.length > 0 && selectedUsers.value.length === filteredUsers.value.length;
});

// Logic
const toggleSelectAll = () => {
    if (selectAll.value) {
        selectedUsers.value = [];
    } else {
        selectedUsers.value = filteredUsers.value.map(u => u.id);
    }
};

const toggleDropdown = (id) => {
    activeDropdown.value = activeDropdown.value === id ? null : id;
};

// Close dropdowns on click outside
const closeDropdowns = () => { activeDropdown.value = null; };
onMounted(() => document.addEventListener('click', closeDropdowns));
onUnmounted(() => document.removeEventListener('click', closeDropdowns));

const fetchUsers = async () => {
    loading.value = true;
    loading.value = true;
    try {
        const [usersRes, rolesRes] = await Promise.all([
            axios.get('/api/admin/users'),
            axios.get('/api/admin/roles')
        ]);
        users.value = usersRes.data.data || usersRes.data;
        roles.value = rolesRes.data;
        // Mock active status logic if not present
        if (users.value.length > 0 && !users.value[0].updated_at) {
             users.value.forEach(u => u.updated_at = new Date().toISOString());
        }
    } catch (error) {
        console.error('Fetch error:', error);
        notificationRef.value?.notify.error('Data Sync Error', 'Failed to retrieve identity registry.');
    } finally {
        loading.value = false;
    }
};

// Modal Actions
const openAddModal = () => {
    editingUser.value = null;
    userForm.value = { name: '', email: '', role: 'user', location: '' };
    showUserModal.value = true;
};

const openEditModal = (user) => {
    editingUser.value = user;
    userForm.value = { ...user };
    showUserModal.value = true;
    activeDropdown.value = null;
};

const openInviteModal = () => {
    inviteForm.value.emails = '';
    showInviteModal.value = true;
};

const openLogModal = (user) => {
    activeDropdown.value = null;
    userLogs.value = [
        { action: 'LOGIN_AUTHORIZED', description: 'Successful biometric authentication via secure node.', timestamp: '2 mins ago', type: 'success' },
        { action: 'PROFILE_UPDATE', description: 'Modified contact signaling channel.', timestamp: '2 days ago', type: 'info' },
        { action: 'PROTOCOL_AGREEMENT', description: 'Signed updated trade sovereignty contract.', timestamp: '1 week ago', type: 'info' }
    ]; // Mock data
    showLogModal.value = true;
};

const closeModals = () => {
    showUserModal.value = false;
    showInviteModal.value = false;
    showLogModal.value = false;
    showConfirmModal.value = false;
    pendingAction.value = null;
    pendingUser.value = null;
};

const saveUser = async () => {
    modalLoading.value = true;
    try {
        if (editingUser.value) {
            await axios.put(`/api/admin/users/${editingUser.value.id}`, userForm.value);
            notificationRef.value?.notify.success('Identity Updated', 'Credentials and metadata synced.');
        } else {
            await axios.post('/api/admin/users', userForm.value);
            notificationRef.value?.notify.success('Identity Created', 'New node registered in the ledger. Credentials sent via email.');
        }
        fetchUsers();
        closeModals();
    } catch (error) {
        console.error('Save user error:', error);
        const message = error.response?.data?.message || 'Could not persist identity data.';
        notificationRef.value?.notify.error('Operation Failed', message);
    } finally {
        modalLoading.value = false;
    }
};

const sendInvites = async () => {
    modalLoading.value = true;
    try {
        await axios.post('/api/admin/users/invite', { emails: inviteForm.value.emails });
        notificationRef.value?.notify.success('Invitations Broadcasted', 'Secure access links queued for delivery.');
        closeModals();
    } catch (error) {
        console.error('Invite error:', error);
        notificationRef.value?.notify.error('Broadcast Error', 'Failed to queue invitations.');
    } finally {
        modalLoading.value = false;
    }
}

const exportData = () => {
    notificationRef.value?.notify.info('Export Initiated', 'Generating CSV ledger manifest...');
};

// Confirmation Handler
const confirmAction = (action, user) => {
    activeDropdown.value = null;
    pendingAction.value = action;
    pendingUser.value = user;
    
    if (action === 'suspend') {
        confirmTitle.value = 'Suspend Identity Node';
        confirmMessage.value = `Are you sure you want to suspend ${user.name}? This will revoke all trading privileges immediately.`;
        confirmActionText.value = 'Suspend Node';
        confirmType.value = 'danger';
    } else if (action === 'activate') {
        confirmTitle.value = 'Restore Identity Node';
        confirmMessage.value = `Re-enable access for ${user.name}?`;
        confirmActionText.value = 'Restore Access';
        confirmType.value = 'success';
    } else if (action === 'delete') {
         confirmTitle.value = 'Permanently Delete Identity';
        confirmMessage.value = `WARNING: excessive force. This action scrubs ${user.name} from the registry permanently.`;
        confirmActionText.value = 'Delete Forever';
        confirmType.value = 'danger';
    }
    
    showConfirmModal.value = true;
};

const confirmBulkAction = (action) => {
    pendingAction.value = `bulk_${action}`;
    pendingUser.value = null;

    if (action === 'activate') {
         confirmTitle.value = `Bulk Activate ${selectedUsers.value.length} Nodes`;
         confirmMessage.value = 'Are you sure you want to restore access for selected users?';
         confirmType.value = 'success';
    } else if (action === 'deactivate') {
        confirmTitle.value = `Bulk Suspend ${selectedUsers.value.length} Nodes`;
        confirmMessage.value = 'Are you sure you want to suspend all selected users?';
        confirmType.value = 'danger';
    } else {
        confirmTitle.value = `Bulk Delete ${selectedUsers.value.length} Nodes`;
        confirmMessage.value = 'Are you sure you want to PERMANENTLY delete these users?';
        confirmType.value = 'danger';
    }
    showConfirmModal.value = true;
};

const confirmImpersonate = (user) => {
    activeDropdown.value = null;
    pendingAction.value = 'impersonate';
    pendingUser.value = user;
    confirmTitle.value = 'Authorize Impersonation';
    confirmMessage.value = `View the system as ${user.name}? This action is logged for audit purposes.`;
    confirmActionText.value = 'Impersonate';
    confirmType.value = 'primary';
    showConfirmModal.value = true;
};

const confirmResetPassword = (user) => {
    activeDropdown.value = null;
    pendingAction.value = 'reset_password';
    pendingUser.value = user;
    confirmTitle.value = 'Force Credential Reset';
    confirmMessage.value = `Invalidate current keys and send reset link to ${user.email}?`;
    confirmActionText.value = 'Send Reset Link';
    confirmType.value = 'primary';
    showConfirmModal.value = true;
};

const verifyUser = async (user) => {
    try {
        await axios.post(`/api/admin/users/${user.id}/verify`);
        notificationRef.value?.notify.success('Identity Verified', `Verification seal applied to ${user.name}.`);
        fetchUsers();
    } catch (error) {
        notificationRef.value?.notify.error('Verification Failed', 'Could not apply verification seal.');
    }
};

const executeConfirmAction = async () => {
    modalLoading.value = true;
    try {
        if (pendingAction.value === 'impersonate') {
            await axios.post(`/api/admin/users/${pendingUser.value.id}/impersonate`);
            notificationRef.value?.notify.success('Session Switch', `Now viewing as ${pendingUser.value.name}.`);
        } else if (pendingAction.value === 'reset_password') {
            await axios.post(`/api/admin/users/${pendingUser.value.id}/reset-password`);
            notificationRef.value?.notify.success('Keys Reset', 'Recovery protocol initiated.');
        } else if (pendingAction.value === 'delete') {
            await axios.delete(`/api/admin/users/${pendingUser.value.id}`);
            notificationRef.value?.notify.success('Node Deleted', 'Identity purged.');
        } else if (pendingAction.value === 'suspend') {
            await axios.post(`/api/admin/users/${pendingUser.value.id}/suspend`);
            notificationRef.value?.notify.success('Node Suspended', 'Access revoked.');
        } else if (pendingAction.value === 'activate') {
            await axios.post(`/api/admin/users/${pendingUser.value.id}/activate`);
             notificationRef.value?.notify.success('Node Activated', 'Access restored.');
        } else if (pendingAction.value && pendingAction.value.startsWith('bulk_')) {
             const actionName = pendingAction.value.replace('bulk_', '');
             await axios.post('/api/admin/users/bulk-action', {
                ids: selectedUsers.value,
                action: actionName
             });
             notificationRef.value?.notify.success('Bulk Operation Executed', 'Ledger updated successfully.');
             selectedUsers.value = [];
        }
        
        fetchUsers();
        closeModals();
    } catch (error) {
        console.error(error);
        notificationRef.value?.notify.error('Execution Failed', 'System denied the operation.');
    } finally {
         modalLoading.value = false;
    }
};

// Formatting helpers
const formatDate = (date) => {
  if (!date) return 'Never';
  return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

const getRoleClasses = (role) => {
    switch (role) {
        case 'admin': return 'bg-agri-forest text-agri-earth border-agri-forest';
        case 'vendor': return 'bg-agri-sand text-agri-forest border-agri-sand';
        case 'buyer': return 'bg-gray-50 text-gray-500 border-gray-100';
        default: return 'bg-gray-100 text-gray-500 border-gray-100';
    }
};

const getAvatarBorder = (role) => {
     switch (role) {
        case 'admin': return 'border-agri-forest/30';
        case 'vendor': return 'border-agri-sand';
        default: return 'border-gray-200';
    }
};

const getAvatarText = (role) => {
     switch (role) {
        case 'admin': return 'text-agri-forest';
        case 'vendor': return 'text-agri-earth';
        default: return 'text-gray-400';
    }
}

onMounted(() => {
    fetchUsers();
});
</script>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.3s ease-out;
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
