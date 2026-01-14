<template>
  <div class="min-h-screen bg-[#F8FAFB] pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
      <!-- Header Area -->
      <div class="mb-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
          <h1 class="text-3xl font-black text-gray-900 tracking-tight">Personal Nexus</h1>
          <p class="text-gray-500 font-medium">Manage your identity, security protocols, and discovery preferences.</p>
        </div>
        <div class="flex items-center space-x-3">
            <div class="px-4 py-2 bg-agri-forest/5 rounded-2xl border border-agri-forest/10 flex items-center space-x-3">
                <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                <span class="text-xs font-black text-agri-forest uppercase tracking-widest leading-none">Account Status: Active</span>
            </div>
            <div class="px-4 py-2 bg-indigo-50 rounded-2xl border border-indigo-100 flex items-center space-x-3">
                <span class="text-xs font-black text-indigo-600 uppercase tracking-widest leading-none">{{ authStore.user?.role }} Tier</span>
            </div>
        </div>
      </div>

      <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Navigation -->
        <aside class="w-full lg:w-72 shrink-0">
          <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden sticky top-24">
            <nav class="p-4 space-y-2">
              <button 
                v-for="tab in tabs" 
                :key="tab.id"
                @click="activeTab = tab.id; resetPasswordForm()"
                :class="[
                  'w-full flex items-center space-x-4 px-5 py-4 rounded-2xl transition-all duration-300 group',
                  activeTab === tab.id 
                    ? 'bg-agri-forest text-white shadow-lg shadow-agri-forest/20' 
                    : 'text-gray-500 hover:bg-agri-sand/30 hover:text-agri-forest'
                ]"
              >
                <div :class="[
                  'w-10 h-10 rounded-xl flex items-center justify-center transition-colors',
                  activeTab === tab.id ? 'bg-white/20 text-white' : 'bg-gray-50 text-gray-400 group-hover:bg-agri-forest/10 group-hover:text-agri-forest'
                ]">
                  <component :is="tab.icon" class="w-5 h-5" />
                </div>
                <span class="font-bold text-sm tracking-tight">{{ tab.name }}</span>
              </button>
            </nav>
            
            <div class="p-6 border-t border-gray-50 bg-gray-50/50">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-2 h-2 rounded-full bg-agri-earth"></div>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Compliance Check</span>
                </div>
                <p class="text-[10px] text-gray-500 font-bold leading-relaxed">
                    Your profile verification is 100% complete for the Current Trade Cycle (Jan 2026).
                </p>
            </div>
          </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1">
          <transition 
            mode="out-in"
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 translate-y-4"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-4"
          >
            <!-- Profile Tab -->
            <div v-if="activeTab === 'profile'" key="profile" class="space-y-8">
              <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center justify-between mb-10">
                    <div>
                        <h2 class="text-xl font-black text-gray-900 tracking-tight">Identity & Reach</h2>
                        <p class="text-sm text-gray-500 font-medium">Update how other traders see and contact you.</p>
                    </div>
                    <div class="w-16 h-16 rounded-2xl bg-agri-sand/30 flex items-center justify-center text-agri-forest">
                        <UserIcon class="w-8 h-8" />
                    </div>
                </div>

                <!-- Profile Photo Section -->
                <div class="mb-10 p-8 bg-gray-50/50 rounded-[2rem] border border-gray-100 shadow-sm transition-all group/avatar">
                    <div class="flex flex-col md:flex-row items-center gap-10">
                        <div class="relative">
                            <!-- Image Container -->
                            <div class="w-32 h-32 md:w-36 md:h-36 rounded-[2rem] overflow-hidden border-4 border-white shadow-md bg-white flex items-center justify-center relative z-10">
                                <img 
                                    v-if="avatarPreview || authStore.user?.avatar_url" 
                                    :src="avatarPreview || authStore.user?.avatar_url" 
                                    class="w-full h-full object-cover"
                                    :class="{'opacity-40 grayscale': uploadingAvatar}"
                                >
                                <div v-else class="flex flex-col items-center">
                                    <UserIcon class="w-12 h-12 text-gray-200" />
                                </div>

                                <!-- Loading Overlay -->
                                <div v-if="uploadingAvatar" class="absolute inset-0 bg-white/60 flex items-center justify-center">
                                    <div class="animate-spin rounded-full h-8 w-8 border-3 border-agri-forest border-t-transparent"></div>
                                </div>
                            </div>
                            
                            <!-- Upload Button Overlay -->
                            <label class="absolute -bottom-2 -right-2 w-12 h-12 bg-agri-forest text-white rounded-2xl shadow-lg border-4 border-white flex items-center justify-center cursor-pointer hover:bg-agri-lush transition-all z-20 group">
                                <input type="file" class="hidden" @change="handleFileChange" accept="image/*" :disabled="uploadingAvatar">
                                <CameraIcon class="w-6 h-6" />
                            </label>
                        </div>

                        <div class="flex-1 space-y-4 text-center md:text-left">
                            <div>
                                <h3 class="text-base font-black text-gray-900 uppercase tracking-widest flex items-center justify-center md:justify-start">
                                    Profile Photo
                                    <span v-if="avatarFile" class="ml-3 px-2 py-0.5 bg-amber-50 text-amber-600 text-[10px] font-black uppercase tracking-widest rounded-md border border-amber-100">
                                        Pending Save
                                    </span>
                                </h3>
                                <p class="text-xs text-gray-500 font-medium leading-relaxed max-w-sm mt-1">
                                    Upload a professional photo so other traders can recognize you. Max size 2MB (JPG, PNG).
                                </p>
                            </div>

                            <!-- Controls -->
                            <div v-if="avatarFile" class="flex items-center justify-center md:justify-start space-x-3 pt-1">
                                <button 
                                    @click="uploadAvatar" 
                                    :disabled="uploadingAvatar"
                                    class="px-6 py-2.5 bg-agri-forest text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-agri-lush transition-all flex items-center shadow-md shadow-agri-forest/20 disabled:opacity-70 disabled:cursor-not-allowed"
                                >
                                    <div v-if="uploadingAvatar" class="w-3.5 h-3.5 mr-2 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                                    <svg v-else class="w-3.5 h-3.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                                    {{ uploadingAvatar ? 'Saving...' : 'Save Changes' }}
                                </button>
                                <button 
                                    @click="cancelAvatarChange" 
                                    :disabled="uploadingAvatar"
                                    class="px-6 py-2.5 bg-white text-gray-500 text-[10px] font-black uppercase tracking-widest rounded-xl border border-gray-200 hover:bg-gray-50 transition-all"
                                >
                                    Cancel
                                </button>
                            </div>
                            <div v-else class="flex flex-col space-y-1">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Identity Status: Verified</span>
                            </div>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="saveProfile" class="space-y-6">
                  <div class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                      <label class="text-xs font-black text-gray-400 uppercase tracking-widest px-1">Full Name</label>
                      <input 
                        v-model="profileData.name"
                        type="text" 
                        class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-agri-forest/10 focus:border-agri-forest outline-none transition-all font-bold text-gray-700"
                        placeholder="e.g., John Export"
                      >
                    </div>
                    <div class="space-y-2">
                      <label class="text-xs font-black text-gray-400 uppercase tracking-widest px-1">Primary Email</label>
                      <input 
                        v-model="profileData.email"
                        type="email" 
                        class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-agri-forest/10 focus:border-agri-forest outline-none transition-all font-bold text-gray-700"
                        placeholder="trade@company.com"
                      >
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-black text-gray-400 uppercase tracking-widest px-1">Mobile Access</label>
                        <input 
                          v-model="profileData.phone"
                          type="text" 
                          class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-agri-forest/10 focus:border-agri-forest outline-none transition-all font-bold text-gray-700"
                          placeholder="+254 7XX XXX XXX"
                        >
                      </div>
                      <div class="space-y-2">
                        <label class="text-xs font-black text-gray-400 uppercase tracking-widest px-1">Global Trade Base</label>
                        <input 
                          v-model="profileData.location"
                          type="text" 
                          class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-agri-forest/10 focus:border-agri-forest outline-none transition-all font-bold text-gray-700"
                          placeholder="Nairobi, Kenya"
                        >
                      </div>
                  </div>

                  <div class="pt-6 border-t border-gray-50 flex items-center space-x-4">
                    <button 
                      type="submit"
                      :disabled="loading"
                      class="px-8 py-4 bg-agri-forest text-white rounded-2xl font-black text-sm hover:bg-agri-lush transition-all shadow-lg shadow-agri-forest/20 disabled:opacity-50"
                    >
                      {{ loading ? 'Synchronizing...' : 'Update Identity' }}
                    </button>
                    <button @click="resetDefaults" type="button" class="px-8 py-4 bg-gray-50 text-gray-500 rounded-2xl font-black text-sm hover:bg-gray-100 transition-all">
                        Reset Defaults
                    </button>
                  </div>
                </form>
              </div>
            </div>

            <!-- Security Tab -->
            <div v-else-if="activeTab === 'security'" key="security" class="space-y-8">
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                    <div class="flex items-center justify-between mb-10">
                        <div>
                            <h2 class="text-xl font-black text-gray-900 tracking-tight">Access Credentials</h2>
                            <p class="text-sm text-gray-500 font-medium">Protect your portal with enterprise-grade encryption.</p>
                        </div>
                        <div class="w-16 h-16 rounded-2xl bg-red-50 flex items-center justify-center text-red-600">
                            <ShieldIcon class="w-8 h-8" />
                        </div>
                    </div>

                    <form @submit.prevent="savePassword" class="space-y-6">
                        <div class="grid gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-black text-gray-400 uppercase tracking-widest px-1">Current Protocol Password</label>
                                <div class="relative">
                                  <input 
                                      v-model="passwordData.current_password"
                                      :type="showCurrent ? 'text' : 'password'" 
                                      class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none font-bold text-gray-700 pr-12"
                                      placeholder="••••••••"
                                  >
                                  <button 
                                    @click="showCurrent = !showCurrent" 
                                    type="button" 
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-agri-forest transition-colors"
                                  >
                                    <component :is="showCurrent ? EyeSlashIcon : EyeIcon" class="w-5 h-5" />
                                  </button>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-xs font-black text-gray-400 uppercase tracking-widest px-1">New Policy Password</label>
                                    <div class="relative">
                                      <input 
                                          v-model="passwordData.password"
                                          :type="showNew ? 'text' : 'password'" 
                                          class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none font-bold text-gray-700 pr-12"
                                          placeholder="••••••••"
                                      >
                                      <button 
                                        @click="showNew = !showNew" 
                                        type="button" 
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-agri-forest transition-colors"
                                      >
                                        <component :is="showNew ? EyeSlashIcon : EyeIcon" class="w-5 h-5" />
                                      </button>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-black text-gray-400 uppercase tracking-widest px-1">Confirm New Policy</label>
                                    <div class="relative">
                                      <input 
                                          v-model="passwordData.password_confirmation"
                                          :type="showConfirm ? 'text' : 'password'" 
                                          class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none font-bold text-gray-700 pr-12"
                                          placeholder="••••••••"
                                      >
                                      <button 
                                        @click="showConfirm = !showConfirm" 
                                        type="button" 
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-agri-forest transition-colors"
                                      >
                                        <component :is="showConfirm ? EyeSlashIcon : EyeIcon" class="w-5 h-5" />
                                      </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-50">
                            <button 
                                type="submit"
                                :disabled="loading"
                                class="px-8 py-4 bg-red-600 text-white rounded-2xl font-black text-sm hover:bg-red-700 transition-all shadow-lg shadow-red-600/20 disabled:opacity-50"
                            >
                                {{ loading ? 'Updating Protocol...' : 'Change Password' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Notifications Tab -->
            <div v-else-if="activeTab === 'notifications'" key="notifications" class="space-y-8">
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                    <div class="flex items-center justify-between mb-10">
                        <div>
                            <h2 class="text-xl font-black text-gray-900 tracking-tight">Event Monitoring</h2>
                            <p class="text-sm text-gray-500 font-medium">Control how the marketplace alerts you of trade events.</p>
                        </div>
                        <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600">
                            <BellIcon class="w-8 h-8" />
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div 
                          v-for="pref in prefItems" 
                          :key="pref.id"
                          class="flex items-center justify-between p-6 bg-gray-50 rounded-2xl hover:bg-white hover:shadow-md transition-all border border-transparent hover:border-gray-100 group"
                        >
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center text-gray-400 group-hover:text-agri-forest transition-colors">
                                    <component :is="pref.icon" class="w-6 h-6" />
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 tracking-tight">{{ pref.name }}</h4>
                                    <p class="text-xs text-gray-500 font-medium">{{ pref.desc }}</p>
                                </div>
                            </div>
                            <button 
                              @click="togglePref(pref.id)"
                              :class="[
                                'w-14 h-8 rounded-full relative transition-all duration-300 outline-none',
                                profileData.notification_preferences[pref.id] ? 'bg-agri-forest' : 'bg-gray-300'
                              ]"
                            >
                                <div :class="[
                                    'absolute top-1 w-6 h-6 bg-white rounded-full shadow-sm transition-all duration-300',
                                    profileData.notification_preferences[pref.id] ? 'left-7' : 'left-1'
                                ]"></div>
                            </button>
                        </div>
                    </div>

                    <div class="pt-10 flex justify-end">
                        <button 
                          @click="saveProfile"
                          :disabled="loading"
                          class="px-8 py-4 bg-agri-forest text-white rounded-2xl font-black text-sm hover:bg-agri-lush transition-all shadow-lg"
                        >
                            {{ loading ? 'Saving...' : 'Save Monitoring Rules' }}
                        </button>
                    </div>
                </div>
            </div>
          </transition>
        </main>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import axios from 'axios'

// Inline Icons (replacing @heroicons/vue to avoid dependency errors)
const UserIcon = {
  template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" /></svg>'
}
const ShieldIcon = {
  template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6.223a.75.75 0 00-.384.736 12.59 12.59 0 005.591 10.314.75.75 0 00.793 0 12.593 12.593 0 005.591-10.314.75.75 0 00-.384-.736A11.959 11.959 0 0112 2.714z" /></svg>'
}
const BellIcon = {
  template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>'
}
const MessageIcon = {
  template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.303.025-.607.038-.913.038-.767 0-1.498-.108-2.185-.311a7.486 7.486 0 01-4.067 1.121c-1.168 0-2.261-.266-3.235-.742L3 18.75l1.242-4.14c-.476-.971-.742-2.067-.742-3.235 0-4.142 3.358-7.5 7.5-7.5 1.488 0 2.87.433 4.032 1.178A7.48 7.48 0 0115 11.25V12c0 .414.336.75.75.75s.75-.336.75-.75V8.511c0-.969.616-1.813 1.5-2.097a8.963 8.963 0 012.25 0z" /></svg>'
}
const BoxIcon = {
  template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-1.24-.925-2.323-2.11-2.455a48.098 48.098 0 00-15.28 0c-1.185.132-2.11 1.24-2.11 2.455z" /></svg>'
}
const AlertIcon = {
  template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>'
}
const EyeIcon = {
  template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.644C3.483 8.633 7.513 5 12 5c4.486 0 8.517 3.633 9.964 6.678.067.141.067.303 0 .444C20.517 15.367 16.487 19 12 19c-4.486 0-8.517-3.633-9.964-6.678z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>'
}
const EyeSlashIcon = {
  template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>'
}
const CameraIcon = {
  template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" /></svg>'
}

const authStore = useAuthStore()
const router = useRouter()
const activeTab = ref('profile')
const loading = ref(false)

const showCurrent = ref(false)
const showNew = ref(false)
const showConfirm = ref(false)
const avatarPreview = ref(null)
const avatarFile = ref(null)
const uploadingAvatar = ref(false)

const profileData = ref({
  name: '',
  email: '',
  phone: '',
  location: '',
  notification_preferences: {
    email_messages: true,
    email_rfqs: true,
    system_alerts: true
  }
})

const passwordData = ref({
  current_password: '',
  password: '',
  password_confirmation: ''
})

const tabs = [
  { id: 'profile', name: 'Identity & Reach', icon: UserIcon },
  { id: 'security', name: 'Security Protocol', icon: ShieldIcon },
  { id: 'notifications', name: 'Event Monitoring', icon: BellIcon },
]

const prefItems = [
  { id: 'email_messages', name: 'Trade Communications', desc: 'Alert me via email for new inbox messages from partners.', icon: MessageIcon },
  { id: 'email_rfqs', name: 'Market RFQs', desc: 'Receive daily summaries of new procurement requests.', icon: BoxIcon },
  { id: 'system_alerts', name: 'Security Protocols', desc: 'Instant emails for login attempts or trade anomalies.', icon: AlertIcon },
]

const togglePref = (id) => {
    profileData.value.notification_preferences[id] = !profileData.value.notification_preferences[id]
}

const handleFileChange = (e) => {
    const file = e.target.files[0]
    if (file) {
        avatarFile.value = file
        const reader = new FileReader()
        reader.onload = (e) => {
            avatarPreview.value = e.target.result
        }
        reader.readAsDataURL(file)
    }
}

const cancelAvatarChange = () => {
    avatarFile.value = null
    avatarPreview.value = null
}

const uploadAvatar = async () => {
    if (!avatarFile.value) return
    uploadingAvatar.value = true
    const formData = new FormData()
    formData.append('avatar', avatarFile.value)

    try {
        const response = await axios.post('/api/user/avatar', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })
        authStore.user = response.data.user
        avatarPreview.value = null
        avatarFile.value = null
        if (window.notify) {
            window.notify.success('Success', 'Profile photo updated successfully.')
        }
    } catch (error) {
        if (window.notify) {
            window.notify.error('Error', error.response?.data?.message || 'Could not update your photo.')
        }
    } finally {
        uploadingAvatar.value = false
    }
}

const fetchUser = async () => {
  try {
    const response = await axios.get('/api/user')
    const user = response.data
    profileData.value = {
      name: user.name,
      email: user.email,
      phone: user.phone || '',
      location: user.location || '',
      notification_preferences: user.notification_preferences || {
        email_messages: true,
        email_rfqs: true,
        system_alerts: true
      }
    }
  } catch (error) {
    console.error('Failed to fetch user:', error)
  }
}

const resetDefaults = () => {
    fetchUser()
    if (window.notify) {
        window.notify.success('Protocol Reset', 'Identity parameters reverted to server state.')
    }
}

const resetPasswordForm = () => {
    passwordData.value = { current_password: '', password: '', password_confirmation: '' }
}

const saveProfile = async () => {
    loading.value = true
    try {
        const response = await axios.put('/api/user/profile', profileData.value)
        if (window.notify) {
            window.notify.success('Sync Complete', 'Your Nexus profile has been updated.')
        }
        // Update store
        authStore.user = response.data
    } catch (error) {
        if (window.notify) {
            const errors = error.response?.data?.errors;
            if (errors) {
                Object.keys(errors).forEach(key => {
                    window.notify.error('Profile Error', errors[key][0]);
                });
            } else {
                window.notify.error('Sync Error', error.response?.data?.message || 'Protocol failure during sync.')
            }
        }
    } finally {
        loading.value = false
    }
}

const savePassword = async () => {
    // Frontend Parity Check
    if (passwordData.value.password !== passwordData.value.password_confirmation) {
        if (window.notify) {
            window.notify.error('Access Failure', 'The new passwords do not match.')
        }
        return
    }

    if (passwordData.value.password.length < 8) {
        if (window.notify) {
            window.notify.error('Access Failure', 'The password must be at least 8 characters.')
        }
        return
    }

    loading.value = true
    try {
        await axios.post('/api/user/change-password', passwordData.value)
        if (window.notify) {
            window.notify.success('Protocol Secured', 'Your access credentials have been reset. Please login with your new credentials.')
        }
        
        // Mandatory Logout and Redirect
        await authStore.logout()
        router.push('/login')
    } catch (error) {
        if (window.notify) {
            const errors = error.response?.data?.errors;
            if (errors) {
                Object.keys(errors).forEach(key => {
                    window.notify.error('Access Failure', errors[key][0]);
                });
            } else {
                window.notify.error('Access Failure', error.response?.data?.message || 'Verification fail.')
            }
        }
    } finally {
        loading.value = false
    }
}

onMounted(() => {
  fetchUser()
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

div {
  font-family: 'Inter', sans-serif;
}
</style>
