<template>
  <div class="min-h-screen bg-gradient-to-br from-green-600 to-green-800 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl w-full bg-white rounded-xl shadow-2xl p-8">
      <div>
        <h2 class="text-center text-3xl font-bold text-gray-900 mb-2">
          Create Your Account
        </h2>
        <p class="text-center text-gray-600 mb-8">Join the International Agro Marketplace</p>
      </div>
      
      <form @submit.prevent="handleRegister" class="flex flex-col gap-6">
        <div v-if="error" class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
          {{ error }}
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
              Full Name
            </label>
            <input
              id="name"
              v-model="form.name"
              type="text"
              required
              class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500"
            />
            <span v-if="errors.name" class="text-red-600 text-xs">{{ errors.name[0] }}</span>
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
              Email Address
            </label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500"
            />
            <span v-if="errors.email" class="text-red-600 text-xs">{{ errors.email[0] }}</span>
          </div>

          <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
              Phone Number
            </label>
            <input
              id="phone"
              v-model="form.phone"
              type="tel"
              class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500"
            />
          </div>

          <div>
            <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
              Location
            </label>
            <input
              id="location"
              v-model="form.location"
              type="text"
              class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500"
            />
          </div>

          <div>
            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
              I am a
            </label>
            <select
              id="role"
              v-model="form.role"
              required
              class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-green-500 focus:border-green-500"
            >
              <option value="">Select...</option>
              <option value="vendor">Seller (Farmer/Trader/Company)</option>
              <option value="user">Buyer</option>
            </select>
          </div>

          <div v-if="form.role === 'vendor'">
            <label for="user_type" class="block text-sm font-medium text-gray-700 mb-2">
              Business Type
            </label>
            <select
              id="user_type"
              v-model="form.user_type"
              class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-green-500 focus:border-green-500"
            >
              <option value="farmer">Farmer</option>
              <option value="trader">Trader/Aggregator</option>
              <option value="company">Agri Company</option>
            </select>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
              Password
            </label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              required
              class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500"
            />
            <span v-if="errors.password" class="text-red-600 text-xs">{{ errors.password[0] }}</span>
          </div>

          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
              Confirm Password
            </label>
            <input
              id="password_confirmation"
              v-model="form.password_confirmation"
              type="password"
              required
              class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500"
            />
          </div>
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
        >
          <span v-if="loading">Creating Account...</span>
          <span v-else>Create Account</span>
        </button>

        <div class="text-center">
          <p class="text-sm text-gray-600">
            Already have an account?
            <router-link to="/login" class="font-medium text-green-600 hover:text-green-500">
              Sign in
            </router-link>
          </p>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const form = ref({
  name: '',
  email: '',
  phone: '',
  location: '',
  role: '',
  user_type: 'farmer',
  password: '',
  password_confirmation: '',
});

const loading = ref(false);
const error = ref('');
const errors = ref({});

const handleRegister = async () => {
  loading.value = true;
  error.value = '';
  errors.value = {};

  const result = await authStore.register(form.value);

  if (result.success) {
    router.push('/subscriptions');
  } else {
    error.value = result.message;
    errors.value = result.errors || {};
  }

  loading.value = false;
};
</script>
