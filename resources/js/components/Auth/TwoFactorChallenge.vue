<template>
    <Navigation />
    <div
        class="min-h-[90vh] bg-gray-50 flex items-center justify-center p-6 bg-gradient-to-br from-agri-sand/30 to-white">
        <div class="max-w-md w-full">
            <!-- Brand Identity -->
            <div class="mb-10 text-center">
                <div class="inline-flex items-center space-x-3 mb-4">
                    <div
                        class="w-12 h-12 bg-agri-forest rounded-2xl flex items-center justify-center shadow-lg shadow-agri-forest/20">
                        <ShieldIcon class="w-7 h-7 text-white" />
                    </div>
                    <span class="text-3xl font-black text-gray-900 tracking-tighter uppercase italic">IAM</span>
                </div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Security Protocol</h1>
                <p class="text-gray-500 font-medium">Multi-Factor Authentication Required</p>
            </div>

            <!-- Challenge Box -->
            <div
                class="bg-white rounded-[2.5rem] shadow-2xl shadow-agri-forest/5 border border-gray-100 p-8 md:p-10 relative overflow-hidden">
                <transition mode="out-in" enter-active-class="transition duration-300 ease-out"
                    enter-from-class="opacity-0 translate-y-4" enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition duration-200 ease-in" leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 translate-y-4">
                    <!-- Normal Challenge (App or Email) -->
                    <div v-if="!showRecovery" key="normal" class="space-y-8">
                        <div class="text-center">
                            <div v-if="authType === 'app'"
                                class="w-16 h-16 bg-agri-forest/5 rounded-2xl flex items-center justify-center mx-auto mb-6 text-agri-forest">
                                <DevicePhoneMobileIcon class="w-8 h-8" />
                            </div>
                            <div v-else
                                class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center mx-auto mb-6 text-indigo-600">
                                <EnvelopeIcon class="w-8 h-8" />
                            </div>
                            <h2 class="text-xl font-black text-gray-900 tracking-tight">
                                {{ authType === 'app' ? 'Authenticator App' : 'Email Verification' }}
                            </h2>
                            <p class="text-sm text-gray-500 font-medium leading-relaxed mt-2">
                                {{ authType === 'app'
                                    ? 'Enter the 6-digit verification code from your authenticator app.'
                                    : `We've dispatched a secure code to your registered email address.` }}
                            </p>
                        </div>

                        <form @submit.prevent="verify" class="space-y-6">
                            <div class="space-y-2">
                                <label
                                    class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Verification
                                    Code</label>
                                <input v-model="challenge.code" type="text" maxlength="6" required
                                    class="w-full px-6 py-5 bg-gray-50 border border-gray-100 rounded-3xl focus:ring-4 focus:ring-agri-forest/10 focus:border-agri-forest outline-none transition-all font-black text-center text-3xl tracking-[0.5em] placeholder:text-gray-200"
                                    placeholder="000000">
                            </div>

                            <button type="submit" :disabled="loading || challenge.code.length < 6"
                                class="w-full py-5 bg-agri-forest text-white rounded-[1.5rem] font-black text-sm uppercase tracking-widest hover:bg-agri-lush transition-all shadow-xl shadow-agri-forest/20 disabled:opacity-50">
                                {{ loading ? 'Synchronizing...' : 'Establish Access' }}
                            </button>
                        </form>

                        <button @click="showRecovery = true"
                            class="w-full text-xs font-black text-agri-forest uppercase tracking-widest hover:text-agri-lush transition-colors">
                            Lost Access? Use Recovery Code
                        </button>
                    </div>

                    <!-- Recovery Code Challenge -->
                    <div v-else key="recovery" class="space-y-8">
                        <div class="text-center">
                            <div
                                class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-6 text-amber-600">
                                <KeyIcon class="w-8 h-8" />
                            </div>
                            <h2 class="text-xl font-black text-gray-900 tracking-tight">Manual Access Override</h2>
                            <p class="text-sm text-gray-500 font-medium leading-relaxed mt-2">
                                Enter one of your security recovery codes to bypass multi-factor authentication.
                            </p>
                        </div>

                        <form @submit.prevent="verifyRecovery" class="space-y-6">
                            <div class="space-y-2">
                                <label
                                    class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Security
                                    Recovery Code</label>
                                <input v-model="challenge.recovery_code" type="text" required
                                    class="w-full px-6 py-5 bg-gray-50 border border-gray-100 rounded-3xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 outline-none transition-all font-black text-center tracking-widest placeholder:text-gray-200"
                                    placeholder="XXXXX-XXXXX">
                            </div>

                            <button type="submit" :disabled="loading || !challenge.recovery_code"
                                class="w-full py-5 bg-gray-900 text-white rounded-[1.5rem] font-black text-sm uppercase tracking-widest hover:bg-black transition-all shadow-xl disabled:opacity-50">
                                {{ loading ? 'Synchronizing...' : 'Override Protocol' }}
                            </button>
                        </form>

                        <button @click="showRecovery = false"
                            class="w-full text-xs font-black text-gray-400 uppercase tracking-widest hover:text-agri-forest transition-colors">
                            Back to Standard Challenge
                        </button>
                    </div>
                </transition>

                <!-- Decorative background elements -->
                <div class="absolute -bottom-20 -right-20 w-40 h-40 bg-agri-forest/5 rounded-full blur-3xl"></div>
            </div>

            <!-- Support & Navigation -->
            <div class="mt-10 space-y-4 text-center">
                <router-link to="/login"
                    class="inline-flex items-center text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-agri-forest transition-colors">
                    <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Return to Login Authority
                </router-link>
                <p class="text-[10px] font-bold text-gray-300 uppercase tracking-widest">
                    Protected by IAM Security Infrastructure
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import Navigation from '../Navigation.vue'
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../../stores/auth'

// Icons
const ShieldIcon = { template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6.223a.75.75 0 00-.384.736 12.59 12.59 0 005.591 10.314.75.75 0 00.793 0 12.593 12.593 0 005.591-10.314.75.75 0 00-.384-.736A11.959 11.959 0 0112 2.714z" /></svg>' }
const DevicePhoneMobileIcon = { template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" /></svg>' }
const EnvelopeIcon = { template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>' }
const KeyIcon = { template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" /></svg>' }

const authStore = useAuthStore()
const router = useRouter()
const route = useRoute()

const loading = ref(false)
const showRecovery = ref(false)
const authType = ref(route.query.type || 'app')
const email = ref(route.query.email || '')

const challenge = ref({
    email: email.value,
    code: '',
    recovery_code: ''
})

const verify = async () => {
    loading.value = true
    try {
        const result = await authStore.verifyTwoFactor({
            email: challenge.value.email,
            code: challenge.value.code
        })

        if (result.success) {
            if (window.notify) window.notify.success('Identity Confrmed', 'MFA challenge successful. Access established.')
            router.push('/dashboard')
        } else {
            if (window.notify) window.notify.error('Access Failure', result.message || 'Verification code failed.')
        }
    } catch (error) {
        if (window.notify) window.notify.error('Protocol Error', 'Verification service unreachable.')
    } finally {
        loading.value = false
    }
}

const verifyRecovery = async () => {
    loading.value = true
    try {
        const result = await authStore.verifyTwoFactor({
            email: challenge.value.email,
            recovery_code: challenge.value.recovery_code
        })

        if (result.success) {
            if (window.notify) window.notify.success('Identity Overridden', 'Recovery code accepted. Access established.')
            router.push('/dashboard')
        } else {
            if (window.notify) window.notify.error('Access Failure', result.message || 'Recovery code failed.')
        }
    } catch (error) {
        if (window.notify) window.notify.error('Protocol Error', 'Verification service unreachable.')
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    if (!challenge.value.email) {
        router.push('/login')
    }
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

div {
    font-family: 'Inter', sans-serif;
}

input::placeholder {
    letter-spacing: normal;
}
</style>
