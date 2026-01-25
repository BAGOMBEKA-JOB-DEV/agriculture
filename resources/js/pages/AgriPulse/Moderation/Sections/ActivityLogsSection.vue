<template>
    <div class="space-y-6">
        <!-- Premium Metric Cards Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div v-for="stat in logStats" :key="stat.label"
                class="bg-gradient-to-br backdrop-blur-md p-5 rounded-3xl border transition-all duration-500 group cursor-default relative overflow-hidden hover:-translate-y-1"
                :class="[stat.bgClass, stat.borderClass]">
                <div class="absolute -top-10 -right-10 w-24 h-24 rounded-full blur-xl transition-all duration-700 group-hover:scale-110"
                    :class="stat.glowClass">
                </div>

                <div class="relative z-10 flex flex-col items-start space-y-3">
                    <div class="p-2.5 rounded-xl border border-white/20 transition-all duration-700 shadow-lg group-hover:scale-105"
                        :class="stat.iconBg">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" :d="stat.iconPath">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-black tracking-tight group-hover:scale-[1.05] transition-transform duration-500 origin-left"
                            :class="stat.textColor">
                            {{ stat.value }}
                        </p>
                        <h4 class="text-[9px] font-bold uppercase tracking-wider mt-1" :class="stat.labelColor">{{
                            stat.label }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-gray-100 overflow-hidden flex flex-col h-full relative">
            <!-- Header -->
            <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-white sticky top-0 z-20">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 tracking-tight">Activity Logs</h2>
                    <p class="text-[11px] text-gray-400 font-semibold uppercase tracking-wider mt-1.5">Audit trail of
                        all
                        moderation actions</p>
                </div>
                <div class="flex items-center gap-4">
                    <!-- Search -->
                    <div class="relative group">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-agri-forest transition-colors"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input v-model="search" type="text" placeholder="Search moderator..."
                            class="pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-agri-forest/10 focus:bg-white text-[10px] font-bold text-gray-700 w-48 transition-all placeholder:font-medium placeholder:text-gray-400"
                            @input="debouncedFetch">
                    </div>

                    <!-- Action Filter -->
                    <select v-model="filterAction"
                        class="bg-gray-50 border border-gray-100 rounded-xl text-[10px] font-black uppercase tracking-widest text-gray-600 py-2.5 pl-4 pr-10 focus:ring-2 focus:ring-agri-forest/10"
                        @change="fetchLogs">
                        <option value="">All Actions</option>
                        <option value="hide_post">Hide Post</option>
                        <option value="delete_post">Delete Post</option>
                        <option value="warn_user">Warn User</option>
                        <option value="resolve_report">Resolve Report</option>
                        <option value="suspend_user">Suspend User</option>
                    </select>
                    <button @click="fetchLogs"
                        class="p-2.5 text-gray-400 hover:text-agri-forest hover:bg-gray-50 rounded-xl transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="flex-1 overflow-auto">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="bg-white border-b border-gray-50 sticky top-0 z-10">
                            <th class="px-8 py-4 w-16">
                                <input type="checkbox" :checked="logs.length > 0 && selectedIds.length === logs.length"
                                    @change="toggleSelectAll"
                                    class="w-4 h-4 rounded border-gray-300 text-agri-forest focus:ring-agri-forest cursor-pointer">
                            </th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                                Moderator
                            </th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] w-1/2">
                                Action Detail</th>
                            <th
                                class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">
                                Date</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] w-20">
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-if="loading" v-for="i in 10" :key="i" class="animate-pulse">
                            <td colspan="4" class="px-8 py-6">
                                <div class="h-4 bg-gray-100 rounded-full w-full"></div>
                            </td>
                        </tr>
                        <tr v-else-if="logs.length === 0">
                            <td colspan="4"
                                class="px-8 py-12 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">
                                No
                                activity found</td>
                        </tr>
                        <tr v-else v-for="log in logs" :key="log.id" @click="selectedLog = log"
                            class="group hover:bg-gray-50/50 transition-colors cursor-pointer">
                            <td class="px-8 py-5" @click.stop>
                                <input type="checkbox" v-model="selectedIds" :value="log.id"
                                    class="w-4 h-4 rounded border-gray-300 text-agri-forest focus:ring-agri-forest cursor-pointer">
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <img :src="log.moderator?.avatar_url" :alt="log.moderator?.name"
                                        class="w-8 h-8 rounded-full object-cover bg-agri-forest/10">
                                    <div>
                                        <span class="block text-xs font-bold text-gray-900">{{ log.moderator?.name ||
                                            'Unknown' }}</span>
                                        <span
                                            class="text-[9px] text-gray-400 uppercase tracking-widest">Moderator</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <p class="text-sm text-gray-700 font-medium leading-relaxed" v-html="formatAction(log)">
                                </p>
                            </td>
                            <td class="px-8 py-5 text-right text-[10px] font-bold text-gray-400">
                                {{ dayjs(log.created_at).format('MMM D, YY • h:mm A') }}
                            </td>
                            <td class="px-8 py-5 text-right relative">
                                <button class="p-2 text-gray-300 hover:text-agri-forest transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-8 py-4 border-t border-gray-50 flex items-center justify-between bg-white text-[10px] font-black uppercase tracking-widest text-gray-400"
                v-if="pagination.links">
                <div>{{ pagination.from || 0 }}-{{ pagination.to || 0 }} / {{ pagination.total || 0 }}</div>
                <div class="flex items-center gap-1">
                    <button v-for="link in pagination.links" :key="link.label" @click="fetchLogs(link.url)"
                        v-html="link.label"
                        class="min-w-[32px] h-8 flex items-center justify-center rounded-lg transition-all border"
                        :class="link.active
                            ? 'bg-agri-forest text-white border-agri-forest'
                            : 'bg-white text-gray-400 border-gray-100 hover:text-agri-forest hover:border-agri-forest'"
                        :disabled="!link.url"></button>
                </div>
            </div>

            <!-- Log Detail Slide-over (Expanded) -->
            <div v-if="selectedLog" class="fixed inset-0 z-[100] flex justify-end overflow-hidden"
                @click="selectedLog = null">
                <div class="absolute inset-0 bg-black/20 backdrop-blur-sm transition-opacity"></div>

                <main
                    class="relative w-full max-w-4xl bg-white border-l border-gray-100 h-full flex flex-col animate-slide-in"
                    @click.stop>
                    <!-- Header -->
                    <div class="p-8 border-b border-gray-50 flex items-center justify-between shrink-0 bg-gray-50/50">
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Action Detail
                            </p>
                            <h3 class="text-xl font-bold text-gray-900 leading-tight">Log #{{ selectedLog.id }}</h3>
                        </div>
                        <button @click="selectedLog = null"
                            class="p-2 text-gray-400 hover:text-gray-900 hover:bg-gray-100 rounded-xl transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto p-8 space-y-8 custom-scrollbar">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Left Column -->
                            <div class="space-y-8">
                                <!-- Action Summary -->
                                <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm">
                                    <div class="flex items-start gap-4">
                                        <div
                                            class="w-12 h-12 rounded-2xl bg-agri-forest/10 flex items-center justify-center text-agri-forest shrink-0">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-bold text-gray-900 mb-1">Action Summary</h4>
                                            <p class="text-sm text-gray-600 leading-relaxed"
                                                v-html="formatAction(selectedLog)"></p>
                                            <p class="text-xs text-gray-400 font-medium mt-2">{{
                                                dayjs(selectedLog.created_at).format('MMMM D, YYYY [at] h:mm:ss A') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Moderator Info -->
                                <div>
                                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-4">
                                        Executed By
                                    </h4>
                                    <div
                                        class="flex items-center gap-4 bg-gray-50 rounded-2xl p-4 border border-gray-100">
                                        <img :src="selectedLog.moderator?.avatar_url" :alt="selectedLog.moderator?.name"
                                            class="w-10 h-10 rounded-xl object-cover bg-white border border-gray-100">
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">{{ selectedLog.moderator?.name }}
                                            </p>
                                            <p class="text-[10px] text-gray-500 uppercase tracking-widest">Moderator ID:
                                                {{
                                                    selectedLog.moderator_id }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Content Author Info (New) -->
                                <div v-if="selectedLog.subject?.user">
                                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Content
                                        Author</h4>
                                    <div
                                        class="flex items-center gap-4 bg-blue-50/50 rounded-2xl p-4 border border-blue-100">
                                        <img :src="selectedLog.subject.user?.avatar_url"
                                            :alt="selectedLog.subject.user?.name"
                                            class="w-10 h-10 rounded-xl object-cover bg-white border border-blue-100">
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">{{ selectedLog.subject.user.name
                                            }}
                                            </p>
                                            <p class="text-[10px] text-gray-500 uppercase tracking-widest">{{
                                                selectedLog.subject.user.email }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-8">
                                <!-- Target/Subject Info -->
                                <div v-if="selectedLog.metadata">
                                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Target
                                        Information</h4>
                                    <div
                                        class="bg-white rounded-2xl border border-gray-100 overflow-hidden divide-y divide-gray-50">
                                        <div class="p-4 flex flex-col gap-1">
                                            <span
                                                class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Type</span>
                                            <span class="text-xs font-bold text-gray-900">{{
                                                selectedLog.subject_type?.split('\\').pop() || 'Unknown' }}</span>
                                        </div>
                                        <div class="p-4 flex flex-col gap-1" v-if="selectedLog.metadata.subject_name">
                                            <span
                                                class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Name</span>
                                            <span class="text-xs font-bold text-gray-900">{{
                                                selectedLog.metadata.subject_name }}</span>
                                        </div>
                                        <div class="p-4 flex flex-col gap-1" v-if="selectedLog.subject?.created_at">
                                            <span
                                                class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Created
                                                At</span>
                                            <span class="text-xs font-bold text-gray-900">{{
                                                dayjs(selectedLog.subject.created_at).format('MMM D, YYYY h:mm A')
                                            }}</span>
                                        </div>
                                        <div class="p-4 flex flex-col gap-1" v-if="selectedLog.metadata.report_reason">
                                            <span
                                                class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Reason</span>
                                            <span class="text-xs font-bold text-red-600 uppercase">{{
                                                selectedLog.metadata.report_reason }}</span>
                                        </div>
                                        <div class="p-4 flex flex-col gap-1">
                                            <span
                                                class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Subject
                                                ID</span>
                                            <span class="text-xs font-mono text-gray-500">#{{ selectedLog.subject_id
                                            }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Full Content Display (Full Width) -->
                        <div v-if="selectedLog.metadata.content_full || selectedLog.subject?.content" class="mt-8">
                            <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Full Content
                                Context</h4>
                            <div
                                class="p-6 bg-gray-50 rounded-3xl text-sm text-gray-700 leading-relaxed whitespace-pre-wrap font-medium border border-gray-100">
                                {{ selectedLog.metadata.content_full || selectedLog.subject?.content }}
                            </div>
                        </div>
                        <!-- Fallback Snippet -->
                        <div v-else-if="selectedLog.metadata.content_snippet" class="mt-8">
                            <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Content Snippet
                            </h4>
                            <div class="p-6 bg-gray-50 rounded-3xl text-sm text-gray-600 italic border border-gray-100">
                                "{{ selectedLog.metadata.content_snippet }}"
                                <p
                                    class="text-[10px] text-gray-400 mt-2 not-italic font-bold uppercase tracking-widest">
                                    *
                                    Full content unavailable for this log</p>
                            </div>
                        </div>

                    </div>
                </main>
            </div>
        </div>

        <!-- Confirm Modal -->
        <Modal :is-open="modal.open" :title="modal.title" :type="modal.type" confirm-text="Confirm"
            @confirm="handleBulkAction" @cancel="modal.open = false" :loading="modal.loading">
            <p class="text-sm text-gray-600 mb-4">{{ modal.message }}</p>
        </Modal>

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
                                <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mb-0.5">Batch
                                    Actions</p>
                                <h4 class="text-xl font-bold text-white tracking-tight leading-none">
                                    {{ selectedIds.length }} Log<span v-if="selectedIds.length > 1">s</span>
                                    <span
                                        class="text-agri-lush animate-pulse ml-2 px-3 py-1 bg-agri-lush/10 rounded-lg">Selected</span>
                                </h4>
                            </div>
                        </div>
                    </div>

                    <div class="relative z-10 flex items-center space-x-3">
                        <!-- Delete Action -->
                        <button @click="openBulkActionModal('delete')" :disabled="isProcessing"
                            class="group/btn flex items-center space-x-3 px-6 py-4 bg-red-500/10 hover:bg-red-600 text-white border border-red-500/20 hover:border-red-600 rounded-2xl transition-all duration-500 disabled:opacity-50 active:scale-95 shadow-lg hover:shadow-red-600/40">
                            <div
                                class="w-8 h-8 rounded-xl bg-red-500/20 group-hover/btn:bg-white/20 flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4 text-red-400 group-hover/btn:text-white" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-widest">Delete</span>
                        </button>

                        <div class="h-10 w-px bg-white/10 mx-2"></div>

                        <button @click="selectedIds = []"
                            class="w-14 h-14 flex items-center justify-center bg-white/5 hover:bg-white/10 text-white/40 hover:text-white rounded-[1.25rem] border border-white/10 transition-all duration-500 group-hover:rotate-90">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import dayjs from 'dayjs'
import Modal from '@/components/Modal.vue'

const props = defineProps({
    globalStats: {
        type: Object,
        default: () => ({})
    }
})

const logStats = computed(() => [
    {
        label: 'Total Logs',
        value: props.globalStats.total_logs || 0,
        iconPath: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
        bgClass: 'from-blue-400/10 to-blue-500/5',
        borderClass: 'border-blue-200/30',
        glowClass: 'bg-blue-500/10',
        iconBg: 'bg-blue-500',
        textColor: 'text-blue-700',
        labelColor: 'text-blue-600/70'
    },
    {
        label: 'Actions Today',
        value: props.globalStats.logs_today || 0,
        iconPath: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        bgClass: 'from-green-400/10 to-green-500/5',
        borderClass: 'border-green-200/30',
        glowClass: 'bg-green-500/10',
        iconBg: 'bg-green-500',
        textColor: 'text-green-700',
        labelColor: 'text-green-600/70'
    },
    {
        label: 'Top Moderator',
        value: props.globalStats.top_moderator || 'None',
        iconPath: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        bgClass: 'from-purple-400/10 to-purple-500/5',
        borderClass: 'border-purple-200/30',
        glowClass: 'bg-purple-500/10',
        iconBg: 'bg-purple-500',
        textColor: 'text-purple-700', // Text size for names might need truncation or smaller font if long
        labelColor: 'text-purple-600/70'
    },
    {
        label: 'Suspensions',
        value: props.globalStats.suspended_users || 0,
        iconPath: 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636',
        bgClass: 'from-red-400/10 to-red-500/5',
        borderClass: 'border-red-200/30',
        glowClass: 'bg-red-500/10',
        iconBg: 'bg-red-500',
        textColor: 'text-red-700',
        labelColor: 'text-red-600/70'
    }
])

const loading = ref(true)
const logs = ref([])
const filterAction = ref('')
const search = ref('')
const selectedIds = ref([])
const isProcessing = ref(false)
const pagination = ref({})
const selectedLog = ref(null)

const modal = ref({
    open: false,
    title: '',
    message: '',
    type: 'primary',
    action: null,
    loading: false
})

const toggleSelectAll = () => {
    if (selectedIds.value.length === logs.value.length) {
        selectedIds.value = []
    } else {
        selectedIds.value = logs.value.map(l => l.id)
    }
}

const availableBulkActions = computed(() => {
    if (selectedIds.value.length === 0) return []
    return ['delete']
})

const openBulkActionModal = (action) => {
    if (action === 'delete') {
        modal.value = {
            open: true,
            title: 'Bulk Delete Logs',
            message: `Are you sure you want to permanently delete ${selectedIds.value.length} activity logs? This action cannot be undone.`,
            type: 'danger',
            action: 'delete',
            loading: false
        }
    }
}

const handleBulkAction = async () => {
    modal.value.loading = true
    try {
        await axios.post('/api/agripulse/moderation/bulk-action', {
            ids: selectedIds.value,
            action: 'delete',
            type: 'log'
        })

        if (window.notify) window.notify.success('Bulk Action Complete', `${selectedIds.value.length} logs deleted.`)
        selectedIds.value = []
        modal.value.open = false
        fetchLogs()
    } catch (error) {
        if (window.notify) window.notify.error('Bulk Action Failed', 'Could not delete logs.')
    } finally {
        modal.value.loading = false
    }
}

const debounce = (fn, delay) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
};

const fetchLogs = async (url) => {
    if (typeof url !== 'string') {
        url = '/api/agripulse/moderation/logs'
    }
    loading.value = true
    try {
        const params = {
            action: filterAction.value,
            search: search.value
        }
        const response = await axios.get(url, { params })
        logs.value = response.data.data
        pagination.value = { from: response.data.from, to: response.data.to, total: response.data.total, links: response.data.links }
    } catch (error) {
        console.error('Failed to fetch logs:', error)
    } finally {
        loading.value = false
    }
}

const debouncedFetch = debounce(() => fetchLogs(), 500)

const formatAction = (log) => {
    const metadata = log.metadata || {}
    const subjectName = metadata.subject_name || log.subject_id
    const snippet = metadata.content_snippet ? `"${metadata.content_snippet}"` : ''
    const reason = metadata.report_reason ? `for <strong>${metadata.report_reason}</strong>` : ''

    const actions = {
        'hide_post': `Hid post ${snippet}`,
        'delete_post': `Soft-deleted post ${snippet}`,
        'restore_post': `Restored post ${snippet}`,
        'pin_post': `Pinned post ${snippet}`,
        'unpin_post': `Unpinned post ${snippet}`,
        'flag_post': `Flagged post ${snippet}`,
        'delete_comment': `Deleted comment ${snippet}`,
        'hide_comment': `Hid comment ${snippet}`,
        'restore_comment': `Restored comment`,
        'resolve_report': `Resolved report on ${subjectName}`,
        'dismiss_report': `Dismissed report on ${subjectName}`,
        'warn_user': `Issued warning to <strong>${subjectName}</strong> ${reason}`,
        'suspend_user': `Suspended user <strong>${subjectName}</strong>`
    }

    return actions[log.action] || log.action.replace(/_/g, ' ')
}

onMounted(() => fetchLogs())
</script>

<style scoped>
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
</style>
