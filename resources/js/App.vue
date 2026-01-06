<script setup lang="js">
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import Sidebar from './components/Sidebar.vue'
import TopNavbar from './components/TopNavbar.vue'
import Breadcrumbs from './components/Breadcrumbs.vue'
import Notification from './components/Notification.vue'

const route = useRoute()

const isAuthenticatedRoute = computed(() => {
  return route.matched.some(record => record.meta.requiresAuth)
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
