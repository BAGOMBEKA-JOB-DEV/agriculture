<template>
  <transition
    enter-active-class="transition duration-200 ease-out"
    enter-from-class="transform scale-95 opacity-0 -translate-y-2"
    enter-to-class="transform scale-100 opacity-100 translate-y-0"
    leave-active-class="transition duration-150 ease-in"
    leave-from-class="transform scale-100 opacity-100 translate-y-0"
    leave-to-class="transform scale-95 opacity-0 -translate-y-2"
  >
    <div
      v-if="searchQuery.trim()"
      v-click-outside="() => $emit('close')"
      class="absolute top-full left-0 right-0 mt-3 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden z-[60]"
      :class="isMobile ? 'inset-x-0 mx-4' : ''"
    >
      <!-- Loading State -->
      <div v-if="isSearching && searchResults.products.length === 0" class="p-8 text-center bg-white">
          <div class="animate-pulse flex flex-col items-center">
              <div class="w-12 h-12 bg-gray-50 rounded-full mb-4"></div>
              <div class="h-2 w-32 bg-gray-50 rounded mb-2"></div>
              <div class="h-2 w-24 bg-gray-50 rounded"></div>
          </div>
      </div>

      <!-- Products Section -->
      <div v-else-if="searchResults.products.length > 0">
        <div class="px-5 py-3 bg-gray-50/50 border-b border-gray-100 flex items-center justify-between">
          <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Global Listings</span>
          <span class="text-[10px] font-bold text-agri-forest">{{ searchResults.products.length }} Matches</span>
        </div>
        <div class="divide-y divide-gray-50 max-h-[60vh] overflow-y-auto">
          <router-link
            v-for="product in searchResults.products"
            :key="product.id"
            :to="`/products/${product.id}`"
            @click="$emit('close')"
            class="flex items-center px-5 py-4 hover:bg-agri-sand/10 transition-colors group"
          >
            <div class="w-10 h-10 rounded-xl bg-agri-sand/30 flex items-center justify-center mr-4 group-hover:bg-agri-forest group-hover:text-white transition-all text-agri-forest">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-bold text-gray-900 truncate">{{ product.name }}</p>
              <p class="text-[10px] text-gray-500 font-medium">{{ product.category?.name || 'Unclassified' }}</p>
            </div>
            <div class="text-right ml-4">
              <p class="text-xs font-black text-agri-forest">${{ product.price }}</p>
            </div>
          </router-link>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50/80 px-5 py-3 border-t border-gray-100">
          <router-link to="/products" class="text-[10px] font-black text-agri-forest hover:text-agri-lush uppercase tracking-widest block text-center">
            Explore All Assets
          </router-link>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="!isSearching" class="p-10 text-center bg-white">
          <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
              <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
          </div>
          <h4 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-1">Zero Assets Found</h4>
          <p class="text-xs font-medium text-gray-400">No results found for "<span class="text-agri-forest font-bold">{{ searchQuery }}</span>"</p>
          <button @click="$emit('clear')" class="mt-4 text-[10px] font-black text-gray-400 hover:text-agri-forest uppercase tracking-[0.2em] transition-colors">
              Clear Discovery Filter
          </button>
      </div>
    </div>
  </transition>
</template>

<script setup lang="js">
const props = defineProps({
  isSearching: Boolean,
  searchResults: Object,
  searchQuery: String,
  isMobile: {
    type: Boolean,
    default: false
  }
})

defineEmits(['close', 'clear'])

// Click outside directive (local to component)
const vClickOutside = {
  mounted(el, binding) {
    el.clickOutsideEvent = (event) => {
      if (!(el === event.target || el.contains(event.target))) {
        binding.value(event)
      }
    }
    document.addEventListener('click', el.clickOutsideEvent)
  },
  unmounted(el) {
    document.removeEventListener('click', el.clickOutsideEvent)
  }
}
</script>
