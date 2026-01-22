<template>
    <div class="min-h-screen bg-[#F3F2EF]">
        <div class="max-w-5xl mx-auto px-4 py-6">
            <!-- Back Button -->
            <button @click="$router.back()"
                class="mb-4 flex items-center text-agri-forest hover:text-agri-lush font-bold transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Feed
            </button>

            <!-- Loading State -->
            <div v-if="loading" class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <div class="animate-pulse space-y-6">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-gray-200 rounded-full"></div>
                        <div class="flex-1 space-y-2">
                            <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                            <div class="h-3 bg-gray-200 rounded w-1/6"></div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="h-3 bg-gray-200 rounded"></div>
                        <div class="h-3 bg-gray-200 rounded w-5/6"></div>
                    </div>
                </div>
            </div>

            <!-- Post Detail -->
            <div v-else-if="post" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <!-- Post Header -->
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-start justify-between">
                        <router-link :to="`/agripulse/profile/${post.user.id}`"
                            class="flex items-center space-x-4 group">
                            <div
                                class="w-14 h-14 rounded-full bg-gradient-to-br from-agri-earth to-agri-sand flex items-center justify-center text-agri-forest font-black flex-shrink-0 overflow-hidden shadow-md group-hover:shadow-lg transition-all">
                                <img v-if="post.user.avatar_url" :src="post.user.avatar_url"
                                    class="w-full h-full object-cover">
                                <span v-else class="text-xl">{{ post.user.name.charAt(0).toUpperCase() }}</span>
                            </div>
                            <div>
                                <h3
                                    class="text-base font-black text-gray-900 group-hover:text-agri-forest transition-colors">
                                    {{ post.user.name }}
                                </h3>
                                <p class="text-sm text-gray-600 font-medium">{{ post.user.role }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ formatTime(post.created_at) }}</p>
                            </div>
                        </router-link>

                        <!-- Delete Button (Own Post) -->
                        <button v-if="currentUser?.id === post.user.id" @click="showDeleteModal = true"
                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- LinkedIn-Style Repost Preview -->
                <div v-if="post.original_post_id && post.original_post" @click.stop="viewOriginalPost"
                    class="mx-6 mt-6 border border-gray-200 rounded-xl overflow-hidden bg-gray-50 hover:bg-gray-100/80 transition-all cursor-pointer group/share shadow-sm">

                    <!-- Original Author Header -->
                    <div class="p-4 flex items-center justify-between border-b border-gray-100">
                        <div class="flex items-center space-x-3">
                            <div @click.stop="viewOriginalAuthorProfile"
                                class="w-10 h-10 rounded-full bg-gradient-to-br from-agri-earth to-agri-sand flex items-center justify-center text-agri-forest font-black overflow-hidden hover:ring-2 hover:ring-agri-forest transition-all">
                                <img v-if="post.original_post.user.avatar_url" :src="post.original_post.user.avatar_url"
                                    class="w-full h-full object-cover">
                                <span v-else>{{ post.original_post.user.name.charAt(0).toUpperCase() }}</span>
                            </div>
                            <div @click.stop="viewOriginalAuthorProfile" class="hover:underline">
                                <p
                                    class="text-sm font-black text-gray-900 group-hover/share:text-agri-forest transition-colors">
                                    {{ post.original_post.user.name }}</p>
                                <p class="text-xs text-gray-500 font-medium">{{ post.original_post.user.role }} at IAM
                                </p>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">{{
                            formatTime(post.original_post.created_at) }}</p>
                    </div>

                    <div class="p-4">
                        <!-- Immediate parent content -->
                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{
                            post.original_post.content }}</p>

                        <!-- Root Context Preview (Simplified) -->
                        <div v-if="post.original_post.original_post"
                            class="mt-3 p-3 border border-gray-200/50 rounded-lg bg-white/50 flex items-center text-xs text-gray-500 italic">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z" />
                            </svg>
                            <span>Originally shared by {{ post.original_post.original_post.user.name }}</span>
                        </div>
                    </div>

                    <!-- Media logic matching PostCard -->
                    <img v-if="post.original_post.image_url" :src="post.original_post.image_url"
                        class="w-full h-auto border-t border-gray-100 max-h-[500px] object-cover">
                    <img v-else-if="post.original_post.original_post?.image_url"
                        :src="post.original_post.original_post.image_url"
                        class="w-full h-auto border-t border-gray-100 max-h-[400px] object-cover opacity-90">

                </div>

                <!-- Post Content -->
                <div class="p-6">
                    <p v-if="post.content" class="text-lg text-gray-800 leading-relaxed whitespace-pre-wrap mb-4">{{
                        post.content }}</p>
                    <img v-if="post.image_url && !post.original_post_id" :src="post.image_url"
                        class="w-full max-h-[600px] object-contain rounded-lg border border-gray-200 shadow-sm">
                </div>

                <!-- Engagement Stats -->
                <div class="px-6 pb-3 border-b border-gray-100">
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <div class="flex items-center space-x-4">
                            <!-- Reactions -->
                            <div v-if="post.reactions_count > 0" class="flex items-center space-x-2">
                                <div class="flex -space-x-1">
                                    <div v-for="(count, type) in post.reaction_breakdown" :key="type"
                                        class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-xs font-bold border-2 border-white shadow-sm">
                                        {{ getReactionEmoji(type) }}
                                    </div>
                                </div>
                                <span class="font-semibold">{{ formatNumber(post.reactions_count) }}</span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span v-if="post.comments_count > 0">{{ formatNumber(post.comments_count) }} {{
                                post.comments_count === 1 ? 'comment' : 'comments' }}</span>
                            <span v-if="post.reposts_count > 0">{{ formatNumber(post.reposts_count) }} {{
                                post.reposts_count === 1 ? 'repost' : 'reposts' }}</span>
                            <span v-if="post.views_count > 0">{{ formatNumber(post.views_count) }} {{
                                post.views_count === 1 ? 'view' : 'views' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center space-x-1">
                        <button @click="handleReact('like')"
                            :class="post.user_reaction ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:bg-gray-100'"
                            class="px-4 py-2 rounded-full transition-all font-bold text-sm flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5">
                                </path>
                            </svg>
                            <span>Like</span>
                        </button>
                        <button @click="focusCommentInput"
                            class="px-4 py-2 rounded-full text-gray-600 hover:bg-gray-100 transition-all font-bold text-sm flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                </path>
                            </svg>
                            <span>Comment</span>
                        </button>

                        <!-- Repost Button with Dropdown -->
                        <div class="relative">
                            <button @click="toggleRepostDropdown"
                                :class="post.is_reposted ? 'text-agri-forest bg-agri-sand/10' : 'text-gray-600 hover:bg-gray-100'"
                                class="px-4 py-2 rounded-full transition-all font-bold text-sm flex items-center space-x-2">
                                <svg class="w-5 h-5" :class="post.is_reposted ? 'text-agri-forest' : ''" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                </svg>
                                <span>Repost</span>
                                <svg class="w-3 h-3 text-gray-400 transition-transform"
                                    :class="{ 'rotate-180': showRepostDropdown }" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div v-if="showRepostDropdown"
                                class="absolute top-full left-0 mt-2 w-64 bg-white rounded-xl shadow-2xl border border-gray-200 py-2 z-50 animate-slideDown">
                                <button @click="handleInstantRepost"
                                    class="w-full px-4 py-3 text-left hover:bg-gray-50 transition-colors flex items-start space-x-3 group">
                                    <svg class="w-5 h-5 text-agri-forest mt-0.5 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                        </path>
                                    </svg>
                                    <div class="flex-1">
                                        <p class="text-sm font-black text-gray-900 group-hover:text-agri-forest">Repost
                                            instantly</p>
                                        <p class="text-xs text-gray-500 mt-0.5">Share immediately to your network</p>
                                    </div>
                                </button>
                                <button @click="handleRepostWithThoughts"
                                    class="w-full px-4 py-3 text-left hover:bg-gray-50 transition-colors flex items-start space-x-3 group border-t border-gray-100">
                                    <svg class="w-5 h-5 text-agri-forest mt-0.5 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                    <div class="flex-1">
                                        <p class="text-sm font-black text-gray-900 group-hover:text-agri-forest">Repost
                                            with your thoughts
                                        </p>
                                        <p class="text-xs text-gray-500 mt-0.5">Add your own commentary</p>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <button @click="toggleSave"
                            :class="post.is_saved ? 'text-agri-forest bg-agri-sand/20' : 'text-gray-600 hover:bg-gray-100'"
                            class="px-4 py-2 rounded-full transition-all font-bold text-sm flex items-center space-x-2">
                            <svg class="w-5 h-5" :class="post.is_saved ? 'fill-current' : ''" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                            </svg>
                            <span>{{ post.is_saved ? 'Saved' : 'Save' }}</span>
                        </button>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="p-6">
                    <h3 class="text-lg font-black text-gray-900 mb-4">Comments</h3>

                    <!-- Add Comment -->
                    <div class="mb-6">
                        <div class="flex items-start space-x-3">
                            <div
                                class="w-10 h-10 rounded-full bg-gradient-to-br from-agri-earth to-agri-sand flex items-center justify-center text-agri-forest font-black  flex-shrink-0 overflow-hidden">
                                <img v-if="currentUser?.avatar_url" :src="currentUser.avatar_url"
                                    class="w-full h-full object-cover">
                                <span v-else class="text-sm">{{ currentUser?.name?.charAt(0).toUpperCase() ||
                                    'U'
                                }}</span>
                            </div>
                            <div class="flex-1">
                                <textarea ref="commentInput" v-model="newComment" placeholder="Add a comment..."
                                    class="w-full border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-agri-forest focus:border-transparent resize-none"
                                    rows="3"></textarea>
                                <div class="flex items-center justify-end mt-2 space-x-2">
                                    <button @click="newComment = ''"
                                        class="px-4 py-2 text-sm font-bold text-gray-600 hover:bg-gray-100 rounded-full transition-all">Cancel</button>
                                    <button @click="submitComment" :disabled="!newComment.trim() || submittingComment"
                                        class="px-6 py-2 text-sm font-black text-white bg-agri-forest hover:bg-agri-lush disabled:bg-gray-300 rounded-full transition-all">
                                        {{ submittingComment ? 'Posting...' : 'Post' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Comments List -->
                    <div v-if="loadingComments" class="space-y-4">
                        <div v-for="i in 3" :key="i" class="animate-pulse flex items-start space-x-3">
                            <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
                            <div class="flex-1 space-y-2">
                                <div class="h-3 bg-gray-200 rounded w-1/4"></div>
                                <div class="h-3 bg-gray-200 rounded w-full"></div>
                            </div>
                        </div>
                    </div>

                    <div v-else-if="comments.length > 0" class="space-y-4">
                        <CommentItem v-for="comment in comments" :key="comment.id" :comment="comment" :post-id="post.id"
                            :current-user="currentUser" :depth="0" @delete="deleteComment" @refresh="fetchPost" />
                    </div>

                    <div v-else class="text-center py-8">
                        <p class="text-gray-500">No comments yet. Be the first to comment!</p>
                    </div>
                </div>
            </div>

            <!-- Error State -->
            <div v-else class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-black text-gray-900 mb-2">Post not found</h3>
                <p class="text-gray-600 mb-6">This post may have been deleted or you don't have access to view it.</p>
                <button @click="$router.push('/agripulse')"
                    class="px-6 py-2.5 bg-agri-forest text-white rounded-full font-black text-sm hover:bg-agri-lush transition-all shadow-lg hover:shadow-xl">
                    Go to Feed
                </button>
            </div>
        </div>

        <!-- Repost with Thoughts Modal -->
        <Modal :isOpen="showRepostModal" title="Repost with your thoughts" confirmText="Repost" cancelText="Cancel"
            @confirm="submitRepostWithThoughts" @cancel="showRepostModal = false" :loading="submittingRepost">
            <div class="space-y-4">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-agri-earth flex items-center justify-center overflow-hidden">
                        <img v-if="currentUser?.avatar_url" :src="currentUser.avatar_url"
                            class="w-full h-full object-cover">
                        <span v-else class="font-bold text-agri-forest">{{ currentUser?.name?.charAt(0) }}</span>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-gray-900">{{ currentUser?.name }}</h4>
                        <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Post to Anyone</p>
                    </div>
                </div>

                <textarea v-model="repostContent"
                    class="w-full h-32 bg-transparent border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-agri-forest focus:border-transparent resize-none"
                    placeholder="What do you want to talk about?"></textarea>

                <!-- Original Post Preview -->
                <div v-if="post" class="p-3 border border-gray-100 rounded-xl bg-gray-50/50">
                    <div class="flex items-center space-x-2 mb-2">
                        <div class="h-6 w-6 rounded-lg bg-agri-earth flex items-center justify-center overflow-hidden">
                            <img v-if="post.user.avatar_url" :src="post.user.avatar_url"
                                class="w-full h-full object-cover">
                            <span v-else class="text-[10px] font-bold text-agri-forest">{{ post.user.name.charAt(0)
                            }}</span>
                        </div>
                        <span class="text-[10px] font-black text-gray-900">{{ post.user.name }}</span>
                    </div>
                    <p class="text-[10px] text-gray-600 truncate">{{ post.content }}</p>
                </div>
            </div>
        </Modal>

        <!-- Delete Confirmation Modal -->
        <Modal :isOpen="showDeleteModal" title="Delete Post" type="danger" confirmText="Delete" @confirm="handleDelete"
            @cancel="showDeleteModal = false">
            <p class="text-gray-700">Are you sure you want to delete this post? This action cannot be undone.</p>
        </Modal>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import Modal from '../../components/Modal.vue'
import CommentItem from './Components/CommentItem.vue'

dayjs.extend(relativeTime)

const route = useRoute()
const router = useRouter()

const post = ref(null)
const currentUser = ref(null)
const loading = ref(true)
const showDeleteModal = ref(false)

// Comments
const comment = ref('')
const comments = ref([])
const loadingComments = ref(false)
const newComment = ref('')
const submittingComment = ref(false)
const commentInput = ref(null)
const showRepostDropdown = ref(false)
const showRepostModal = ref(false)
const repostContent = ref('')
const submittingRepost = ref(false)

const formatTime = (time) => dayjs(time).fromNow()
const formatNumber = (num) => {
    if (!num) return '0'
    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M'
    if (num >= 1000) return (num / 1000).toFixed(1) + 'K'
    return num.toString()
}

const getReactionEmoji = (type) => {
    const emojis = {
        'like': '👍',
        'love': '❤️',
        'celebrate': '🎉',
        'support': '💪',
        'insightful': '💡',
        'curious': '🤔'
    }
    return emojis[type] || '👍'
}

const fetchPost = async () => {
    loading.value = true
    try {
        const postId = route.params.id
        const response = await axios.get(`/api/agripulse/posts/${postId}`)
        post.value = response.data

        // Set comments from the post data
        comments.value = response.data.all_comments || []
    } catch (error) {
        console.error('Failed to fetch post:', error)
        post.value = null
    } finally {
        loading.value = false
    }
}

const fetchCurrentUser = async () => {
    try {
        const response = await axios.get('/api/user')
        currentUser.value = response.data
    } catch (error) {
        console.error('Failed to fetch user:', error)
    }
}

const handleReact = async (type) => {
    try {
        await axios.post(`/api/agripulse/posts/${post.value.id}/react`, { type })
        await fetchPost() // Refresh to get updated reaction status
    } catch (error) {
        console.error('Reaction failed:', error)
    }
}

const toggleRepostDropdown = (e) => {
    e.stopPropagation()
    showRepostDropdown.value = !showRepostDropdown.value
    if (showRepostDropdown.value) {
        document.addEventListener('click', closeRepostDropdown)
    }
}

const closeRepostDropdown = () => {
    showRepostDropdown.value = false
    document.removeEventListener('click', closeRepostDropdown)
}

const handleInstantRepost = async () => {
    try {
        await axios.post(`/api/agripulse/posts/${post.value.id}/repost`, {
            type: 'repost',
            original_post_id: post.value.id
        })
        showRepostDropdown.value = false
        await fetchPost()
        alert('Reposted successfully!')
    } catch (error) {
        console.error('Instant repost failed:', error)
        alert('Failed to repost. Please try again.')
    }
}

const handleRepostWithThoughts = () => {
    showRepostDropdown.value = false
    showRepostModal.value = true
}

const submitRepostWithThoughts = async () => {
    if (submittingRepost.value) return
    submittingRepost.value = true
    try {
        await axios.post('/api/agripulse/posts', {
            content: repostContent.value,
            type: 'repost',
            original_post_id: post.value.id
        })
        repostContent.value = ''
        showRepostModal.value = false
        await fetchPost()
        alert('Reposted with thoughts successfully!')
    } catch (error) {
        console.error('Repost failed:', error)
    } finally {
        submittingRepost.value = false
    }
}

const toggleSave = async () => {
    try {
        await axios.post(`/api/agripulse/posts/${post.value.id}/save`)
        await fetchPost() // Refresh to get updated save status
    } catch (error) {
        console.error('Save failed:', error)
    }
}

const focusCommentInput = () => {
    commentInput.value?.focus()
}

const submitComment = async () => {
    if (!newComment.value.trim()) return

    submittingComment.value = true
    try {
        await axios.post(`/api/agripulse/posts/${post.value.id}/comments`, {
            content: newComment.value
        })
        newComment.value = ''
        await fetchPost() // Refresh to get new comments
    } catch (error) {
        console.error('Comment submit failed:', error)
        alert('Failed to post comment. Please try again.')
    } finally {
        submittingComment.value = false
    }
}

const deleteComment = async (commentId) => {
    if (!confirm('Are you sure you want to delete this comment?')) return

    try {
        await axios.delete(`/api/agripulse/comments/${commentId}`)
        await fetchPost() // Refresh to remove deleted comment
    } catch (error) {
        console.error('Delete comment failed:', error)
        alert('Failed to delete comment. Please try again.')
    }
}

const refreshPost = () => {
    fetchPost()
}

const handleDelete = async () => {
    try {
        await axios.delete(`/api/agripulse/posts/${post.value.id}`)
        router.push('/agripulse')
    } catch (error) {
        console.error('Failed to delete post:', error)
        alert('Failed to delete post. Please try again.')
    }
}

onMounted(() => {
    fetchCurrentUser()
    fetchPost()
})
const viewOriginalPost = () => {
    if (post.value?.original_post) {
        window.location.href = `/agripulse/post/${post.value.original_post.id}`
    }
}

const viewOriginalAuthorProfile = () => {
    if (post.value?.original_post?.user) {
        window.location.href = `/agripulse/profile/${post.value.original_post.user.id}`
    }
}
</script>

<style scoped>
/* Any additional styles if needed */
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-slideDown {
    animation: slideDown 0.2s ease-out;
}
</style>
