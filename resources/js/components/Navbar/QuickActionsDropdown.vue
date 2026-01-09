<template>
  <div class="relative">
    <button
      @click="toggleDropdown"
      class="flex items-center space-x-2 px-3 md:px-5 py-2.5 bg-agri-forest text-white rounded-xl hover:bg-agri-lush shadow-lg shadow-agri-forest/20 transition-all duration-300 group"
    >
      <svg class="h-4 w-4 transition-transform group-hover:rotate-90" :class="{ 'rotate-90': isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
      </svg>
      <span class="hidden sm:inline text-xs font-black uppercase tracking-widest">Global Action</span>
    </button>

    <transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="transform scale-95 opacity-0 translate-y-2"
      enter-to-class="transform scale-100 opacity-100 translate-y-0"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="transform scale-100 opacity-100 translate-y-0"
      leave-to-class="transform scale-95 opacity-0 translate-y-2"
    >
      <div
        v-if="isOpen"
        class="absolute right-0 mt-3 w-64 bg-white rounded-2xl shadow-2xl ring-1 ring-black ring-opacity-5 z-[70] py-3 border border-gray-100 overflow-hidden"
        @click.stop
      >
        <div class="px-5 py-2 mb-2 bg-gray-50/50">
          <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] leading-none">Marketplace Nexus</p>
        </div>
        <router-link
          to="/vendor/products/create"
          @click="closeDropdown"
          class="flex items-center px-5 py-3.5 text-sm text-gray-700 hover:bg-agri-sand/20 hover:text-agri-forest transition-colors group"
        >
          <div class="w-8 h-8 rounded-lg bg-agri-forest/5 flex items-center justify-center mr-3 text-agri-forest group-hover:bg-agri-forest group-hover:text-white transition-all">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
             </svg>
          </div>
          <span class="font-bold tracking-tight">Broadcast Listing</span>
        </router-link>
        <router-link
          to="/rfqs/create"
          @click="closeDropdown"
          class="flex items-center px-5 py-3.5 text-sm text-gray-700 hover:bg-agri-sand/20 hover:text-agri-forest transition-colors group"
        >
          <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center mr-3 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-all">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
          </div>
          <span class="font-bold tracking-tight">Submit RFQ Request</span>
        </router-link>
      </div>
    </transition>
  </div>
</template>

<script setup lang="js">
import { ref, onMounted, onUnmounted } from 'vue'

const isOpen = ref(false)

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
}

const closeDropdown = () => {
  isOpen.value = false
}

const handleClickOutside = (event) => {
  if (isOpen.value && !event.target.closest('.relative')) {
    closeDropdown()
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
