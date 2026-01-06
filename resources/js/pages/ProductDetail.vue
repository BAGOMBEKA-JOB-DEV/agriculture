<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div v-if="loading" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
      </div>

      <div v-else-if="product" class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="md:flex">
          <!-- Image Section -->
          <div class="md:w-1/2 bg-gray-100 h-96 md:h-auto flex items-center justify-center">
            <div class="text-6xl text-green-200">
              <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
              </svg>
            </div>
          </div>

          <!-- Info Section -->
          <div class="md:w-1/2 p-8">
            <div class="flex justify-between items-start mb-4">
              <div>
                <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full uppercase font-semibold tracking-wide">
                  {{ product.category?.name }}
                </span>
                <h1 class="mt-2 text-3xl font-bold text-gray-900">{{ product.name }}</h1>
                <p class="text-gray-500 flex items-center mt-1">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                  </svg>
                  {{ product.location }}
                </p>
              </div>
              <div class="text-right">
                <div v-if="!product.price_on_request">
                  <span class="text-3xl font-bold text-gray-900">${{ product.price }}</span>
                  <span class="text-gray-500">/{{ product.unit }}</span>
                </div>
                <div v-else class="text-xl font-semibold text-green-600">
                  Price on Request
                </div>
              </div>
            </div>

            <div class="border-t border-b border-gray-100 py-4 my-6">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <span class="text-sm text-gray-500 block">Available Quantity</span>
                  <span class="font-medium text-gray-900">{{ product.quantity_available }} {{ product.unit }}</span>
                </div>
                <div>
                  <span class="text-sm text-gray-500 block">Minimum Order</span>
                  <span class="font-medium text-gray-900">{{ product.minimum_order_quantity }} {{ product.unit }}</span>
                </div>
              </div>
            </div>

            <div class="mb-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-2">Description</h3>
              <p class="text-gray-600 leading-relaxed">{{ product.description }}</p>
            </div>

            <!-- Seller Info -->
            <div class="flex items-center p-4 bg-gray-50 rounded-lg mb-6">
              <div class="flex-shrink-0 bg-green-200 rounded-full w-12 h-12 flex items-center justify-center text-green-700 font-bold text-xl">
                {{ product.seller?.name?.charAt(0) }}
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-900">{{ product.seller?.name }}</p>
                <p class="text-xs text-gray-500">Verified Seller</p>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-4">
              <template v-if="authStore.isAuthenticated && authStore.user.id !== product.user_id">
                <button 
                  @click="showMessageModal = true"
                  class="flex-1 bg-white border-2 border-green-600 text-green-600 px-6 py-3 rounded-lg font-semibold hover:bg-green-50 transition"
                >
                  Message Seller
                </button>
                <button 
                  @click="showRfqModal = true"
                  class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition"
                >
                  Request Quote
                </button>
              </template>
              <template v-else-if="!authStore.isAuthenticated">
                <router-link to="/login" class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition text-center">
                  Login to Contact Seller
                </router-link>
              </template>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="text-center py-12">
        <p class="text-gray-500">Product not found.</p>
      </div>

      <!-- Message Modal -->
      <div v-if="showMessageModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showMessageModal = false"></div>
          <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                Message {{ product.seller?.name }}
              </h3>
              <div class="mt-4">
                <textarea v-model="messageText" rows="4" class="w-full border rounded-lg px-3 py-2" placeholder="Write your message here..."></textarea>
              </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button @click="sendMessage" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 sm:ml-3 sm:w-auto sm:text-sm">
                Send Message
              </button>
              <button @click="showMessageModal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- RFQ Modal -->
      <div v-if="showRfqModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showRfqModal = false"></div>
          <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                Request Quote
              </h3>
              <div class="mt-4 flex flex-col gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Quantity Needed</label>
                  <div class="flex mt-1">
                    <input v-model="rfqForm.quantity" type="number" class="w-2/3 border rounded-l-lg px-3 py-2">
                    <span class="w-1/3 border border-l-0 rounded-r-lg px-3 py-2 bg-gray-50 text-gray-500">{{ product.unit }}</span>
                  </div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Delivery Location</label>
                  <input v-model="rfqForm.delivery_location" type="text" class="mt-1 w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Required By</label>
                  <input v-model="rfqForm.preferred_delivery_date" type="date" class="mt-1 w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Additional Notes</label>
                  <textarea v-model="rfqForm.message" rows="3" class="mt-1 w-full border rounded-lg px-3 py-2"></textarea>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button @click="submitRfq" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 sm:ml-3 sm:w-auto sm:text-sm">
                Submit RFQ
              </button>
              <button @click="showRfqModal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const product = ref(null);
const loading = ref(true);
const showMessageModal = ref(false);
const showRfqModal = ref(false);
const messageText = ref('');
const rfqForm = ref({
  quantity: '',
  delivery_location: '',
  preferred_delivery_date: '',
  message: ''
});

const fetchProduct = async () => {
  try {
    const response = await axios.get(`/products/${route.params.id}`);
    product.value = response.data;
  } catch (error) {
    console.error('Error fetching product:', error);
  } finally {
    loading.value = false;
  }
};

const sendMessage = async () => {
  if (!messageText.value.trim()) return;
  try {
    await axios.post('/messages', {
      receiver_id: product.value.user_id,
      product_id: product.value.id,
      message: messageText.value
    });
    showMessageModal.value = false;
    messageText.value = '';
    alert('Message sent successfully!');
  } catch (error) {
    alert(error.response?.data?.message || 'Failed to send message');
  }
};

const submitRfq = async () => {
  try {
    await axios.post('/rfqs', {
      ...rfqForm.value,
      product_id: product.value.id,
      seller_id: product.value.user_id
    });
    showRfqModal.value = false;
    router.push('/buyer/rfqs');
    alert('RFQ submitted successfully!');
  } catch (error) {
    alert(error.response?.data?.message || 'Failed to submit RFQ');
  }
};

onMounted(() => {
  fetchProduct();
});
</script>
