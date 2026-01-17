<script setup lang="js">
import { computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from './stores/auth'
import Sidebar from './components/Sidebar.vue'
import TopNavbar from './components/TopNavbar.vue'
import Breadcrumbs from './components/Breadcrumbs.vue'
import Notification from './components/Notification.vue'

const route = useRoute()
const authStore = useAuthStore()

const isAuthenticatedRoute = computed(() => {
  return route.matched.some(record => record.meta.requiresAuth)
})

onMounted(() => {
  authStore.checkAuth()
})
</script>

<template>
  <div id="app" class="font-sans">
    <Notification />
    <div v-if="isAuthenticatedRoute" class="min-h-screen bg-gray-50 iam-authenticated-layout">
      <Sidebar>
        <div class="flex flex-col h-screen">
          <!-- Top Navbar -->
          <TopNavbar />

          <!-- Breadcrumbs -->
          <Breadcrumbs />

          <!-- Main Content -->
          <main class="flex-1 overflow-y-auto p-6">
            <router-view />
          </main>
        </div>
      </Sidebar>
    </div>
    <div v-else class="min-h-screen bg-gray-50">
      <router-view />
    </div>
  </div>
</template>

<style>
/* 
  IAM Global Layout "Premium Stretch"
  Allows content to fill the screen while maintaining 
  elegant horizontal gutters for professional aesthetics.
  SCOPED: Only applies to authenticated routes.
*/

/* Target ALL Tailwind width-limiting containers ONLY in the authenticated area */
#app .iam-authenticated-layout .container,
#app .iam-authenticated-layout .max-w-7xl,
#app .iam-authenticated-layout .max-w-6xl,
#app .iam-authenticated-layout .max-w-5xl,
#app .iam-authenticated-layout .max-w-4xl,
#app .iam-authenticated-layout .max-w-3xl,
#app .iam-authenticated-layout .max-w-2xl,
#app .iam-authenticated-layout .max-w-xl,
#app .iam-authenticated-layout nav .max-w-7xl,
#app .iam-authenticated-layout nav .container {
  max-width: 100% !important;
  width: 100% !important;
  margin-left: 0 !important;
  margin-right: 0 !important;
  /* Maintain internal aesthetic padding */
  padding-left: 1.5rem !important;
  padding-right: 1.5rem !important;
  transition: all 0.2s ease-in-out;
}

/* Force left-alignment without aggressive edge-touching for auth layout */
#app .iam-authenticated-layout .mx-auto {
  margin-left: 0 !important;
  margin-right: 0 !important;
}

/* Limit stretch on extremely wide monitors for readability in auth layout */
@media (min-width: 1920px) {

  #app .iam-authenticated-layout .container,
  #app .iam-authenticated-layout .max-w-7xl {
    max-width: 1600px !important;
  }
}
</style>
