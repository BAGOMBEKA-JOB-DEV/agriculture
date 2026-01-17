<script setup lang="js">
import { computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from './stores/auth'
import Sidebar from './components/Sidebar.vue'
import TopNavbar from './components/TopNavbar.vue'
import Breadcrumbs from './components/Breadcrumbs.vue'
import Notification from './components/Notification.vue'
import AuthenticatedFooter from './components/AuthenticatedFooter.vue'

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
    <div v-if="isAuthenticatedRoute" class="min-h-screen bg-gray-50">
      <Sidebar>
        <div class="flex flex-col h-screen">
          <!-- Top Navbar -->
          <TopNavbar />

          <!-- Breadcrumbs -->
          <Breadcrumbs />

          <!-- Main Content Area -->
          <div class="flex-1 flex flex-col overflow-hidden">
            <main class="flex-1 overflow-y-auto p-6 scroll-smooth">
              <router-view />
              <!-- Footer within the scroll context -->
              <AuthenticatedFooter class="mt-12" />
            </main>
          </div>
        </div>
      </Sidebar>
    </div>
    <div v-else class="min-h-screen bg-gray-50">
      <router-view />
    </div>
  </div>
</template>
