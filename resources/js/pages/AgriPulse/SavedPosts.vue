<template>
    <div class="min-h-screen bg-[#F3F2EF] pb-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- Left Sidebar: Navigation & Stats -->
                <div class="hidden lg:block lg:col-span-3">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden sticky top-6">
                        <!-- Back Navigation -->
                        <router-link to="/agripulse"
                            class="flex items-center space-x-3 p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors group">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-agri-forest transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7">
                                </path>
                            </svg>
                            <span
                                class="text-sm font-black text-gray-700 group-hover:text-agri-forest transition-colors">
                                Back to Feed</span>
                        </router-link>

                        <!-- User Card -->
                        <div class="h-16 bg-gradient-to-r from-agri-forest to-agri-lush"></div>
                        <div class="px-4 pb-4 -mt-8 flex flex-col items-center border-b border-gray-100">
                            <div class="h-16 w-16 rounded-xl bg-white p-1 shadow-md mb-3">
                                <div
                                    class="w-full h-full rounded-lg bg-agri-earth flex items-center justify-center overflow-hidden">
                                    <img v-if="user?.avatar_url" :src="user.avatar_url"
                                        class="w-full h-full object-cover">
                                    <span v-else class="text-xl font-bold text-agri-forest">{{ userInitial }}</span>
                                </div>
                            </div>
                            <h3 class="text-sm font-black text-gray-900">{{ user?.name }}</h3>
                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-1 text-center">
                                {{ user?.role }}
                            </p>
                        </div>

                        <!-- Saved Stats -->
                        <div class="p-4">
                            <div class="flex items-center justify-between text-xs font-black uppercase tracking-widest">
                                <span class="text-gray-400">Saved Posts</span>
                                <span class="bg-agri-forest text-white px-3 py-1 rounded-full text-[10px]">{{ totalSaved
                                    }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Center: Saved Posts Feed -->
                <div class="lg:col-span-6 space-y-4">
                    <!-- Page Header -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-2xl font-black text-gray-900">Saved Posts</h1>
                                <p class="text-sm text-gray-500 font-medium mt-1">
                                    {{ totalSaved }} {{ totalSaved === 1 ? 'post' : 'posts' }} saved for later
                                </p>
                            </div>
                            <div
                                class="w-14 h-14 rounded-full bg-gradient-to-br from-agri-earth to-agri-sand flex items-center justify-center">
                                <svg class="w-7 h-7 text-agri-forest" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div v-if="loading && posts.length === 0" class="space-y-4">
                        <div v-for="i in 3" :key="i"
                            class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 animate-pulse">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="h-12 w-12 bg-gray-200 rounded-lg"></div>
                                <div class="flex-1 space-y-2">
                                    <div class="h-3 bg-gray-200 rounded w-1/4"></div>
                                    <div class="h-2 bg-gray-200 rounded w-1/3"></div>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="h-3 bg-gray-200 rounded w-full"></div>
                                <div class="h-3 bg-gray-200 rounded w-5/6"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Saved Posts List -->
                    <div v-else-if="posts.length > 0" class="space-y-4">
                        <PostCard v-for="post in posts" :key="post.id" :post="post" :currentUser="user"
                            @refresh="fetchSavedPosts" />

                        <!-- Load More Button -->
                        <button v-if="pagination.nextPage" @click="loadMore"
                            class="w-full py-3 bg-white border border-gray-200 rounded-xl text-sm font-black text-gray-500 hover:bg-gray-50 transition-colors uppercase tracking-widest"
                            :disabled="loadingMore">
                            {{ loadingMore ? 'Loading...' : 'Load More Saves' }}
                        </button>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-black text-gray-900 mb-2">No Saved Posts Yet</h3>
                        <p class="text-sm text-gray-500 font-medium max-w-sm mx-auto mb-6">
                            Start building your collection! Tap the bookmark icon on any post to save it here for later.
                        </p>
                        <router-link to="/agripulse"
                            class="inline-flex items-center px-6 py-3 bg-agri-forest text-white rounded-full font-black text-sm hover:bg-agri-lush transition-all shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Explore AgriPulse
                        </router-link>
                    </div>
                </div>

                <!-- Right Sidebar: Tips & Info -->
                <div class="hidden lg:block lg:col-span-3">
                    <div class="space-y-4 sticky top-6">
                        <!-- Tips Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                            <div class="flex items-center space-x-2 mb-4">
                                <div
                                    class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-black text-gray-900">Organize Your Saves</h3>
                            </div>
                            <ul class="space-y-2 text-xs text-gray-600">
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-agri-forest mr-2 mt-0.5 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Save posts for quick reference later</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-agri-forest mr-2 mt-0.5 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Unsave anytime with one click</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-agri-forest mr-2 mt-0.5 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Still interact with saved posts</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Collections Preview (Future Feature) -->
                        <div
                            class="bg-gradient-to-br from-agri-forest to-agri-lush rounded-xl shadow-sm p-5 text-white">
                            <h3 class="text-sm font-black mb-2">Coming Soon: Collections</h3>
                            <p class="text-xs opacity-90 mb-4">Organize saves into custom folders like "Market
                                Insights",
                                "Product Ideas", or "Farming Tips".</p>
                            <div class="flex items-center text-xs font-bold">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span>In Development</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import PostCard from './Components/PostCard.vue'

const router = useRouter()

const user = ref(null)
const posts = ref([])
const loading = ref(true)
const loadingMore = ref(false)

const pagination = ref({
    currentPage: 1,
    lastPage: 1,
    nextPage: null,
    total: 0
})

const userInitial = computed(() => {
    return user.value?.name?.charAt(0).toUpperCase() || 'U'
})

const totalSaved = computed(() => pagination.value.total || 0)

const fetchUser = async () => {
    try {
        const response = await axios.get('/api/user')
        user.value = response.data
    } catch (error) {
        console.error('Failed to fetch user:', error)
    }
}

const fetchSavedPosts = async (page = 1) => {
    if (page === 1) {
        loading.value = true
    } else {
        loadingMore.value = true
    }

    try {
        const response = await axios.get(`/api/agripulse/saved-posts?page=${page}`)

        if (page === 1) {
            posts.value = response.data.data
        } else {
            posts.value = [...posts.value, ...response.data.data]
        }

        pagination.value = {
            currentPage: response.data.current_page,
            lastPage: response.data.last_page,
            nextPage: response.data.current_page < response.data.last_page ? response.data.current_page + 1 : null,
            total: response.data.total
        }
    } catch (error) {
        console.error('Failed to fetch saved posts:', error)
    } finally {
        loading.value = false
        loadingMore.value = false
    }
}

const loadMore = () => {
    if (pagination.value.nextPage) {
        fetchSavedPosts(pagination.value.nextPage)
    }
}

onMounted(() => {
    fetchUser()
    fetchSavedPosts()
})
</script>

<style scoped>
/* No additional styles needed - using Tailwind */
</style>
