<template>
  <div class="bg-white rounded-3xl border border-gray-100 overflow-hidden flex flex-col h-full relative">
    <!-- Header -->
    <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-white sticky top-0 z-20">
      <div>
        <h2 class="text-xl font-bold text-gray-900 tracking-tight">Reports</h2>
        <p class="text-[11px] text-gray-400 font-semibold uppercase tracking-wider mt-1.5">Resolution of community
          safety signals</p>
      </div>
      <div class="flex items-center gap-4">
        <!-- Search -->
        <div class="relative group">
          <svg
            class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-agri-forest transition-colors"
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
          <input v-model="search" type="text" placeholder="Search reports..."
            class="pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-agri-forest/10 focus:bg-white text-[10px] font-bold text-gray-700 w-48 transition-all placeholder:font-medium placeholder:text-gray-400"
            @input="debouncedFetch">
        </div>

        <select v-model="status"
          class="bg-gray-50 border border-gray-100 rounded-xl text-[10px] font-black uppercase tracking-widest text-gray-600 py-2.5 pl-4 pr-10 focus:ring-2 focus:ring-agri-forest/10"
          @change="fetchReports">
          <option value="pending">Pending</option>
          <option value="resolved">Resolved</option>
          <option value="dismissed">Dismissed</option>
        </select>
        <button @click="fetchReports"
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
      <table class="w-full text-left border-collapse min-w-[1000px]">
        <thead>
          <tr class="bg-white border-b border-gray-50 sticky top-0 z-10">
            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] w-24">ID</th>
            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Reason</th>
            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Message</th>
            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Reported by</th>
            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">Date</th>
            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right w-20">
              Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr v-if="loading" v-for="i in 10" :key="i" class="animate-pulse">
            <td colspan="6" class="px-8 py-6">
              <div class="h-4 bg-gray-100 rounded-full w-full"></div>
            </td>
          </tr>
          <tr v-else-if="reports.length === 0">
            <td colspan="6" class="px-8 py-12 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">No
              reports found matching your criteria</td>
          </tr>
          <tr v-else v-for="report in reports" :key="report.id" @click="viewDetails(report)"
            class="group hover:bg-gray-50/50 transition-colors cursor-pointer">
            <td class="px-8 py-5">
              <span class="font-mono text-[10px] text-gray-400 font-bold">#{{ report.id }}</span>
            </td>
            <td class="px-8 py-5">
              <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full" :class="severityColor(report.reason)"></div>
                <span class="text-xs font-black text-gray-900 uppercase tracking-tight">{{ report.reason }}</span>
              </div>
            </td>
            <td class="px-8 py-5">
              <p class="text-xs text-gray-500 font-medium truncate max-w-sm italic">
                "{{ report.description || 'No description provided' }}"
              </p>
            </td>
            <td class="px-8 py-5">
              <div class="flex flex-col">
                <span class="text-xs font-bold text-gray-900">{{ report.reporter?.name }}</span>
                <span class="text-[9px] text-gray-400 uppercase tracking-widest">ID #{{ report.reporter_id }}</span>
              </div>
            </td>
            <td class="px-8 py-5 text-right text-[10px] font-bold text-gray-400">
              {{ dayjs(report.created_at).format('MMM D, YY') }}
            </td>
            <td class="px-8 py-5 text-right relative" @click.stop>
              <button @click="toggleMenu(report.id)"
                class="p-2 text-gray-400 hover:text-gray-900 h-8 w-8 flex items-center justify-center rounded-lg bg-gray-50 border-gray-100 hover:border-gray-300 border transition-all">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                  <path
                    d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                </svg>
              </button>

              <!-- Dropdown Action Menu -->
              <div v-if="openMenuId === report.id"
                class="absolute right-8 top-12 w-48 bg-white rounded-2xl border border-gray-100 overflow-hidden z-20 animate-in fade-in slide-in-from-top-2"
                v-click-outside="() => openMenuId = null">
                <div class="p-1.5 space-y-0.5 text-left">
                  <button @click="viewDetails(report)"
                    class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-left text-xs font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                      </path>
                    </svg>
                    View details
                  </button>
                  <div v-if="report.status === 'pending'">
                    <div class="h-px bg-gray-50 my-1 mx-2"></div>
                    <button @click="initResolve(report, 'resolve')"
                      class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-left text-xs font-bold text-green-600 hover:bg-green-50 transition-colors">
                      <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                      </svg>
                      Resolve
                    </button>
                    <button @click="initResolve(report, 'dismiss')"
                      class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-left text-xs font-bold text-gray-400 hover:bg-gray-50 transition-colors">
                      <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                      </svg>
                      Dismiss
                    </button>
                  </div>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div
      class="px-8 py-4 border-t border-gray-50 flex items-center justify-between bg-white text-[10px] font-black uppercase tracking-widest text-gray-400"
      v-if="pagination.links">
      <div>{{ pagination.from || 0 }}-{{ pagination.to || 0 }} / {{ pagination.total || 0 }}</div>
      <div class="flex items-center gap-1">
        <button v-for="link in pagination.links" :key="link.label" @click="fetchReports(link.url)" v-html="link.label"
          class="min-w-[32px] h-8 flex items-center justify-center rounded-lg transition-all border" :class="link.active
            ? 'bg-agri-forest text-white border-agri-forest'
            : 'bg-white text-gray-400 border-gray-100 hover:text-agri-forest hover:border-agri-forest'"
          :disabled="!link.url"></button>
      </div>
    </div>

    <!-- Report Detail Slide-over -->
    <div v-if="selectedReport" class="fixed inset-0 z-[100] flex justify-end overflow-hidden"
      @click="selectedReport = null">
      <div class="absolute inset-0 bg-black/20 backdrop-blur-sm transition-opacity"></div>

      <main class="relative w-full max-w-xl bg-white border-l border-gray-100 h-full flex flex-col animate-slide-in"
        @click.stop>
        <div class="p-8 border-b border-gray-50 flex items-center justify-between shrink-0">
          <div class="flex items-center gap-4">
            <div
              class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center text-red-600 border border-red-100">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                </path>
              </svg>
            </div>
            <div>
              <h3 class="text-xl font-bold text-gray-900 tracking-tighter">Report details</h3>
              <p
                class="text-[11px] font-semibold text-gray-400 uppercase tracking-[0.1em] mt-1.5 flex items-center gap-2">
                <span class="bg-gray-100 px-2 py-0.5 rounded text-gray-600">ID: #{{ selectedReport.id }}</span>
                <span>&bull;</span>
                <span>Reason: {{ selectedReport.reason }}</span>
              </p>
            </div>
          </div>
          <button @click="selectedReport = null"
            class="p-3 text-gray-400 hover:text-gray-900 hover:bg-gray-100 rounded-2xl transition-all">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <div class="p-10 flex-1 overflow-y-auto space-y-10 custom-scrollbar">
          <div class="space-y-4">
            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] ml-1">REPORTER'S NOTES</h4>
            <div class="p-8 bg-gray-50 rounded-[2rem] border border-gray-100 italic text-gray-600 text-lg font-medium">
              "{{ selectedReport.description || 'No additional notes provided by the reporter.' }}"
            </div>
          </div>

          <div v-if="selectedReport.post" class="space-y-4">
            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] ml-1">REPORTED CONTENT</h4>
            <div class="p-8 bg-red-50/50 rounded-[2rem] border border-red-100/50 space-y-6">
              <p class="text-lg font-medium text-gray-900 leading-relaxed italic">"{{ selectedReport.post.content ||
                '[Image/Product content]' }}"</p>
              <div class="flex items-center gap-4 pt-4 border-t border-red-100 italic">
                <div class="text-[9px] font-bold uppercase tracking-widest text-red-500">OWNER ID #{{
                  selectedReport.post.user_id }}</div>
                <div class="w-1 h-1 rounded-full bg-red-200"></div>
                <div class="text-[9px] font-bold uppercase tracking-widest text-red-500">Status: {{
                  selectedReport.post.status }}</div>
              </div>
            </div>
          </div>
        </div>

        <div v-if="selectedReport.status === 'pending'"
          class="p-8 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3 shrink-0">
          <button @click="initResolve(selectedReport, 'dismiss')"
            class="px-8 h-12 bg-white text-gray-400 hover:text-gray-900 rounded-2xl text-[10px] font-black uppercase tracking-widest border border-gray-200 transition-all">Dismiss</button>
          <button @click="initResolve(selectedReport, 'resolve')"
            class="px-10 h-12 bg-green-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest">Resolve</button>
        </div>
        <div v-else class="p-8 bg-gray-100/50 border-t border-gray-50 text-center shrink-0">
          <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Resolved by {{
            selectedReport.resolver?.name || 'System' }} on {{ dayjs(selectedReport.resolved_at).format('MMM D, YYYY')
            }}</p>
        </div>
      </main>
    </div>

    <!-- Resolve Modal -->
    <Modal :is-open="modal.open" :title="modal.action === 'resolve' ? 'Resolve report' : 'Dismiss report'"
      :type="modal.action === 'resolve' ? 'success' : 'primary'" confirm-text="Confirm" @confirm="executeResolve"
      @cancel="modal.open = false" :loading="modal.loading">
      <div>
        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block ml-2">Notes</label>
        <textarea v-model="modal.note"
          class="w-full p-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-agri-forest/20 text-sm font-medium h-32 resize-none"
          placeholder="Enter reason for resolving/dismissing..."></textarea>
      </div>
    </Modal>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import dayjs from 'dayjs'
import Modal from '@/components/Modal.vue'

const vClickOutside = {
  mounted(el, binding) {
    el.clickOutsideEvent = (event) => {
      if (!(el === event.target || el.contains(event.target))) {
        binding.value(event);
      }
    };
    document.addEventListener('click', el.clickOutsideEvent);
  },
  unmounted(el) {
    document.removeEventListener('click', el.clickOutsideEvent);
  },
};

const loading = ref(true)
const reports = ref([])
const status = ref('pending')
const search = ref('')
const openMenuId = ref(null)
const selectedReport = ref(null)
const pagination = ref({})

const modal = ref({
  open: false,
  action: '',
  target: null,
  note: '',
  loading: false
})

const debounce = (fn, delay) => {
  let timeoutId;
  return (...args) => {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => fn(...args), delay);
  };
};

const fetchReports = async (url) => {
  if (typeof url !== 'string') {
    url = '/api/agripulse/moderation/reports'
  }

  loading.value = true
  openMenuId.value = null
  try {
    const params = {
      status: status.value,
      search: search.value
    }
    const response = await axios.get(url, { params })
    reports.value = response.data.data
    pagination.value = { from: response.data.from, to: response.data.to, total: response.data.total, links: response.data.links }
  } catch (error) {
    console.error('Failed to fetch reports:', error)
  } finally {
    loading.value = false
  }
}

const debouncedFetch = debounce(() => fetchReports(), 500)

const severityColor = (reason) => {
  if (['harassment', 'hate_speech', 'violence'].includes(reason)) return 'bg-red-500'
  if (['spam', 'scam'].includes(reason)) return 'bg-orange-500'
  return 'bg-blue-500'
}

const toggleMenu = (id) => {
  openMenuId.value = openMenuId.value === id ? null : id
}

const viewDetails = (report) => {
  selectedReport.value = report
  openMenuId.value = null
}

const initResolve = (report, action) => {
  modal.value = {
    open: true,
    action,
    target: report,
    note: '',
    loading: false
  }
  openMenuId.value = null
}

const executeResolve = async () => {
  modal.value.loading = true
  const { target, action, note } = modal.value

  try {
    await axios.post(`/api/agripulse/moderation/reports/${target.id}/${action}`, { note })
    modal.value.open = false

    if (selectedReport.value && selectedReport.value.id === target.id) {
      selectedReport.value = null
    }

    if (window.notify) {
      window.notify.success('Success', `Report marked as ${action}ed`)
    }
    fetchReports()
  } catch (error) {
    console.error(error)
    if (window.notify) {
      window.notify.error('Error', 'Failed to update report status')
    }
  } finally {
    modal.value.loading = false
  }
}

onMounted(() => fetchReports())
</script>

<style scoped>
.animate-in {
  animation: fade-in 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes fade-in {
  from {
    opacity: 0;
  }

  to {
    opacity: 1;
  }
}

@keyframes slide-in-from-top-2 {
  from {
    transform: translateY(-0.5rem);
  }

  to {
    transform: translateY(0);
  }
}

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
