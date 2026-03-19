<template>
    <div class="min-h-screen bg-gray-50/50 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="py-8 flex flex-col md:flex-row md:items-end md:justify-between space-y-4 md:space-y-0">
                <div>
                    <h1 class="text-3xl font-extrabold text-agri-forest tracking-tight">Agent Portal</h1>
                    <p class="mt-1 text-sm text-gray-500 font-medium">Real-time support operations and team performance.
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <router-link to="/support/admin/tickets"
                        class="inline-flex items-center px-4 py-2 bg-agri-forest text-white rounded-xl text-sm font-semibold hover:bg-agri-lush shadow-md transition-all duration-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                        Ticket Queue
                    </router-link>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Open Tickets -->
                <div
                    class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 group hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="p-3 bg-blue-50 rounded-xl text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                                </path>
                            </svg>
                        </div>
                        <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg">Active</span>
                    </div>
                    <p class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-1">Open Tickets</p>
                    <h3 class="text-2xl font-black text-gray-900">{{ stats.open_tickets }}</h3>
                </div>

                <!-- SLA Breached -->
                <div
                    class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 group hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="p-3 bg-red-50 rounded-xl text-red-600 group-hover:bg-red-600 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-bold text-red-600 bg-red-50 px-2 py-1 rounded-lg">At Risk</span>
                    </div>
                    <p class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-1">SLA Breached</p>
                    <h3 class="text-2xl font-black text-gray-900">{{ stats.sla_breached }}</h3>
                </div>

                <!-- Average CSAT -->
                <div
                    class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 group hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="p-3 bg-yellow-50 rounded-xl text-yellow-600 group-hover:bg-yellow-600 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.382-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z">
                                </path>
                            </svg>
                        </div>
                        <span class="text-xs font-bold text-yellow-600 bg-yellow-50 px-2 py-1 rounded-lg">{{
                            stats.csat_percentage }}% Happy</span>
                    </div>
                    <p class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-1">CSAT Score</p>
                    <h3 class="text-2xl font-black text-gray-900">{{ stats.csat_score }}/5.0</h3>
                </div>

                <!-- Avg Resolution Time -->
                <div class="bg-agri-forest rounded-2xl p-6 shadow-xl border border-agri-lush relative overflow-hidden">
                    <div class="absolute -right-4 -bottom-4 opacity-10">
                        <svg class="w-32 h-32 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="relative z-10 text-white">
                        <p class="text-xs font-bold text-agri-earth uppercase tracking-widest mb-1 opacity-80">
                            Resolution Speed</p>
                        <h3 class="text-3xl font-black mb-2">{{ stats.avg_resolution_time }}h</h3>
                        <div class="w-full bg-agri-lush/50 rounded-full h-1.5 mt-2">
                            <div class="bg-agri-earth h-1.5 rounded-full" style="width: 75%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Ticket Volume Chart (Simulated) -->
                <div class="lg:col-span-2 bg-white rounded-3xl p-6 pb-0 shadow-sm border border-gray-100">
                    <div class="mb-4">
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Ticket Volume Activity
                        </h3>
                        <p class="text-xs text-gray-400 font-medium mt-1">Daily ticket creation trends</p>
                    </div>
                    <div class="h-[350px] w-full">
                        <Line v-if="volumeData.length > 0" :data="chartData" :options="chartOptions" />
                        <div v-else class="h-full flex items-center justify-center text-gray-400 text-sm">
                            No data available
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Feed -->
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 tracking-tight mb-6">Live Feed</h3>
                    <div class="space-y-6">
                        <div v-for="activity in recentActivity" :key="activity.id" class="flex items-start space-x-4">
                            <div
                                :class="['w-8 h-8 rounded-lg flex-shrink-0 flex items-center justify-center', activity.bgClass]">
                                <svg class="w-4 h-4" :class="activity.iconClass" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path v-if="activity.icon === 'svg-ticket'" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2"
                                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                                    </path>
                                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-900 truncate">{{ activity.title }}</p>
                                <p class="text-xs text-gray-500 font-medium line-clamp-1">{{ activity.description }}</p>
                                <p class="text-[10px] text-gray-400 uppercase tracking-wider mt-1">{{ activity.time }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <button
                        class="w-full mt-8 py-3 bg-gray-50 text-xs font-bold text-gray-500 hover:bg-gray-100 rounded-xl transition-colors">
                        View All Notifications
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    Filler
} from 'chart.js'
import { Line } from 'vue-chartjs'

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    Filler
)

const stats = ref({
    open_tickets: 0,
    sla_breached: 0,
    csat_score: 0,
    csat_percentage: 0,
    avg_resolution_time: 0
})

const volumeData = ref([])

const chartData = computed(() => ({
    labels: volumeData.value.map(v => v.label),
    datasets: [
        {
            label: 'Volume',
            backgroundColor: (context) => {
                const ctx = context.chart.ctx;
                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, 'rgba(21, 87, 36, 0.1)'); // Dark Green fade
                gradient.addColorStop(1, 'rgba(21, 87, 36, 0)');
                return gradient;
            },
            borderColor: '#155724', // Dark Agri-Forest Green
            pointBackgroundColor: '#155724',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 6,
            pointHoverRadius: 8,
            data: volumeData.value.map(v => v.created),
            fill: true,
            tension: 0
        }
    ]
}))

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false
        },
        tooltip: {
            backgroundColor: '#155724', // Dark Green
            titleColor: '#fff',
            bodyColor: '#fff',
            displayColors: false,
            padding: 12,
            cornerRadius: 8,
            titleFont: {
                weight: 'bold',
                size: 13
            },
            bodyFont: {
                weight: 'bold',
                size: 14
            },
            callbacks: {
                title: (context) => context[0].label,
                label: (context) => `Tickets: ${context.raw}`
            }
        }
    },
    scales: {
        x: {
            grid: {
                display: false,
                drawBorder: false
            },
            ticks: {
                color: '#9CA3AF',
                font: {
                    weight: 'bold',
                    size: 11
                }
            }
        },
        y: {
            beginAtZero: true,
            grid: {
                color: '#F3F4F6',
                drawBorder: false
            },
            ticks: {
                padding: 10
            },
            border: {
                display: false
            }
        }
    },
    interaction: {
        intersect: false,
        mode: 'index',
    },
}

const recentActivity = ref([])

const fetchDashboardStats = async () => {
    try {
        const response = await axios.get('/api/support/admin/dashboard')
        const data = response.data
        stats.value = {
            open_tickets: data.tickets_by_status?.find(s => s.status === 'open')?.total || 0,
            sla_breached: data.tickets_by_status?.find(s => ['overdue', 'breached'].includes(s.status))?.total || 0,
            csat_score: data.csat_score || 0,
            csat_percentage: Math.round((data.csat_score / 5) * 100) || 0,
            avg_resolution_time: data.avg_resolution_time?.toFixed(1) || 0
        }

        // Process volume data to ensure last 7 days are shown even if empty
        const days = [];
        for (let i = 6; i >= 0; i--) {
            const d = new Date();
            d.setDate(d.getDate() - i);
            const dateStr = d.toISOString().split('T')[0];

            // Find data for this date
            const existing = data.volume_data?.find(v => v.date === dateStr);

            days.push({
                label: d.toLocaleDateString([], { month: 'short', day: 'numeric' }),
                created: existing ? existing.total : 0,
                resolved: 0
            });
        }
        volumeData.value = days;

        recentActivity.value = data.recent_activity || []
    } catch (error) {
        console.error('Failed to fetch dashboard stats:', error)
    }
}

// Removed legacy chart helper
// const getChartHeight = (val) => ...

onMounted(() => {
    fetchDashboardStats()
})
</script>

<style scoped>
.line-clamp-1 {
    display: -webkit-box;
    line-clamp: 1;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
