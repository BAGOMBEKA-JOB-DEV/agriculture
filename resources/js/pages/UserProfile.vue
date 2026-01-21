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
                                <div class="h-40 w-40 rounded-2xl bg-white p-2 shadow-2xl ring-4 ring-white">
                                    <div
                                        class="w-full h-full rounded-xl bg-gradient-to-br from-agri-earth to-agri-sand flex items-center justify-center overflow-hidden">
                                        <img v-if="profileUser?.avatar_url" :src="profileUser.avatar_url"
                                            class="w-full h-full object-cover">
                                        <span v-else class="text-6xl font-black text-agri-forest">{{ userInitial
                                            }}</span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div v-if="!isOwnProfile" class="mb-2 flex items-center space-x-3">
                                    <button
                                        class="px-6 py-2.5 bg-agri-forest text-white rounded-full font-black text-sm hover:bg-agri-lush transition-all shadow-lg hover:shadow-xl active:scale-95">
                                        <svg class="w-4 h-4 inline-block mr-2 -mt-0.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                                            </path>
                                        </svg>
                                        Connect
                                    </button>
                                    <button
                                        class="px-6 py-2.5 bg-white border-2 border-agri-forest text-agri-forest rounded-full font-black text-sm hover:bg-agri-sand/30 transition-all active:scale-95">
                                        Message
                                    </button>
                                    <button
                                        class="p-2.5 bg-white border-2 border-gray-300 rounded-full text-gray-600 hover:bg-gray-50 transition-all active:scale-95">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="mt-6">
                                <h1 class="text-3xl font-black text-gray-900 flex items-center">
                                    {{ profileUser?.name }}
                                    <svg v-if="profileUser?.email_verified_at" class="w-6 h-6 ml-2 text-blue-600"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </h1>
                                <p class="text-lg text-gray-700 font-semibold mt-2">{{ profileUser?.role }}</p>
                                <p class="text-base text-gray-600 mt-1">International Agro Marketplace</p>
                                <div class="flex items-center space-x-2 mt-2 text-sm text-gray-500">
                                    <span>Uganda</span>
                                    <span>•</span>
                                    <button class="text-agri-forest font-bold hover:underline">{{ profileUser?.email
                                        }}</button>
                                </div>

                                <div v-if="isOwnProfile" class="mt-6 flex flex-wrap items-center gap-3">
                                    <button
                                        class="px-6 py-2.5 bg-agri-forest text-white rounded-full font-black text-sm hover:bg-agri-lush transition-all shadow-lg hover:shadow-xl active:scale-95">
                                        <svg class="w-4 h-4 inline-block mr-2 -mt-0.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Open to work
                                    </button>
                                    <button
                                        class="px-6 py-2.5 bg-white border-2 border-agri-forest text-agri-forest rounded-full font-black text-sm hover:bg-agri-sand/30 transition-all active:scale-95">
                                        Add section
                                    </button>
                                    <button
                                        class="px-6 py-2.5 bg-white border-2 border-gray-300 text-gray-700 rounded-full font-black text-sm hover:bg-gray-50 transition-all active:scale-95">
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
                                class="group cursor-pointer hover:bg-gray-50 p-4 rounded-xl transition-all border-2 border-transparent hover:border-agri-sand">
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
                                        <p class="text-2xl font-black text-gray-900">{{
                                            formatNumber(stats.profile_views) }}</p>
                                        <p class="text-sm text-gray-600 font-semibold mt-1">Profile views</p>
                                        <p
                                            class="text-xs text-gray-500 mt-1 group-hover:text-agri-forest transition-colors">
                                            See who viewed your profile</p>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="group cursor-pointer hover:bg-gray-50 p-4 rounded-xl transition-all border-2 border-transparent hover:border-agri-sand">
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
                                        <p class="text-2xl font-black text-gray-900">{{ formatNumber(stats.post_views)
                                            }}</p>
                                        <p class="text-sm text-gray-600 font-semibold mt-1">Post impressions</p>
                                        <p
                                            class="text-xs text-gray-500 mt-1 group-hover:text-agri-forest transition-colors">
                                            Check engagement</p>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="group cursor-pointer hover:bg-gray-50 p-4 rounded-xl transition-all border-2 border-transparent hover:border-agri-sand">
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
                                        <p class="text-2xl font-black text-gray-900">{{
                                            formatNumber(stats.total_reactions) }}</p>
                                        <p class="text-sm text-gray-600 font-semibold mt-1">Total reactions</p>
                                        <p
                                            class="text-xs text-gray-500 mt-1 group-hover:text-agri-forest transition-colors">
                                            Engagement insights</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- About -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-black text-gray-900">About</h2>
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
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="text-xl font-black text-gray-900">Activity</h2>
                                <p class="text-sm text-gray-600 font-semibold mt-1">{{ formatNumber(stats?.total_posts
                                    || 0) }} posts</p>
                            </div>
                            <button v-if="isOwnProfile" @click="$router.push('/agripulse')"
                                class="px-6 py-2.5 bg-agri-forest text-white rounded-full font-black text-sm hover:bg-agri-lush transition-all shadow-lg hover:shadow-xl active:scale-95">
                                Create a post
                            </button>
                        </div>

                        <div v-if="loading" class="space-y-6">
                            <div v-for="i in 2" :key="i" class="animate-pulse">
                                <div class="h-4 bg-gray-200 rounded w-3/4 mb-3"></div>
                                <div class="h-3 bg-gray-200 rounded w-full mb-2"></div>
                                <div class="h-3 bg-gray-200 rounded w-5/6"></div>
                            </div>
                        </div>

                        <div v-else-if="posts.length > 0" class="space-y-6">
                            <div v-for="post in posts.slice(0, 3)" :key="post.id"
                                class="border-b border-gray-100 pb-6 last:border-0 group cursor-pointer hover:bg-gray-50 p-4 -m-4 rounded-xl transition-all">
                                <p class="text-xs text-gray-500 font-semibold mb-3">{{ profileUser?.name }} posted • {{
                                    formatTime(post.created_at) }}</p>
                                <p class="text-base text-gray-800 leading-relaxed mb-3"
                                    :class="{ 'line-clamp-3': !post.expanded }">{{ post.content }}</p>
                                <img v-if="post.image_url" :src="post.image_url"
                                    class="w-full h-64 object-cover rounded-lg border border-gray-200 mb-3 shadow-sm">
                                <div class="flex items-center space-x-6 text-sm text-gray-600 font-semibold">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5 text-blue-500" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.974 18H11a2 2 0 002-2V8.333a2 2 0 00-2-2H9.333a2 2 0 01-1.863-1.28l-.527-1.405A2 2 0 005.076 2.333l-.01.006A2 2 0 003 4.333V9h1a1 1 0 011 1v.333z">
                                            </path>
                                        </svg>
                                        {{ post.reactions_count || 0 }}
                                    </span>
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                            </path>
                                        </svg>
                                        {{ post.comments_count || 0 }} comments
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-12">
                            <div
                                class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z">
                                    </path>
                                </svg>
                            </div>
                            <p class="text-gray-500 font-medium">No posts yet</p>
                        </div>

                        <button v-if="posts.length > 0" @click="$router.push('/agripulse')"
                            class="w-full mt-6 py-3 text-sm font-black text-gray-600 hover:bg-gray-50 rounded-lg transition-all border-2 border-gray-200 hover:border-agri-forest">
                            View all posts
                            <svg class="w-4 h-4 inline-block ml-1 -mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </button>
                    </div>

                    <!-- Date Joined -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-black text-gray-900">Date Joined</h2>
                        </div>

                        <div
                            class="flex items-start space-x-4 p-4 bg-gradient-to-r from-agri-sand/20 to-transparent rounded-xl border-l-4 border-agri-forest">
                            <div
                                class="w-14 h-14 bg-gradient-to-br from-agri-forest to-agri-lush rounded-xl flex items-center justify-center flex-shrink-0 shadow-md">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-black text-gray-900 text-lg">Member since {{
                                    formatDate(profileUser?.created_at) }}</h3>
                                <p class="text-sm text-gray-600 font-medium mt-1">Joined International Agro Marketplace
                                </p>
                                <p class="text-xs text-gray-500 mt-2">{{ getMembershipDuration(profileUser?.created_at)
                                    }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar -->
                <div class="lg:col-span-4 space-y-4">

                    <!-- Profile Insights (Own Profile) -->
                    <div v-if="isOwnProfile" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                        <h3 class="text-base font-black text-gray-900 mb-4">Profile language</h3>
                        <p class="text-sm text-gray-700 font-semibold">English</p>

                        <hr class="my-4 border-gray-200">

                        <h3 class="text-base font-black text-gray-900 mb-3">Public profile URL</h3>
                        <p
                            class="text-xs text-gray-600 font-mono bg-gray-50 p-2 rounded border border-gray-200 truncate">
                            agrimarket.com/in/{{ profileUser?.name?.toLowerCase().replace(/\s+/g, '-') }}
                        </p>
                    </div>

                    <!-- People Also Viewed -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                        <h3 class="text-base font-black text-gray-900 mb-5">People also viewed</h3>

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
                                        class="w-12 h-12 rounded-full bg-gradient-to-br from-agri-earth to-agri-sand flex items-center justify-center text-agri-forest font-black flex-shrink-0 overflow-hidden shadow-sm group-hover:shadow-md transition-all">
                                        <img v-if="suggestedUser.avatar_url" :src="suggestedUser.avatar_url"
                                            class="w-full h-full object-cover">
                                        <span v-else>{{ suggestedUser.name.charAt(0).toUpperCase() }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4
                                            class="text-sm font-black text-gray-900 truncate group-hover:text-agri-forest transition-colors">
                                            {{ suggestedUser.name }}</h4>
                                        <p class="text-xs text-gray-600 font-medium truncate mt-0.5">{{
                                            suggestedUser.role }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ suggestedUser.posts_count }} posts</p>
                                    </div>
                                </router-link>
                                <button v-if="!isOwnProfile"
                                    class="w-full mt-2 px-4 py-1.5 border-2 border-gray-300 rounded-full text-xs font-black text-gray-700 hover:bg-gray-50 hover:border-agri-forest transition-all">
                                    Connect
                                </button>
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
import { useRoute } from 'vue-router'
import axios from 'axios'
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'

dayjs.extend(relativeTime)

const route = useRoute()

const profileUser = ref(null)
const currentUser = ref(null)
const posts = ref([])
const stats = ref(null)
const loading = ref(true)
const suggestedUsers = ref([])
const loadingSuggested = ref(true)

const userInitial = computed(() => profileUser.value?.name?.charAt(0)?.toUpperCase() || 'U')
const isOwnProfile = computed(() => currentUser.value?.id === profileUser.value?.id)

const formatTime = (time) => dayjs(time).fromNow()
const formatDate = (date) => dayjs(date).format('MMMM YYYY')
const formatNumber = (num) => {
    if (!num) return '0'
    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M'
    if (num >= 1000) return (num / 1000).toFixed(1) + 'K'
    return num.toString()
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
</style>
