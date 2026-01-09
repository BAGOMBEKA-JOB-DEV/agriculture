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
                @click="activeTab = tab.id"
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
                    <button type="button" class="px-8 py-4 bg-gray-50 text-gray-500 rounded-2xl font-black text-sm hover:bg-gray-100 transition-all">
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
                                <input 
                                    v-model="passwordData.current_password"
                                    type="password" 
                                    class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none font-bold text-gray-700"
                                    placeholder="••••••••"
                                >
                            </div>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-xs font-black text-gray-400 uppercase tracking-widest px-1">New Policy Password</label>
                                    <input 
                                        v-model="passwordData.password"
                                        type="password" 
                                        class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none font-bold text-gray-700"
                                        placeholder="••••••••"
                                    >
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-black text-gray-400 uppercase tracking-widest px-1">Confirm New Policy</label>
                                    <input 
                                        v-model="passwordData.password_confirmation"
                                        type="password" 
                                        class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none font-bold text-gray-700"
                                        placeholder="••••••••"
                                    >
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

const authStore = useAuthStore()
const activeTab = ref('profile')
const loading = ref(false)

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
            window.notify.error('Sync Error', error.response?.data?.message || 'Protocol failure during sync.')
        }
    } finally {
        loading.value = false
    }
}

const savePassword = async () => {
    loading.value = true
    try {
        await axios.post('/api/user/change-password', passwordData.value)
        if (window.notify) {
            window.notify.success('Protocol Secured', 'Your access credentials have been reset.')
        }
        passwordData.value = { current_password: '', password: '', password_confirmation: '' }
    } catch (error) {
        if (window.notify) {
            window.notify.error('Access Failure', error.response?.data?.errors?.current_password?.[0] || 'Verification fail.')
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
