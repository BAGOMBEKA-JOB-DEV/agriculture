<template>
  <div class="min-h-screen bg-gray-50/50 pb-12">
    <div class="bg-agri-forest py-12 relative overflow-hidden">
        <div class="absolute inset-0 bg-agri-earth/5 opacity-20"></div>
        <div class="container mx-auto px-6 relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-6 lg:space-y-0">
                <div>
                    <h1 class="text-4xl font-black text-white mb-2 tracking-tighter">
                       Market <span class="text-agri-earth">Moderation</span>
                    </h1>
                    <p class="text-agri-earth/70 font-medium text-sm">Verify asset compliance and maintain global trade integrity.</p>
                </div>
                
                <div class="flex items-center space-x-4">
                  <!-- Discovery Search -->
                  <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-agri-earth/40 group-focus-within:text-agri-earth transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                      </svg>
                    </div>
                    <input 
                      v-model="filters.q"
                      @input="debouncedFetch"
                      type="text" 
                      placeholder="Search Assets or IDs..."
                      class="bg-white/10 backdrop-blur-md border border-white/20 rounded-[2rem] pl-14 pr-8 py-4 text-white placeholder-agri-earth/50 font-bold text-sm w-full lg:w-96 focus:outline-none focus:ring-2 focus:ring-agri-earth/50 transition-all shadow-xl"
                    >
                  </div>

                  <!-- Advanced Toggle -->
                  <button 
                    @click="showAdvancedFilters = !showAdvancedFilters"
                    class="p-4 rounded-[1.5rem] border transition-all duration-300 flex items-center space-x-2"
                    :class="showAdvancedFilters ? 'bg-agri-earth text-agri-forest border-agri-earth' : 'bg-white/5 text-white border-white/10 hover:bg-white/10'"
                  >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    <span class="text-[10px] font-black uppercase tracking-widest hidden sm:inline">Filters</span>
                  </button>
                </div>
            </div>

            <!-- Advanced Filter Panel -->
            <Transition
              enter-active-class="transition-all duration-500 ease-out"
              leave-active-class="transition-all duration-300 ease-in"
              enter-from-class="max-h-0 opacity-0 mt-0"
              enter-to-class="max-h-[500px] opacity-100 mt-8"
              leave-from-class="max-h-[500px] opacity-100 mt-8"
              leave-to-class="max-h-0 opacity-0 mt-0"
            >
              <div v-if="showAdvancedFilters" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 overflow-hidden">
                <!-- Status Filter -->
                <div class="space-y-2">
                  <label class="text-[9px] font-black text-agri-earth/60 uppercase tracking-widest ml-4">Listing Status</label>
                  <select v-model="filters.status" @change="fetchProducts" class="w-full bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl px-6 py-4 text-white font-bold text-xs outline-none cursor-pointer focus:ring-2 focus:ring-agri-earth/30 transition-all">
                    <option value="" class="text-gray-900">All Asset Statuses</option>
                    <option value="pending" class="text-gray-900">Pending Verification</option>
                    <option value="available" class="text-gray-900">Active Trade</option>
                    <option value="rejected" class="text-gray-900">Rejected / Compliance</option>
                  </select>
                </div>

                <!-- Category Filter -->
                <div class="space-y-2">
                  <label class="text-[9px] font-black text-agri-earth/60 uppercase tracking-widest ml-4">Trade Classification</label>
                  <select v-model="filters.category_id" @change="fetchProducts" class="w-full bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl px-6 py-4 text-white font-bold text-xs outline-none cursor-pointer focus:ring-2 focus:ring-agri-earth/30 transition-all">
                    <option value="" class="text-gray-900">All Classifications</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id" class="text-gray-900">{{ cat.name }}</option>
                  </select>
                </div>

                <!-- Provenance Filter -->
                <div class="space-y-2">
                  <label class="text-[9px] font-black text-agri-earth/60 uppercase tracking-widest ml-4">Sovereign Origin</label>
                  <input 
                    v-model="filters.location"
                    @input="debouncedFetch"
                    type="text" 
                    placeholder="Filter by Location..."
                    class="w-full bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl px-6 py-4 text-white font-bold text-xs outline-none focus:ring-2 focus:ring-agri-earth/30 transition-all placeholder-agri-earth/40"
                  >
                </div>

                <!-- Seller Filter -->
                <div class="space-y-2">
                  <label class="text-[9px] font-black text-agri-earth/60 uppercase tracking-widest ml-4">Asset Producer</label>
                  <input 
                    v-model="filters.seller"
                    @input="debouncedFetch"
                    type="text" 
                    placeholder="Search Producers..."
                    class="w-full bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl px-6 py-4 text-white font-bold text-xs outline-none focus:ring-2 focus:ring-agri-earth/30 transition-all placeholder-agri-earth/40"
                  >
                </div>

                <!-- Price Range Filter -->
                <div class="lg:col-span-2 space-y-2 bg-white/5 p-6 rounded-[2rem] border border-white/10">
                  <label class="text-[9px] font-black text-agri-earth/60 uppercase tracking-widest block mb-4">Trade Valuation Range ($)</label>
                  <div class="flex items-center space-x-4">
                    <input 
                      v-model="filters.min_price" 
                      @input="debouncedFetch"
                      type="number" 
                      placeholder="Min" 
                      class="flex-1 bg-white/10 border border-white/10 rounded-xl px-4 py-3 text-white text-xs font-bold focus:outline-none focus:ring-1 focus:ring-agri-earth/40"
                    >
                    <span class="text-white/20 font-black">TO</span>
                    <input 
                      v-model="filters.max_price" 
                      @input="debouncedFetch"
                      type="number" 
                      placeholder="Max" 
                      class="flex-1 bg-white/10 border border-white/10 rounded-xl px-4 py-3 text-white text-xs font-bold focus:outline-none focus:ring-1 focus:ring-agri-earth/40"
                    >
                    <button 
                      @click="resetFilters"
                      class="px-4 py-3 text-[9px] font-black text-agri-earth hover:text-white uppercase tracking-widest transition-colors"
                    >
                      Clear All
                    </button>
                  </div>
                </div>
              </div>
            </Transition>
        </div>
    </div>

    <div class="container mx-auto px-6 -mt-8 relative z-20">
      <div v-if="loading" class="flex flex-col items-center justify-center py-24 bg-white rounded-[3rem] shadow-xl border border-gray-100">
        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-agri-forest"></div>
        <p class="mt-4 text-agri-forest font-black uppercase text-xs tracking-widest">Scanning Market Repositories...</p>
      </div>

      <div v-else class="grid grid-cols-1 gap-8">
        <div v-for="product in products" :key="product.id" class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 p-10 flex flex-col lg:flex-row lg:items-center justify-between group hover:border-agri-earth/30 transition-all duration-500">
          <div class="flex-1 lg:pr-12">
            <div class="flex items-center space-x-3 mb-4">
              <span class="px-3 py-1 rounded-full bg-agri-sand text-agri-forest text-[10px] font-black uppercase tracking-widest">
                {{ product.category?.name || 'Trade Asset' }}
              </span>
              <span :class="getStatusClasses(product.status)" class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest inline-block shadow-sm">
                {{ product.status }}
              </span>
            </div>
            
            <h3 class="text-3xl font-black text-gray-900 mb-2 tracking-tight group-hover:text-agri-forest transition-colors">{{ product.name }}</h3>
            <div class="flex items-center space-x-4 mb-6">
                <div class="w-12 h-12 rounded-xl bg-gray-50 flex-shrink-0 overflow-hidden border border-gray-100">
                    <img v-if="product.images && product.images.length > 0" :src="product.images[0]" class="w-full h-full object-cover">
                    <div v-else class="w-full h-full flex items-center justify-center text-xs font-black text-gray-400">
                        {{ product.seller?.name?.charAt(0) }}
                    </div>
                </div>
                <span class="text-xs font-bold uppercase tracking-wide">Produced by: {{ product.seller?.name || 'Authorized Producer' }}</span>
            </div>
            
            <p class="text-gray-500 font-medium leading-relaxed mb-8 max-w-2xl">{{ product.description }}</p>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
              <div>
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Trade Price</span>
                <p class="text-sm font-black text-agri-forest">${{ product.price }}<span class="text-gray-400 font-bold">/{{ product.unit }}</span></p>
              </div>
              <div>
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Inventory</span>
                <p class="text-sm font-black text-gray-900">Total Asset Pool</p>
              </div>
              <div>
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Provenance</span>
                <p class="text-sm font-black text-gray-900 italic">{{ product.location }}</p>
              </div>
              <div>
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Batch Floor</span>
                <p class="text-sm font-black text-gray-900">{{ product.min_order }} {{ product.unit }}</p>
              </div>
            </div>
          </div>

          <div class="mt-10 lg:mt-0 lg:w-64 flex flex-col space-y-4">
            <button 
              v-if="product.status !== 'available'"
              @click="openApproveModal(product)" 
              class="w-full bg-agri-forest text-white py-4 rounded-2xl font-black uppercase text-xs tracking-widest hover:bg-agri-lush transition-all shadow-xl shadow-agri-forest/20"
            >
              Authorize Listing
            </button>
            <button 
              v-if="product.status !== 'rejected'"
              @click="openRejectModal(product)" 
              class="w-full bg-red-50 text-red-600 py-4 rounded-2xl font-black uppercase text-xs tracking-widest hover:bg-red-100 transition-all border border-red-100"
            >
              Refuse/Revoke
            </button>
            <button 
              @click="openInspector(product)" 
              class="w-full bg-agri-sand text-agri-forest py-4 rounded-2xl font-black uppercase text-xs tracking-widest hover:bg-agri-sand/50 transition-all border border-agri-forest/20 flex items-center justify-center"
            >
              Inspect Asset
              <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
          </div>
        </div>

        <div v-if="products.length === 0" class="text-center py-24 bg-white rounded-[3rem] shadow-xl border border-gray-100">
          <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-300">
             <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
             </svg>
          </div>
          <p class="text-gray-400 font-black uppercase tracking-widest">Ledger Clear: No assets awaiting moderation</p>
        </div>
      </div>
    </div>

    <!-- Regulatory Inspection Drawer -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition-opacity duration-300 ease-out"
        leave-active-class="transition-opacity duration-300 ease-in"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="isDrawerOpen" class="fixed inset-0 z-[100] overflow-hidden">
          <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="closeInspector"></div>
          
          <Transition
            enter-active-class="transform transition-transform duration-500 ease-out"
            leave-active-class="transform transition-transform duration-500 ease-in"
            enter-from-class="translate-x-full"
            enter-to-class="translate-x-0"
            leave-from-class="translate-x-0"
            leave-to-class="translate-x-full"
            appear
          >
            <div v-if="isDrawerOpen" class="absolute inset-y-0 right-0 max-w-2xl w-full bg-white shadow-2xl flex flex-col">
              <!-- Drawer Header -->
              <div class="px-8 py-10 border-b border-gray-100 bg-white sticky top-0 z-20">
            <div class="flex items-center justify-between mb-6">
               <div class="flex items-center space-x-3">
                  <div class="w-12 h-12 rounded-2xl bg-agri-forest flex items-center justify-center text-white font-black text-xl">
                    {{ inspectedProduct?.name?.charAt(0) }}
                  </div>
                  <div>
                    <h2 class="text-2xl font-black text-gray-900 tracking-tight">{{ inspectedProduct?.name }}</h2>
                    <p class="text-[10px] font-black text-agri-forest uppercase tracking-widest">Asset Registry ID: #{{ inspectedProduct?.id }}</p>
                  </div>
               </div>
               <button @click="closeInspector" class="p-3 bg-gray-50 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all">
                 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
               </button>
            </div>

            <!-- Tab Navigation -->
            <div class="flex space-x-1 p-1 bg-gray-50 rounded-xl">
               <button 
                 v-for="tab in ['manifest', 'evidence', 'registry']" 
                 :key="tab"
                 @click="activeTab = tab"
                 class="flex-1 py-3 text-[10px] font-black uppercase tracking-widest rounded-lg transition-all"
                 :class="activeTab === tab ? 'bg-white text-agri-forest shadow-sm' : 'text-gray-400 hover:text-gray-600'"
               >
                 {{ tab }}
               </button>
            </div>
          </div>

          <!-- Drawer Content -->
          <div class="flex-1 overflow-y-auto p-8 bg-white custom-scrollbar">
            <!-- Manifest Tab -->
            <div v-if="activeTab === 'manifest'" class="space-y-10 animate-in fade-in slide-in-from-right-4 duration-300">
               <div>
                 <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Qualitative Brief</h4>
                 <p class="text-sm font-medium text-gray-600 leading-relaxed italic border-l-4 border-agri-forest/20 pl-6">
                    "{{ inspectedProduct?.description }}"
                 </p>
               </div>
               <div class="grid grid-cols-2 gap-8">
                  <div class="p-6 bg-agri-sand/20 rounded-[2rem] border border-agri-sand/30">
                     <span class="text-[9px] font-black text-agri-forest uppercase tracking-widest mb-1 block">Trade Price</span>
                     <p class="text-xl font-black text-gray-900">${{ inspectedProduct?.price }}<span class="text-gray-400 text-xs">/{{ inspectedProduct?.unit }}</span></p>
                  </div>
                  <div class="p-6 bg-gray-50 rounded-[2rem] border border-gray-100">
                     <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Sovereign Origin</span>
                     <p class="text-xl font-black text-gray-900">{{ inspectedProduct?.location }}</p>
                  </div>
               </div>
            </div>

            <!-- Evidence Tab -->
            <div v-if="activeTab === 'evidence'" class="space-y-6 animate-in fade-in slide-in-from-right-4 duration-300">
               <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Multi-Media Registry</h4>
               <div class="grid grid-cols-1 gap-6">
                  <div 
                    v-for="(img, idx) in inspectedProduct?.images" 
                    :key="idx" 
                    class="aspect-video rounded-[2.5rem] overflow-hidden border border-gray-100 bg-gray-50 cursor-zoom-in group/evidence relative"
                    @click="openLightbox(idx)"
                  >
                    <img :src="img" class="w-full h-full object-cover transition-transform duration-700 group-hover/evidence:scale-110">
                    <div class="absolute inset-0 bg-black/0 group-hover/evidence:bg-black/20 transition-all flex items-center justify-center">
                       <svg class="w-12 h-12 text-white opacity-0 group-hover/evidence:opacity-100 transition-all transform scale-90 group-hover/evidence:scale-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                       </svg>
                    </div>
                  </div>
                  <div v-if="!inspectedProduct?.images || inspectedProduct?.images.length === 0" class="py-24 text-center bg-gray-50 rounded-[2.5rem] border-2 border-dashed border-gray-200">
                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest">No Visual Evidence Provided</p>
                  </div>
               </div>
            </div>

            <!-- Registry Tab -->
            <div v-if="activeTab === 'registry'" class="space-y-10 animate-in fade-in slide-in-from-right-4 duration-300">
               <div>
                  <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6">Technical Compliance Matrix</h4>
                  <div class="space-y-4">
                     <div v-for="(val, label) in { 'Provenance': inspectedProduct?.location, 'Registry Unit': inspectedProduct?.unit, 'Batch Floor': inspectedProduct?.min_order + ' ' + inspectedProduct?.unit, 'Classification': inspectedProduct?.category?.name }" :key="label" class="flex justify-between items-center py-4 border-b border-gray-50">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ label }}</span>
                        <span class="text-xs font-black text-gray-900">{{ val }}</span>
                     </div>
                  </div>
               </div>
               <div>
                  <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6">Trade Capabilities</h4>
                  <div class="bg-gray-50 rounded-[2rem] p-8 border border-gray-100">
                    <ul class="space-y-4">
                      <li v-for="capability in ['Global Logistics Protocol', 'Standardized Quality Yield', 'Secured Trade Escrow']" :key="capability" class="flex items-center text-[10px] font-black text-gray-600 uppercase tracking-widest">
                        <svg class="w-4 h-4 text-agri-forest mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ capability }}
                      </li>
                    </ul>
                  </div>
               </div>
            </div>
          </div>

          <!-- Drawer Footer -->
          <div class="p-8 border-t border-gray-100 bg-white flex flex-col space-y-4">
            <div class="flex space-x-4">
               <button 
                 @click="inspectedProduct.status !== 'available' ? openApproveModal(inspectedProduct) : null" 
                 :disabled="inspectedProduct?.status === 'available'"
                 class="flex-1 bg-agri-forest text-white py-5 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-agri-lush transition-all shadow-xl shadow-agri-forest/20 disabled:opacity-20"
               >
                 Authorize Asset
               </button>
               <button 
                 @click="inspectedProduct.status !== 'rejected' ? openRejectModal(inspectedProduct) : null" 
                 :disabled="inspectedProduct?.status === 'rejected'"
                 class="flex-1 bg-red-50 text-red-600 py-5 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-red-100 transition-all border border-red-100 disabled:opacity-20"
               >
                 Refuse / Revoke
               </button>
            </div>
            <router-link 
               :to="`/products/${inspectedProduct?.id}`" 
               target="_blank"
               class="w-full py-4 text-center text-[10px] font-black text-gray-400 hover:text-agri-forest transition-colors uppercase tracking-widest"
            >
               Preview Global Registry Sheet
            </router-link>
          </div>
        </div>
      </Transition>
    </div>
  </Transition>
</Teleport>

    <!-- Cinematic Evidence Lightbox -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition-opacity duration-300 ease-out"
        leave-active-class="transition-opacity duration-300 ease-in"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="showLightbox" class="fixed inset-0 z-[200] bg-black/95 backdrop-blur-xl flex flex-col items-center justify-center p-8 overflow-hidden">
          <!-- Close Button -->
          <button 
            @click="showLightbox = false"
            class="absolute top-12 right-12 w-16 h-16 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white transition-all group z-[210]"
          >
            <svg class="w-8 h-8 group-hover:rotate-90 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>

          <!-- Main Carousel -->
          <div class="w-full max-w-6xl relative group/carousel">
            <Transition
              enter-active-class="transition-all duration-500 ease-out"
              leave-active-class="transition-all duration-300 ease-in"
              enter-from-class="opacity-0 scale-95"
              enter-to-class="opacity-100 scale-100"
              leave-from-class="opacity-100 scale-100"
              leave-to-class="opacity-0 scale-95"
              mode="out-in"
            >
              <div :key="lightboxIndex" class="aspect-video relative overflow-hidden rounded-[4rem] shadow-[0_0_100px_rgba(0,0,0,0.5)] border border-white/10">
                  <img :src="inspectedProduct.images[lightboxIndex]" class="w-full h-full object-contain">
              </div>
            </Transition>

            <!-- Navigation Controls -->
            <div v-if="inspectedProduct?.images.length > 1" class="absolute inset-y-0 -inset-x-20 flex items-center justify-between pointer-events-none">
              <button 
                @click="prevImage"
                class="w-20 h-20 bg-white/10 hover:bg-white rounded-full flex items-center justify-center text-white hover:text-agri-forest transition-all shadow-2xl backdrop-blur-md pointer-events-auto"
              >
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
              </button>
              <button 
                @click="nextImage"
                class="w-20 h-20 bg-white/10 hover:bg-white rounded-full flex items-center justify-center text-white hover:text-agri-forest transition-all shadow-2xl backdrop-blur-md pointer-events-auto"
              >
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
              </button>
            </div>
          </div>

          <!-- Counter & Caption -->
          <div class="mt-12 text-center">
            <p class="text-[10px] font-black text-agri-earth uppercase tracking-[0.3em] mb-2">Visual Evidence {{ lightboxIndex + 1 }} of {{ inspectedProduct?.images.length }}</p>
            <h3 class="text-xl font-black text-white tracking-tight">{{ inspectedProduct?.name }}</h3>
          </div>
        </div>
      </Transition>
    </Teleport>

    <Modal
      :isOpen="isModalOpen"
      :title="modalConfig.title"
      :message="modalConfig.message"
      :confirm-text="modalConfig.confirmText"
      :type="modalConfig.type"
      :loading="isProcessing"
      @confirm="handleModalConfirm"
      @cancel="isModalOpen = false"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Modal from '@/components/Modal.vue';

const products = ref([]);
const categories = ref([]);
const loading = ref(true);
const showAdvancedFilters = ref(false);

const filters = ref({
  q: '',
  status: '',
  category_id: '',
  location: '',
  seller: '',
  min_price: null,
  max_price: null
});

let debounceTimer = null;
const debouncedFetch = () => {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => {
    fetchProducts();
  }, 500);
};

const resetFilters = () => {
  filters.value = {
    q: '',
    status: '',
    category_id: '',
    location: '',
    seller: '',
    min_price: null,
    max_price: null
  };
  fetchProducts();
};

const fetchCategories = async () => {
  try {
    const response = await axios.get('/api/categories');
    categories.value = response.data;
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

// Modal State
const isModalOpen = ref(false);
const modalConfig = ref({
  title: '',
  message: '',
  confirmText: '',
  type: 'primary',
  action: null,
  product: null
});
const isProcessing = ref(false);

// Drawer State
const isDrawerOpen = ref(false);
const inspectedProduct = ref(null);
const activeTab = ref('manifest');

// Lightbox State
const showLightbox = ref(false);
const lightboxIndex = ref(0);

const openInspector = (product) => {
  inspectedProduct.value = product;
  isDrawerOpen.value = true;
  activeTab.value = 'manifest';
};

const closeInspector = () => {
  isDrawerOpen.value = false;
  setTimeout(() => {
    inspectedProduct.value = null;
  }, 300);
};

const openLightbox = (index) => {
  lightboxIndex.value = index;
  showLightbox.value = true;
};

const nextImage = () => {
  if (inspectedProduct.value?.images) {
    lightboxIndex.value = (lightboxIndex.value + 1) % inspectedProduct.value.images.length;
  }
};

const prevImage = () => {
  if (inspectedProduct.value?.images) {
    const len = inspectedProduct.value.images.length;
    lightboxIndex.value = (lightboxIndex.value - 1 + len) % len;
  }
};

const fetchProducts = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/admin/products', {
      params: { ...filters.value }
    });
    products.value = response.data.data || response.data;
  } catch (error) {
    console.error('Error fetching products:', error);
  } finally {
    loading.value = false;
  }
};

const openApproveModal = (product) => {
  modalConfig.value = {
    title: 'Authorize Commodity Listing',
    message: `Confirm that ${product.name} meets all regulatory and quality standards. This will make the listing active on the global marketplace.`,
    confirmText: 'Confirm Authorization',
    type: 'success',
    action: 'approve',
    product: product
  };
  isModalOpen.value = true;
};

const openRejectModal = (product) => {
  modalConfig.value = {
    title: 'Refuse/Revoke Listing',
    message: `Are you sure you want to refuse or revoke ${product.name}? If already active, it will be immediately removed from the marketplace.`,
    confirmText: 'Confirm Refusal',
    type: 'danger',
    action: 'reject',
    product: product
  };
  isModalOpen.value = true;
};

const handleModalConfirm = async () => {
  const { action, product } = modalConfig.value;
  if (!product) return;

  isProcessing.value = true;
  try {
    const url = `/api/admin/products/${product.id}/${action}`;
    await axios.post(url);
    
    if (window.notify) {
      const successTitle = action === 'approve' ? 'Authorization Granted' : 'Listing Refused';
      const successMsg = action === 'approve' ? `${product.name} is now live.` : 'Compliance report generated.';
      window.notify.success(successTitle, successMsg);
    }
    
    isModalOpen.value = false;
    fetchProducts();
  } catch (error) {
    if (window.notify) window.notify.error('Ledger Error', 'Transmission interception or authorization failure.');
  } finally {
    isProcessing.value = false;
  }
};

const getStatusClasses = (status) => {
    switch (status) {
        case 'pending': return 'bg-yellow-50 text-yellow-600 border border-yellow-100';
        case 'available': return 'bg-agri-lush text-white';
        case 'rejected': return 'bg-red-50 text-red-600 border border-red-100';
        default: return 'bg-gray-50 text-gray-500';
    }
};

onMounted(() => {
  fetchProducts();
  fetchCategories();
});
</script>

<style scoped>
.tracking-tighter {
  letter-spacing: -2px;
}
</style>
