<template>
    <footer class="bg-white border-t border-gray-100 py-8 px-6 mt-auto">
        <div class="iam-authenticated-layout">
            <div
                class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <!-- Brand Info -->
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-lg bg-agri-forest/5 flex items-center justify-center">
                        <svg class="w-4 h-4 text-agri-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <span class="text-sm font-black text-gray-900 tracking-tight">INTERNATIONAL AGRO MARKETPLACE
                        (IAM)</span>
                </div>

                <!-- Copyright & Links -->
                <div class="flex flex-col md:flex-row items-center md:space-x-8 space-y-2 md:space-y-0">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        &copy; {{ new Date().getFullYear() }} IAM REGISTRY. ALL RIGHTS RESERVED.
                    </p>
                    <div class="flex items-center space-x-6">
                        <a href="#"
                            class="text-[10px] font-black text-gray-400 hover:text-agri-forest uppercase tracking-widest transition-colors">Security
                            Manual</a>
                        <a href="#"
                            class="text-[10px] font-black text-gray-400 hover:text-agri-forest uppercase tracking-widest transition-colors">Trade
                            Compliance</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>

<script setup lang="js">
// Dedicated footer for the authenticated area
</script>

<style scoped>
footer {
    font-family: 'Inter', sans-serif;
}
</style>
