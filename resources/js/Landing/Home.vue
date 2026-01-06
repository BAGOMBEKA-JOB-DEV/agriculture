<template>
  <!-- Navigation -->
  <Navigation />

  <div class="font-sans text-gray-900 overflow-x-hidden">
    <!-- Hero Section -->
    <section class="relative min-h-[90vh] flex items-center overflow-hidden">
      <!-- Background Image with Overlay -->
      <div class="absolute inset-0 z-0">
        <img 
          :src="heroAgri" 
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
              to="/products"
              class="bg-white/10 backdrop-blur-md border border-white/20 text-white px-10 py-5 rounded-2xl text-lg font-black hover:bg-white/20 transition-all duration-300 text-center"
            >
              Browse Marketplace
            </router-link>
          </div>
          
          <div class="mt-16 grid grid-cols-3 gap-8 border-t border-white/10 pt-8 max-w-xl">
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
                  :src="agriTrade" 
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

    <!-- Footer -->
    <footer class="bg-gray-950 text-white py-20 border-t border-white/5">
      <div class="container mx-auto px-6">
        <div class="grid md:grid-cols-4 gap-12 mb-16">
          <div class="col-span-1 md:col-span-1">
            <div class="flex items-center space-x-3 mb-6">
               <div class="w-8 h-8 rounded-lg bg-agri-forest flex items-center justify-center text-agri-earth">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
               </div>
               <span class="text-lg font-black tracking-tight uppercase">INTERNATIONAL AGRO MARKETPLACE (IAM)
</span>
            </div>
            <p class="text-gray-500 font-medium leading-relaxed">The premier digital portal for international agricultural commodity trade and verified logistics management.</p>
          </div>
          <div>
            <h4 class="font-black text-agri-earth uppercase tracking-widest text-xs mb-8">Marketplace</h4>
            <ul class="space-y-4 text-gray-400 font-semibold text-sm">
              <li><router-link to="/products" class="hover:text-white transition-colors">Global Inventory</router-link></li>
              <li><router-link to="/rfqs" class="hover:text-white transition-colors">Procurement Portal</router-link></li>
              <li><router-link to="/subscriptions" class="hover:text-white transition-colors">Tiered Memberships</router-link></li>
            </ul>
          </div>
          <div>
            <h4 class="font-black text-agri-earth uppercase tracking-widest text-xs mb-8">Logistics</h4>
            <ul class="space-y-4 text-gray-400 font-semibold text-sm">
              <li><a href="#" class="hover:text-white transition-colors">Container Tracking</a></li>
              <li><a href="#" class="hover:text-white transition-colors">Port Schedules</a></li>
              <li><a href="#" class="hover:text-white transition-colors">Insurance Partners</a></li>
            </ul>
          </div>
          <div>
            <h4 class="font-black text-agri-earth uppercase tracking-widest text-xs mb-8">Headquarters</h4>
            <p class="text-gray-400 font-semibold text-sm mb-4 leading-relaxed">IAM Trade Center, Floor 18<br>Business District, London EC2A</p>
            <div class="flex space-x-4">
               <a href="#" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-agri-forest transition-colors"><span class="sr-only">LinkedIn</span></a>
               <a href="#" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-agri-forest transition-colors"><span class="sr-only">Twitter</span></a>
            </div>
          </div>
        </div>
        <div class="border-t border-white/5 pt-8 flex flex-col md:flex-row justify-between items-center text-xs font-bold text-gray-600 uppercase tracking-widest">
           <p>&copy; 2026 International Agro Marketplace. Built for Integrity.</p>
           <div class="flex space-x-8 mt-4 md:mt-0">
              <a href="#" class="hover:text-white transition-colors">Privacy Charter</a>
              <a href="#" class="hover:text-white transition-colors">Trade Ethics</a>
           </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import Navigation from '../components/Navigation.vue'
import heroAgri from 'assets/hero-agri.png'
import agriTrade from 'assets/agri-trade.png'
</script>

<style scoped>
.text-gradient {
  background: linear-gradient(to right, #D4A373, #E9C46A);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}
</style>
