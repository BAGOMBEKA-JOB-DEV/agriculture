<template>
    <div class="min-h-screen bg-[#F3F2EF]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-8 flex flex-col">
                    <!-- Back Button: Moved inside grid column for perfect left-alignment -->
                    <button @click="$router.back()"
                        class="mb-4 inline-flex items-center text-agri-forest hover:text-agri-lush font-bold transition-all hover:-translate-x-1 group/back">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                        <span class="text-sm">Back to Feed</span>
                    </button>

                    <div class="space-y-4 animate-fadeIn">
                        <!-- Loading State -->
                        <div v-if="loading" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                            <div class="animate-pulse space-y-6">
                                <div class="flex items-start space-x-4">
                                    <div class="w-12 h-12 bg-gray-100 rounded-full"></div>
                                    <div class="flex-1 space-y-2">
                                        <div class="h-4 bg-gray-100 rounded w-1/4"></div>
                                        <div class="h-3 bg-gray-100 rounded w-1/6"></div>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <div class="h-3 bg-gray-100 rounded"></div>
                                    <div class="h-3 bg-gray-100 rounded w-5/6"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Post Detail -->
                        <div v-else-if="post"
                            class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <!-- Post Header -->
                            <div class="p-6 border-b border-gray-100">
                                <div class="flex items-start justify-between">
                                    <router-link :to="`/agripulse/profile/${post.user.id}`"
                                        class="flex items-center space-x-4 group">
                                        <div
                                            class="w-14 h-14 rounded-full bg-gradient-to-br from-agri-earth to-agri-sand flex items-center justify-center text-agri-forest font-black flex-shrink-0 overflow-hidden shadow-md group-hover:shadow-lg transition-all border-2 border-white">
                                            <img v-if="post.user.avatar_url" :src="post.user.avatar_url"
                                                class="w-full h-full object-cover">
                                            <span v-else class="text-xl">{{ post.user.name.charAt(0).toUpperCase()
                                            }}</span>
                                        </div>
                                        <div>
                                            <h3
                                                class="text-base font-black text-gray-900 group-hover:text-agri-forest transition-colors">
                                                {{ post.user.name }}
                                            </h3>
                                            <p class="text-[13px] text-gray-500 font-medium">{{ post.user.role }}</p>
                                            <p
                                                class="text-[11px] text-gray-400 mt-1 font-bold uppercase tracking-wider">
                                                {{
                                                    formatTime(post.created_at) }}</p>
                                        </div>
                                    </router-link>

                                    <!-- Delete Button (Own Post) -->
                                    <button v-if="currentUser?.id === post.user.id" @click="showDeleteModal = true"
                                        class="p-2 text-gray-400 hover:text-red-600 hover:bg-neutral-50 rounded-xl transition-all active:scale-95">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Repost Preview -->
                            <div v-if="post.original_post_id && post.original_post" @click.stop="viewOriginalPost"
                                class="mx-6 mt-6 border border-gray-100 rounded-2xl overflow-hidden bg-neutral-50/50 hover:bg-neutral-100/50 transition-all cursor-pointer group/share shadow-sm">

                                <!-- Original Author Header -->
                                <div class="p-4 flex items-center justify-between border-b border-gray-100">
                                    <div class="flex items-center space-x-3">
                                        <div @click.stop="viewOriginalAuthorProfile"
                                            class="w-10 h-10 rounded-full bg-gradient-to-br from-agri-earth to-agri-sand flex items-center justify-center text-agri-forest font-black overflow-hidden hover:ring-2 hover:ring-agri-forest transition-all">
                                            <img v-if="post.original_post.user.avatar_url"
                                                :src="post.original_post.user.avatar_url"
                                                class="w-full h-full object-cover">
                                            <span v-else>{{ post.original_post.user.name.charAt(0).toUpperCase()
                                            }}</span>
                                        </div>
                                        <div @click.stop="viewOriginalAuthorProfile" class="hover:underline">
                                            <p
                                                class="text-sm font-black text-gray-900 group-hover/share:text-agri-forest transition-colors">
                                                {{ post.original_post.user.name }}</p>
                                            <p class="text-[11px] text-gray-500 font-bold uppercase tracking-widest">
                                                {{ post.original_post.user.role }}
                                            </p>
                                        </div>
                                    </div>
                                    <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest">{{
                                        formatTime(post.original_post.created_at) }}</p>
                                </div>

                                <div class="p-4">
                                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{
                                        post.original_post.content }}</p>

                                    <div v-if="post.original_post.original_post"
                                        class="mt-3 p-3 border border-gray-200/40 rounded-xl bg-white/40 flex items-center text-[11px] text-gray-500 italic">
                                        <svg class="w-4 h-4 mr-2 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z" />
                                        </svg>
                                        <span>First shared by {{ post.original_post.original_post.user.name }}</span>
                                    </div>
                                </div>

                                <img v-if="post.original_post.image_url" :src="post.original_post.image_url"
                                    class="w-full h-auto border-t border-gray-100 max-h-[500px] object-cover">
                            </div>

                            <!-- Post Content -->
                            <div class="p-6">
                                <p v-if="post.content"
                                    class="text-lg text-gray-800 leading-relaxed whitespace-pre-wrap mb-4 font-medium">
                                    {{
                                        post.content }}</p>
                                <img v-if="post.image_url && !post.original_post_id" :src="post.image_url"
                                    class="w-full max-h-[600px] object-contain rounded-2xl border border-gray-100 shadow-sm">
                            </div>

                            <!-- Engagement Stats -->
                            <div class="px-6 pb-4 border-b border-gray-50">
                                <div class="flex items-center justify-between text-[13px] text-gray-500">
                                    <div class="flex items-center space-x-4">
                                        <div v-if="post.reactions_count > 0" class="flex items-center space-x-2">
                                            <div class="flex -space-x-1.5">
                                                <div v-for="(count, type) in post.reaction_breakdown" :key="type"
                                                    class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center text-[10px] border-2 border-white shadow-sm">
                                                    {{ getReactionEmoji(type) }}
                                                </div>
                                            </div>
                                            <span class="font-bold text-gray-700">{{ formatNumber(post.reactions_count)
                                            }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4 font-semibold">
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
                            <div
                                class="px-6 py-4 border-b border-gray-50 flex items-center justify-between bg-neutral-50/30">
                                <div class="flex items-center space-x-1">
                                    <button @click="handleReact('like')"
                                        :class="post.user_reaction ? 'text-blue-600 bg-blue-50/50' : 'text-gray-600 hover:bg-white hover:shadow-sm'"
                                        class="px-5 py-2.5 rounded-full transition-all font-black text-[13px] flex items-center space-x-2 active:scale-95">
                                        <svg class="w-5 h-5" :fill="post.user_reaction ? 'currentColor' : 'none'"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5">
                                            </path>
                                        </svg>
                                        <span>Like</span>
                                    </button>
                                    <button @click="focusCommentInput"
                                        class="px-5 py-2.5 rounded-full text-gray-600 hover:bg-white hover:shadow-sm transition-all font-black text-[13px] flex items-center space-x-2 active:scale-95">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                            </path>
                                        </svg>
                                        <span>Comment</span>
                                    </button>

                                    <!-- Repost -->
                                    <div class="relative">
                                        <button @click="toggleRepostDropdown"
                                            :class="post.is_reposted ? 'text-agri-forest' : 'text-gray-600 hover:bg-white hover:shadow-sm'"
                                            class="px-5 py-2.5 rounded-full transition-all font-black text-[13px] flex items-center space-x-2 active:scale-95">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                            </svg>
                                            <span>Repost</span>
                                        </button>

                                        <div v-if="showRepostDropdown"
                                            class="absolute top-full left-0 mt-2 w-64 bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-gray-100 py-2 z-50 animate-slideDown">
                                            <button @click="handleInstantRepost"
                                                class="w-full px-4 py-3 text-left hover:bg-neutral-50 transition-colors flex items-start space-x-3 group">
                                                <div class="p-2 bg-agri-forest/5 rounded-lg text-agri-forest">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2.5" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-[13px] font-black text-gray-900">Repost now</p>
                                                    <p class="text-[11px] text-gray-500">Post it right away</p>
                                                </div>
                                            </button>
                                            <button @click="handleRepostWithThoughts"
                                                class="w-full px-4 py-3 text-left hover:bg-neutral-50 transition-colors flex items-start space-x-3 group border-t border-gray-50">
                                                <div class="p-2 bg-agri-forest/5 rounded-lg text-agri-forest">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2.5"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-[13px] font-black text-gray-900">Add your thoughts
                                                    </p>
                                                    <p class="text-[11px] text-gray-500">Say what you think first</p>
                                                </div>
                                            </button>
                                        </div>
                                    </div>

                                    <button @click="toggleSave"
                                        :class="post.is_saved ? 'text-agri-forest bg-agri-forest/10' : 'text-gray-600 hover:bg-white hover:shadow-sm'"
                                        class="px-5 py-2.5 rounded-full transition-all font-black text-[13px] flex items-center space-x-2 active:scale-95">
                                        <svg class="w-5 h-5" :fill="post.is_saved ? 'currentColor' : 'none'"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                                        </svg>
                                        <span>{{ post.is_saved ? 'Saved' : 'Save' }}</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Comments Section -->
                            <div class="p-6">
                                <h3 class="text-base font-black text-gray-900 mb-6 uppercase tracking-wider">Comments
                                </h3>

                                <!-- Add Comment -->
                                <div class="mb-8">
                                    <div class="flex items-start space-x-4">
                                        <div
                                            class="w-10 h-10 rounded-full bg-neutral-100 flex items-center justify-center text-agri-forest font-black flex-shrink-0 overflow-hidden border border-gray-100">
                                            <img v-if="currentUser?.avatar_url" :src="currentUser.avatar_url"
                                                class="w-full h-full object-cover">
                                            <span v-else class="text-sm font-black">{{
                                                currentUser?.name?.charAt(0).toUpperCase() || 'U' }}</span>
                                        </div>
                                        <div class="flex-1">
                                            <textarea ref="commentInput" v-model="newComment"
                                                placeholder="Write a comment..."
                                                class="w-full border-gray-100 bg-neutral-50/50 rounded-2xl p-4 text-sm focus:ring-2 focus:ring-agri-forest/20 focus:border-agri-forest focus:bg-white transition-all resize-none shadow-inner"
                                                rows="3"></textarea>
                                            <div class="flex items-center justify-end mt-3 space-x-3">
                                                <button @click="newComment = ''"
                                                    class="px-5 py-2 text-[13px] font-bold text-gray-500 hover:bg-neutral-100 rounded-full transition-all">Cancel</button>
                                                <button @click="submitComment"
                                                    :disabled="!newComment.trim() || submittingComment"
                                                    class="px-8 py-2.5 text-[13px] font-black text-white bg-agri-forest hover:bg-agri-lush disabled:bg-neutral-200 rounded-full transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0 disabled:translate-y-0 disabled:shadow-none">
                                                    {{ submittingComment ? 'Posting...' : 'Post Comment' }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Comments List -->
                                <div v-if="loadingComments" class="space-y-6">
                                    <div v-for="i in 3" :key="i" class="animate-pulse flex items-start space-x-4">
                                        <div class="w-10 h-10 bg-neutral-100 rounded-full"></div>
                                        <div class="flex-1 space-y-3">
                                            <div class="h-3 bg-neutral-100 rounded w-1/4"></div>
                                            <div class="h-10 bg-neutral-100 rounded-xl w-full"></div>
                                        </div>
                                    </div>
                                </div>

                                <div v-else-if="comments.length > 0" class="space-y-6">
                                    <CommentItem v-for="comment in comments" :key="comment.id" :comment="comment"
                                        :post-id="post.id" :current-user="currentUser" :depth="0"
                                        @delete="deleteComment" @refresh="fetchPost" />
                                </div>

                                <div v-else
                                    class="text-center py-12 bg-neutral-50/30 rounded-2xl border border-dashed border-neutral-200">
                                    <p class="text-gray-400 font-medium text-sm">Be the first to share your thoughts!
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Error State -->
                        <div v-else class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                            <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-black text-gray-900 mb-2">Pulse not found</h3>
                            <p class="text-gray-500 mb-8 max-w-xs mx-auto text-sm font-medium leading-relaxed">This post
                                might
                                have been removed or you may not have access to see it.</p>
                            <button @click="$router.push('/agripulse')"
                                class="px-8 py-3 bg-agri-forest text-white rounded-full font-black text-sm hover:bg-agri-lush transition-all shadow-lg hover:shadow-xl hover:-translate-y-1 active:translate-y-0">
                                Return to Feed
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar: Leadership Updates -->
                <div class="hidden lg:block lg:col-span-4 self-start">
                    <!-- Anchor margin for vertical alignment with main content -->
                    <div class="pt-[44px]"></div>
                    <div class="sticky top-6 space-y-5">
                        <div
                            class="bg-white/70 backdrop-blur-md rounded-2xl shadow-sm border border-white p-5 group/sidebar relative overflow-hidden">
                            <div class="flex items-center justify-between mb-5">
                                <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em]">Leadership
                                    Updates
                                </h3>
                                <div
                                    class="w-2 h-2 rounded-full bg-agri-forest animate-pulse shadow-[0_0_8px_rgba(23,73,56,0.5)]">
                                </div>
                            </div>

                            <div v-if="loadingSuperAdminPosts" class="space-y-5">
                                <div v-for="i in 2" :key="i" class="animate-pulse">
                                    <div class="h-3 bg-neutral-100 rounded w-full mb-3"></div>
                                    <div class="h-2 bg-neutral-100 rounded w-2/3"></div>
                                </div>
                            </div>

                            <div v-else-if="superAdminPosts.length > 0" class="space-y-6">
                                <router-link v-for="post in superAdminPosts" :key="post.id"
                                    :to="`/agripulse/post/${post.id}`" class="block group/link">
                                    <p
                                        class="text-[13px] font-bold text-gray-800 line-clamp-2 leading-relaxed group-hover/link:text-agri-forest transition-colors">
                                        {{ post.content }}
                                    </p>
                                    <div class="flex items-center mt-2.5 space-x-2">
                                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">{{
                                            formatTime(post.created_at)
                                        }}</p>
                                        <div class="w-1 h-1 rounded-full bg-neutral-200"></div>
                                        <p class="text-[10px] text-agri-forest/70 font-black uppercase tracking-widest">
                                            Pulse
                                        </p>
                                    </div>
                                </router-link>
                            </div>

                            <div v-else
                                class="text-center py-10 bg-neutral-50/50 rounded-xl border border-dashed border-neutral-100">
                                <p class="text-[11px] text-gray-400 font-black uppercase tracking-widest">No updates yet
                                </p>
                            </div>
                        </div>

                        <!-- Community Discovery Card -->
                        <div
                            class="bg-gradient-to-br from-agri-forest to-agri-lush rounded-2xl p-6 text-white shadow-xl overflow-hidden relative group cursor-pointer hover:shadow-agri-forest/20 transition-all duration-500">
                            <div
                                class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-1000">
                            </div>
                            <div class="relative z-10">
                                <h4 class="text-[10px] font-black uppercase tracking-[0.2em] mb-2 text-white/70">
                                    Community Focus
                                </h4>
                                <p class="text-sm font-bold leading-relaxed mb-5">Discover insights from global
                                    agricultural
                                    experts.</p>
                                <button @click="$router.push('/agripulse')"
                                    class="w-full py-3 bg-white text-agri-forest rounded-full text-[11px] font-black uppercase tracking-widest hover:bg-agri-sand transition-all shadow-lg active:scale-95">
                                    Explore More
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
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
const comments = ref([])
const loadingComments = ref(false)
const newComment = ref('')
const submittingComment = ref(false)
const commentInput = ref(null)
const showRepostDropdown = ref(false)
const showRepostModal = ref(false)
const repostContent = ref('')
const submittingRepost = ref(false)

// Leadership Updates Sidebar
const superAdminPosts = ref([])
const loadingSuperAdminPosts = ref(true)

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
        if (window.notify) window.notify.success('Reposted Successfully', 'Post reposted to your network.')
    } catch (error) {
        console.error('Instant repost failed:', error)
        if (window.notify) window.notify.error('Almost there!', 'Failed to repost pulse.')
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
        if (window.notify) window.notify.success('Reposted with Thoughts', 'Post reposted successfully!')
    } catch (error) {
        console.error('Repost failed:', error)
        if (window.notify) window.notify.error('Almost there!', 'Could not repost.')
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
        if (window.notify) window.notify.success('Comment Posted', 'Your thoughts are now part of the conversation.')
    } catch (error) {
        console.error('Comment submit failed:', error)
        if (window.notify) window.notify.error('Almost there!', 'We couldn\'t add your comment.')
    } finally {
        submittingComment.value = false
    }
}

const deleteComment = async (commentId) => {
    try {
        await axios.delete(`/api/agripulse/comments/${commentId}`)
        await fetchPost() // Refresh to remove deleted comment
        if (window.notify) window.notify.success('Comment Deleted', 'Your comment has been removed.')
    } catch (error) {
        console.error('Delete comment failed:', error)
        if (window.notify) window.notify.error('Almost there!', 'We couldn\'t remove your comment.')
    }
}

const handleDelete = async () => {
    try {
        await axios.delete(`/api/agripulse/posts/${post.value.id}`)
        if (window.notify) window.notify.success('Post Deleted', 'Your post has been removed.')
        router.push('/agripulse')
    } catch (error) {
        console.error('Failed to delete post:', error)
        if (window.notify) window.notify.error('Almost there!', 'We couldn\'t remove your post.')
    }
}

onMounted(() => {
    fetchCurrentUser()
    fetchPost()
    fetchSuperAdminPosts()
})

const viewOriginalPost = () => {
    if (post.value?.original_post) {
        router.push(`/agripulse/post/${post.value.original_post.id}`)
    }
}

const viewOriginalAuthorProfile = () => {
    if (post.value?.original_post?.user) {
        router.push(`/agripulse/profile/${post.value.original_post.user.id}`)
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

.animate-slideDown {
    animation: slideDown 0.2s ease-out;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-clamp: 2;
}
</style>
