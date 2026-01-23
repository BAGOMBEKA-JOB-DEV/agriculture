<template>
    <div class="min-h-screen bg-[#F3F2EF]">
        <div class="max-w-7xl mx-auto px-4 py-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- Main Content -->
                <div class="lg:col-span-8 space-y-4">

                    <!-- Profile Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <!-- Cover Photo -->
                        <div
                            class="h-48 bg-gradient-to-br from-agri-forest via-agri-lush to-agri-earth relative overflow-hidden">
                            <div class="absolute inset-0 opacity-20">
                                <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                                    <defs>
                                        <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                                            <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5" />
                                        </pattern>
                                    </defs>
                                    <rect width="100" height="100" fill="url(#grid)" />
                                </svg>
                            </div>
                            <div
                                class="absolute bottom-0 left-0 right-0 h-1/2 bg-gradient-to-t from-black/20 to-transparent">
                            </div>
                        </div>

                        <!-- Profile Info -->
                        <div class="px-8 pb-6 -mt-20 relative">
                            <div class="flex items-end justify-between">
                                <div class="h-40 w-40 rounded-2xl bg-white p-2 shadow-sm ring-4 ring-white">
                                    <div
                                        class="w-full h-full rounded-xl bg-gray-50 flex items-center justify-center overflow-hidden border border-gray-100">
                                        <img v-if="profileUser?.avatar_url" :src="profileUser.avatar_url"
                                            class="w-full h-full object-cover">
                                        <span v-else class="text-6xl font-semibold text-agri-forest opacity-40">{{
                                            userInitial
                                            }}</span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div v-if="!isOwnProfile" class="mb-2 flex items-center space-x-3">
                                    <!-- Connect Button (Commented Out per request) -->
                                    <!-- <button
                                        class="px-6 py-2.5 bg-agri-forest text-white rounded-full font-black text-sm hover:bg-agri-lush transition-all shadow-lg hover:shadow-xl active:scale-95">
                                        <svg class="w-4 h-4 inline-block mr-2 -mt-0.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                                            </path>
                                        </svg>
                                        Connect
                                    </button> -->
                                    <button @click="openChat"
                                        class="px-8 py-2.5 bg-white border-2 border-agri-forest text-agri-forest rounded-full font-semibold text-sm hover:bg-agri-forest hover:text-white transition-all shadow-sm flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        Message
                                    </button>
                                    <button
                                        class="p-2.5 bg-white border-2 border-gray-100 rounded-full text-gray-400 hover:text-agri-forest hover:border-agri-forest transition-all active:scale-95">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="mt-6">
                                <h1 class="text-3xl font-semibold text-gray-900 flex items-center tracking-tight">
                                    {{ profileUser?.name }}
                                    <svg v-if="profileUser?.email_verified_at" class="w-6 h-6 ml-2 text-blue-600"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </h1>
                                <p class="text-lg text-gray-700 font-medium mt-1">{{ profileUser?.role }}</p>
                                <p class="text-base text-gray-500 mt-0.5">International Agro Marketplace</p>
                                <div class="flex items-center space-x-2 mt-2 text-sm text-gray-500">
                                    <span>Uganda</span>
                                    <span>•</span>
                                    <button class="text-agri-forest font-medium hover:underline">{{ profileUser?.email
                                    }}</button>
                                </div>

                                <div v-if="isOwnProfile" class="mt-6 flex flex-wrap items-center gap-3">
                                    <!-- Open to work (Removed per request) -->
                                    <button
                                        class="px-6 py-2.5 bg-agri-forest text-white rounded-full font-semibold text-sm hover:bg-agri-lush transition-all shadow-sm">
                                        Add to profile
                                    </button>
                                    <button
                                        class="px-6 py-2.5 bg-white border-2 border-agri-forest text-agri-forest rounded-full font-semibold text-sm hover:bg-agri-sand/20 transition-all">
                                        Improve profile
                                    </button>
                                    <button
                                        class="px-6 py-2.5 bg-white border-2 border-gray-100 text-gray-500 rounded-full font-semibold text-sm hover:bg-gray-50 transition-all">
                                        More
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Analytics (Own Profile Only) -->
                    <div v-if="isOwnProfile && stats" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-black text-gray-900">Analytics</h2>
                            <div class="flex items-center text-xs text-gray-500">
                                <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                    <path fill-rule="evenodd"
                                        d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Private to you
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div
                                class="group cursor-pointer hover:bg-gray-50/50 p-4 rounded-xl transition-all border border-transparent hover:border-gray-100">
                                <div class="flex items-center space-x-4">
                                    <div
                                        class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-2xl font-semibold text-gray-900 tracking-tight">{{
                                            formatNumber(stats.profile_views) }}</p>
                                        <p class="text-sm text-gray-600 font-medium mt-1">Profile views</p>
                                        <p
                                            class="text-xs text-gray-500 mt-1 group-hover:text-agri-forest transition-colors">
                                            Check your views</p>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="group cursor-pointer hover:bg-gray-50/50 p-4 rounded-xl transition-all border border-transparent hover:border-gray-100">
                                <div class="flex items-center space-x-4">
                                    <div
                                        class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-2xl font-semibold text-gray-900 tracking-tight">{{
                                            formatNumber(stats.post_views)
                                        }}</p>
                                        <p class="text-sm text-gray-600 font-medium mt-1">Post impressions</p>
                                        <p
                                            class="text-xs text-gray-500 mt-1 group-hover:text-agri-forest transition-colors">
                                            View your reach</p>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="group cursor-pointer hover:bg-gray-50/50 p-4 rounded-xl transition-all border border-transparent hover:border-gray-100">
                                <div class="flex items-center space-x-4">
                                    <div
                                        class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5">
                                            </path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-2xl font-semibold text-gray-900 tracking-tight">{{
                                            formatNumber(stats.total_reactions) }}</p>
                                        <p class="text-sm text-gray-600 font-medium mt-1">Total reactions</p>
                                        <p
                                            class="text-xs text-gray-500 mt-1 group-hover:text-agri-forest transition-colors">
                                            See results</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- About -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold text-gray-900 tracking-tight">About</h2>
                            <button v-if="isOwnProfile"
                                class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                    </path>
                                </svg>
                            </button>
                        </div>
                        <p class="text-base text-gray-700 leading-relaxed">
                            {{ profileUser?.role }} at International Agro Marketplace, passionate about transforming
                            agriculture through digital innovation and sustainable practices.
                        </p>
                    </div>

                    <!-- Activity -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900 tracking-tight">Activity</h2>
                                <p
                                    class="text-[13px] text-blue-600 font-semibold mt-0.5 hover:underline cursor-pointer">
                                    {{ formatFollowers(stats?.followers_count || 13215) }} followers
                                </p>
                            </div>
                            <div class="flex items-center space-x-2.5">
                                <button v-if="isOwnProfile" @click="$router.push('/agripulse')"
                                    class="px-5 py-2 border-2 border-agri-forest text-agri-forest rounded-full font-bold text-[13px] hover:bg-agri-forest/5 transition-all active:scale-95">
                                    Create a post
                                </button>
                                <button v-if="isOwnProfile"
                                    class="p-2 text-gray-400 hover:bg-gray-100/80 border border-transparent hover:border-gray-200 rounded-full transition-all">
                                    <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Filter Chips -->
                        <div class="flex items-center space-x-2 overflow-x-auto pb-4 scrollbar-hide -mx-1 px-1 mt-4">
                            <button v-for="filter in filters" :key="filter" @click="activeFilter = filter"
                                class="px-4 py-1.5 rounded-full text-sm font-semibold transition-all whitespace-nowrap border"
                                :class="[
                                    activeFilter === filter
                                        ? 'bg-agri-forest text-white border-agri-forest'
                                        : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'
                                ]">
                                {{ filter }}
                            </button>
                        </div>

                        <div v-if="loading" class="space-y-6 mt-4">
                            <div v-for="i in 2" :key="i" class="animate-pulse flex space-x-4">
                                <div class="w-12 h-12 bg-gray-200 rounded-full"></div>
                                <div class="flex-1 space-y-2">
                                    <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                    <div class="h-3 bg-gray-200 rounded w-full"></div>
                                </div>
                            </div>
                        </div>

                        <div v-else-if="filteredActivity.length > 0" class="mt-4 relative group/carousel">
                            <div id="activity-carousel"
                                class="flex space-x-4 overflow-x-auto pb-6 px-1.5 scrollbar-hide snap-x snap-mandatory scroll-smooth">
                                <div v-for="item in filteredActivity" :key="item.id"
                                    class="flex-shrink-0 w-[560px] snap-start border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-all bg-white flex flex-col h-full ring-1 ring-gray-100/50">
                                    <!-- Social Context Header -->
                                    <div class="px-3 py-2 border-b border-gray-100 flex items-center justify-between">
                                        <div class="flex items-center space-x-2 text-[11px] text-gray-500 font-medium">
                                            <div class="w-5 h-5 rounded-full bg-gray-100 overflow-hidden flex-shrink-0">
                                                <img v-if="profileUser?.avatar_url" :src="profileUser.avatar_url"
                                                    class="w-full h-full object-cover">
                                            </div>
                                            <span>{{ profileUser.name }} {{ getActivityVerb(item) }}</span>
                                        </div>
                                        <button class="text-gray-400 hover:text-gray-600">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Content -->
                                    <div @click="navigateToActivity(item)" class="p-4 flex-1 cursor-pointer">
                                        <div class="flex items-center space-x-3 mb-3">
                                            <div class="w-12 h-12 rounded-full overflow-hidden flex-shrink-0">
                                                <img :src="getActivityAuthor(item).avatar_url"
                                                    class="w-full h-full object-cover">
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="text-sm font-semibold text-gray-900 truncate">{{
                                                    getActivityAuthor(item).name }}</h4>
                                                <p class="text-[11px] text-gray-500 truncate mt-0.5">{{
                                                    getActivityAuthor(item).role }}</p>
                                                <div class="flex items-center text-[10px] text-gray-400 mt-0.5">
                                                    <span>{{ formatTime(item.created_at) }}</span>
                                                    <span class="mx-1">•</span>
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zm0 13c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6z" />
                                                        <path d="M7 9h2V4H7v5z" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-800 line-clamp-3 mb-4 leading-normal">{{
                                            getActivityContent(item) }}</p>
                                        <div v-if="getActivityImage(item)"
                                            class="rounded-lg overflow-hidden border border-gray-100 mb-3 aspect-video bg-gray-50 flex items-center justify-center">
                                            <img :src="getActivityImage(item)" class="w-full h-full object-cover">
                                        </div>
                                    </div>

                                    <!-- Footer Actions -->
                                    <div class="px-4 py-3 border-t border-gray-100 mt-auto">
                                        <div
                                            class="flex items-center space-x-3 text-[11px] text-gray-400 mb-3 font-medium">
                                            <span class="flex items-center">
                                                <div
                                                    class="w-4 h-4 rounded-full bg-blue-500 flex items-center justify-center mr-1.5 text-[8px] text-white shadow-sm">
                                                    👍</div>
                                                {{ (item.post || item).reactions_count || 0 }}
                                            </span>
                                            <span class="mx-0.5 opacity-30">•</span>
                                            <span>{{ (item.post || item).reposts_count || 0 }} reposts</span>
                                        </div>
                                        <div
                                            class="flex items-center justify-between border-t border-gray-50 pt-2.5 px-1">
                                            <button @click="handleReact(item, 'like')"
                                                class="flex items-center space-x-2 text-gray-500 hover:text-agri-forest text-[13px] font-semibold py-1.5 px-3 rounded-lg hover:bg-gray-50 transition-all"
                                                :class="{ 'text-blue-600': (item.post || item).user_reaction?.type === 'like' }">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                                </svg>
                                                <span>{{ (item.post || item).user_reaction?.type === 'like' ? 'Liked' :
                                                    'Like' }}</span>
                                            </button>
                                            <button @click="navigateToActivity(item)"
                                                class="flex items-center space-x-2 text-gray-500 hover:text-agri-forest text-[13px] font-semibold py-1.5 px-3 rounded-lg hover:bg-gray-50 transition-all">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                </svg>
                                                <span>Comment</span>
                                            </button>
                                            <button @click="handleRepost(item)"
                                                class="flex items-center space-x-2 text-gray-500 hover:text-agri-forest text-[13px] font-semibold py-1.5 px-3 rounded-lg hover:bg-gray-50 transition-all"
                                                :class="{ 'text-agri-forest': (item.post || item).is_reposted }">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                                <span>Repost</span>
                                            </button>
                                            <button @click="handleSend"
                                                class="flex items-center space-x-2 text-gray-500 hover:text-agri-forest text-[13px] font-semibold py-1.5 px-3 rounded-lg hover:bg-gray-50 transition-all">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                                </svg>
                                                <span>Send</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Carousel Navigation -->
                            <button v-if="filteredActivity.length > 1" @click="scrollCarousel(-1)"
                                class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-5 w-10 min-h-[40px] bg-white shadow-xl border border-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:text-agri-forest opacity-0 group-hover/carousel:opacity-100 transition-all z-20 hover:scale-110">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <button v-if="filteredActivity.length > 1" @click="scrollCarousel(1)"
                                class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-5 w-10 min-h-[40px] bg-white shadow-xl border border-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:text-agri-forest opacity-0 group-hover/carousel:opacity-100 transition-all z-20 hover:scale-110">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>

                        <div v-else class="text-center py-12">
                            <div
                                class="w-20 h-20 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-gray-100 shadow-sm">
                                <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <p class="text-gray-400 font-semibold text-xs uppercase tracking-wider">No recent activity
                            </p>
                        </div>

                        <button v-if="filteredActivity.length > 0" @click="showAllPosts = true"
                            class="w-full mt-4 py-2.5 text-sm font-semibold text-gray-500 hover:text-agri-forest hover:bg-gray-50 rounded-xl transition-all border border-gray-100 flex items-center justify-center group">
                            View all activity
                            <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-0.5 transition-transform"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold text-gray-900 tracking-tight">Date Joined</h2>
                        </div>

                        <div
                            class="flex items-center space-x-6 p-6 bg-white border border-gray-100 rounded-2xl group transition-all duration-300 hover:bg-gray-50/30 hover:border-gray-200/50">
                            <div
                                class="w-16 h-16 bg-agri-forest/5 border border-agri-forest/10 rounded-2xl flex items-center justify-center flex-shrink-0 transition-all duration-300">
                                <svg class="w-8 h-8 text-agri-forest" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 text-xl tracking-tight leading-none">Member since
                                    {{
                                        formatDate(profileUser?.created_at) }}</h3>
                                <p class="text-xs text-agri-forest/60 font-medium uppercase tracking-tight mt-2.5">
                                    Member status
                                </p>
                                <div
                                    class="flex items-center mt-3.5 text-xs text-gray-400 font-medium bg-gray-50/50 w-fit px-3 py-1.5 rounded-lg border border-gray-100/50">
                                    <svg class="w-3.5 h-3.5 mr-2 opacity-60" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Time on platform: {{ getMembershipDuration(profileUser?.created_at) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar -->
                <div class="lg:col-span-4 space-y-4">

                    <!-- Profile Insights (Own Profile) -->
                    <div v-if="isOwnProfile" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                        <h3 class="text-base font-semibold text-gray-900 mb-4 tracking-tight">Profile language</h3>
                        <p class="text-sm text-gray-700 font-semibold">English</p>

                        <hr class="my-4 border-gray-200">

                        <h3 class="text-base font-semibold text-gray-900 mb-3 tracking-tight">Public profile URL</h3>
                        <p
                            class="text-xs text-gray-600 font-mono bg-gray-50 p-2 rounded border border-gray-200 truncate group-hover:bg-gray-100 transition-colors">
                            {{ publicProfileUrl }}
                        </p>
                    </div>

                    <!-- People Also Viewed -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                        <h3 class="text-base font-semibold text-gray-900 mb-5 tracking-tight">People also viewed</h3>

                        <div v-if="loadingSuggested" class="space-y-4">
                            <div v-for="i in 3" :key="i" class="flex items-start space-x-3 animate-pulse">
                                <div class="w-12 h-12 rounded-full bg-gray-200"></div>
                                <div class="flex-1 space-y-2">
                                    <div class="h-3 bg-gray-200 rounded w-3/4"></div>
                                    <div class="h-2 bg-gray-200 rounded w-full"></div>
                                </div>
                            </div>
                        </div>

                        <div v-else-if="suggestedUsers.length > 0" class="space-y-4">
                            <div v-for="suggestedUser in suggestedUsers" :key="suggestedUser.id" class="group">
                                <router-link :to="`/agripulse/profile/${suggestedUser.id}`"
                                    class="flex items-start space-x-3 hover:bg-gray-50 p-3 -m-3 rounded-xl transition-all">
                                    <div
                                        class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-agri-forest font-semibold flex-shrink-0 overflow-hidden border border-gray-200 group-hover:bg-gray-200 transition-all">
                                        <img v-if="suggestedUser.avatar_url" :src="suggestedUser.avatar_url"
                                            class="w-full h-full object-cover">
                                        <span v-else>{{ suggestedUser.name.charAt(0).toUpperCase() }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4
                                            class="text-sm font-semibold text-gray-900 truncate group-hover:text-agri-forest transition-colors tracking-tight">
                                            {{ suggestedUser.name }}</h4>
                                        <p class="text-xs text-gray-600 font-medium truncate mt-0.5">{{
                                            suggestedUser.role }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ suggestedUser.posts_count }} posts</p>
                                    </div>
                                </router-link>
                                <!-- Connect Button (Commented Out per request) -->
                                <!-- <button v-if="!isOwnProfile"
                                    class="w-full mt-2 px-4 py-1.5 border-2 border-gray-300 rounded-full text-xs font-black text-gray-700 hover:bg-gray-50 hover:border-agri-forest transition-all">
                                    Connect
                                </button> -->
                            </div>
                        </div>

                        <div v-else class="text-center py-6">
                            <p class="text-sm text-gray-500">No suggestions available</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'

dayjs.extend(relativeTime)

const route = useRoute()
const router = useRouter()

const scrollCarousel = (direction) => {
    const container = document.getElementById('activity-carousel')
    if (container) {
        container.scrollBy({ left: direction * 566, behavior: 'smooth' })
    }
}

const profileUser = ref(null)
const currentUser = ref(null)
const posts = ref([])
const stats = ref(null)
const loading = ref(true)
const suggestedUsers = ref([])
const loadingSuggested = ref(true)
const showAllPosts = ref(false)
const activeFilter = ref('Posts')
const filters = ['Posts', 'Comments', 'Videos', 'Images', 'Newsletter', 'Documents']
const recentComments = ref([])

const userInitial = computed(() => profileUser.value?.name?.charAt(0)?.toUpperCase() || 'U')
const isOwnProfile = computed(() => currentUser.value?.id === profileUser.value?.id)

const filteredActivity = computed(() => {
    if (activeFilter.value === 'Posts') {
        return posts.value
    } else if (activeFilter.value === 'Comments') {
        return recentComments.value
    } else if (activeFilter.value === 'Videos') {
        // Mock filter for demonstration based on target design
        return posts.value.filter(p => p.content?.toLowerCase().includes('video'))
    } else if (activeFilter.value === 'Images') {
        return posts.value.filter(p => p.image_url)
    }
    return []
})

const displayedPosts = computed(() => {
    return showAllPosts.value ? posts.value : posts.value.slice(0, 3)
})

const getActivityVerb = (item) => {
    if (item.post_id) return 'commented on this'
    if (item.type === 'repost') return 'reposted this'
    return '' // Default or "posted this" can be empty to show ". You"
}

const getActivityAuthor = (item) => {
    // If it's a comment, show the original post's author
    if (item.post) return item.post.user
    // If it's a repost, show original post author too? LinkedIn shows the user as the actor, but the card content is the post.
    if (item.original_post) return item.original_post.user
    return profileUser.value
}

const getActivityContent = (item) => {
    if (item.content) return item.content
    if (item.post) return item.post.content
    if (item.original_post) return item.original_post.content
    return ''
}

const getActivityImage = (item) => {
    if (item.image_url) return item.image_url
    if (item.post?.image_url) return item.post.image_url
    if (item.original_post?.image_url) return item.original_post.image_url
    return null
}

const handleReact = async (item, type) => {
    const post = item.post || item
    try {
        await axios.post(`/api/agripulse/posts/${post.id}/react`, { type })
        fetchProfile() // Refresh to update counts/state
    } catch (error) {
        console.error('Reaction failed:', error)
    }
}

const handleRepost = async (item) => {
    const post = item.post || item
    try {
        await axios.post(`/api/agripulse/posts/${post.id}/repost`, {
            type: 'repost',
            original_post_id: post.id
        })
        fetchProfile()
    } catch (error) {
        console.error('Repost failed:', error)
    }
}

const handleSend = () => {
    if (profileUser.value?.id) {
        window.location.href = `/messages?userId=${profileUser.value.id}`
    }
}

const navigateToActivity = (item) => {
    const id = item.post_id || item.id
    router.push(`/agripulse/post/${id}`)
}

const publicProfileUrl = computed(() => {
    const host = window.location.origin
    const userName = profileUser.value?.name?.toLowerCase().replace(/\s+/g, '-') || 'user'
    return `${host}/agripulse/profile/${profileUser.value?.id || ''}`
})

const formatTime = (time) => dayjs(time).fromNow()
const formatDate = (date) => dayjs(date).format('MMMM YYYY')
const formatNumber = (num) => {
    if (!num) return '0'
    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M'
    if (num >= 1000) return (num / 1000).toFixed(1) + 'K'
    return num.toString()
}

const formatFollowers = (num) => {
    if (!num) return '0'
    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M'
    if (num >= 1000) return (num / 1000).toFixed(1) + 'K'
    return num.toLocaleString()
}

const getMembershipDuration = (joinDate) => {
    if (!joinDate) return ''
    const duration = dayjs().diff(dayjs(joinDate), 'day')
    if (duration < 30) return `${duration} day${duration !== 1 ? 's' : ''} ago`
    if (duration < 365) return `${Math.floor(duration / 30)} month${Math.floor(duration / 30) !== 1 ? 's' : ''} ago`
    return `${Math.floor(duration / 365)} year${Math.floor(duration / 365) !== 1 ? 's' : ''} ago`
}

const fetchProfile = async () => {
    loading.value = true
    try {
        const userId = route.params.id
        const response = await axios.get(`/api/agripulse/users/${userId}/profile`)

        profileUser.value = response.data.user
        stats.value = response.data.stats
        posts.value = response.data.posts.data
        recentComments.value = response.data.recent_comments

        // Track profile view
        await axios.post(`/api/agripulse/users/${userId}/view`).catch(() => { })
    } catch (error) {
        console.error('Profile fetch failed:', error)
    } finally {
        loading.value = false
    }
}

const fetchCurrentUser = async () => {
    try {
        const response = await axios.get('/api/user')
        currentUser.value = response.data
    } catch (error) {
        console.error('User fetch failed:', error)
    }
}

const fetchSuggestedUsers = async () => {
    loadingSuggested.value = true
    try {
        const response = await axios.get('/api/agripulse/suggested-users')
        suggestedUsers.value = response.data
    } catch (error) {
        console.error('Suggested users fetch failed:', error)
    } finally {
        loadingSuggested.value = false
    }
}

// Watch for route param changes (when clicking suggested users)
watch(() => route.params.id, (newId, oldId) => {
    if (newId && newId !== oldId) {
        // Smooth scroll to top for better UX
        window.scrollTo({ top: 0, behavior: 'smooth' })

        // Reload all profile data
        fetchProfile()
        fetchSuggestedUsers()
    }
})

const openChat = () => {
    if (profileUser.value?.id) {
        // Navigate to the messages page and pass the userId
        window.location.href = `/messages?userId=${profileUser.value.id}`
    }
}

onMounted(() => {
    fetchCurrentUser()
    fetchProfile()
    fetchSuggestedUsers()
})
</script>

<style scoped>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-clamp: 3;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
