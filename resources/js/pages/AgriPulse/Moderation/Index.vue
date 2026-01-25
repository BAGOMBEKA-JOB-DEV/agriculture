<template>
  <div class="min-h-screen bg-[#F8FAFB] pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
      <!-- Header -->
      <div class="mb-10 flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-black text-agri-forest tracking-tight">AgriPulse Moderation</h1>
          <p class="text-gray-500 mt-1 font-medium">Manage posts, comments, and users</p>
        </div>
        <div class="flex items-center gap-3">
          <div class="px-4 py-2 bg-agri-forest/10 rounded-xl border border-agri-forest/20 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
            <span class="text-xs font-bold text-agri-forest uppercase tracking-widest">Live</span>
          </div>
        </div>
      </div>

      <div class="flex flex-col lg:flex-row gap-8 relative">
        <!-- Sub-Sidebar Navigation -->
        <aside class="w-full shrink-0 transition-all duration-300 ease-[cubic-bezier(0.25,0.1,0.25,1)] relative z-40"
          :class="isCollapsed ? 'lg:w-24' : 'lg:w-72'">
          <div
            class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-visible sticky top-24 transition-all duration-300"
            :class="isCollapsed ? 'p-2' : ''">

            <div class="flex items-center justify-between transition-all duration-300"
              :class="isCollapsed ? 'px-0 py-4 justify-center' : 'px-6 py-6 border-b border-gray-50'">
              <h2 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] transition-opacity duration-200"
                :class="isCollapsed ? 'hidden' : 'block'">Menu</h2>

              <button @click="isCollapsed = !isCollapsed"
                class="p-1.5 text-gray-400 hover:text-agri-forest hover:bg-agri-forest/10 rounded-lg transition-colors"
                :title="isCollapsed ? 'Expand menu' : 'Collapse menu'">
                <svg class="w-4 h-4 transition-transform duration-300" :class="isCollapsed ? 'rotate-180' : ''"
                  fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                </svg>
              </button>
            </div>

            <nav class="p-3 space-y-2">
              <button v-for="section in sections" :key="section.id" @click="activeSection = section.id"
                class="flex items-center rounded-2xl transition-all duration-300 group relative text-left" :class="[
                  activeSection === section.id
                    ? 'bg-agri-forest text-white shadow-lg shadow-agri-forest/20 hover:bg-agri-forest/90'
                    : 'text-gray-500 hover:bg-agri-forest/5 hover:text-agri-forest',
                  isCollapsed ? 'justify-center gap-0 p-0 w-12 h-12 mx-auto' : 'gap-4 px-4 py-3.5 w-full'
                ]">

                <div class="p-2 rounded-xl transition-colors duration-300 shrink-0"
                  :class="activeSection === section.id ? 'text-white' : 'bg-gray-100 text-gray-400 group-hover:bg-agri-forest/10 group-hover:text-agri-forest'"
                  v-html="section.icon"></div>

                <div class="flex flex-col overflow-hidden transition-all duration-300"
                  :class="isCollapsed ? 'w-0 opacity-0' : 'w-auto opacity-100'">
                  <span class="font-bold text-sm whitespace-nowrap">{{ section.name }}</span>
                  <span v-if="activeSection === section.id"
                    class="text-[10px] opacity-70 uppercase tracking-widest font-medium whitespace-nowrap">Selected</span>
                </div>

                <!-- Tooltip (only visible when collapsed) -->
                <div v-if="isCollapsed"
                  class="absolute left-full top-1/2 -translate-y-1/2 ml-4 px-4 py-2.5 bg-white border border-gray-100 text-agri-forest text-xs font-black uppercase tracking-widest rounded-xl opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-[100] shadow-[0_8px_30px_rgb(0,0,0,0.12)]">
                  {{ section.name }}
                  <!-- Arrow -->
                  <div
                    class="absolute right-full top-1/2 -translate-y-1/2 -mr-[1px] border-8 border-transparent border-r-white drop-shadow-sm rotate-180">
                  </div>
                </div>
              </button>
            </nav>

            <div class="bg-gray-50/50 mt-4 border-t border-gray-100 overflow-hidden transition-all duration-300"
              :class="isCollapsed ? 'h-0 opacity-0 p-0' : 'p-6 h-auto opacity-100'">
              <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest leading-loose">
                Only for moderators. All actions are recorded.
              </p>
            </div>
          </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 min-w-0">
          <transition mode="out-in" enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 translate-y-4" enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-200 ease-in" leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-4">
            <component :is="activeComponent" :key="activeSection" @view-report="handleViewReport"
              :selected-report-id="selectedReportId" :global-stats="dashboardStats" @view-logs="activeSection = 'logs'"
              @refresh-stats="fetchDashboardStats" />
          </transition>
        </main>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, defineAsyncComponent, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()
const activeSection = ref(route.query.section || 'overview')
const selectedReportId = ref(null)
const isCollapsed = ref(false)
const dashboardStats = ref({})
const loadingStats = ref(true)

const fetchDashboardStats = async () => {
  try {
    const response = await axios.get('/api/agripulse/moderation/dashboard')
    dashboardStats.value = response.data.stats
  } catch (error) {
    console.error('Failed to fetch dashboard stats:', error)
  } finally {
    loadingStats.value = false
  }
}

const handleViewReport = (reportId) => {
  selectedReportId.value = reportId
  activeSection.value = 'reports'
}

watch(activeSection, (newSection) => {
  router.replace({ query: { ...route.query, section: newSection } })
})

onMounted(() => {
  fetchDashboardStats()
  if (route.query.section) {
    activeSection.value = route.query.section
  }
})

const sections = [
  {
    id: 'overview',
    name: 'Overview',
    icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>',
    component: defineAsyncComponent(() => import('./Sections/OverviewSection.vue'))
  },
  {
    id: 'posts',
    name: 'Post Management',
    icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>',
    component: defineAsyncComponent(() => import('./Sections/PostsSection.vue'))
  },
  {
    id: 'comments',
    name: 'Comments',
    icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>',
    component: defineAsyncComponent(() => import('./Sections/CommentsSection.vue'))
  },
  {
    id: 'users',
    name: 'User Control',
    icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>',
    component: defineAsyncComponent(() => import('./Sections/UsersSection.vue'))
  },
  {
    id: 'reports',
    name: 'Report Queue',
    icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
    component: defineAsyncComponent(() => import('./Sections/ReportsSection.vue'))
  },
  {
    id: 'logs',
    name: 'Activity Logs',
    icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
    component: defineAsyncComponent(() => import('./Sections/ActivityLogsSection.vue'))
  },
  {
    id: 'analytics',
    name: 'Insights',
    icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>',
    component: defineAsyncComponent(() => import('./Sections/AnalyticsSection.vue'))
  }
]

const activeComponent = computed(() => {
  return sections.find(s => s.id === activeSection.value)?.component
})
</script>
