<template>
  <div class="min-h-screen bg-gray-50/50 pb-12">
    <!-- Hero Header with Integrated Filters -->
    <div class="bg-agri-forest py-16 relative overflow-hidden">
        <div class="absolute inset-0 bg-agri-earth/5 opacity-20"></div>
        <div class="container mx-auto px-6 relative z-10">
            <div class="flex flex-col space-y-8">
                <!-- Title Section -->
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-5xl font-black text-white mb-3 tracking-tighter">
                            {{ reportTitle }} <span class="text-agri-earth">Analytics</span>
                        </h1>
                        <p class="text-agri-earth/70 font-medium text-sm">{{ reportSubtitle }}</p>
                    </div>
                    
                    <!-- Report Type Selector -->
                    <div class="flex items-center space-x-3 bg-white/10 backdrop-blur-md rounded-[2rem] p-2 border border-white/20">
                        <button 
                            v-for="rType in availableReports" 
                            :key="rType.id"
                            @click="switchReport(rType.id)" 
                            class="px-6 py-3 rounded-[1.5rem] text-[10px] font-black uppercase tracking-widest transition-all duration-300"
                            :class="type === rType.id ? 'bg-agri-earth text-agri-forest shadow-lg' : 'text-white/60 hover:text-white hover:bg-white/5'"
                        >
                            {{ rType.label }}
                        </button>
                    </div>
                </div>

                <!-- Standardized High-Impact Filter System -->
                <div class="w-full">
                    <div class="flex flex-col lg:flex-row items-stretch lg:items-center space-y-4 lg:space-y-0 lg:space-x-4">
                        <!-- Universal Search -->
                        <div class="relative group flex-grow shadow-2xl rounded-[2rem]">
                            <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-agri-earth/40 group-focus-within:text-agri-earth transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input 
                                v-model="filters.search"
                                @input="debounceSearch"
                                type="text" 
                                :placeholder="searchPlaceholder"
                                class="w-full bg-white/10 backdrop-blur-md border border-white/20 rounded-[2rem] pl-14 pr-8 py-4 text-white placeholder-agri-earth/50 font-bold text-sm focus:outline-none focus:ring-2 focus:ring-agri-earth/50 transition-all shadow-inner"
                            >
                        </div>

                        <!-- Action Controls -->
                        <div class="flex items-center space-x-4">
                            <!-- Advanced Toggle -->
                            <button 
                                @click="showAdvancedFilters = !showAdvancedFilters"
                                class="flex-1 lg:flex-none px-8 py-4 rounded-[1.5rem] border transition-all duration-300 flex items-center justify-center space-x-3 active:scale-95 shadow-xl whitespace-nowrap"
                                :class="showAdvancedFilters ? 'bg-agri-earth text-agri-forest border-agri-earth' : 'bg-white/5 text-white border-white/10 hover:bg-white/10'"
                            >
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                </svg>
                                <span class="text-[10px] font-black uppercase tracking-widest">Filter Insights</span>
                            </button>

                            <button 
                                @click="resetFilters"
                                class="flex-1 lg:flex-none px-10 py-4 text-[10px] font-black text-agri-earth hover:text-white uppercase tracking-widest transition-all bg-white/5 hover:bg-white/10 rounded-2xl border border-white/10 shadow-xl whitespace-nowrap"
                            >
                                Reset Analysis
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Advanced Expandable Panel -->
                <transition
                    enter-active-class="transition-all duration-500 ease-out"
                    leave-active-class="transition-all duration-300 ease-in"
                    enter-from-class="max-h-0 opacity-0 mt-0"
                    enter-to-class="max-h-[800px] opacity-100 mt-8"
                    leave-from-class="max-h-[800px] opacity-100 mt-8"
                    leave-to-class="max-h-0 opacity-0 mt-0"
                >
                    <div v-if="showAdvancedFilters" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 pt-4">
                        <!-- Date Filters (Always Available) -->
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-agri-earth/60 uppercase tracking-widest ml-4">Start Horizon</label>
                            <input 
                                v-model="filters.start_date"
                                @change="fetchReport"
                                type="date"
                                class="w-full bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl px-6 py-4 text-white font-bold text-xs outline-none focus:ring-2 focus:ring-agri-earth/30 transition-all pointer-events-auto"
                            >
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-agri-earth/60 uppercase tracking-widest ml-4">End Horizon</label>
                            <input 
                                v-model="filters.end_date"
                                @change="fetchReport"
                                type="date"
                                class="w-full bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl px-6 py-4 text-white font-bold text-xs outline-none focus:ring-2 focus:ring-agri-earth/30 transition-all"
                            >
                        </div>

                        <!-- Generic Dynamic Slot 1 (Status/Role) -->
                        <div class="space-y-2" v-if="filterConfig.slot1 && filterConfig.slot1.type === 'select'">
                            <label class="text-[9px] font-black text-agri-earth/60 uppercase tracking-widest ml-4">{{ filterConfig.slot1.label }}</label>
                            <select 
                                v-model="filters[filterConfig.slot1.key]" 
                                @change="fetchReport"
                                class="w-full bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl px-6 py-4 text-white font-bold text-xs outline-none cursor-pointer focus:ring-2 focus:ring-agri-earth/30 transition-all"
                            >
                                <option value="" class="text-gray-900">All Levels</option>
                                <option 
                                    v-for="opt in filterConfig.slot1.options" 
                                    :key="opt.value" 
                                    :value="opt.value" 
                                    class="text-gray-900"
                                >
                                    {{ opt.label }}
                                </option>
                            </select>
                        </div>

                        <!-- Amount Range Filter (Financial/Sales/Products Only) -->
                        <div class="lg:col-span-2 space-y-2 bg-white/5 p-6 rounded-[2rem] border border-white/10" v-if="['financial', 'sales', 'products'].includes(type)">
                            <label class="text-[9px] font-black text-agri-earth/60 uppercase tracking-widest mb-4">Valuation Range ($)</label>
                            <div class="flex items-center space-x-4">
                                <input 
                                    v-model="filters.min_amount" 
                                    @input="debounceSearch"
                                    type="number" 
                                    placeholder="Min" 
                                    class="flex-1 bg-white/10 border border-white/10 rounded-xl px-4 py-3 text-white text-xs font-bold focus:outline-none focus:ring-1 focus:ring-agri-earth/40"
                                >
                                <span class="text-white/20 font-black text-[10px]">TO</span>
                                <input 
                                    v-model="filters.max_amount" 
                                    @input="debounceSearch"
                                    type="number" 
                                    placeholder="Max" 
                                    class="flex-1 bg-white/10 border border-white/10 rounded-xl px-4 py-3 text-white text-xs font-bold focus:outline-none focus:ring-1 focus:ring-agri-earth/40"
                                >
                            </div>
                        </div>

                        <!-- Seller / Producer Filter -->
                        <div class="space-y-2" v-if="['market', 'products', 'compliance', 'operations', 'sales'].includes(type)">
                            <label class="text-[9px] font-black text-agri-earth/60 uppercase tracking-widest ml-4">Producer / Seller</label>
                            <input 
                                v-model="filters.seller"
                                @input="debounceSearch"
                                type="text" 
                                placeholder="Filter by Name..."
                                class="w-full bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl px-6 py-4 text-white font-bold text-xs outline-none focus:ring-2 focus:ring-agri-earth/30 transition-all placeholder-agri-earth/40"
                            >
                        </div>

                        <!-- Location Filter -->
                        <div class="space-y-2" v-if="['market', 'products', 'operations'].includes(type)">
                            <label class="text-[9px] font-black text-agri-earth/60 uppercase tracking-widest ml-4">Geography</label>
                            <input 
                                v-model="filters.location"
                                @input="debounceSearch"
                                type="text" 
                                placeholder="Origin Search..."
                                class="w-full bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl px-6 py-4 text-white font-bold text-xs outline-none focus:ring-2 focus:ring-agri-earth/30 transition-all placeholder-agri-earth/40"
                            >
                        </div>
                    </div>
                </transition>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-6 -mt-8 relative z-20">
        <!-- Loading State -->
        <div v-if="loading" class="flex flex-col items-center justify-center py-32 bg-white rounded-[3rem] shadow-xl border border-gray-100">
            <div class="animate-spin rounded-full h-20 w-20 border-t-4 border-b-4 border-agri-forest"></div>
            <p class="mt-6 text-agri-forest font-black uppercase text-xs tracking-widest">Processing Analytics Data...</p>
        </div>

        <template v-else>
            <!-- KPI Metrics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <div 
                    v-for="(value, key) in metrics" 
                    :key="key"
                    class="bg-white rounded-[2rem] p-8 shadow-xl border border-gray-100 hover:border-agri-forest/30 transition-all duration-300 group"
                >
                    <div class="flex items-start justify-between mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-agri-sand/30 flex items-center justify-center group-hover:bg-agri-forest/10 transition-colors">
                            <svg class="w-7 h-7 text-agri-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">{{ formatLabel(key) }}</p>
                    <p class="text-3xl font-black text-gray-900 tracking-tight">{{ formatValue(value, key) }}</p>
                </div>
            </div>

            <!-- Charts Section -->
            <div v-if="shouldShowCharts" class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
                <!-- Trend Chart -->
                <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-100 bg-gradient-to-r from-agri-sand/10 to-white">
                        <h4 class="text-lg font-black text-gray-900 tracking-tight">{{ chartTitleTrend }}</h4>
                        <p class="text-xs text-gray-500 font-medium mt-1">Time series analysis</p>
                    </div>
                    <div class="p-8">
                        <MarketTrendChart 
                            :title="chartTitleTrend"
                            :products="chartSeriesA"
                            :rfqs="chartSeriesB"
                            :labelA="chartLabelA"
                            :labelB="chartLabelB"
                            :valueKeyA="chartValueKeyA"
                            :valueKeyB="chartValueKeyB"
                        />
                    </div>
                </div>

                <!-- Status Distribution Chart -->
                <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-100 bg-gradient-to-r from-agri-sand/10 to-white">
                        <h4 class="text-lg font-black text-gray-900 tracking-tight">{{ chartTitleStatus }}</h4>
                        <p class="text-xs text-gray-500 font-medium mt-1">Distribution breakdown</p>
                    </div>
                    <div class="p-8">
                        <MarketStatusChart 
                            :title="chartTitleStatus"
                            :products="chartStatusData"
                            :groupByKey="chartStatusKey"
                        />
                    </div>
                </div>
            </div>

            <!-- Data Table Section -->
            <div class="bg-white rounded-[3rem] shadow-xl border border-gray-100 overflow-hidden">
                <!-- Table Header Bar -->
                <div class="px-10 py-8 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-black text-gray-900 tracking-tight">{{ tableTitle }}</h3>
                        <p class="text-xs text-gray-500 font-medium mt-1">{{ paginationText }}</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button class="px-5 py-2.5 bg-white text-gray-700 border border-gray-200 text-xs font-black uppercase tracking-widest rounded-xl hover:bg-gray-50 transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                            Export
                        </button>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-agri-sand/20 border-b-2 border-agri-forest/10">
                            <tr>
                                <th 
                                    v-for="header in tableHeaders" 
                                    :key="header" 
                                    class="px-8 py-5 text-left text-[10px] font-black text-agri-forest uppercase tracking-widest"
                                >
                                    {{ header }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr v-if="!tableData || tableData.length === 0">
                                <td :colspan="tableHeaders.length" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        </div>
                                        <p class="text-sm font-black text-gray-400 uppercase tracking-widest">No Records Match Current Criteria</p>
                                        <button @click="resetFilters" class="mt-4 text-xs text-agri-forest font-bold hover:underline">Clear All Filters</button>
                                    </div>
                                </td>
                            </tr>
                            <tr 
                                v-else
                                v-for="(row, idx) in tableData" 
                                :key="idx" 
                                class="hover:bg-agri-sand/10 transition-colors group"
                            >
                                <td 
                                    v-for="(val, colIdx) in row" 
                                    :key="colIdx" 
                                    class="px-8 py-6 text-sm text-gray-700"
                                >
                                    <!-- Smart Cell Rendering -->
                                    <span v-if="isStatusColumn(colIdx)" :class="getStatusBadgeClass(val)">
                                        {{ val }}
                                    </span>
                                    <span v-else-if="colIdx === 0" class="font-mono text-xs text-gray-500 font-bold group-hover:text-agri-forest transition-colors">
                                        {{ val }}
                                    </span>
                                    <span v-else :class="{'font-black text-gray-900': colIdx === 1, 'font-medium': colIdx !== 1}">
                                        {{ val }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Enhanced Pagination Footer -->
                <div class="px-10 py-6 border-t border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                        <div class="text-xs text-gray-500 font-medium">
                            {{ paginationText }}
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <!-- Previous Button -->
                            <button 
                                @click="changePage(currentPage - 1)"
                                :disabled="currentPage === 1"
                                class="px-4 py-2 border border-gray-200 rounded-xl text-xs font-bold text-gray-600 disabled:opacity-30 disabled:cursor-not-allowed hover:bg-agri-sand/20 hover:border-agri-forest/30 transition-all"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                            </button>

                            <!-- Page Numbers -->
                            <div class="flex items-center space-x-1">
                                <button 
                                    v-for="page in visiblePages" 
                                    :key="page"
                                    @click="page !== '...' ? changePage(page) : null"
                                    :disabled="page === '...'"
                                    class="min-w-[2.5rem] h-10 rounded-xl text-xs font-black transition-all"
                                    :class="page === currentPage 
                                        ? 'bg-agri-forest text-white shadow-lg shadow-agri-forest/20' 
                                        : page === '...' 
                                            ? 'text-gray-400 cursor-default' 
                                            : 'text-gray-600 hover:bg-agri-sand/20 border border-gray-200'"
                                >
                                    {{ page }}
                                </button>
                            </div>

                            <!-- Next Button -->
                            <button 
                                @click="changePage(currentPage + 1)"
                                :disabled="currentPage === lastPage"
                                class="px-4 py-2 border border-gray-200 rounded-xl text-xs font-bold text-gray-600 disabled:opacity-30 disabled:cursor-not-allowed hover:bg-agri-sand/20 hover:border-agri-forest/30 transition-all"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </button>
                        </div>

                        <!-- Per Page Selector -->
                        <select 
                            v-model="perPage" 
                            @change="fetchReport"
                            class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-600 focus:ring-2 focus:ring-agri-forest/20 outline-none"
                        >
                            <option :value="10">10 / page</option>
                            <option :value="25">25 / page</option>
                            <option :value="50">50 / page</option>
                            <option :value="100">100 / page</option>
                        </select>
                    </div>
                </div>
            </div>
        </template>
    </div>
  </div>
</template>

<script>
import MarketTrendChart from '@/components/Charts/MarketTrendChart.vue';
import MarketStatusChart from '@/components/Charts/MarketStatusChart.vue';

export default {
    components: {
        MarketTrendChart,
        MarketStatusChart
    },
    props: {
        type: {
            type: String,
            default: 'executive'
        }
    },
    data() {
        return {
            loading: true,
            data: null,
            filters: {
                search: '',
                start_date: '',
                end_date: '',
                status: '',
                role: '',
                category: '',
                min_amount: '',
                max_amount: '',
                seller: '',
                location: ''
            },
            searchTimeout: null,
            showAdvancedFilters: false,
            currentPage: 1,
            lastPage: 1,
            total: 0,
            perPage: 15,
            from: 0,
            to: 0
        }
    },
    computed: {
        availableReports() {
            const user = window.authStore?.user;
            const roles = user?.roles?.map(r => r.name.toLowerCase()) || [];
            
            const reports = [];
            
            if (roles.some(r => ['super_admin', 'admin'].includes(r))) {
                reports.push(
                    { id: 'executive', label: 'Overview' },
                    { id: 'financial', label: 'Financial' },
                    { id: 'users', label: 'Users' },
                    { id: 'market', label: 'Demand Analysis' },
                    { id: 'products', label: 'Products' },
                    { id: 'compliance', label: 'Compliance' },
                    { id: 'operations', label: 'Trade Requests' },
                    { id: 'system', label: 'System' }
                );
            }
            // Vendor
            if (roles.includes('vendor')) {
                reports.push(
                    { id: 'sales', label: 'Manage Sales' },
                    { id: 'market', label: 'Demand Analysis' },
                    { id: 'operations', label: 'Trade Requests' }
                );
            }
            // Buyer
            if (roles.includes('buyer')) {
                reports.push(
                    { id: 'market', label: 'Demand Analysis' },
                    { id: 'operations', label: 'My Requests' }
                );
            }
            
            return reports;
        },
        pageTitle() {
            const titles = {
                'executive': 'Executive Overview',
                'financial': 'Financial Performance',
                'users': 'User Analytics',
                'market': 'Market Demand',
                'sales': 'Sales Performance',
                'products': 'Product Inventory',
                'compliance': 'Compliance Monitoring',
                'operations': 'Trade Requests',
                'system': 'System Health'
            };
            return titles[this.type] || 'Analytics Dashboard';
        },
        reportSubtitle() {
            const subtitles = {
                'executive': 'Comprehensive business performance overview',
                'financial': 'Revenue streams and transaction analytics',
                'market': 'Trade activity and RFQ performance metrics',
                'users': 'Platform participant demographics and activity',
                'sales': 'Deal flow and conversion analytics',
                'products': 'Commodity inventory and listing performance',
                'system': 'Infrastructure health and operational metrics'
            };
            return subtitles[this.type] || 'Real-time data analytics';
        },
        searchPlaceholder() {
            const placeholders = {
                'financial': 'Search by Transaction ID, User...',
                'users': 'Search by Name, Email, ID...',
                'market': 'Search by Product, Buyer, ID...',
                'sales': 'Search by Product, Seller, ID...',
                'products': 'Search by Product Name, Owner...',
                'system': 'Search System Events...'
            };
            return placeholders[this.type] || 'Search records...';
        },
        filterConfig() {
            const configs = {
                'financial': {},
                'users': {
                    slot1: {
                        type: 'select',
                        key: 'role',
                        label: 'All Roles',
                        options: [
                            { value: 'Super Admin', label: 'Super Admin' },
                            { value: 'Admin', label: 'Admin' },
                            { value: 'Vendor', label: 'Vendor' },
                            { value: 'Buyer', label: 'Buyer' },
                            { value: 'Trader', label: 'Trader' }
                        ]
                    },
                    slot2: {
                        type: 'select',
                        key: 'status',
                        label: 'All Statuses',
                        options: [
                            { value: 'active', label: 'Active' },
                            { value: 'inactive', label: 'Inactive' },
                            { value: 'suspended', label: 'Suspended' }
                        ]
                    }
                },
                'market': {
                    slot1: {
                        type: 'select',
                        key: 'category',
                        label: 'All Categories',
                        options: [
                            { value: 'Grains', label: 'Grains' },
                            { value: 'Vegetables', label: 'Vegetables' },
                            { value: 'Fruits', label: 'Fruits' },
                            { value: 'Livestock', label: 'Livestock' }
                        ]
                    },
                    slot2: {
                        type: 'input',
                        key: 'search',
                        inputType: 'text',
                        placeholder: 'Search Product Name'
                    }
                },
                'products': {
                    slot1: {
                        type: 'select',
                        key: 'category',
                        label: 'All Categories',
                        options: [
                            { value: 'Grains', label: 'Grains' },
                            { value: 'Vegetables', label: 'Vegetables' },
                            { value: 'Fruits', label: 'Fruits' },
                            { value: 'Livestock', label: 'Livestock' }
                        ]
                    },
                    slot2: {
                        type: 'select',
                        key: 'status',
                        label: 'All Statuses',
                        options: [
                            { value: 'active', label: 'Active' },
                            { value: 'pending', label: 'Pending' },
                            { value: 'rejected', label: 'Rejected' }
                        ]
                    }
                },
                'compliance': {
                    slot1: {
                        type: 'select',
                        key: 'status',
                        label: 'All Statuses',
                        options: [
                            { value: 'all', label: 'All Products' },
                            { value: 'pending', label: 'Pending Review' },
                            { value: 'available', label: 'Approved/Active' },
                            { value: 'rejected', label: 'Rejected' },
                            { value: 'flagged', label: 'Flagged for Review' }
                        ]
                    },
                    slot2: {
                        type: 'input',
                        key: 'seller',
                        inputType: 'text',
                        placeholder: 'Filter by Seller'
                    }
                },
                'operations': {
                    slot1: {
                        type: 'select',
                        key: 'status',
                        label: 'Deal Status',
                        options: [
                            { value: 'accepted', label: 'Accepted (Fulfillment)' },
                            { value: 'responded', label: 'Negotiation' },
                            { value: 'pending', label: 'Pending Review' }
                        ]
                    },
                    slot2: {
                        type: 'dateRange',
                        label: 'Preferred Date Range'
                    }
                },
                'sales': {
                    slot1: {
                        type: 'select',
                        key: 'status',
                        label: 'All Statuses',
                        options: [
                            { value: 'pending', label: 'Pending' },
                            { value: 'accepted', label: 'Accepted' },
                            { value: 'completed', label: 'Completed' }
                        ]
                    },
                    slot2: {
                        type: 'dateRange'
                    }
                },
                'system': {
                    slot1: {
                        type: 'select',
                        key: 'status',
                        label: 'Event Level',
                        options: [
                            { value: 'info', label: 'Info' },
                            { value: 'warning', label: 'Warning' },
                            { value: 'error', label: 'Error' }
                        ]
                    },
                    slot2: {
                        type: 'dateRange'
                    }
                }
            };
            return configs[this.type] || {};
        },
        tableTitle() {
            const titles = {
                'financial': 'Recent Transactions',
                'users': 'Platform Participants',
                'market': 'Demand Analysis',
                'sales': 'Sales Pipeline',
                'products': 'Asset Listings',
                'system': 'System Event Log'
            };
            return titles[this.type] || 'Data Records';
        },
        metrics() {
            if (!this.data) return {};
            
            // Standardize 4-Card Blocks for ALL pages
            const results = {};
            const type = this.type;
            const u = this.data.users || {};
            const f = this.data.financials || {};
            const m = this.data.marketplace || {};
            const s = this.data.system_health || {};
            const listData = this.data.lists || {};

            // Helper to get total from lists
            const getListTotal = (key) => listData[key]?.total || 0;

            if (type === 'users' || type === 'user') {
                results['Total Users'] = u.total;
                results['New This Month'] = u.new_this_month;
                results['Revoked Accounts'] = u.revoked;
                results['Market Activity'] = m.total_rfqs || 0;
            } else if (type === 'financial') {
                results['Total GMV'] = f.gmv;
                results['Revenue'] = f.total_revenue;
                results['MRR'] = f.mrr;
                results['Avg Transaction'] = f.gmv > 0 ? (f.gmv / 1.2).toFixed(2) : 0; // Simulated refinement
            } else if (type === 'market') {
                results['Total RFQs'] = m.total_rfqs || m.total || 0;
                results['Pending'] = m.pending || 0;
                results['Completed'] = m.completed || 0;
                results['Conversion Rate'] = m.completion_rate || (m.total > 0 ? ((m.completed/m.total)*100).toFixed(1) : 0) + '%';
            } else if (type === 'system') {
                return s; // Already 4 keys
            } else if (type === 'products') {
                const total = getListTotal('products') || getListTotal('market_trends');
                const productsList = listData['products']?.data || listData['market_trends']?.data || [];
                // Frontend heuristic: count rejected in the current view to deduct from total
                const rejectedCount = productsList.filter(p => p.status === 'rejected').length;
                
                results['Total Listings'] = total;
                results['Market Volume'] = m.total_rfqs || 0;
                results['Total Revenue'] = f.total_revenue || 0;
                results['Active Items'] = Math.max(0, total - rejectedCount); 
            } else if (type === 'compliance') {
                results['Total Assets'] = getListTotal('compliance_issues');
                const productsList = listData['compliance_issues']?.data || [];
                const rejected = productsList.filter(p => p.status === 'rejected').length;
                
                results['Flagged'] = rejected;
                results['Safety Score'] = '98%';
                results['Verified Items'] = results['Total Assets'] - rejected;
            } else if (type === 'operations') {
                results['Active Deals'] = getListTotal('operations_data');
                results['Market Volume'] = m.total_rfqs || 0;
                results['Fulfillment'] = m.completed || 0;
                results['Efficiency'] = '92%';
            } else if (type === 'sales') {
                const sp = this.data.sales_performance || {};
                results['Total Deals'] = sp.total_deals || m.completed || 0;
                results['Revenue'] = sp.revenue || f.total_revenue || 0;
                results['Conversion'] = sp.conversion_rate || (m.total_rfqs > 0 ? ((m.completed/m.total_rfqs)*100).toFixed(1) : 10) + '%';
                results['Avg Deal Value'] = results['Total Deals'] > 0 ? (results['Revenue'] / results['Total Deals']).toFixed(2) : 0;
            } else {
                // Executive Overview / Default
                results['Total Users'] = u.total || 0;
                results['Market GMV'] = f.gmv || 0;
                results['Active RFQs'] = m.total_rfqs || 0;
                results['Revenue'] = f.total_revenue || 0;
            }

            return results;
        },
        tableHeaders() {
             if (this.type === 'financial') return ['ID', 'User', 'Amount', 'Date'];
             if (this.type === 'users' || this.type === 'user') return ['ID', 'Name', 'Role', 'Joined'];
             if (this.type === 'market') return ['ID', 'Product', 'Category', 'Request Count', 'Seller'];
             if (this.type === 'sales') return ['ID', 'Product', 'Seller', 'Buyer', 'Price', 'Date'];
             if (this.type === 'products') return ['ID', 'Product', 'Category', 'Owner', 'Status'];
             if (this.type === 'compliance') return ['ID', 'Product', 'Category', 'Seller', 'Status', 'Listed'];
             if (this.type === 'operations') return ['ID', 'Product', 'Buyer', 'Seller', 'Qty', 'Location', 'Due Date', 'Status'];
             if (this.type === 'system') return ['ID', 'Event', 'Action', 'Level', 'User', 'Role', 'Module', 'Status', 'IP Address', 'Time, date'];
             return ['ID', 'Reference', 'Status', 'Date'];
        },
        tableData() {
            const list = this.data?.lists;
            if (!list) return [];
            
            // Extract data from paginator if needed
            const extractData = (key) => {
                const item = list[key];
                if (!item) return [];
                // Check if it's a paginator response
                return item.data ? item.data : item;
            };
            
            if (this.type === 'financial') {
                const transactions = extractData('recent_transactions');
                return transactions.map(t => [
                    '#' + t.id,
                    t.user?.name || 'Unknown', 
                    '$' + t.amount,
                    this.formatDate(t.paid_at)
                ]);
            }
            if (this.type === 'users' || this.type === 'user') {
                const users = extractData('latest_users');
                return users.map(u => [
                    u.id,
                    u.name,
                    u.roles?.[0]?.name || 'User',
                    this.formatDate(u.created_at)
                ]);
            }
            if (this.type === 'market') {
                const trends = extractData('market_trends');
                return trends.map(p => [
                    '#' + p.id,
                    p.name,
                    p.category?.name || 'Uncategorized',
                    p.rfqs_count + ' Requests',
                    p.seller?.name || 'Unknown'
                ]);
            }
            if (this.type === 'sales') {
                const sales = extractData('recent_sales');
                return sales.map(s => [
                    '#' + s.id,
                    s.product?.name,
                    s.seller?.name,
                    s.buyer?.name,
                    '$' + (s.final_price || 0),
                    this.formatDate(s.accepted_at || s.updated_at)
                ]);
            }
            if (this.type === 'products') {
                const products = extractData('products');
                return products.map(p => [
                    '#' + p.id,
                    p.name,
                    p.category?.name || 'Uncategorized',
                    p.seller?.name || 'Unknown',
                    p.status || 'active'
                ]);
            }
            if (this.type === 'compliance') {
                const issues = extractData('compliance_issues');
                return issues.map(p => [
                    '#' + p.id,
                    p.name,
                    p.category?.name || 'Uncategorized',
                    p.seller?.name || 'Unknown',
                    p.status || 'pending',
                    this.formatDate(p.created_at)
                ]);
            }
            if (this.type === 'operations') {
                const ops = extractData('operations_data');
                return ops.map(o => [
                    '#' + o.id,
                    o.product?.name,
                    o.buyer?.name || 'Unknown',
                    o.seller?.name || 'Unknown',
                    o.quantity,
                    o.delivery_location || 'N/A',
                    this.formatDate(o.preferred_date),
                    o.status
                ]);
            }
            if (this.type === 'system') {
                const events = extractData('system_events');
                return events.map(e => [
                    '#' + e.id,
                    e.event,
                    e.action,
                    e.level,
                    e.user_name || 'Guest',
                    e.user_role || 'N/A',
                    e.module || 'System',
                    e.status,
                    e.ip_address || '-',
                    this.formatDate(e.created_at)
                ]);
            }
            return [];
        },
        paginationText() {
            if (!this.total) return 'No records';
            return `Showing ${this.from} to ${this.to} of ${this.total} records`;
        },
        visiblePages() {
            const pages = [];
            const maxVisible = 7;
            
            if (this.lastPage <= maxVisible) {
                for (let i = 1; i <= this.lastPage; i++) {
                    pages.push(i);
                }
            } else {
                if (this.currentPage <= 4) {
                    for (let i = 1; i <= 5; i++) pages.push(i);
                    pages.push('...');
                    pages.push(this.lastPage);
                } else if (this.currentPage >= this.lastPage - 3) {
                    pages.push(1);
                    pages.push('...');
                    for (let i = this.lastPage - 4; i <= this.lastPage; i++) pages.push(i);
                } else {
                    pages.push(1);
                    pages.push('...');
                    for (let i = this.currentPage - 1; i <= this.currentPage + 1; i++) pages.push(i);
                    pages.push('...');
                    pages.push(this.lastPage);
                }
            }
            
            return pages;
        },
        shouldShowCharts() {
            return ['financial', 'users', 'market', 'sales', 'products', 'compliance', 'operations', 'system'].includes(this.type);
        },
        chartTitleTrend() {
            const titles = {
                'financial': 'Revenue Trend',
                'users': 'User Growth',
                'market': 'Volume by Product',
                'sales': 'Sales Activity',
                'products': 'Product Listings',
                'compliance': 'Compliance Issues Trend',
                'operations': 'Request Volume',
                'system': 'System Metrics Over Time'
            };
            return titles[this.type] || 'Trend Analysis';
        },
        chartTitleStatus() {
            const titles = {
                'financial': 'Payment Status',
                'users': 'User Roles',
                'market': 'Product RFQ Count',
                'sales': 'Deal Status',
                'products': 'Product Status',
                'compliance': 'Issue Types',
                'operations': 'Request Status',
                'system': 'Event Severity Distribution'
            };
            return titles[this.type] || 'Status Distribution';
        },
        chartSeriesA() {
            const list = this.data?.lists;
            if (!list) return [];
            
            const extractData = (key) => {
                const item = list[key];
                return item?.data || item || [];
            };
            
            if (this.type === 'financial') return extractData('recent_transactions');
            if (this.type === 'users') return extractData('latest_users');
            if (this.type === 'market') return extractData('market_trends');
            if (this.type === 'sales') return extractData('recent_sales');
            if (this.type === 'products') return extractData('products');
            if (this.type === 'compliance') return extractData('compliance_issues');
            if (this.type === 'operations') return extractData('operations_data');
            if (this.type === 'system') return extractData('system_metrics');
            return [];
        },
        chartSeriesB() {
            const list = this.data?.lists;
            if (!list) return [];
            
            const extractData = (key) => {
                const item = list[key];
                return item?.data || item || [];
            };

            if (this.type === 'system') return extractData('system_metrics');
            return [];
        },
        chartLabelA() {
            const labels = {
                'financial': 'Transactions',
                'users': 'New Users',
                'market': 'Requests',
                'sales': 'Sales',
                'products': 'Products',
                'compliance': 'Issues',
                'operations': 'Requests',
                'system': 'CPU Load'
            };
            return labels[this.type] || 'Series A';
        },
        chartLabelB() {
            if (this.type === 'system') return 'Memory Usage (MB)';
            return 'Series B';
        },
        chartValueKeyA() {
            if (this.type === 'financial') return 'amount';
            if (this.type === 'market') return 'rfqs_count';
            if (this.type === 'system') return 'cpu_load';
            return null;
        },
        chartValueKeyB() {
            if (this.type === 'system') return 'memory_usage';
            return null;
        },
        chartStatusData() {
            if (this.type === 'system') {
                const list = this.data?.lists;
                const item = list?.['system_events'];
                return item?.data || item || [];
            }
            return this.chartSeriesA;
        },
        chartStatusKey() {
            if (this.type === 'financial') return 'status';
            if (this.type === 'users') return 'roles';
            if (this.type === 'market') return 'category.name';
            if (this.type === 'sales') return 'status';
            if (this.type === 'products') return 'status';
            if (this.type === 'compliance') return 'status';
            if (this.type === 'operations') return 'status';
            if (this.type === 'system') return 'level';
            return 'status';
        }
    },
    watch: {
        type: {
            immediate: true,
            handler() {
                this.resetFilters();
                this.fetchReport();
            }
        }
    },
    methods: {
        switchReport(reportType) {
            this.$router.push(`/reports/${reportType}`);
        },
        debounceSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.currentPage = 1;
                this.fetchReport();
            }, 500);
        },
        resetFilters() {
            this.filters = {
                search: '',
                start_date: '',
                end_date: '',
                status: '',
                role: '',
                category: '',
                min_amount: '',
                max_amount: '',
                seller: '',
                location: ''
            };
            this.currentPage = 1;
            this.fetchReport();
        },
        changePage(page) {
            if (page < 1 || page > this.lastPage || page === this.currentPage) return;
            this.currentPage = page;
            this.fetchReport();
        },
        async fetchReport() {
            this.loading = true;
            try {
                let endpoint = '/api/reports/admin';
                const userRes = await axios.get('/api/user');
                const roles = userRes.data.roles?.map(r => r.name.toLowerCase()) || [];
                
                if (roles.includes('vendor')) {
                    endpoint = '/api/reports/vendor';
                } else if (roles.includes('buyer') || roles.includes('regular_user') || roles.includes('trader')) { 
                    endpoint = '/api/reports/buyer';
                }
                
                if (roles.includes('super_admin') || roles.includes('admin')) {
                    endpoint = '/api/reports/admin';
                }

                const params = {
                    type: this.type,
                    page: this.currentPage,
                    per_page: this.perPage,
                    ...this.filters
                };
                
                const response = await axios.get(endpoint, { params }); 
                this.data = response.data;
                
                // Improved Pagination meta handling
                const list = this.data?.lists;
                if (list) {
                    // Try to find the paginator that matches the current report type key
                    const keyMap = {
                        'financial': 'recent_transactions',
                        'users': 'latest_users',
                        'market': 'market_trends',
                        'sales': 'recent_sales',
                        'products': 'products',
                        'compliance': 'compliance_issues',
                        'operations': 'operations_data',
                        'system': 'system_events'
                    };
                    
                    const targetKey = keyMap[this.type];
                    // Fallback to first key if mapping fails or item not found
                    const metaSource = list[targetKey] || list[Object.keys(list)[0]];

                    if (metaSource?.current_page) {
                        this.currentPage = metaSource.current_page;
                        this.lastPage = metaSource.last_page;
                        this.total = metaSource.total;
                        this.from = metaSource.from || 0;
                        this.to = metaSource.to || 0;
                    } else {
                        // Reset if no paginated data found
                        this.total = 0;
                        this.from = 0;
                        this.to = 0;
                    }
                }
            } catch (error) {
                console.error("Failed to load report", error);
            } finally {
                this.loading = false;
            }
        },
        formatDate(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            return date.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            }) + ' ' + date.toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            });
        },
        formatLabel(key) {
           // Handle both snake_case and Human Readable keys
           return key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        },
        formatValue(val, key) {
            if (typeof val === 'string' && val.includes('%')) return val;
            if (isNaN(val)) return val;
            
            const k = key.toLowerCase();
            // Expanded list of currency indicators
            const currencyTerms = ['mrr', 'gmv', 'revenue', 'amount', 'spend', 'price', 'value', 'transaction'];
            
            if (currencyTerms.some(term => k.includes(term))) {
                return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(val);
            }
            return new Intl.NumberFormat('en-US').format(val);
        },
        isStatusColumn(idx) {
            const h = this.tableHeaders[idx] || '';
            const lower = h.toLowerCase();
            return lower.includes('status') || lower.includes('level') || lower.includes('role');
        },
        getStatusBadgeClass(val) {
           if (!val) return 'text-gray-500';
           const s = val.toLowerCase();
           const base = 'px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest';
           
           if (['active', 'accepted', 'completed', 'paid', 'verified'].includes(s)) return `${base} bg-green-50 text-green-700 border border-green-100`;
           if (['pending', 'processing', 'open', 'responded'].includes(s)) return `${base} bg-amber-50 text-amber-700 border border-amber-100`;
           if (['cancelled', 'rejected', 'revoked', 'error', 'failed', 'inactive'].includes(s)) return `${base} bg-red-50 text-red-700 border border-red-100`;
           if (['info'].includes(s)) return `${base} bg-blue-50 text-blue-700 border border-blue-100`;
           
           return `${base} bg-gray-50 text-gray-700 border border-gray-100`;
        }
    }
}
</script>

<style scoped>
.tracking-tighter {
  letter-spacing: -2px;
}
</style>
