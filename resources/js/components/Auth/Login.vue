<template>
  <!-- Navigation -->
  <Navigation />

  <div class="min-h-[90vh] flex flex-col lg:flex-row bg-white">
    <!-- Left side - Branding & Context -->
    <div class="lg:w-1/2 relative overflow-hidden bg-agri-forest flex items-center justify-center p-12">
      <div class="absolute inset-0 z-0">
        <img :src="heroAgri" alt="Agriculture Trade" class="w-full h-full object-cover opacity-30">
        <div class="absolute inset-0 bg-gradient-to-br from-agri-forest via-agri-forest/80 to-transparent"></div>
      </div>
      
      <div class="relative z-10 max-w-md text-white">
         <div class="w-16 h-16 rounded-2xl bg-agri-earth flex items-center justify-center shadow-2xl mb-10">
            <svg class="w-10 h-10 text-agri-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
         </div>
         <h2 class="text-4xl font-black mb-6 tracking-tight leading-tight">Access the Global <br><span class="text-agri-earth">Agro-Trading Floor</span></h2>
         <p class="text-lg text-gray-300 font-medium mb-12 leading-relaxed">Enter your credentials to manage your inventory, RFQs, and cross-border settlements with enterprise-grade security.</p>
         
         <div class="space-y-6">
            <div class="flex items-center space-x-4">
                <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-agri-earth">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <span class="text-sm font-bold opacity-80 uppercase tracking-widest text-white">Verified Security</span>
            </div>
             <div class="flex items-center space-x-4">
                <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-agri-earth">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="text-sm font-bold opacity-80 uppercase tracking-widest text-white">Encrypted Trade Flow</span>
            </div>
         </div>
      </div>
    </div>

    <!-- Right side - Form -->
    <div class="flex-1 flex items-center justify-center py-20 px-6 sm:px-12 lg:w-1/2 bg-gray-50/30">
      <div class="max-w-md w-full">
        <div class="mb-12">
          <h2 class="text-3xl font-black text-agri-forest tracking-tight">System Login</h2>
          <p class="mt-2 text-sm text-gray-500 font-semibold tracking-wide uppercase">Identity Portal • IAM Hub</p>
        </div>
        
        <form class="space-y-6" @submit.prevent="handleLogin">
          <div class="space-y-4">
            <div class="group">
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 group-focus-within:text-agri-forest transition-colors">Access Email</label>
              <input 
                type="email" 
                placeholder="registered@agro.iam" 
                class="w-full px-5 py-4 bg-white border border-gray-100 rounded-2xl shadow-sm focus:ring-4 focus:ring-agri-forest/10 focus:border-agri-forest transition-all outline-none font-medium"
                v-model="form.email"
                required
                autocomplete="username"
              >
              <p v-if="errors.email" class="mt-2 text-xs text-red-500 font-bold">{{ errors.email[0] }}</p>
            </div>

            <div class="group">
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 group-focus-within:text-agri-forest transition-colors">Credential Key</label>
              <input 
                type="password" 
                placeholder="••••••••" 
                class="w-full px-5 py-4 bg-white border border-gray-100 rounded-2xl shadow-sm focus:ring-4 focus:ring-agri-forest/10 focus:border-agri-forest transition-all outline-none font-medium"
                v-model="form.password"
                required
                autocomplete="current-password"
              >
              <p v-if="errors.password" class="mt-2 text-xs text-red-500 font-bold">{{ errors.password[0] }}</p>
            </div>
          </div>

          <div class="flex items-center justify-between py-2 text-sm font-bold">
             <label class="flex items-center space-x-2 text-gray-400 cursor-pointer hover:text-agri-forest transition-colors">
                <input type="checkbox" class="w-4 h-4 rounded border-gray-200 text-agri-forest focus:ring-agri-forest">
                <span>Maintain Session</span>
             </label>
             <a href="#" class="text-agri-earth hover:text-agri-sand transition-colors">Lost Access?</a>
          </div>

          <button
            :disabled="loading"
            class="w-full bg-agri-forest text-white py-5 rounded-2xl font-black text-lg shadow-xl hover:bg-agri-lush hover:shadow-agri-forest/20 hover:-translate-y-1 transition-all flex items-center justify-center disabled:opacity-50 disabled:translate-y-0"
          >
            <span v-if="loading" class="flex items-center">
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Verifying...
            </span>
            <span v-else>Authorize Entry</span>
          </button>

          <div class="text-center pt-8 border-t border-gray-100">
            <p class="text-sm font-bold text-gray-400">
              New to the Network?
              <router-link to="/register" class="text-agri-earth hover:text-agri-sand ml-2">Request Account Access</router-link>
            </p>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="js">
import Navigation from '../Navigation.vue'
import heroAgri from 'assets/hero-agri.png'
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'

const router = useRouter()
const authStore = useAuthStore()
const form = ref({
  email: '',
  password: ''
})
const loading = ref(false)
const errors = ref({})

const handleLogin = async () => {
  loading.value = true
  errors.value = {}
  
  const result = await authStore.login(form.value)
  
  if (result.success) {
    router.push('/dashboard')
  } else {
    if (result.errors) {
      errors.value = result.errors
    } else if (window.notify) {
      window.notify.error('Auth Denied', result.message || 'Invalid Credentials')
    }
    loading.value = false
  }
}
</script>
