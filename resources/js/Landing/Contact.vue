<template>
  <!-- Navigation -->
  <Navigation />

  <div class="font-sans text-gray-900 overflow-x-hidden bg-white">
    <!-- Hero Section -->
    <section class="bg-gray-50/50 py-32 border-b border-gray-100">
      <div class="container mx-auto px-6 text-center">
        <h1 class="text-5xl md:text-7xl font-black text-agri-forest mb-6 tracking-tighter">
          Logistics <span class="text-agri-earth underline decoration-agri-earth/30">Support</span>
        </h1>
        <p class="text-xl text-gray-500 max-w-2xl mx-auto font-medium leading-relaxed">
          Need assistance with your trade identity, container tracking, or marketplace listings? Our global desk is ready to assist.
        </p>
      </div>
    </section>

    <!-- Contact Section -->
    <section class="py-24">
      <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-20">
          <!-- Contact Form -->
          <div class="bg-white p-10 md:p-12 rounded-[2.5rem] shadow-2xl shadow-agri-forest/5 border border-gray-100">
            <h2 class="text-3xl font-black text-agri-forest mb-8 tracking-tight">Inquiry Portal</h2>
            <form @submit.prevent="submitForm" class="space-y-6">
              <div class="grid md:grid-cols-2 gap-6">
                 <div class="group">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 group-focus-within:text-agri-forest transition-colors">Full Authority Name</label>
                    <input
                      v-model="form.name"
                      type="text"
                      class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest transition-all outline-none font-medium text-sm"
                      placeholder="John Smith"
                      required
                    />
                  </div>
                  <div class="group">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 group-focus-within:text-agri-forest transition-colors">Trade Email</label>
                    <input
                      v-model="form.email"
                      type="email"
                      class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest transition-all outline-none font-medium text-sm"
                      placeholder="compliance@agro.iam"
                      required
                    />
                  </div>
              </div>
              
              <div class="group">
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 group-focus-within:text-agri-forest transition-colors">Detailed Briefing</label>
                <textarea
                  v-model="form.message"
                  rows="6"
                  class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-agri-forest/5 focus:border-agri-forest transition-all outline-none font-medium text-sm resize-none"
                  placeholder="Explain your situation in detail..."
                  required
                ></textarea>
              </div>

              <button
                type="submit"
                :disabled="loading"
                class="w-full bg-agri-forest text-white py-5 rounded-2xl font-black text-lg shadow-xl hover:bg-agri-lush hover:shadow-agri-forest/20 hover:-translate-y-1 transition-all disabled:opacity-50 disabled:translate-y-0"
              >
                <span v-if="loading">Encrypting & Sending...</span>
                <span v-else>Submit Secure Request</span>
              </button>
            </form>
          </div>

          <!-- Contact Info -->
          <div class="flex flex-col justify-center">
            <h2 class="text-4xl font-black text-agri-forest mb-12 tracking-tight">Global Trade Hubs</h2>
            <div class="space-y-12">
              <div class="flex items-start group">
                <div class="w-16 h-16 bg-agri-sand/30 rounded-2xl flex items-center justify-center text-agri-forest shrink-0 mr-8 transition-transform group-hover:scale-110">
                  <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-xl font-black text-gray-900 mb-2">Corporate Support</h3>
                  <p class="text-gray-500 font-bold mb-1">priority@agrixchange.iam</p>
                  <p class="text-gray-500 font-bold">compliance@agrixchange.iam</p>
                </div>
              </div>

              <div class="flex items-start group">
                <div class="w-16 h-16 bg-agri-sand/30 rounded-2xl flex items-center justify-center text-agri-forest shrink-0 mr-8 transition-transform group-hover:scale-110">
                  <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-xl font-black text-gray-900 mb-2">IAM London Hub</h3>
                  <p class="text-gray-500 font-bold leading-relaxed">18 Bishopsgate, Level 42<br>Business District, London, UK</p>
                </div>
              </div>

               <div class="flex items-start group">
                <div class="w-16 h-16 bg-agri-sand/30 rounded-2xl flex items-center justify-center text-agri-forest shrink-0 mr-8 transition-transform group-hover:scale-110">
                  <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-xl font-black text-gray-900 mb-2">Emergency Response</h3>
                  <p class="text-gray-500 font-bold">Global: +44 20 7946 0123</p>
                  <p class="text-xs text-agri-earth font-black uppercase mt-1 tracking-widest">24/7 Operations Desk</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-950 text-white py-12 border-t border-white/5">
      <div class="container mx-auto px-6 text-center">
        <p class="text-xs font-bold text-gray-600 uppercase tracking-widest">&copy; 2026 International Agro Marketplace. Built for Reliability.</p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import Navigation from '../components/Navigation.vue'
import { ref, reactive } from 'vue'

const loading = ref(false)

const form = reactive({
  name: '',
  email: '',
  message: '',
})

const submitForm = async () => {
  loading.value = true
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1500))
    if (window.notify) {
        window.notify.success('Inquiry Logged', 'Your request has been received by our global dispatch team.')
    }
    form.name = ''
    form.email = ''
    form.message = ''
  } catch (error) {
    console.error('Error sending message:', error)
  } finally {
    loading.value = false
  }
}
</script>
