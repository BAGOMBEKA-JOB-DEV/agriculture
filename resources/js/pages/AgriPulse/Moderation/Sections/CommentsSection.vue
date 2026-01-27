<template>
  <div class="space-y-6">
    <!-- Premium Metric Cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div v-for="stat in commentStats" :key="stat.label"
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
          <h2 class="text-xl font-bold text-gray-900 tracking-tight">Comment Inventory</h2>
          <p class="text-[11px] text-gray-400 font-semibold uppercase tracking-wider">Monitor user interactions
          </p>
        </div>
        <div class="flex items-center gap-4">
          <div class="relative group">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
              stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input v-model="search" type="text" placeholder="Search comments..."
              class="pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-agri-forest/10 focus:bg-white text-xs font-medium w-64 transition-all"
              @input="debouncedFetch">
          </div>
          <button @click="fetchComments"
            class="p-2.5 text-gray-400 hover:text-agri-forest hover:bg-gray-50 rounded-xl transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
              </path>
            </svg>
          </button>
          <button @click="showExportModal = true; exportMode = 'all'"
            class="px-4 py-2.5 bg-agri-forest text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-agri-lush transition-all shadow-md hover:shadow-lg flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
              </path>
            </svg>
            Export
          </button>
        </div>
      </div>

      <!-- Table -->
      <div class="flex-1 overflow-auto">
        <table class="w-full text-left border-collapse min-w-[1000px]">
          <thead>
            <tr class="bg-white border-b border-gray-50 sticky top-0 z-10">
              <th class="px-8 py-4 w-16">
                <input type="checkbox" :checked="comments.length > 0 && selectedIds.length === comments.length"
                  @change="toggleSelectAll"
                  class="w-4 h-4 rounded border-gray-300 text-agri-forest focus:ring-agri-forest cursor-pointer">
              </th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] w-24">ID</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Comment</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Author</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Parent Post</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Status
              </th>
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
            <tr v-else-if="comments.length === 0">
              <td colspan="7" class="px-8 py-12 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">
                No
                comments found matching your criteria</td>
            </tr>
            <tr v-else v-for="comment in comments" :key="comment.id" @click="viewDetails(comment)"
              class="group hover:bg-gray-50/50 transition-colors cursor-pointer">
              <td class="px-8 py-5" @click.stop>
                <input type="checkbox" v-model="selectedIds" :value="comment.id"
                  class="w-4 h-4 rounded border-gray-300 text-agri-forest focus:ring-agri-forest cursor-pointer">
              </td>
              <td class="px-8 py-5">
                <span class="font-mono text-[10px] text-gray-400 font-bold">#{{ comment.id }}</span>
              </td>
              <td class="px-8 py-5">
                <p class="text-xs text-gray-600 font-medium truncate max-w-sm italic">
                  "{{ comment.content }}"
                </p>
              </td>
              <td class="px-8 py-5">
                <div class="flex items-center gap-2">
                  <img :src="comment.user.avatar_url" :alt="comment.user.name" class="w-6 h-6 rounded-lg object-cover">
                  <div class="flex flex-col">
                    <span class="text-xs font-bold text-gray-900 leading-none">{{ comment.user.name }}</span>
                    <span class="text-[9px] text-gray-400 mt-1">{{ dayjs(comment.created_at).fromNow() }}</span>
                  </div>
                </div>
              </td>
              <td class="px-8 py-5">
                <div v-if="comment.post" class="flex items-center gap-2 text-agri-forest/60">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2z"></path>
                  </svg>
                  <span class="text-[10px] font-black uppercase tracking-widest truncate max-w-[120px]">Post #{{
                    comment.post.id }}</span>
                </div>
              </td>
              <td class="px-8 py-5 text-center">
                <span class="inline-flex px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border"
                  :class="statusClasses(comment)">
                  {{ comment.deleted_at ? 'Deleted' : (comment.status || 'Live') }}
                </span>
              </td>
              <td class="px-8 py-5 text-right relative" @click.stop>
                <button @click="toggleMenu(comment.id)"
                  class="p-2 text-gray-400 hover:text-gray-900 h-8 w-8 flex items-center justify-center rounded-lg bg-gray-50 border-gray-100 hover:border-gray-300 border transition-all">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path
                      d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                  </svg>
                </button>

                <!-- Dropdown Menu -->
                <div v-if="openMenuId === comment.id"
                  class="absolute right-8 top-12 w-48 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden z-20 animate-in fade-in slide-in-from-top-2"
                  v-click-outside="() => openMenuId = null">
                  <div class="p-1.5 space-y-0.5 text-left">
                    <button @click="viewDetails(comment)"
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
                    <button v-if="comment.status !== 'hidden' && !comment.deleted_at"
                      @click="initAction(comment, 'hide')"
                      class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-left text-xs font-bold text-orange-600 hover:bg-orange-50 transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.046m4.596-4.596A10.001 10.001 0 0112 4.5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21l-3-3m-3-3l-3-3m2-2a3 3 0 11-4.243 4.243M9.878 9.878L12 12">
                        </path>
                      </svg>
                      Hide comment
                    </button>
                    <button v-if="comment.status === 'hidden' || comment.deleted_at"
                      @click="initAction(comment, 'restore')"
                      class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-left text-xs font-bold text-green-600 hover:bg-green-50 transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                        </path>
                      </svg>
                      Restore comment
                    </button>
                    <button @click="warnAuthor(comment)"
                      class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-left text-xs font-bold text-gray-900 hover:bg-gray-50 transition-colors">
                      <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                      </svg>
                      Warn author
                    </button>
                    <button v-if="!comment.deleted_at" @click="initAction(comment, 'delete')"
                      class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-left text-xs font-bold text-red-600 hover:bg-red-50 transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                      </svg>
                      Delete comment
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
          <button v-for="link in pagination.links" :key="link.label" @click="fetchComments(link.url)"
            v-html="link.label"
            class="min-w-[32px] h-8 flex items-center justify-center rounded-lg transition-all border" :class="link.active
              ? 'bg-agri-forest text-white border-agri-forest'
              : 'bg-white text-gray-400 border-gray-100 hover:text-agri-forest hover:border-agri-forest'"
            :disabled="!link.url"></button>
        </div>
      </div>

      <!-- Comment Detail Slide-over (EXPANDED) -->
      <div v-if="selectedComment" class="fixed inset-0 z-[100] flex justify-end overflow-hidden"
        @click="selectedComment = null">
        <div class="absolute inset-0 bg-black/20 backdrop-blur-sm transition-opacity"></div>

        <main class="relative w-full max-w-5xl bg-white border-l border-gray-100 h-full flex flex-col animate-slide-in"
          @click.stop>
          <div class="px-10 py-8 border-b border-gray-50 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-4">
              <img :src="selectedComment.user.avatar_url" :alt="selectedComment.user.name"
                class="w-16 h-16 rounded-2xl object-cover shadow-sm bg-gray-50">
              <div>
                <h3 class="text-2xl font-bold text-gray-900 leading-tight">Comment #{{ selectedComment.id }}</h3>
                <p
                  class="text-[11px] font-semibold text-gray-400 uppercase tracking-[0.1em] mt-1.5 flex items-center gap-2">
                  <span>By {{ selectedComment.user.name }}</span>
                  <span>&bull;</span>
                  <span>{{ dayjs(selectedComment.created_at).fromNow() }}</span>
                </p>
              </div>
            </div>
            <button @click="selectedComment = null"
              class="p-3 text-gray-400 hover:text-gray-900 hover:bg-gray-100 rounded-2xl transition-all">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <div class="p-10 flex-1 overflow-y-auto space-y-8 custom-scrollbar">
            <!-- Comment Content -->
            <div class="space-y-4">
              <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Comment Text</h4>
              <div
                class="bg-gray-50 p-8 rounded-3xl border border-gray-100 text-gray-700 leading-relaxed text-lg font-medium italic">
                "{{ selectedComment.content }}"
              </div>
            </div>

            <!-- Parent Post Context -->
            <div v-if="selectedComment.post" class="space-y-4">
              <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Parent Post Context</h4>
              <div class="p-8 bg-agri-forest/5 rounded-3xl border border-agri-forest/10 space-y-6">
                <!-- Parent Author -->
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-3">
                    <img :src="selectedComment.post.user?.avatar_url" :alt="selectedComment.post.user?.name || 'User'"
                      class="w-8 h-8 rounded-lg object-cover bg-white border border-agri-forest/10">
                    <div>
                      <p class="text-[10px] font-bold text-gray-900 leading-none mb-1">{{
                        selectedComment.post.user?.name
                        || 'Unknown Author' }}</p>
                      <p class="text-[9px] font-medium text-gray-500 uppercase tracking-widest">{{
                        dayjs(selectedComment.post.created_at).fromNow() }}</p>
                    </div>
                  </div>
                  <div
                    class="bg-white px-3 py-1 rounded-full border border-agri-forest/10 text-[9px] font-bold text-agri-forest uppercase tracking-widest">
                    Post #{{ selectedComment.post.id }}
                  </div>
                </div>

                <!-- Content -->
                <p
                  class="text-sm text-gray-600 font-medium italic leading-relaxed border-l-2 border-agri-forest/20 pl-4">
                  "{{ selectedComment.post.content || 'Image/Product post' }}"
                </p>

                <!-- Parent Stats -->
                <div class="grid grid-cols-3 gap-3 pt-2">
                  <div class="bg-white p-3 rounded-2xl border border-agri-forest/10 text-center">
                    <p class="text-xs font-bold text-gray-900">{{ selectedComment.post.reactions_count ?? 0 }}</p>
                    <p class="text-[8px] font-bold text-gray-400 uppercase tracking-tighter">Reactions</p>
                  </div>
                  <div class="bg-white p-3 rounded-2xl border border-agri-forest/10 text-center">
                    <p class="text-xs font-bold text-gray-900">{{ selectedComment.post.comments_count ?? 0 }}</p>
                    <p class="text-[8px] font-bold text-gray-400 uppercase tracking-tighter">Comments</p>
                  </div>
                  <div class="bg-white p-3 rounded-2xl border border-agri-forest/10 text-center">
                    <p class="text-xs font-bold text-red-600">{{ selectedComment.post.reports_count ?? 0 }}</p>
                    <p class="text-[8px] font-bold text-red-400 uppercase tracking-tighter">Reports</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Author Statistics -->
            <div class="space-y-4" v-if="commentDetails.author_stats">
              <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Author Statistics</h4>
              <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-6 bg-purple-50 border border-purple-100 rounded-3xl text-center">
                  <p class="text-3xl font-black text-purple-600">{{ commentDetails.author_stats.total_comments || 0 }}
                  </p>
                  <p class="text-[9px] font-black text-purple-400 uppercase tracking-widest mt-2">Total Comments</p>
                </div>
                <div class="p-6 bg-blue-50 border border-blue-100 rounded-3xl text-center">
                  <p class="text-3xl font-black text-blue-600">{{ commentDetails.author_stats.total_posts || 0 }}</p>
                  <p class="text-[9px] font-black text-blue-400 uppercase tracking-widest mt-2">Posts Created</p>
                </div>
                <div class="p-6 bg-gray-50 border border-gray-100 rounded-3xl text-center">
                  <p class="text-3xl font-black text-gray-900">{{ commentDetails.author_stats.account_age_days || 0 }}
                  </p>
                  <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-2">Days Active</p>
                </div>
                <div class="p-6 bg-green-50 border border-green-100 rounded-3xl text-center">
                  <p class="text-3xl font-black text-green-600">{{ commentDetails.author_stats.account_age_hours || 0 }}
                  </p>
                  <p class="text-[9px] font-black text-green-400 uppercase tracking-widest mt-2">Hours Active</p>
                </div>
              </div>
            </div>

            <!-- Author's Recent Comments -->
            <div class="space-y-4"
              v-if="commentDetails.author_recent_comments && commentDetails.author_recent_comments.length > 0">
              <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Author's Recent Comments ({{
                commentDetails.author_recent_comments.length }})</h4>
              <div class="bg-white rounded-3xl border border-gray-50 overflow-hidden divide-y divide-gray-50">
                <div v-for="comment in commentDetails.author_recent_comments" :key="comment.id"
                  class="p-4 hover:bg-gray-50/50 transition-colors">
                  <div class="flex items-start gap-3">
                    <div class="flex-1">
                      <p class="text-xs text-gray-700 italic mb-2 line-clamp-2">"{{ comment.content }}"</p>
                      <div class="flex items-center gap-3 text-[9px] font-bold text-gray-400">
                        <span class="flex items-center gap-1">
                          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2z"></path>
                          </svg>
                          Post #{{ comment.post?.id }}
                        </span>
                        <span class="text-gray-300">|</span>
                        <span>{{ dayjs(comment.created_at).fromNow() }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Related Comments on Same Post -->
            <div class="space-y-4" v-if="commentDetails.related_comments && commentDetails.related_comments.length > 0">
              <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Other Comments on This Post ({{
                commentDetails.related_comments.length }})</h4>
              <div class="bg-white rounded-3xl border border-gray-50 overflow-hidden divide-y divide-gray-50">
                <div v-for="comment in commentDetails.related_comments" :key="comment.id"
                  class="p-4 hover:bg-gray-50/50 transition-colors">
                  <div class="flex items-start gap-3">
                    <img :src="comment.user?.avatar_url" :alt="comment.user?.name || 'User'"
                      class="w-8 h-8 rounded-lg object-cover shrink-0 bg-agri-sand">
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
          </div>

          <div class="p-8 bg-gray-50/50 border-t border-gray-50 flex items-center justify-end gap-3 shrink-0">
            <button v-if="selectedComment.status !== 'hidden' && !selectedComment.deleted_at"
              @click="initAction(selectedComment, 'hide')"
              class="h-12 px-6 bg-orange-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-orange-700 transition-colors">
              Hide
            </button>
            <button v-if="selectedComment.status === 'hidden' || selectedComment.deleted_at"
              @click="initAction(selectedComment, 'restore')"
              class="h-12 px-6 bg-green-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-green-700 transition-colors">
              Restore
            </button>
            <button @click="initAction(selectedComment, 'delete')"
              class="h-12 px-6 bg-red-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-red-700 transition-colors">
              Delete
            </button>
          </div>
        </main>
      </div>

      <!-- Sovereign Action Bar -->
      <Transition enter-active-class="transition duration-700 cubic-bezier(0.16, 1, 0.3, 1)"
        enter-from-class="translate-y-full opacity-0 scale-95" enter-to-class="translate-y-0 opacity-100 scale-100"
        leave-active-class="transition duration-500 cubic-bezier(0.7, 0, 0.84, 0)"
        leave-from-class="translate-y-0 opacity-100 scale-100" leave-to-class="translate-y-full opacity-0 scale-95">
        <div v-if="selectedIds.length > 0"
          class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[80] w-full max-w-5xl px-6">
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
                    {{ selectedIds.length }} Comment<span v-if="selectedIds.length > 1">s</span>
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

              <!-- Export Action -->
              <button @click="showExportModal = true; exportMode = 'selected'" :disabled="isProcessing"
                class="group/btn flex items-center space-x-3 px-6 py-4 bg-agri-forest/10 hover:bg-agri-forest text-white border border-agri-forest/20 hover:border-agri-forest rounded-2xl transition-all duration-500 disabled:opacity-50 active:scale-95 shadow-lg hover:shadow-agri-forest/40">
                <div
                  class="w-8 h-8 rounded-xl bg-agri-forest/20 group-hover/btn:bg-white/20 flex items-center justify-center transition-colors">
                  <svg class="w-4 h-4 text-agri-forest group-hover/btn:text-white" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                      d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-widest">Export</span>
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

      <!-- Export Modal -->
      <Modal :isOpen="showExportModal" @cancel="showExportModal = false"
        :title="'Export ' + (exportMode === 'selected' ? 'Selected' : 'All') + ' Comments'">
        <div class="p-6 space-y-6">
          <div class="text-center space-y-2">
            <div class="w-16 h-16 bg-agri-forest/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-agri-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Select Export Format</h3>
            <p class="text-sm text-gray-500">
              {{ exportMode === 'selected' ? 'Exporting ' + selectedIds.length + ' selected records.' : 'Exporting all records based on active filters.' }}
            </p>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <button v-for="format in exportFormats" :key="format.value" @click="handleExport(format.value)"
              :disabled="isProcessing"
              class="group p-4 border-2 border-gray-100 rounded-2xl hover:border-agri-forest hover:bg-agri-forest/5 transition-all text-left space-y-2 disabled:opacity-50">
              <div
                class="w-10 h-10 bg-gray-50 group-hover:bg-agri-forest/10 rounded-xl flex items-center justify-center transition-colors">
                <svg class="w-5 h-5 text-gray-400 group-hover:text-agri-forest" fill="none" stroke="currentColor"
                  viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="format.icon" />
                </svg>
              </div>
              <div>
                <p class="text-sm font-black text-gray-900 leading-tight">{{ format.label }}</p>
                <p class="text-[10px] text-gray-500 font-medium">{{ format.description }}</p>
              </div>
            </button>
          </div>

          <div class="bg-blue-50/50 p-4 rounded-2xl border border-blue-100 flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-[11px] text-blue-700 font-medium leading-relaxed">
              Exports are processed asynchronously. You will receive a notification with a download link once your file
              is ready.
            </p>
          </div>
        </div>
      </Modal>

      <!-- Action Modal -->
      <Modal :is-open="modal.open" :title="modal.title" :type="modal.type" confirm-text="Confirm"
        @confirm="executeAction" @cancel="modal.open = false" :loading="modal.loading">
        <p class="text-sm text-gray-600 mb-4">{{ modal.message }}</p>
      </Modal>

      <!-- Governance Modal (Warning) -->
      <Modal :is-open="governanceModal.isOpen" title="Protocol: Warning" type="warning" confirm-text="Issue Warning"
        @confirm="submitWarning" @cancel="governanceModal.isOpen = false" :loading="governanceModal.loading">
        <div>
          <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Reason for
            action</label>
          <textarea v-model="governanceModal.reason"
            class="w-full p-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-agri-forest/20 text-sm font-medium h-32 resize-none"
            placeholder="Describe why you are issuing this warning..."></textarea>
        </div>
      </Modal>
    </div>
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

const commentStats = computed(() => [
  {
    label: 'Total Comments',
    value: props.globalStats.total_comments || 0,
    iconPath: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
    bgClass: 'from-purple-400/10 to-purple-500/5',
    borderClass: 'border-purple-200/30',
    glowClass: 'bg-purple-500/10',
    iconBg: 'bg-purple-500',
    textColor: 'text-purple-700',
    labelColor: 'text-purple-600/70'
  },
  {
    label: 'Active Live',
    value: props.globalStats.active_comments || 0,
    iconPath: 'M5 13l4 4L19 7',
    bgClass: 'from-green-400/10 to-green-500/5',
    borderClass: 'border-green-200/30',
    glowClass: 'bg-green-500/10',
    iconBg: 'bg-green-500',
    textColor: 'text-green-700',
    labelColor: 'text-green-600/70'
  },
  {
    label: 'Hidden',
    value: props.globalStats.hidden_comments || 0,
    iconPath: 'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21',
    bgClass: 'from-orange-400/10 to-orange-500/5',
    borderClass: 'border-orange-200/30',
    glowClass: 'bg-orange-500/10',
    iconBg: 'bg-orange-500',
    textColor: 'text-orange-700',
    labelColor: 'text-orange-600/70'
  },
  {
    label: 'Deleted',
    value: props.globalStats.deleted_comments || 0,
    iconPath: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
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
const comments = ref([])
const search = ref('')
const selectedIds = ref([])
const isProcessing = ref(false)
const showExportModal = ref(false)
const exportMode = ref('all') // 'all' or 'selected'
const exportFormats = [
  { value: 'pdf', label: 'PDF', description: 'Enterprise Document', icon: 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z' },
  { value: 'excel', label: 'Excel', description: 'Data Spreadsheet', icon: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
  { value: 'word', label: 'Word', description: 'Rich Document', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
  { value: 'csv', label: 'CSV', description: 'Raw Data File', icon: 'M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2' }
]

const handleExport = async (format) => {
  try {
    isProcessing.value = true
    await axios.post('/api/agripulse/moderation/export/comments', {
      format,
      ids: exportMode.value === 'selected' ? selectedIds.value : null,
      filters: { search: search.value }
    })

    if (window.notify) {
      window.notify.success('Export Started', 'We\'ll notify you when your export is ready to download.')
    }

    showExportModal.value = false
  } catch (error) {
    console.error('Export failed:', error)
    if (window.notify) {
      window.notify.error('Export Failed', 'Could not start export. Please try again.')
    }
  } finally {
    isProcessing.value = false
  }
}

const toggleSelectAll = () => {
  if (selectedIds.value.length === comments.value.length) {
    selectedIds.value = []
  } else {
    selectedIds.value = comments.value.map(c => c.id)
  }
}

const availableBulkActions = computed(() => {
  if (selectedIds.value.length === 0) return []
  const selectedItems = comments.value.filter(c => selectedIds.value.includes(c.id))

  const actions = ['delete'] // Delete is always available

  // If any selected item is hidden or deleted, 'restore' is valid
  if (selectedItems.some(c => c.status === 'hidden' || c.deleted_at)) {
    actions.push('restore')
  }

  // If any selected item is active/published, 'hide' is valid
  if (selectedItems.some(c => c.status !== 'hidden' && !c.deleted_at)) {
    actions.push('hide')
  }

  return actions
})

const openBulkActionModal = (action) => {
  const configs = {
    hide: { title: 'Bulk Hide', message: `Hide ${selectedIds.value.length} selected comments?`, type: 'primary' },
    delete: { title: 'Bulk Delete', message: `Delete ${selectedIds.value.length} selected comments permanently?`, type: 'danger' },
    restore: { title: 'Bulk Restore', message: `Restore ${selectedIds.value.length} selected comments?`, type: 'success' }
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
      type: 'comment'
    })

    if (window.notify) window.notify.success('Bulk Action Complete', `${selectedIds.value.length} comments processed.`)
    selectedIds.value = []
    modal.value.open = false
    fetchComments()
    emit('refresh-stats')
  } catch (error) {
    if (window.notify) window.notify.error('Bulk Action Failed', 'Could not process selected items.')
  } finally {
    modal.value.loading = false
  }
}
const pagination = ref({})
const openMenuId = ref(null)
const selectedComment = ref(null)

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

const governanceModal = ref({
  isOpen: false,
  reason: '',
  targetUser: null,
  loading: false
})

const fetchComments = async (url) => {
  if (typeof url !== 'string') {
    url = '/api/agripulse/moderation/comments'
  }
  loading.value = true
  openMenuId.value = null
  try {
    const response = await axios.get(url, { params: { search: search.value } })
    comments.value = response.data.data
    pagination.value = { from: response.data.from, to: response.data.to, total: response.data.total, links: response.data.links }
  } catch (error) {
    console.error('Failed to fetch comments:', error)
  } finally {
    loading.value = false
  }
}

const debouncedFetch = debounce(() => fetchComments(), 500)

const statusClasses = (comment) => {
  if (comment.deleted_at) return 'bg-gray-100 text-gray-500 border-gray-200'
  if (comment.status === 'hidden') return 'bg-orange-50 text-orange-600 border-orange-200'
  return 'bg-green-50 text-green-600 border-green-200'
}

const toggleMenu = (id) => {
  openMenuId.value = openMenuId.value === id ? null : id
}

const commentDetails = ref({
  author_stats: null,
  author_recent_comments: [],
  related_comments: []
})

const viewDetails = async (comment) => {
  selectedComment.value = comment
  openMenuId.value = null

  // Fetch detailed information
  try {
    const response = await axios.get(`/api/agripulse/moderation/comments/${comment.id}/details`)
    commentDetails.value = {
      author_stats: response.data.author_stats,
      author_recent_comments: response.data.author_recent_comments,
      related_comments: response.data.related_comments
    }
  } catch (error) {
    console.error('Failed to fetch comment details:', error)
  }
}

const initAction = (comment, action) => {
  const configs = {
    hide: { title: 'Hide comment', message: 'Do you want to hide this comment from others?', type: 'primary' },
    delete: { title: 'Delete comment', message: 'Are you sure you want to delete this comment permanently?', type: 'danger' },
    restore: { title: 'Restore comment', message: 'Do you want to restore this comment and make it visible again?', type: 'success' }
  }

  modal.value = {
    open: true,
    ...configs[action],
    action,
    target: comment,
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
    if (action === 'delete') {
      await axios.delete(`/api/agripulse/moderation/comments/${target.id}`)
    } else {
      await axios.post(`/api/agripulse/moderation/comments/${target.id}/${action}`)
    }
    modal.value.open = false

    if (window.notify) {
      window.notify.success('Action successful', `Comment #${target.id} has been ${action}ed.`)
    }

    if (selectedComment.value && selectedComment.value.id === target.id) {
      selectedComment.value = null
    }
    fetchComments()

    // Notify parent to refresh stats
    emit('refresh-stats')
  } catch (error) {
    if (window.notify) {
      window.notify.error('Action failed', 'The server could not complete the request.')
    } else {
      alert('Action failed. Please try again.')
    }
  } finally {
    modal.value.loading = false
  }
}

const warnAuthor = (comment) => {
  governanceModal.value = {
    isOpen: true,
    reason: '',
    targetUser: comment.user,
    loading: false
  }
  openMenuId.value = null
}

const submitWarning = async () => {
  if (!governanceModal.value.reason) return alert('Please provide a reason.')

  governanceModal.value.loading = true
  try {
    await axios.post(`/api/agripulse/moderation/users/${governanceModal.value.targetUser.id}/warn`, {
      reason: governanceModal.value.reason
    })
    governanceModal.value.isOpen = false

    if (window.notify) {
      window.notify.success('Warning Sent', `A warning has been issued to ${governanceModal.value.targetUser.name}.`)
    }
  } catch (error) {
    if (window.notify) {
      window.notify.error('Warning failed', 'Could not send the warning signal.')
    } else {
      alert('Warning failed.')
    }
  } finally {
    governanceModal.value.loading = false
  }
}

onMounted(() => fetchComments())
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
