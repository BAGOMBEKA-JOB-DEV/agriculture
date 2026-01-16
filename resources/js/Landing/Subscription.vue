<template>
  <Navigation />

  <div class="font-sans text-gray-900 overflow-x-hidden bg-white min-h-screen selection:bg-agri-forest/20">
    <!-- Flashy Hero Section -->
    <section class="relative pt-32 pb-24 bg-white overflow-hidden text-center lg:text-left">
      <!-- High-Impact Background Accents & Overlays -->
      <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-agri-sand/30 rounded-full blur-[160px] -translate-y-1/2 translate-x-1/2"></div>
      <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-agri-lush/10 rounded-full blur-[120px] translate-y-1/2 -translate-x-1/2"></div>
      
      <!-- Catchy Background Image Overlay (Agricultural Texture) -->
      <div class="absolute inset-0 opacity-[0.05] pointer-events-none bg-[url('https://images.unsplash.com/photo-1595841696677-5b89eb007218?q=80&w=2000&auto=format&fit=crop')] bg-cover bg-center mix-blend-multiply"></div>
      
      <!-- Mesh Gradient to break whiteness -->
      <div class="absolute top-0 left-0 w-full h-[500px] bg-gradient-to-b from-agri-sand/20 to-transparent pointer-events-none"></div>

      <div class="container mx-auto px-6 relative z-10">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
           <div class="relative">
              <!-- Big Catchy Animation: Coffee Burst -->
              <div class="absolute -top-32 -left-20 w-48 h-48 animate-float-slow delay-700 pointer-events-none z-20 hidden xl:block">
                 <img :src="'/images/catchy_coffee_3d_asset_1768568689881.png'" alt="Coffee" class="w-full h-auto drop-shadow-xl mix-blend-multiply brightness-[1.02] contrast-[1.05]" />
              </div>

              <div class="inline-flex items-center space-x-2 px-4 py-2 rounded-xl bg-agri-forest/5 border border-agri-forest/10 text-agri-forest text-[11px] font-black uppercase tracking-[0.25em] mb-8 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-agri-earth animate-pulse"></span>
                <span>Global trade made easy</span>
              </div>
              
              <h1 class="text-6xl md:text-8xl font-black text-gray-900 mb-8 leading-[0.9] tracking-tighter relative">
                <div class="relative inline-block">
                  Membership 
                  <!-- Emojis Cluster -->
                  <div class="absolute -top-10 -left-12 w-12 h-12 animate-bounce-slow opacity-80 pointer-events-none hidden md:flex items-center justify-center text-3xl select-none">🌍</div>
                  <div class="absolute -top-16 left-1/2 w-10 h-10 animate-float-slow delay-300 opacity-60 pointer-events-none hidden md:flex items-center justify-center text-2xl select-none">💎</div>
                </div>
                <br/>
                <div class="relative inline-block">
                  <span class="text-agri-forest italic">Plans</span>
                  <!-- Catchy Overlay: Oranges -->
                  <div class="absolute -bottom-10 -right-40 w-32 h-32 animate-bounce-slow delay-500 pointer-events-none z-20 hidden lg:block">
                     <img :src="'/images/catchy_orange_vanilla_3d_asset_1768568771867.png'" alt="Oranges" class="w-full h-auto drop-shadow-lg mix-blend-multiply brightness-[1.02] contrast-[1.05]" />
                  </div>
                  <!-- More Emojis -->
                  <div class="absolute -top-4 -right-16 w-12 h-12 animate-bounce-slow delay-700 opacity-80 pointer-events-none hidden md:flex items-center justify-center text-4xl select-none">📈</div>
                  <div class="absolute -bottom-8 -right-10 w-10 h-10 animate-float-slow delay-1000 opacity-60 pointer-events-none hidden md:flex items-center justify-center text-2xl select-none">🛡️</div>
                </div>
              </h1>
              <p class="text-xl text-gray-500 max-w-xl mb-12 font-medium leading-relaxed mx-auto lg:mx-0">
                Pick your side and start trading. We offer simple plans that grow with your farm or business.
              </p>

              <!-- Persona Toggle -->
              <div class="inline-flex p-1 bg-gray-100 rounded-[2rem] shadow-inner border border-gray-200">
                 <button 
                   @click="activePersona = 'buyer'"
                   :class="[
                      'py-4 px-10 rounded-[1.75rem] text-sm font-black uppercase tracking-widest transition-all duration-500 flex items-center space-x-3',
                      activePersona === 'buyer' ? 'bg-white text-agri-forest shadow-xl scale-[1.02]' : 'text-gray-400 hover:text-gray-600'
                   ]"
                 >
                   <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                   </svg>
                   <span>Buyer</span>
                 </button>
                 <button 
                   @click="activePersona = 'vendor'"
                   :class="[
                      'py-4 px-10 rounded-[1.75rem] text-sm font-black uppercase tracking-widest transition-all duration-500 flex items-center space-x-3',
                      activePersona === 'vendor' ? 'bg-agri-forest text-white shadow-xl shadow-agri-forest/20 scale-[1.02]' : 'text-gray-400 hover:text-gray-600'
                   ]"
                 >
                   <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                   </svg>
                   <span>Vendor</span>
                 </button>
              </div>
           </div>

           <!-- Hero Illustration Group -->
           <div class="relative group hidden lg:block">
              <!-- Catchy Asset: Cow character -->
              <div class="absolute -top-20 -left-20 w-56 h-56 animate-float-slow pointer-events-none z-30">
                 <img :src="'/images/catchy_cow_dairy_3d_asset_1768568845836.png'" alt="Cow" class="w-full h-auto drop-shadow-2xl hover:scale-110 transition-transform duration-500 mix-blend-multiply brightness-[1.02] contrast-[1.05]" />
              </div>

              <div class="absolute inset-0 bg-agri-sand/20 rounded-[4rem] blur-3xl scale-90 group-hover:scale-100 transition-transform duration-1000"></div>
              <img :src="'/images/agri_trade_premium_hero_1768567240130.png'" 
                   alt="Global Trade" 
                   class="relative z-10 w-full h-auto drop-shadow-[0_40px_60px_rgba(0,0,0,0.1)] hover:rotate-2 transition-transform duration-700"
              />
              <div class="absolute -top-10 -right-10 w-24 h-24 animate-bounce-slow delay-150 pointer-events-none flex items-center justify-center p-4 text-4xl drop-shadow-xl">
                 🚢
              </div>
              <div class="absolute -bottom-12 -left-12 w-20 h-20 animate-bounce-slow pointer-events-none flex items-center justify-center p-4 text-4xl drop-shadow-xl">
                 🌾
              </div>
              <div class="absolute top-1/2 -right-20 w-16 h-16 animate-float-slow delay-500 pointer-events-none opacity-60 text-5xl">⚓</div>
              <div class="absolute -bottom-20 right-20 w-16 h-16 animate-bounce-slow delay-200 pointer-events-none opacity-60 text-5xl">💰</div>
           </div>
        </div>
      </div>
    </section>

    <!-- Persona Summary Section -->
    <section class="py-24 bg-agri-sand/10 relative overflow-hidden">
      <!-- Background Emojis -->
      <div class="absolute top-10 left-[10%] opacity-20 animate-float-slow text-4xl">🍏</div>
      <div class="absolute bottom-10 right-[15%] opacity-20 animate-bounce-slow text-3xl">🌽</div>
      <div class="absolute top-1/2 left-[5%] opacity-10 animate-pulse-slow text-5xl">🚜</div>

      <div class="container mx-auto px-6 max-w-6xl relative z-10">
        <transition enter-active-class="transition duration-1000 ease-out" enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100" mode="out-in">
          <div :key="activePersona" class="flex flex-col md:flex-row items-center gap-16">
             <div class="w-full md:w-1/2">
                <img v-if="activePersona === 'buyer'" :src="'/images/buyer_persona_3d_1768567279824.png'" class="w-full h-auto rounded-[3rem] drop-shadow-2xl hover:scale-105 transition-transform duration-500 mix-blend-multiply brightness-[1.02] contrast-[1.05]" />
                <img v-else :src="'/images/vendor_persona_3d_1768567313936.png'" class="w-full h-auto rounded-[3rem] drop-shadow-2xl hover:scale-105 transition-transform duration-500 mix-blend-multiply brightness-[1.02] contrast-[1.05]" />
             </div>
             <div class="w-full md:w-1/2 text-left">
                <h2 class="text-4xl font-black text-agri-forest mb-6 italic leading-tight">
                   {{ activePersona === 'buyer' ? 'Better Buying' : 'Sell to the World' }} <br/>
                   <span class="text-agri-earth">Made Easy</span>
                </h2>
                <p class="text-lg text-gray-600 font-medium leading-relaxed mb-8">
                   {{ activePersona === 'buyer' 
                      ? 'Built for professional traders to find and buy from verified sellers globally. We make international trade simple and safe.' 
                      : 'Help sellers list products and handle exports easily to get the best prices. Grow your farm business with our digital tools.' 
                   }}
                </p>
                <div class="grid grid-cols-2 gap-4">
                   <div v-for="point in personaPoints" :key="point" class="flex items-center space-x-3 p-4 bg-white/60 backdrop-blur-sm rounded-2xl border border-white">
                      <div class="w-2 h-2 rounded-full bg-agri-earth"></div>
                      <span class="text-[10px] font-black uppercase tracking-widest text-agri-forest">{{ point }}</span>
                   </div>
                </div>
             </div>
          </div>
        </transition>
      </div>
    </section>

    <!-- Pricing Cards -->
    <section class="py-32 bg-white relative overflow-hidden">
      <!-- Background Overlays -->
      <div class="absolute top-[20%] left-[5%] opacity-10 animate-float-slow text-6xl">📦</div>
      <div class="absolute bottom-[10%] left-[12%] opacity-10 animate-bounce-slow text-4xl">🏷️</div>
      <div class="absolute top-[30%] right-[8%] opacity-10 animate-pulse-slow text-5xl">📜</div>
      <div class="absolute bottom-[15%] right-[10%] opacity-10 animate-float-slow text-7xl">🏗️</div>

      <div class="container mx-auto px-6 max-w-7xl relative z-10">
        <transition enter-active-class="transition duration-500 ease-out" enter-from-class="opacity-0 translate-y-8" enter-to-class="opacity-100 translate-y-0" mode="out-in">
          <div :key="activePersona" class="grid lg:grid-cols-3 gap-10 items-stretch">
            <div v-for="plan in activePlans" :key="plan.id"
                 :class="['bg-white rounded-[4rem] p-12 flex flex-col transition-all duration-700 hover:-translate-y-4 border relative group transform-gpu',
                    plan.popular ? 'border-agri-earth ring-1 ring-agri-earth/20 shadow-2xl scale-[1.05] z-10' : 'border-gray-100 shadow-xl']">
              
              <div v-if="plan.popular" class="absolute -top-3 left-1/2 -translate-x-1/2 bg-agri-earth text-white text-[10px] font-black uppercase tracking-[0.2em] px-5 py-2 rounded-full shadow-lg z-20">
                Best Choice
              </div>

              <div class="mb-10 text-center">
                <div class="w-16 h-16 bg-gray-50 rounded-2xl mx-auto mb-6 flex items-center justify-center text-3xl shadow-inner group-hover:scale-110 transition-transform">
                   {{ plan.emoji }}
                </div>
                <span class="text-[10px] font-black uppercase tracking-[0.25em] mb-4 block" :class="plan.popular ? 'text-agri-earth' : 'text-gray-400'">
                  {{ plan.tier }}
                </span>
                <div class="flex items-baseline justify-center mb-6">
                  <span class="text-6xl font-black text-gray-900 tracking-tighter">{{ plan.price }}</span>
                  <span class="text-gray-400 font-bold ml-2">{{ plan.duration }}</span>
                </div>
                <p class="text-gray-500 font-medium text-sm h-12 uppercase tracking-tighter">{{ plan.description }}</p>
              </div>
              
              <div class="space-y-4 mb-8 flex-1 border-t border-gray-50 pt-8 mt-4 text-left">
                <div v-for="feature in plan.features" :key="feature" class="flex items-start space-x-3">
                  <div :class="['w-5 h-5 rounded-lg flex items-center justify-center text-xs shrink-0 mt-0.5', 
                    plan.popular ? 'bg-agri-earth/10 text-agri-earth' : 'bg-agri-forest/5 text-agri-forest']">
                    ✓
                  </div>
                  <span class="text-[12px] font-black text-gray-500 group-hover:text-gray-900 uppercase tracking-tight">
                    {{ feature }}
                  </span>
                </div>
              </div>

              <router-link to="/register" :class="['w-full py-5 rounded-2xl text-center font-black text-[10px] uppercase tracking-[0.2em] transition-all shadow-xl',
                plan.popular ? 'bg-agri-earth text-white hover:bg-agri-forest' : 'bg-gray-50 text-agri-forest hover:bg-agri-forest hover:text-white border border-gray-100']">
                Get Started
              </router-link>
            </div>
          </div>
        </transition>
      </div>
    </section>

    <!-- Comparison Matrix -->
    <section class="py-24 bg-agri-forest text-white relative overflow-hidden">
      <!-- Background Overlays -->
      <div class="absolute top-[10%] left-[2%] opacity-20 animate-float-slow text-5xl">🌾</div>
      <div class="absolute bottom-[15%] right-[5%] opacity-20 animate-bounce-slow text-4xl">🚢</div>
      <div class="absolute top-1/2 right-[2%] opacity-10 animate-pulse-slow text-3xl">⚓</div>
      
      <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/natural-paper.png')]"></div>
      <div class="absolute -left-20 -bottom-20 w-[600px] h-[600px] bg-agri-earth/20 rounded-full blur-[120px]"></div>

      <div class="container mx-auto px-6 max-w-6xl relative z-10">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-black mb-4 uppercase">Compare All Plans</h2>
          <p class="text-agri-sand/60 font-medium max-w-2xl mx-auto uppercase tracking-widest text-xs">Find the right fit for your business</p>
        </div>

        <div class="hidden lg:block bg-white text-gray-900 rounded-[3rem] shadow-2xl overflow-hidden">
          <table class="w-full border-collapse">
            <thead>
              <tr class="bg-gray-50">
                <th class="py-10 px-8 text-left border-b">
                  <div class="flex flex-col">
                    <span class="text-[10px] font-black uppercase tracking-[0.3em] text-agri-earth mb-2">Details</span>
                    <span class="text-xl font-black text-agri-forest uppercase">Features</span>
                  </div>
                </th>
                <th v-for="plan in activePlans" :key="plan.id" class="py-10 px-8 text-center border-b">
                  <div class="flex flex-col items-center">
                    <span class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 mb-2">{{ plan.tier }}</span>
                    <span class="text-lg font-black text-gray-900 uppercase tracking-tighter">{{ plan.price }}</span>
                  </div>
                </th>
              </tr>
            </thead>
            <tbody>
              <template v-for="group in activeComparison" :key="group.title">
                <tr class="bg-agri-forest/[0.05]">
                  <td colspan="4" class="py-4 px-8 text-[11px] font-black uppercase text-agri-forest">
                    {{ group.title }}
                  </td>
                </tr>
                <tr v-for="item in group.items" :key="item.label" class="group hover:bg-gray-50 transition-colors">
                  <td class="py-6 px-8 border-b">
                    <div class="flex flex-col">
                      <span class="text-sm font-black text-gray-700 uppercase">{{ item.label }}</span>
                      <span v-if="item.description" class="text-[9px] font-bold text-gray-400 mt-1 uppercase">{{ item.description }}</span>
                    </div>
                  </td>
                  <td v-for="pKey in ['explorer', 'pro', 'enterprise']" :key="pKey" class="py-6 px-8 text-center border-b">
                    <div v-if="item[pKey] === true" class="flex justify-center">
                      <div class="w-8 h-8 rounded-full bg-agri-forest/10 flex items-center justify-center text-agri-forest">✓</div>
                    </div>
                    <span v-else-if="typeof item[pKey] === 'string'" class="text-[10px] font-black text-gray-600 uppercase">{{ item[pKey] }}</span>
                    <div v-else class="flex justify-center"><div class="w-1.5 h-1.5 rounded-full bg-gray-200"></div></div>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>

        <!-- Mobile View -->
        <div class="lg:hidden space-y-6">
           <div class="flex bg-white/10 rounded-2xl p-1 mb-8">
              <button v-for="(p, idx) in ['Basic', 'Pro', 'Elite']" :key="p" @click="mobilePlanIdx = idx" :class="['flex-1 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all', mobilePlanIdx === idx ? 'bg-white text-agri-forest shadow-lg' : 'text-white/60']">
                {{ p }}
              </button>
           </div>
           <div v-for="group in activeComparison" :key="group.title" class="bg-white text-gray-900 rounded-[2rem] p-8">
              <h3 class="text-xs font-black uppercase tracking-widest text-agri-forest mb-6 border-b pb-4 border-gray-100 italic">{{ group.title }}</h3>
              <div class="space-y-6">
                 <div v-for="item in group.items" :key="item.label" class="flex items-center justify-between gap-4">
                    <div class="flex flex-col max-w-[70%]">
                       <span class="text-[11px] font-black uppercase text-gray-700">{{ item.label }}</span>
                       <span v-if="item.description" class="text-[8px] font-bold text-gray-400 uppercase tracking-tighter">{{ item.description }}</span>
                    </div>
                    <div class="flex-shrink-0">
                       <div v-if="getMobileValue(item) === true" class="w-6 h-6 rounded-full bg-agri-forest/10 flex items-center justify-center text-agri-forest font-bold text-xs">✓</div>
                       <span v-else-if="typeof getMobileValue(item) === 'string'" class="text-[10px] font-black text-agri-forest uppercase">{{ getMobileValue(item) }}</span>
                       <div v-else class="w-1.5 h-1.5 rounded-full bg-gray-200"></div>
                    </div>
                 </div>
              </div>
           </div>
        </div>
      </div>
    </section>

    <!-- Trust Section -->
    <section class="py-24 bg-gray-50/50 relative overflow-hidden">
      <!-- Background Overlays -->
      <div class="absolute top-1/2 left-[5%] opacity-10 animate-bounce-slow text-4xl">🛡️</div>
      <div class="absolute top-10 right-[20%] opacity-10 animate-float-slow text-3xl">🤝</div>
      <div class="absolute bottom-5 left-[30%] opacity-10 animate-pulse-slow text-2xl">🌍</div>

      <div class="container mx-auto px-6">
        <div class="max-w-4xl mx-auto flex flex-col md:flex-row items-center justify-between space-y-10 md:space-y-0 text-center md:text-left">
           <div>
              <h2 class="text-3xl font-black text-agri-forest mb-4 uppercase leading-none">Safe and Secure</h2>
              <p class="text-gray-500 font-medium max-w-sm uppercase text-[10px] tracking-widest leading-relaxed">Your trades are safe with us. We use world-class security for every deal.</p>
           </div>
           <div class="flex flex-wrap justify-center md:justify-end gap-10 opacity-30 select-none grayscale">
              <span class="text-2xl font-black text-gray-900 border-2 border-gray-900 px-4 py-1 rounded-xl">ISO-9001</span>
              <span class="text-2xl font-black text-gray-900 border-2 border-gray-900 px-4 py-1 rounded-xl">GMP+</span>
              <span class="text-2xl font-black text-gray-900 border-2 border-gray-900 px-4 py-1 rounded-xl">TRUSTED-TRADE</span>
           </div>
        </div>
      </div>
    </section>

    <Footer />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import Navigation from '../components/Navigation.vue'
import Footer from '../components/Footer.vue'

const activePersona = ref('buyer')
const mobilePlanIdx = ref(1)

const buyerPlans = [
  { id: 'b1', tier: 'Basic Explorer', price: '$0', duration: '/14 days', description: 'Find Products & Sellers', emoji: '🧭', popular: false, features: ['Marketplace Access', 'Seller Directory', '2 Quote Credits', 'Basic Freight Info', 'Trade Community Access'] },
  { id: 'b2', tier: 'Professional Buyer', price: '$89', duration: '/month', description: 'Pro Buying Tools', emoji: '🏢', popular: true, features: ['Verified Buyer Status', 'Unlimited Quote Requests', 'Priority Visibility', 'Real-time Tracking', 'Safe Payment Service', 'Market Watch Alerts'] },
  { id: 'b3', tier: 'Venture Enterprise', price: '$999', duration: '/year', description: 'Large Business Portal', emoji: '🌐', popular: false, features: ['Private Trade Portal', 'Dedicated Account Manager', 'Direct System Access', 'Easy Compliance', 'Bulk Deal Credits', 'VIP Event Access'] }
]

const vendorPlans = [
  { id: 'v1', tier: 'Field Starter', price: '$0', duration: '/14 days', description: 'Show Your Harvest', emoji: '🚜', popular: false, features: ['1 Product Listing', 'Basic Storefront', 'Direct Chat', 'Photo Uploads', 'Email Support'] },
  { id: 'v2', tier: 'Market Producer', price: '$149', duration: '/month', description: 'Sell More Harvest', emoji: '🏛️', popular: true, features: ['Verified Seller Badge', 'Unlimited Listings', 'High-quality Gallery', 'Top Search Result', 'Performance Reports', 'Store Management Hub'] },
  { id: 'v3', tier: 'Industrial Seller', price: '$1,450', duration: '/year', description: 'Big Export Hub', emoji: '🏭', popular: false, features: ['Bulk Export Tools', 'Multi-Warehouse Control', 'Quality Audit Support', 'Priority Support', 'Automatic Inventory', 'Export Expert Access'] }
]

const activePlans = computed(() => activePersona.value === 'buyer' ? buyerPlans : vendorPlans)

const personaPoints = computed(() => activePersona.value === 'buyer' 
    ? ['Global Access', 'Verified Sellers', 'Market Intel', 'Safe Payments']
    : ['Digital ID', 'Export Tools', 'Direct Sales', 'Trade Network'])

const buyerComparison = [
  { title: 'Finding Items', items: [
    { label: 'Browse Products', description: 'See all global farm products', explorer: true, pro: true, enterprise: true },
    { label: 'Seller Directory', description: 'Check seller history and ratings', explorer: true, pro: true, enterprise: true },
    { label: 'Safe Searches', description: 'Save your custom search filters', explorer: false, pro: true, enterprise: true },
    { label: 'Market Prices', description: '3-year pricing history', explorer: false, pro: false, enterprise: true }
  ]},
  { title: 'Trade Tools', items: [
    { label: 'Quote Requests', description: 'How many RFQs you can send', explorer: '2/Month', pro: 'Unlimited', enterprise: 'Unlimited' },
    { label: 'Early Notifications', description: 'Get quotes before others do', explorer: false, pro: true, enterprise: true },
    { label: 'Verified Status', description: 'Higher trust with sellers', explorer: false, pro: true, enterprise: true },
    { label: 'Shipment Tracking', description: 'Follow your items live', explorer: false, pro: 'Standard', enterprise: 'PRO Tracking' }
  ]},
  { title: 'Safety & Money', items: [
    { label: 'Payment Protection', description: 'Safe way to pay sellers', explorer: false, pro: true, enterprise: true },
    { label: 'Legal Checks', description: 'Automatic document checks', explorer: false, pro: false, enterprise: true },
    { label: 'Custom Port Link', description: 'Direct link to shipping ports', explorer: false, pro: false, enterprise: true }
  ]}
]

const vendorComparison = [
  { title: 'Selling Tools', items: [
    { label: 'Product Listings', description: 'How many items you can list', explorer: '1 Item', pro: 'Unlimited', enterprise: 'Unlimited' },
    { label: 'Product Gallery', description: 'High-quality photos for items', explorer: false, pro: true, enterprise: true },
    { label: 'Search Ranking', description: 'Appear higher in search results', explorer: false, pro: 'Better', enterprise: 'Best' },
    { label: 'Inventory Link', description: 'Connect your system to ours', explorer: false, pro: false, enterprise: true }
  ]},
  { title: 'Seller Identity', items: [
    { label: 'Verified Producer', description: 'Global trust certificate', explorer: false, pro: true, enterprise: true },
    { label: 'Own Storefront', description: 'Your own branded farm page', explorer: 'Basic', pro: 'Great', enterprise: 'Full Brand' },
    { label: 'Sales Reports', description: 'See who visits your store', explorer: false, pro: true, enterprise: true },
    { label: 'Expert Help', description: 'Help with global shipping', explorer: false, pro: false, enterprise: true }
  ]},
  { title: 'Rules & Shipping', items: [
    { label: 'Export Helper', description: 'Direct help with export laws', explorer: false, pro: false, enterprise: true },
    { label: 'Warehouse Manager', description: 'Manage multiple depots', explorer: false, pro: false, enterprise: true },
    { label: 'Fast Dispute help', description: 'Fix issues with buyers fast', explorer: false, pro: true, enterprise: true }
  ]}
]

const activeComparison = computed(() => activePersona.value === 'buyer' ? buyerComparison : vendorComparison)
const getMobileValue = (item) => { const keys = ['explorer', 'pro', 'enterprise']; return item[keys[mobilePlanIdx.value]] }
</script>

<style scoped>
@keyframes bounce-slow { 0%, 100% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-20px) rotate(5deg); } }
@keyframes float-slow { 0%, 100% { transform: translate(0, 0) rotate(0deg); } 33% { transform: translate(15px, -15px) rotate(-5deg); } 66% { transform: translate(-10px, 10px) rotate(5deg); } }
@keyframes pulse-slow { 0%, 100% { opacity: 0.1; transform: scale(1); } 50% { opacity: 0.3; transform: scale(1.1); } }
.animate-bounce-slow { animation: bounce-slow 4s infinite ease-in-out; }
.animate-float-slow { animation: float-slow 6s infinite ease-in-out; }
.animate-pulse-slow { animation: pulse-slow 5s infinite ease-in-out; }
.transition-all { transition-duration: 500ms; }
button, a { -webkit-tap-highlight-color: transparent; }
</style>
