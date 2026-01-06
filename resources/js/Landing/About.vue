<template>
  <!-- Navigation -->
  <Navigation />

  <div class="font-sans text-gray-900 overflow-x-hidden">
    <!-- Hero Section -->
    <section class="bg-agri-forest py-32 relative overflow-hidden">
      <div class="absolute inset-0 opacity-10">
         <img :src="heroAgri" alt="Agriculture" class="w-full h-full object-cover">
      </div>
      <div class="container mx-auto px-6 relative z-10 text-center">
        <h1 class="text-5xl md:text-7xl font-black text-white mb-8 tracking-tighter">
          The <span class="text-agri-earth text-gradient">Agrixchange</span> Mission
        </h1>
        <p class="text-xl text-agri-earth/80 max-w-3xl mx-auto font-medium leading-relaxed">
          We are building the definitive digital infrastructure for global agricultural trade, 
          bridging the gap between local producers and international demand.
        </p>
      </div>
    </section>

    <!-- Mission Section -->
    <section class="py-24 bg-white">
      <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-20 items-center">
          <div>
            <div class="w-16 h-2 bg-agri-earth mb-8 rounded-full"></div>
            <h2 class="text-4xl font-black text-agri-forest mb-8 tracking-tight">
              Empowering the World's Breadbasket
            </h2>
            <p class="text-lg text-gray-600 mb-6 leading-relaxed">
              Agrixchange was founded on the belief that fair trade starts with transparency. Our platform democratizes access to international markets, allowing farmers of all scales to trade with the same tools as global conglomerates.
            </p>
            <p class="text-lg text-gray-600 leading-relaxed font-medium">
              By combining blockchain-verified origin tracking with enterprise-grade logistics, we ensure that every bushel, bag, and crate meets the highest standards of integrity.
            </p>
          </div>
          <div class="relative rounded-3xl overflow-hidden shadow-2xl border-8 border-gray-50">
             <img :src="agriTrade" alt="Agriculture Logistics" class="w-full h-full object-cover">
             <div class="absolute inset-0 bg-agri-forest/20"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- Values Section -->
    <section class="py-24 bg-gray-50/50">
      <div class="container mx-auto px-6">
        <div class="text-center mb-20">
          <h2 class="text-4xl font-black text-agri-forest mb-4">Our Core Integrity</h2>
          <p class="text-gray-500 font-bold uppercase tracking-widest text-xs">Built on trust, delivered with quality</p>
        </div>

        <div class="grid md:grid-cols-3 gap-12">
          <div class="bg-white p-10 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 border border-transparent hover:border-agri-earth/20">
            <div class="w-16 h-16 bg-agri-sand/30 rounded-2xl flex items-center justify-center text-agri-forest mb-8">
               <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
               </svg>
            </div>
            <h3 class="text-2xl font-black text-gray-900 mb-4 tracking-tight">Price Integrity</h3>
            <p class="text-gray-600 font-medium">Transparent, market-driven pricing models that ensure fair value for producers and competitive rates for buyers.</p>
          </div>

          <div class="bg-white p-10 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 border border-transparent hover:border-agri-earth/20 text-white bg-agri-forest">
            <div class="w-16 h-16 bg-agri-earth rounded-2xl flex items-center justify-center text-agri-forest mb-8 shadow-lg">
               <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
               </svg>
            </div>
            <h3 class="text-2xl font-black mb-4 tracking-tight">Origin Trust</h3>
            <p class="text-agri-earth/80 font-medium leading-relaxed">A rigorous verification protocol for every member of our network, from family farms to international trade houses.</p>
          </div>

          <div class="bg-white p-10 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 border border-transparent hover:border-agri-earth/20">
            <div class="w-16 h-16 bg-agri-sand/30 rounded-2xl flex items-center justify-center text-agri-forest mb-8">
               <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
               </svg>
            </div>
            <h3 class="text-2xl font-black text-gray-900 mb-4 tracking-tight">Logistics Agility</h3>
            <p class="text-gray-600 font-medium">Streamlined supply chain solutions that reduce friction and maximize the freshness and quality of traded commodities.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Final CTA -->
    <section class="py-24 bg-agri-earth text-white text-center">
       <div class="container mx-auto px-6">
          <h2 class="text-5xl font-black mb-8 tracking-tighter">Enter the Global Arena</h2>
          <router-link to="/register" class="bg-agri-forest text-white px-12 py-5 rounded-2xl text-xl font-black hover:bg-agri-lush transition-all inline-block shadow-2xl">Create Trade Identity</router-link>
       </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-950 text-white py-12 border-t border-white/5">
      <div class="container mx-auto px-6 text-center">
        <p class="text-xs font-bold text-gray-600 uppercase tracking-widest">&copy; 2026 International Agro Marketplace. Excellence in Agriculture.</p>
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
  background: linear-gradient(to right, #ffffff, #D4A373);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}
</style>
