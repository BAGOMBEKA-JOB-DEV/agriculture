<template>
    <div class="comment-thread">
        <div class="flex items-start space-x-3">
            <!-- User Avatar -->
            <router-link :to="`/agripulse/profile/${comment.user.id}`" class="flex-shrink-0">
                <div :class="avatarSize"
                    class="rounded-full bg-gradient-to-br from-agri-earth to-agri-sand flex items-center justify-center text-agri-forest font-black overflow-hidden hover:ring-2 hover:ring-agri-forest transition-all">
                    <img v-if="comment.user.avatar_url" :src="comment.user.avatar_url"
                        class="w-full h-full object-cover">
                    <span :class="depth > 2 ? 'text-xs' : 'text-sm'">{{ comment.user.name.charAt(0).toUpperCase()
                    }}</span>
                </div>
            </router-link>

            <!-- Comment Content -->
            <div class="flex-1 min-w-0">
                <!-- Comment Body -->
                <div :class="commentBgClass" class="rounded-xl p-3 border border-gray-100">
                    <router-link :to="`/agripulse/profile/${comment.user.id}`" :class="textSize"
                        class="font-black text-gray-900 hover:text-agri-forest transition-colors">
                        {{ comment.user.name }}
                    </router-link>
                    <p :class="textSize" class="text-gray-700 mt-1 whitespace-pre-wrap">{{ comment.content }}</p>
                </div>

                <!-- Comment Actions -->
                <div :class="actionTextSize"
                    class="flex items-center space-x-4 mt-2 text-gray-500 font-bold uppercase tracking-wider">
                    <span>{{ formatTime(comment.created_at) }}</span>
                    <button @click="toggleReply" class="hover:text-agri-forest transition-colors">
                        Reply
                    </button>
                    <button v-if="currentUser?.id === comment.user_id" @click="$emit('delete', comment.id)"
                        class="hover:text-red-600 transition-colors">
                        Delete
                    </button>

                    <!-- Collapse/Expand Toggle (only if has replies) -->
                    <button v-if="hasReplies" @click="toggleCollapse"
                        class="flex items-center space-x-1 hover:text-agri-forest transition-colors">
                        <svg :class="isCollapsed ? 'rotate-0' : 'rotate-90'" class="w-3 h-3 transition-transform"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ replyCountText }}</span>
                    </button>
                </div>

                <!-- Reply Input -->
                <div v-if="showReplyInput" :class="depth > 0 ? 'mt-3' : 'mt-3'" class="animate-slideDown">
                    <div class="flex items-start space-x-2">
                        <div
                            class="w-8 h-8 rounded-full bg-gradient-to-br from-agri-earth to-agri-sand flex items-center justify-center text-agri-forest font-black flex-shrink-0 overflow-hidden">
                            <img v-if="currentUser?.avatar_url" :src="currentUser.avatar_url"
                                class="w-full h-full object-cover">
                            <span class="text-xs">{{ currentUser?.name?.charAt(0).toUpperCase() || 'U' }}</span>
                        </div>
                        <div class="flex-1">
                            <textarea ref="replyInput" v-model="replyContent" @keydown.enter.ctrl="submitReply"
                                @keydown.esc="cancelReply"
                                class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-agri-forest focus:border-transparent transition-all resize-none"
                                :placeholder="`Reply to ${comment.user.name}...`" rows="2"></textarea>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs text-gray-400">Ctrl+Enter to post</span>
                                <div class="flex items-center space-x-2">
                                    <button @click="cancelReply"
                                        class="px-3 py-1.5 text-xs font-bold text-gray-600 hover:bg-gray-100 rounded-full transition-all">
                                        Cancel
                                    </button>
                                    <button @click="submitReply" :disabled="!replyContent.trim() || submitting"
                                        class="px-4 py-1.5 text-xs font-black text-white bg-agri-forest hover:bg-agri-lush disabled:bg-gray-300 rounded-full transition-all">
                                        {{ submitting ? 'Posting...' : 'Reply' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Nested Replies (Recursive & Collapsible) -->
                <div v-if="hasReplies && !isCollapsed"
                    :class="depth > 0 ? 'mt-3 ml-4 pl-4 border-l-2 border-gray-100' : 'mt-4'"
                    class="space-y-3 animate-fadeIn">

                    <!-- Display visible replies -->
                    <CommentItem v-for="reply in visibleReplies" :key="reply.id" :comment="reply" :post-id="postId"
                        :current-user="currentUser" :depth="depth + 1" @delete="$emit('delete', $event)"
                        @refresh="$emit('refresh')" />

                    <!-- Load More Replies Button -->
                    <button v-if="hasMoreReplies" @click="loadMoreReplies"
                        class="flex items-center space-x-2 text-xs font-bold text-agri-forest hover:text-agri-lush transition-colors py-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                        <span>Load {{ remainingRepliesCount }} more {{ remainingRepliesCount === 1 ? 'reply' : 'replies'
                            }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue'
import axios from 'axios'
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'

// Note: This component is recursive - it imports itself for nested replies
import CommentItem from './CommentItem.vue'

dayjs.extend(relativeTime)

const props = defineProps({
    comment: {
        type: Object,
        required: true
    },
    postId: {
        type: [String, Number],
        required: true
    },
    currentUser: {
        type: Object,
        default: null
    },
    depth: {
        type: Number,
        default: 0
    }
})

const emit = defineEmits(['delete', 'refresh'])

const showReplyInput = ref(false)
const replyContent = ref('')
const submitting = ref(false)
const replyInput = ref(null)

// Collapse/Pagination state
const isCollapsed = ref(false)
const visibleReplyCount = ref(20) // Show 20 replies initially
const REPLIES_PER_PAGE = 20

// Computed properties
const hasReplies = computed(() => props.comment.replies && props.comment.replies.length > 0)
const totalReplies = computed(() => props.comment.replies?.length || 0)
const visibleReplies = computed(() => {
    if (!hasReplies.value) return []
    return props.comment.replies.slice(0, visibleReplyCount.value)
})
const hasMoreReplies = computed(() => totalReplies.value > visibleReplyCount.value)
const remainingRepliesCount = computed(() => totalReplies.value - visibleReplyCount.value)

const replyCountText = computed(() => {
    if (!hasReplies.value) return ''
    const count = totalReplies.value
    return `${count} ${count === 1 ? 'reply' : 'replies'}`
})

// Dynamic styling based on nesting depth
const avatarSize = computed(() => {
    if (props.depth === 0) return 'w-10 h-10'
    if (props.depth === 1) return 'w-9 h-9'
    if (props.depth === 2) return 'w-8 h-8'
    return 'w-7 h-7'
})

const commentBgClass = computed(() => {
    return props.depth % 2 === 0 ? 'bg-gray-50' : 'bg-white'
})

const textSize = computed(() => {
    if (props.depth >= 3) return 'text-xs'
    if (props.depth >= 1) return 'text-sm'
    return 'text-sm'
})

const actionTextSize = computed(() => {
    if (props.depth >= 2) return 'text-[10px]'
    return 'text-xs'
})

const formatTime = (time) => dayjs(time).fromNow()

const toggleCollapse = () => {
    isCollapsed.value = !isCollapsed.value
}

const loadMoreReplies = () => {
    visibleReplyCount.value += REPLIES_PER_PAGE
}

const toggleReply = async () => {
    showReplyInput.value = !showReplyInput.value
    if (showReplyInput.value) {
        await nextTick()
        replyInput.value?.focus()
    }
}

const cancelReply = () => {
    showReplyInput.value = false
    replyContent.value = ''
}

const submitReply = async () => {
    if (!replyContent.value.trim() || submitting.value) return

    // Permission check for replying
    if (props.currentUser?.is_restricted) {
        if (window.notify) window.notify.accessDenied('Replying')
        return
    }

    submitting.value = true
    try {
        await axios.post(`/api/agripulse/posts/${props.postId}/comments`, {
            content: replyContent.value,
            parent_id: props.comment.id
        })

        if (window.notify) window.notify.success('Reply Posted', 'Your reply has been added to the thread.')

        replyContent.value = ''
        showReplyInput.value = false
        emit('refresh')
    } catch (error) {
        console.error('Reply failed:', error)
        if (window.notify) {
            if (error.response?.status === 403) {
                window.notify.accessDenied('Replying')
            } else {
                window.notify.error('Almost there!', 'We couldn\'t post your reply. Please try again.')
            }
        }
    } finally {
        submitting.value = false
    }
}
</script>

<style scoped>
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

@keyframes fadeIn {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}

.animate-slideDown {
    animation: slideDown 0.2s ease-out;
}

.animate-fadeIn {
    animation: fadeIn 0.3s ease-out;
}
</style>
