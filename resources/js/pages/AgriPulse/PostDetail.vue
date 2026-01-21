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

                <!-- Repost Context -->
                <div v-if="post.original_post_id && post.original_post"
                    class="mx-6 mt-6 p-4 bg-gray-50 rounded-xl border-2 border-gray-200">
                    <p class="text-sm text-gray-600 font-semibold mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M2 4.25A2.25 2.25 0 014.25 2h2.5A2.25 2.25 0 019 4.25v2.5A2.25 2.25 0 016.75 9h-2.5A2.25 2.25 0 012 6.75v-2.5zm0 9A2.25 2.25 0 014.25 11h2.5A2.25 2.25 0 019 13.25v2.5A2.25 2.25 0 016.75 18h-2.5A2.25 2.25 0 012 15.75v-2.5zm9-9A2.25 2.25 0 0113.25 2h2.5A2.25 2.25 0 0118 4.25v2.5A2.25 2.25 0 0115.75 9h-2.5A2.25 2.25 0 0111 6.75v-2.5zm0 9A2.25 2.25 0 0113.25 11h2.5A2.25 2.25 0 0118 13.25v2.5A2.25 2.25 0 0115.75 18h-2.5A2.25 2.25 0 0111 15.75v-2.5z">
                            </path>
                        </svg>
                        {{ post.user.name }} reposted
                    </p>
                    <router-link :to="`/agripulse/profile/${post.original_post.user.id}`"
                        class="flex items-center space-x-3 mb-3 group">
                        <div
                            class="w-10 h-10 rounded-full bg-gradient-to-br from-agri-earth to-agri-sand flex items-center justify-center text-agri-forest font-black overflow-hidden">
                            <img v-if="post.original_post.user.avatar_url" :src="post.original_post.user.avatar_url"
                                class="w-full h-full object-cover">
                            <span v-else>{{ post.original_post.user.name.charAt(0).toUpperCase() }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-black text-gray-900 group-hover:text-agri-forest transition-colors">
                                {{
                                    post.original_post.user.name }}</p>
                            <p class="text-xs text-gray-500">{{ formatTime(post.original_post.created_at) }}</p>
                        </div>
                    </router-link>
                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{
                        post.original_post.content }}</p>
                    <img v-if="post.original_post.image_url" :src="post.original_post.image_url"
                        class="mt-3 w-full h-96 object-cover rounded-lg border border-gray-200">
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
                            <span>{{ post.user_reaction ? 'Like' : 'Like' }}</span>
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

        <!-- Delete Confirmation Modal -->
        <Modal v-model="showDeleteModal" title="Delete Post" type="danger" @confirm="handleDelete">
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
</script>

<style scoped>
/* Any additional styles if needed */
</style>
