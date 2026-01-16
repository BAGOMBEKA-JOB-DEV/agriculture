<template>
    <div class="min-h-screen bg-gray-50/50 pb-24">
        <Notification ref="notificationRef" />

        <!-- Simple Enterprise Header -->
        <div class="bg-agri-forest pt-16 pb-32 relative overflow-hidden rounded-b-[2rem] shadow-xl">
            <div class="container mx-auto px-8 relative z-10">
                <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                    <div>
                        <h1 class="text-4xl font-extrabold text-white tracking-tight mb-4 uppercase">
                           {{ pageTitle }}
                        </h1>
                        <p class="text-white/70 font-medium max-w-xl text-lg">
                            Find and connect with verified partners in our global agricultural network.
                        </p>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Search -->
                        <div class="relative group w-full lg:w-96">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-agri-earth/40 group-focus-within:text-agri-earth transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input 
                                v-model="filters.search"
                                @input="debounceFetch"
                                type="text" 
                                placeholder="Search Identity or ID..." 
                                class="w-full h-14 pl-12 pr-6 rounded-[2rem] bg-white/10 backdrop-blur-md border border-white/20 text-white font-bold placeholder-agri-earth/50 focus:outline-none focus:ring-2 focus:ring-agri-earth/50 transition-all shadow-xl outline-none"
                            >
                        </div>

                        <!-- Advanced Filter Toggle -->
                        <button 
                            @click="showAdvancedFilters = !showAdvancedFilters"
                            class="h-14 px-6 rounded-[1.5rem] border transition-all duration-300 flex items-center space-x-3 shadow-xl backdrop-blur-md"
                            :class="showAdvancedFilters ? 'bg-agri-earth text-agri-forest border-agri-earth' : 'bg-white/5 text-white border-white/10 hover:bg-white/10'"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                            <span class="text-[10px] font-black uppercase tracking-widest hidden sm:inline">Filters</span>
                        </button>
                    </div>
                </div>

                <!-- Advanced Filter Panel -->
                <Transition
                    enter-active-class="transition-all duration-500 ease-out"
                    leave-active-class="transition-all duration-300 ease-in"
                    enter-from-class="max-h-0 opacity-0 mt-0"
                    enter-to-class="max-h-[500px] opacity-100 mt-8"
                    leave-from-class="max-h-[500px] opacity-100 mt-8"
                    leave-to-class="max-h-0 opacity-0 mt-0"
                >
                    <div v-if="showAdvancedFilters" class="grid grid-cols-1 md:grid-cols-3 gap-4 overflow-hidden">
                        <!-- Sovereign Origin (Location) -->
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-agri-earth/60 uppercase tracking-widest ml-4">Sovereign Origin</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-agri-earth/40 group-focus-within:text-agri-earth transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                </div>
                                <input 
                                    v-model="filters.location"
                                    @input="debounceFetch"
                                    type="text" 
                                    placeholder="Filter by Location..."
                                    class="w-full h-14 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl pl-12 pr-6 text-white font-bold text-xs outline-none focus:ring-2 focus:ring-agri-earth/30 transition-all placeholder-agri-earth/40"
                                >
                            </div>
                        </div>

                        <!-- Identity Status -->
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-agri-earth/60 uppercase tracking-widest ml-4">Identity Status</label>
                            <select v-model="filters.status" @change="fetchUsers" class="w-full h-14 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl px-6 text-white font-bold text-xs outline-none cursor-pointer focus:ring-2 focus:ring-agri-earth/30 transition-all">
                                <option value="" class="text-gray-900">All Identity Statuses</option>
                                <option value="active" class="text-gray-900">Active Trade</option>
                                <option value="inactive" class="text-gray-900">Dormant Identity</option>
                            </select>
                        </div>

                        <!-- Verification Level -->
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-agri-earth/60 uppercase tracking-widest ml-4">Verification Registry</label>
                            <div class="flex items-center space-x-3">
                                <select v-model="filters.verified" @change="fetchUsers" class="flex-1 h-14 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl px-6 text-white font-bold text-xs outline-none cursor-pointer focus:ring-2 focus:ring-agri-earth/30 transition-all">
                                    <option value="" class="text-gray-900">All Trust Levels</option>
                                    <option value="yes" class="text-gray-900">Verified Protocol</option>
                                    <option value="no" class="text-gray-900">Pending Verification</option>
                                </select>
                                <button 
                                    @click="resetFilters"
                                    class="h-14 px-6 rounded-2xl text-[9px] font-black text-agri-earth hover:text-white uppercase tracking-widest transition-colors"
                                >
                                    Clear All
                                </button>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </div>

        <!-- Directory Grid -->
        <div class="container mx-auto px-8 -mt-10 relative z-20">
            <!-- Loading Skeletons -->
            <div v-if="loading && users.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12 justify-items-center opacity-60 pointer-events-none transition-opacity duration-300">
                <NetworkCard 
                    v-for="user in users" 
                    :key="user.id" 
                    :user="user"
                />
            </div>

            <div v-else-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12 justify-items-center">
                <div v-for="i in 6" :key="i" class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm w-[350px] flex flex-col h-full animate-pulse">
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 rounded-2xl bg-gray-50"></div>
                            <div class="space-y-3">
                                <div class="h-5 bg-gray-50 rounded-lg w-32"></div>
                                <div class="h-3 bg-gray-50 rounded-lg w-20"></div>
                            </div>
                        </div>
                        <div class="w-16 h-6 bg-gray-50 rounded-lg"></div>
                    </div>
                    <div class="flex-1 space-y-3 mb-6">
                        <div class="h-4 bg-gray-50 rounded-lg w-full"></div>
                        <div class="h-4 bg-gray-50 rounded-lg w-4/5"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 pt-6 border-t border-gray-50">
                        <div class="h-12 bg-gray-50 rounded-xl"></div>
                        <div class="h-12 bg-gray-50 rounded-xl"></div>
                    </div>
                </div>
            </div>

            <div v-else-if="users.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12 justify-items-center">
                <NetworkCard 
                    v-for="user in users" 
                    :key="user.id" 
                    :user="user"
                    @message="handleMessage"
                    @view="handleViewProfile"
                />
            </div>

            <div v-else class="text-center py-24 bg-white rounded-[3rem] shadow-xl border border-gray-100 px-8 max-w-4xl mx-auto">
                <div class="w-20 h-20 bg-gray-50 rounded-[2rem] flex items-center justify-center mx-auto mb-8 text-gray-200">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <h3 class="text-2xl font-black text-gray-900 mb-3 uppercase tracking-tight">No Participants Identified</h3>
                <p class="text-gray-400 font-medium text-lg">We couldn't locate any identities matching your current criteria in the registry.</p>
                <button @click="resetFilters" class="mt-8 px-8 py-4 bg-agri-forest text-white rounded-2xl font-black uppercase text-xs tracking-widest hover:bg-agri-lush transition-all">Reset All Filters</button>
            </div>
            
            <!-- Pagination Controls -->
             <div v-if="pagination.total > pagination.per_page" class="mt-12 flex justify-center">
                <div class="bg-white px-3 py-2 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4">
                    <button 
                        @click="changePage(pagination.current_page - 1)"
                        :disabled="pagination.current_page === 1"
                        class="w-10 h-10 rounded-xl hover:bg-gray-50 text-gray-600 disabled:opacity-30 transition-all flex items-center justify-center border border-gray-100"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <span class="text-xs font-bold text-gray-500">Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
                    <button 
                        @click="changePage(pagination.current_page + 1)"
                        :disabled="pagination.current_page === pagination.last_page"
                        class="w-10 h-10 rounded-xl hover:bg-gray-50 text-gray-600 disabled:opacity-30 transition-all flex items-center justify-center border border-gray-100"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- User Details Drawer -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition-opacity duration-300 ease-out"
                leave-active-class="transition-opacity duration-300 ease-in"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="isDrawerOpen" class="fixed inset-0 z-[100] overflow-hidden">
                    <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm" @click="closeDrawer"></div>
                    
                    <Transition
                        enter-active-class="transform transition-transform duration-400 ease-out"
                        leave-active-class="transform transition-transform duration-300 ease-in"
                        enter-from-class="translate-x-full"
                        enter-to-class="translate-x-0"
                        leave-from-class="translate-x-0"
                        leave-to-class="translate-x-full"
                        appear
                    >
                        <div v-if="isDrawerOpen" class="absolute inset-y-0 right-0 max-w-md w-full bg-white shadow-2xl flex flex-col">
                            <!-- Drawer Header -->
                            <div class="px-8 py-8 border-b border-gray-100">
                                <div class="flex items-center justify-between mb-8">
                                    <div>
                                        <h2 class="text-xl font-bold text-gray-900 leading-none mb-2">{{ selectedUser?.name }}</h2>
                                        <div class="flex items-center space-x-2">
                                            <span class="px-2 py-0.5 rounded-lg bg-agri-forest/10 text-agri-forest text-[10px] font-bold uppercase tracking-wider">{{ selectedUser?.roles?.[0]?.name }}</span>
                                        </div>
                                    </div>
                                    <button @click="closeDrawer" class="w-10 h-10 bg-gray-50 rounded-xl text-gray-400 hover:text-gray-900 transition-colors flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>

                                <!-- Navigation Tabs -->
                                <div class="flex space-x-1 p-1 bg-gray-50 rounded-xl">
                                    <button 
                                        @click="activeIntelTab = 'intel'"
                                        class="flex-1 py-2 text-[11px] font-bold rounded-lg transition-all"
                                        :class="activeIntelTab === 'intel' ? 'bg-white text-agri-forest shadow-sm' : 'text-gray-400'"
                                    >
                                        Profile
                                    </button>
                                    <button 
                                        @click="activeIntelTab = 'connect'"
                                        class="flex-1 py-2 text-[11px] font-bold rounded-lg transition-all"
                                        :class="activeIntelTab === 'connect' ? 'bg-white text-agri-forest shadow-sm' : 'text-gray-400'"
                                    >
                                        Chat
                                    </button>
                                </div>
                            </div>

                            <!-- Drawer Content -->
                            <div class="flex-1 overflow-y-auto px-8 py-8 bg-white custom-scrollbar">
                                <!-- Profile Info -->
                                <div v-if="activeIntelTab === 'intel'" class="space-y-8 animate-in fade-in slide-in-from-right-4 duration-300">
                                    <!-- Refined User Profile Summary (Enlarged Avatar) -->
                                    <div class="py-6 text-center">
                                        <div 
                                            class="w-48 h-48 rounded-[2.5rem] bg-gray-50 border-8 border-white shadow-2xl flex items-center justify-center text-agri-forest font-bold text-6xl overflow-hidden cursor-zoom-in mx-auto group relative transition-transform hover:scale-105"
                                            @click="selectedUser?.avatar_url ? openLightbox(0, [selectedUser.avatar_url], selectedUser.name, 'User Identity') : null"
                                        >
                                            <img v-if="selectedUser?.avatar_url" :src="selectedUser.avatar_url" class="w-full h-full object-cover">
                                            <span v-else>{{ selectedUser?.name?.charAt(0) }}</span>
                                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-all"></div>
                                        </div>
                                    </div>

                                    <div>
                                        <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">Contact Details</h4>
                                        <div class="space-y-4">
                                            <div class="flex items-center p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                                <div class="w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-agri-forest shadow-sm flex-shrink-0">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                </div>
                                                <div class="ml-4">
                                                    <p class="text-[9px] font-bold text-gray-400 uppercase">Location</p>
                                                    <p class="text-sm font-bold text-gray-900">{{ selectedUser?.location || 'Not provided' }}</p>
                                                </div>
                                            </div>

                                            <div class="flex items-center p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                                <div class="w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-agri-forest shadow-sm flex-shrink-0">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                </div>
                                                <div class="ml-4 min-w-0">
                                                    <p class="text-[9px] font-bold text-gray-400 uppercase">Email Address</p>
                                                    <p class="text-sm font-bold text-gray-900 truncate">{{ selectedUser?.email }}</p>
                                                </div>
                                            </div>

                                            <div class="flex items-center p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                                <div class="w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-agri-forest shadow-sm flex-shrink-0">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                </div>
                                                <div class="ml-4">
                                                    <p class="text-[9px] font-bold text-gray-400 uppercase">Phone Number</p>
                                                    <p class="text-sm font-bold text-gray-900">{{ selectedUser?.phone || 'Not provided' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- About -->
                                    <div v-if="selectedUser?.seller_profile">
                                        <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">About</h4>
                                        <div class="p-6 bg-blue-50/50 rounded-2xl border border-blue-100/50">
                                            <p class="text-sm text-gray-700 leading-relaxed">
                                                {{ selectedUser.seller_profile.bio || 'This user has not added a bio yet.' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Chat Actions -->
                                <div v-if="activeIntelTab === 'connect'" class="space-y-6 animate-in fade-in slide-in-from-right-4 duration-300 text-center py-12 px-4">
                                     <div class="w-20 h-20 bg-agri-forest/10 rounded-3xl flex items-center justify-center mx-auto mb-6 text-agri-forest">
                                         <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                     </div>
                                     <h4 class="text-xl font-bold text-gray-900">Start a conversation</h4>
                                     <p class="text-gray-500 text-sm">Send a direct message to begin discussing potential trade opportunities.</p>
                                     <button 
                                        @click="handleMessage(selectedUser)"
                                        class="w-full mt-8 bg-agri-forest text-white py-4 rounded-xl font-bold text-sm tracking-wide hover:bg-agri-lush transition-all shadow-sm shadow-agri-forest/10"
                                     >
                                        Message {{ selectedUser?.name.split(' ')[0] }}
                                     </button>
                                </div>
                            </div>
                        </div>
                    </Transition>
                </div>
            </Transition>
        </Teleport>

        <!-- Cinematic Intelligence Lightbox -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition-opacity duration-300 ease-out"
                leave-active-class="transition-opacity duration-300 ease-in"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showLightbox" class="fixed inset-0 z-[200] bg-black/95 backdrop-blur-xl flex flex-col items-center justify-center p-8 overflow-hidden">
                    <!-- Close Button -->
                    <button 
                        @click="showLightbox = false"
                        class="absolute top-12 right-12 w-16 h-16 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white transition-all group z-[210]"
                    >
                        <svg class="w-8 h-8 group-hover:rotate-90 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    <!-- Main Content -->
                    <div class="w-full max-w-4xl relative group/carousel">
                        <Transition
                            enter-active-class="transition-all duration-500 ease-out"
                            leave-active-class="transition-all duration-300 ease-in"
                            enter-from-class="opacity-0 scale-95"
                            enter-to-class="opacity-100 scale-100"
                            leave-from-class="opacity-100 scale-100"
                            leave-to-class="opacity-0 scale-95"
                            mode="out-in"
                        >
                            <div :key="lightboxIndex" class="aspect-square relative overflow-hidden rounded-[4rem] shadow-[0_0_100px_rgba(0,0,0,0.5)] border border-white/10 bg-white/5">
                                <img :src="lightboxImages[lightboxIndex]" class="w-full h-full object-contain">
                            </div>
                        </Transition>

                        <!-- Navigation Controls -->
                        <div v-if="lightboxImages.length > 1" class="absolute inset-y-0 -inset-x-20 flex items-center justify-between pointer-events-none">
                            <button 
                                @click="prevImage"
                                class="w-20 h-20 bg-white/10 hover:bg-white rounded-full flex items-center justify-center text-white hover:text-agri-forest transition-all shadow-2xl backdrop-blur-md pointer-events-auto"
                            >
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button 
                                @click="nextImage"
                                class="w-20 h-20 bg-white/10 hover:bg-white rounded-full flex items-center justify-center text-white hover:text-agri-forest transition-all shadow-2xl backdrop-blur-md pointer-events-auto"
                            >
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Metadata -->
                    <div class="mt-12 text-center animate-in fade-in slide-in-from-bottom-4 duration-700">
                        <p class="text-[10px] font-black text-agri-sand uppercase tracking-[0.4em] mb-4">{{ lightboxLabel }} {{ lightboxImages.length > 1 ? (lightboxIndex + 1) + ' of ' + lightboxImages.length : '' }}</p>
                        <h3 class="text-3xl font-black text-white tracking-tighter uppercase">{{ lightboxTitle }}</h3>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- Cinematic Discovery Overlay -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition-opacity duration-300 ease-out"
                leave-active-class="transition-opacity duration-500 ease-in"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="loading && users.length === 0" class="fixed inset-0 z-[150] bg-white/60 backdrop-blur-xl flex flex-col items-center justify-center">
                    <div class="relative">
                        <div class="w-24 h-24 border-4 border-agri-forest/20 rounded-full animate-ping absolute inset-0"></div>
                        <div class="w-24 h-24 border-t-4 border-agri-forest rounded-full animate-spin relative z-10"></div>
                    </div>
                    <div class="mt-12 text-center">
                        <p class="text-[10px] font-black text-agri-forest uppercase tracking-[0.4em] mb-4">Establishing Protocol</p>
                        <h3 class="text-2xl font-black text-gray-900 tracking-tighter uppercase">Scouting Global Ecosystem...</h3>
                    </div>
                </div>
            </Transition>
        </Teleport>
     </div>
 </template>

<script setup>
import { ref, watch, computed } from 'vue'
import { useRouter } from 'vue-router'
import { usePermissions } from '../../composables/usePermissions'
import axios from 'axios'
import NetworkCard from '../../components/NetworkCard.vue'
import Notification from '../../components/Notification.vue'

const router = useRouter()
const { can, ensure } = usePermissions()
const notificationRef = ref(null)

const users = ref([])
const loading = ref(true)
const pagination = ref({})
let fetchTimeout = null
const props = defineProps(['type'])
const currentType = computed(() => props.type)

// Advanced Filters
const showAdvancedFilters = ref(false)
const filters = ref({
    search: '',
    location: '',
    status: '',
    verified: ''
})

// Drawer State
const isDrawerOpen = ref(false)
const selectedUser = ref(null)
const activeIntelTab = ref('intel')

// Lightbox State
const showLightbox = ref(false)
const lightboxIndex = ref(0)
const lightboxImages = ref([])
const lightboxTitle = ref('')
const lightboxLabel = ref('')
const fetchUsers = async (page = 1) => {
    loading.value = true
    try {
        const response = await axios.get(`/api/network/${currentType.value}`, {
            params: {
                page,
                search: filters.value.search,
                location: filters.value.location,
                status: filters.value.status,
                verified: filters.value.verified
            }
        })
        
        users.value = response.data.data || []
        pagination.value = response.data
        
    } catch (error) {
        console.error('Failed to fetch users:', error)
        users.value = []
        if (notificationRef.value) {
             const msg = error.response?.data?.message || 'Failed to load directory.'
             notificationRef.value.addNotification('error', 'Network Error', msg)
        }
    } finally {
        loading.value = false
    }
}

const debounceFetch = () => {
    if (fetchTimeout) clearTimeout(fetchTimeout)
    fetchTimeout = setTimeout(() => {
        fetchUsers()
    }, 400)
}

const resetFilters = () => {
    filters.value = {
        search: '',
        location: '',
        status: '',
        verified: ''
    }
    fetchUsers()
}

const changePage = (page) => {
    fetchUsers(page)
    window.scrollTo({ top: 0, behavior: 'smooth' })
}

const handleMessage = (user) => {
    if (!ensure('access_conversations')) return;

    router.push({
        path: '/messages',
        query: { userId: user.id }
    })
}

const handleViewProfile = (user) => {
    selectedUser.value = user
    isDrawerOpen.value = true
    activeIntelTab.value = 'intel'
}

const closeDrawer = () => {
    isDrawerOpen.value = false
}

const openLightbox = (index, images = null, title = null, label = null) => {
    lightboxImages.value = images || (selectedUser.value?.avatar_url ? [selectedUser.value.avatar_url] : [])
    lightboxIndex.value = index
    lightboxTitle.value = title || selectedUser.value?.name || 'Identity Verification'
    lightboxLabel.value = label || 'User Identity'
    showLightbox.value = true
}

const nextImage = () => {
  if (lightboxImages.value.length > 0) {
    lightboxIndex.value = (lightboxIndex.value + 1) % lightboxImages.value.length;
  }
};

const prevImage = () => {
  if (lightboxImages.value.length > 0) {
    const len = lightboxImages.value.length;
    lightboxIndex.value = (lightboxIndex.value - 1 + len) % len;
  }
};

const pageTitle = computed(() => {
    switch(currentType.value) {
        case 'vendors': return 'Verified Vendors'
        case 'traders': return 'Active Traders'
        case 'buyers': return 'Global Buyers'
        default: return 'Global Network'
    }
})

// Fetch users when type or filters change
watch([currentType, filters], () => {
    if (currentType.value) {
        fetchUsers()
    }
}, { immediate: true, deep: true })
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    height: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
}
</style>
