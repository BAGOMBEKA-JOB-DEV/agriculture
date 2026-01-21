<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4 overflow-hidden">
        <!-- Repost Header -->
        <div v-if="post.type === 'repost'" class="px-4 pt-3 pb-1 border-b border-gray-50 flex items-center space-x-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
            </svg>
            <router-link :to="`/agripulse/profile/${post.user.id}`"
                class="text-xs font-bold text-gray-500 hover:text-agri-forest transition-colors">
                {{ post.user.name }} reposted this
            </router-link>
        </div>

        <!--Post Info -->
        <div class="p-4 flex items-start justify-between">
            <router-link :to="`/agripulse/profile/${post.user.id}`" class="flex items-center space-x-3 group">
                <div
                    class="h-12 w-12 rounded-lg bg-agri-earth flex items-center justify-center overflow-hidden flex-shrink-0 group-hover:ring-2 group-hover:ring-agri-forest transition-all">
                    <img v-if="post.user.avatar_url" :src="post.user.avatar_url" class="w-full h-full object-cover">
                    <span v-else class="text-lg font-bold text-agri-forest">{{ post.user.name.charAt(0).toUpperCase()
                        }}</span>
                </div>
                <div>
                    <h4
                        class="text-sm font-black text-gray-900 leading-tight group-hover:text-agri-forest transition-colors">
                        {{ post.user.name }}</h4>
                    <p class="text-xs text-gray-500 font-medium truncate max-w-[200px]">{{ post.user.role }} at IAM</p>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">{{
                        formatTime(post.created_at) }}</p>
                </div>
            </router-link>

            <!-- More Options (Delete & Save) -->
            <div class="flex items-center space-x-2">
                <button @click="toggleSave"
                    class="p-2 text-gray-400 hover:text-agri-forest hover:bg-agri-sand/20 rounded-lg transition-all">
                    <svg v-if="post.is_saved" class="w-5 h-5 fill-current text-agri-forest" fill="currentColor"
                        viewBox="0 0 24 24">
                        <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                    </svg>
                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                    </svg>
                </button>

                <button v-if="post.user_id === currentUser.id" @click="handleDelete"
                    class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 pb-3 cursor-pointer hover:bg-gray-50/50 transition-colors" @click="viewPost">
            <p class="text-sm text-gray-800 whitespace-pre-wrap leading-relaxed">{{ post.content }}</p>

            <!-- Product Tag -->
            <div v-if="post.product"
                class="mt-3 p-3 bg-agri-sand/10 border border-agri-sand/50 rounded-xl flex items-center space-x-3 group cursor-pointer"
                @click="viewProduct">
                <div
                    class="w-12 h-12 rounded-lg bg-agri-sand/30 flex-shrink-0 flex items-center justify-center text-agri-forest font-black overflow-hidden group-hover:bg-agri-forest group-hover:text-white transition-all">
                    <img v-if="post.product.images && post.product.images[0]" :src="post.product.images[0]"
                        class="w-full h-full object-cover">
                    <span v-else>{{ post.product.name.charAt(0) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-black text-agri-forest uppercase tracking-widest">{{ post.product.name }}</p>
                    <p class="text-[10px] text-gray-500 font-bold truncate">{{ post.product.location }} • ${{
                        post.product.price }}</p>
                </div>
                <svg class="w-4 h-4 text-agri-forest transform group-hover:translate-x-1 transition-transform"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </div>

        <!-- Image -->
        <div v-if="post.image_url" class="relative group cursor-pointer"
            @click="$emit('preview-image', post.image_url)">
            <img :src="post.image_url"
                class="w-full h-auto max-h-[600px] object-cover border-y border-gray-100 shadow-inner">
        </div>

        <!-- Original Post (Inside Repost) -->
        <div v-if="post.type === 'repost' && post.original_post"
            class="mx-4 mb-4 p-4 border border-gray-100 rounded-xl bg-gray-50/50">
            <router-link :to="`/agripulse/profile/${post.original_post.user.id}`"
                class="flex items-center space-x-3 mb-2 group">
                <div
                    class="h-8 w-8 rounded-lg bg-agri-earth flex items-center justify-center overflow-hidden flex-shrink-0 group-hover:ring-2 group-hover:ring-agri-forest transition-all">
                    <img v-if="post.original_post.user.avatar_url" :src="post.original_post.user.avatar_url"
                        class="w-full h-full object-cover">
                    <span v-else class="text-xs font-bold text-agri-forest">{{
                        post.original_post.user.name.charAt(0).toUpperCase() }}</span>
                </div>
                <div>
                    <h5 class="text-xs font-black text-gray-900 group-hover:text-agri-forest transition-colors">{{
                        post.original_post.user.name }}</h5>
                    <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">{{
                        formatTime(post.original_post.created_at) }}</p>
                </div>
            </router-link>
            <p class="text-xs text-gray-700 whitespace-pre-wrap">{{ post.original_post.content }}</p>
            <img v-if="post.original_post.image_url" :src="post.original_post.image_url"
                class="mt-2 w-full h-auto rounded-lg border border-gray-200 shadow-sm">
        </div>

        <!-- Interaction Bar (Counts) -->
        <div class="px-4 py-2 border-t border-gray-50 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="flex items-center -space-x-1">
                    <div class="p-1 bg-blue-500 rounded-full border-2 border-white shadow-sm z-30">
                        <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.974 18H11a2 2 0 002-2V8.333a2 2 0 00-2-2H9.333a2 2 0 01-1.863-1.28l-.527-1.405A2 2 0 005.076 2.333l-.01.006A2 2 0 003 4.333V9h1a1 1 0 011 1v.333z" />
                        </svg>
                    </div>
                    <div class="p-1 bg-red-500 rounded-full border-2 border-white shadow-sm z-20">
                        <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <span class="pl-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">{{
                        post.reactions_count || 0 }} reactions</span>
                </div>
            </div>
            <div class="flex items-center space-x-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                <span>{{ post.comments_count || 0 }} comments</span>
                <span>•</span>
                <span>{{ post.reposts_count || 0 }} reposts</span>
                <span>•</span>
                <span>{{ post.views_count || 0 }} views</span>
            </div>
        </div>

        <!-- Interaction Buttons -->
        <div class="px-2 py-1 border-t border-gray-50 flex items-center justify-around">
            <div class="relative group">
                <!-- Reaction Tooltip (LinkedIn Style) -->
                <div
                    class="absolute bottom-full left-0 mb-2 p-1.5 bg-white rounded-full shadow-2xl border border-gray-100 flex items-center space-x-1 transition-all opacity-0 invisible group-hover:opacity-100 group-hover:visible translate-y-1 group-hover:translate-y-0 z-50">
                    <button v-for="type in reactionTypes" :key="type.id" @click="handleReact(type.id)"
                        class="p-2 hover:bg-gray-100 rounded-full transition-transform hover:scale-125"
                        :title="type.label">
                        <span class="text-xl">{{ type.emoji }}</span>
                    </button>
                </div>

                <button @click="handleReact('like')"
                    :class="['flex items-center space-x-2 px-6 py-2.5 rounded-lg transition-all duration-200 group-active:scale-95',
                        userReaction ? reactionTypes.find(r => r.id === userReaction.type)?.colorClass : 'text-gray-500 hover:bg-gray-100']">
                    <div v-if="userReaction" class="text-xl -mt-1">{{reactionTypes.find(r => r.id ===
                        userReaction.type)?.emoji}}</div>
                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5">
                        </path>
                    </svg>
                    <span class="text-xs font-black uppercase tracking-widest">{{userReaction ? reactionTypes.find(r =>
                        r.id === userReaction.type)?.label : 'Like'}}</span>
                </button>
            </div>

            <button @click="toggleComments"
                class="flex items-center space-x-2 px-6 py-2.5 rounded-lg text-gray-500 hover:bg-gray-100 transition-all group">
                <svg class="w-5 h-5 text-gray-400 group-hover:text-agri-forest" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                    </path>
                </svg>
                <span class="text-xs font-black uppercase tracking-widest group-hover:text-gray-900">Comment</span>
            </button>

            <button @click="handleRepost"
                class="flex items-center space-x-2 px-6 py-2.5 rounded-lg text-gray-500 hover:bg-gray-100 transition-all group">
                <svg class="w-5 h-5 text-gray-400 group-hover:text-agri-forest" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
                <span class="text-xs font-black uppercase tracking-widest group-hover:text-gray-900">Repost</span>
            </button>

            <button @click="handleSend"
                class="flex items-center space-x-2 px-6 py-2.5 rounded-lg text-gray-500 hover:bg-gray-100 transition-all group">
                <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
                <span class="text-xs font-black uppercase tracking-widest group-hover:text-gray-900">Send</span>
            </button>
        </div>

        <!-- Comments Section -->
        <div v-if="showComments" class="border-t border-gray-50 bg-gray-50/50">
            <!-- Comment Input -->
            <div class="p-4 border-b border-gray-100 bg-white">
                <div class="flex items-start space-x-3">
                    <div
                        class="h-10 w-10 rounded-lg bg-agri-earth flex items-center justify-center overflow-hidden flex-shrink-0">
                        <img v-if="currentUser?.avatar_url" :src="currentUser.avatar_url"
                            class="w-full h-full object-cover">
                        <span v-else class="text-sm font-bold text-agri-forest">{{
                            currentUser?.name?.charAt(0)?.toUpperCase() }}</span>
                    </div>
                    <div class="flex-1">
                        <textarea v-model="newComment" @keydown.enter.ctrl="submitComment"
                            class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm resize-none focus:outline-none focus:ring-2 focus:ring-agri-forest/20 focus:border-agri-forest transition-all"
                            placeholder="Add a comment... (Ctrl+Enter to post)" rows="2"></textarea>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-[10px] text-gray-400 font-bold">{{ newComment.length }}/1000</span>
                            <button @click="submitComment" :disabled="!newComment.trim() || submittingComment"
                                :class="['px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest transition-all',
                                    newComment.trim() ? 'bg-agri-forest text-white hover:bg-agri-lush' : 'bg-gray-200 text-gray-400 cursor-not-allowed']">
                                {{ submittingComment ? 'Posting...' : 'Post' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comments List -->
            <div class="px-4 py-3 space-y-3 max-h-96 overflow-y-auto scrollbar-hide">
                <div v-if="loadingComments" class="space-y-3">
                    <div v-for="i in 2" :key="i" class="flex space-x-3 animate-pulse">
                        <div class="h-8 w-8 bg-gray-200 rounded-lg"></div>
                        <div class="flex-1 space-y-2">
                            <div class="h-3 bg-gray-200 rounded w-1/4"></div>
                            <div class="h-2 bg-gray-200 rounded w-full"></div>
                        </div>
                    </div>
                </div>

                <div v-else-if="comments.length === 0" class="text-center py-6 text-sm text-gray-400 font-medium">
                    No comments yet. Be the first to comment!
                </div>

                <div v-else class="space-y-3">
                    <CommentItem v-for="comment in comments" :key="comment.id" :comment="comment" :post-id="post.id"
                        :current-user="currentUser" :depth="0" @delete="deleteComment" @refresh="loadComments" />
                </div>

                <button v-if="commentPagination.nextPage" @click="loadMoreComments"
                    class="w-full py-2 text-xs font-black text-gray-500 hover:text-agri-forest uppercase tracking-widest">
                    Load more comments
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Post Modal -->
    <Modal :isOpen="showDeletePostModal" title="Delete Post"
        message="Are you sure you want to delete this post? This action cannot be undone." confirmText="Delete"
        cancelText="Cancel" type="danger" @confirm="confirmDeletePost" @cancel="showDeletePostModal = false" />

    <!-- Delete Comment Modal -->
    <Modal :isOpen="showDeleteCommentModal" title="Delete Comment"
        message="Are you sure you want to delete this comment?" confirmText="Delete" cancelText="Cancel" type="danger"
        @confirm="confirmDeleteComment" @cancel="showDeleteCommentModal = false" />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import Modal from '../../../components/Modal.vue'
import CommentItem from './CommentItem.vue'

dayjs.extend(relativeTime)

const props = defineProps({
    post: {
        type: Object,
        required: true
    },
    currentUser: {
        type: Object,
        required: true
    }
})

const emit = defineEmits(['refresh', 'preview-image', 'repost', 'send'])

const showComments = ref(false)
const comments = ref([])
const loadingComments = ref(false)
const newComment = ref('')
const submittingComment = ref(false)

// Delete confirmation modals
const showDeletePostModal = ref(false)
const showDeleteCommentModal = ref(false)
const commentToDelete = ref(null)

const commentPagination = ref({
    currentPage: 1,
    nextPage: null
})

const userReaction = computed(() => props.post.user_reaction)

const reactionTypes = [
    { id: 'like', label: 'Like', emoji: '👍', colorClass: 'text-blue-600 bg-blue-50' },
    { id: 'celebrate', label: 'Celebrate', emoji: '👏', colorClass: 'text-green-600 bg-green-50' },
    { id: 'support', label: 'Support', emoji: '🤝', colorClass: 'text-indigo-600 bg-indigo-50' },
    { id: 'love', label: 'Love', emoji: '❤️', colorClass: 'text-red-600 bg-red-50' },
    { id: 'insightful', label: 'Insightful', emoji: '💡', colorClass: 'text-yellow-600 bg-yellow-50' },
    { id: 'funny', label: 'Funny', emoji: '😆', colorClass: 'text-orange-600 bg-orange-50' }
]

const formatTime = (time) => dayjs(time).fromNow()

const handleReact = async (type) => {
    try {
        await axios.post(`/api/agripulse/posts/${props.post.id}/react`, { type })
        emit('refresh')
    } catch (error) {
        console.error('Reaction failed:', error)
    }
}

const toggleSave = async () => {
    try {
        await axios.post(`/api/agripulse/posts/${props.post.id}/save`)
        emit('refresh')
    } catch (error) {
        console.error('Save failed:', error)
    }
}

const handleDelete = () => {
    showDeletePostModal.value = true
}

const confirmDeletePost = async () => {
    try {
        await axios.delete(`/api/agripulse/posts/${props.post.id}`)
        showDeletePostModal.value = false
        emit('refresh')
    } catch (error) {
        console.error('Delete failed:', error)
    }
}

const handleRepost = () => {
    emit('repost', props.post)
}

const handleSend = () => {
    emit('send', props.post)
}

const viewProduct = () => {
    if (props.post.product) {
        window.location.href = `/products/${props.post.product.id}`
    }
}

const viewPost = () => {
    // Navigate to post detail page
    window.location.href = `/agripulse/post/${props.post.id}`
}

const toggleComments = async () => {
    showComments.value = !showComments.value
    if (showComments.value && comments.value.length === 0) {
        await loadComments()
    }
}

const loadComments = async (page = 1) => {
    loadingComments.value = true
    try {
        const response = await axios.get(`/api/agripulse/posts/${props.post.id}/comments?page=${page}`)
        if (page === 1) {
            comments.value = response.data.data
        } else {
            comments.value = [...comments.value, ...response.data.data]
        }
        commentPagination.value = {
            currentPage: response.data.current_page,
            nextPage: response.data.next_page_url
        }
    } catch (error) {
        console.error('Failed to load comments:', error)
    } finally {
        loadingComments.value = false
    }
}

const loadMoreComments = () => {
    loadComments(commentPagination.value.currentPage + 1)
}

const submitComment = async () => {
    if (!newComment.value.trim() || submittingComment.value) return

    submittingComment.value = true
    try {
        await axios.post(`/api/agripulse/posts/${props.post.id}/comments`, {
            content: newComment.value
        })
        newComment.value = ''
        await loadComments(1)
        emit('refresh')
    } catch (error) {
        console.error('Comment failed:', error)
    } finally {
        submittingComment.value = false
    }
}


const deleteComment = (commentId) => {
    commentToDelete.value = commentId
    showDeleteCommentModal.value = true
}

const confirmDeleteComment = async () => {
    if (!commentToDelete.value) return

    try {
        await axios.delete(`/api/agripulse/comments/${commentToDelete.value}`)
        showDeleteCommentModal.value = false
        commentToDelete.value = null
        await loadComments(1)
        emit('refresh')
    } catch (error) {
        console.error('Delete comment failed:', error)
    }
}

// Track view on mount
onMounted(async () => {
    try {
        await axios.post(`/api/agripulse/posts/${props.post.id}/view`)
    } catch (error) {
        // Silent fail for view tracking
    }
})
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
