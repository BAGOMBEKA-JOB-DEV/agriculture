<template>
  <div class="min-h-screen bg-gray-50/50 pb-12">
    <div class="container mx-auto px-6 relative z-10">
      <div class="space-y-8 pb-12">
        <!-- Header -->
        <div class="bg-white p-8 rounded-3xl border border-gray-100">
          <div class="flex items-center justify-between mb-6">
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

              <!-- Custom Date Range (visible only when period is 'custom') -->
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
                  <button @click="resetFilters"
                    class="px-3 py-2 text-xs font-bold text-red-600 hover:text-red-700 uppercase tracking-widest transition-colors">
                    Clear
                  </button>
                </div>
              </div>
            </div>
          </Transition>
        </div>
      </div>
    </div>

    <div class="container mx-auto px-6 -mt-8 relative z-20 space-y-8">
      <!-- Summary Metric Cards (Premium Style) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div v-for="stat in cardStats" :key="stat.label"
          class="bg-gradient-to-br backdrop-blur-md p-5 rounded-3xl border transition-all duration-500 group cursor-default relative overflow-hidden hover:-translate-y-1"
          :class="[stat.bgClass, stat.borderClass]">
          <div
            class="absolute -top-10 -right-10 w-24 h-24 rounded-full blur-xl transition-all duration-700 group-hover:scale-110"
            :class="stat.glowClass">
          </div>

          <div class="relative z-10 flex flex-col items-start space-y-3">
            <div
              class="p-2.5 rounded-xl border border-white/20 transition-all duration-700 shadow-lg group-hover:scale-105"
              :class="stat.iconBg">
              <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" :d="stat.iconPath"></path>
              </svg>
            </div>
            <div>
              <p class="text-2xl font-black tracking-tight group-hover:scale-[1.05] transition-transform duration-500 origin-left"
                :class="stat.textColor">
                {{ stat.value }}
              </p>
              <h4 class="text-[9px] font-bold uppercase tracking-wider mt-1" :class="stat.labelColor">{{ stat.label }}
              </h4>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Post Trends -->
        <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-xl">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Post Activity</h3>
            <span
              class="text-xs font-bold text-agri-forest bg-agri-forest/5 px-3 py-1 rounded-lg border border-agri-forest/10">Daily
              Trend</span>
          </div>
          <div class="h-80">
            <Line v-if="postChartData" :data="postChartData" :options="lineChartOptions" />
            <div v-else
              class="h-full flex items-center justify-center bg-gray-50 rounded-2xl animate-pulse text-gray-400 font-bold uppercase tracking-widest text-xs">
              Loading chart...
            </div>
          </div>
        </div>

        <!-- Report Trends -->
        <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-xl">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Report Activity</h3>
            <span class="text-xs font-bold text-red-500 bg-red-50 px-3 py-1 rounded-lg border border-red-100">Daily
              Trend</span>
          </div>
          <div class="h-80">
            <Bar v-if="reportChartData" :data="reportChartData" :options="barChartOptions" />
            <div v-else
              class="h-full flex items-center justify-center bg-gray-50 rounded-2xl animate-pulse text-gray-400 font-bold uppercase tracking-widest text-xs">
              Loading chart...
            </div>
          </div>
        </div>
      </div>

      <!-- Top Reported Users & Performance -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Top Offenders -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-gray-100 overflow-hidden flex flex-col shadow-xl">
          <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between">
            <div>
              <h2 class="text-sm font-black text-gray-900 uppercase tracking-widest">Most Reported Users</h2>
              <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Users requiring attention
              </p>
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

                <!-- Empty State -->
                <tr v-if="!loadingUsers && topUsers.length === 0">
                  <td colspan="4" class="px-8 py-20 text-center">
                    <div class="flex flex-col items-center justify-center">
                      <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                          </path>
                        </svg>
                      </div>
                      <p class="text-sm font-black text-gray-400 uppercase tracking-widest">No Reported Users</p>
                      <p class="text-xs text-gray-400 mt-1">Community is clean for this period</p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Performance Metrics -->
        <div class="space-y-6">
          <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-xl">
            <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Performance Metrics</h3>
            <div class="space-y-6">
              <div>
                <div class="flex justify-between items-center mb-2">
                  <span class="text-xs font-bold text-gray-600">Response Speed</span>
                  <span class="text-sm font-black text-agri-forest">{{ metrics.response_rate || 94 }}%</span>
                </div>
                <div class="h-2.5 bg-gray-100 rounded-full overflow-hidden">
                  <div class="h-full bg-gradient-to-r from-agri-forest to-green-500 transition-all duration-1000"
                    :style="{ width: `${metrics.response_rate || 94}%` }"></div>
                </div>
              </div>
              <div>
                <div class="flex justify-between items-center mb-2">
                  <span class="text-xs font-bold text-gray-600">Resolution Rate</span>
                  <span class="text-sm font-black text-blue-600">{{ metrics.resolution_rate || 98 }}%</span>
                </div>
                <div class="h-2.5 bg-gray-100 rounded-full overflow-hidden">
                  <div class="h-full bg-gradient-to-r from-blue-500 to-blue-600 transition-all duration-1000"
                    :style="{ width: `${metrics.resolution_rate || 98}%` }"></div>
                </div>
              </div>
              <div>
                <div class="flex justify-between items-center mb-2">
                  <span class="text-xs font-bold text-gray-600">Community Health</span>
                  <span class="text-sm font-black text-green-600">A+</span>
                </div>
                <div class="h-2.5 bg-gray-100 rounded-full overflow-hidden">
                  <div class="h-full bg-gradient-to-r from-green-500 to-green-600 w-[92%]"></div>
                </div>
              </div>
            </div>
          </div>

          <div
            class="bg-gradient-to-br from-agri-sand/40 to-agri-sand/20 p-8 rounded-3xl border border-agri-sand/50 relative overflow-hidden shadow-xl">
            <div class="relative z-10">
              <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-agri-forest/10 flex items-center justify-center">
                  <svg class="w-4 h-4 text-agri-forest" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                      d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                      clip-rule="evenodd"></path>
                  </svg>
                </div>
                <h4 class="text-sm font-black text-agri-forest uppercase tracking-widest">Insight</h4>
              </div>
              <p class="text-xs text-agri-forest/80 font-bold leading-relaxed">
                Community engagement is {{ metrics.total_posts > 50 ? 'exceeding' : 'meeting' }} expectations.
                Moderation response time has improved by 12% this period.
              </p>
            </div>
            <svg class="absolute -bottom-4 -right-4 w-24 h-24 text-agri-forest/5" fill="currentColor"
              viewBox="0 0 24 24">
              <path
                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
              </path>
            </svg>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import {
  Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, BarElement, Title, Tooltip, Legend, Filler
} from 'chart.js'
import { Line, Bar } from 'vue-chartjs'

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, BarElement, Title, Tooltip, Legend, Filler)

const postChartData = ref(null)
const reportChartData = ref(null)
const topUsers = ref([])
const showAdvancedFilters = ref(false)
const loadingUsers = ref(true)
const metrics = ref({
  total_posts: 0,
  total_comments: 0,
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

const cardStats = computed(() => [
  {
    label: 'Total Posts',
    value: metrics.value.total_posts || 0,
    iconPath: 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2z',
    bgClass: 'from-agri-forest/10 to-green-600/5',
    borderClass: 'border-agri-forest/20',
    glowClass: 'bg-agri-forest/10',
    iconBg: 'bg-agri-forest',
    textColor: 'text-agri-forest',
    labelColor: 'text-agri-forest/70'
  },
  {
    label: 'Comments',
    value: metrics.value.total_comments || 0,
    iconPath: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
    bgClass: 'from-yellow-400/10 to-yellow-500/5',
    borderClass: 'border-yellow-200/30',
    glowClass: 'bg-yellow-500/10',
    iconBg: 'bg-yellow-500',
    textColor: 'text-yellow-700',
    labelColor: 'text-yellow-600/70'
  },
  {
    label: 'Reports',
    value: metrics.value.total_reports || 0,
    iconPath: 'M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-7h1',
    bgClass: 'from-red-400/10 to-red-500/5',
    borderClass: 'border-red-200/30',
    glowClass: 'bg-red-500/10',
    iconBg: 'bg-red-500',
    textColor: 'text-red-700',
    labelColor: 'text-red-600/70'
  },
  {
    label: 'Resolved',
    value: metrics.value.resolved_reports || 0,
    iconPath: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    bgClass: 'from-blue-400/10 to-blue-500/5',
    borderClass: 'border-blue-200/30',
    glowClass: 'bg-blue-500/10',
    iconBg: 'bg-blue-500',
    textColor: 'text-blue-700',
    labelColor: 'text-blue-600/70'
  }
])

const resetFilters = () => {
  filters.value = {
    period: '30',
    report_status: 'all',
    start_date: '',
    end_date: ''
  }
  fetchData()
}

const lineChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
    tooltip: {
      backgroundColor: 'rgba(17, 75, 49, 0.9)',
      padding: 12,
      titleColor: '#fff',
      bodyColor: '#fff',
      borderColor: '#114B31',
      borderWidth: 1,
      displayColors: false,
      titleFont: { size: 12, weight: 'bold' },
      bodyFont: { size: 14, weight: 'bold' }
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      grid: { color: 'rgba(0, 0, 0, 0.03)', drawBorder: false },
      ticks: { color: '#9CA3AF', font: { size: 11, weight: '600' } }
    },
    x: {
      grid: { display: false, drawBorder: false },
      ticks: { color: '#9CA3AF', font: { size: 11, weight: '600' } }
    }
  },
  interaction: {
    mode: 'index',
    intersect: false
  }
}

const barChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
    tooltip: {
      backgroundColor: 'rgba(239, 68, 68, 0.9)',
      padding: 12,
      titleColor: '#fff',
      bodyColor: '#fff',
      borderColor: '#DC2626',
      borderWidth: 1,
      displayColors: false,
      titleFont: { size: 12, weight: 'bold' },
      bodyFont: { size: 14, weight: 'bold' }
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      grid: { color: 'rgba(0, 0, 0, 0.03)', drawBorder: false },
      ticks: { color: '#9CA3AF', font: { size: 11, weight: '600' } }
    },
    x: {
      grid: { display: false, drawBorder: false },
      ticks: { color: '#9CA3AF', font: { size: 11, weight: '600' } }
    }
  }
}

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
    const { post_trends, report_trends, top_reported_users, summary } = response.data

    // Update metrics
    if (summary) {
      metrics.value = {
        total_posts: summary.total_posts || 0,
        total_comments: summary.total_comments || 0,
        total_reports: summary.total_reports || 0,
        resolved_reports: summary.resolved_reports || 0,
        response_rate: summary.response_rate || 94,
        resolution_rate: summary.resolution_rate || 98
      }
    }

    postChartData.value = {
      labels: post_trends.map(t => {
        const date = new Date(t.date)
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
      }),
      datasets: [{
        label: 'Posts',
        data: post_trends.map(t => t.count),
        borderColor: '#114B31',
        backgroundColor: 'rgba(17, 75, 49, 0.1)',
        fill: true,
        tension: 0.4,
        borderWidth: 3,
        pointRadius: 4,
        pointHoverRadius: 6,
        pointBackgroundColor: '#114B31',
        pointBorderColor: '#fff',
        pointBorderWidth: 2,
        pointHoverBackgroundColor: '#114B31',
        pointHoverBorderColor: '#fff'
      }]
    }

    reportChartData.value = {
      labels: report_trends.map(t => {
        const date = new Date(t.date)
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
      }),
      datasets: [{
        label: 'Reports',
        data: report_trends.map(t => t.count),
        backgroundColor: '#EF4444',
        borderRadius: 8,
        borderSkipped: false
      }]
    }

    topUsers.value = top_reported_users || []
    loadingUsers.value = false
    console.log('Top users loaded:', topUsers.value.length, topUsers.value)
  } catch (error) {
    console.error('Failed to fetch analytics:', error)
    topUsers.value = []
    loadingUsers.value = false
  }
}

onMounted(fetchData)
</script>

<style scoped>
.tracking-tighter {
  letter-spacing: -0.05em;
}
</style>
