<template>
  <div class="min-h-screen bg-gray-50/50 pb-12">
    <div class="container mx-auto px-6 relative z-10">
      <div class="space-y-6 pb-8">
        <!-- Header -->
        <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
            <div>
              <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Analytics & Insights</h2>
              <p class="text-sm text-gray-500 font-medium mt-1">Real-time overview of community activity and moderation
              </p>
            </div>
            <div class="flex items-center gap-3">
              <button @click="resetFilters"
                class="px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-gray-700 hover:bg-gray-100 transition-all flex items-center gap-2"
                title="Clear all filters">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span class="text-xs font-bold uppercase tracking-widest">Clear</span>
              </button>

              <button @click="showAdvancedFilters = !showAdvancedFilters"
                class="px-4 py-2.5 rounded-xl border transition-all flex items-center gap-2"
                :class="showAdvancedFilters ? 'bg-agri-forest text-white border-agri-forest' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100'">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                </svg>
                <span class="text-xs font-bold uppercase tracking-widest">{{ showAdvancedFilters ? 'Hide' : 'Show' }}
                  Filters</span>
              </button>
            </div>
          </div>

          <!-- Advanced Filter Panel -->
          <Transition enter-active-class="transition-all duration-300 ease-out"
            leave-active-class="transition-all duration-200 ease-in" enter-from-class="max-h-0 opacity-0"
            enter-to-class="max-h-96 opacity-100" leave-from-class="max-h-96 opacity-100"
            leave-to-class="max-h-0 opacity-0">
            <div v-if="showAdvancedFilters"
              class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 pt-6 border-t border-gray-100 overflow-hidden">
              <!-- Period Filter -->
              <div class="space-y-2">
                <label class="text-xs font-bold text-gray-600 uppercase tracking-widest">Time Period</label>
                <select v-model="filters.period" @change="fetchData"
                  class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-bold text-gray-700 outline-none cursor-pointer focus:ring-2 focus:ring-agri-forest/20 focus:border-agri-forest transition-all">
                  <option value="7">Last 7 Days</option>
                  <option value="30">Last 30 Days</option>
                  <option value="90">Last 90 Days</option>
                  <option value="365">Last Year</option>
                  <option value="custom">Custom Range</option>
                </select>
              </div>

              <!-- Report Status Filter -->
              <div class="space-y-2">
                <label class="text-xs font-bold text-gray-600 uppercase tracking-widest">Report Status</label>
                <select v-model="filters.report_status" @change="fetchData"
                  class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-bold text-gray-700 outline-none cursor-pointer focus:ring-2 focus:ring-agri-forest/20 focus:border-agri-forest transition-all">
                  <option value="all">All Reports</option>
                  <option value="pending">Pending</option>
                  <option value="resolved">Resolved</option>
                  <option value="dismissed">Dismissed</option>
                </select>
              </div>

              <!-- Custom Date Range -->
              <div v-if="filters.period === 'custom'"
                class="lg:col-span-2 space-y-2 bg-gray-50 p-4 rounded-xl border border-gray-200">
                <label class="text-xs font-bold text-gray-600 uppercase tracking-widest block mb-2">Custom Date
                  Range</label>
                <div class="flex items-center gap-3">
                  <input v-model="filters.start_date" @change="fetchData" type="date"
                    class="flex-1 bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-agri-forest/20">
                  <span class="text-gray-400 font-bold text-sm">TO</span>
                  <input v-model="filters.end_date" @change="fetchData" type="date"
                    class="flex-1 bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-agri-forest/20">
                </div>
              </div>
            </div>
          </Transition>

          <!-- Navigation Tabs -->
          <div class="flex items-center gap-2 mt-8 border-b border-gray-100 overflow-x-auto">
            <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id"
              class="px-6 py-3 text-xs font-black uppercase tracking-widest transition-all relative whitespace-nowrap"
              :class="activeTab === tab.id ? 'text-agri-forest' : 'text-gray-400 hover:text-gray-600'">
              {{ tab.label }}
              <div v-if="activeTab === tab.id"
                class="absolute bottom-0 left-0 w-full h-0.5 bg-agri-forest rounded-t-full"></div>
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="container mx-auto px-6 -mt-4 relative z-20 space-y-8">
      <!-- DASHBOARD CONTENT BASED ON TAB -->

      <!-- 1. OVERVIEW TAB -->
      <div v-if="activeTab === 'overview'" class="space-y-6 animate-in">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div v-for="stat in overviewStats" :key="stat.label"
            class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-start justify-between">
              <div>
                <p class="text-2xl font-black tracking-tight text-gray-900 group-hover:scale-105 transition-transform"
                  :class="stat.textColor">
                  {{ stat.value }}
                </p>
                <h4 class="text-[9px] font-bold uppercase tracking-wider mt-1 text-gray-400">{{ stat.label }}</h4>
              </div>
              <div class="p-2.5 rounded-xl transition-all" :class="stat.bgClass">
                <svg class="w-4 h-4" :class="stat.iconColor" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" :d="stat.iconPath"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Post Activity Chart -->
        <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-lg">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Post Volume Activity</h3>
              <p class="text-xs text-gray-400 font-medium mt-1">Daily post creation trends</p>
            </div>
          </div>
          <div class="h-96">
            <Line v-if="postChartData" :data="postChartData" :options="lineChartOptions" />
            <div v-else class="h-full flex items-center justify-center bg-gray-50 rounded-2xl animate-pulse">
              <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Loading data...</span>
            </div>
          </div>
        </div>
      </div>

      <!-- 2. SAFETY & REPORTS TAB -->
      <div v-else-if="activeTab === 'safety'" class="space-y-6 animate-in">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
          <!-- Safety Specific Stats -->
          <div class="bg-red-50 p-6 rounded-3xl border border-red-100">
            <p class="text-xs font-bold text-red-400 uppercase tracking-widest mb-2">Total Reports</p>
            <p class="text-3xl font-black text-red-600">{{ metrics.total_reports }}</p>
          </div>
          <div class="bg-green-50 p-6 rounded-3xl border border-green-100">
            <p class="text-xs font-bold text-green-400 uppercase tracking-widest mb-2">Resolved</p>
            <p class="text-3xl font-black text-green-600">{{ metrics.resolved_reports }}</p>
          </div>
          <div class="bg-blue-50 p-6 rounded-3xl border border-blue-100">
            <p class="text-xs font-bold text-blue-400 uppercase tracking-widest mb-2">Resolution Rate</p>
            <p class="text-3xl font-black text-blue-600">{{ metrics.resolution_rate }}%</p>
          </div>
        </div>

        <!-- Report Trends Chart -->
        <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-lg">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Report Frequency</h3>
              <p class="text-xs text-gray-400 font-medium mt-1">Incoming safety reports over time</p>
            </div>
          </div>
          <div class="h-96">
            <Bar v-if="reportChartData" :data="reportChartData" :options="barChartOptions" />
            <div v-else class="h-full flex items-center justify-center bg-gray-50 rounded-2xl animate-pulse">
              <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Loading data...</span>
            </div>
          </div>
        </div>
      </div>

      <!-- 3. ENGAGEMENT TAB -->
      <div v-else-if="activeTab === 'engagement'" class="space-y-6 animate-in">
        <!-- New Stats Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
          <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Comments</p>
                <p class="text-3xl font-black text-gray-900 mt-2">{{ metrics.total_comments }}</p>
              </div>
              <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                  </path>
                </svg>
              </div>
            </div>
          </div>
          <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Likes</p>
                <p class="text-3xl font-black text-gray-900 mt-2">{{ metrics.total_likes }}</p>
              </div>
              <div class="p-3 bg-pink-50 text-pink-600 rounded-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                  </path>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Comment Trends -->
          <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-lg">
            <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6">Comment Volume</h3>
            <div class="h-80">
              <Line v-if="commentChartData" :data="commentChartData" :options="commentChartOptions" />
              <div v-else class="h-full flex items-center justify-center bg-gray-50 rounded-2xl animate-pulse">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Loading...</span>
              </div>
            </div>
          </div>

          <!-- Like Trends -->
          <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-lg">
            <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6">Like/Reaction Volume</h3>
            <div class="h-80">
              <Line v-if="reactionChartData" :data="reactionChartData" :options="reactionChartOptions" />
              <div v-else class="h-full flex items-center justify-center bg-gray-50 rounded-2xl animate-pulse">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Loading...</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 4. RISK ANALYSIS TAB -->
      <div v-else-if="activeTab === 'risk'" class="space-y-6 animate-in">
        <div class="bg-white rounded-3xl border border-gray-100 overflow-hidden flex flex-col shadow-lg">
          <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-red-50/30">
            <div>
              <h2 class="text-sm font-black text-red-900 uppercase tracking-widest">Most Reported Users</h2>
              <p class="text-[10px] text-red-400 font-bold uppercase tracking-widest mt-1">High priority accounts
                requiring investigation</p>
            </div>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-gray-50/50">
                  <th
                    class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                    User</th>
                  <th
                    class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 text-center">
                    Reports</th>
                  <th
                    class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 text-center">
                    Total Posts</th>
                  <th
                    class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 text-right">
                    Risk Level</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-50">
                <tr v-if="loadingUsers" v-for="i in 5" :key="i" class="animate-pulse">
                  <td colspan="4" class="px-8 py-6 h-16 bg-white">
                    <div class="h-4 bg-gray-100 rounded-full w-full"></div>
                  </td>
                </tr>
                <tr v-for="user in topUsers" :key="user.id" class="hover:bg-gray-50/50 transition-colors group">
                  <td class="px-8 py-5">
                    <div class="flex items-center gap-3">
                      <div
                        class="w-10 h-10 rounded-xl bg-agri-sand flex items-center justify-center flex-shrink-0 border border-gray-100">
                        <img v-if="user.avatar_url" :src="user.avatar_url" class="w-full h-full rounded-xl object-cover"
                          @error="$event.target.style.display = 'none'">
                        <span v-else class="text-sm font-black text-agri-forest">{{ user.name?.[0] || '?' }}</span>
                      </div>
                      <div class="flex flex-col">
                        <span
                          class="text-xs font-bold text-gray-900 leading-none mb-1 group-hover:text-agri-forest transition-colors">{{
                            user.name }}</span>
                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ user.email
                          }}</span>
                      </div>
                    </div>
                  </td>
                  <td class="px-8 py-5 text-center">
                    <span
                      class="text-sm font-black text-red-600 bg-red-50 px-3 py-1.5 rounded-xl border border-red-100">{{
                        user.posts_count }}</span>
                  </td>
                  <td class="px-8 py-5 text-center font-bold text-sm text-gray-900">
                    {{ user.all_posts_count || 0 }}
                  </td>
                  <td class="px-8 py-5 text-right">
                    <div class="inline-flex h-2.5 w-28 bg-gray-100 rounded-full overflow-hidden border border-gray-200">
                      <div class="bg-gradient-to-r from-orange-500 to-red-600 h-full transition-all duration-1000"
                        :style="{ width: `${Math.min(user.posts_count * 20, 100)}%` }"></div>
                    </div>
                  </td>
                </tr>
                <tr v-if="!loadingUsers && topUsers.length === 0">
                  <td colspan="4" class="px-8 py-20 text-center">
                    <div class="flex flex-col items-center justify-center">
                      <p class="text-sm font-black text-gray-400 uppercase tracking-widest">No Reported Users</p>
                      <p class="text-xs text-gray-400 mt-1">Community is clean for this period</p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import axios from 'axios'
import { useRouter, useRoute } from 'vue-router'
import {
  Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, BarElement, Title, Tooltip, Legend, Filler
} from 'chart.js'
import { Line, Bar } from 'vue-chartjs'

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, BarElement, Title, Tooltip, Legend, Filler)

const router = useRouter()
const route = useRoute()

const activeTab = ref(route.query.tab || 'overview')
const tabs = [
  { id: 'overview', label: 'Overview' },
  { id: 'safety', label: 'Safety & Reports' },
  { id: 'engagement', label: 'Engagement' },
  { id: 'risk', label: 'Risk Analysis' },
]

watch(activeTab, (newTab) => {
  router.replace({ query: { ...route.query, tab: newTab } })
})

const postChartData = ref(null)
const reportChartData = ref(null)
const commentChartData = ref(null)
const reactionChartData = ref(null)
const topUsers = ref([])
const showAdvancedFilters = ref(false)
const loadingUsers = ref(true)

const metrics = ref({
  total_posts: 0,
  total_comments: 0,
  total_likes: 0,
  total_reports: 0,
  resolved_reports: 0,
  response_rate: 94,
  resolution_rate: 98
})

const filters = ref({
  period: '30',
  report_status: 'all',
  start_date: '',
  end_date: ''
})

const overviewStats = computed(() => [
  {
    label: 'Total Posts',
    value: metrics.value.total_posts || 0,
    iconPath: 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2z',
    bgClass: 'bg-green-50',
    iconColor: 'text-green-600',
    textColor: 'text-green-700'
  },
  {
    label: 'Total Reports',
    value: metrics.value.total_reports || 0,
    iconPath: 'M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-7h1',
    bgClass: 'bg-red-50',
    iconColor: 'text-red-600',
    textColor: 'text-red-700'
  },
  {
    label: 'Response Speed',
    value: `${metrics.value.response_rate}%`,
    iconPath: 'M13 10V3L4 14h7v7l9-11h-7z',
    bgClass: 'bg-yellow-50',
    iconColor: 'text-yellow-600',
    textColor: 'text-yellow-700'
  },
  {
    label: 'Community Health',
    value: 'A+',
    iconPath: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    bgClass: 'bg-blue-50',
    iconColor: 'text-blue-600',
    textColor: 'text-blue-700'
  }
])

const resetFilters = () => {
  filters.value = { period: '30', report_status: 'all', start_date: '', end_date: '' }
  fetchData()
}

// Chart Options (Reusable logic)
const commonChartOptions = (colorHex, colorRgbs) => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
    tooltip: {
      backgroundColor: colorRgbs,
      padding: 12,
      titleColor: '#fff',
      bodyColor: '#fff',
      borderColor: colorHex,
      borderWidth: 1,
      displayColors: false,
      titleFont: { size: 12, weight: 'bold' },
      bodyFont: { size: 14, weight: 'bold' }
    }
  },
  scales: {
    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.03)', drawBorder: false }, ticks: { color: '#9CA3AF', font: { size: 11, weight: '600' } } },
    x: { grid: { display: false, drawBorder: false }, ticks: { color: '#9CA3AF', font: { size: 11, weight: '600' } } }
  },
  interaction: { mode: 'index', intersect: false }
})

const lineChartOptions = commonChartOptions('#114B31', 'rgba(17, 75, 49, 0.9)')
const barChartOptions = commonChartOptions('#DC2626', 'rgba(239, 68, 68, 0.9)')
const commentChartOptions = commonChartOptions('#7C3AED', 'rgba(124, 58, 237, 0.9)') // Purple
const reactionChartOptions = commonChartOptions('#DB2777', 'rgba(219, 39, 119, 0.9)') // Pink

const fetchData = async () => {
  try {
    loadingUsers.value = true
    const params = {
      period: filters.value.period,
      status: filters.value.report_status
    }

    if (filters.value.period === 'custom' && filters.value.start_date && filters.value.end_date) {
      params.start_date = filters.value.start_date
      params.end_date = filters.value.end_date
    }

    const response = await axios.get('/api/agripulse/moderation/analytics', { params })
    const { post_trends, report_trends, comment_trends, reaction_trends, top_reported_users, summary } = response.data

    if (summary) {
      metrics.value = {
        total_posts: summary.total_posts || 0,
        total_comments: summary.total_comments || 0,
        total_reports: summary.total_reports || 0,
        resolved_reports: summary.resolved_reports || 0,
        response_rate: summary.response_rate || 94,
        resolution_rate: summary.resolution_rate || 98,
        total_likes: reaction_trends?.reduce((acc, curr) => acc + curr.count, 0) || 0
      }
    }

    const mapTrends = (trends) => ({
      labels: trends.map(t => new Date(t.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })),
      data: trends.map(t => t.count)
    })

    const pTrends = mapTrends(post_trends || [])
    postChartData.value = {
      labels: pTrends.labels,
      datasets: [{
        label: 'Posts',
        data: pTrends.data,
        borderColor: '#114B31',
        backgroundColor: 'rgba(17, 75, 49, 0.05)',
        fill: true,
        tension: 0.4,
        borderWidth: 3,
        pointRadius: 3,
        pointHoverRadius: 6,
        pointBackgroundColor: '#114B31'
      }]
    }

    const rTrends = mapTrends(report_trends || [])
    reportChartData.value = {
      labels: rTrends.labels,
      datasets: [{
        label: 'Reports',
        data: rTrends.data,
        backgroundColor: '#EF4444',
        borderRadius: 6,
        borderSkipped: false
      }]
    }

    if (comment_trends) {
      const cTrends = mapTrends(comment_trends)
      commentChartData.value = {
        labels: cTrends.labels,
        datasets: [{
          label: 'Comments',
          data: cTrends.data,
          borderColor: '#7C3AED',
          backgroundColor: 'rgba(124, 58, 237, 0.05)',
          fill: true,
          tension: 0.4,
          borderWidth: 3,
          pointRadius: 3,
          pointBackgroundColor: '#7C3AED'
        }]
      }
    }

    if (reaction_trends) {
      const lTrends = mapTrends(reaction_trends)
      reactionChartData.value = {
        labels: lTrends.labels,
        datasets: [{
          label: 'Likes',
          data: lTrends.data,
          borderColor: '#DB2777',
          backgroundColor: 'rgba(219, 39, 119, 0.05)',
          fill: true,
          tension: 0.4,
          borderWidth: 3,
          pointRadius: 3,
          pointBackgroundColor: '#DB2777'
        }]
      }
    }

    topUsers.value = top_reported_users || []
    loadingUsers.value = false
  } catch (error) {
    console.error('Failed to fetch analytics:', error)
    loadingUsers.value = false
  }
}

onMounted(fetchData)
</script>

<style scoped>
.animate-in {
  animation: fadeIn 0.4s ease-out forwards;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
