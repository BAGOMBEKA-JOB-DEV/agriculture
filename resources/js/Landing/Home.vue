<template>
  <!-- Navigation -->
  <Navigation />

  <div class="font-sans text-gray-900 overflow-x-hidden">
    <!-- Hero Section -->
    <section class="relative min-h-[90vh] flex items-center overflow-hidden">
      <!-- Background Image with Overlay -->
      <div class="absolute inset-0 z-0">
        <img 
          :src="'/images/hero-agri.png'" 
          alt="Modern Agriculture" 
          class="w-full h-full object-cover"
        >
        <div class="absolute inset-0 bg-gradient-to-r from-agri-forest/90 via-agri-forest/60 to-transparent"></div>
      </div>

      <div class="container mx-auto px-6 relative z-10 text-white pt-20">
        <div class="max-w-3xl">
          <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-agri-earth/20 border border-agri-earth/30 text-agri-earth text-xs font-bold uppercase tracking-widest mb-6 backdrop-blur-sm">
            <span class="w-2 h-2 rounded-full bg-agri-earth animate-pulse"></span>
            <span>Global Agro-Trade Network</span>
          </div>
          <h1 class="text-5xl md:text-7xl font-black mb-6 leading-tight tracking-tighter">
            Connecting <span class="text-agri-earth text-gradient">Global Harvests</span> to International Markets
          </h1>
          <p class="text-xl text-gray-200 mb-10 max-w-2xl leading-relaxed font-medium">
            Empowering producers and traders through a high-integrity marketplace. Trade premium agricultural commodities with complete transparency and verified logistics.
          </p>
          <div class="flex flex-col sm:flex-row gap-5">
            <router-link
              to="/register"
              class="bg-agri-earth text-white px-10 py-5 rounded-2xl text-lg font-black hover:bg-agri-sand hover:text-agri-forest transition-all duration-300 shadow-2xl hover:shadow-agri-earth/20 hover:-translate-y-1 text-center"
            >
              Start Trading Now
            </router-link>
            <router-link
              to="/marketplace"
              class="bg-white/10 backdrop-blur-md border border-white/20 text-white px-10 py-5 rounded-2xl text-lg font-black hover:bg-white/20 transition-all duration-300 text-center"
            >
              Browse Marketplace
            </router-link>
          </div>
          
          <div class="mt-16 grid grid-cols-3 gap-8 border-t border-white/10 pt-8 max-w-xl mb-5">
            <div>
              <p class="text-3xl font-black text-white">45k+</p>
              <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Verified Sellers</p>
            </div>
             <div>
              <p class="text-3xl font-black text-white">$1.2B</p>
              <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Trade Volume</p>
            </div>
             <div>
              <p class="text-3xl font-black text-white">120+</p>
              <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Global Ports</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Trusted By / Logos Section -->
    <section class="py-12 bg-white border-b border-gray-100">
      <div class="container mx-auto px-6">
        <p class="text-center text-[10px] font-black text-gray-400 uppercase tracking-widest mb-8">Trusted by global agriculture leaders</p>
        <div class="flex flex-wrap justify-center items-center gap-12 opacity-40 grayscale hover:grayscale-0 transition-all duration-500">
           <span class="text-2xl font-black tracking-tighter text-gray-900 italic">AGRI-GLOBAL</span>
           <span class="text-2xl font-black tracking-tighter text-gray-900 italic">HARVEST-X</span>
           <span class="text-2xl font-black tracking-tighter text-gray-900 italic">TERRA-PLUS</span>
           <span class="text-2xl font-black tracking-tighter text-gray-900 italic">EURO-AGRI</span>
           <span class="text-2xl font-black tracking-tighter text-gray-900 italic">AFRO-TRADE</span>
        </div>
      </div>
    </section>

    <!-- Value Propositions -->
    <section class="py-24 bg-gray-50/50">
      <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-20 items-center">
          <div>
            <div class="w-16 h-2 bg-agri-earth mb-8 rounded-full"></div>
            <h2 class="text-4xl md:text-5xl font-black text-agri-forest mb-8 leading-tight">
              Enterprise Trading Infrastructure for Modern Farmers
            </h2>
            <div class="space-y-8">
              <div class="flex items-start">
                <div class="w-12 h-12 rounded-2xl bg-agri-sand/30 flex items-center justify-center text-agri-forest shrink-0 mr-6">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-xl font-bold text-gray-900 mb-2">Verified Origins</h3>
                  <p class="text-gray-600 font-medium">Every listing undergores a rigorous verification process to ensure source integrity and quality standards are exceeded.</p>
                </div>
              </div>
              
              <div class="flex items-start">
                <div class="w-12 h-12 rounded-2xl bg-agri-sand/30 flex items-center justify-center text-agri-forest shrink-0 mr-6">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-xl font-bold text-gray-900 mb-2">Smart Logisitic Tracking</h3>
                  <p class="text-gray-600 font-medium">Real-time container and batch tracking from the field to the destination port, providing total peace of mind.</p>
                </div>
              </div>

               <div class="flex items-start">
                <div class="w-12 h-12 rounded-2xl bg-agri-sand/30 flex items-center justify-center text-agri-forest shrink-0 mr-6">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-xl font-bold text-gray-900 mb-2">Financial Settlement</h3>
                  <p class="text-gray-600 font-medium">Secure multi-currency escrow services and localized payment options for international trade simplified.</p>
                </div>
              </div>
            </div>
          </div>
          
          <div class="relative">
             <div class="absolute -top-10 -left-10 w-40 h-40 bg-agri-earth/10 rounded-full blur-3xl"></div>
             <div class="absolute -bottom-10 -right-10 w-60 h-60 bg-agri-lush/10 rounded-full blur-3xl"></div>
             <div class="relative rounded-3xl overflow-hidden shadow-2xl border-8 border-white group">
                <img 
                  :src="'/images/agri-trade.png'" 
                  alt="Agriculture Logistics" 
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                >
                <div class="absolute bottom-6 left-6 right-6 p-6 bg-white/90 backdrop-blur-md rounded-2xl shadow-xl">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-full bg-agri-forest flex items-center justify-center text-white">
                             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                             </svg>
                        </div>
                        <div>
                            <p class="text-sm font-black text-agri-forest italic uppercase">Certified Export Quality</p>
                            <p class="text-xs text-gray-500 font-bold">Audit Date: Jan 2026 • Status: Passing</p>
                        </div>
                    </div>
                </div>
             </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Final CTA Section -->
    <section class="py-24 bg-agri-forest relative overflow-hidden text-center">
       <div class="absolute top-0 right-0 w-96 h-96 bg-agri-lush opacity-30 rounded-full blur-[120px] -translate-y-1/2 translate-x-1/2"></div>
       <div class="absolute bottom-0 left-0 w-96 h-96 bg-agri-earth opacity-10 rounded-full blur-[120px] translate-y-1/2 -translate-x-1/2"></div>
       
       <div class="container mx-auto px-6 relative z-10">
          <h2 class="text-5xl md:text-6xl font-black text-white mb-8 tracking-tighter">Ready to expand your <br> <span class="text-agri-earth">agricultural footprint?</span></h2>
          <p class="text-xl text-agri-earth/80 max-w-2xl mx-auto mb-12 font-medium">Join the fast-growing network of verified producers and traders on the International Agro Marketplace.</p>
          <div class="flex flex-col sm:flex-row justify-center gap-6">
             <router-link to="/register" class="bg-agri-earth text-white px-12 py-5 rounded-2xl text-lg font-black hover:bg-agri-sand hover:text-agri-forest transition-all shadow-xl">Register Account</router-link>
             <button class="bg-transparent border-2 border-white/20 text-white px-12 py-5 rounded-2xl text-lg font-black hover:bg-white/10 transition-all">Support Desk</button>
          </div>
       </div>
    </section>

    <Footer />
  </div>
</template>

<script setup>
import Navigation from '../components/Navigation.vue'
import Footer from '../components/Footer.vue'
</script>

<style scoped>
.text-gradient {
  background: linear-gradient(to right, #D4A373, #E9C46A);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}
</style>
