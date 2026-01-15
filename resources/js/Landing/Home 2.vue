<template>
  <Navigation />

  <!-- Hero -->
  <section class="relative overflow-hidden">
    <div class="bg-hero-pattern absolute inset-0 opacity-30" aria-hidden="true"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
      <div class="grid md:grid-cols-2 gap-8 items-center">
        <div>
          <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight">Connect verified agricultural sellers with institutional buyers</h1>
          <p class="mt-6 text-lg text-gray-700 max-w-xl">INTERNATIONAL AGRO MARKETPLACE (IAM) is a trusted procurement platform for bulk agricultural trade — KYC-verified suppliers, negotiated pricing, and end-to-end order management for institutions and large buyers.</p>

          <div class="mt-8 flex flex-col sm:flex-row gap-3">
            <router-link to="/register" class="inline-flex items-center justify-center rounded-md bg-emerald-600 px-6 py-3 text-white text-base font-semibold hover:bg-emerald-700">Get Started</router-link>
            <a href="#how-it-works" class="inline-flex items-center justify-center rounded-md border border-gray-300 px-6 py-3 text-base font-medium text-gray-700 hover:border-emerald-600 hover:text-emerald-600">How it works</a>
          </div>

          <div class="mt-10 grid grid-cols-2 gap-4 max-w-md">
            <div>
              <div class="text-sm text-gray-500">Verified suppliers</div>
              <div class="text-xl font-semibold text-gray-900">KYC & certifications</div>
            </div>
            <div>
              <div class="text-sm text-gray-500">Bulk volumes</div>
              <div class="text-xl font-semibold text-gray-900">Negotiated pricing</div>
            </div>
          </div>
        </div>

        <div class="hero-illustration rounded-2xl p-8">
          <svg width="100%" height="320" viewBox="0 0 600 320" fill="none" xmlns="http://www.w3.org/2000/svg" class="mx-auto">
            <rect width="600" height="320" rx="24" fill="url(#g)" />
            <defs>
              <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0" stop-color="#ECFDF5"/>
                <stop offset="1" stop-color="#D1FAE5"/>
              </linearGradient>
            </defs>
          </svg>
        </div>
      </div>
    </div>
  </section>

  <!-- Marketplace preview -->
  <section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="text-2xl font-bold text-gray-900">Marketplace</h2>
      <p class="mt-2 text-gray-600">Browse categories and bulk offers from verified sellers.</p>

      <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="border rounded-lg p-6 hover:shadow"> 
          <h3 class="font-semibold text-gray-900">Grains & Cereals</h3>
          <p class="text-sm text-gray-600 mt-2">Maize, Rice, Wheat — bulk lots from verified cooperatives.</p>
        </div>
        <div class="border rounded-lg p-6 hover:shadow"> 
          <h3 class="font-semibold text-gray-900">Oilseeds</h3>
          <p class="text-sm text-gray-600 mt-2">Soybeans, groundnuts, and more.</p>
        </div>
        <div class="border rounded-lg p-6 hover:shadow"> 
          <h3 class="font-semibold text-gray-900">Specialty Commodities</h3>
          <p class="text-sm text-gray-600 mt-2">Cocoa beans, coffee, and horticulture lots.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- How it works -->
  <section id="how-it-works" class="py-16 bg-emerald-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="text-2xl font-bold text-gray-900">How it works</h2>
      <div class="mt-8 grid md:grid-cols-3 gap-6">
        <div class="p-6 bg-white rounded-lg">
          <div class="text-emerald-600 font-bold">1</div>
          <h3 class="mt-4 font-semibold">Register & Verify</h3>
          <p class="mt-2 text-sm text-gray-600">Create an account and complete KYC to join the marketplace.</p>
        </div>
        <div class="p-6 bg-white rounded-lg">
          <div class="text-emerald-600 font-bold">2</div>
          <h3 class="mt-4 font-semibold">Post / Request Bulk Offers</h3>
          <p class="mt-2 text-sm text-gray-600">Sellers list bulk lots; buyers submit RFQs and receive quotations.</p>
        </div>
        <div class="p-6 bg-white rounded-lg">
          <div class="text-emerald-600 font-bold">3</div>
          <h3 class="mt-4 font-semibold">Negotiate & Contract</h3>
          <p class="mt-2 text-sm text-gray-600">Negotiate terms, confirm orders and manage fulfillment on-platform.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="py-16">
    <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
      <h2 class="text-3xl font-bold text-gray-900">Ready to source or sell in bulk?</h2>
      <p class="mt-4 text-gray-600">Join institutions and verified suppliers using IAM to trade at scale.</p>
      <div class="mt-8">
        <router-link to="/register" class="inline-flex items-center justify-center rounded-md bg-emerald-600 px-8 py-3 text-white text-lg font-semibold hover:bg-emerald-700">Get Started</router-link>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-gray-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid md:grid-cols-3 gap-8">
        <div>
          <h3 class="text-xl font-bold mb-4">INTERNATIONAL AGRO MARKETPLACE (IAM)</h3>
          <p class="text-gray-400">Trusted procurement platform for institutional agricultural trade.</p>
        </div>
        <div>
          <h4 class="font-semibold mb-4">Platform</h4>
          <ul class="space-y-2 text-gray-400">
            <li><router-link to="/features" class="hover:text-white">Features</router-link></li>
            <li><router-link to="/pricing" class="hover:text-white">Pricing</router-link></li>
            <li><router-link to="/about" class="hover:text-white">About</router-link></li>
          </ul>
        </div>
        <div>
          <h4 class="font-semibold mb-4">Support</h4>
          <ul class="space-y-2 text-gray-400">
            <li><router-link to="/contact" class="hover:text-white">Contact</router-link></li>
            <li><a href="#" class="hover:text-white">Help Center</a></li>
            <li><a href="#" class="hover:text-white">Documentation</a></li>
          </ul>
        </div>
      </div>
      <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
        <p>&copy; 2026 INTERNATIONAL AGRO MARKETPLACE (IAM). All rights reserved.</p>
      </div>
    </div>
  </footer>
</template>

<script setup>
import Navigation from '../components/Navigation.vue'
</script>
