<template>
  <div class="min-h-screen bg-gray-50/50 pb-12">
    <Notification ref="notificationRef" />

    <!-- Header Section -->
    <div class="bg-white border-b border-gray-100 px-8 pt-8 pb-0 mb-8">
      <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-8">
          <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Admin Alerts</h1>
            <p class="mt-2 text-[11px] text-gray-400 font-semibold uppercase tracking-wider">Monitor community updates
              and broadcast system-wide signals</p>
          </div>
          <div class="flex items-center space-x-3">
            <router-link to="/admin/notifications/create"
              class="inline-flex items-center justify-center px-6 py-3 border border-transparent shadow-lg shadow-agri-forest/20 text-sm font-bold rounded-xl text-white bg-agri-forest hover:bg-agri-lush transform hover:-translate-y-0.5 transition-all duration-200">
              <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              New Announcement
            </router-link>
          </div>
        </div>

        <!-- Tab Switcher -->
        <div class="flex space-x-8">
          <button @click="switchTab('broadcasts')"
            class="pb-4 text-xs font-black uppercase tracking-[0.2em] transition-all relative"
            :class="activeTab === 'broadcasts' ? 'text-agri-forest' : 'text-gray-400 hover:text-gray-600'">
            System Announcements
            <div v-if="activeTab === 'broadcasts'"
              class="absolute bottom-0 left-0 right-0 h-1 bg-agri-forest rounded-t-full"></div>
          </button>
          <button @click="switchTab('agripulse')"
            class="pb-4 text-xs font-black uppercase tracking-[0.2em] transition-all relative"
            :class="activeTab === 'agripulse' ? 'text-agri-forest' : 'text-gray-400 hover:text-gray-600'">
            AgriPulse Moderation Updates
            <div v-if="activeTab === 'agripulse'"
              class="absolute bottom-0 left-0 right-0 h-1 bg-agri-forest rounded-t-full"></div>
          </button>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-8 space-y-8">

      <!-- SLICK METRIC CARDS ROW (Context Aware) -->
      <div v-if="activeTab === 'broadcasts'" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div
          class="bg-gradient-to-br from-blue-400/10 to-blue-500/5 backdrop-blur-md p-6 rounded-[2rem] border border-blue-200/30 transition-all duration-500 group cursor-default relative overflow-hidden hover:-translate-y-1">
          <div
            class="absolute -top-10 -right-10 w-24 h-24 rounded-full blur-xl bg-blue-500/10 transition-all duration-700 group-hover:scale-110">
          </div>
          <div class="relative z-10 flex flex-col items-start space-y-4">
            <div
              class="p-3 rounded-xl bg-blue-500 border border-white/20 shadow-lg group-hover:scale-105 transition-all">
              <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path
                  d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"
                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
              </svg>
            </div>
            <div>
              <p class="text-3xl font-black text-blue-700 tracking-tight">{{ broadcastPagination.total || 0 }}</p>
              <h4 class="text-[9px] font-bold uppercase tracking-wider mt-1 text-blue-600/70">Total Announcements</h4>
            </div>
          </div>
        </div>

        <div
          class="bg-gradient-to-br from-red-400/10 to-red-500/5 backdrop-blur-md p-6 rounded-[2rem] border border-red-200/30 transition-all duration-500 group cursor-default relative overflow-hidden hover:-translate-y-1">
          <div
            class="absolute -top-10 -right-10 w-24 h-24 rounded-full blur-xl bg-red-500/10 transition-all duration-700 group-hover:scale-110">
          </div>
          <div class="relative z-10 flex flex-col items-start space-y-4">
            <div
              class="p-3 rounded-xl bg-red-500 border border-white/20 shadow-lg group-hover:scale-105 transition-all">
              <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"
                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
              </svg>
            </div>
            <div>
              <p class="text-3xl font-black text-red-700 tracking-tight">{{ criticalBroadcastCount }}</p>
              <h4 class="text-[9px] font-bold uppercase tracking-wider mt-1 text-red-600/70">Urgent Alerts</h4>
            </div>
          </div>
        </div>

        <div
          class="bg-gradient-to-br from-orange-400/10 to-orange-500/5 backdrop-blur-md p-6 rounded-[2rem] border border-orange-200/30 transition-all duration-500 group cursor-default relative overflow-hidden hover:-translate-y-1">
          <div
            class="absolute -top-10 -right-10 w-24 h-24 rounded-full blur-xl bg-orange-500/10 transition-all duration-700 group-hover:scale-110">
          </div>
          <div class="relative z-10 flex flex-col items-start space-y-4">
            <div
              class="p-3 rounded-xl bg-orange-500 border border-white/20 shadow-lg group-hover:scale-105 transition-all">
              <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"
                  stroke-width="2.5" />
              </svg>
            </div>
            <div>
              <p class="text-2xl font-black text-orange-700 tracking-tight">{{ lastBroadcastDate }}</p>
              <h4 class="text-[9px] font-bold uppercase tracking-wider mt-1 text-orange-600/70">Last Broadcast</h4>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div
          class="bg-gradient-to-br from-agri-forest/10 to-agri-forest/5 backdrop-blur-md p-6 rounded-[2rem] border border-agri-forest/20 transition-all duration-500 group cursor-default relative overflow-hidden hover:-translate-y-1">
          <div
            class="absolute -top-10 -right-10 w-24 h-24 rounded-full blur-xl bg-agri-forest/10 transition-all duration-700 group-hover:scale-110">
          </div>
          <div class="relative z-10 flex flex-col items-start space-y-4">
            <div
              class="p-3 rounded-xl bg-agri-forest border border-white/20 shadow-lg group-hover:scale-105 transition-all">
              <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-linecap="round" stroke-linejoin="round"
                  stroke-width="2.5" />
              </svg>
            </div>
            <div>
              <p class="text-3xl font-black text-agri-forest tracking-tight">{{ agriPulsePagination.total || 0 }}</p>
              <h4 class="text-[9px] font-bold uppercase tracking-wider mt-1 text-agri-forest/70">Active Signals</h4>
            </div>
          </div>
        </div>

        <div
          class="bg-gradient-to-br from-red-400/10 to-red-500/5 backdrop-blur-md p-6 rounded-[2rem] border border-red-200/30 transition-all duration-500 group cursor-default relative overflow-hidden hover:-translate-y-1">
          <div
            class="absolute -top-10 -right-10 w-24 h-24 rounded-full blur-xl bg-red-500/10 transition-all duration-700 group-hover:scale-110">
          </div>
          <div class="relative z-10 flex flex-col items-start space-y-4">
            <div
              class="p-3 rounded-xl bg-red-500 border border-white/20 shadow-lg group-hover:scale-105 transition-all">
              <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"
                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
              </svg>
            </div>
            <div>
              <p class="text-3xl font-black text-red-700 tracking-tight">{{ criticalSignalCount }}</p>
              <h4 class="text-[9px] font-bold uppercase tracking-wider mt-1 text-red-600/70">Critical Signals</h4>
            </div>
          </div>
        </div>

        <div
          class="bg-gradient-to-br from-purple-400/10 to-purple-500/5 backdrop-blur-md p-6 rounded-[2rem] border border-purple-200/30 transition-all duration-500 group cursor-default relative overflow-hidden hover:-translate-y-1">
          <div
            class="absolute -top-10 -right-10 w-24 h-24 rounded-full blur-xl bg-purple-500/10 transition-all duration-700 group-hover:scale-110">
          </div>
          <div class="relative z-10 flex flex-col items-start space-y-4">
            <div
              class="p-3 rounded-xl bg-purple-500 border border-white/20 shadow-lg group-hover:scale-105 transition-all">
              <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path
                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
              </svg>
            </div>
            <div>
              <p class="text-3xl font-black text-purple-700 tracking-tight">24h</p>
              <h4 class="text-[9px] font-bold uppercase tracking-wider mt-1 text-purple-600/70">Rolling Window</h4>
            </div>
          </div>
        </div>
      </div>

      <!-- SLICK TABLE (Activity Logs Style) -->
      <div class="bg-white rounded-3xl border border-gray-100 overflow-hidden shadow-sm">

        <!-- BROADCASTS -->
        <div v-if="activeTab === 'broadcasts'">
          <div v-if="loadingBroadcasts" v-for="i in 5" :key="i"
            class="p-6 border-b border-gray-50 flex items-center justify-between animate-pulse">
            <div class="h-4 bg-gray-100 rounded-full w-1/3"></div>
            <div class="h-4 bg-gray-100 rounded-full w-1/4"></div>
          </div>

          <div v-else-if="broadcasts.length === 0" class="p-24 text-center">
            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest">No entries found</h3>
          </div>

          <table v-else class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-gray-50/50 border-b border-gray-100">
                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Title</th>
                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">
                  Priority</th>
                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Sender
                </th>
                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">Date
                </th>
                <th class="px-8 py-5 w-20"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="announcement in broadcasts" :key="announcement.id"
                class="group hover:bg-gray-50/50 transition-colors cursor-default">
                <td class="px-8 py-6">
                  <div class="flex flex-col">
                    <span class="text-xs font-bold text-gray-900 group-hover:text-agri-forest">{{ announcement.subject
                    }}</span>
                    <span class="text-[10px] text-gray-400 font-medium truncate max-w-xs mt-1">{{
                      stripHtml(announcement.message) }}</span>
                  </div>
                </td>
                <td class="px-8 py-6 text-center">
                  <span
                    :class="['px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-lg border', priorityClass(announcement.priority)]">{{
                      announcement.priority }}</span>
                </td>
                <td class="px-8 py-6 text-center text-xs font-bold text-gray-500">
                  {{ announcement.creator?.name || 'Admin' }}
                </td>
                <td class="px-8 py-6 text-right text-[10px] font-bold text-gray-400">
                  {{ formatDate(announcement.created_at) }}
                </td>
                <td class="px-8 py-6 text-right">
                  <router-link :to="`/admin/notifications/${announcement.id}/edit`"
                    class="p-2 text-gray-300 hover:text-agri-forest transition-colors inline-block">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
                    </svg>
                  </router-link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- AGRIPULSE -->
        <div v-else>
          <!-- Table Header with Filters -->
          <div
            class="px-8 py-6 border-b border-gray-50 flex flex-col md:flex-row items-center justify-between bg-white sticky top-0 z-20 gap-4">
            <div>
              <h2 class="text-xl font-bold text-gray-900 tracking-tight">AgriPulse Moderation Updates</h2>
              <p class="text-[11px] text-gray-400 font-semibold uppercase tracking-wider mt-1.5">Audit trail of
                community signals</p>
            </div>

            <div class="flex items-center gap-4 w-full md:w-auto">
              <!-- Search -->
              <div class="relative group flex-1 md:flex-none">
                <svg
                  class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-agri-forest transition-colors"
                  fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input v-model="searchAgriPulse" type="text" placeholder="Search user or subject..."
                  class="pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-agri-forest/10 focus:bg-white text-[10px] font-bold text-gray-700 w-full md:w-64 transition-all placeholder:font-medium placeholder:text-gray-400"
                  @input="debouncedFetch">
              </div>

              <!-- Priority Filter -->
              <select v-model="filterPriority"
                class="bg-gray-50 border border-gray-100 rounded-xl text-[10px] font-black uppercase tracking-widest text-gray-600 py-2.5 pl-4 pr-10 focus:ring-2 focus:ring-agri-forest/10"
                @change="fetchAgriPulseSignals(1)">
                <option value="">All Priorities</option>
                <option value="critical">Critical</option>
                <option value="high">High</option>
                <option value="normal">Normal</option>
                <option value="success">Success</option>
              </select>

              <button @click="fetchAgriPulseSignals(1)"
                class="p-2.5 text-gray-400 hover:text-agri-forest hover:bg-gray-50 rounded-xl transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                  </path>
                </svg>
              </button>
            </div>
          </div>

          <div v-if="loadingAgriPulse" v-for="i in 5" :key="i"
            class="p-6 border-b border-gray-50 flex items-center justify-between animate-pulse">
            <div class="h-4 bg-gray-100 rounded-full w-1/3"></div>
            <div class="h-4 bg-gray-100 rounded-full w-1/4"></div>
          </div>

          <div v-else-if="agriPulseSignals.length === 0" class="p-24 text-center">
            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest">No signals found</h3>
          </div>

          <table v-else class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-gray-50/50 border-b border-gray-100">
                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] w-1/3">Update Logic
                </th>
                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">
                  Priority</th>
                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Affected User</th>
                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">Time
                </th>
                <th class="px-8 py-5 w-20"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="signal in agriPulseSignals" :key="signal.id" @click="openDrawer(signal)"
                class="group hover:bg-gray-50/50 transition-colors cursor-pointer">
                <td class="px-8 py-6">
                  <div class="flex flex-col">
                    <span class="text-xs font-bold text-gray-900 leading-tight">{{ signal.data?.subject || 'Signal'
                    }}</span>
                    <span class="text-[9px] text-gray-400 font-black uppercase tracking-wider mt-1">LOGREF #{{
                      signal.id.substring(0, 8) }}</span>
                  </div>
                </td>
                <td class="px-8 py-6 text-center">
                  <span
                    :class="['px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-lg border', priorityClass(signal.data?.priority)]">{{
                      signal.data?.priority || 'Normal' }}</span>
                </td>
                <td class="px-8 py-6">
                  <div class="flex items-center space-x-3">
                    <div
                      class="w-8 h-8 rounded-full bg-agri-forest/5 flex items-center justify-center text-[10px] font-black text-agri-forest border border-agri-forest/10">
                      {{ signal.target_user_name?.charAt(0) }}</div>
                    <div class="flex flex-col">
                      <span class="text-[11px] font-black text-gray-900 leading-none mb-0.5">{{ signal.target_user_name
                      }}</span>
                      <span class="text-[9px] text-gray-400 font-bold lowercase tracking-tight">{{
                        signal.target_user_email }}</span>
                    </div>
                  </div>
                </td>
                <td class="px-8 py-6 text-right text-[10px] font-bold text-gray-400">
                  {{ formatDate(signal.created_at) }}
                </td>
                <td class="px-8 py-6 text-right">
                  <button class="p-2 text-gray-300 group-hover:text-agri-forest transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path d="M9 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
                    </svg>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- SLICK PAGINATION -->
        <div
          class="px-8 py-5 border-t border-gray-50 flex items-center justify-between bg-white text-[10px] font-black uppercase tracking-widest text-gray-400">
          <div>{{ activePagination.from || 0 }}-{{ activePagination.to || 0 }} / {{ activePagination.total || 0 }} ENTRY
            RECORDS</div>
          <div class="flex space-x-2">
            <button @click="fetchData(activePagination.current_page - 1)"
              :disabled="activePagination.current_page === 1"
              class="px-4 py-2 bg-white border border-gray-100 rounded-xl hover:border-agri-forest hover:text-agri-forest transition-all disabled:opacity-30">PREV</button>
            <button @click="fetchData(activePagination.current_page + 1)"
              :disabled="activePagination.current_page === activePagination.last_page"
              class="px-4 py-2 bg-white border border-gray-100 rounded-xl hover:border-agri-forest hover:text-agri-forest transition-all disabled:opacity-30">NEXT</button>
          </div>
        </div>
      </div>
    </div>

    <!-- ENTERPRISE DETAIL DRAWER (max-w-4xl) -->
    <transition enter-active-class="transform transition ease-in-out duration-500 sm:duration-700"
      enter-from-class="translate-x-full" enter-to-class="translate-x-0"
      leave-active-class="transform transition ease-in-out duration-500 sm:duration-700"
      leave-from-class="translate-x-0" leave-to-class="translate-x-full">
      <div v-if="showDrawer" class="fixed inset-0 z-[100] flex justify-end overflow-hidden" @click="showDrawer = false">
        <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity"></div>
        <div class="relative w-full max-w-4xl bg-white border-l border-gray-100 h-full flex flex-col shadow-2xl"
          @click.stop>

          <!-- Header (Logs Style) -->
          <div class="p-8 border-b border-gray-50 flex items-center justify-between shrink-0 bg-gray-50/50">
            <div>
              <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Update Dossier Detail</p>
              <h3 class="text-2xl font-black text-gray-900 tracking-tight">{{ selectedSignal?.data?.subject }}</h3>
            </div>
            <button @click="showDrawer = false"
              class="p-3 text-gray-400 hover:text-gray-900 hover:bg-white rounded-2xl transition-all border border-transparent hover:border-gray-100">
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
              </svg>
            </button>
          </div>

          <!-- Dual Column Content -->
          <div class="flex-1 overflow-y-auto p-8 space-y-10 custom-scrollbar">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
              <!-- Left Column: Intent & Message -->
              <div class="space-y-8">
                <div class="bg-white rounded-[2rem] border border-gray-100 p-8 shadow-sm space-y-4">
                  <h4 class="text-[10px] font-black text-agri-forest uppercase tracking-[0.2em]">Sent Signal Logic</h4>
                  <p class="text-sm text-gray-700 font-medium leading-relaxed"
                    v-html="parseMessage(selectedSignal?.data?.message)"></p>
                </div>

                <!-- Context Author (Logs Style) -->
                <div class="space-y-4">
                  <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Target Identity</h4>
                  <div class="bg-blue-50/50 rounded-2xl p-6 border border-blue-100/50 flex items-center gap-5">
                    <div
                      class="w-12 h-12 rounded-xl bg-white border border-blue-100 flex items-center justify-center text-xl font-black text-blue-600">
                      {{ selectedSignal?.target_user_name?.charAt(0) }}</div>
                    <div>
                      <p class="text-sm font-black text-gray-900">{{ selectedSignal?.target_user_name }}</p>
                      <p class="text-[10px] text-gray-500 font-bold tracking-tight">{{ selectedSignal?.target_user_email
                      }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Right Column: Referenced Evidence -->
              <div class="space-y-8">
                <div v-if="selectedSignal?.data?.content_details"
                  class="bg-gray-50/50 rounded-[2rem] border border-gray-100 p-8 space-y-6">
                  <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Content Reference</h4>

                  <div
                    v-if="selectedSignal.data.content_details.post_content || selectedSignal.data.content_details.comment_content"
                    class="relative pl-6">
                    <div class="absolute left-0 top-0 w-1 h-full bg-agri-forest shadow-sm rounded-full"></div>
                    <p class="text-sm font-serif italic text-gray-700 leading-relaxed">"{{
                      selectedSignal.data.content_details.post_content ||
                      selectedSignal.data.content_details.comment_content }}"</p>
                  </div>

                  <div v-if="selectedSignal.data.image_url"
                    class="rounded-2xl overflow-hidden border border-gray-100 shadow-sm">
                    <img :src="selectedSignal.data.image_url" class="w-full h-auto object-cover max-h-[250px]"
                      alt="Evidence media">
                  </div>

                  <!-- Engagement Snapshot -->
                  <div v-if="selectedSignal.data.content_details.metrics"
                    class="grid grid-cols-3 gap-6 pt-6 border-t border-gray-200/50">
                    <div class="text-center group-hover:scale-105 transition-transform">
                      <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Likes</p>
                      <p class="text-lg font-black text-blue-600">{{
                        selectedSignal.data.content_details.metrics.reactions }}</p>
                    </div>
                    <div class="text-center">
                      <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Coms</p>
                      <p class="text-lg font-black text-purple-600">{{
                        selectedSignal.data.content_details.metrics.comments }}</p>
                    </div>
                    <div class="text-center">
                      <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Rep</p>
                      <p class="text-lg font-black text-red-600">{{ selectedSignal.data.content_details.metrics.reports
                      }}</p>
                    </div>
                  </div>
                </div>

                <!-- Timestamp Meta -->
                <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-2">
                  <div class="flex justify-between items-baseline">
                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Logged At</span>
                    <span class="text-xs font-bold text-gray-900">{{ formatDate(selectedSignal?.created_at) }}</span>
                  </div>
                  <div class="flex justify-between items-baseline">
                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Signal Reference</span>
                    <span class="text-[10px] font-mono text-gray-500 font-bold uppercase">{{
                      selectedSignal?.id.split('-').pop() }}</span>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <!-- Footer -->
          <div class="p-8 border-t border-gray-50 bg-gray-50/30 flex justify-end shrink-0">
            <button @click="showDrawer = false"
              class="px-10 py-4 bg-gray-900 text-white rounded-[1.25rem] text-[10px] font-black uppercase tracking-[0.2em] hover:bg-black transition-all shadow-xl active:scale-95 leading-none">Close
              Update</button>
          </div>
        </div>
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';
import Notification from '../../../components/Notification.vue';

const notificationRef = ref(null);
const activeTab = ref(localStorage.getItem('admin_notif_tab') || 'broadcasts');

// Data
const broadcasts = ref([]);
const agriPulseSignals = ref([]);
const loadingBroadcasts = ref(false);
const loadingAgriPulse = ref(false);
const broadcastPagination = ref({});
const agriPulsePagination = ref({});

// Filters
const searchAgriPulse = ref('');
const filterPriority = ref('');

// UI
const showDrawer = ref(false);
const selectedSignal = ref(null);

const activePagination = computed(() => activeTab.value === 'broadcasts' ? broadcastPagination.value : agriPulsePagination.value);

const switchTab = (tab) => {
  activeTab.value = tab;
  localStorage.setItem('admin_notif_tab', tab);
  fetchData(1);
};

const fetchData = (page) => {
  if (activeTab.value === 'broadcasts') fetchAnnouncements(page);
  else fetchAgriPulseSignals(page);
};

const fetchAnnouncements = async (page = 1) => {
  loadingBroadcasts.value = true;
  try {
    const res = await axios.get(`/api/admin/notifications?page=${page}`);
    broadcasts.value = res.data.data;
    broadcastPagination.value = formatPagination(res.data);
  } catch (err) { console.error(err); } finally { loadingBroadcasts.value = false; }
};

const fetchAgriPulseSignals = async (page = 1) => {
  loadingAgriPulse.value = true;
  try {
    const params = {
      page,
      search: searchAgriPulse.value,
      priority: filterPriority.value
    };
    const res = await axios.get(`/api/admin/notifications/agripulse`, { params });
    agriPulseSignals.value = res.data.data;
    agriPulsePagination.value = formatPagination(res.data);
  } catch (err) { console.error(err); } finally { loadingAgriPulse.value = false; }
};

const debounce = (fn, delay) => {
  let timeoutId;
  return (...args) => {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => fn(...args), delay);
  };
};

const debouncedFetch = debounce(() => fetchAgriPulseSignals(1), 500);

const formatPagination = (data) => ({
  current_page: data.current_page,
  last_page: data.last_page,
  total: data.total,
  from: data.from,
  to: data.to
});

const openDrawer = (signal) => {
  selectedSignal.value = signal;
  showDrawer.value = true;
};

const priorityClass = (p) => {
  if (p === 'critical') return 'bg-red-400/10 text-red-700 border-red-200/50';
  if (p === 'high') return 'bg-orange-400/10 text-orange-700 border-orange-200/50';
  if (p === 'success') return 'bg-green-400/10 text-green-700 border-green-200/50';
  return 'bg-blue-400/10 text-blue-700 border-blue-200/50';
};

const criticalBroadcastCount = computed(() => broadcasts.value.filter(a => a.priority === 'critical').length);
const criticalSignalCount = computed(() => agriPulseSignals.value.filter(s => s.data?.priority === 'critical').length);
const lastBroadcastDate = computed(() => broadcasts.value.length ? new Date(broadcasts.value[0].created_at).toLocaleDateString(undefined, { month: 'short', day: 'numeric' }) : 'N/A');

const formatDate = (d) => new Date(d).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
const stripHtml = (h) => h ? h.replace(/<[^>]*>?/gm, '') : '';
const parseMessage = (m) => m ? m.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" class="text-agri-forest font-bold hover:underline" target="_blank">$1</a>') : '';

onMounted(() => fetchData(1));
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: rgba(0, 0, 0, 0.05);
  border-radius: 20px;
}
</style>
