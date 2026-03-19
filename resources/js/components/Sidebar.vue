<template>
  <div class="flex h-screen bg-gray-50">
    <!-- Mobile sidebar backdrop -->
    <div v-if="isMobileOpen" class="fixed inset-0 z-40 lg:hidden" @click="closeMobileSidebar">
      <div class="absolute inset-0 bg-agri-forest/30 backdrop-blur-sm"></div>
    </div>

    <!-- Sidebar -->
    <div :class="[
      'fixed inset-y-0 left-0 z-50 bg-agri-forest shadow-2xl transform transition-all duration-300 ease-in-out lg:static lg:inset-0',
      isMobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
      isCollapsed ? 'lg:w-[5.5rem]' : 'lg:w-64 w-64'
    ]">
      <div class="flex flex-col h-full">
        <!-- Logo Area -->
        <div
          class="flex items-center h-20 border-b border-agri-lush/30 relative overflow-hidden transition-opacity duration-300"
          :class="[
            isCollapsed ? 'justify-center px-0' : 'px-6',
            isDropdownOpen ? 'opacity-40' : ''
          ]">
          <div class="flex items-center min-w-max">
            <div class="w-8 h-8 rounded-lg bg-agri-earth flex items-center justify-center shadow-sm flex-shrink-0">
              <svg class="w-5 h-5 text-agri-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                </path>
              </svg>
            </div>
            <transition enter-active-class="transition-opacity duration-300" enter-from-class="opacity-0"
              enter-to-class="opacity-100" leave-active-class="transition-opacity duration-200"
              leave-from-class="opacity-100" leave-to-class="opacity-0">
              <h1 v-if="!isCollapsed" class="ml-3 text-white text-lg font-bold tracking-tight truncate">IAM</h1>
            </transition>
          </div>

          <!-- Desktop Collapse Toggle -->
          <button @click="toggleCollapse"
            class="hidden lg:flex absolute right-4 w-6 h-6 items-center justify-center rounded-lg hover:bg-agri-lush/50 text-agri-earth transition-colors">
            <svg class="w-4 h-4 transform transition-transform duration-300" :class="{ 'rotate-180': isCollapsed }"
              fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
          </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 overflow-y-auto space-y-8 scrollbar-hide transition-opacity duration-300 delay-75"
          :class="isDropdownOpen ? 'opacity-40' : ''">
          <div v-for="category in categorizedNavigation" :key="category.title">
            <h3 :class="[
              'px-2 text-xs font-semibold text-agri-earth uppercase tracking-wider mb-4 opacity-70 transition-all duration-300 truncate',
              isCollapsed ? 'opacity-0 h-0 mb-0 pointer-events-none' : 'opacity-70'
            ]">
              {{ category.title }}
            </h3>
            <div class="space-y-1">
              <router-link v-for="item in category.items" :key="item.name" :to="item.path" :class="[
                'group flex items-center py-2.5 text-sm font-medium rounded-xl transition-all duration-200 relative z-20 cursor-pointer',
                isCollapsed ? 'px-0 justify-center' : 'px-4',
                isActive(item)
                  ? 'bg-agri-earth text-agri-forest shadow-lg'
                  : 'text-gray-300 hover:bg-agri-lush hover:text-white'
              ]" @click="closeMobileSidebar">
                <div :class="[
                  'p-1 rounded-lg transition-all duration-200 flex-shrink-0',
                  isCollapsed ? 'mr-0' : 'mr-3',
                  isActive(item)
                    ? 'bg-agri-forest text-agri-earth'
                    : 'bg-agri-lush text-gray-400 group-hover:text-white'
                ]" v-html="item.icon"></div>
                <transition enter-active-class="transition-opacity duration-300" enter-from-class="opacity-0"
                  enter-to-class="opacity-100" leave-active-class="transition-opacity duration-200"
                  leave-from-class="opacity-100" leave-to-class="opacity-0">
                  <span v-if="!isCollapsed" class="truncate transition-opacity">{{ item.label }}</span>
                </transition>

                <!-- Nexus Insight Tooltip: Efficiency Mode Only -->
                <div v-if="isCollapsed"
                  class="fixed left-[5.5rem] px-3 py-2 bg-white/95 backdrop-blur-md text-agri-forest text-[10px] font-black uppercase tracking-widest rounded-xl shadow-2xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 translate-x-1 group-hover:translate-x-0 z-[100] whitespace-nowrap lg:block hidden">
                  {{ item.label }}
                  <!-- Tooltip Arrow -->
                  <div
                    class="absolute right-full top-1/2 -translate-y-1/2 border-8 border-transparent border-r-white/95">
                  </div>
                </div>
              </router-link>
            </div>
          </div>
        </nav>

        <!-- User Section -->
        <div class="p-4 border-t border-agri-lush/30">
          <div class="relative">
            <button @click="toggleDropdown"
              class="flex items-center w-full p-2 rounded-xl bg-agri-lush/50 hover:bg-agri-lush transition-all duration-300 group overflow-hidden"
              :class="{ 'justify-center': isCollapsed }">
              <div class="flex-shrink-0">
                <div
                  class="h-10 w-10 rounded-xl bg-agri-earth flex items-center justify-center shadow-inner overflow-hidden">
                  <img v-if="user?.avatar_url" :src="user.avatar_url" class="w-full h-full object-cover">
                  <span v-else class="text-sm font-bold text-agri-forest">{{ userInitial }}</span>
                </div>
              </div>
              <transition enter-active-class="transition-all duration-300" enter-from-class="w-0 opacity-0 ml-0"
                enter-to-class="w-auto opacity-100 ml-3" leave-active-class="transition-all duration-200"
                leave-from-class="w-auto opacity-100 ml-3" leave-to-class="w-0 opacity-0 ml-0">
                <div v-if="!isCollapsed" class="ml-3 flex-1 text-left truncate transition-all overflow-hidden">
                  <p class="text-sm font-semibold text-white truncate">{{ user?.name || 'User Name' }}</p>
                  <p class="text-xs text-gray-400 truncate capitalize">{{ user?.role || 'Guest' }} Account</p>
                </div>
              </transition>
              <svg v-if="!isCollapsed"
                :class="['h-4 w-4 text-gray-400 transition-transform duration-200 ml-2', isDropdownOpen ? 'rotate-180' : '']"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>

              <!-- Nexus Insight Tooltip: Efficiency Mode Only -->
              <div v-if="isCollapsed"
                class="fixed left-[5.5rem] px-3 py-2 bg-white/95 backdrop-blur-md text-agri-forest text-[10px] font-black uppercase tracking-widest rounded-xl shadow-2xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 translate-x-1 group-hover:translate-x-0 z-[100] whitespace-nowrap lg:block hidden">
                Account Actions
                <!-- Tooltip Arrow -->
                <div class="absolute right-full top-1/2 -translate-y-1/2 border-8 border-transparent border-r-white/95">
                </div>
              </div>
            </button>

            <!-- User Dropdown -->
            <transition enter-active-class="transition duration-200 ease-out"
              enter-from-class="transform scale-95 opacity-0 translate-y-2"
              enter-to-class="transform scale-100 opacity-100 translate-y-0"
              leave-active-class="transition duration-150 ease-in"
              leave-from-class="transform scale-100 opacity-100 translate-y-0"
              leave-to-class="transform scale-95 opacity-0 translate-y-2">
              <div v-if="isDropdownOpen"
                class="absolute bottom-full left-0 right-0 mb-3 mx-1 py-3 bg-agri-forest rounded-2xl shadow-[0_0_50px_0_rgba(0,0,0,0.6)] ring-1 ring-white/10 z-50 overflow-hidden">
                <!-- Profile Link -->
                <router-link to="/settings" @click="closeDropdown"
                  class="flex items-center px-5 py-3.5 text-sm font-medium text-white hover:bg-white/10 transition-all duration-300 group border-b border-white/10 relative overflow-hidden">
                  <div
                    class="h-9 w-9 rounded-xl bg-white/10 flex items-center justify-center mr-4 group-hover:bg-agri-earth/20 transition-all duration-300 shadow-inner border border-white/5">
                    <svg class="h-4 w-4 text-agri-earth group-hover:text-white transition-colors duration-300"
                      fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                  </div>
                  <div class="flex flex-col relative z-10">
                    <span
                      class="font-bold tracking-tight text-white group-hover:translate-x-1 transition-transform duration-300">My
                      Profile</span>
                    <span
                      class="text-[10px] text-gray-300 uppercase tracking-[0.2em] group-hover:text-white/90 transition-colors duration-300">Manage
                      Account</span>
                  </div>
                </router-link>

                <!-- Sign Out -->
                <button @click="handleLogout"
                  class="flex items-center w-full px-5 py-3.5 text-sm font-medium text-red-300 hover:bg-red-500/10 hover:text-red-200 transition-all duration-300 group relative overflow-hidden">
                  <div
                    class="h-9 w-9 rounded-xl bg-red-500/10 flex items-center justify-center mr-4 group-hover:bg-red-500/20 transition-all duration-300 border border-red-500/10">
                    <svg class="h-4 w-4 transition-transform group-hover:rotate-180 duration-500" fill="none"
                      stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                      </path>
                    </svg>
                  </div>
                  <div class="flex flex-col items-start pr-2 relative z-10">
                    <span
                      class="font-bold tracking-tight group-hover:translate-x-1 transition-transform duration-300">Sign
                      Out</span>
                    <span
                      class="text-[10px] text-red-300/50 uppercase tracking-[0.2em] group-hover:text-red-200/80 transition-colors duration-300 w-full text-left">End
                      Session</span>
                  </div>
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
        <button @click="toggleMobileSidebar" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 focus:outline-none">
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
        </button>
        <div class="flex items-center">
          <div class="w-6 h-6 rounded bg-agri-forest flex items-center justify-center mr-2">
            <svg class="w-3 h-3 text-agri-earth" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
              </path>
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
  users: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>',
  pulse: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>',
  shield: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>',
  support: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>'
}

const router = useRouter()
const route = useRoute()

const isMobileOpen = ref(false)
const isCollapsed = ref(false)
const isDropdownOpen = ref(false)
const user = ref(null)

const isActive = (item) => {
  // Prevent AgriPulse link from being active when in moderation
  if (item.name === 'AgriPulse' && route.path.includes('/agripulse/moderation')) {
    return false
  }

  return route.name === item.name ||
    route.path === item.path ||
    (route.path.startsWith(item.path + '/') && item.path !== '/dashboard' && item.path !== '/super-admin')
}

const userInitial = computed(() => {
  return user.value?.name?.charAt(0)?.toUpperCase() || 'U'
})

const userPermissions = computed(() => {
  const perms = new Set()
  user.value?.roles?.forEach(role => {
    role.permissions?.forEach(p => perms.add(p.slug))
  })
  return perms
})

const can = (permission) => {
  return userPermissions.value.has(permission)
}

const categorizedNavigation = computed(() => {
  const nav = []

  // Overview
  if (can('view_dashboard')) {
    nav.push({
      title: 'Overview',
      items: [
        { name: 'Dashboard', path: '/dashboard', label: 'Dashboard', icon: icons.dashboard },
        { name: 'AgriPulse', path: '/agripulse', label: 'AgriPulse', icon: icons.pulse },
      ]
    })
  }

  // Global Network (Dedicated Section)
  const networkItems = []
  if (can('view_vendors')) {
    networkItems.push({ name: 'NetworkVendors', path: '/network/vendors', label: 'Verified Vendors', icon: icons.users })
  }
  if (can('view_traders')) {
    networkItems.push({ name: 'NetworkTraders', path: '/network/traders', label: 'Active Traders', icon: icons.rfqs })
  }
  if (can('view_buyers')) {
    networkItems.push({ name: 'NetworkBuyers', path: '/network/buyers', label: 'Global Buyers', icon: icons.dashboard })
  }

  if (networkItems.length > 0) {
    nav.push({
      title: 'Global Network',
      items: networkItems
    })
  }

  // Marketplace Section
  const marketplaceItems = []

  if (can('view_marketplace_products')) {
    marketplaceItems.push({ name: 'Products', path: '/products', label: 'Browse Products', icon: icons.products })
  }
  if (can('view_rfqs')) {
    marketplaceItems.push({ name: 'RFQs', path: '/rfqs', label: 'Trade Requests', icon: icons.rfqs })
  }
  if (can('access_conversations')) {
    marketplaceItems.push({ name: 'Messages', path: '/messages', label: 'Conversations', icon: icons.boards })
  }

  if (marketplaceItems.length > 0) {
    nav.push({
      title: 'Marketplace',
      items: marketplaceItems
    })
  }

  // Sales Suite
  if (can('access_vendor') && can('manage_warehouse')) {
    nav.push({
      title: 'Sales Suite',
      items: [
        { name: 'MyProducts', path: '/vendor/products', label: 'Inventory Control', icon: icons.products },
      ]
    })
  }

  // Trade Tiers
  if (can('view_subscriptions')) {
    nav.push({
      title: 'Trade Tiers',
      items: [
        { name: 'Subscriptions', path: '/subscriptions', label: 'Upgrade Access', icon: icons.reports },
      ]
    })
  }

  // Communications & Help
  const communicationItems = []
  if (can('access_inbox')) {
    communicationItems.push({ name: 'UserNotifications', path: '/notifications', label: 'Inbox', icon: icons.reports })
  }
  // All authenticated users can access Support Center by default, or with specific permission
  communicationItems.push({ name: 'SupportCenter', path: '/support', label: 'Support Center', icon: icons.support })

  if (communicationItems.length > 0) {
    nav.push({
      title: 'Communications & Help',
      items: communicationItems
    })
  }

  // Dynamic: Regulator Suite (Admin Access)
  const adminItems = []

  if (can('moderate_products')) {
    adminItems.push({ name: 'AdminProducts', path: '/admin/products', label: 'Asset Moderation', icon: icons.products })
  }

  if (can('manage_users')) {
    // Removed Identity Nexus - now handled in User Management section
  }

  if (can('agripulse.moderate.posts') || can('agripulse.moderate.comments') || can('agripulse.moderate.users') || can('agripulse.moderate.reports')) {
    adminItems.push({ name: 'AgriPulseModeration', path: '/agripulse/moderation', label: 'AgriPulse Moderation Hub', icon: icons.shield })
  }

  if (can('support.tickets.view_all') || can('support.tickets.view_assigned') || can('support.kb.manage') || can('support.settings.sla') || can('support.reports.view')) {
    adminItems.push({ name: 'AgentPortal', path: '/support/admin', label: 'Agent Portal', icon: icons.support })
  }

  if (adminItems.length > 0) {
    nav.push({
      title: 'Regulator Suite',
      items: adminItems
    })
  }

  // Dynamic: Global Governance (Super Admin + Specialized Permissions)
  const governanceItems = []

  // Super Admin Dashboard (only for super admins)
  if (can('access_super_admin')) {
    governanceItems.push({ name: 'SuperAdminDashboard', path: '/super-admin', label: 'User Management', icon: icons.dashboard })
  }

  if (can('manage_categories')) {
    governanceItems.push({ name: 'SuperAdminCategories', path: '/super-admin/categories', label: 'Asset Classifications', icon: icons.rfqs })
  }

  if (can('broadcast_notifications')) {
    governanceItems.push({ name: 'AdminNotifications', path: '/admin/notifications', label: 'Broadcast Center', icon: icons.reports })
  }

  if (can('manage_roles')) {
    governanceItems.push({ name: 'RoleManagement', path: '/super-admin/roles', label: 'Role Ecosystem', icon: icons.tasks })
  }

  if (governanceItems.length > 0) {
    nav.push({
      title: 'User Management',
      items: governanceItems
    })
  }

  // Analytics & Reports (Dedicated Section)
  const reportItems = []

  if (can('view_reports_financial') || can('access_super_admin')) {
    reportItems.push({ name: 'ReportsFinancial', path: '/reports/financial', label: 'Financial Reports', icon: icons.reports })
  }

  if (can('view_reports_sales') || can('access_super_admin')) {
    reportItems.push({ name: 'ReportsSales', path: '/reports/sales', label: 'Sales Reports', icon: icons.products })
  }

  if (can('view_reports_market') || can('access_super_admin')) {
    reportItems.push({ name: 'ReportsMarket', path: '/reports/market', label: 'Demand Analysis', icon: icons.rfqs })
  }

  if (can('view_reports_products') || can('access_super_admin')) {
    reportItems.push({ name: 'ReportsProducts', path: '/reports/products', label: 'Product Reports', icon: icons.tasks })
  }

  if (can('view_reports_users') || can('access_super_admin')) {
    reportItems.push({ name: 'ReportsUser', path: '/reports/users', label: 'User Reports', icon: icons.users })
  }

  if (can('view_reports_compliance') || can('access_super_admin')) {
    reportItems.push({ name: 'ReportsCompliance', path: '/reports/compliance', label: 'Compliance Reports', icon: icons.reports })
  }

  if (can('view_reports_trade_requests') || can('access_super_admin')) {
    reportItems.push({ name: 'ReportsOperations', path: '/reports/operations', label: 'Trade Requests', icon: icons.tasks })
  }

  if (can('view_reports_system') || can('access_super_admin')) {
    reportItems.push({ name: 'ReportsSystem', path: '/reports/system', label: 'System Health', icon: icons.dashboard })
  }

  if (reportItems.length > 0) {
    nav.push({
      title: 'Analytics & Reports',
      items: reportItems
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
