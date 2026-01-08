<template>
  <div class="min-h-screen bg-gray-50/50 pb-12">
    <!-- Components -->
    <Notification ref="notificationRef" />
    <Modal
      :is-open="showConfirmModal"
      title="Update Announcement"
      message="Are you sure you want to update this announcement? The changes will be visible to users immediately."
      confirm-text="Yes, Update"
      cancel-text="Cancel"
      :loading="loading"
      @confirm="executeUpdate"
      @cancel="showConfirmModal = false"
    />

    <!-- Header -->
    <div class="bg-white border-b border-gray-100 px-8 py-6 mb-8">
      <div class="max-w-4xl mx-auto flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-black text-gray-900 tracking-tight">Edit Announcement</h1>
          <p class="text-sm font-medium text-gray-500 mt-1">Update existing system announcement.</p>
        </div>
        <button @click="$router.back()" class="text-sm font-bold text-gray-500 hover:text-gray-900 transition-colors">
            Cancel
        </button>
      </div>
    </div>

    <!-- Content -->
    <div class="max-w-4xl mx-auto px-6">
      <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8">
        <form @submit.prevent="confirmUpdate" class="space-y-8">
          
          <!-- Subject -->
          <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Subject Line</label>
            <input 
                v-model="form.subject" 
                type="text" 
                class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 font-bold text-gray-900 focus:ring-2 focus:ring-agri-forest/20 focus:border-agri-forest transition-all placeholder-gray-300" 
                placeholder="e.g., Scheduled Platform Maintenance" 
                required
            >
          </div>

          <!-- Priority -->
          <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">Priority Level</label>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <label class="relative flex items-center p-4 rounded-xl border-2 cursor-pointer transition-all duration-200"
                       :class="form.priority === 'normal' ? 'bg-blue-50 border-blue-500 ring-1 ring-blue-500' : 'bg-white border-gray-100 hover:border-gray-200 hover:bg-gray-50'">
                    <input type="radio" v-model="form.priority" value="normal" class="hidden">
                    <div class="flex items-center w-full">
                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center mr-3 flex-shrink-0">
                            <span class="block h-3 w-3 rounded-full bg-blue-500"></span>
                        </div>
                        <div>
                            <span :class="['block text-sm font-bold', form.priority === 'normal' ? 'text-blue-900' : 'text-gray-900']">Normal</span>
                            <span :class="['block text-xs font-medium', form.priority === 'normal' ? 'text-blue-600' : 'text-gray-500']">Standard info</span>
                        </div>
                    </div>
                </label>

                <label class="relative flex items-center p-4 rounded-xl border-2 cursor-pointer transition-all duration-200"
                       :class="form.priority === 'high' ? 'bg-orange-50 border-orange-500 ring-1 ring-orange-500' : 'bg-white border-gray-100 hover:border-gray-200 hover:bg-gray-50'">
                    <input type="radio" v-model="form.priority" value="high" class="hidden">
                    <div class="flex items-center w-full">
                         <div class="h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center mr-3 flex-shrink-0">
                            <span class="block h-3 w-3 rounded-full bg-orange-500"></span>
                        </div>
                        <div>
                            <span :class="['block text-sm font-bold', form.priority === 'high' ? 'text-orange-900' : 'text-gray-900']">High</span>
                            <span :class="['block text-xs font-medium', form.priority === 'high' ? 'text-orange-600' : 'text-gray-500']">Important</span>
                        </div>
                    </div>
                </label>

                <label class="relative flex items-center p-4 rounded-xl border-2 cursor-pointer transition-all duration-200"
                       :class="form.priority === 'critical' ? 'bg-red-50 border-red-500 ring-1 ring-red-500' : 'bg-white border-gray-100 hover:border-gray-200 hover:bg-gray-50'">
                    <input type="radio" v-model="form.priority" value="critical" class="hidden">
                    <div class="flex items-center w-full">
                        <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center mr-3 flex-shrink-0">
                            <span class="block h-3 w-3 rounded-full bg-red-500 animate-pulse"></span>
                        </div>
                        <div>
                            <span :class="['block text-sm font-bold', form.priority === 'critical' ? 'text-red-900' : 'text-gray-900']">Critical</span>
                            <span :class="['block text-xs font-medium', form.priority === 'critical' ? 'text-red-600' : 'text-gray-500']">Urgent attention</span>
                        </div>
                    </div>
                </label>
            </div>
          </div>

          <!-- Message -->
          <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Message Body</label>
            <textarea 
                v-model="form.message" 
                rows="6" 
                class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 font-medium text-gray-900 focus:ring-2 focus:ring-agri-forest/20 focus:border-agri-forest transition-all placeholder-gray-300 resize-none" 
                placeholder="Type your system-wide announcement here..." 
                required
            ></textarea>
            <p class="mt-2 text-xs text-gray-400 font-medium text-right">Supports HTML content</p>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
            <button type="submit" :disabled="loading" class="px-8 py-4 bg-agri-forest text-white font-bold rounded-xl shadow-lg shadow-agri-forest/20 hover:bg-agri-lush hover:shadow-xl hover:translate-y-[-1px] transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span>Update Alert</span>
            </button>
          </div>

        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import { useRouter, useRoute } from 'vue-router';
import Notification from '../../../components/Notification.vue';
import Modal from '../../../components/Modal.vue';

const router = useRouter();
const route = useRoute();
const loading = ref(false);
const showConfirmModal = ref(false);
const notificationRef = ref(null);

const form = reactive({
    subject: '',
    message: '',
    priority: 'normal'
});

const fetchNotification = async () => {
    loading.value = true;
    try {
        const response = await axios.get(`/api/admin/notifications/${route.params.id}`);
        const data = response.data;
        form.subject = data.subject;
        form.message = data.message;
        form.priority = data.priority;
    } catch (error) {
        console.error('Error fetching notification:', error);
        notificationRef.value?.notify.error('Error', 'Failed to load notification details.');
        setTimeout(() => router.push('/admin/notifications'), 1000);
    } finally {
        loading.value = false;
    }
};

const confirmUpdate = () => {
    showConfirmModal.value = true;
};

const executeUpdate = async () => {
    loading.value = true;
    try {
        await axios.put(`/api/admin/notifications/${route.params.id}`, form);
        
        showConfirmModal.value = false;
        notificationRef.value?.notify.success('Update Success', 'The announcement has been updated successfully.');
        
        setTimeout(() => {
            router.push('/admin/notifications');
        }, 1500);

    } catch (error) {
        console.error('Error updating notification:', error);
        showConfirmModal.value = false;
        notificationRef.value?.notify.error('Update Failed', 'An error occurred while updating the announcement.');
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchNotification();
});
</script>
