<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <router-link to="/" class="text-2xl font-bold text-green-600">
              IAM
            </router-link>
            <div class="hidden md:ml-10 md:flex md:gap-x-8">
              <router-link to="/products" class="text-gray-700 hover:text-green-600 px-3 py-2 text-sm font-medium">
                Products
              </router-link>
              <router-link to="/subscriptions" class="text-gray-700 hover:text-green-600 px-3 py-2 text-sm font-medium">
                Subscriptions
              </router-link>
              <a href="#about" class="text-gray-700 hover:text-green-600 px-3 py-2 text-sm font-medium">
                About
              </a>
              <a href="#contact" class="text-gray-700 hover:text-green-600 px-3 py-2 text-sm font-medium">
                Contact Us
              </a>
            </div>
          </div>
          <div class="flex items-center gap-x-4">
            <template v-if="!authStore.isAuthenticated">
              <router-link to="/login" class="text-gray-700 hover:text-green-600 px-3 py-2 text-sm font-medium">
                Login
              </router-link>
              <router-link to="/register" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700">
                Register
              </router-link>
            </template>
            <template v-else>
              <span class="text-sm text-gray-700">{{ authStore.user?.name }}</span>
              <button @click="handleLogout" class="text-gray-700 hover:text-green-600 px-3 py-2 text-sm font-medium">
                Logout
              </button>
            </template>
          </div>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative bg-gradient-to-br from-green-600 to-green-800 text-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center">
          <h1 class="text-5xl md:text-6xl font-bold mb-6">
            International Agro Marketplace
          </h1>
          <p class="text-xl md:text-2xl mb-8 text-green-100">
            Connecting Agricultural Sellers with Bulk Buyers Worldwide
          </p>
          <div class="flex justify-center gap-x-4">
            <router-link to="/register" class="bg-white text-green-600 px-8 py-3 rounded-lg text-lg font-semibold hover:bg-green-50 transition">
              Start Selling
            </router-link>
            <router-link to="/products" class="border-2 border-white text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-white hover:text-green-600 transition">
              Browse Products
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Features Section / About -->
    <div id="about" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
      <div class="text-center mb-16">
        <h2 class="text-4xl font-bold text-gray-900 mb-4">Why Choose IAM?</h2>
        <p class="text-xl text-gray-600">Subscription-based marketplace designed for agricultural trade</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition">
          <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-6">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-gray-900 mb-4">Subscription-Based</h3>
          <p class="text-gray-600">Simple weekly or monthly plans. No transaction fees. Predictable costs for your business.</p>
        </div>

        <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition">
          <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-6">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.312 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-gray-900 mb-4">Verified Sellers</h3>
          <p class="text-gray-600">All sellers go through verification. Trade with confidence and trust.</p>
        </div>

        <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition">
          <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-6">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-gray-900 mb-4">Direct Communication</h3>
          <p class="text-gray-600">Message sellers directly. Submit RFQs. Negotiate deals on your terms.</p>
        </div>
      </div>
    </div>

    <!-- Categories Section -->
    <div class="bg-gray-100 py-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-gray-900 mb-4">Product Categories</h2>
          <p class="text-xl text-gray-600">Browse agricultural products by category</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
          <div class="bg-white p-6 rounded-lg text-center hover:shadow-lg transition cursor-pointer">
            <div class="text-4xl mb-3">🌾</div>
            <h4 class="font-semibold text-gray-900">Grains & Cereals</h4>
          </div>
          <div class="bg-white p-6 rounded-lg text-center hover:shadow-lg transition cursor-pointer">
            <div class="text-4xl mb-3">🍞</div>
            <h4 class="font-semibold text-gray-900">Flour</h4>
          </div>
          <div class="bg-white p-6 rounded-lg text-center hover:shadow-lg transition cursor-pointer">
            <div class="text-4xl mb-3">🥬</div>
            <h4 class="font-semibold text-gray-900">Vegetables</h4>
          </div>
          <div class="bg-white p-6 rounded-lg text-center hover:shadow-lg transition cursor-pointer">
            <div class="text-4xl mb-3">🐄</div>
            <h4 class="font-semibold text-gray-900">Livestock</h4>
          </div>
          <div class="bg-white p-6 rounded-lg text-center hover:shadow-lg transition cursor-pointer">
            <div class="text-4xl mb-3">🌸</div>
            <h4 class="font-semibold text-gray-900">Flowers</h4>
          </div>
        </div>
      </div>
    </div>

    <!-- Contact Section -->
    <div id="contact" class="bg-white py-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-gray-900 mb-4">Contact Us</h2>
          <p class="text-xl text-gray-600">Get in touch with our support team</p>
        </div>
        <div class="max-w-xl mx-auto">
          <form class="flex flex-col gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700">Name</label>
              <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 border p-2">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Email</label>
              <input type="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 border p-2">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Message</label>
              <textarea rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 border p-2"></textarea>
            </div>
            <button type="button" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Send Message</button>
          </form>
        </div>
      </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-green-600 text-white py-20">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl font-bold mb-6">Ready to Start Trading?</h2>
        <p class="text-xl mb-8 text-green-100">Join thousands of farmers, traders, and buyers on our platform</p>
        <router-link to="/register" class="inline-block bg-white text-green-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-green-50 transition">
          Create Your Account
        </router-link>
      </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div>
            <h3 class="text-xl font-bold mb-4">IAM</h3>
            <p class="text-gray-400 text-sm">International Agro Marketplace - Connecting farmers, traders, and buyers worldwide.</p>
          </div>
          <div>
            <h4 class="font-semibold mb-4">For Sellers</h4>
            <ul class="space-y-2 text-sm text-gray-400">
              <li><a href="#" class="hover:text-white">List Products</a></li>
              <li><a href="#" class="hover:text-white">Pricing</a></li>
              <li><a href="#" class="hover:text-white">Seller Guide</a></li>
            </ul>
          </div>
          <div>
            <h4 class="font-semibold mb-4">For Buyers</h4>
            <ul class="space-y-2 text-sm text-gray-400">
              <li><a href="#" class="hover:text-white">Browse Products</a></li>
              <li><a href="#" class="hover:text-white">Submit RFQ</a></li>
              <li><a href="#" class="hover:text-white">Buyer Guide</a></li>
            </ul>
          </div>
          <div>
            <h4 class="font-semibold mb-4">Company</h4>
            <ul class="space-y-2 text-sm text-gray-400">
              <li><a href="#" class="hover:text-white">About Us</a></li>
              <li><a href="#" class="hover:text-white">Contact</a></li>
              <li><a href="#" class="hover:text-white">Terms of Service</a></li>
            </ul>
          </div>
        </div>
        <div class="border-t border-gray-700 mt-8 pt-8 text-center text-sm text-gray-400">
          <p>&copy; 2026 International Agro Marketplace. All rights reserved.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();

const handleLogout = async () => {
  await authStore.logout();
  router.push('/');
};
</script>
