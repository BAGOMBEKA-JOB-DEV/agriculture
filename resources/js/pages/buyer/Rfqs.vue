<template>
  <div class="min-h-screen bg-gray-50/50 pb-12">
    <div class="bg-agri-forest py-16 relative overflow-hidden">
        <div class="absolute inset-0 bg-agri-earth/5 opacity-20"></div>
        <div class="container mx-auto px-6 relative z-10">
            <h1 class="text-4xl font-black text-white mb-2 tracking-tighter">
               Trade <span class="text-agri-earth">Requests</span>
            </h1>
            <p class="text-agri-earth/70 font-medium">Manage your sourcing requests and negotiate with verified suppliers.</p>
        </div>
    </div>

    <div class="container mx-auto px-6 -mt-12 relative z-20">
      
      <!-- Action Bar -->
      <div class="flex justify-between items-end mb-8">
        <div>
            <!-- Breadcrumb or Filter could go here -->
             <p class="text-sm text-gray-400 font-medium">Overview</p>
        </div>
        <button @click="$router.push('/rfqs/create')" 
                class="inline-flex items-center justify-center px-6 py-2.5 bg-agri-earth hover:bg-[#c29260] text-white text-sm font-bold uppercase tracking-wide rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            New Request
        </button>
      </div>

      <!-- RFQ Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
        <div v-for="rfq in rfqs" :key="rfq.id" class="card-enterprise flex flex-col h-full group hover:border-agri-forest transition-all duration-300">
            
            <!-- Card Header -->
            <div class="px-6 py-5 border-b border-gray-100/50 flex justify-between items-start bg-gray-50/30">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-white border border-gray-100 flex items-center justify-center text-agri-forest font-serif font-black text-lg">
                        {{ rfq.product?.name?.charAt(0) || 'R' }}
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 leading-tight group-hover:text-agri-forest transition-colors">{{ rfq.product?.name }}</h3>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">#{{ rfq.id }}</p>
                    </div>
                </div>
                <!-- Simple Status Dot + Text -->
                 <div class="flex items-center space-x-1.5 bg-white px-2 py-1 rounded-full border border-gray-100 shadow-sm">
                    <span class="w-2 h-2 rounded-full" 
                          :class="{
                              'bg-yellow-400': rfq.status === 'pending',
                              'bg-blue-500': rfq.status === 'responded',
                              'bg-green-500': rfq.status === 'accepted',
                              'bg-red-500': rfq.status === 'rejected'
                          }"></span>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-600">{{ rfq.status }}</span>
                </div>
            </div>

            <!-- Card Body -->
            <div class="p-6 flex-1 flex flex-col gap-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Volume</span>
                        <p class="text-sm font-semibold text-gray-900">{{ rfq.quantity }} <span class="text-gray-500 text-xs">{{ rfq.product?.unit }}</span></p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Needed By</span>
                        <p class="text-sm font-semibold text-gray-900">{{ formatDate(rfq.preferred_date) }}</p>
                    </div>
                </div>
                
                <!-- Supplier / Submitted To Section -->
                <div class="pt-2 border-t border-gray-50 grid grid-cols-2 gap-4">
                     <div class="space-y-1">
                        <!-- Label changes based on role -->
                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">
                            {{ rfq.buyer_id === authStore.user?.id ? 'Submitted To' : 'Requested By' }}
                        </span>
                        
                        <!-- Participant Name/Avatar -->
                        <div class="flex items-center">
                            <div class="w-5 h-5 rounded-full bg-agri-sand text-agri-forest text-[9px] font-black flex items-center justify-center mr-2 shadow-sm border border-agri-earth/10">
                                {{ (rfq.buyer_id === authStore.user?.id ? rfq.seller?.name : rfq.buyer?.name)?.charAt(0) || 'U' }}
                            </div>
                            <p class="text-xs font-bold text-gray-900 truncate">
                                {{ (rfq.buyer_id === authStore.user?.id ? rfq.seller?.name : rfq.buyer?.name) || 'Unknown User' }}
                            </p>
                        </div>
                     </div>
                     <div class="space-y-1">
                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Created</span>
                        <p class="text-xs font-semibold text-gray-700">{{ formatDate(rfq.created_at) }}</p>
                     </div>
                </div>

                <div class="space-y-1 pt-2 border-t border-gray-50">
                     <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Ship To</span>
                     <p class="text-sm font-medium text-gray-700 truncate flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        {{ rfq.delivery_location }}
                     </p>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex items-center justify-between">
                
                <!-- Response Count -->
                <div class="flex items-center space-x-2">
                    <div class="flex -space-x-1.5 overflow-hidden">
                        <div v-for="i in Math.min(rfq.vendor_response_count || 0, 3)" :key="i" class="w-5 h-5 rounded-full bg-agri-clay border border-white"></div>
                        <div v-if="(rfq.vendor_response_count || 0) === 0" class="w-5 h-5 rounded-full bg-gray-200 border border-white flex items-center justify-center text-[8px] text-gray-500">0</div>
                    </div>
                    <span class="text-xs font-medium text-gray-500">
                        <strong class="text-gray-900">{{ rfq.vendor_response_count || 0 }}</strong> Responses
                    </span>
                </div>

                <div class="flex items-center space-x-3">
                    <button @click="cancelRfq(rfq)" class="text-[10px] font-black text-gray-400 hover:text-red-500 uppercase tracking-wider transition-colors">
                        Cancel
                    </button>
                    <button @click="viewNegotiations(rfq)" 
                            class="text-[10px] font-black text-agri-forest hover:text-agri-earth uppercase tracking-wider flex items-center group/btn">
                        Negotiations
                        <svg class="w-3.5 h-3.5 ml-1 transform group-hover/btn:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
            </div>
        </div>
      </div>
      
      <!-- Empty State -->
      <div v-if="rfqs.length === 0" class="card-enterprise p-16 text-center">
           <div class="w-20 h-20 bg-agri-sand/50 rounded-full flex items-center justify-center mx-auto mb-6 text-agri-earth">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
           </div>
           <h3 class="text-lg font-bold text-gray-900 mb-2">Start Trading</h3>
           <p class="text-sm text-gray-500 mb-8 max-w-sm mx-auto">Create a request to broadcast your needs to our network of premium agriculture suppliers.</p>
           <button @click="$router.push('/rfqs/create')" class="inline-flex items-center px-6 py-3 bg-agri-earth hover:bg-[#c29260] text-white text-sm font-bold uppercase tracking-wide rounded-lg shadow-sm hover:shadow transition-all">
               Create First Request
           </button>
      </div>

    </div>

    <!-- Negotiation Drawer (Slide-over from Right) -->
    <div v-if="selectedRfq" class="relative z-50">
        <!-- Backdrop -->
        <transition 
            enter-active-class="ease-in-out duration-500" 
            enter-from-class="opacity-0" 
            enter-to-class="opacity-100" 
            leave-active-class="ease-in-out duration-500" 
            leave-from-class="opacity-100" 
            leave-to-class="opacity-0"
        >
            <div class="fixed inset-0 bg-agri-forest/30 backdrop-blur-sm transition-opacity" @click="closeNegotiations"></div>
        </transition>

        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="absolute inset-0 overflow-hidden">
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                    <transition 
                        enter-active-class="transform transition ease-in-out duration-500 sm:duration-700" 
                        enter-from-class="translate-x-full" 
                        enter-to-class="translate-x-0" 
                        leave-active-class="transform transition ease-in-out duration-500 sm:duration-700" 
                        leave-from-class="translate-x-0" 
                        leave-to-class="translate-x-full"
                    >
                        <div v-if="selectedRfq" class="pointer-events-auto w-screen max-w-6xl">
                            <div class="flex h-full bg-white shadow-xl flex-col">
                              
                              <!-- RFQ Summary Card (Top) -->
                              <div class="px-10 py-6 bg-agri-forest border-b border-agri-lush/20 shrink-0">
                                <div class="flex items-center justify-between mb-4">
                                  <h2 class="text-lg font-black text-white">Trade Request Summary</h2>
                                  <button @click="closeNegotiations" class="text-white/70 hover:text-white">
                                      <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                  </button>
                                </div>
                                <div class="grid grid-cols-4 gap-4">
                                  <div>
                                    <p class="text-[10px] text-agri-earth/60 font-black uppercase tracking-widest mb-1">Product</p>
                                    <p class="text-sm font-black text-white">{{ selectedRfq.product?.name }}</p>
                                  </div>
                                  <div>
                                    <p class="text-[10px] text-agri-earth/60 font-black uppercase tracking-widest mb-1">Quantity</p>
                                    <p class="text-sm font-black text-agri-earth">{{ selectedRfq.quantity }} {{ selectedRfq.product?.unit }}</p>
                                  </div>
                                  <div>
                                    <p class="text-[10px] text-agri-earth/60 font-black uppercase tracking-widest mb-1">Delivery Date</p>
                                    <p class="text-sm font-bold text-white">{{ formatDate(selectedRfq.preferred_date) }}</p>
                                  </div>
                                  <div>
                                    <p class="text-[10px] text-agri-earth/60 font-black uppercase tracking-widest mb-1">Location</p>
                                    <p class="text-sm font-bold text-white truncate">{{ selectedRfq.delivery_location || 'TBD' }}</p>
                                  </div>
                                </div>
                                <div v-if="selectedRfq.additional_notes" class="mt-4 pt-4 border-t border-agri-lush/20">
                                  <p class="text-[10px] text-agri-earth/60 font-black uppercase tracking-widest mb-1">Additional Notes</p>
                                  <p class="text-xs text-white/90 italic">"{{ selectedRfq.additional_notes }}"</p>
                                </div>
                              </div>

                              <!-- Negotiation Area (Role-Based) -->
                              <div class="flex flex-1 overflow-hidden">
                                <!-- BUYER VIEW: Conversation List + Chat -->
                                <template v-if="isRfqCreator">
                                  <!-- Vendor Conversation List (LEFT 1/3) -->
                                  <div class="w-1/3 border-r border-gray-100 flex flex-col bg-gray-50/30">
                                    <div class="p-8 border-b border-gray-100 bg-white">
                                      <h2 class="text-xs font-black text-gray-400 uppercase tracking-widest">Negotiating Vendors</h2>
                                    </div>
                                    <div class="flex-1 overflow-y-auto">
                                      <div v-for="convo in conversations" 
                                           :key="convo.user.id"
                                           @click="selectVendor(convo.user.id)"
                                           class="p-6 border-b border-gray-50 cursor-pointer hover:bg-white transition-all duration-300 relative group"
                                           :class="{ 'bg-white shadow-inner': selectedVendorId == convo.user.id }">
                                        <div v-if="selectedVendorId == convo.user.id" class="absolute left-0 top-0 bottom-0 w-1.5 bg-agri-forest"></div>
                                        
                                        <div class="flex items-center mb-3">
                                          <div class="w-10 h-10 rounded-xl bg-agri-sand flex items-center justify-center text-agri-forest font-black mr-3">
                                              {{ convo.user.name.charAt(0) }}
                                          </div>
                                          <div class="flex-1 min-w-0">
                                              <div class="flex justify-between items-center">
                                                  <span class="font-black text-gray-900 truncate">{{ convo.user.name }}</span>
                                                  <span class="text-[10px] font-bold text-gray-400">{{ formatRecentDate(convo.last_message.created_at) }}</span>
                                              </div>
                                          </div>
                                        </div>
                                        <p class="text-xs text-gray-500 font-medium truncate leading-relaxed">
                                          <span v-if="convo.last_message.sender_id === authStore.user?.id" class="text-agri-forest font-black">You:</span>
                                          {{ convo.last_message.message }}
                                        </p>
                                      </div>
                                      
                                      <div v-if="conversations.length === 0" class="p-12 text-center text-gray-400">
                                          <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-gray-100">
                                              <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                          </div>
                                          <p class="text-xs font-black uppercase tracking-widest">No Vendors Yet</p>
                                          <p class="text-xs mt-1">Vendors will appear here when they respond</p>
                                      </div>
                                    </div>
                                  </div>

                                  <!-- Chat with Selected Vendor (RIGHT 2/3) -->
                                  <div class="w-2/3 flex flex-col bg-white">
                                    <template v-if="selectedVendorId">
                                      <!-- Header -->
                                      <div class="px-10 py-6 border-b border-gray-100 flex justify-between items-center bg-white">
                                        <div class="flex items-center">
                                           <div class="w-10 h-10 rounded-xl bg-agri-forest text-agri-earth flex items-center justify-center font-black mr-4">
                                               {{ selectedVendorName.charAt(0) }}
                                           </div>
                                           <div>
                                               <span class="font-black text-gray-900">{{ selectedVendorName }}</span>
                                               <p class="text-[10px] text-agri-lush font-black uppercase tracking-widest">Verified Supplier</p>
                                           </div>
                                        </div>
                                      </div>

                                      <!-- Messages -->
                                      <div class="flex-1 overflow-y-auto p-10 space-y-8 bg-gray-50/20" ref="messagesContainer" @scroll="handleScroll">
                                        <template v-for="(msg, index) in activeMessages" :key="msg.id">
                                          
                                          <!-- Date Divider -->
                                          <div v-if="shouldShowDateDivider(msg, index, activeMessages)" class="flex justify-center my-6">
                                            <span class="bg-gray-100 text-gray-400 text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full">
                                              {{ formatDateDivider(msg.created_at) }}
                                            </span>
                                          </div>

                                          <div class="flex flex-col mb-2"
                                               :class="msg.sender_id === authStore.user?.id ? 'items-end' : 'items-start'">
                                            
                                            <div class="relative group max-w-[80%]">
                                                <div class="rounded-3xl px-8 py-5 shadow-sm"
                                                     :class="msg.sender_id === authStore.user?.id 
                                                         ? 'bg-agri-forest text-white rounded-br-none' 
                                                         : 'bg-white text-gray-800 rounded-bl-none border border-gray-100'">
                                                    <p v-if="msg.message" class="text-sm font-medium leading-relaxed">{{ msg.message }}</p>
                                                    
                                                    <!-- Attachments -->
                                                    <div v-if="msg.attachments && msg.attachments.length > 0" class="mt-3 space-y-2">
                                                        <div v-for="attachment in msg.attachments" :key="attachment.id">
                                                            <!-- Image Preview -->
                                                            <div v-if="attachment.file_type.startsWith('image/')" class="rounded-lg overflow-hidden">
                                                                <img :src="`/storage/${attachment.file_path}`" :alt="attachment.file_name" class="max-w-xs rounded-lg">
                                                                <a :href="`/storage/${attachment.file_path}`" download :class="msg.sender_id === authStore.user?.id ? 'text-agri-earth' : 'text-agri-forest'" class="text-xs font-bold mt-1 inline-block hover:underline">
                                                                    Download {{ attachment.file_name }}
                                                                </a>
                                                            </div>
                                                            
                                                            <!-- Document/File -->
                                                            <a v-else :href="`/storage/${attachment.file_path}`" download 
                                                               class="flex items-center space-x-2 p-3 rounded-lg transition-colors"
                                                               :class="msg.sender_id === authStore.user?.id ? 'bg-white/10 hover:bg-white/20' : 'bg-gray-100 hover:bg-gray-200'">
                                                                <svg class="w-5 h-5" :class="msg.sender_id === authStore.user?.id ? 'text-agri-earth' : 'text-agri-forest'" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"/>
                                                                </svg>
                                                                <div class="flex-1 min-w-0">
                                                                    <p class="text-xs font-bold truncate" :class="msg.sender_id === authStore.user?.id ? 'text-white' : 'text-gray-900'">{{ attachment.file_name }}</p>
                                                                    <p class="text-[10px]" :class="msg.sender_id === authStore.user?.id ? 'text-agri-earth/70' : 'text-gray-500'">{{ formatFileSize(attachment.file_size) }}</p>
                                                                </div>
                                                                <svg class="w-4 h-4" :class="msg.sender_id === authStore.user?.id ? 'text-agri-earth' : 'text-agri-forest'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                                </svg>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex items-center mt-1.5 space-x-1.5 px-2" :class="msg.sender_id === authStore.user?.id ? 'justify-end' : 'justify-start'">
                                                    <span class="text-[9px] font-bold uppercase tracking-widest text-gray-400">
                                                        {{ formatFullDate(msg.created_at) }}
                                                    </span>
                                                    <!-- Read Receipt for own messages -->
                                                    <svg v-if="msg.sender_id === authStore.user?.id && msg.is_read" 
                                                         class="w-3 h-3 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M18 7l-1.41-1.41-6.34 6.34 1.41 1.41L18 7zm4.24-1.41L11.66 16.17 7.48 12l-1.41 1.41L11.66 19l12-12-1.42-1.41zM.41 13.41L6 19l1.41-1.41L1.83 12 .41 13.41z"/>
                                                    </svg>
                                                    <svg v-else-if="msg.sender_id === authStore.user?.id" 
                                                         class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M18 7l-1.41-1.41-6.34 6.34 1.41 1.41L18 7zm4.24-1.41L11.66 16.17 7.48 12l-1.41 1.41L11.66 19l12-12-1.42-1.41zM.41 13.41L6 19l1.41-1.41L1.83 12 .41 13.41z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                          </div>
                                        </template>
                                      </div>

                                      <!-- Input -->
                                      <div class="p-8 border-t border-gray-100 bg-white">
                                        <form @submit.prevent="sendMessage" class="space-y-3">
                                          <!-- Selected Files Preview -->
                                          <div v-if="selectedFiles.length > 0" class="flex flex-wrap gap-2">
                                              <div v-for="(file, index) in selectedFiles" :key="index" class="flex items-center space-x-2 bg-gray-100 rounded-lg px-3 py-2">
                                                  <svg class="w-4 h-4 text-agri-forest" fill="currentColor" viewBox="0 0 20 20">
                                                      <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"/>
                                                  </svg>
                                                  <span class="text-xs font-medium text-gray-700 truncate max-w-[150px]">{{ file.name }}</span>
                                                  <button type="button" @click="removeFile(index)" class="text-red-500 hover:text-red-700">
                                                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                      </svg>
                                                  </button>
                                              </div>
                                          </div>
                                          
                                          <div class="flex items-center bg-gray-50 rounded-2xl p-2 focus-within:ring-4 focus-within:ring-agri-forest/5 transition-all">
                                            <input ref="fileInput" type="file" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.zip" @change="handleFileSelect" class="hidden">
                                            <button type="button" @click="$refs.fileInput.click()" class="text-gray-400 hover:text-agri-forest p-2 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                                </svg>
                                            </button>
                                            <input v-model="newMessage" 
                                                   type="text" 
                                                   placeholder="Draft negotiation response..." 
                                                   class="flex-1 bg-transparent border-none px-4 py-4 focus:outline-none font-medium text-sm text-gray-900">
                                            <button type="submit" 
                                                    :disabled="(!newMessage.trim() && selectedFiles.length === 0) || sending"
                                                    class="bg-agri-forest text-agri-earth w-12 h-12 rounded-xl flex items-center justify-center hover:bg-agri-lush hover:text-white disabled:opacity-30 transition-all shadow-lg">
                                              <svg class="w-5 h-5 transform rotate-90" fill="currentColor" viewBox="0 0 20 20">
                                                  <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
                                              </svg>
                                            </button>
                                          </div>
                                        </form>
                                      </div>
                                    </template>
                                    <div v-else class="flex-1 flex flex-col items-center justify-center text-gray-400 bg-gray-50/30">
                                      <div class="w-24 h-24 rounded-[3rem] bg-white shadow-sm flex items-center justify-center mb-8 text-gray-200">
                                          <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                          </svg>
                                      </div>
                                      <p class="font-black uppercase text-[10px] tracking-widest text-gray-400">Select Vendor to Negotiate</p>
                                    </div>
                                  </div>
                                </template>

                                <!-- VENDOR VIEW: Direct Chat with Buyer -->
                                <template v-else>
                                  <div class="flex-1 flex flex-col bg-white">
                                    <!-- Header -->
                                    <div class="px-10 py-6 border-b border-gray-100 flex items-center bg-white">
                                       <div class="w-10 h-10 rounded-xl bg-agri-forest text-agri-earth flex items-center justify-center font-black mr-4">
                                           {{ selectedRfq.buyer?.name?.charAt(0) || 'B' }}
                                       </div>
                                       <div>
                                           <span class="font-black text-gray-900">{{ selectedRfq.buyer?.name }}</span>
                                           <p class="text-[10px] text-blue-600 font-black uppercase tracking-widest">Request Creator</p>
                                       </div>
                                    </div>

                                    <!-- Messages -->
                                    <div class="flex-1 overflow-y-auto p-10 space-y-8 bg-gray-50/20" ref="messagesContainer">
                                      <div v-for="msg in activeMessages" :key="msg.id" 
                                           class="flex flex-col mb-2"
                                           :class="msg.sender_id === authStore.user?.id ? 'items-end' : 'items-start'">
                                        
                                        <div class="relative group max-w-[80%]">
                                            <div class="rounded-3xl px-8 py-5 shadow-sm"
                                                 :class="msg.sender_id === authStore.user?.id 
                                                     ? 'bg-agri-forest text-white rounded-br-none' 
                                                     : 'bg-white text-gray-800 rounded-bl-none border border-gray-100'">
                                                <p v-if="msg.message" class="text-sm font-medium leading-relaxed">{{ msg.message }}</p>
                                                
                                                <!-- Attachments -->
                                                <div v-if="msg.attachments && msg.attachments.length > 0" class="mt-3 space-y-2">
                                                    <div v-for="attachment in msg.attachments" :key="attachment.id">
                                                        <!-- Image Preview -->
                                                        <div v-if="attachment.file_type.startsWith('image/')" class="rounded-lg overflow-hidden">
                                                            <img :src="`/storage/${attachment.file_path}`" :alt="attachment.file_name" class="max-w-xs rounded-lg">
                                                            <a :href="`/storage/${attachment.file_path}`" download :class="msg.sender_id === authStore.user?.id ? 'text-agri-earth' : 'text-agri-forest'" class="text-xs font-bold mt-1 inline-block hover:underline">
                                                                Download {{ attachment.file_name }}
                                                            </a>
                                                        </div>
                                                        
                                                        <!-- Document/File -->
                                                        <a v-else :href="`/storage/${attachment.file_path}`" download 
                                                           class="flex items-center space-x-2 p-3 rounded-lg transition-colors"
                                                           :class="msg.sender_id === authStore.user?.id ? 'bg-white/10 hover:bg-white/20' : 'bg-gray-100 hover:bg-gray-200'">
                                                            <svg class="w-5 h-5" :class="msg.sender_id === authStore.user?.id ? 'text-agri-earth' : 'text-agri-forest'" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"/>
                                                            </svg>
                                                            <div class="flex-1 min-w-0">
                                                                <p class="text-xs font-bold truncate" :class="msg.sender_id === authStore.user?.id ? 'text-white' : 'text-gray-900'">{{ attachment.file_name }}</p>
                                                                <p class="text-[10px]" :class="msg.sender_id === authStore.user?.id ? 'text-agri-earth/70' : 'text-gray-500'">{{ formatFileSize(attachment.file_size) }}</p>
                                                            </div>
                                                            <svg class="w-4 h-4" :class="msg.sender_id === authStore.user?.id ? 'text-agri-earth' : 'text-agri-forest'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                            </svg>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center mt-1.5 space-x-1.5 px-2" :class="msg.sender_id === authStore.user?.id ? 'justify-end' : 'justify-start'">
                                                <span class="text-[9px] font-bold uppercase tracking-widest text-gray-400">
                                                    {{ formatFullDate(msg.created_at) }}
                                                </span>
                                                <!-- Read Receipt for own messages -->
                                                <svg v-if="msg.sender_id === authStore.user?.id && msg.is_read" 
                                                     class="w-3 h-3 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M18 7l-1.41-1.41-6.34 6.34 1.41 1.41L18 7zm4.24-1.41L11.66 16.17 7.48 12l-1.41 1.41L11.66 19l12-12-1.42-1.41zM.41 13.41L6 19l1.41-1.41L1.83 12 .41 13.41z"/>
                                                </svg>
                                                <svg v-else-if="msg.sender_id === authStore.user?.id" 
                                                     class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M18 7l-1.41-1.41-6.34 6.34 1.41 1.41L18 7zm4.24-1.41L11.66 16.17 7.48 12l-1.41 1.41L11.66 19l12-12-1.42-1.41zM.41 13.41L6 19l1.41-1.41L1.83 12 .41 13.41z"/>
                                                </svg>
                                            </div>
                                        </div>
                                      </div>
                                    </div>

                                    <!-- Input -->
                                    <div class="p-8 border-t border-gray-100 bg-white">
                                      <form @submit.prevent="sendMessage" class="space-y-3">
                                        <!-- Selected Files Preview -->
                                        <div v-if="selectedFiles.length > 0" class="flex flex-wrap gap-2">
                                            <div v-for="(file, index) in selectedFiles" :key="index" class="flex items-center space-x-2 bg-gray-100 rounded-lg px-3 py-2">
                                                <svg class="w-4 h-4 text-agri-forest" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"/>
                                                </svg>
                                                <span class="text-xs font-medium text-gray-700 truncate max-w-[150px]">{{ file.name }}</span>
                                                <button type="button" @click="removeFile(index)" class="text-red-500 hover:text-red-700">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center bg-gray-50 rounded-2xl p-2 focus-within:ring-4 focus-within:ring-agri-forest/5 transition-all">
                                          <input ref="fileInput" type="file" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.zip" @change="handleFileSelect" class="hidden">
                                          <button type="button" @click="$refs.fileInput.click()" class="text-gray-400 hover:text-agri-forest p-2 transition-colors">
                                              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                              </svg>
                                          </button>
                                          <input v-model="newMessage" 
                                                 type="text" 
                                                 placeholder="Draft your proposal..." 
                                                 class="flex-1 bg-transparent border-none px-4 py-4 focus:outline-none font-medium text-sm text-gray-900">
                                          <button type="submit" 
                                                  :disabled="(!newMessage.trim() && selectedFiles.length === 0) || sending"
                                                  class="bg-agri-forest text-agri-earth w-12 h-12 rounded-xl flex items-center justify-center hover:bg-agri-lush hover:text-white disabled:opacity-30 transition-all shadow-lg">
                                            <svg class="w-5 h-5 transform rotate-90" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
                                            </svg>
                                          </button>
                                        </div>
                                      </form>
                                    </div>
                                  </div>
                                </template>
                              </div>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const rfqs = ref([]);
const loading = ref(true);
const selectedRfq = ref(null);
const conversations = ref([]);
const selectedVendorId = ref(null);
const selectedVendorName = ref('');
const activeMessages = ref([]);
const newMessage = ref('');
const sending = ref(false);
const messagesContainer = ref(null);
const pollingInterval = ref(null);
const selectedFiles = ref([]);
const fileInput = ref(null);

const isRfqCreator = computed(() => {
  return selectedRfq.value && selectedRfq.value.buyer_id === authStore.user?.id;
});

const handleFileSelect = (event) => {
    const files = Array.from(event.target.files);
    selectedFiles.value = [...selectedFiles.value, ...files];
    event.target.value = ''; // Reset input
};

const removeFile = (index) => {
    selectedFiles.value.splice(index, 1);
};

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
};

const fetchRfqs = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/rfqs');
    // Show ALL RFQs to everyone
    rfqs.value = response.data;
  } catch (error) {
    console.error('Failed to fetch RFQs:', error);
  } finally {
    loading.value = false;
  }
};

const viewNegotiations = async (rfq) => {
    selectedRfq.value = rfq;
    
    // If user is the RFQ creator (buyer), show conversation list
    if (rfq.buyer_id === authStore.user?.id) {
        await fetchConversations(rfq.id);
    } else {
        // If user is a vendor, show direct chat with buyer
        await fetchMessages(rfq.id, rfq.buyer_id);
        startPolling(rfq.id, rfq.buyer_id);
    }
};

const fetchConversations = async (rfqId) => {
    try {
        const response = await axios.get(`/api/rfqs/${rfqId}/messages`);
        const messages = response.data;
        
        // Group messages by vendor (sender who is not the buyer)
        const grouped = messages.reduce((acc, msg) => {
            // If I sent it, the vendor is the receiver. If they sent it, they are the vendor.
            const vendorId = msg.sender_id === authStore.user.id ? msg.receiver_id : msg.sender_id;
            
            if (vendorId) { // Only group if we can identify the vendor
                if (!acc[vendorId]) {
                    acc[vendorId] = [];
                }
                acc[vendorId].push(msg);
            }
            return acc;
        }, {});
        
        // Convert to conversation format
        conversations.value = Object.entries(grouped).map(([vendorId, msgs]) => {
            const lastMsg = msgs[msgs.length - 1];
            // Identify the counterparty user object
            const counterparty = msgs.find(m => m.sender_id != authStore.user.id)?.sender 
                || (lastMsg.receiver_id == vendorId ? lastMsg.receiver : null) // Logic to find user details if only I sent messages
                || { id: vendorId, name: 'Vendor' }; // Fallback

            return {
                user: counterparty,
                last_message: lastMsg
            };
        });
    } catch (error) {
        console.error('Failed to load conversations:', error);
    }
};

const selectVendor = async (vendorId) => {
    selectedVendorId.value = vendorId;
    const convo = conversations.value.find(c => c.user.id == vendorId);
    selectedVendorName.value = convo ? convo.user.name : 'Vendor';
    
    await fetchMessages(selectedRfq.value.id, vendorId);
    startPolling(selectedRfq.value.id, vendorId);
};

const userScrolledUp = ref(false);

const handleScroll = (e) => {
    const { scrollTop, scrollHeight, clientHeight } = e.target;
    userScrolledUp.value = scrollHeight - scrollTop - clientHeight > 50;
};

const shouldShowDateDivider = (msg, index, allMessages) => {
    if (index === 0) return true;
    const prevMsg = allMessages[index - 1];
    const currentDate = new Date(msg.created_at).toDateString();
    const prevDate = new Date(prevMsg.created_at).toDateString();
    return currentDate !== prevDate;
};

const formatDateDivider = (dateString) => {
    const date = new Date(dateString);
    const today = new Date();
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);
    
    if (date.toDateString() === today.toDateString()) return 'Today';
    if (date.toDateString() === yesterday.toDateString()) return 'Yesterday';
    return date.toLocaleDateString([], { weekday: 'short', month: 'short', day: 'numeric' });
};

const fetchMessages = async (rfqId, vendorId, silent = false) => {
    try {
        const response = await axios.get(`/api/rfqs/${rfqId}/messages`);
        
        // Filter messages between buyer and selected vendor
        const allMessages = response.data;
        const filtered = allMessages.filter(m => 
            // 1. Sent by ME to THIS VENDOR
            (m.sender_id == authStore.user?.id && m.receiver_id == vendorId) ||
            // 2. Sent by ME (Legacy/Broadcast - no receiver set) -> Show in all chats contextually? 
            //    Yes, to avoid "missing messages" complaint.
            (m.sender_id == authStore.user?.id && !m.receiver_id) ||
            // 3. Sent by VENDOR (Receiver is implied to be ME or NULL)
            (m.sender_id == vendorId) 
        );
        
        activeMessages.value = filtered;

        if (!silent || !userScrolledUp.value) {
            scrollToBottom();
        }
    } catch (error) {
        console.error('Failed to load messages:', error);
        if (!silent) activeMessages.value = [];
    }
};

const startPolling = (rfqId, vendorId) => {
    stopPolling();
    pollingInterval.value = setInterval(() => {
        fetchMessages(rfqId, vendorId, true);
    }, 3000);
};

const stopPolling = () => {
    if (pollingInterval.value) {
        clearInterval(pollingInterval.value);
        pollingInterval.value = null;
    }
};

const closeNegotiations = () => {
    selectedRfq.value = null;
    selectedVendorId.value = null;
    conversations.value = [];
    activeMessages.value = [];
    stopPolling();
};

const sendMessage = async () => {
    if (!newMessage.value.trim() && selectedFiles.value.length === 0) return;
    
    sending.value = true;
    try {
        const formData = new FormData();
        formData.append('message', newMessage.value);
        if (selectedVendorId.value) {
             formData.append('receiver_id', selectedVendorId.value);
        }
        
        // Append files
        selectedFiles.value.forEach((file, index) => {
            formData.append(`attachments[${index}]`, file);
        });
        
        const response = await axios.post(`/api/rfqs/${selectedRfq.value.id}/messages`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        
        // Optimistically update is risky if we rely on relationships not returned immediately
        // But since we poll/fetch, we can just fetch.
        await fetchMessages(selectedRfq.value.id, selectedVendorId.value, true);
        
        newMessage.value = '';
        selectedFiles.value = [];
        scrollToBottom();
        fetchConversations(selectedRfq.value.id); // Update sidebar list order/preview
    } catch (error) {
        console.error('Failed to send message:', error);
        const errorMsg = error.response?.data?.message || 'Message sending failed.';
        if (window.notify) window.notify.error('Error', errorMsg);
    } finally {
        sending.value = false;
    }
};

const scrollToBottom = () => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
    });
};

const cancelRfq = async (rfq) => {
    if (!confirm('Are you sure you want to cancel this request?')) return;
    
    try {
        await axios.delete(`/api/rfqs/${rfq.id}`);
        rfqs.value = rfqs.value.filter(r => r.id !== rfq.id);
        if (window.notify) window.notify.success('Cancelled', 'Trade Request withdrawn successfully.');
    } catch (error) {
        console.error('Failed to cancel RFQ:', error);
    }
};

const formatDate = (dateString) => {
    if (!dateString) return 'TBA';
    return new Date(dateString).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

const formatRecentDate = (date) => {
  if (!date) return '';
  const d = new Date(date);
  return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

const formatFullDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleString([], { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
};

const getStatusClasses = (status) => {
    switch (status) {
        case 'pending': return 'bg-yellow-50 text-yellow-600 border border-yellow-100';
        case 'responded': return 'bg-blue-50 text-blue-600 border border-blue-100';
        case 'accepted': return 'bg-green-50 text-green-600 border border-green-100';
        case 'rejected': return 'bg-red-50 text-red-600 border border-red-100';
        default: return 'bg-gray-50 text-gray-500';
    }
};

onMounted(() => {
  fetchRfqs();
});

onUnmounted(() => {
    stopPolling();
});
</script>
