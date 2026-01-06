<template>
  <nav class="flex mb-2 px-6 py-3 bg-white border-b border-gray-200" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-3">
      <!-- Home/Dashboard Link -->
      <li>
        <div>
          <router-link
            to="/dashboard"
            class="flex items-center text-gray-500 hover:text-gray-700 transition-colors duration-200"
          >
            <svg class="flex-shrink-0 h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="text-sm font-medium">Dashboard</span>
          </router-link>
        </div>
      </li>

      <!-- Breadcrumb Items -->
      <li v-for="(crumb, index) in breadcrumbs" :key="crumb.path" class="flex items-center">
        <!-- Separator -->
        <svg class="flex-shrink-0 h-4 w-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>

        <!-- Clickable breadcrumb -->
        <router-link
          v-if="index < breadcrumbs.length - 1"
          :to="crumb.path"
          class="text-sm font-medium text-gray-500 hover:text-indigo-600 transition-colors duration-200 flex items-center"
        >
          {{ crumb.label }}
        </router-link>

        <!-- Current page (not clickable) -->
        <span
          v-else
          class="text-sm font-semibold text-gray-900 flex items-center"
        >
          {{ crumb.label }}
        </span>
      </li>
    </ol>

    <!-- Page Actions (optional) -->
    <div class="ml-auto flex items-center space-x-3">
      <slot name="actions">
        <!-- Default actions can be added here -->
      </slot>
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

  for (let i = 0; i < matched.length; i++) {
    const match = matched[i]
    if (match.meta && match.meta.breadcrumb) {
      const breadcrumb = match.meta.breadcrumb

      // Handle dynamic segments
      let label = breadcrumb.label
      let path = match.path

      // Replace dynamic segments in path and label
      if (match.params) {
        Object.keys(match.params).forEach(param => {
          const value = match.params[param]
          path = path.replace(`:${param}`, value)

          // If label contains param placeholder, replace it
          if (label.includes(`:${param}`)) {
            label = label.replace(`:${param}`, value)
          }
        })
      }

      crumbs.push({
        label,
        path
      })
    }
  }

  return crumbs
})
</script>
