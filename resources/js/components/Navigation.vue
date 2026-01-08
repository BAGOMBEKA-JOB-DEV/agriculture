<template>
  <!-- Navigation -->
  <nav class="bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-100 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-20">
        <div class="flex items-center">
          <router-link to="/" class="flex items-center space-x-3 group">
            <div class="w-10 h-10 rounded-xl bg-agri-forest flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform duration-300">
               <svg class="w-6 h-6 text-agri-earth" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
               </svg>
            </div>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight group-hover:text-agri-forest transition-colors">INTERNATIONAL AGRO MARKETPLACE (IAM)
</h1>
          </router-link>
        </div>

        <!-- Desktop Navigation -->
        <div class="hidden md:flex items-center space-x-6">
          <router-link
            v-for="item in navItems"
            :key="item.path"
            :to="item.path"
            class="text-sm font-semibold text-gray-500 hover:text-agri-forest px-3 py-2 rounded-xl hover:bg-agri-sand/20 transition-all duration-200"
          >
            {{ item.name }}
          </router-link>
          
          <div class="h-6 w-px bg-gray-200 mx-2"></div>
          
          <router-link
            to="/login"
            class="text-sm font-bold text-agri-forest hover:text-agri-lush px-4 py-2 transition-colors duration-200"
          >
            Sign In
          </router-link>
          <router-link
            to="/register"
            class="bg-agri-forest text-white hover:bg-agri-lush px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-xl hover:-translate-y-0.5"
          >
            Join Marketplace
          </router-link>
        </div>

        <!-- Mobile menu button -->
        <div class="md:hidden flex items-center">
          <button
            @click="mobileMenuOpen = !mobileMenuOpen"
            class="text-gray-500 hover:text-agri-forest p-2 rounded-xl focus:outline-none transition-colors"
          >
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Mobile Navigation -->
      <transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition duration-100 ease-in"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
      >
        <div v-show="mobileMenuOpen" class="md:hidden absolute left-4 right-4 mt-2 p-4 bg-white rounded-2xl shadow-2xl border border-gray-100 transform origin-top overflow-hidden">
          <div class="space-y-2">
            <router-link
              v-for="item in navItems"
              :key="item.path"
              :to="item.path"
              @click="mobileMenuOpen = false"
              class="block px-4 py-3 text-base font-semibold text-gray-700 hover:text-agri-forest hover:bg-agri-sand/20 rounded-xl transition-all"
            >
              {{ item.name }}
            </router-link>
            
            <div class="border-t border-gray-100 pt-4 mt-4 flex flex-col space-y-3">
              <router-link
                to="/login"
                @click="mobileMenuOpen = false"
                class="block px-4 py-3 text-base font-bold text-agri-forest text-center"
              >
                Sign In
              </router-link>
              <router-link
                to="/register"
                @click="mobileMenuOpen = false"
                class="block px-4 py-4 text-base font-bold bg-agri-forest text-white rounded-xl text-center shadow-lg"
              >
                Join Marketplace
              </router-link>
            </div>
          </div>
        </div>
      </transition>
    </div>
  </nav>
</template>

<script setup>
import { ref } from 'vue'

const mobileMenuOpen = ref(false)

const navItems = [
  { name: 'Home', path: '/' },
  { name: 'Marketplace', path: '/marketplace' },
  { name: 'Trade Tiers', path: '/subscriptions' }
]
</script>
