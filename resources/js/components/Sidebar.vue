<template>
  <div class="flex h-screen bg-gray-50">
    <!-- Mobile sidebar backdrop -->
    <div
      v-if="isMobileOpen"
      class="fixed inset-0 z-40 lg:hidden"
      @click="closeMobileSidebar"
    >
      <div class="absolute inset-0 bg-agri-forest/30 backdrop-blur-sm"></div>
    </div>

    <!-- Sidebar -->
    <div
      :class="[
        'fixed inset-y-0 left-0 z-50 bg-agri-forest shadow-2xl transform transition-all duration-300 ease-in-out lg:static lg:inset-0',
        isMobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
        isCollapsed ? 'lg:w-[5.5rem]' : 'lg:w-64 w-64'
      ]"
    >
      <div class="flex flex-col h-full">
        <!-- Logo Area -->
        <div 
          class="flex items-center h-20 border-b border-agri-lush/30 relative overflow-hidden transition-all duration-300"
          :class="isCollapsed ? 'justify-center px-0' : 'px-6'"
        >
          <div class="flex items-center min-w-max">
            <div class="w-8 h-8 rounded-lg bg-agri-earth flex items-center justify-center shadow-sm flex-shrink-0">
              <svg class="w-5 h-5 text-agri-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
              </svg>
            </div>
            <transition
              enter-active-class="transition-opacity duration-300"
              enter-from-class="opacity-0"
              enter-to-class="opacity-100"
              leave-active-class="transition-opacity duration-200"
              leave-from-class="opacity-100"
              leave-to-class="opacity-0"
            >
              <h1 v-if="!isCollapsed" class="ml-3 text-white text-lg font-bold tracking-tight truncate">IAM</h1>
            </transition>
          </div>
          
          <!-- Desktop Collapse Toggle -->
          <button 
            @click="toggleCollapse"
            class="hidden lg:flex absolute right-4 w-6 h-6 items-center justify-center rounded-lg hover:bg-agri-lush/50 text-agri-earth transition-colors"
          >
            <svg class="w-4 h-4 transform transition-transform duration-300" :class="{ 'rotate-180': isCollapsed }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
          </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 overflow-y-auto space-y-8 scrollbar-hide">
          <div v-for="category in categorizedNavigation" :key="category.title">
            <h3 
              :class="[
                'px-2 text-xs font-semibold text-agri-earth uppercase tracking-wider mb-4 opacity-70 transition-all duration-300 truncate',
                isCollapsed ? 'opacity-0 h-0 mb-0 pointer-events-none' : 'opacity-70'
              ]"
            >
              {{ category.title }}
            </h3>
            <div class="space-y-1">
              <router-link
                v-for="item in category.items"
                :key="item.name"
                :to="item.path"
                :class="[
                  'group flex items-center py-2.5 text-sm font-medium rounded-xl transition-all duration-200 relative',
                  isCollapsed ? 'px-0 justify-center' : 'px-4',
                  $route.name === item.name || $route.path === item.path || ($route.path.startsWith(item.path + '/') && item.path !== '/dashboard' && item.path !== '/super-admin')
                    ? 'bg-agri-earth text-agri-forest shadow-lg'
                    : 'text-gray-300 hover:bg-agri-lush hover:text-white'
                ]"
                @click="closeMobileSidebar"
              >
                <div 
                  :class="[
                    'p-1 rounded-lg transition-all duration-200 flex-shrink-0',
                    isCollapsed ? 'mr-0' : 'mr-3',
                    $route.name === item.name || $route.path === item.path || ($route.path.startsWith(item.path + '/') && item.path !== '/dashboard' && item.path !== '/super-admin')
                      ? 'bg-agri-forest text-agri-earth'
                      : 'bg-agri-lush text-gray-400 group-hover:text-white'
                  ]"
                  v-html="item.icon"
                ></div>
                <transition
                  enter-active-class="transition-opacity duration-300"
                  enter-from-class="opacity-0"
                  enter-to-class="opacity-100"
                  leave-active-class="transition-opacity duration-200"
                  leave-from-class="opacity-100"
                  leave-to-class="opacity-0"
                >
                  <span v-if="!isCollapsed" class="truncate transition-opacity">{{ item.label }}</span>
                </transition>

                <!-- Nexus Insight Tooltip: Efficiency Mode Only -->
                <div 
                  v-if="isCollapsed" 
                  class="fixed left-[5.5rem] px-3 py-2 bg-white/95 backdrop-blur-md text-agri-forest text-[10px] font-black uppercase tracking-widest rounded-xl shadow-2xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 translate-x-1 group-hover:translate-x-0 z-[100] whitespace-nowrap lg:block hidden"
                >
                  {{ item.label }}
                  <!-- Tooltip Arrow -->
                  <div class="absolute right-full top-1/2 -translate-y-1/2 border-8 border-transparent border-r-white/95"></div>
                </div>
              </router-link>
            </div>
          </div>
        </nav>

        <!-- User Section -->
        <div class="p-4 border-t border-agri-lush/30">
          <div class="relative">
            <button
              @click="toggleDropdown"
              class="flex items-center w-full p-2 rounded-xl bg-agri-lush/50 hover:bg-agri-lush transition-all duration-300 group overflow-hidden"
              :class="{ 'justify-center': isCollapsed }"
            >
              <div class="flex-shrink-0">
                <div class="h-10 w-10 rounded-xl bg-agri-earth flex items-center justify-center shadow-inner">
                  <span class="text-sm font-bold text-agri-forest">{{ userInitial }}</span>
                </div>
              </div>
              <transition
                enter-active-class="transition-all duration-300"
                enter-from-class="w-0 opacity-0 ml-0"
                enter-to-class="w-auto opacity-100 ml-3"
                leave-active-class="transition-all duration-200"
                leave-from-class="w-auto opacity-100 ml-3"
                leave-to-class="w-0 opacity-0 ml-0"
              >
                <div v-if="!isCollapsed" class="ml-3 flex-1 text-left truncate transition-all overflow-hidden">
                  <p class="text-sm font-semibold text-white truncate">{{ user?.name || 'User Name' }}</p>
                  <p class="text-xs text-gray-400 truncate capitalize">{{ user?.role || 'Guest' }} Account</p>
                </div>
              </transition>
              <svg
                v-if="!isCollapsed"
                :class="['h-4 w-4 text-gray-400 transition-transform duration-200 ml-2', isDropdownOpen ? 'rotate-180' : '']"
                fill="none" stroke="currentColor" viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>

              <!-- Nexus Insight Tooltip: Efficiency Mode Only -->
              <div 
                v-if="isCollapsed" 
                class="fixed left-[5.5rem] px-3 py-2 bg-white/95 backdrop-blur-md text-agri-forest text-[10px] font-black uppercase tracking-widest rounded-xl shadow-2xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 translate-x-1 group-hover:translate-x-0 z-[100] whitespace-nowrap lg:block hidden"
              >
                Account Actions
                <!-- Tooltip Arrow -->
                <div class="absolute right-full top-1/2 -translate-y-1/2 border-8 border-transparent border-r-white/95"></div>
              </div>
            </button>

            <!-- User Dropdown -->
            <transition
              enter-active-class="transition duration-100 ease-out"
              enter-from-class="transform scale-95 opacity-0"
              enter-to-class="transform scale-100 opacity-100"
              leave-active-class="transition duration-75 ease-in"
              leave-from-class="transform scale-100 opacity-100"
              leave-to-class="transform scale-95 opacity-0"
            >
              <div
                v-if="isDropdownOpen"
                class="absolute bottom-full left-0 right-0 mb-2 py-1 bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden z-20"
              >
                <router-link
                  to="/settings"
                  @click="closeDropdown"
                  class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-agri-sand/50 transition-colors"
                >
                  <svg class="mr-3 h-5 w-5 text-agri-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                  My Profile
                </router-link>
                <button
                  @click="handleLogout"
                  class="flex items-center w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors"
                >
                  <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                  </svg>
                  Sign Out
                </button>
              </div>
            </transition>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content wrapper -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <!-- Mobile Top Bar -->
      <header class="bg-white border-b border-gray-100 lg:hidden flex items-center justify-between h-16 px-4 shrink-0">
        <button
          @click="toggleMobileSidebar"
          class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 focus:outline-none"
        >
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
        </button>
        <div class="flex items-center">
            <div class="w-6 h-6 rounded bg-agri-forest flex items-center justify-center mr-2">
                <svg class="w-3 h-3 text-agri-earth" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
          <span class="text-sm font-bold text-gray-900 tracking-[0.2em] uppercase">IAM</span>
        </div>
        <div class="w-10"></div>
      </header>

      <!-- Page Body -->
      <main class="flex-1 overflow-y-auto relative z-0 focus:outline-none">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup lang="js">
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'

// SVG Icons as strings to keep it clean
const icons = {
  dashboard: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>',
  projects: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path></svg>',
  boards: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>',
  tasks: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>',
  reports: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>',
  products: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>',
  rfqs: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>',
  users: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>'
}

const router = useRouter()
const route = useRoute()

const isMobileOpen = ref(false)
const isCollapsed = ref(false)
const isDropdownOpen = ref(false)
const user = ref(null)

const userInitial = computed(() => {
  return user.value?.name?.charAt(0)?.toUpperCase() || 'U'
})

const categorizedNavigation = computed(() => {
  const role = user.value?.role || 'user'
  
  const nav = [
    {
      title: 'Overview',
      items: [
        { name: 'Dashboard', path: '/dashboard', label: 'Dashboard', icon: icons.dashboard },
      ]
    }
  ]

  // Marketplace Section (Primary for all users)
  nav.push({
    title: 'Marketplace',
    items: [
      { name: 'Products', path: '/products', label: 'Browse Products', icon: icons.products },
      { name: 'RFQs', path: '/rfqs', label: 'Trade Requests', icon: icons.rfqs },
      { name: 'Messages', path: '/messages', label: 'Conversations', icon: icons.boards },
    ]
  })

  // Role-specific sections
  if (role === 'vendor') {
    nav.push({
      title: 'Sales Suite',
      items: [
        { name: 'MyProducts', path: '/vendor/products', label: 'Inventory Control', icon: icons.products },
      ]
    })
  }

  // Common Business logic
  nav.push({
    title: 'Trade Tiers',
    items: [
      { name: 'Subscriptions', path: '/subscriptions', label: 'Upgrade Access', icon: icons.reports },
    ]
  })

  // Communications (Common)
  nav.push({
    title: 'Communications',
    items: [
      { name: 'UserNotifications', path: '/notifications', label: 'Inbox', icon: icons.reports }, // Using reports icon for now or dashboard
    ]
  })

  if (role === 'admin') {
    nav.push({
      title: 'Regulator Suite',
      items: [
        { name: 'AdminProducts', path: '/admin/products', label: 'Asset Moderation', icon: icons.products },
        { name: 'MyProducts', path: '/vendor/products', label: 'Inventory Control', icon: icons.products },
        { name: 'AdminUsers', path: '/admin/users', label: 'Identity Nexus', icon: icons.users },
      ]
    })
  }

  if (role === 'super_admin') {
    nav.push({
      title: 'Global Governance',
      items: [
        { name: 'SuperAdminDashboard', path: '/super-admin', label: 'Governance Nexus', icon: icons.dashboard },
        { name: 'AdminProducts', path: '/admin/products', label: 'Global Product Oversight', icon: icons.products },
        { name: 'MyProducts', path: '/vendor/products', label: 'Inventory Control', icon: icons.products },
        { name: 'SuperAdminCategories', path: '/super-admin/categories', label: 'Asset Classifications', icon: icons.rfqs },
        { name: 'AdminNotifications', path: '/admin/notifications', label: 'Broadcast Center', icon: icons.reports },
        { name: 'AdminUsers', path: '/admin/users', label: 'Entity Registry', icon: icons.users },
      ]
    })
  }

  return nav
})

const toggleMobileSidebar = () => {
  isMobileOpen.value = !isMobileOpen.value
}

const toggleCollapse = () => {
  isCollapsed.value = !isCollapsed.value
}

const closeMobileSidebar = () => {
  isMobileOpen.value = false
}

const toggleDropdown = () => {
  isDropdownOpen.value = !isDropdownOpen.value
}

const closeDropdown = () => {
  isDropdownOpen.value = false
}

const fetchUser = async () => {
  try {
    const response = await axios.get('/api/user')
    user.value = response.data
  } catch (error) {
    console.error('Failed to fetch user:', error)
  }
}

const handleLogout = async () => {
  try {
    await axios.post('/api/logout')
    localStorage.removeItem('token')
    delete axios.defaults.headers.common['Authorization']
    closeDropdown()
    router.push('/login')
  } catch (error) {
    console.error('Logout failed:', error)
  }
}

onMounted(() => {
  fetchUser()
})
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
