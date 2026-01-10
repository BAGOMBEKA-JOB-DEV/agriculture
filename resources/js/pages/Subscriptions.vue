<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Choose Your Subscription Plan</h1>
        <p class="text-gray-600">Select a plan to unlock full access to the marketplace</p>
      </div>

      <div v-if="loading" class="text-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600 mx-auto"></div>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 justify-center">
        <div v-for="plan in plans" :key="plan.id" class="bg-white rounded-xl shadow-lg hover:shadow-xl transition overflow-hidden border-2 border-transparent hover:border-green-500">
          <div class="p-8">
            <h3 class="text-2xl font-bold text-gray-900 text-center mb-2">{{ plan.name }}</h3>
            <p class="text-center text-gray-500 mb-6 capitalize">{{ plan.plan_type }} Plan</p>
            
            <div class="text-center mb-8">
              <span class="text-4xl font-bold text-gray-900">${{ plan.price }}</span>
              <span class="text-gray-500">/{{ plan.plan_type === 'weekly' ? 'week' : 'month' }}</span>
            </div>

            <ul class="flex flex-col gap-4 mb-8">
              <li v-for="(feature, index) in plan.features" :key="index" class="flex items-center">
                <svg class="h-5 w-5 text-green-500 mr-3" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                  <path d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="text-gray-600">{{ feature }}</span>
              </li>
            </ul>

            <button 
              @click="subscribe(plan)"
              :disabled="processing === plan.id"
              class="w-full bg-green-600 text-white rounded-lg px-4 py-3 font-semibold hover:bg-green-700 disabled:opacity-50 transition"
            >
              {{ processing === plan.id ? 'Processing...' : 'Subscribe Now' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import axios from 'axios';

const router = useRouter();
const authStore = useAuthStore();
const plans = ref([]);
const loading = ref(true);
const processing = ref(null);

const fetchPlans = async () => {
  try {
    const response = await axios.get('/subscription-plans');
    plans.value = response.data;
  } catch (error) {
    console.error('Error fetching plans:', error);
  } finally {
    loading.value = false;
  }
};

const subscribe = async (plan) => {
  processing.value = plan.id;
  try {
    await axios.post(`/subscriptions/${plan.id}/subscribe`);
    // Refresh auth user to update subscription status
    await authStore.checkAuth();
    
    // Redirect based on role
    if (authStore.user.role === 'vendor') {
      router.push('/vendor/dashboard');
    } else {
      router.push('/buyer/dashboard');
    }
  } catch (error) {
    if (window.notify) window.notify.error('Access Failure', error.response?.data?.message || 'Subscription failed');
  } finally {
    processing.value = null;
  }
};

onMounted(() => {
  fetchPlans();
});
</script>
