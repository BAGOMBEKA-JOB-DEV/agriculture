<template>
  <Navigation />
  <div class="min-h-[90vh] flex flex-col items-center justify-center bg-gray-50/30 p-6 sm:px-12">
    <div class="max-w-md w-full bg-white p-12 rounded-[2.5rem] shadow-xl border border-gray-100">
      <div class="mb-12">
        <h2 class="text-3xl font-black text-agri-forest tracking-tight">Lost Access</h2>
        <p class="mt-2 text-sm text-gray-500 font-semibold tracking-wide uppercase">Initiate Recovery Protocol</p>
      </div>

      <div v-if="success" class="animate-in fade-in slide-in-from-top-2 duration-300">
        <div class="bg-agri-forest/5 border border-agri-forest/10 rounded-[1.5rem] p-8 text-center">
          <div class="w-16 h-16 rounded-full bg-agri-forest/10 flex items-center justify-center mx-auto mb-6 text-agri-forest">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <h4 class="text-lg font-black text-agri-forest mb-2">Protocol Initiated</h4>
          <p class="text-sm text-gray-600 font-medium leading-relaxed">
            If an account exists for <span class="text-agri-forest font-bold">{{ email }}</span>, you will receive a recovery link shortly.
          </p>
          <button @click="router.push('/login')" class="mt-8 text-xs font-black text-agri-earth uppercase tracking-widest hover:underline">
            Return to Login
          </button>
        </div>
      </div>

      <form v-else @submit.prevent="handleSubmit" class="space-y-6">
        <Alert v-if="error" type="error" title="Recovery Failed" @close="error = null">
          {{ error }}
        </Alert>

        <div class="group">
          <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 group-focus-within:text-agri-forest">Registered Email</label>
          <input 
            type="email" 
            placeholder="recovery@agro.iam" 
            class="w-full px-5 py-4 bg-gray-50/50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-agri-forest/10 focus:border-agri-forest transition-all outline-none font-medium"
            v-model="email"
            required
          >
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
            Processing...
          </span>
          <span v-else>Request Recovery Link</span>
        </button>

        <div class="text-center pt-8">
          <router-link to="/login" class="text-sm font-black text-gray-400 hover:text-agri-forest uppercase tracking-widest">
            Back to Login
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="js">
import Navigation from '../Navigation.vue'
import Alert from '../Form/Alert.vue'
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const email = ref('')
const loading = ref(false)
const success = ref(false)
const error = ref(null)

const handleSubmit = async () => {
  loading.value = true
  error.value = null
  
  try {
    await axios.post('/api/forgot-password', { email: email.value })
    success.value = true
  } catch (err) {
    error.value = err.response?.data?.message || 'Identity recovery failed. Please check your network connection.'
  } finally {
    loading.value = false
  }
}
</script>
