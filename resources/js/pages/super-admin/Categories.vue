<template>
  <div class="min-h-screen bg-gray-50/50 pb-24">
    <!-- Enterprise Header -->
    <div class="bg-agri-forest py-16 relative overflow-hidden">
        <div class="absolute inset-0 bg-agri-earth/5 opacity-20"></div>
        <div class="container mx-auto px-6 relative z-10">
            <h1 class="text-4xl font-black text-white mb-2 tracking-tight">
               Asset <span class="text-agri-earth">Classifications</span>
            </h1>
            <p class="text-agri-earth/60 font-medium max-w-xl text-sm leading-relaxed">System-wide registry for global asset categorization and market discovery integrity.</p>
        </div>
    </div>

    <div class="container mx-auto px-6 -mt-10 relative z-20">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Creation Panel -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 sticky top-24">
            <div class="flex items-center space-x-3 mb-8 pb-4 border-b border-gray-50">
                <div class="w-1.5 h-6 bg-agri-earth rounded-full"></div>
                <h2 class="text-xs font-black text-gray-900 uppercase tracking-widest">Protocol Definition</h2>
            </div>
            <form @submit.prevent="handleSubmit" class="space-y-6">
              <div class="group">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1 group-focus-within:text-agri-forest transition-colors">Category Designation</label>
                <input v-model="form.name" type="text" required class="w-full px-5 py-3.5 bg-gray-50 border border-transparent rounded-2xl focus:ring-2 focus:ring-agri-forest/10 focus:border-agri-forest transition-all outline-none font-bold text-gray-700" placeholder="e.g., Grains & Cereals">
              </div>
              
              <div class="group">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1 group-focus-within:text-agri-forest transition-colors">Sector Brief</label>
                <textarea v-model="form.description" rows="3" class="w-full px-5 py-3.5 bg-gray-50 border border-transparent rounded-2xl focus:ring-2 focus:ring-agri-forest/10 focus:border-agri-forest transition-all outline-none font-medium text-sm text-gray-700 resize-none" placeholder="Provide a brief description of this trade sector..."></textarea>
              </div>

              <div class="flex items-center space-x-3 px-1">
                <input v-model="form.is_active" type="checkbox" id="is_active" class="w-4 h-4 rounded-md border-gray-300 text-agri-forest focus:ring-agri-forest">
                <label for="is_active" class="text-[10px] font-bold text-gray-500 uppercase tracking-widest cursor-pointer">Active Protocol</label>
              </div>

              <div class="pt-4 space-y-3">
                <button type="submit" :disabled="loading" class="w-full py-4 rounded-2xl bg-agri-forest text-white font-black uppercase text-[10px] tracking-widest hover:bg-agri-lush transition-all shadow-lg shadow-agri-forest/10 disabled:opacity-50">
                  {{ loading ? 'Synchronizing...' : (editingId ? 'Update Designation' : 'Broadcast Category') }}
                </button>
                
                <button v-if="editingId" @click="resetForm" type="button" class="w-full py-4 rounded-2xl bg-gray-50 text-gray-400 font-black uppercase text-[10px] tracking-widest hover:bg-gray-100 transition-all border border-gray-100">
                  Abort Edit
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Registry List -->
        <div class="lg:col-span-2 space-y-4">
          <div v-for="cat in categories" :key="cat.id" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 flex items-center justify-between group hover:border-agri-forest/20 hover:bg-agri-sand/5 transition-all duration-300">
            <div class="flex items-center space-x-5">
              <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center text-agri-forest text-lg font-black group-hover:bg-white transition-colors">
                {{ cat.name.charAt(0) }}
              </div>
              <div>
                <div class="flex items-center space-x-3 mb-0.5">
                  <h3 class="text-lg font-black text-gray-900 tracking-tight leading-none">{{ cat.name }}</h3>
                  <span v-if="!cat.is_active" class="px-2 py-0.5 rounded-full bg-red-50 text-red-500 text-[7px] font-black uppercase tracking-widest border border-red-100">Inactive</span>
                </div>
                <p class="text-[11px] text-gray-500 font-medium max-w-md leading-relaxed">{{ cat.description || 'No sectoral description provided in global registry.' }}</p>
              </div>
            </div>
            
            <div class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-all transform translate-x-4 group-hover:translate-x-0">
              <button @click="editCategory(cat)" class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-agri-forest hover:bg-agri-forest hover:text-white transition-all border border-gray-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </button>
              <button @click="initiateDelete(cat)" class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-red-500 hover:bg-red-500 hover:text-white transition-all border border-red-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v2m3 4h6"></path>
                </svg>
              </button>
            </div>
          </div>
          
          <div v-if="categories.length === 0" class="py-16 text-center bg-white rounded-3xl shadow-sm border border-gray-100 border-dashed">
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em]">No Classifications Found</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Deletion Verification Modal -->
    <Modal
      :is-open="showDeleteModal"
      title="Purge Protocol"
      :message="`Are you certain you want to permanently remove '${categoryToDelete?.name}' from the global asset registry?`"
      confirm-text="Confirm Purge"
      cancel-text="Abort"
      type="danger"
      :loading="isDeleting"
      @confirm="confirmDelete"
      @cancel="showDeleteModal = false"
    >
      <div class="p-4 bg-red-50 rounded-2xl border border-red-100">
        <div class="flex items-start space-x-3">
          <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
          </svg>
          <div>
            <p class="text-[11px] font-black text-red-900 uppercase tracking-widest mb-1">Irreversible Action</p>
            <p class="text-xs text-red-700 font-medium leading-relaxed">
              Purging this classification will synchronize across all exchange nodes. This action cannot be undone if the category is not attached to existing assets.
            </p>
          </div>
        </div>
      </div>
    </Modal>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Modal from '../../components/Modal.vue';

const categories = ref([]);
const loading = ref(false);
const editingId = ref(null);

const showDeleteModal = ref(false);
const categoryToDelete = ref(null);
const isDeleting = ref(false);

const form = ref({
  name: '',
  description: '',
  is_active: true
});

const fetchCategories = async () => {
  try {
    const response = await axios.get('/api/categories');
    categories.value = response.data;
  } catch (err) {
    console.error('Registry Sync Failure:', err);
  }
};

const handleSubmit = async () => {
  loading.value = true;
  try {
    if (editingId.value) {
      await axios.put(`/api/super-admin/categories/${editingId.value}`, form.value);
      if (window.notify) window.notify.success('Registry Updated', 'Market classification successfully updated.');
    } else {
      await axios.post('/api/super-admin/categories', form.value);
      if (window.notify) window.notify.success('Category Broadcasted', 'New asset classification live in registry.');
    }
    resetForm();
    fetchCategories();
  } catch (err) {
    const msg = err.response?.data?.message || 'Registry update failed.';
    if (window.notify) window.notify.error('Transmission Error', msg);
    
    // Handle validation errors specifically if they exist
    if (err.response?.data?.errors) {
        const errors = err.response.data.errors;
        Object.keys(errors).forEach(key => {
            if (window.notify) window.notify.error('Validation Fail', errors[key][0]);
        });
    }
  } finally {
    loading.value = false;
  }
};

const editCategory = (cat) => {
  editingId.value = cat.id;
  form.value = {
    name: cat.name,
    description: cat.description,
    is_active: cat.is_active
  };
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const initiateDelete = (cat) => {
    categoryToDelete.value = cat;
    showDeleteModal.value = true;
};

const confirmDelete = async () => {
  if (!categoryToDelete.value) return;
  
  isDeleting.value = true;
  try {
    await axios.delete(`/api/super-admin/categories/${categoryToDelete.value.id}`);
    if (window.notify) window.notify.success('Registry Cleared', 'Classification removed from global ledger.');
    fetchCategories();
    showDeleteModal.value = false;
    categoryToDelete.value = null;
  } catch (err) {
    const msg = err.response?.data?.message || 'Purge protocol failed.';
    if (window.notify) window.notify.error('Action Restricted', msg);
  } finally {
    isDeleting.value = false;
  }
};

const resetForm = () => {
  editingId.value = null;
  form.value = {
    name: '',
    description: '',
    is_active: true
  };
};

onMounted(fetchCategories);
</script>

<style scoped>
.tracking-tighter {
  letter-spacing: -2px;
}
</style>
