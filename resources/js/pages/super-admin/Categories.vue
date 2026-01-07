<template>
  <div class="min-h-screen bg-gray-50/50 pb-24">
    <!-- Forest Header -->
    <div class="bg-agri-forest py-20 relative overflow-hidden">
        <div class="absolute inset-0 bg-agri-earth/5 opacity-20"></div>
        <div class="container mx-auto px-6 relative z-10">
            <h1 class="text-5xl font-black text-white mb-4 tracking-tighter">
               Asset <span class="text-agri-earth">Categories</span>
            </h1>
            <p class="text-agri-earth/70 font-medium max-w-xl">Maintain the global asset classification registry for high-integrity market discovery.</p>
        </div>
    </div>

    <div class="container mx-auto px-6 -mt-10 relative z-20">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Creation Panel -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-[2.5rem] shadow-2xl border border-gray-100 p-10 sticky top-10">
            <h2 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-8 border-b border-gray-50 pb-4">Define New Sector</h2>
            <form @submit.prevent="handleSubmit" class="space-y-8">
              <div class="group">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 group-focus-within:text-agri-forest transition-colors">Category Designation</label>
                <input v-model="form.name" type="text" required class="w-full px-6 py-4 bg-gray-50 border border-transparent rounded-2xl focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest transition-all outline-none font-black text-gray-900" placeholder="e.g., Grains & Cereals">
              </div>
              
              <div class="group">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 group-focus-within:text-agri-forest transition-colors">Sector Brief</label>
                <textarea v-model="form.description" rows="4" class="w-full px-6 py-4 bg-gray-50 border border-transparent rounded-2xl focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest transition-all outline-none font-medium text-sm text-gray-900 resize-none" placeholder="Provide a brief description of this trade sector..."></textarea>
              </div>

              <div class="flex items-center space-x-3">
                <input v-model="form.is_active" type="checkbox" id="is_active" class="w-5 h-5 rounded-lg border-gray-100 text-agri-forest focus:ring-agri-forest">
                <label for="is_active" class="text-xs font-black text-gray-500 uppercase tracking-widest cursor-pointer">Active Protocol</label>
              </div>

              <button type="submit" :disabled="loading" class="w-full py-5 rounded-2xl bg-agri-forest text-white font-black uppercase text-xs tracking-widest hover:bg-agri-lush transition-all shadow-xl shadow-agri-forest/20 disabled:opacity-50">
                <span v-if="loading">Synchronizing...</span>
                <span v-else>{{ editingId ? 'Update Designation' : 'Broadcast Category' }}</span>
              </button>
              
              <button v-if="editingId" @click="resetForm" type="button" class="w-full py-5 rounded-2xl bg-gray-50 text-gray-400 font-black uppercase text-xs tracking-widest hover:bg-gray-100 transition-all">
                Abort Edit
              </button>
            </form>
          </div>
        </div>

        <!-- Registry List -->
        <div class="lg:col-span-2 space-y-8">
          <div v-for="cat in categories" :key="cat.id" class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 p-8 flex items-center justify-between group hover:shadow-2xl transition-all duration-500">
            <div class="flex items-center space-x-6">
              <div class="w-16 h-16 rounded-[1.5rem] bg-agri-sand/20 flex items-center justify-center text-agri-forest text-2xl font-black">
                {{ cat.name.charAt(0) }}
              </div>
              <div>
                <div class="flex items-center space-x-3 mb-1">
                  <h3 class="text-xl font-black text-gray-900">{{ cat.name }}</h3>
                  <span v-if="!cat.is_active" class="px-3 py-1 rounded-full bg-red-50 text-red-500 text-[8px] font-black uppercase tracking-widest">Inactive</span>
                </div>
                <p class="text-sm text-gray-500 font-medium max-w-md">{{ cat.description || 'No sector description provided.' }}</p>
              </div>
            </div>
            
            <div class="flex items-center space-x-4 opacity-0 group-hover:opacity-100 transition-opacity">
              <button @click="editCategory(cat)" class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center text-agri-forest hover:bg-agri-forest hover:text-white transition-all shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </button>
              <button @click="deleteCategory(cat.id)" class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-red-500 hover:bg-red-500 hover:text-white transition-all shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v2m3 4h6"></path>
                </svg>
              </button>
            </div>
          </div>
          
          <div v-if="categories.length === 0" class="py-20 text-center bg-white rounded-[3rem] shadow-xl border border-gray-100">
            <p class="text-gray-400 font-black uppercase text-xs tracking-widest">No Classifications Found</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const categories = ref([]);
const loading = ref(false);
const editingId = ref(null);

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

const deleteCategory = async (id) => {
  if (!confirm('Permanently purge this asset classification?')) return;
  try {
    await axios.delete(`/api/super-admin/categories/${id}`);
    if (window.notify) window.notify.success('Registry Cleared', 'Classification removed from global ledger.');
    fetchCategories();
  } catch (err) {
    const msg = err.response?.data?.message || 'Purge protocol failed.';
    if (window.notify) window.notify.error('Action Restricted', msg);
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
