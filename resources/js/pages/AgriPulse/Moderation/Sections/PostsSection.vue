<template>
  <div class="space-y-6">
    <!-- Premium Metric Cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
      <div v-for="stat in postStats" :key="stat.label"
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
          <h2 class="text-xl font-bold text-gray-900 tracking-tight">Post Inventory</h2>
          <p class="text-[11px] text-gray-500 font-medium uppercase tracking-wider">Detailed management and visibility
          </p>
        </div>
        <div class="flex items-center gap-4">
          <div class="relative group">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
              stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input v-model="filters.search" type="text" placeholder="Search content or author..."
              class="pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-agri-forest/10 focus:bg-white text-xs font-medium w-64 transition-all"
              @input="debouncedFetch">
          </div>
          <button @click="fetchPosts"
            class="p-2.5 text-gray-400 hover:text-agri-forest hover:bg-gray-50 rounded-xl transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
              </path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Filters -->
      <div class="px-8 py-4 bg-gray-50/50 border-b border-gray-50 flex items-center gap-6">
        <div class="flex items-center gap-2">
          <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Status:</span>
          <select v-model="filters.status" @change="fetchPosts"
            class="bg-transparent border-none text-[10px] font-black text-gray-600 uppercase tracking-widest focus:ring-0 cursor-pointer p-0">
            <option value="">All</option>
            <option value="published">Published</option>
            <option value="hidden">Hidden</option>
            <option value="flagged">Flagged</option>
          </select>
        </div>
        <div class="w-px h-4 bg-gray-200"></div>
        <label class="flex items-center gap-2 cursor-pointer group">
          <input v-model="filters.deleted" type="checkbox" @change="fetchPosts"
            class="w-3.5 h-3.5 rounded border-gray-300 text-agri-forest focus:ring-agri-forest/20">
          <span
            class="text-[10px] font-black text-gray-400 uppercase tracking-widest group-hover:text-gray-600 transition-colors">Include
            Trashed</span>
        </label>
      </div>

      <!-- Table -->
      <div class="flex-1 overflow-auto">
        <table class="w-full text-left border-collapse min-w-[1000px]">
          <thead>
            <tr class="bg-white border-b border-gray-50 sticky top-0 z-10">
              <th class="px-8 py-4 w-16">
                <input type="checkbox" :checked="posts.length > 0 && selectedIds.length === posts.length"
                  @change="toggleSelectAll"
                  class="w-4 h-4 rounded border-gray-300 text-agri-forest focus:ring-agri-forest cursor-pointer">
              </th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] w-24">ID</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Post Content</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Author</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Status
              </th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">Date</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right w-20">
                Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-if="loading" v-for="i in 10" :key="i" class="animate-pulse">
              <td colspan="7" class="px-8 py-6">
                <div class="h-4 bg-gray-100 rounded-full w-full"></div>
              </td>
            </tr>
            <tr v-else-if="posts.length === 0">
              <td colspan="7" class="px-8 py-12 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">
                No
                posts found matching your criteria</td>
            </tr>
            <tr v-else v-for="post in posts" :key="post.id" @click="viewDetails(post)"
              class="group hover:bg-gray-50/50 transition-colors cursor-pointer">
              <td class="px-8 py-5" @click.stop>
                <input type="checkbox" v-model="selectedIds" :value="post.id"
                  class="w-4 h-4 rounded border-gray-300 text-agri-forest focus:ring-agri-forest cursor-pointer">
              </td>
              <td class="px-8 py-5">
                <span class="font-mono text-[10px] text-gray-400 font-bold">#{{ post.id }}</span>
              </td>
              <td class="px-8 py-5">
                <div class="flex items-center gap-3">
                  <div v-if="post.is_pinned" class="shrink-0 text-agri-forest">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M16 12V4H17V2H7V4H8V12L6 14V16H11.2V22H12.8V16H18V14L16 12Z" />
                    </svg>
                  </div>
                  <p class="text-sm text-gray-600 font-medium truncate max-w-md">
                    {{ post.content || 'Image/Product post' }}
                    <span v-if="post.image_url"
                      class="ml-2 inline-flex items-center text-[10px] text-blue-500 font-black uppercase tracking-tighter">[Image]</span>
                  </p>
                </div>
              </td>
              <td class="px-8 py-5">
                <div class="flex items-center gap-2">
                  <img :src="post.user.avatar_url" :alt="post.user.name" class="w-6 h-6 rounded-lg object-cover">
                  <span class="text-xs font-bold text-gray-900">{{ post.user.name }}</span>
                </div>
              </td>
              <td class="px-8 py-5 text-center">
                <span class="inline-flex px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border"
                  :class="statusClasses(post)">
                  {{ post.deleted_at ? 'Trashed' : (post.status || 'Live') }}
                </span>
              </td>
              <td class="px-8 py-5 text-right text-[11px] font-bold text-gray-400">
                {{ dayjs(post.created_at).format('MMM D, YYYY') }}
              </td>
              <td class="px-8 py-5 text-right relative" @click.stop>
                <button @click="toggleMenu(post.id)"
                  class="p-2 text-gray-400 hover:text-gray-900 h-8 w-8 flex items-center justify-center rounded-lg bg-gray-50 border-gray-100 hover:border-gray-300 border transition-all">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path
                      d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                  </svg>
                </button>

                <!-- Dropdown Menu -->
                <div v-if="openMenuId === post.id"
                  class="absolute right-8 top-12 w-48 bg-white rounded-2xl border border-gray-100 overflow-hidden z-20 animate-in fade-in slide-in-from-top-2"
                  v-click-outside="() => openMenuId = null">
                  <div class="p-1.5 space-y-0.5">
                    <button @click="viewDetails(post)"
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
                    <button @click="initAction(post, 'pin')"
                      class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-left text-xs font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                      <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                      </svg>
                      {{ post.is_pinned ? 'Unpin post' : 'Pin post' }}
                    </button>
                    <div class="h-px bg-gray-50 my-1 mx-2"></div>
                    <button v-if="post.status !== 'hidden' && !post.deleted_at" @click="initAction(post, 'hide')"
                      class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-left text-xs font-bold text-orange-600 hover:bg-orange-50 transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.046m4.596-4.596A10.001 10.001 0 0112 4.5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21l-3-3m-3-3l-3-3m2-2a3 3 0 11-4.243 4.243M9.878 9.878L12 12">
                        </path>
                      </svg>
                      Hide post
                    </button>
                    <button v-if="post.status === 'hidden' || post.deleted_at" @click="initAction(post, 'restore')"
                      class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-left text-xs font-bold text-green-600 hover:bg-green-50 transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                        </path>
                      </svg>
                      Restore post
                    </button>
                    <button v-if="post.status !== 'flagged' && !post.deleted_at" @click="initAction(post, 'flag')"
                      class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-left text-xs font-bold text-purple-600 hover:bg-purple-50 transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-7h1"></path>
                      </svg>
                      Flag post
                    </button>
                    <button v-if="!post.deleted_at" @click="initAction(post, 'delete')"
                      class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-left text-xs font-bold text-red-600 hover:bg-red-50 transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                      </svg>
                      Delete post
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
        class="px-8 py-4 border-t border-gray-50 flex items-center justify-between bg-white text-[10px] font-black uppercase tracking-widest text-gray-400 sticky bottom-0 z-10">
        <div>{{ pagination.from || 0 }}-{{ pagination.to || 0 }} of {{ pagination.total || 0 }}</div>
        <div class="flex items-center gap-1">
          <button v-for="link in pagination.links" :key="link.label" @click="fetchPosts(link.url)" v-html="link.label"
            class="min-w-[32px] h-8 flex items-center justify-center rounded-lg transition-all border" :class="link.active
              ? 'bg-agri-forest text-white border-agri-forest'
              : 'bg-white text-gray-400 border-gray-100 hover:text-agri-forest hover:border-agri-forest'"
            :disabled="!link.url"></button>
        </div>
      </div>

      <!-- Post Detail Slide-over (EXPANDED) -->
      <div v-if="selectedPost" class="fixed inset-0 z-[100] flex justify-end overflow-hidden"
        @click="selectedPost = null">
        <div class="absolute inset-0 bg-black/20 backdrop-blur-sm transition-opacity"></div>

        <main class="relative w-full max-w-5xl bg-white border-l border-gray-100 h-full flex flex-col animate-slide-in"
          @click.stop>
          <div class="p-8 border-b border-gray-50 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-4">
              <img :src="selectedPost.user.avatar_url" :alt="selectedPost.user.name"
                class="w-16 h-16 rounded-2xl object-cover shadow-sm bg-gray-50">
              <div>
                <h3 class="text-2xl font-bold text-gray-900 leading-tight">Post #{{ selectedPost.id }}</h3>
                <p
                  class="text-[11px] font-semibold text-gray-400 uppercase tracking-[0.1em] mt-1.5 flex items-center gap-2">
                  <span>By {{ selectedPost.user.name }}</span>
                  <span>&bull;</span>
                  <span>{{ dayjs(selectedPost.created_at).fromNow() }}</span>
                </p>
              </div>
            </div>
            <button @click="selectedPost = null"
              class="p-3 text-gray-400 hover:text-gray-900 hover:bg-gray-100 rounded-2xl transition-all">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <div class="flex-1 overflow-y-auto p-8 space-y-8 custom-scrollbar">
            <!-- Content -->
            <div class="space-y-4">
              <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Content</h4>
              <div
                class="bg-gray-50 p-8 rounded-3xl border border-gray-100 text-gray-700 leading-relaxed text-lg font-medium italic">
                "{{ selectedPost.content || 'No text content available for this post.' }}"
              </div>
            </div>

            <div v-if="selectedPost.image_url" class="space-y-4">
              <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Attached Image</h4>
              <div class="rounded-3xl overflow-hidden border border-gray-100">
                <img :src="selectedPost.image_url" class="w-full object-cover max-h-96">
              </div>
            </div>

            <!-- Engagement Metrics -->
            <div class="space-y-4">
              <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Engagement Overview</h4>
              <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-6 bg-blue-50 border border-blue-100 rounded-3xl text-center">
                  <p class="text-3xl font-black text-blue-600">{{ selectedPost.reactions_count || 0 }}</p>
                  <p class="text-[9px] font-black text-blue-400 uppercase tracking-widest mt-2">Likes</p>
                </div>
                <div class="p-6 bg-purple-50 border border-purple-100 rounded-3xl text-center">
                  <p class="text-3xl font-black text-purple-600">{{ selectedPost.comments_count || 0 }}</p>
                  <p class="text-[9px] font-black text-purple-400 uppercase tracking-widest mt-2">Comments</p>
                </div>
                <div class="p-6 bg-red-50 border border-red-100 rounded-3xl text-center">
                  <p class="text-3xl font-black text-red-600">{{ selectedPost.reports_count || 0 }}</p>
                  <p class="text-[9px] font-black text-red-400 uppercase tracking-widest mt-2">Reports</p>
                </div>
                <div class="p-6 bg-gray-50 border border-gray-100 rounded-3xl text-center">
                  <p class="text-sm font-black text-gray-900">{{ dayjs(selectedPost.created_at).format('MMM D, YYYY') }}
                  </p>
                  <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-2">Posted</p>
                </div>
              </div>
            </div>

            <!-- Author Statistics -->
            <div class="space-y-4" v-if="postDetails.author_stats">
              <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Author Statistics</h4>
              <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-5 bg-white border border-gray-100 rounded-3xl text-center">
                  <p class="text-2xl font-black text-gray-900">{{ postDetails.author_stats.total_posts || 0 }}</p>
                  <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1">Total Posts</p>
                </div>
                <div class="p-5 bg-blue-50 border border-blue-100 rounded-3xl text-center">
                  <p class="text-2xl font-black text-blue-600">{{ postDetails.author_stats.total_reactions || 0 }}</p>
                  <p class="text-[9px] font-black text-blue-400 uppercase tracking-widest mt-1">Total Likes</p>
                </div>
                <div class="p-5 bg-purple-50 border border-purple-100 rounded-3xl text-center">
                  <p class="text-2xl font-black text-purple-600">{{ postDetails.author_stats.total_comments_received ||
                    0
                  }}</p>
                  <p class="text-[9px] font-black text-purple-400 uppercase tracking-widest mt-1">Comments Received</p>
                </div>
                <div class="p-5 bg-red-50 border border-red-100 rounded-3xl text-center">
                  <p class="text-2xl font-black text-red-600">{{ postDetails.author_stats.reports_received || 0 }}</p>
                  <p class="text-[9px] font-black text-red-400 uppercase tracking-widest mt-1">Reports</p>
                </div>
              </div>
            </div>

            <!-- Top Comments -->
            <div class="space-y-4" v-if="postDetails.top_comments && postDetails.top_comments.length > 0">
              <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Top Comments ({{
                postDetails.top_comments.length }})</h4>
              <div class="bg-white rounded-3xl border border-gray-50 overflow-hidden divide-y divide-gray-50">
                <div v-for="comment in postDetails.top_comments.slice(0, 5)" :key="comment.id"
                  class="p-4 hover:bg-gray-50/50 transition-colors">
                  <div class="flex items-start gap-3">
                    <img :src="comment.user?.avatar_url" :alt="comment.user?.name || 'User'"
                      class="w-8 h-8 rounded-lg object-cover shrink-0 bg-gray-50">
                    <div class="flex-1">
                      <p class="text-xs font-bold text-gray-900 mb-1">{{ comment.user?.name || 'Unknown' }}</p>
                      <p class="text-xs text-gray-600 italic line-clamp-2">"{{ comment.content }}"</p>
                      <span class="text-[9px] font-bold text-gray-400 uppercase mt-2 inline-block">
                        {{ dayjs(comment.created_at).fromNow() }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Post Reports Breakdown -->
            <div class="space-y-4" v-if="postDetails.post_reports && postDetails.post_reports.length > 0">
              <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Reports Against This Post ({{
                postDetails.post_reports.length }})</h4>
              <div class="bg-white rounded-3xl border border-gray-50 overflow-hidden">
                <table class="w-full text-left">
                  <thead class="bg-gray-50">
                    <tr class="text-[9px] font-black text-gray-400 uppercase tracking-widest">
                      <th class="px-6 py-3">Reason</th>
                      <th class="px-6 py-3">Reporter</th>
                      <th class="px-6 py-3 text-right">Status</th>
                      <th class="px-6 py-3 text-right">Date</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-50">
                    <tr v-for="report in postDetails.post_reports" :key="report.id">
                      <td class="px-6 py-4">
                        <span class="text-[10px] font-black uppercase tracking-tight text-red-500">{{ report.reason
                        }}</span>
                      </td>
                      <td class="px-6 py-4 text-xs font-bold text-gray-900">{{ report.reporter?.name || 'Unknown' }}
                      </td>
                      <td class="px-6 py-4 text-right">
                        <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded-lg border"
                          :class="report.status === 'pending' ? 'bg-yellow-50 text-yellow-600 border-yellow-200' : report.status === 'resolved' ? 'bg-green-50 text-green-600 border-green-200' : 'bg-gray-50 text-gray-600 border-gray-200'">
                          {{ report.status }}
                        </span>
                      </td>
                      <td class="px-6 py-4 text-right text-[10px] text-gray-400 font-bold">
                        {{ dayjs(report.created_at).format('MMM D, YY') }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Author's Recent Posts -->
            <div class="space-y-4" v-if="postDetails.author_recent_posts && postDetails.author_recent_posts.length > 0">
              <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Author's Other Recent Posts</h4>
              <div class="bg-white rounded-3xl border border-gray-50 overflow-hidden divide-y divide-gray-50">
                <div v-for="post in postDetails.author_recent_posts" :key="post.id"
                  class="p-4 hover:bg-gray-50/50 transition-colors">
                  <div class="flex items-start gap-3">
                    <div class="flex-1">
                      <p class="text-xs text-gray-700 italic mb-2 line-clamp-2">"{{ post.content || 'Image/Product post'
                      }}"</p>
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

          <div class="p-8 border-t border-gray-50 bg-gray-50/30 flex items-center justify-end gap-3 shrink-0">
            <button @click="initAction(selectedPost, 'pin')"
              class="h-12 px-6 rounded-2xl text-xs font-black uppercase tracking-widest transition-all border"
              :class="selectedPost.is_pinned ? 'bg-agri-forest text-white border-agri-forest' : 'bg-white text-gray-700 border-gray-200 hover:border-agri-forest'">
              {{ selectedPost.is_pinned ? 'Unpin' : 'Pin' }}
            </button>
            <button v-if="selectedPost.status !== 'hidden' && !selectedPost.deleted_at"
              @click="initAction(selectedPost, 'hide')"
              class="h-12 px-6 bg-orange-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-orange-700 transition-colors">
              Hide
            </button>
            <button v-if="selectedPost.status === 'hidden' || selectedPost.deleted_at"
              @click="initAction(selectedPost, 'restore')"
              class="h-12 px-6 bg-green-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-green-700 transition-colors">
              Restore
            </button>
            <button @click="initAction(selectedPost, 'delete')"
              class="h-12 px-6 bg-red-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-red-700 transition-colors">
              Delete
            </button>
          </div>
        </main>
      </div>

      <!-- Action Modal -->
      <Modal :is-open="modal.open" :title="modal.title" :type="modal.type" confirm-text="Confirm"
        @confirm="executeAction" @cancel="modal.open = false" :loading="modal.loading">
        <p class="text-sm text-gray-600 mb-4">{{ modal.message }}</p>
      </Modal>
    </div>

    <!-- Sovereign Action Bar -->
    <Transition enter-active-class="transition duration-700 cubic-bezier(0.16, 1, 0.3, 1)"
      enter-from-class="translate-y-full opacity-0 scale-95" enter-to-class="translate-y-0 opacity-100 scale-100"
      leave-active-class="transition duration-500 cubic-bezier(0.7, 0, 0.84, 0)"
      leave-from-class="translate-y-0 opacity-100 scale-100" leave-to-class="translate-y-full opacity-0 scale-95">
      <div v-if="selectedIds.length > 0" class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[80] w-full max-w-5xl px-6">
        <div
          class="bg-gray-900/95 backdrop-blur-3xl px-8 py-5 rounded-[3rem] border border-white/20 shadow-[0_50px_100px_rgba(0,0,0,0.6)] flex items-center justify-between relative overflow-hidden group">

          <!-- Animated Mesh Glows -->
          <div
            class="absolute -top-24 -right-24 w-64 h-64 bg-agri-forest/20 rounded-full blur-[80px] group-hover:bg-agri-forest/30 transition-all duration-1000">
          </div>
          <div
            class="absolute -bottom-24 -left-24 w-64 h-64 bg-agri-earth/20 rounded-full blur-[80px] group-hover:bg-agri-earth/30 transition-all duration-1000">
          </div>

          <div class="relative z-10 flex items-center space-x-8">
            <div class="flex items-center space-x-3">
              <div class="flex -space-x-4">
                <div v-for="i in Math.min(selectedIds.length, 3)" :key="i"
                  class="w-10 h-10 rounded-full bg-agri-forest border-2 border-gray-900 flex items-center justify-center text-[10px] font-bold text-white shadow-xl">
                  {{ selectedIds.length }}
                </div>
              </div>
              <div class="h-10 w-px bg-white/10 mx-2"></div>
              <div>
                <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mb-0.5">Batch Actions</p>
                <h4 class="text-xl font-bold text-white tracking-tight leading-none">
                  {{ selectedIds.length }} Post<span v-if="selectedIds.length > 1">s</span>
                  <span class="text-agri-lush animate-pulse ml-2 px-3 py-1 bg-agri-lush/10 rounded-lg">Selected</span>
                </h4>
              </div>
            </div>
          </div>

          <div class="relative z-10 flex items-center space-x-3">
            <!-- Hide Action -->
            <button v-if="availableBulkActions.includes('hide')" @click="openBulkActionModal('hide')"
              :disabled="isProcessing"
              class="group/btn flex items-center space-x-3 px-6 py-4 bg-white/5 hover:bg-orange-600 text-white border border-white/10 hover:border-orange-600 rounded-2xl transition-all duration-500 disabled:opacity-50 active:scale-95 shadow-lg hover:shadow-orange-600/40">
              <div
                class="w-8 h-8 rounded-xl bg-white/10 group-hover/btn:bg-white/20 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-orange-400 group-hover/btn:text-white" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.046m4.596-4.596A10.001 10.001 0 0112 4.5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21l-3-3m-3-3l-3-3m2-2a3 3 0 11-4.243 4.243M9.878 9.878L12 12" />
                </svg>
              </div>
              <span class="text-[10px] font-bold uppercase tracking-widest">Hide</span>
            </button>

            <!-- Restore Action -->
            <button v-if="availableBulkActions.includes('restore')" @click="openBulkActionModal('restore')"
              :disabled="isProcessing"
              class="group/btn flex items-center space-x-3 px-6 py-4 bg-white/5 hover:bg-green-600 text-white border border-white/10 hover:border-green-600 rounded-2xl transition-all duration-500 disabled:opacity-50 active:scale-95 shadow-lg hover:shadow-green-600/40">
              <div
                class="w-8 h-8 rounded-xl bg-white/10 group-hover/btn:bg-white/20 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-green-400 group-hover/btn:text-white" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
              </div>
              <span class="text-[10px] font-bold uppercase tracking-widest">Restore</span>
            </button>

            <!-- Flag Action -->
            <button v-if="availableBulkActions.includes('flag')" @click="openBulkActionModal('flag')"
              :disabled="isProcessing"
              class="group/btn flex items-center space-x-3 px-6 py-4 bg-white/5 hover:bg-purple-600 text-white border border-white/10 hover:border-purple-600 rounded-2xl transition-all duration-500 disabled:opacity-50 active:scale-95 shadow-lg hover:shadow-purple-600/40">
              <div
                class="w-8 h-8 rounded-xl bg-white/10 group-hover/btn:bg-white/20 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-purple-400 group-hover/btn:text-white" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-7h1" />
                </svg>
              </div>
              <span class="text-[10px] font-bold uppercase tracking-widest">Flag</span>
            </button>

            <!-- Delete Action -->
            <button @click="openBulkActionModal('delete')" :disabled="isProcessing"
              class="group/btn flex items-center space-x-3 px-6 py-4 bg-red-500/10 hover:bg-red-600 text-white border border-red-500/20 hover:border-red-600 rounded-2xl transition-all duration-500 disabled:opacity-50 active:scale-95 shadow-lg hover:shadow-red-600/40">
              <div
                class="w-8 h-8 rounded-xl bg-red-500/20 group-hover/btn:bg-white/20 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-red-400 group-hover/btn:text-white" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </div>
              <span class="text-[10px] font-bold uppercase tracking-widest">Delete</span>
            </button>

            <div class="h-10 w-px bg-white/10 mx-2"></div>

            <button @click="selectedIds = []"
              class="w-14 h-14 flex items-center justify-center bg-white/5 hover:bg-white/10 text-white/40 hover:text-white rounded-[1.25rem] border border-white/10 transition-all duration-500 group-hover:rotate-90">
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </Transition>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import Modal from '@/components/Modal.vue'

dayjs.extend(relativeTime)

const emit = defineEmits(['view-report', 'view-logs', 'refresh-stats'])

const props = defineProps({
  globalStats: {
    type: Object,
    default: () => ({})
  }
})

const postStats = computed(() => [
  {
    label: 'Total Posts',
    value: props.globalStats.total_posts || 0,
    iconPath: 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2z',
    bgClass: 'from-blue-400/10 to-blue-500/5',
    borderClass: 'border-blue-200/30',
    glowClass: 'bg-blue-500/10',
    iconBg: 'bg-blue-500',
    textColor: 'text-blue-700',
    labelColor: 'text-blue-600/70'
  },
  {
    label: 'Active Live',
    value: props.globalStats.active_posts || 0,
    iconPath: 'M5 13l4 4L19 7',
    bgClass: 'from-green-400/10 to-green-500/5',
    borderClass: 'border-green-200/30',
    glowClass: 'bg-green-500/10',
    iconBg: 'bg-green-500',
    textColor: 'text-green-700',
    labelColor: 'text-green-600/70'
  },
  {
    label: 'New Today',
    value: props.globalStats.new_posts_today || 0,
    iconPath: 'M12 6v6m0 0v6m0-6h6m-6 0H6',
    bgClass: 'from-orange-400/10 to-orange-500/5',
    borderClass: 'border-orange-200/30',
    glowClass: 'bg-orange-500/10',
    iconBg: 'bg-orange-500',
    textColor: 'text-orange-700',
    labelColor: 'text-orange-600/70'
  },
  {
    label: 'Flagged',
    value: props.globalStats.flagged_posts || 0,
    iconPath: 'M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-7h1',
    bgClass: 'from-amber-400/10 to-amber-500/5',
    borderClass: 'border-amber-200/30',
    glowClass: 'bg-amber-500/10',
    iconBg: 'bg-amber-500',
    textColor: 'text-amber-700',
    labelColor: 'text-amber-600/70'
  },
  {
    label: 'Hidden/Trash',
    value: (props.globalStats.hidden_posts || 0) + (props.globalStats.deleted_posts || 0),
    iconPath: 'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21',
    bgClass: 'from-rose-400/10 to-rose-500/5',
    borderClass: 'border-rose-200/30',
    glowClass: 'bg-rose-500/10',
    iconBg: 'bg-rose-500',
    textColor: 'text-rose-700',
    labelColor: 'text-rose-600/70'
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
const posts = ref([])
const filters = ref({ search: '', status: '', deleted: false })
const selectedIds = ref([])
const isProcessing = ref(false)

const toggleSelectAll = () => {
  if (selectedIds.value.length === posts.value.length) {
    selectedIds.value = []
  } else {
    selectedIds.value = posts.value.map(p => p.id)
  }
}

const availableBulkActions = computed(() => {
  if (selectedIds.value.length === 0) return []
  const selectedItems = posts.value.filter(p => selectedIds.value.includes(p.id))

  const actions = ['delete'] // Delete is always available

  // If any selected item is hidden or deleted, 'restore' is valid
  // (Assuming bulk restore is safe even if some are already active - logic can filter on backend or ignore)
  if (selectedItems.some(p => p.status === 'hidden' || p.deleted_at)) {
    actions.push('restore')
  }

  // If any selected item is active/published, 'hide' and 'flag' are valid
  if (selectedItems.some(p => p.status !== 'hidden' && !p.deleted_at)) {
    actions.push('hide')
    actions.push('flag')
  }

  return actions
})

const openBulkActionModal = (action) => {
  const configs = {
    hide: { title: 'Bulk Hide', message: `Hide ${selectedIds.value.length} selected posts?`, type: 'primary' },
    delete: { title: 'Bulk Delete', message: `Delete ${selectedIds.value.length} selected posts permanently?`, type: 'danger' },
    restore: { title: 'Bulk Restore', message: `Restore ${selectedIds.value.length} selected posts?`, type: 'success' },
    flag: { title: 'Bulk Flag', message: `Flag ${selectedIds.value.length} selected posts?`, type: 'primary' }
  }

  if (!configs[action]) return

  modal.value = {
    open: true,
    ...configs[action],
    action: action,
    target: null,
    isBulk: true,
    reason: '',
    loading: false
  }
}

const handleBulkAction = async () => {
  modal.value.loading = true
  const action = modal.value.action

  try {
    await axios.post('/api/agripulse/moderation/bulk-action', {
      ids: selectedIds.value,
      action: action,
      type: 'post'
    })

    if (window.notify) window.notify.success('Bulk Action Complete', `${selectedIds.value.length} posts processed.`)
    selectedIds.value = []
    modal.value.open = false
    fetchPosts()
    emit('refresh-stats')
  } catch (error) {
    if (window.notify) window.notify.error('Bulk Action Failed', 'Could not process selected items.')
  } finally {
    modal.value.loading = false
  }
}

const pagination = ref({})
const openMenuId = ref(null)
const selectedPost = ref(null)

const modal = ref({
  open: false,
  title: '',
  message: '',
  type: 'primary',
  action: null,
  target: null,
  isBulk: false,
  reason: '',
  loading: false
})

const fetchPosts = async (url) => {
  if (typeof url !== 'string') {
    url = '/api/agripulse/moderation/posts'
  }
  loading.value = true
  openMenuId.value = null
  try {
    const params = { ...filters.value, deleted: filters.value.deleted ? 'true' : 'false' }
    const response = await axios.get(url, { params })
    posts.value = response.data.data
    pagination.value = { from: response.data.from, to: response.data.to, total: response.data.total, links: response.data.links }
  } catch (error) {
    console.error('Failed to fetch posts:', error)
  } finally {
    loading.value = false
  }
}

const debouncedFetch = debounce(() => fetchPosts(), 500)

const statusClasses = (post) => {
  if (post.deleted_at) return 'bg-gray-100 text-gray-500 border-gray-200'
  if (post.status === 'published' || !post.status) return 'bg-green-50 text-green-600 border-green-200'
  if (post.status === 'hidden') return 'bg-orange-50 text-orange-600 border-orange-200'
  if (post.status === 'flagged') return 'bg-purple-50 text-purple-600 border-purple-200'
  return 'bg-gray-50 text-gray-400 border-gray-100'
}

const toggleMenu = (id) => {
  openMenuId.value = openMenuId.value === id ? null : id
}

const postDetails = ref({
  author_stats: null,
  top_comments: [],
  post_reports: [],
  author_recent_posts: []
})

const viewDetails = async (post) => {
  selectedPost.value = post
  openMenuId.value = null

  // Fetch detailed information
  try {
    const response = await axios.get(`/api/agripulse/moderation/posts/${post.id}/details`)
    postDetails.value = {
      author_stats: response.data.author_stats,
      top_comments: response.data.top_comments,
      post_reports: response.data.post_reports,
      author_recent_posts: response.data.author_recent_posts
    }
  } catch (error) {
    console.error('Failed to fetch post details:', error)
  }
}

const initAction = (post, action) => {
  const configs = {
    hide: { title: 'Hide post', message: 'Do you want to hide this post from others?', type: 'primary' },
    delete: { title: 'Delete post', message: 'Are you sure you want to delete this post permanently?', type: 'danger' },
    restore: { title: 'Restore post', message: 'Do you want to make this post visible again?', type: 'success' },
    pin: {
      title: post.is_pinned ? 'Unpin post' : 'Pin post',
      message: post.is_pinned ? 'Do you want to unpin this post?' : 'Do you want to pin this post to the top?',
      type: 'primary'
    },
    flag: { title: 'Flag post', message: 'Do you want to flag this post for investigation?', type: 'primary' }
  }

  if (!configs[action]) return

  modal.value = {
    open: true,
    ...configs[action],
    action,
    target: post,
    isBulk: false,
    reason: '',
    loading: false
  }
  openMenuId.value = null
}

const executeAction = async () => {
  modal.value.loading = true
  const { target, action, isBulk } = modal.value

  if (isBulk) {
    return handleBulkAction()
  }

  try {
    await axios.post(`/api/agripulse/moderation/posts/${target.id}/${action}`)
    modal.value.open = false

    if (window.notify) {
      window.notify.success('Action Successful', `Post has been ${action}ed`)
    }
    fetchPosts()
  } catch (error) {
    if (window.notify) {
      window.notify.error('Action Failed', 'Could not complete request')
    }
  } finally {
    modal.value.loading = false
  }
}
onMounted(() => fetchPosts())
</script>

<style scoped>
.animate-in {
  animation: fade-in 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
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
