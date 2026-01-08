<template>
  <footer class="bg-gray-950 text-white py-24 border-t border-white/5 relative overflow-hidden">
    <!-- Decorative background element -->
    <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-[#1A4D2E]/20 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute -top-24 -right-24 w-96 h-96 bg-[#D4A373]/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="container mx-auto px-6 relative z-10">
      <div class="grid md:grid-cols-12 gap-16 mb-20">
        <!-- Brand Narrative Column -->
        <div class="md:col-span-4">
          <router-link to="/" class="flex items-center space-x-3 mb-8 group">
            <div class="w-10 h-10 rounded-xl bg-[#1A4D2E] flex items-center justify-center text-[#D4A373] shadow-lg group-hover:scale-105 transition-transform duration-300">
                <svg class="w-6 h-6 border-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <span class="text-xl font-black tracking-tighter uppercase leading-none">AgriXchange <br><span class="text-[#D4A373] text-[10px] tracking-[0.3em] font-bold">Global Trade Hub</span></span>
          </router-link>
          <p class="text-gray-500 font-medium leading-relaxed mb-10 text-sm max-w-sm italic">
            "Empowering the world's agricultural producers through a high-integrity, transparency-driven digital trade protocol."
          </p>
          <div class="flex space-x-4">
            <a href="#" class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center hover:bg-[#1A4D2E] hover:text-white transition-all duration-300 border border-white/5 group">
              <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
            </a>
            <a href="#" class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center hover:bg-[#1A4D2E] hover:text-white transition-all duration-300 border border-white/5 group">
              <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
            </a>
          </div>
        </div>

        <!-- Links Grid -->
        <div class="md:col-span-8">
          <div class="grid grid-cols-2 md:grid-cols-4 gap-12">
            <div>
              <h4 class="font-black text-[#D4A373] uppercase tracking-[0.2em] text-[10px] mb-8">Supply Chain</h4>
              <ul class="space-y-4 text-gray-500 font-bold text-xs uppercase tracking-widest">
                <li><router-link to="/marketplace" class="hover:text-white transition-colors">Global Inventory</router-link></li>
                <li><router-link to="/marketplace" class="hover:text-white transition-colors">Trade Discovery</router-link></li>
                <li><a href="#" class="hover:text-white transition-colors">Logostics Hub</a></li>
                <li><a href="#" class="hover:text-white transition-colors">Origin Ledger</a></li>
              </ul>
            </div>
            <div>
              <h4 class="font-black text-[#D4A373] uppercase tracking-[0.2em] text-[10px] mb-8">Governance</h4>
              <ul class="space-y-4 text-gray-500 font-bold text-xs uppercase tracking-widest">
                <li><a href="#" class="hover:text-white transition-colors">Verification Protocol</a></li>
                <li><a href="#" class="hover:text-white transition-colors">Smart Escrow</a></li>
                <li><a href="#" class="hover:text-white transition-colors">Arbitration</a></li>
                <li><a href="#" class="hover:text-white transition-colors">Policy Center</a></li>
              </ul>
            </div>
            <div>
              <h4 class="font-black text-[#D4A373] uppercase tracking-[0.2em] text-[10px] mb-8">Membership</h4>
              <ul class="space-y-4 text-gray-500 font-bold text-xs uppercase tracking-widest">
                <li><router-link to="/subscriptions" class="hover:text-white transition-colors">Tiered Status</router-link></li>
                <li><a href="#" class="hover:text-white transition-colors">Corporate Accounts</a></li>
                <li><a href="#" class="hover:text-white transition-colors">Developer API</a></li>
                <li><a href="#" class="hover:text-white transition-colors">Market Insights</a></li>
              </ul>
            </div>
            <div>
              <h4 class="font-black text-[#D4A373] uppercase tracking-[0.2em] text-[10px] mb-8">Contact</h4>
              <p class="text-gray-500 font-bold text-xs uppercase tracking-widest mb-4">Trade HQ, Floor 24</p>
              <p class="text-gray-500 font-bold text-xs uppercase tracking-widest mb-8 leading-loose">Business Bay, DXB<br>United Arab Emirates</p>
              <a href="mailto:trade@agrixchange.com" class="text-white font-black text-xs underline underline-offset-8 hover:text-[#D4A373] transition-colors">trade@agrixchange.com</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer Bottom Bar -->
      <div class="border-t border-white/5 pt-12 flex flex-col md:flex-row justify-between items-center gap-8">
        <div class="flex flex-col md:flex-row items-center gap-8">
            <p class="text-[10px] font-black text-gray-700 uppercase tracking-[0.3em]">
              &copy; 2026 AgriXchange Systems. Managed by International Agro Registry.
            </p>
            <div class="flex items-center space-x-6 text-[10px] font-black text-gray-500 uppercase tracking-widest">
               <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-green-500/40 mr-2 animate-pulse"></span> Network Status: Nominal</span>
               <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-blue-500/40 mr-2"></span> Security Tier: L3 Verified</span>
            </div>
        </div>
        
        <div class="flex space-x-12 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">
           <a href="#" class="hover:text-[#D4A373] transition-colors">Privacy Charter</a>
           <a href="#" class="hover:text-[#D4A373] transition-colors">Trade Integrity Policy</a>
           <a href="#" class="hover:text-[#D4A373] transition-colors">Legal Disclosure</a>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
// Enterprise Grade Footer Logic (Stateless)
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@100;400;700;900&display=swap');

footer {
  font-family: 'Outfit', sans-serif;
}

.tracking-tighter {
  letter-spacing: -2px;
}
</style>
