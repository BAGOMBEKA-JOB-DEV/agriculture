<template>
  <div class="space-y-8">
    <!-- Quick Stats Grid - 9 Small Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
      <div v-for="stat in detailedStats" :key="stat.label"
        :class="['bg-gradient-to-br backdrop-blur-md p-5 rounded-3xl border transition-all duration-500 group cursor-default relative overflow-hidden hover:-translate-y-1', stat.bgClass, stat.borderClass]">
        <div
          :class="['absolute -top-10 -right-10 w-24 h-24 rounded-full blur-xl transition-all duration-700 group-hover:scale-110', stat.glowClass]">
        </div>

        <div class="relative z-10 flex flex-col items-start space-y-3">
          <div
            :class="['p-2.5 rounded-xl border border-white/20 transition-all duration-700 shadow-lg group-hover:scale-105', stat.iconBg]">
            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" :d="stat.iconPath"></path>
            </svg>
          </div>
          <div>
            <p class="text-2xl font-black tracking-tight group-hover:scale-[1.05] transition-transform duration-500 origin-left"
              :class="stat.textColor">
              {{ stat.value }}
            </p>
            <h4 class="text-[10px] font-bold uppercase tracking-wider mt-1" :class="stat.labelColor">{{ stat.label }}
            </h4>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Recent Activity Timeline -->
      <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
        <div class="p-6 border-b border-gray-50 flex items-center justify-between">
          <h2 class="text-lg font-black text-gray-900">Recent Actions</h2>
          <button @click="$emit('view-logs')"
            class="text-xs font-bold text-agri-forest hover:underline uppercase tracking-widest">View All</button>
        </div>
        <div class="p-6 flex-1">
          <div v-if="loading" class="flex items-center justify-center py-12">
            <div class="w-8 h-8 border-4 border-agri-forest/20 border-t-agri-forest rounded-full animate-spin"></div>
          </div>
          <div v-else-if="logs.length === 0" class="text-center py-12 text-gray-400 font-medium">
            No actions recorded yet
          </div>
          <div v-else class="space-y-6">
            <div v-for="log in logs" :key="log.id" class="flex gap-4">
              <div class="relative">
                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center z-10 relative">
                  <img :src="log.moderator.avatar_url" class="w-full h-full rounded-xl object-cover"
                    v-if="log.moderator.avatar_url">
                  <span v-else class="text-xs font-bold text-gray-400">{{ log.moderator.name[0] }}</span>
                </div>
                <div v-if="log !== logs[logs.length - 1]"
                  class="absolute top-10 bottom-0 left-1/2 -translate-x-1/2 w-0.5 bg-gray-100 -mb-6"></div>
              </div>
              <div class="flex-1 pb-6 border-b border-gray-50 last:border-0 last:pb-0">
                <div class="flex items-center justify-between mb-1">
                  <span class="text-sm font-bold text-gray-900">{{ log.moderator.name }}</span>
                  <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{
                    formatTime(log.created_at) }}</span>
                </div>
                <p class="text-sm text-gray-500 leading-relaxed" v-html="formatAction(log)"></p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Tips / System Health -->
      <div class="space-y-6">
        <div class="bg-agri-forest p-8 rounded-3xl text-white shadow-xl relative overflow-hidden group">
          <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:scale-110 transition-transform duration-500">
            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2L1 21h22L12 2zm0 3.45L20.1 19H3.9L12 5.45zM11 16h2v2h-2v-2zm0-7h2v5h-2V9z" />
            </svg>
          </div>
          <h2 class="text-2xl font-black mb-4 relative z-10 leading-tight">Everything is running smoothly</h2>
          <p class="text-white/80 font-medium mb-6 relative z-10">
            The platform is healthy. No problems found in the last 24 hours.
          </p>
          <div class="flex items-center gap-4 relative z-10">
            <div
              class="px-4 py-2 bg-white/20 rounded-xl backdrop-blur-md border border-white/10 text-xs font-bold uppercase tracking-widest">
              Uptime: 99.9%
            </div>
          </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-black text-gray-400 uppercase tracking-[0.2em]">Urgent Tasks</h2>
            <span v-if="urgentTasks.length > 0"
              class="px-2 py-1 bg-red-50 text-red-600 text-[10px] font-black uppercase tracking-wider rounded-lg border border-red-100">{{
                urgentTasks.length }} Pending</span>
          </div>

          <div v-if="urgentTasks.length === 0" class="py-8 text-center">
            <div class="w-12 h-12 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-3">
              <svg class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">All caught up!</p>
          </div>

          <div v-else class="space-y-4">
            <div v-for="task in urgentTasks" :key="task.id"
              class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100 hover:border-agri-forest/30 transition-colors group">
              <div class="flex items-center gap-3">
                <div
                  class="w-8 h-8 rounded-lg bg-red-100 text-red-500 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2L1 21h22L12 2zm0 3.45L20.1 19H3.9L12 5.45zM11 16h2v2h-2v-2zm0-7h2v5h-2V9z" />
                  </svg>
                </div>
                <div class="min-w-0">
                  <p class="text-xs font-bold text-gray-900 truncate max-w-[150px]">Report: {{ task.reason }}</p>
                  <p class="text-[10px] text-gray-500">{{ formatTime(task.created_at) }} • {{ task.reporter?.name ||
                    'Unknown' }}</p>
                </div>
              </div>
              <button @click="$emit('view-report', task.id)"
                class="px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-[10px] font-black text-agri-forest uppercase tracking-widest hover:bg-agri-forest hover:text-white hover:border-agri-forest transition-all shadow-sm whitespace-nowrap">
                Check
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'

dayjs.extend(relativeTime)

const loading = ref(true)
const statsData = ref({})
const logs = ref([])
const urgentTasks = ref([])

defineEmits(['view-report'])

const detailedStats = computed(() => [
  {
    label: 'Total Posts',
    value: statsData.value.total_posts || 0,
    iconPath: 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2z',
    bgClass: 'from-blue-400/10 to-blue-500/5',
    borderClass: 'border-blue-200/30',
    glowClass: 'bg-blue-500/10',
    iconBg: 'bg-blue-500',
    textColor: 'text-blue-700',
    labelColor: 'text-blue-600/70'
  },
  {
    label: 'Active Posts',
    value: statsData.value.active_posts || 0,
    iconPath: 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z',
    bgClass: 'from-green-400/10 to-green-500/5',
    borderClass: 'border-green-200/30',
    glowClass: 'bg-green-500/10',
    iconBg: 'bg-green-500',
    textColor: 'text-green-700',
    labelColor: 'text-green-600/70'
  },
  {
    label: 'Comments',
    value: statsData.value.total_comments || 0,
    iconPath: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
    bgClass: 'from-purple-400/10 to-purple-500/5',
    borderClass: 'border-purple-200/30',
    glowClass: 'bg-purple-500/10',
    iconBg: 'bg-purple-500',
    textColor: 'text-purple-700',
    labelColor: 'text-purple-600/70'
  },
  {
    label: 'Active Reports',
    value: statsData.value.pending_reports || 0,
    iconPath: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
    bgClass: 'from-red-400/10 to-red-500/5',
    borderClass: 'border-red-200/30',
    glowClass: 'bg-red-500/10',
    iconBg: 'bg-red-500',
    textColor: 'text-red-700',
    labelColor: 'text-red-600/70'
  },
  {
    label: 'Resolved',
    value: statsData.value.resolved_reports || 0,
    iconPath: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    bgClass: 'from-emerald-400/10 to-emerald-500/5',
    borderClass: 'border-emerald-200/30',
    glowClass: 'bg-emerald-500/10',
    iconBg: 'bg-emerald-500',
    textColor: 'text-emerald-700',
    labelColor: 'text-emerald-600/70'
  },
  {
    label: 'New Today',
    value: statsData.value.new_posts_today || 0,
    iconPath: 'M12 6v6m0 0v6m0-6h6m-6 0H6',
    bgClass: 'from-orange-400/10 to-orange-500/5',
    borderClass: 'border-orange-200/30',
    glowClass: 'bg-orange-500/10',
    iconBg: 'bg-orange-500',
    textColor: 'text-orange-700',
    labelColor: 'text-orange-600/70'
  },
  {
    label: 'Hidden Posts',
    value: statsData.value.hidden_posts || 0,
    iconPath: 'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21',
    bgClass: 'from-gray-400/10 to-gray-500/5',
    borderClass: 'border-gray-200/30',
    glowClass: 'bg-gray-500/10',
    iconBg: 'bg-gray-500',
    textColor: 'text-gray-700',
    labelColor: 'text-gray-600/70'
  },
  {
    label: 'Active Users',
    value: statsData.value.active_users || 0,
    iconPath: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
    bgClass: 'from-indigo-400/10 to-indigo-500/5',
    borderClass: 'border-indigo-200/30',
    glowClass: 'bg-indigo-500/10',
    iconBg: 'bg-indigo-500',
    textColor: 'text-indigo-700',
    labelColor: 'text-indigo-600/70'
  },
  {
    label: 'Warned Users',
    value: statsData.value.warned_users || 0,
    iconPath: 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636',
    bgClass: 'from-yellow-400/10 to-yellow-500/5',
    borderClass: 'border-yellow-200/30',
    glowClass: 'bg-yellow-500/10',
    iconBg: 'bg-yellow-500',
    textColor: 'text-yellow-700',
    labelColor: 'text-yellow-600/70'
  },
  {
    label: 'Total Likes',
    value: statsData.value.total_likes || 0,
    iconPath: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
    bgClass: 'from-pink-400/10 to-pink-500/5',
    borderClass: 'border-pink-200/30',
    glowClass: 'bg-pink-500/10',
    iconBg: 'bg-pink-500',
    textColor: 'text-pink-700',
    labelColor: 'text-pink-600/70'
  },
  {
    label: 'Total Reposts',
    value: statsData.value.total_reposts || 0,
    iconPath: 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
    bgClass: 'from-cyan-400/10 to-cyan-500/5',
    borderClass: 'border-cyan-200/30',
    glowClass: 'bg-cyan-500/10',
    iconBg: 'bg-cyan-500',
    textColor: 'text-cyan-700',
    labelColor: 'text-cyan-600/70'
  },
  {
    label: 'Flagged Posts',
    value: statsData.value.flagged_posts || 0,
    iconPath: 'M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-7h1',
    bgClass: 'from-amber-400/10 to-amber-500/5',
    borderClass: 'border-amber-200/30',
    glowClass: 'bg-amber-500/10',
    iconBg: 'bg-amber-500',
    textColor: 'text-amber-700',
    labelColor: 'text-amber-600/70'
  },
  {
    label: 'Deleted Posts',
    value: statsData.value.deleted_posts || 0,
    iconPath: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
    bgClass: 'from-rose-400/10 to-rose-500/5',
    borderClass: 'border-rose-200/30',
    glowClass: 'bg-rose-500/10',
    iconBg: 'bg-rose-500',
    textColor: 'text-rose-700',
    labelColor: 'text-rose-600/70'
  },
  {
    label: 'Suspended Users',
    value: statsData.value.suspended_users || 0,
    iconPath: 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 715.636 5.636m12.728 12.728L5.636 5.636',
    bgClass: 'from-slate-400/10 to-slate-500/5',
    borderClass: 'border-slate-200/30',
    glowClass: 'bg-slate-500/10',
    iconBg: 'bg-slate-500',
    textColor: 'text-slate-700',
    labelColor: 'text-slate-600/70'
  },
  {
    label: 'Total Users',
    value: statsData.value.total_users || 0,
    iconPath: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
    bgClass: 'from-teal-400/10 to-teal-500/5',
    borderClass: 'border-teal-200/30',
    glowClass: 'bg-teal-500/10',
    iconBg: 'bg-teal-500',
    textColor: 'text-teal-700',
    labelColor: 'text-teal-600/70'
  }
])



const fetchData = async () => {
  try {
    const response = await axios.get('/api/agripulse/moderation/dashboard')
    statsData.value = response.data.stats
    logs.value = response.data.recent_logs
    urgentTasks.value = response.data.latest_reports || []
  } catch (error) {
    console.error('Failed to fetch stats:', error)
  } finally {
    loading.value = false
  }
}

const formatTime = (time) => dayjs(time).fromNow()

const formatAction = (log) => {
  const metadata = log.metadata || {}
  const subjectName = metadata.subject_name || 'item'
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

onMounted(fetchData)
</script>
