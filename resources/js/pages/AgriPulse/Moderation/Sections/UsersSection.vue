<template>
  <div class="space-y-6">
    <!-- Premium Metric Cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div v-for="stat in userStats" :key="stat.label"
        class="bg-gradient-to-br backdrop-blur-md p-5 rounded-3xl border transition-all duration-500 group cursor-default relative overflow-hidden hover:-translate-y-1"
        :class="[stat.bgClass, stat.borderClass]">
        <div
          class="absolute -top-10 -right-10 w-24 h-24 rounded-full blur-xl transition-all duration-700 group-hover:scale-110"
          :class="stat.glowClass">
        </div>

        <div class="relative z-10 flex flex-col items-start space-y-3">
          <div
            class="p-2.5 rounded-xl border border-white/20 transition-all duration-700 shadow-lg group-hover:scale-105"
            :class="stat.iconBg">
            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" :d="stat.iconPath"></path>
            </svg>
          </div>
          <div>
            <p class="text-2xl font-black tracking-tight group-hover:scale-[1.05] transition-transform duration-500 origin-left"
              :class="stat.textColor">
              {{ stat.value }}
            </p>
            <h4 class="text-[9px] font-bold uppercase tracking-wider mt-1" :class="stat.labelColor">{{ stat.label }}
            </h4>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-3xl border border-gray-100 overflow-hidden flex flex-col h-full relative">
      <!-- Header -->
      <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-white sticky top-0 z-20">
        <div>
          <h2 class="text-xl font-bold text-gray-900 tracking-tight">User Registry</h2>
          <p class="text-[11px] text-gray-400 font-semibold uppercase tracking-wider">Manage platform community
          </p>
        </div>
        <div class="flex items-center gap-4">
          <div class="relative group">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
              stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input v-model="search" type="text" placeholder="Search by name, email..."
              class="pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-agri-forest/10 focus:bg-white text-xs font-medium w-64 transition-all"
              @input="debouncedFetch">
          </div>
          <button @click="fetchUsers"
            class="p-2.5 text-gray-400 hover:text-agri-forest hover:bg-gray-50 rounded-xl transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
              </path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Table -->
      <div class="flex-1 overflow-auto">
        <table class="w-full text-left border-collapse min-w-[1000px]">
          <thead>
            <tr class="bg-white border-b border-gray-50 sticky top-0 z-10">
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] w-24">ID</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">User</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Posts
              </th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Reports
              </th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Actions
              </th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">Joined
              </th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right w-20">
                Manage
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-if="loading" v-for="i in 10" :key="i" class="animate-pulse">
              <td colspan="7" class="px-8 py-6">
                <div class="h-4 bg-gray-100 rounded-full w-full"></div>
              </td>
            </tr>
            <tr v-else v-for="user in users" :key="user.id" @click="viewDetails(user)"
              class="group hover:bg-gray-50/50 transition-colors cursor-pointer">
              <td class="px-8 py-5">
                <span class="font-mono text-[10px] text-gray-400 font-bold">#{{ user.id }}</span>
              </td>
              <td class="px-8 py-5">
                <div class="flex items-center gap-3">
                  <div
                    class="w-8 h-8 rounded-xl bg-agri-sand flex items-center justify-center text-[10px] font-black text-agri-forest">
                    {{ user.name[0] }}
                  </div>
                  <div class="flex flex-col">
                    <span class="text-xs font-bold text-gray-900 leading-none">{{ user.name }}</span>
                    <span class="text-[9px] text-gray-400 mt-1">{{ user.email }}</span>
                  </div>
                </div>
              </td>
              <td class="px-8 py-5 text-center">
                <span class="text-xs font-bold text-gray-900">{{ user.posts_count || 0 }}</span>
              </td>
              <td class="px-8 py-5 text-center">
                <span class="text-xs font-bold"
                  :class="user.received_reports_count > 0 ? 'text-red-600' : 'text-gray-400'">
                  {{ user.received_reports_count || 0 }}
                </span>
              </td>
              <td class="px-8 py-5 text-center">
                <span class="text-xs font-bold"
                  :class="user.moderation_actions_count > 0 ? 'text-orange-600' : 'text-gray-400'">
                  {{ user.moderation_actions_count || 0 }}
                </span>
              </td>
              <td class="px-8 py-5 text-right text-[10px] font-bold text-gray-400">
                {{ dayjs(user.created_at).format('MMM D, YYYY') }}
              </td>
              <td class="px-8 py-5 text-right relative" @click.stop>
                <button @click="toggleMenu(user.id)"
                  class="p-2 text-gray-400 hover:text-gray-900 h-8 w-8 flex items-center justify-center rounded-lg bg-gray-50 border-gray-100 hover:border-gray-300 border transition-all">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path
                      d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                  </svg>
                </button>

                <!-- Dropdown Menu -->
                <div v-if="openMenuId === user.id"
                  class="absolute right-8 top-12 w-48 bg-white rounded-2xl border border-gray-100 overflow-hidden z-20 animate-in fade-in slide-in-from-top-2"
                  v-click-outside="() => openMenuId = null">
                  <div class="p-1.5 space-y-0.5 text-left">
                    <button @click="viewDetails(user)"
                      class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-left text-xs font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                      <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                        </path>
                      </svg>
                      View details
                    </button>
                    <div class="h-px bg-gray-50 my-1 mx-2"></div>
                    <button @click="openActionModal(user, 'warning')"
                      class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-left text-xs font-bold text-orange-600 hover:bg-orange-50 transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                      </svg>
                      Warn user
                    </button>
                    <button @click="openActionModal(user, 'suspension')"
                      class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-left text-xs font-bold text-red-600 hover:bg-red-50 transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636">
                        </path>
                      </svg>
                      Suspend user
                    </button>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div
        class="px-8 py-4 border-t border-gray-50 flex items-center justify-between bg-white text-[10px] font-black uppercase tracking-widest text-gray-400"
        v-if="pagination.links">
        <div>{{ pagination.from || 0 }}-{{ pagination.to || 0 }} / {{ pagination.total || 0 }}</div>
        <div class="flex items-center gap-1">
          <button v-for="link in pagination.links" :key="link.label" @click="fetchUsers(link.url)" v-html="link.label"
            class="min-w-[32px] h-8 flex items-center justify-center rounded-lg transition-all border" :class="link.active
              ? 'bg-agri-forest text-white border-agri-forest'
              : 'bg-white text-gray-400 border-gray-100 hover:text-agri-forest hover:border-agri-forest'"
            :disabled="!link.url"></button>
        </div>
      </div>

      <!-- User Detail Slide-over (EXPANDED) -->
      <div v-if="selectedUser" class="fixed inset-0 z-[100] flex justify-end overflow-hidden"
        @click="selectedUser = null">
        <div class="absolute inset-0 bg-black/20 backdrop-blur-sm transition-opacity"></div>

        <main class="relative w-full max-w-6xl bg-white border-l border-gray-100 h-full flex flex-col animate-slide-in"
          @click.stop>
          <!-- Header -->
          <div class="p-8 border-b border-gray-50 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-4">
              <div
                class="w-16 h-16 rounded-2xl bg-agri-sand flex items-center justify-center text-2xl font-black text-agri-forest">
                {{ selectedUser.name[0] }}
              </div>
              <div>
                <h3 class="text-2xl font-bold text-gray-900 leading-tight">{{ selectedUser.name }}</h3>
                <p
                  class="text-[11px] font-semibold text-gray-400 uppercase tracking-[0.1em] mt-1.5 flex items-center gap-2">
                  <span class="bg-gray-100 px-2 py-0.5 rounded text-gray-600">ID: #{{ selectedUser.id }}</span>
                  <span>&bull;</span>
                  <span>{{ selectedUser.email }}</span>
                </p>
              </div>
            </div>
            <button @click="selectedUser = null"
              class="p-3 text-gray-400 hover:text-gray-900 hover:bg-gray-100 rounded-2xl transition-all">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <!-- Content -->
          <div class="flex-1 overflow-y-auto p-8 space-y-8 custom-scrollbar">
            <!-- Account Overview -->
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
              <div class="p-6 bg-white border border-gray-100 rounded-3xl text-center">
                <p class="text-3xl font-black text-gray-900">{{ selectedUser.posts_count || 0 }}</p>
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-2">Total Posts</p>
              </div>
              <div class="p-6 bg-blue-50 border border-blue-100 rounded-3xl text-center">
                <p class="text-3xl font-black text-blue-600">{{ profileData.engagement?.total_reactions || 0 }}</p>
                <p class="text-[9px] font-black text-blue-400 uppercase tracking-widest mt-2">Total Likes</p>
              </div>
              <div class="p-6 bg-purple-50 border border-purple-100 rounded-3xl text-center">
                <p class="text-3xl font-black text-purple-600">{{ profileData.engagement?.total_comments || 0 }}</p>
                <p class="text-[9px] font-black text-purple-400 uppercase tracking-widest mt-2">Comments Received</p>
              </div>
              <div class="p-6 bg-red-50 border border-red-100 rounded-3xl text-center">
                <p class="text-3xl font-black text-red-600">{{ history.reports.length }}</p>
                <p class="text-[9px] font-black text-red-400 uppercase tracking-widest mt-2">Reports</p>
              </div>
              <div class="p-6 bg-gray-50 border border-gray-100 rounded-3xl text-center">
                <p class="text-sm font-black text-gray-600">{{ dayjs(selectedUser.created_at).format('MMM D, YYYY') }}
                </p>
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-2">Joined</p>
              </div>
            </div>

            <!-- Engagement Analytics -->
            <div class="space-y-4">
              <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Engagement Analytics</h4>
              <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="p-6 bg-white border border-gray-100 rounded-3xl">
                  <div class="flex items-center justify-between mb-4">
                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Avg Likes/Post</span>
                    <span class="text-2xl font-black text-blue-600">{{ profileData.engagement?.avg_reactions_per_post ||
                      0 }}</span>
                  </div>
                  <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500 transition-all"
                      :style="{ width: `${Math.min((profileData.engagement?.avg_reactions_per_post || 0) * 10, 100)}%` }">
                    </div>
                  </div>
                </div>
                <div class="p-6 bg-white border border-gray-100 rounded-3xl">
                  <div class="flex items-center justify-between mb-4">
                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Avg Comments/Post</span>
                    <span class="text-2xl font-black text-purple-600">{{ profileData.engagement?.avg_comments_per_post
                      || 0 }}</span>
                  </div>
                  <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-purple-500 transition-all"
                      :style="{ width: `${Math.min((profileData.engagement?.avg_comments_per_post || 0) * 10, 100)}%` }">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Post Type Breakdown -->
            <div class="space-y-4">
              <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Content Breakdown</h4>
              <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-5 bg-white border border-gray-100 rounded-3xl text-center">
                  <p class="text-2xl font-black text-gray-900">{{ profileData.post_breakdown?.with_images || 0 }}</p>
                  <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1">Image Posts</p>
                </div>
                <div class="p-5 bg-white border border-gray-100 rounded-3xl text-center">
                  <p class="text-2xl font-black text-gray-900">{{ profileData.post_breakdown?.with_products || 0 }}</p>
                  <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1">Product Posts</p>
                </div>
                <div class="p-5 bg-white border border-gray-100 rounded-3xl text-center">
                  <p class="text-2xl font-black text-gray-900">{{ profileData.post_breakdown?.text_only || 0 }}</p>
                  <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1">Text Only</p>
                </div>
                <div class="p-5 bg-white border border-gray-100 rounded-3xl text-center">
                  <p class="text-2xl font-black text-gray-900">{{ profileData.post_breakdown?.total || 0 }}</p>
                  <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1">Total</p>
                </div>
              </div>
            </div>

            <!-- Post Status Breakdown -->
            <div class="space-y-4">
              <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Content Status</h4>
              <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-5 bg-green-50 border border-green-100 rounded-3xl text-center">
                  <p class="text-2xl font-black text-green-600">{{ profileData.status_breakdown?.published || 0 }}</p>
                  <p class="text-[9px] font-black text-green-500 uppercase tracking-widest mt-1">Published</p>
                </div>
                <div class="p-5 bg-orange-50 border border-orange-100 rounded-3xl text-center">
                  <p class="text-2xl font-black text-orange-600">{{ profileData.status_breakdown?.hidden || 0 }}</p>
                  <p class="text-[9px] font-black text-orange-500 uppercase tracking-widest mt-1">Hidden</p>
                </div>
                <div class="p-5 bg-purple-50 border border-purple-100 rounded-3xl text-center">
                  <p class="text-2xl font-black text-purple-600">{{ profileData.status_breakdown?.flagged || 0 }}</p>
                  <p class="text-[9px] font-black text-purple-500 uppercase tracking-widest mt-1">Flagged</p>
                </div>
                <div class="p-5 bg-gray-100 border border-gray-200 rounded-3xl text-center">
                  <p class="text-2xl font-black text-gray-600">{{ profileData.status_breakdown?.deleted || 0 }}</p>
                  <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest mt-1">Deleted</p>
                </div>
              </div>
            </div>

            <!-- Top Posts -->
            <div class="space-y-4" v-if="profileData.posts && profileData.posts.length > 0">
              <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Top Posts by Engagement</h4>
              <div class="bg-white rounded-3xl border border-gray-50 overflow-hidden">
                <div class="divide-y divide-gray-50">
                  <div v-for="post in profileData.posts.slice(0, 5)" :key="post.id"
                    class="p-4 hover:bg-gray-50/50 transition-colors">
                    <div class="flex items-start gap-3">
                      <div class="flex-1">
                        <p class="text-xs text-gray-700 italic mb-2 line-clamp-2">"{{ post.content || 'Image/Product post' }}"</p>
                        <div class="flex items-center gap-4 text-[9px] font-bold text-gray-400">
                          <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                              <path
                                d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z" />
                            </svg>
                            {{ post.reactions_count || 0 }}
                          </span>
                          <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                              <path fill-rule="evenodd"
                                d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z"
                                clip-rule="evenodd" />
                            </svg>
                            {{ post.comments_count || 0 }}
                          </span>
                          <span class="flex items-center gap-1 text-red-500" v-if="post.reports_count > 0">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                              <path fill-rule="evenodd"
                                d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z"
                                clip-rule="evenodd" />
                            </svg>
                            {{ post.reports_count }}
                          </span>
                        </div>
                      </div>
                      <span class="text-[9px] font-bold text-gray-400 uppercase">{{ dayjs(post.created_at).fromNow()
                      }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Recent Activity -->
            <div class="space-y-4">
              <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Recent Activity</h4>
              <div class="bg-white rounded-3xl border border-gray-50 overflow-hidden">
                <div class="divide-y divide-gray-50">
                  <div v-for="item in recentActivity" :key="item.id" class="p-4 hover:bg-gray-50/50 transition-colors">
                    <div class="flex items-start gap-3">
                      <div :class="item.type === 'post' ? 'bg-blue-100 text-blue-600' : 'bg-purple-100 text-purple-600'"
                        class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0">
                        <svg v-if="item.type === 'post'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                          <path
                            d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" />
                        </svg>
                        <svg v-else class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd"
                            d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z"
                            clip-rule="evenodd" />
                        </svg>
                      </div>
                      <div class="flex-1">
                        <p class="text-xs font-bold text-gray-900 mb-1">
                          {{ item.type === 'post' ? 'Posted' : 'Commented on post' }}
                        </p>
                        <p class="text-xs text-gray-600 italic line-clamp-2">"{{ item.content }}"</p>
                        <span class="text-[9px] font-bold text-gray-400 uppercase mt-2 inline-block">
                          {{ dayjs(item.created_at).fromNow() }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Moderation History -->
            <div class="space-y-4">
              <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Moderation History</h4>
              <div class="p-6 bg-orange-50 border border-orange-100 rounded-3xl text-center"
                v-if="history.actions.length === 0">
                <p class="text-sm font-bold text-orange-600">No moderation actions on record</p>
                <p class="text-[9px] font-bold text-orange-400 uppercase tracking-widest mt-1">This user has a clean
                  record</p>
              </div>
              <div v-else class="bg-white rounded-3xl border border-gray-50 overflow-hidden">
                <table class="w-full text-left">
                  <thead class="bg-gray-50">
                    <tr class="text-[9px] font-black text-gray-400 uppercase tracking-widest">
                      <th class="px-6 py-3">Action</th>
                      <th class="px-6 py-3">Reason</th>
                      <th class="px-6 py-3 text-right">Moderator</th>
                      <th class="px-6 py-3 text-right">Date</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-50">
                    <tr v-for="action in history.actions" :key="action.id">
                      <td class="px-6 py-4">
                        <span class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-widest border"
                          :class="action.action_type === 'suspension' ? 'bg-red-50 text-red-600' : 'bg-orange-50 text-orange-600'">
                          {{ action.action_type }}
                        </span>
                      </td>
                      <td class="px-6 py-4 text-xs text-gray-600 max-w-xs truncate">{{ action.reason }}</td>
                      <td class="px-6 py-4 text-right text-xs font-bold text-gray-900">{{ action.moderator?.name }}</td>
                      <td class="px-6 py-4 text-right text-[10px] text-gray-400 font-bold">{{
                        dayjs(action.created_at).format('MMM D, YY') }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Reports Against User -->
            <div class="space-y-4">
              <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Reports Against User</h4>
              <div class="p-6 bg-green-50 border border-green-100 rounded-3xl text-center"
                v-if="history.reports.length === 0">
                <p class="text-sm font-bold text-green-600">No active reports</p>
                <p class="text-[9px] font-bold text-green-400 uppercase tracking-widest mt-1">This user has not been
                  reported</p>
              </div>
              <div v-else class="bg-white rounded-3xl border border-gray-50 overflow-hidden">
                <table class="w-full text-left">
                  <thead class="bg-gray-50">
                    <tr class="text-[9px] font-black text-gray-400 uppercase tracking-widest">
                      <th class="px-6 py-3">Reason</th>
                      <th class="px-6 py-3">Content excerpt</th>
                      <th class="px-6 py-3 text-right">Status</th>
                      <th class="px-6 py-3 text-right">Date</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-50">
                    <tr v-for="report in history.reports" :key="report.id">
                      <td class="px-6 py-4">
                        <span class="text-[10px] font-black uppercase tracking-tight text-red-500">{{ report.reason
                        }}</span>
                      </td>
                      <td class="px-6 py-4 text-xs text-gray-500 italic max-w-sm truncate">{{ report.post?.content ||
                        'Non-textual content' }}</td>
                      <td class="px-6 py-4 text-right">
                        <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded-lg border"
                          :class="report.status === 'pending' ? 'bg-yellow-50 text-yellow-600' : 'bg-green-50 text-green-600'">{{
                            report.status }}</span>
                      </td>
                      <td class="px-6 py-4 text-right text-[10px] text-gray-400 font-bold">{{
                        dayjs(report.created_at).format('MMM D, YY') }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Footer Actions -->
          <div class="p-8 border-t border-gray-50 bg-gray-50/30 flex items-center justify-end gap-3 shrink-0">
            <button @click="openActionModal(selectedUser, 'warning')"
              class="h-12 px-6 bg-orange-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-orange-700 transition-colors">
              Warn User
            </button>
            <button @click="openActionModal(selectedUser, 'suspension')"
              class="h-12 px-6 bg-red-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-red-700 transition-colors">
              Suspend User
            </button>
          </div>
        </main>
      </div>

      <!-- Governance Modal -->
      <Modal :is-open="modal.open" :title="`Protocol: ${modal.type}`"
        :type="modal.type === 'suspension' ? 'danger' : 'primary'" confirm-text="Execute"
        @confirm="submitModerationAction" @cancel="modal.open = false" :loading="modal.loading">
        <div class="space-y-6">
          <div>
            <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Reason for
              action</label>
            <textarea v-model="modal.reason"
              class="w-full p-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-agri-forest/20 text-sm font-medium h-32 resize-none"
              placeholder="Describe why you are taking this action..."></textarea>
          </div>

          <div v-if="modal.type === 'suspension'">
            <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Expires at
              (Optional)</label>
            <input v-model="modal.expires_at" type="datetime-local"
              class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-agri-forest/20 text-sm font-medium">
          </div>
        </div>
      </Modal>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import Modal from '@/components/Modal.vue'

dayjs.extend(relativeTime)

const props = defineProps({
  globalStats: {
    type: Object,
    default: () => ({})
  }
})

const userStats = computed(() => [
  {
    label: 'Total Users',
    value: props.globalStats.total_users || 0,
    iconPath: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
    bgClass: 'from-blue-400/10 to-blue-500/5',
    borderClass: 'border-blue-200/30',
    glowClass: 'bg-blue-500/10',
    iconBg: 'bg-blue-500',
    textColor: 'text-blue-700',
    labelColor: 'text-blue-600/70'
  },
  {
    label: 'Active Now',
    value: props.globalStats.active_users || 0,
    iconPath: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    bgClass: 'from-green-400/10 to-green-500/5',
    borderClass: 'border-green-200/30',
    glowClass: 'bg-green-500/10',
    iconBg: 'bg-green-500',
    textColor: 'text-green-700',
    labelColor: 'text-green-600/70'
  },
  {
    label: 'Suspended',
    value: props.globalStats.suspended_users || 0,
    iconPath: 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636',
    bgClass: 'from-red-400/10 to-red-500/5',
    borderClass: 'border-red-200/30',
    glowClass: 'bg-red-500/10',
    iconBg: 'bg-red-500',
    textColor: 'text-red-700',
    labelColor: 'text-red-600/70'
  },
  {
    label: 'Warned',
    value: props.globalStats.warned_users || 0,
    iconPath: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
    bgClass: 'from-orange-400/10 to-orange-500/5',
    borderClass: 'border-orange-200/30',
    glowClass: 'bg-orange-500/10',
    iconBg: 'bg-orange-500',
    textColor: 'text-orange-700',
    labelColor: 'text-orange-600/70'
  }
])

const vClickOutside = {
  mounted(el, binding) {
    el.clickOutsideEvent = (event) => {
      if (!(el === event.target || el.contains(event.target))) {
        binding.value(event);
      }
    };
    document.addEventListener('click', el.clickOutsideEvent);
  },
  unmounted(el) {
    document.removeEventListener('click', el.clickOutsideEvent);
  },
};

const debounce = (fn, delay) => {
  let timeoutId;
  return (...args) => {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => fn(...args), delay);
  };
};

const loading = ref(true)
const users = ref([])
const search = ref('')
const pagination = ref({})
const openMenuId = ref(null)
const selectedUser = ref(null)
const history = ref({ actions: [], reports: [] })
const profileData = ref({
  engagement: null,
  post_breakdown: null,
  status_breakdown: null,
  posts: [],
  recent_posts: [],
  recent_comments: []
})

const modal = ref({
  open: false,
  type: 'warning',
  reason: '',
  expires_at: '',
  loading: false
})

const recentActivity = computed(() => {
  const posts = (profileData.value.recent_posts || []).map(p => ({ ...p, type: 'post' }))
  const comments = (profileData.value.recent_comments || []).map(c => ({ ...c, type: 'comment' }))
  return [...posts, ...comments]
    .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
    .slice(0, 10)
})

const fetchUsers = async (url) => {
  if (typeof url !== 'string') {
    url = '/api/agripulse/moderation/users'
  }
  loading.value = true
  openMenuId.value = null
  try {
    const response = await axios.get(url, { params: { search: search.value } })
    users.value = response.data.data
    pagination.value = { from: response.data.from, to: response.data.to, total: response.data.total, links: response.data.links }
  } catch (error) {
    console.error('Failed to fetch users:', error)
  } finally {
    loading.value = false
  }
}

const debouncedFetch = debounce(() => fetchUsers(), 500)

const toggleMenu = (id) => {
  openMenuId.value = openMenuId.value === id ? null : id
}

const viewDetails = async (user) => {
  selectedUser.value = user
  openMenuId.value = null
  await fetchUserHistory(user.id)
}

const fetchUserHistory = async (userId) => {
  try {
    const response = await axios.get(`/api/agripulse/moderation/users/${userId}/history`)
    history.value = {
      actions: response.data.actions,
      reports: response.data.reports
    }
    profileData.value = {
      engagement: response.data.engagement,
      post_breakdown: response.data.post_breakdown,
      status_breakdown: response.data.status_breakdown,
      posts: response.data.posts,
      recent_posts: response.data.recent_posts,
      recent_comments: response.data.recent_comments
    }
  } catch (error) {
    console.error('Failed to fetch history:', error)
  }
}

const openActionModal = (user, type) => {
  modal.value = { open: true, type, reason: '', expires_at: '', loading: false, targetUser: user }
  openMenuId.value = null
}

const submitModerationAction = async () => {
  if (!modal.value.reason) return alert('Please provide a reason.')

  modal.value.loading = true
  try {
    await axios.post(`/api/agripulse/moderation/users/${modal.value.targetUser.id}/${modal.value.type}`, {
      reason: modal.value.reason,
      expires_at: modal.value.expires_at
    })
    modal.value.open = false

    if (window.notify) {
      window.notify.success('Action Executed', `${modal.value.type} protocol successfully applied to user.`)
    }

    if (selectedUser.value && selectedUser.value.id === modal.value.targetUser.id) {
      await fetchUserHistory(selectedUser.value.id)
    }

    fetchUsers()
  } catch (error) {
    if (window.notify) {
      window.notify.error('Execution Failed', 'Moderation signal was not accepted.')
    } else {
      alert('Action failed.')
    }
  } finally {
    modal.value.loading = false
  }
}

onMounted(() => fetchUsers())
</script>

<style scoped>
.animate-in {
  animation: fade-in 0.2s ease-out forwards;
}

@keyframes fade-in {
  from {
    opacity: 0;
  }

  to {
    opacity: 1;
  }
}

@keyframes slide-in-from-top-2 {
  from {
    transform: translateY(-0.5rem);
  }

  to {
    transform: translateY(0);
  }
}

.animate-slide-in {
  animation: slide-in 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes slide-in {
  from {
    transform: translateX(100%);
  }

  to {
    transform: translateX(0);
  }
}

.fade-in {
  animation-name: fade-in;
}

.slide-in-from-top-2 {
  animation-name: slide-in-from-top-2;
}

.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: rgba(200, 200, 200, 0.3);
  border-radius: 20px;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
