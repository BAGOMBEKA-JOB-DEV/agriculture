<template>
    <div class="min-h-screen bg-gray-50/50 pb-24">
        <Notification ref="notificationRef" />

        <!-- Governance Header: Sovereign Dark Branding -->
        <div class="bg-agri-forest pt-20 pb-32 relative overflow-hidden rounded-b-[3rem] shadow-2xl">
            <!-- Abstract Branding Accents -->
            <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -mr-32 -mt-32 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-agri-lush/10 rounded-full -ml-32 -mb-32 blur-2xl"></div>
            
            <div class="container mx-auto px-8 relative z-10">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-12 mb-16">
                    <div>
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center border border-white/20 shadow-inner">
                                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            </div>
                            <span class="text-[11px] font-black text-white/40 uppercase tracking-[0.4em]">Nexus Governance Authority</span>
                        </div>
                        <h1 class="text-5xl font-black text-white tracking-tighter leading-none mb-4">
                           Role <span class="text-agri-lush">Ecosystem</span>
                        </h1>
                        <p class="text-white/60 font-medium max-w-2xl text-xl leading-relaxed">Orchestrate granular system privileges and define sovereign access protocols.</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button 
                            @click="openModal()"
                            class="group relative px-8 py-4 bg-white text-agri-forest rounded-2xl font-black tracking-wide hover:bg-agri-lush hover:text-white transition-all duration-300 shadow-[0_0_30px_rgba(255,255,255,0.2)] hover:shadow-[0_0_40px_rgba(132,204,22,0.4)] overflow-hidden"
                        >
                            <span class="relative z-10 flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
                                <span>INITIALIZE ROLE</span>
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Telemetry Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                     <!-- Total Roles -->
                    <div class="bg-white/10 backdrop-blur-2xl p-8 rounded-[2rem] border border-white/10 relative overflow-hidden group hover:bg-white/15 transition-all duration-500">
                        <div class="absolute -right-12 -top-12 w-32 h-32 bg-agri-lush/20 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-700"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-8">
                                <div class="p-3 rounded-xl bg-agri-lush/20 text-agri-lush border border-white/10">
                                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                </div>
                                <span class="bg-white/10 px-2 py-1 rounded-lg text-[10px] font-bold text-white/50 uppercase tracking-wider">Active</span>
                            </div>
                            <h4 class="text-[10px] font-black text-white/40 uppercase tracking-[0.3em] mb-1">Total Roles</h4>
                            <p class="text-4xl font-black text-white tracking-tight">{{ roles.length }}</p>
                        </div>
                    </div>

                    <!-- Total Permissions -->
                    <div class="bg-white/10 backdrop-blur-2xl p-8 rounded-[2rem] border border-white/10 relative overflow-hidden group hover:bg-white/15 transition-all duration-500">
                        <div class="absolute -right-12 -top-12 w-32 h-32 bg-blue-500/20 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-700"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-8">
                                <div class="p-3 rounded-xl bg-blue-500/20 border border-white/10">
                                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                                </div>
                                <span class="bg-white/10 px-2 py-1 rounded-lg text-[10px] font-bold text-white/50 uppercase tracking-wider">Secure</span>
                            </div>
                            <h4 class="text-[10px] font-black text-white/40 uppercase tracking-[0.3em] mb-1">Permissions</h4>
                            <p class="text-4xl font-black text-white tracking-tight">{{ permissions.length }}</p>
                        </div>
                    </div>

                    <!-- Custom Definitions -->
                    <div class="bg-white/10 backdrop-blur-2xl p-8 rounded-[2rem] border border-white/10 relative overflow-hidden group hover:bg-white/15 transition-all duration-500">
                        <div class="absolute -right-12 -top-12 w-32 h-32 bg-purple-500/20 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-700"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-8">
                                <div class="p-3 rounded-xl bg-purple-500/20 border border-white/10">
                                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </div>
                                <span class="bg-white/10 px-2 py-1 rounded-lg text-[10px] font-bold text-white/50 uppercase tracking-wider">Custom</span>
                            </div>
                            <h4 class="text-[10px] font-black text-white/40 uppercase tracking-[0.3em] mb-1">Custom Defined</h4>
                            <p class="text-4xl font-black text-white tracking-tight">{{ customRolesCount }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Registry Grid -->
        <div class="container mx-auto px-8 -mt-20 relative z-20">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div v-for="role in roles" :key="role.id" class="group bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-xl hover:shadow-[0_20px_50px_rgba(0,0,0,0.1)] transition-all duration-500 relative overflow-hidden">
                    <!-- Card Glow Effect -->
                    <div class="absolute inset-0 bg-gradient-to-br from-gray-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-start justify-between mb-8">
                            <div>
                                <h3 class="text-2xl font-black text-gray-900 group-hover:text-agri-forest transition-colors duration-300">{{ role.name }}</h3>
                                <p class="text-xs font-bold text-gray-400 font-mono mt-1 bg-gray-50 inline-block px-2 py-1 rounded-lg border border-gray-100">{{ role.slug }}</p>
                            </div>
                            <div class="flex space-x-1">
                                <button 
                                    @click="openDrawer(role)" 
                                    class="p-3 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-2xl transition-all"
                                    title="View Details"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                <button 
                                    @click="openModal(role)" 
                                    class="p-3 text-gray-400 hover:text-agri-forest hover:bg-agri-forest/5 rounded-2xl transition-all"
                                    title="Access Configuration"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>
                                <button 
                                    v-if="!isSystemRole(role.slug)"
                                    @click="handleDelete(role)" 
                                    class="p-3 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-2xl transition-all"
                                    title="Decommission Role"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </div>
                        
                        <p v-if="role.description" class="text-sm font-medium text-gray-500 mb-8 leading-relaxed">
                            {{ role.description }}
                        </p>
                        <p v-else class="text-sm italic text-gray-400 mb-8">
                            No functional description provided.
                        </p>

                        <div class="border-t border-gray-100 pt-6">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Access Matrix</span>
                                <span class="text-xs font-bold text-agri-forest bg-agri-forest/10 px-2 py-1 rounded-lg">{{ role.permissions.length }} Nodes</span>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <span 
                                    v-for="permission in role.permissions.slice(0, 3)" 
                                    :key="permission.id"
                                    class="px-2.5 py-1 text-[10px] font-bold rounded-lg bg-gray-50 text-gray-600 border border-gray-100 uppercase tracking-wide"
                                >
                                    {{ permission.name }}
                                </span>
                                <span 
                                    v-if="role.permissions.length > 3" 
                                    class="px-2.5 py-1 text-[10px] font-bold rounded-lg bg-gray-50 text-gray-400 border border-gray-100"
                                >
                                    +{{ role.permissions.length - 3 }}
                                </span>
                                <span 
                                    v-if="role.permissions.length === 0" 
                                    class="text-xs italic text-gray-300"
                                >
                                    Restricted Access
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role Modal -->
        <Modal 
            :is-open="isModalOpen" 
            :title="isEditing ? 'Configure Role Identity' : 'Initialize New Role'"
            :loading="isSubmitting"
            :confirm-text="isEditing ? 'Update Configuration' : 'Initialize Role'"
            :max-width-class="'max-w-2xl'"
            :form-id="'roleForm'"
            @cancel="closeModal"
        >
             <form @submit.prevent="handleSubmit" id="roleForm" class="space-y-8 py-2">
                <div>
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-3">Role Designation</label>
                    <input 
                        v-model="form.name" 
                        type="text" 
                        required
                        class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-2 border-gray-100 text-gray-900 font-bold focus:border-agri-forest focus:ring-4 focus:ring-agri-forest/5 outline-none transition-all placeholder-gray-400"
                        placeholder="e.g. Regional Compliance Officer"
                    >
                </div>

                <div>
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-3">Functional Description</label>
                    <input 
                        required
                        v-model="form.description" 
                        type="text" 
                        class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-2 border-gray-100 text-gray-900 font-bold focus:border-agri-forest focus:ring-4 focus:ring-agri-forest/5 outline-none transition-all placeholder-gray-400"
                        placeholder="Define the scope of authority..."
                    >
                </div>

                <div>
                    <div class="flex items-center justify-between mb-4">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest">Permission Protocol</label>
                        <span class="text-xs font-bold text-agri-forest bg-agri-forest/10 px-3 py-1 rounded-lg">{{ form.permissions.length }} Active</span>
                    </div>
                    <div class="max-h-72 overflow-y-auto border-2 border-gray-100 rounded-2xl p-4 bg-gray-50/50 space-y-2 custom-scrollbar">
                        <label 
                            v-for="permission in permissions" 
                            :key="permission.id" 
                            class="flex items-start p-4 rounded-xl cursor-pointer transition-all duration-200 group hover:bg-white hover:shadow-sm border border-transparent hover:border-gray-100"
                            :class="{'bg-white shadow-sm border-agri-forest/20': form.permissions.includes(permission.id)}"
                        >
                            <div class="flex items-center h-6">
                                <input 
                                    type="checkbox" 
                                    :value="permission.id" 
                                    v-model="form.permissions"
                                    class="w-5 h-5 text-agri-forest border-gray-300 rounded-lg focus:ring-agri-forest transition-colors"
                                >
                            </div>
                            <div class="ml-4 select-none">
                                <span class="block text-sm font-bold text-gray-900 group-hover:text-agri-forest transition-colors">{{ permission.name }}</span>
                                <span class="block text-xs font-medium text-gray-500 mt-0.5">{{ permission.description }}</span>
                            </div>
                        </label>
                    </div>
                </div>
            </form>
        </Modal>

        <!-- Delete Confirmation Modal -->
        <Modal 
            :is-open="showDeleteConfirm" 
            title="Decommission Role"
            message="This action will permanently revoke this role identity from the registry. All associated user privileges will be suspended. Continue?"
            confirm-text="Confirm Decommission"
            cancel-text="Abort"
            type="danger"
            :loading="isSubmitting"
            :max-width-class="'max-w-md'"
            @confirm="deleteRole" 
            @cancel="showDeleteConfirm = false"
        />

        <!-- Enterprise Role Details Drawer -->
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
                    <!-- Backdrop -->
                    <div @click="closeDrawer" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
                    
                    <!-- Drawer Panel -->
                    <Transition
                        enter-active-class="transition-transform duration-500 ease-out"
                        leave-active-class="transition-transform duration-400 ease-in"
                        enter-from-class="translate-x-full"
                        enter-to-class="translate-x-0"
                        leave-from-class="translate-x-0"
                        leave-to-class="translate-x-full"
                    >
                        <div v-if="isDrawerOpen" class="absolute right-0 top-0 bottom-0 w-full max-w-2xl bg-white shadow-2xl flex flex-col">
                            <!-- Header -->
                            <div class="bg-gradient-to-br from-agri-forest to-agri-lush p-8 relative overflow-hidden">
                                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32 blur-3xl"></div>
                                <div class="absolute bottom-0 left-0 w-48 h-48 bg-agri-earth/20 rounded-full -ml-24 -mb-24 blur-2xl"></div>
                                
                                <div class="relative z-10">
                                    <div class="flex items-start justify-between mb-6">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3 mb-4">
                                                <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center border border-white/20 shadow-inner">
                                                    <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                                                </div>
                                                <span class="text-[10px] font-black text-white/50 uppercase tracking-[0.3em]">Role Identity</span>
                                            </div>
                                            <h2 class="text-4xl font-black text-white tracking-tight mb-2">{{ selectedRole?.name }}</h2>
                                            <p class="text-xs font-bold text-white/60 font-mono bg-white/10 inline-block px-3 py-1.5 rounded-lg border border-white/20">{{ selectedRole?.slug }}</p>
                                        </div>
                                        <button 
                                            @click="closeDrawer" 
                                            class="p-3 text-white/60 hover:text-white hover:bg-white/10 rounded-2xl transition-all"
                                        >
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>
                                    
                                    <div v-if="isSystemRole(selectedRole?.slug)" class="inline-flex items-center space-x-2 bg-amber-500/20 border border-amber-400/30 text-amber-100 px-4 py-2 rounded-xl">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                        <span class="text-xs font-bold">System Protected Role</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 overflow-y-auto p-8 space-y-8 custom-scrollbar">
                                <!-- Description Section -->
                                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                                    <div class="flex items-center space-x-2 mb-4">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Functional Description</h3>
                                    </div>
                                    <p v-if="selectedRole?.description" class="text-sm font-medium text-gray-700 leading-relaxed">{{ selectedRole.description }}</p>
                                    <p v-else class="text-sm italic text-gray-400">No description provided for this role.</p>
                                </div>

                                <!-- Metadata Grid -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-gradient-to-br from-blue-50 to-blue-100/50 rounded-2xl p-6 border border-blue-200/50">
                                        <div class="flex items-center space-x-2 mb-3">
                                            <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                                            </div>
                                            <span class="text-[10px] font-black text-blue-600/60 uppercase tracking-wider">Permissions</span>
                                        </div>
                                        <p class="text-3xl font-black text-blue-700">{{ selectedRole?.permissions?.length || 0 }}</p>
                                        <p class="text-xs font-bold text-blue-600/70 mt-1">Active Grants</p>
                                    </div>

                                    <div class="bg-gradient-to-br from-purple-50 to-purple-100/50 rounded-2xl p-6 border border-purple-200/50">
                                        <div class="flex items-center space-x-2 mb-3">
                                            <div class="w-8 h-8 rounded-lg bg-purple-500/20 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                            </div>
                                            <span class="text-[10px] font-black text-purple-600/60 uppercase tracking-wider">Type</span>
                                        </div>
                                        <p class="text-xl font-black text-purple-700">{{ isSystemRole(selectedRole?.slug) ? 'System' : 'Custom' }}</p>
                                        <p class="text-xs font-bold text-purple-600/70 mt-1">Role Classification</p>
                                    </div>
                                </div>

                                <!-- Permissions Matrix -->
                                <div>
                                    <div class="flex items-center justify-between mb-6">
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                            <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Permission Matrix</h3>
                                        </div>
                                        <span class="text-xs font-bold text-agri-forest bg-agri-forest/10 px-3 py-1.5 rounded-lg">{{ selectedRole?.permissions?.length || 0 }} Grants</span>
                                    </div>

                                    <div v-if="selectedRole?.permissions?.length > 0" class="space-y-3">
                                        <div 
                                            v-for="permission in selectedRole.permissions" 
                                            :key="permission.id"
                                            class="group bg-white border border-gray-100 rounded-2xl p-5 hover:border-agri-forest/30 hover:shadow-md transition-all duration-300"
                                        >
                                            <div class="flex items-start space-x-4">
                                                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-gradient-to-br from-agri-forest to-agri-lush flex items-center justify-center shadow-sm">
                                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <h4 class="text-sm font-bold text-gray-900 group-hover:text-agri-forest transition-colors mb-1">{{ permission.name }}</h4>
                                                    <p class="text-xs font-medium text-gray-500 leading-relaxed">{{ permission.description || 'No description available' }}</p>
                                                    <div class="mt-2">
                                                        <span class="inline-block text-[10px] font-bold text-gray-400 bg-gray-50 px-2 py-1 rounded-md font-mono border border-gray-100">{{ permission.slug }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else class="text-center py-12 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                        <p class="text-sm font-bold text-gray-400">No Permissions Assigned</p>
                                        <p class="text-xs text-gray-400 mt-1">This role has restricted access</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer Actions -->
                            <div class="border-t border-gray-100 p-6 bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <button 
                                        @click="closeDrawer" 
                                        class="px-6 py-3 text-sm font-bold text-gray-600 hover:text-gray-900 hover:bg-white rounded-xl transition-all"
                                    >
                                        Close
                                    </button>
                                    <div class="flex space-x-3">
                                        <button 
                                            @click="openModal(selectedRole); closeDrawer()" 
                                            class="px-6 py-3 bg-agri-forest text-white rounded-xl font-bold hover:bg-agri-lush transition-all shadow-lg hover:shadow-xl"
                                        >
                                            <span class="flex items-center space-x-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                <span>Edit Role</span>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Transition>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import Notification from '../../components/Notification.vue'
import Modal from '../../components/Modal.vue'

const notificationRef = ref(null)
const roles = ref([])
const permissions = ref([])
const isModalOpen = ref(false)
const isSubmitting = ref(false)
const isEditing = ref(false)
const editingId = ref(null)
const showDeleteConfirm = ref(false)
const roleToDelete = ref(null)
const isDrawerOpen = ref(false)
const selectedRole = ref(null)

const form = ref({
    name: '',
    description: '',
    permissions: []
})

const customRolesCount = computed(() => {
    return roles.value.filter(role => !isSystemRole(role.slug)).length
})

const isSystemRole = (slug) => {
    return ['super_admin', 'admin', 'vendor', 'user'].includes(slug)
}

const notify = (type, title, message) => {
    if (notificationRef.value && notificationRef.value.addNotification) {
        notificationRef.value.addNotification(type, title, message)
    } else if (window.notify) {
        window.notify[type](title, message)
    } else {
        console.warn('Notification system not available:', { type, title, message })
    }
}

const fetchData = async () => {
    try {
        const response = await axios.get('/api/super-admin/roles')
        roles.value = response.data.roles
        permissions.value = response.data.permissions
    } catch (error) {
        console.error('Failed to fetch roles:', error)
        notify('error', 'Connection Error', 'Failed to load roles and permissions.')
    }
}

const openModal = (role = null) => {
    if (role) {
        isEditing.value = true
        editingId.value = role.id
        form.value = {
            name: role.name,
            description: role.description || '',
            permissions: role.permissions.map(p => p.id)
        }
    } else {
        isEditing.value = false
        editingId.value = null
        form.value = {
            name: '',
            description: '',
            permissions: []
        }
    }
    isModalOpen.value = true
}

const closeModal = () => {
    isModalOpen.value = false
}

const handleSubmit = async () => {
    if (!form.value.name) return
    
    isSubmitting.value = true
    try {
        if (isEditing.value) {
            await axios.put(`/api/super-admin/roles/${editingId.value}`, form.value)
            notify('success', 'Role Updated', 'The role has been successfully reconfigured.')
        } else {
            await axios.post('/api/super-admin/roles', form.value)
            notify('success', 'Role Created', 'New role has been initialized.')
        }
        await fetchData()
        closeModal()
    } catch (error) {
        console.error('Failed to save role:', error)
        const errorMessage = error.response?.data?.message || 'Failed to process role configuration.'
        notify('error', 'Operation Failed', errorMessage)
    } finally {
        isSubmitting.value = false
    }
}

const openDrawer = (role) => {
    selectedRole.value = role
    isDrawerOpen.value = true
}

const closeDrawer = () => {
    isDrawerOpen.value = false
    setTimeout(() => {
        selectedRole.value = null
    }, 300)
}

const handleDelete = (role) => {
    roleToDelete.value = role
    showDeleteConfirm.value = true
}

const deleteRole = async () => {
    if (!roleToDelete.value) return

    isSubmitting.value = true
    try {
        await axios.delete(`/api/super-admin/roles/${roleToDelete.value.id}`)
        await fetchData()
        notify('success', 'Role Decommissioned', 'The role has been permanently deleted.')
        showDeleteConfirm.value = false
        roleToDelete.value = null
    } catch (error) {
        console.error('Failed to delete role:', error)
        const errorMessage = error.response?.data?.message || 'Failed to delete role.'
        notify('error', 'Deletion Failed', errorMessage)
    } finally {
        isSubmitting.value = false
    }
}

onMounted(() => {
    fetchData()
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #E5E7EB;
    border-radius: 20px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #D1D5DB;
}
</style>
