<template>
  <Navigation />
  <div class="min-h-[90vh] flex flex-col items-center justify-center bg-gray-50/30 p-6 sm:px-12">
    <div class="max-w-md w-full bg-white p-12 rounded-[2.5rem] shadow-xl border border-gray-100">
      <div class="mb-12">
        <h2 class="text-3xl font-black text-agri-forest tracking-tight">Set New Credentials</h2>
        <p class="mt-2 text-sm text-gray-500 font-semibold tracking-wide uppercase">Identity Re-verification</p>
      </div>

      <div v-if="success" class="animate-in fade-in slide-in-from-top-2 duration-300">
        <div class="bg-agri-forest/5 border border-agri-forest/10 rounded-[1.5rem] p-8 text-center">
          <div class="w-16 h-16 rounded-full bg-agri-forest flex items-center justify-center mx-auto mb-6 text-white">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <h4 class="text-lg font-black text-agri-forest mb-2">Protocol Complete</h4>
          <p class="text-sm text-gray-600 font-medium leading-relaxed">
            Your access credentials have been successfully updated. You may now proceed to the trade floor.
          </p>
          <button @click="router.push('/login')" class="w-full mt-8 bg-agri-forest text-white py-4 rounded-2xl font-black uppercase tracking-widest hover:bg-agri-lush transition-all">
            Enter Dashboard
          </button>
        </div>
      </div>

      <form v-else @submit.prevent="handleSubmit" class="space-y-6">
        <div v-if="error" class="bg-red-50 border border-red-100 rounded-2xl p-4 text-xs font-bold text-red-600 uppercase tracking-widest flex items-center space-x-3">
           <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
           <span>{{ error }}</span>
        </div>

        <div class="space-y-4">
          <div class="group opacity-60">
            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Access Email</label>
            <input 
              type="email" 
              v-model="form.email"
              class="w-full px-5 py-4 bg-gray-100 border border-gray-100 rounded-2xl outline-none font-medium cursor-not-allowed"
              readonly
            >
          </div>

          <div class="group">
            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 group-focus-within:text-agri-forest">New Credential Key</label>
            <input 
              type="password" 
              placeholder="••••••••" 
              class="w-full px-5 py-4 bg-gray-50/50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-agri-forest/10 focus:border-agri-forest transition-all outline-none font-medium"
              v-model="form.password"
              required
            >
          </div>

          <div class="group">
            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 group-focus-within:text-agri-forest">Confirm Key</label>
            <input 
              type="password" 
              placeholder="••••••••" 
              class="w-full px-5 py-4 bg-gray-50/50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-agri-forest/10 focus:border-agri-forest transition-all outline-none font-medium"
              v-model="form.password_confirmation"
              required
            >
          </div>
        </div>

        <button
          :disabled="loading"
          class="w-full bg-agri-forest text-white py-5 rounded-2xl font-black text-lg shadow-xl hover:bg-agri-lush transition-all flex items-center justify-center disabled:opacity-50"
        >
          <span v-if="loading" class="flex items-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Updating Registry...
          </span>
          <span v-else>Finalize Credential Update</span>
        </button>
      </form>
    </div>
  </div>
</template>

<script setup lang="js">
import Navigation from '../Navigation.vue'
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const route = useRoute()
const loading = ref(false)
const success = ref(false)
const error = ref(null)

const form = ref({
  token: '',
  email: '',
  password: '',
  password_confirmation: ''
})

onMounted(() => {
  form.value.token = route.query.token || ''
  form.value.email = route.query.email || ''
  
  if (!form.value.token || !form.value.email) {
    error.value = 'Identity protocol failure: Missing token or email. Please request a new link.'
  }
})

const handleSubmit = async () => {
  if (form.value.password !== form.value.password_confirmation) {
    error.value = 'Credential mismatch: Passwords do not match.'
    return
  }
  
  loading.value = true
  error.value = null
  
  try {
    const res = await axios.post('/api/reset-password', form.value)
    success.value = true
  } catch (err) {
    if (err.response?.status === 422 && err.response.data.errors) {
      const errors = err.response.data.errors
      error.value = Object.values(errors).flat().join(' ')
    } else {
      error.value = err.response?.data?.message || 'Update failed. The link may have expired or is invalid.'
    }
  } finally {
    loading.value = false
  }
}
</script>
