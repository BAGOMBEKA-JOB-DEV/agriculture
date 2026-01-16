<template>
  <!-- Navigation -->
  <Navigation />

  <div class="min-h-[90vh] flex flex-col lg:flex-row bg-white">
    <!-- Left side - Onboarding Guide -->
    <div class="lg:w-1/3 relative overflow-hidden bg-agri-forest flex flex-col justify-start p-12">
      <div class="absolute inset-0 z-0">
        <img :src="'/images/agri-trade.png'" alt="Agriculture Trade" class="w-full h-auto object-cover opacity-20">
        <div class="absolute inset-0 bg-gradient-to-br from-agri-forest via-agri-forest/80 to-transparent"></div>
      </div>
      
      <div class="relative z-10 text-white mt-10">
         <h2 class="text-4xl font-black mb-8 tracking-tighter leading-tight">Join the <br><span class="text-agri-earth text-gradient">IAM Trade Ecosystem</span></h2>
         
         <div class="space-y-10">
            <div class="flex items-start group">
                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-agri-earth shrink-0 mr-4 transition-transform group-hover:scale-110">1</div>
                <div>
                   <h3 class="font-bold text-white mb-1">Define Your Identity</h3>
                   <p class="text-sm text-gray-400 font-medium">Select your primary trade role to get a tailored dashboard and features.</p>
                </div>
            </div>
            <div class="flex items-start group">
                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-agri-earth shrink-0 mr-4 transition-transform group-hover:scale-110">2</div>
                <div>
                   <h3 class="font-bold text-white mb-1">Verification Phase</h3>
                   <p class="text-sm text-gray-400 font-medium">Our compliance team reviews your credentials to maintain high network integrity.</p>
                </div>
            </div>
            <div class="flex items-start group">
                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-agri-earth shrink-0 mr-4 transition-transform group-hover:scale-110">3</div>
                <div>
                   <h3 class="font-bold text-white mb-1">Global Trade Access</h3>
                   <p class="text-sm text-gray-400 font-medium">Once approved, start listing inventory or submitting procurement RFQs globally.</p>
                </div>
            </div>
         </div>
      </div>
    </div>

    <!-- Right side - Registration Form -->
    <div class="flex-1 flex items-center justify-center py-20 px-6 sm:px-12 lg:w-2/3 bg-gray-50/50">
      <div class="max-w-2xl w-full">
        <div class="mb-10 text-center lg:text-left">
          <h2 class="text-3xl font-black text-agri-forest tracking-tight">Access Request</h2>
          <p class="mt-2 text-sm text-gray-500 font-semibold tracking-wide uppercase">New Member Onboarding • International Agro Marketplace</p>
        </div>

        <!-- Onboarding Alert -->
        <Alert 
          v-if="regError" 
          type="error" 
          title="Onboarding Error" 
          class="mb-8"
          @close="regError = null"
        >
          {{ regError }}
        </Alert>
        
        <form class="space-y-8" @submit.prevent="handleRegister">
          <!-- Role Selection (As per plan.md) -->
          <div class="space-y-4">
             <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Select Your Trade Authority</label>
             <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div 
                  @click="form.role = 'vendor'" 
                  :class="['p-5 rounded-2xl border-2 transition-all cursor-pointer group', form.role === 'vendor' ? 'bg-agri-forest border-agri-forest text-white' : 'bg-white border-gray-100 text-gray-500 hover:border-agri-forest/30']"
                >
                   <div :class="['w-10 h-10 rounded-xl flex items-center justify-center mb-4 transition-colors', form.role === 'vendor' ? 'bg-agri-earth text-agri-forest' : 'bg-gray-50 text-gray-400 group-hover:text-agri-forest']">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                      </svg>
                   </div>
                   <p class="font-black uppercase tracking-widest text-xs mb-1">Producer/Farmer</p>
                   <p class="text-[10px] font-bold opacity-60 uppercase">List Harvests & Direct Sales</p>
                </div>

                <div 
                  @click="form.role = 'buyer'" 
                  :class="['p-5 rounded-2xl border-2 transition-all cursor-pointer group', form.role === 'buyer' ? 'bg-agri-forest border-agri-forest text-white' : 'bg-white border-gray-100 text-gray-500 hover:border-agri-forest/30']"
                >
                   <div :class="['w-10 h-10 rounded-xl flex items-center justify-center mb-4 transition-colors', form.role === 'buyer' ? 'bg-agri-earth text-agri-forest' : 'bg-gray-50 text-gray-400 group-hover:text-agri-forest']">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                      </svg>
                   </div>
                   <p class="font-black uppercase tracking-widest text-xs mb-1">Procurement/Buyer</p>
                   <p class="text-[10px] font-bold opacity-60 uppercase">Sourcing & Multi-Vendor RFQs</p>
                </div>

                <div 
                  @click="form.role = 'trader'" 
                  :class="['p-5 rounded-2xl border-2 transition-all cursor-pointer group', form.role === 'trader' ? 'bg-agri-forest border-agri-forest text-white' : 'bg-white border-gray-100 text-gray-500 hover:border-agri-forest/30']"
                >
                   <div :class="['w-10 h-10 rounded-xl flex items-center justify-center mb-4 transition-colors', form.role === 'trader' ? 'bg-agri-earth text-agri-forest' : 'bg-gray-50 text-gray-400 group-hover:text-agri-forest']">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                      </svg>
                   </div>
                   <p class="font-black uppercase tracking-widest text-xs mb-1">Trade Broker</p>
                   <p class="text-[10px] font-bold opacity-60 uppercase">Facilitate Arbitrage & Logistics</p>
                </div>
             </div>
             <p v-if="errors.role" class="text-xs text-red-500 font-bold mt-2">{{ errors.role[0] }}</p>
          </div>

          <!-- Basic Info -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="group">
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 group-focus-within:text-agri-forest transition-colors">Trade Name / Entity</label>
              <input 
                type="text" 
                placeholder="AgriCorp International" 
                class="w-full px-5 py-4 bg-white border border-gray-100 rounded-2xl shadow-sm focus:ring-4 focus:ring-agri-forest/10 focus:border-agri-forest transition-all outline-none font-medium text-sm"
                v-model="form.name"
                required
              >
              <p v-if="errors.name" class="mt-2 text-xs text-red-500 font-bold">{{ errors.name[0] }}</p>
            </div>

            <div class="group">
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 group-focus-within:text-agri-forest transition-colors">Business Intelligence Email</label>
              <input 
                type="email" 
                placeholder="headquarters@agro.iam" 
                class="w-full px-5 py-4 bg-white border border-gray-100 rounded-2xl shadow-sm focus:ring-4 focus:ring-agri-forest/10 focus:border-agri-forest transition-all outline-none font-medium text-sm"
                v-model="form.email"
                required
              >
              <p v-if="errors.email" class="mt-2 text-xs text-red-500 font-bold">{{ errors.email[0] }}</p>
            </div>

            <div class="group">
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 group-focus-within:text-agri-forest transition-colors">Secure Passkey</label>
              <div class="relative">
                <input 
                  :type="showPassword ? 'text' : 'password'" 
                  placeholder="••••••••" 
                  class="w-full px-5 py-4 bg-white border border-gray-100 rounded-2xl shadow-sm focus:ring-4 focus:ring-agri-forest/10 focus:border-agri-forest transition-all outline-none font-medium text-sm pr-12"
                  v-model="form.password"
                  required
                >
                <button 
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-agri-forest transition-colors focus:outline-none p-1"
                >
                  <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88L1 1m11 11l9 9" />
                  </svg>
                </button>
              </div>
              <p v-if="errors.password" class="mt-2 text-xs text-red-500 font-bold">{{ errors.password[0] }}</p>
            </div>

            <div class="group">
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 group-focus-within:text-agri-forest transition-colors">Confirm Integrity</label>
              <div class="relative">
                <input 
                  :type="showConfirmPassword ? 'text' : 'password'" 
                  placeholder="••••••••" 
                  class="w-full px-5 py-4 bg-white border border-gray-100 rounded-2xl shadow-sm focus:ring-4 focus:ring-agri-forest/10 focus:border-agri-forest transition-all outline-none font-medium text-sm pr-12"
                  v-model="form.password_confirmation"
                  required
                >
                <button 
                  type="button"
                  @click="showConfirmPassword = !showConfirmPassword"
                  class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-agri-forest transition-colors focus:outline-none p-1"
                >
                  <svg v-if="!showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88L1 1m11 11l9 9" />
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <div class="flex items-start space-x-3 p-4 bg-gray-100/50 rounded-2xl">
             <input type="checkbox" required class="mt-1 w-4 h-4 rounded border-gray-300 text-agri-forest focus:ring-agri-forest">
             <p class="text-xs text-gray-500 font-medium leading-relaxed">
               I acknowledge that my identity will be verified against the IAM Protocol and I agree to the <a href="#" class="text-agri-forest font-bold hover:underline">Global Trade Integrity Charter</a>.
             </p>
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
              Initializing...
            </span>
            <span v-else>Request Hub Access</span>
          </button>

          <div class="text-center">
            <p class="text-sm font-bold text-gray-400">
              Already a Member?
              <router-link to="/login" class="text-agri-earth hover:text-agri-sand ml-2">Secure Login</router-link>
            </p>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="js">
import Navigation from '../Navigation.vue'
import Alert from '../Form/Alert.vue'
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'

const router = useRouter()
const authStore = useAuthStore()
const form = ref({
  name: '',
  email: '',
  role: 'vendor', // Default to producer
  password: '',
  password_confirmation: ''
})
const loading = ref(false)
const errors = ref({})
const regError = ref(null)
const showPassword = ref(false)
const showConfirmPassword = ref(false)

const handleRegister = async () => {
  loading.value = true
  errors.value = {}
  regError.value = null
  
  const result = await authStore.register(form.value)
  
  if (result.success) {
    if (window.notify) {
      window.notify.success('Access Request Received', 'Please await identity verification for your ' + form.value.role + ' account.')
    }
    router.push('/login')
  } else {
    if (result.errors) {
      errors.value = result.errors
      regError.value = "Identity verification failed. Please check the required fields below."
    } else {
      regError.value = result.message || 'Onboarding encountererd an unexpected protocol error.'
    }
    loading.value = false
  }
}
</script>

<style scoped>
.text-gradient {
  background: linear-gradient(to right, #D4A373, #E9C46A);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}
</style>
