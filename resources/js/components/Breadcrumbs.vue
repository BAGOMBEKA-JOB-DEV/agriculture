<template>
  <nav class="flex items-center bg-white/80 backdrop-blur-md border-b border-gray-100/50 shadow-sm overflow-hidden" aria-label="Breadcrumb">
    <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
      <ol class="flex items-center space-x-1">
        <!-- Home/Dashboard Link -->
        <li>
          <router-link
            to="/dashboard"
            class="flex items-center group transition-all duration-300"
          >
            <div class="w-8 h-8 rounded-xl bg-agri-forest/5 flex items-center justify-center text-agri-forest group-hover:bg-agri-forest group-hover:text-white transition-all duration-300">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
              </svg>
            </div>
          </router-link>
        </li>

        <!-- Breadcrumb Items -->
        <li v-for="(crumb, index) in breadcrumbs" :key="crumb.path" class="flex items-center">
          <!-- Professional Separator -->
          <svg class="w-4 h-4 text-gray-300 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>

          <!-- Clickable breadcrumb -->
          <router-link
            v-if="index < breadcrumbs.length - 1"
            :to="crumb.path"
            class="text-[10px] font-black text-gray-400 hover:text-agri-forest uppercase tracking-[0.2em] transition-all duration-300"
          >
            {{ crumb.label }}
          </router-link>

          <!-- Current page (Terminal State) -->
          <span
            v-else
            class="text-[10px] font-black text-agri-forest uppercase tracking-[0.2em] bg-agri-forest/5 px-3 py-1.5 rounded-lg border border-agri-forest/10"
          >
            {{ crumb.label }}
          </span>
        </li>
      </ol>

      <!-- Page Actions -->
      <div class="flex items-center space-x-3">
        <slot name="actions"></slot>
      </div>
    </div>
  </nav>
</template>

<script setup lang="js">
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

const breadcrumbs = computed(() => {
  const crumbs = []
  const matched = route.matched

  matched.forEach((match) => {
    if (match.meta && match.meta.breadcrumb) {
      const breadcrumb = match.meta.breadcrumb

      // Support both single object and array-based breadcrumbs
      const breadcrumbItems = Array.isArray(breadcrumb) ? breadcrumb : [breadcrumb]

      breadcrumbItems.forEach((item) => {
        let label = item.label || item
        let path = item.path || match.path

        // Dynamic Parameter Interpolation
        if (route.params) {
          Object.keys(route.params).forEach(param => {
            const value = route.params[param]
            path = path.replace(`:${param}`, value)
            if (label.includes(`:${param}`)) {
              label = label.replace(`:${param}`, value)
            }
          })
        }

        // Avoid duplicated entries
        if (!crumbs.some(c => c.label === label)) {
          crumbs.push({ label, path })
        }
      })
    }
  })

  return crumbs
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

nav {
  font-family: 'Inter', sans-serif;
}
</style>
