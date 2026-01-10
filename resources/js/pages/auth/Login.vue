<template>
  <div class="min-h-screen bg-gradient-to-br from-green-600 to-green-800 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-xl shadow-2xl p-8">
      <div>
        <h2 class="text-center text-3xl font-bold text-gray-900 mb-2">
          Welcome Back
        </h2>
        <p class="text-center text-gray-600 mb-8">Sign in to your account</p>
      </div>
      
      <form @submit.prevent="handleLogin" class="flex flex-col gap-8">
        <!-- High-Fidelity Error Registry -->
        <div v-if="error" class="animate-in fade-in slide-in-from-top-2 duration-300">
           <!-- Revocation Specific UI -->
           <div v-if="error.includes('revoked')" class="bg-red-50/50 border border-red-100 rounded-[1.5rem] p-6 flex items-start space-x-4 shadow-sm relative overflow-hidden group">
              <div class="absolute inset-y-0 left-0 w-1.5 bg-red-500"></div>
              <div class="w-12 h-12 rounded-2xl bg-red-100 flex items-center justify-center flex-shrink-0">
                 <svg class="w-6 h-6 text-red-600 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                 </svg>
              </div>
              <div class="flex-1">
                 <h4 class="text-[10px] font-black text-red-600 uppercase tracking-[0.2em] mb-1">Access Protocol Violation</h4>
                 <p class="text-xs font-bold text-gray-900 leading-relaxed">{{ error }}</p>
                 <div class="mt-4 flex items-center space-x-4">
                    <a href="mailto:support@agriglobal.com" class="text-[9px] font-black text-red-600 uppercase tracking-widest hover:underline transition-all">Emergency Support</a>
                    <span class="w-1 h-1 rounded-full bg-red-200"></span>
                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Protocol ID: #REVOKED</span>
                 </div>
              </div>
           </div>

           <!-- Standard Error UI -->
           <div v-else class="bg-gray-50 border border-gray-100 rounded-2xl px-6 py-4 flex items-center space-x-4">
              <div class="w-1.5 h-6 bg-red-400 rounded-full"></div>
              <p class="text-[11px] font-black text-gray-600 uppercase tracking-tighter">{{ error }}</p>
           </div>
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
            placeholder="you@example.com"
          />
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
            placeholder="••••••••"
          />
        </div>

        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <input
              id="remember"
              v-model="form.remember"
              type="checkbox"
              class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
            />
            <label for="remember" class="ml-2 block text-sm text-gray-700">
              Remember me
            </label>
          </div>
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="loading">Signing in...</span>
          <span v-else>Sign In</span>
        </button>

        <div class="text-center">
          <p class="text-sm text-gray-600">
            Don't have an account?
            <router-link to="/register" class="font-medium text-green-600 hover:text-green-500">
              Register here
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
  email: '',
  password: '',
  remember: false,
});

const loading = ref(false);
const error = ref('');

const handleLogin = async () => {
  loading.value = true;
  error.value = '';

  const result = await authStore.login(form.value);

  if (result.success) {
    // Redirect based on permissions
    if (authStore.can('access_super_admin')) {
        router.push('/super-admin');
    } else if (authStore.can('access_admin')) {
        router.push('/admin/dashboard'); // Or whatever the admin landing is
    } else if (authStore.can('access_vendor')) {
        router.push('/vendor/dashboard'); // Ensure this route exists or matches sidebar
    } else {
      router.push('/dashboard'); // Standard user dashboard
    }
  } else {
    error.value = result.message;
    
    // Cinematic Revocation Toast
    if (result.message.includes('revoked') && window.notify) {
       window.notify.error('Access Revoked', result.message, 0); // Persistent until closed
    }
  }

  loading.value = false;
};
</script>
