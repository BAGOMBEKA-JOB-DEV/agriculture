<template>
    <div class="min-h-screen bg-[#F3F2EF] pb-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- Left Sidebar: Profile Summary (Dynamic) -->
                <div class="hidden lg:block lg:col-span-3">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden sticky top-6">
                        <div class="h-16 bg-agri-forest"></div>
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
                                {{ user?.role }} at International Agro Marketplace
                            </p>
                        </div>

                        <div class="p-4 space-y-3">
                            <div
                                class="flex justify-between items-center text-[10px] font-black uppercase tracking-widest text-gray-400">
                                <span>Post views</span>
                                <span class="text-agri-forest">{{ userStats.post_views || 0 }}</span>
                            </div>
                            <div
                                class="flex justify-between items-center text-[10px] font-black uppercase tracking-widest text-gray-400">
                                <span>Profile visits</span>
                                <span class="text-agri-forest">{{ userStats.profile_views || 0 }}</span>
                            </div>
                        </div>

                        <div class="p-4 bg-gray-50 border-t border-gray-100">
                            <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">My Items</p>
                            <div @click="navigateToSavedPosts"
                                class="flex items-center space-x-2 text-xs font-bold text-gray-700 hover:text-agri-forest cursor-pointer transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                                </svg>
                                <span>Saved posts ({{ userStats.saved_posts_count || 0 }})</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Center: Feed -->
                <div class="lg:col-span-6 space-y-4">
                    <PostComposer :user="user" @open-modal="openComposer" />

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

                    <div v-else-if="posts.length > 0" class="space-y-4">
                        <PostCard v-for="post in posts" :key="post.id" :post="post" :currentUser="user"
                            @refresh="fetchFeed" @preview-image="previewImage" @repost="prepareRepost" />

                        <button v-if="pagination.nextPage" @click="loadMore"
                            class="w-full py-3 bg-white border border-gray-200 rounded-xl text-sm font-black text-gray-500 hover:bg-gray-50 transition-colors uppercase tracking-widest"
                            :disabled="loadingMore">
                            {{ loadingMore ? 'Updating Stream...' : 'Show More Pulse' }}
                        </button>
                    </div>

                    <div v-else class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                        <div
                            class="w-20 h-20 bg-agri-sand/30 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-agri-forest" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-black text-gray-900 mb-1">Silence in the Fields</h3>
                        <p class="text-sm text-gray-500 font-medium max-w-xs mx-auto">Be the first to spark an AgriPulse
                            conversation. Share market updates or product news!</p>
                        <button @click="openComposer"
                            class="mt-6 px-6 py-2.5 bg-agri-forest text-white rounded-full font-bold shadow-lg hover:shadow-xl transition-all">
                            Start Post
                        </button>
                    </div>
                </div>

                <!-- Right Sidebar: Super Admin Posts -->
                <div class="hidden lg:block lg:col-span-3 space-y-4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sticky top-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-black text-gray-900 tracking-tight">Market Pulse News</h3>
                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>

                        <div v-if="loadingSuperAdminPosts" class="space-y-4">
                            <div v-for="i in 3" :key="i" class="animate-pulse">
                                <div class="h-3 bg-gray-200 rounded w-full mb-2"></div>
                                <div class="h-2 bg-gray-200 rounded w-2/3"></div>
                            </div>
                        </div>

                        <div v-else-if="superAdminPosts.length > 0" class="space-y-4">
                            <router-link v-for="post in superAdminPosts" :key="post.id"
                                :to="`/agripulse?post=${post.id}`" class="group cursor-pointer block">
                                <h4
                                    class="text-xs font-black text-gray-800 group-hover:text-agri-forest transition-colors leading-snug line-clamp-2">
                                    {{ post.content || 'New update from leadership' }}
                                </h4>
                                <p class="text-[10px] text-gray-400 mt-1 font-bold">
                                    {{ formatTime(post.created_at) }} • {{ post.views_count || 0 }} views
                                </p>
                            </router-link>
                        </div>

                        <div v-else class="text-center py-6">
                            <p class="text-xs text-gray-400 font-medium">No leadership updates yet</p>
                        </div>

                        <button
                            class="w-full mt-4 py-1.5 text-[10px] font-black text-gray-500 hover:bg-gray-100 rounded-lg transition-colors uppercase tracking-widest flex items-center justify-center">
                            Show more
                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Promoted Asset
                        </p>
                        <div class="h-32 bg-agri-sand/20 rounded-xl overflow-hidden mb-3 border border-agri-sand/50">
                            <img src="https://images.unsplash.com/photo-1595147321092-239618580628?q=80&w=1000&auto=format&fit=crop"
                                class="w-full h-full object-cover grayscale opacity-50 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-700">
                        </div>
                        <p class="text-xs font-bold text-gray-700 text-center">Get the leading Market Intelligence
                            Report 2026</p>
                        <button
                            class="w-full mt-3 py-2 bg-white border-2 border-agri-forest text-agri-forest rounded-full text-xs font-black hover:bg-agri-forest hover:text-white transition-all uppercase tracking-widest">
                            Learn More
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Composer Modal -->
        <div v-if="showComposer" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-agri-forest/40 backdrop-blur-md" @click="closeComposer"></div>
            <div
                class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl relative z-10 overflow-hidden transform transition-all scale-100 opacity-100 animate-modal-in">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-xl font-black text-gray-900 tracking-tight">Create a Post</h3>
                    <button @click="closeComposer"
                        class="p-2 text-gray-400 hover:text-gray-900 hover:bg-gray-100 rounded-xl transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-6">
                    <div class="flex items-center space-x-3 mb-4">
                        <div
                            class="h-12 w-12 rounded-xl bg-agri-earth flex items-center justify-center overflow-hidden border border-agri-lush/20 shadow-inner">
                            <img v-if="user?.avatar_url" :src="user.avatar_url" class="w-full h-full object-cover">
                            <span v-else class="text-lg font-bold text-agri-forest">{{ userInitial }}</span>
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-gray-900">{{ user?.name }}</h4>
                            <button
                                class="flex items-center space-x-1 px-2 py-0.5 rounded-full border border-gray-300 text-[10px] font-black text-gray-500">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v2.197A5.973 5.973 0 0110 16v-2a2 2 0 00-2-2 2 2 0 01-2-2 2 2 0 00-1.668-1.973z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>Anyone</span>
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <textarea v-model="newPost.content"
                        class="w-full h-40 bg-transparent border-none focus:ring-0 text-lg text-gray-800 placeholder-gray-400 resize-none p-0"
                        placeholder="What's on your mind?"></textarea>

                    <!-- Repost Preview -->
                    <div v-if="newPost.original_post"
                        class="mt-2 p-3 border border-gray-100 rounded-xl bg-gray-50/50 relative">
                        <button @click="newPost.original_post = null"
                            class="absolute -top-2 -right-2 p-1 bg-white border border-gray-200 rounded-full text-gray-400 hover:text-red-500 shadow-sm transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        <div class="flex items-center space-x-2 mb-2">
                            <div
                                class="h-6 w-6 rounded-lg bg-agri-earth flex items-center justify-center overflow-hidden">
                                <img v-if="newPost.original_post.user.avatar_url"
                                    :src="newPost.original_post.user.avatar_url" class="w-full h-full object-cover">
                                <span v-else class="text-[10px] font-bold text-agri-forest">{{
                                    newPost.original_post.user.name.charAt(0) }}</span>
                            </div>
                            <span class="text-[10px] font-black text-gray-900">{{ newPost.original_post.user.name
                            }}</span>
                        </div>
                        <p class="text-[10px] text-gray-600 truncate">{{ newPost.original_post.content }}</p>
                    </div>

                    <!-- Image Preview -->
                    <div v-if="imagePreview" class="mt-4 relative group">
                        <img :src="imagePreview"
                            class="w-full h-48 object-cover rounded-xl border border-gray-200 shadow-inner">
                        <button @click="removeImage"
                            class="absolute top-2 right-2 p-1.5 bg-white/90 backdrop-blur rounded-xl text-gray-500 hover:text-red-500 shadow-lg transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <label
                            class="p-2.5 text-gray-500 hover:bg-gray-200 rounded-xl cursor-pointer transition-all active:scale-95">
                            <input type="file" ref="fileInput" class="hidden" @change="handleImageSelect"
                                accept="image/*">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </label>
                    </div>

                    <button @click="submitPost" :disabled="!isValidPost || submitting"
                        :class="['px-6 py-2 rounded-full font-black uppercase tracking-widest transition-all shadow-md',
                            isValidPost && !submitting ? 'bg-agri-forest text-white hover:bg-agri-lush hover:shadow-lg active:scale-95' : 'bg-gray-300 text-gray-100 cursor-not-allowed']">
                        {{ submitting ? 'Spreading...' : 'Pulse It' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Image Preview Modal -->
        <div v-if="activePreviewImage" class="fixed inset-0 z-[100] flex items-center justify-center p-4"
            @click="activePreviewImage = null">
            <div class="absolute inset-0 bg-black/90 backdrop-blur-xl"></div>
            <img :src="activePreviewImage"
                class="max-w-full max-h-full object-contain relative z-10 shadow-2xl animate-scale-in">
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import PostComposer from './Components/PostComposer.vue'
import PostCard from './Components/PostCard.vue'

dayjs.extend(relativeTime)

const router = useRouter()

const user = ref(null)
const posts = ref([])
const loading = ref(true)
const loadingMore = ref(false)
const submitting = ref(false)
const showComposer = ref(false)
const activePreviewImage = ref(null)

const userStats = ref({
    post_views: 0,
    profile_views: 0,
    saved_posts_count: 0
})

const superAdminPosts = ref([])
const loadingSuperAdminPosts = ref(true)

const pagination = ref({
    currentPage: 1,
    nextPage: null
})

const newPost = ref({
    content: '',
    image: null,
    original_post: null
})

const imagePreview = ref(null)
const fileInput = ref(null)

const userInitial = computed(() => user.value?.name?.charAt(0)?.toUpperCase() || 'U')
const isValidPost = computed(() => newPost.value.content.trim().length > 0 || newPost.value.image || newPost.value.original_post)

const formatTime = (time) => dayjs(time).fromNow()

const fetchUser = async () => {
    try {
        const response = await axios.get('/api/user')
        user.value = response.data
    } catch (error) {
        console.error('User fetch failed:', error)
    }
}

const fetchUserStats = async () => {
    try {
        const response = await axios.get('/api/agripulse/my-stats')
        userStats.value = response.data
    } catch (error) {
        console.error('Stats fetch failed:', error)
    }
}

const fetchSuperAdminPosts = async () => {
    try {
        const response = await axios.get('/api/agripulse/super-admin-posts')
        superAdminPosts.value = response.data
    } catch (error) {
        console.error('Super admin posts fetch failed:', error)
    } finally {
        loadingSuperAdminPosts.value = false
    }
}

const fetchFeed = async (append = false) => {
    if (append) loadingMore.value = true
    else loading.value = true

    try {
        const page = append ? pagination.value.currentPage + 1 : 1
        const response = await axios.get(`/api/agripulse/feed?page=${page}`)

        if (append) {
            posts.value = [...posts.value, ...response.data.data]
        } else {
            posts.value = response.data.data
        }

        pagination.value = {
            currentPage: response.data.current_page,
            nextPage: response.data.next_page_url
        }
    } catch (error) {
        console.error('Feed fetch failed:', error)
    } finally {
        loading.value = false
        loadingMore.value = false
    }
}

const openComposer = (type = 'post') => {
    showComposer.value = true
    if (type === 'photo') {
        setTimeout(() => fileInput.value?.click(), 100)
    }
}

const closeComposer = () => {
    showComposer.value = false
    if (!submitting.value) {
        resetComposer()
    }
}

const resetComposer = () => {
    newPost.value = { content: '', image: null, original_post: null }
    imagePreview.value = null
}

const handleImageSelect = (e) => {
    const file = e.target.files[0]
    if (file) {
        newPost.value.image = file
        const reader = new FileReader()
        reader.onload = (e) => imagePreview.value = e.target.result
        reader.readAsDataURL(file)
    }
}

const removeImage = () => {
    newPost.value.image = null
    imagePreview.value = null
    if (fileInput.value) fileInput.value.value = ''
}

const submitPost = async () => {
    if (!isValidPost.value || submitting.value) return

    submitting.value = true
    const formData = new FormData()
    formData.append('content', newPost.value.content)
    if (newPost.value.image) {
        formData.append('image', newPost.value.image)
    }
    if (newPost.value.original_post) {
        formData.append('type', 'repost')
        formData.append('original_post_id', newPost.value.original_post.id)
    }

    try {
        await axios.post('/api/agripulse/posts', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })
        resetComposer()
        showComposer.value = false
        await fetchFeed()
        await fetchUserStats()
    } catch (error) {
        console.error('Post submission failed:', error)
    } finally {
        submitting.value = false
    }
}

const previewImage = (url) => {
    activePreviewImage.value = url
}

const prepareRepost = (post) => {
    newPost.value.original_post = post
    showComposer.value = true
}

const navigateToSavedPosts = () => {
    router.push('/agripulse/saved')
}

const loadMore = () => fetchFeed(true)

onMounted(() => {
    fetchUser()
    fetchFeed()
    fetchUserStats()
    fetchSuperAdminPosts()
})
</script>

<style scoped>
.animate-modal-in {
    animation: modal-in 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.animate-scale-in {
    animation: scale-in 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes modal-in {
    from {
        opacity: 0;
        transform: scale(0.9) translateY(20px);
    }

    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

@keyframes scale-in {
    from {
        opacity: 0;
        transform: scale(0.8);
    }

    to {
        opacity: 1;
        transform: scale(1);
    }
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-clamp: 2;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
