<template>
  <div class="space-y-6">
    <!-- Premium Metric Cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div v-for="stat in reportStats" :key="stat.label"
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

    <div class="bg-white rounded-3xl border border-gray-100 overflow-hidden flex flex-col h-full relative">
      <!-- Header -->
      <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-white sticky top-0 z-20">
        <div>
          <h2 class="text-xl font-bold text-gray-900 tracking-tight">Report Center</h2>
          <p class="text-[11px] text-gray-400 font-semibold uppercase tracking-wider mt-1.5">Actionable safety signals
          </p>
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
              <td colspan="6" class="px-8 py-12 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">
                No
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
                <div class="flex items-center gap-3">
                  <img :src="report.reporter?.avatar_url" :alt="report.reporter?.name"
                    class="w-8 h-8 rounded-lg object-cover bg-gray-50 shrink-0">
                  <div class="flex flex-col">
                    <span class="text-xs font-bold text-gray-900">{{ report.reporter?.name }}</span>
                    <span class="text-[9px] text-gray-400 uppercase tracking-widest">ID #{{ report.reporter_id }}</span>
                  </div>
                </div>
              </td>
              <td class="px-8 py-5 text-right text-[10px] font-bold text-gray-400">
                {{ dayjs(report.created_at).format('MMM D, YY h:mm A') }}
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
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                          </path>
                        </svg>
                        Resolve
                      </button>
                      <button @click="initResolve(report, 'dismiss')"
                        class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-left text-xs font-bold text-gray-400 hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12">
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
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm transition-opacity"></div>

        <main
          class="relative w-full max-w-5xl bg-white border-l border-gray-100 h-full flex flex-col animate-slide-in shadow-2xl"
          @click.stop>
          <!-- Header -->
          <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between shrink-0 bg-white z-10">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-2xl flex items-center justify-center border transition-colors"
                :class="severityDetailClass(selectedReport.reason)">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                  </path>
                </svg>
              </div>
              <div>
                <h3 class="text-xl font-bold text-gray-900 tracking-tight">Investigation Case #{{ selectedReport.id }}
                </h3>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mt-1">
                  Opened {{ dayjs(selectedReport.created_at).fromNow() }}
                </p>
              </div>
            </div>
            <button @click="selectedReport = null"
              class="p-2 text-gray-400 hover:text-gray-900 hover:bg-gray-100 rounded-xl transition-all">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <!-- Body Grid -->
          <div class="flex-1 overflow-hidden grid grid-cols-1 md:grid-cols-12 bg-gray-50/50">
            <!-- Left Sidebar: Context & Reporter -->
            <div class="md:col-span-4 bg-white border-r border-gray-100 overflow-y-auto custom-scrollbar p-8 space-y-8">

              <!-- Status Card -->
              <div>
                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Current Status</h4>
                <div class="flex items-center gap-3">
                  <span class="px-3 py-1.5 rounded-lg text-xs font-black uppercase tracking-wider" :class="{
                    'bg-yellow-100 text-yellow-700': selectedReport.status === 'pending',
                    'bg-green-100 text-green-700': selectedReport.status === 'resolved',
                    'bg-gray-100 text-gray-600': selectedReport.status === 'dismissed'
                  }">
                    {{ selectedReport.status }}
                  </span>
                  <span v-if="selectedReport.resolver" class="text-xs font-medium text-gray-500">
                    by {{ selectedReport.resolver.name }}
                  </span>
                </div>
              </div>

              <!-- Reporter Profile -->
              <div class="pt-6 border-t border-gray-50">
                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Reporter Intelligence
                </h4>
                <div class="flex items-center gap-4 mb-4">
                  <div class="w-12 h-12 rounded-xl bg-gray-100 overflow-hidden border border-gray-200">
                    <img v-if="selectedReport.reporter?.avatar_url" :src="selectedReport.reporter.avatar_url"
                      class="w-full h-full object-cover">
                    <span v-else
                      class="w-full h-full flex items-center justify-center text-xs font-bold text-gray-400">{{
                        selectedReport.reporter?.name?.[0] || '?' }}</span>
                  </div>
                  <div>
                    <h3 class="text-sm font-bold text-gray-900 leading-tight">{{ selectedReport.reporter?.name ||
                      'Unknown User' }}</h3>
                    <span class="text-[10px] font-bold text-gray-400 block mt-0.5">{{ selectedReport.reporter?.email ||
                      'No email' }}</span>
                  </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                  <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                    <span class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Role</span>
                    <span class="text-xs font-bold text-gray-700">{{ selectedReport.reporter?.roles?.[0]?.name || 'User'
                      }}</span>
                  </div>
                  <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                    <span class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">User
                      ID</span>
                    <span class="font-mono text-xs font-bold text-gray-700">#{{ selectedReport.reporter_id }}</span>
                  </div>
                </div>
              </div>

              <!-- Report Reason -->
              <div class="pt-6 border-t border-gray-50">
                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Report Context</h4>
                <div class="space-y-4">
                  <div>
                    <span class="text-xs font-medium text-gray-500">Primary Reason</span>
                    <p class="text-sm font-bold text-gray-900 mt-1 capitalize">{{ selectedReport.reason.replace('_', ' ') }}</p>
                  </div>
                  <div class="p-4 bg-red-50 rounded-xl border border-red-100">
                    <span class="text-[9px] font-black text-red-400 uppercase tracking-widest mb-2 block">Reporter's
                      Note</span>
                    <p class="text-sm font-medium text-gray-700 italic">"{{ selectedReport.description || 'No additional details provided.' }}"</p>
                  </div>
                </div>
              </div>

              <!-- Resolution Verdict -->
              <div v-if="selectedReport.status !== 'pending'" class="pt-6 border-t border-gray-50">
                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Resolution Verdict</h4>
                <div class="p-4 bg-green-50 rounded-xl border border-green-100">
                  <div class="flex items-center gap-2 mb-2">
                    <span class="text-[9px] font-black text-green-600 uppercase tracking-widest">Final Note</span>
                    <span class="text-[9px] text-green-400 font-bold ml-auto" v-if="selectedReport.resolved_at">
                      {{ dayjs(selectedReport.resolved_at).format('MMM D, h:mm A') }}
                    </span>
                  </div>
                  <p class="text-xs font-semibold text-gray-700 italic">"{{ selectedReport.resolution_note || 'No resolution note provided.' }}"</p>
                  <div class="mt-3 pt-3 border-t border-green-100 flex items-center justify-between">
                    <span class="text-[9px] font-black text-green-600 uppercase tracking-widest">Moderator</span>
                    <span class="text-[10px] font-bold text-gray-900">{{ selectedReport.resolver?.name || 'System'
                      }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Right: Content -->
            <div class="md:col-span-8 overflow-y-auto custom-scrollbar p-8">
              <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6">Red Flagged Content</h4>

              <div v-if="selectedReport.post"
                class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
                <!-- Post Header -->
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gray-100 overflow-hidden border border-gray-200">
                      <img v-if="selectedReport.post.user?.avatar_url" :src="selectedReport.post.user.avatar_url"
                        class="w-full h-full object-cover">
                      <span v-else
                        class="w-full h-full flex items-center justify-center text-xs font-bold text-gray-400">{{
                          selectedReport.post.user?.name?.[0] }}</span>
                    </div>
                    <div>
                      <h3 class="text-sm font-bold text-gray-900">{{ selectedReport.post.user?.name || 'Unknown Author'
                        }}
                      </h3>
                      <p class="text-[10px] text-gray-500 font-bold">Posted {{
                        dayjs(selectedReport.post.created_at).format('MMM D, h:mm A') }}</p>
                    </div>
                  </div>
                  <span
                    class="px-3 py-1 bg-gray-100 rounded-lg text-xs font-bold text-gray-600 uppercase tracking-wider">
                    {{ selectedReport.post.status }}
                  </span>
                </div>

                <!-- Post Content -->
                <div class="p-6 space-y-4">
                  <p class="text-base text-gray-800 leading-relaxed whitespace-pre-line">{{ selectedReport.post.content
                    }}
                  </p>

                  <div v-if="selectedReport.post.image_url"
                    class="rounded-2xl overflow-hidden border border-gray-100 bg-gray-50">
                    <img :src="selectedReport.post.image_url" class="w-full max-h-80 object-cover">
                  </div>
                </div>

                <!-- Stats -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center gap-6">
                  <div class="flex items-center gap-2 text-gray-500">
                    <span class="font-bold text-gray-900">{{ selectedReport.post.reactions_count || 0 }}</span>
                    <span class="text-xs font-bold uppercase tracking-wider">Reactions</span>
                  </div>
                  <div class="flex items-center gap-2 text-gray-500">
                    <span class="font-bold text-gray-900">{{ selectedReport.post.comments_count || 0 }}</span>
                    <span class="text-xs font-bold uppercase tracking-wider">Comments</span>
                  </div>
                  <div class="flex items-center gap-2 text-gray-500">
                    <span class="font-bold text-gray-900">{{ selectedReport.post.reposts_count || 0 }}</span>
                    <span class="text-xs font-bold uppercase tracking-wider">Reposts</span>
                  </div>
                  <!-- View Post Button -->
                  <a :href="`/agripulse/post/${selectedReport.post.id}`" target="_blank"
                    class="ml-auto text-xs font-black text-agri-forest uppercase tracking-widest hover:underline flex items-center gap-1">
                    Open on Feed
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                  </a>
                </div>
              </div>

              <div v-else class="p-12 text-center bg-gray-100 rounded-3xl border border-gray-200 border-dashed">
                <p class="text-sm font-bold text-gray-500 italic">Content has been deleted or is unavailable.</p>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div v-if="selectedReport.status === 'pending'"
            class="p-6 bg-white border-t border-gray-100 flex items-center justify-between shrink-0 shadow-[0_-4px_20px_rgba(0,0,0,0.02)]">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
              Action Required
            </p>
            <div class="flex items-center gap-3">
              <button @click="initResolve(selectedReport, 'dismiss')"
                class="px-6 h-11 bg-white hover:bg-gray-50 text-gray-500 hover:text-gray-900 rounded-xl text-[10px] font-black uppercase tracking-widest border border-gray-200 transition-all shadow-sm">
                Dismiss Report
              </button>
              <button @click="initResolve(selectedReport, 'resolve')"
                class="px-8 h-11 bg-agri-forest hover:bg-agri-lush text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-agri-forest/20 transition-all">
                Take Action / Resolve
              </button>
            </div>
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
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import axios from 'axios'
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import Modal from '@/components/Modal.vue'

dayjs.extend(relativeTime)

const props = defineProps({
  selectedReportId: {
    type: [Number, String],
    default: null
  },
  globalStats: {
    type: Object,
    default: () => ({})
  }
})

const reportStats = computed(() => [
  {
    label: 'Total Reports',
    value: props.globalStats.total_reports || 0,
    iconPath: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    bgClass: 'from-blue-400/10 to-blue-500/5',
    borderClass: 'border-blue-200/30',
    glowClass: 'bg-blue-500/10',
    iconBg: 'bg-blue-500',
    textColor: 'text-blue-700',
    labelColor: 'text-blue-600/70'
  },
  {
    label: 'Pending Review',
    value: props.globalStats.pending_reports || 0,
    iconPath: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
    bgClass: 'from-yellow-400/10 to-yellow-500/5',
    borderClass: 'border-yellow-200/30',
    glowClass: 'bg-yellow-500/10',
    iconBg: 'bg-yellow-500',
    textColor: 'text-yellow-700',
    labelColor: 'text-yellow-600/70'
  },
  {
    label: 'Processed',
    value: props.globalStats.resolved_reports || 0,
    iconPath: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    bgClass: 'from-green-400/10 to-green-500/5',
    borderClass: 'border-green-200/30',
    glowClass: 'bg-green-500/10',
    iconBg: 'bg-green-500',
    textColor: 'text-green-700',
    labelColor: 'text-green-600/70'
  },
  {
    label: 'Dismissed',
    // Fallback since we only pulled 'resolved_reports' which combines resolved+dismissed. 
    // If strict separation is needed, we'd need another API field, but for now this is semantic enough.
    // Or we can just calculate it if we track it. For now let's just use 'Processed' as the main stat and maybe 'Total' as the 4th?
    // Let's actually show 'High Priority' if we have flagged posts count, or just 0 as placeholder if the key is missing.
    // Actually, 'Dismissed' is useful.
    value: (props.globalStats.total_reports || 0) - (props.globalStats.pending_reports || 0) - (props.globalStats.resolved_reports || 0) > 0 ? (props.globalStats.total_reports - props.globalStats.pending_reports - props.globalStats.resolved_reports) : 0,
    // Simple math check: Total = Pending + Resolved + Dismissed. Solves via subtraction, though imprecise if 'resolved' is a bucket.
    // Let's just use a placeholder based on 'resolved_reports' if granular data isn't there, or 0.
    // Wait, dashboard stats 'resolved_reports' INCLUDES dismissed. So Processed = Resolved + Dismissed.
    // So distinct 'Resolved' vs 'Dismissed' isn't possible from that one field.
    // Let's rename this card to 'Actioned Today' or 'New Today' using another field?
    // Or just 'Flagged Content' count.
    value: props.globalStats.flagged_posts || 0,
    label: 'Red Flagged',
    iconPath: 'M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-7h1',
    bgClass: 'from-red-400/10 to-red-500/5',
    borderClass: 'border-red-200/30',
    glowClass: 'bg-red-500/10',
    iconBg: 'bg-red-500',
    textColor: 'text-red-700',
    labelColor: 'text-red-600/70'
  }
])

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

const severityDetailClass = (reason) => {
  if (['harassment', 'hate_speech', 'violence'].includes(reason)) return 'bg-red-50 text-red-600 border-red-100'
  if (['spam', 'scam'].includes(reason)) return 'bg-orange-50 text-orange-600 border-orange-100'
  return 'bg-blue-50 text-blue-600 border-blue-100'
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

onMounted(async () => {
  await fetchReports()
  if (props.selectedReportId) {
    const report = reports.value.find(r => r.id == props.selectedReportId)
    if (report) {
      viewDetails(report)
    }
  }
})

watch(() => props.selectedReportId, (newId) => {
  if (newId) {
    const report = reports.value.find(r => r.id == newId)
    if (report) {
      viewDetails(report)
    }
  }
})
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
