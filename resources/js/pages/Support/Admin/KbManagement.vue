<template>
    <div class="min-h-screen bg-gray-50/50 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="py-8 flex flex-col md:flex-row md:items-end md:justify-between space-y-4 md:space-y-0">
                <div>
                    <div class="flex items-center space-x-2 text-agri-forest/60 mb-2">
                        <router-link to="/support/admin" class="hover:text-agri-forest transition-colors">Agent Portal</router-link>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <span class="font-bold text-agri-forest">Knowledge Base</span>
                    </div>
                    <h1 class="text-3xl font-extrabold text-agri-forest tracking-tight">Manage Articles</h1>
                    <p class="mt-1 text-sm text-gray-500 font-medium">Create and edit help articles for your users.</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button @click="openCategoryModal"
                        class="inline-flex items-center px-4 py-2 bg-white text-agri-forest border border-agri-forest/20 rounded-xl text-sm font-semibold hover:bg-gray-50 shadow-sm transition-all duration-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                        </svg>
                        Categories
                    </button>
                    <button @click="openArticleModal()"
                        class="inline-flex items-center px-4 py-2 bg-agri-forest text-white rounded-xl text-sm font-semibold hover:bg-agri-lush shadow-md transition-all duration-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        New Article
                    </button>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Articles</p>
                    <h3 class="text-2xl font-black text-gray-900">{{ articles.length }}</h3>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Published</p>
                    <h3 class="text-2xl font-black text-[#155724]">{{ articles.filter(a => a.is_published).length }}</h3>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Views</p>
                    <h3 class="text-2xl font-black text-blue-600">{{ totalViews }}</h3>
                </div>
            </div>

            <!-- Articles List -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
                    <h3 class="font-bold text-gray-900 tracking-tight">Article List</h3>
                    <div class="relative w-64">
                        <input v-model="searchQuery" type="text" placeholder="Filter articles..."
                            class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-agri-forest outline-none transition-all">
                        <svg class="w-4 h-4 absolute left-3.5 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Article Details</th>
                                <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Category</th>
                                <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                                <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Performance</th>
                                <th class="px-8 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr v-for="article in filteredArticles" :key="article.id" class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-8 py-5">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-xl bg-agri-forest/5 flex items-center justify-center mr-4 text-agri-forest group-hover:scale-110 transition-transform">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-bold text-gray-900 group-hover:text-agri-forest transition-colors">{{ article.title }}</h4>
                                            <p class="text-[10px] text-gray-400 mt-0.5 font-medium">Modified {{ formatDate(article.updated_at) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-[10px] font-bold uppercase tracking-wider">
                                        {{ article.category?.name || 'Uncategorized' }}
                                    </span>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center">
                                        <div :class="article.is_published ? 'bg-green-500' : 'bg-orange-400'" class="w-2 h-2 rounded-full mr-2"></div>
                                        <span class="text-xs font-bold" :class="article.is_published ? 'text-green-600' : 'text-orange-600'">
                                            {{ article.is_published ? 'Published' : 'Draft' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center space-x-6">
                                        <div class="flex items-center text-gray-500">
                                            <svg class="w-4 h-4 mr-1.5 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            <span class="text-xs font-bold">{{ article.views_count || 0 }}</span>
                                        </div>
                                        <div class="flex items-center text-green-600/80">
                                            <svg class="w-4 h-4 mr-1.5 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.704a1 1 0 01.94 1.347l-2.292 6.059A2 2 0 0115.448 19H8a2 2 0 01-2-2v-7a2 2 0 01.653-1.47l4.83-4.83a1 1 0 011.414 0L14 4.828V10z"></path>
                                            </svg>
                                            <span class="text-xs font-bold">{{ article.helpful_count || 0 }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button @click="openArticleModal(article)"
                                            class="w-8 h-8 rounded-lg border border-gray-100 flex items-center justify-center text-gray-400 hover:text-agri-forest hover:border-agri-forest hover:bg-agri-forest/5 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button @click="confirmDeleteArticle(article)"
                                            class="w-8 h-8 rounded-lg border border-red-50 flex items-center justify-center text-red-300 hover:text-red-600 hover:border-red-200 hover:bg-red-50 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="filteredArticles.length === 0">
                                <td colspan="5" class="px-8 py-12 text-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <h4 class="text-sm font-bold text-gray-400">No articles found matching your filter.</h4>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Article Editor Modal -->
        <div v-if="showArticleModal" class="fixed inset-0 z-[100] flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-agri-forest/80 backdrop-blur-sm transition-opacity" @click="showArticleModal = false"></div>
            <div class="bg-white w-full max-w-4xl rounded-3xl shadow-2xl relative z-10 overflow-hidden transform transition-all h-[90vh] flex flex-col">
                <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                    <h3 class="text-xl font-extrabold text-agri-forest tracking-tight">{{ editingArticle.id ? 'Edit Article' : 'New Article' }}</h3>
                    <button @click="showArticleModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="flex-1 overflow-y-auto p-8 space-y-6">
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Title</label>
                        <input v-model="editingArticle.title" type="text"
                            class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-6 py-4 text-lg font-bold focus:ring-4 focus:ring-agri-forest/10 focus:bg-white outline-none transition-all placeholder-gray-300"
                            placeholder="e.g., How to use the app">
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Category</label>
                            <select v-model="editingArticle.category_id"
                                class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-5 py-4 text-sm font-semibold focus:ring-4 focus:ring-agri-forest/10 focus:bg-white outline-none transition-all appearance-none">
                                <option value="" disabled>Select category</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Access & Visibility</label>
                            <div class="flex items-center space-x-4 h-[60px]">
                                <label class="flex items-center cursor-pointer group">
                                    <div class="relative">
                                        <input type="checkbox" v-model="editingArticle.is_published" class="sr-only">
                                        <div :class="editingArticle.is_published ? 'bg-agri-forest' : 'bg-gray-200'" class="w-10 h-5 rounded-full transition-colors"></div>
                                        <div :class="editingArticle.is_published ? 'translate-x-5' : 'translate-x-0'" class="absolute -left-0 -top-0 bg-white w-5 h-5 rounded-full border border-gray-200 transform transition-transform"></div>
                                    </div>
                                    <span class="ml-3 text-xs font-bold text-gray-600 group-hover:text-agri-forest transition-colors">Published</span>
                                </label>
                                <label class="flex items-center cursor-pointer group">
                                    <div class="relative">
                                        <input type="checkbox" v-model="editingArticle.is_public" class="sr-only">
                                        <div :class="editingArticle.is_public ? 'bg-blue-600' : 'bg-gray-200'" class="w-10 h-5 rounded-full transition-colors"></div>
                                        <div :class="editingArticle.is_public ? 'translate-x-5' : 'translate-x-0'" class="absolute -left-0 -top-0 bg-white w-5 h-5 rounded-full border border-gray-200 transform transition-transform"></div>
                                    </div>
                                    <span class="ml-3 text-xs font-bold text-gray-600 group-hover:text-blue-600 transition-colors">Public Access</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Article Content (Markdown)</label>
                        <textarea v-model="editingArticle.content" rows="12"
                            class="w-full bg-gray-50 border border-gray-100 rounded-3xl px-6 py-6 text-base focus:ring-4 focus:ring-agri-forest/10 focus:bg-white outline-none transition-all font-mono leading-relaxed"
                            placeholder="Draft your knowledge here..."></textarea>
                    </div>
                </div>

                <div class="px-8 py-6 border-t border-gray-50 flex items-center justify-between bg-gray-50/50">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Autosaving disabled in draft mode</p>
                    <div class="flex space-x-3">
                        <button @click="showArticleModal = false" class="px-6 py-3 text-gray-500 font-bold text-sm hover:text-gray-900 transition-colors">Discard</button>
                        <button @click="saveArticle" :disabled="!isArticleValid"
                            class="px-8 py-3 bg-agri-forest text-white text-sm font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-agri-forest/20 hover:shadow-xl hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50 disabled:pointer-events-none">
                            {{ editingArticle.id ? 'Commit Changes' : 'Publish Core' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Management Modal -->
        <div v-if="showCategoryModal" class="fixed inset-0 z-[100] flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-agri-forest/80 backdrop-blur-sm transition-opacity" @click="showCategoryModal = false"></div>
            <div class="bg-white w-full max-w-xl rounded-3xl shadow-2xl relative z-10 overflow-hidden transform transition-all">
                <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                    <h3 class="text-lg font-bold text-agri-forest tracking-tight">Manage Categories</h3>
                    <button @click="showCategoryModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-8">
                    <div class="space-y-4 mb-8 max-h-[300px] overflow-y-auto pr-2">
                        <div v-for="cat in categories" :key="cat.id" class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100 group">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center mr-3 text-agri-forest">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ cat.name }}</p>
                                    <p class="text-[10px] text-gray-400 font-medium">{{ cat.articles_count || 0 }} Articles</p>
                                </div>
                            </div>
                            <button @click="confirmDeleteCategory(cat)" class="opacity-0 group-hover:opacity-100 text-red-400 hover:text-red-600 p-2 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="bg-agri-forest/5 p-6 rounded-3xl border border-agri-forest/10">
                        <h4 class="text-[10px] font-black text-agri-forest uppercase tracking-widest mb-4">Add Category</h4>
                        <div class="space-y-4">
                            <input v-model="newCategory.name" type="text" placeholder="Category Name"
                                class="w-full bg-white border border-gray-100 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-agri-forest outline-none transition-all">
                            <textarea v-model="newCategory.description" rows="2" placeholder="Brief description..."
                                class="w-full bg-white border border-gray-100 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-agri-forest outline-none transition-all resize-none"></textarea>
                            <button @click="createCategory" :disabled="!newCategory.name"
                                class="w-full py-3 bg-agri-forest text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-agri-lush transition-all disabled:opacity-50">
                                Save Category
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reusable Delete Modal -->
        <Modal 
            :is-open="showDeleteModal" 
            :title="deleteModalTitle" 
            :message="deleteModalMessage" 
            confirm-text="Delete" 
            type="danger" 
            :loading="isDeleting"
            @confirm="handleDelete" 
            @cancel="showDeleteModal = false"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import Modal from '../../../components/Modal.vue'

const articles = ref([])
const categories = ref([])
const searchQuery = ref('')
const isLoading = ref(false)

// Delete Modal State
const showDeleteModal = ref(false)
const isDeleting = ref(false)
const itemToDelete = ref(null)
const deleteType = ref('article') // 'article' or 'category'
const deleteModalTitle = computed(() => deleteType.value === 'article' ? 'Delete Article' : 'Delete Category')
const deleteModalMessage = computed(() => {
    if (deleteType.value === 'article') {
        return `Are you sure you want to delete "${itemToDelete.value?.title}"? This action cannot be undone.`
    }
    return `Are you sure you want to delete "${itemToDelete.value?.name}"? All articles in this category will be uncategorized.`
})

// Article Modal State
const showArticleModal = ref(false)
const editingArticle = ref({
    id: null,
    title: '',
    content: '',
    category_id: '',
    is_published: false,
    is_public: true
})

// Category Modal State
const showCategoryModal = ref(false)
const newCategory = ref({
    name: '',
    description: '',
    is_public: true
})

const filteredArticles = computed(() => {
    if (!searchQuery.value) return articles.value
    const q = searchQuery.value.toLowerCase()
    return articles.value.filter(a => 
        a.title.toLowerCase().includes(q) || 
        a.category?.name.toLowerCase().includes(q)
    )
})

const totalViews = computed(() => {
    return articles.value.reduce((sum, a) => sum + (a.views_count || 0), 0)
})

const isArticleValid = computed(() => {
    return editingArticle.value.title && editingArticle.value.content && editingArticle.value.category_id
})

const fetchKbData = async () => {
    isLoading.value = true
    try {
        const [artResp, catResp] = await Promise.all([
            axios.get('/api/support/kb/articles?include_drafts=1&limit=100'),
            axios.get('/api/support/kb/categories')
        ])
        articles.value = artResp.data.data || artResp.data
        categories.value = catResp.data
    } catch (error) {
        console.error('Failed to fetch KB data:', error)
    } finally {
        isLoading.value = false
    }
}

const openArticleModal = (article = null) => {
    if (article) {
        editingArticle.value = { ...article }
    } else {
        editingArticle.value = {
            id: null,
            title: '',
            content: '',
            category_id: categories.value[0]?.id || '',
            is_published: false,
            is_public: true
        }
    }
    showArticleModal.value = true
}

const saveArticle = async () => {
    try {
        if (editingArticle.value.id) {
            await axios.put(`/api/support/admin/kb/articles/${editingArticle.value.id}`, editingArticle.value)
            if (window.notify) window.notify.success('Article Updated', 'The article has been successfully modified.')
        } else {
            await axios.post('/api/support/admin/kb/articles', editingArticle.value)
            if (window.notify) window.notify.success('Article Created', 'Your new article is now live.')
        }
        await fetchKbData()
        showArticleModal.value = false
    } catch (error) {
        console.error('Failed to save article:', error)
        if (window.notify) window.notify.error('Oops!', 'Could not save the article. Please check all fields.')
    }
}

const confirmDeleteArticle = (article) => {
    itemToDelete.value = article
    deleteType.value = 'article'
    showDeleteModal.value = true
}

const confirmDeleteCategory = (category) => {
    itemToDelete.value = category
    deleteType.value = 'category'
    showDeleteModal.value = true
}

const handleDelete = async () => {
    isDeleting.value = true
    try {
        if (deleteType.value === 'article') {
            await axios.delete(`/api/support/admin/kb/articles/${itemToDelete.value.id}`)
            if (window.notify) window.notify.success('Article Removed', 'The knowledge asset was deleted.')
        } else {
            await axios.delete(`/api/support/admin/kb/categories/${itemToDelete.value.id}`)
            if (window.notify) window.notify.success('Category Removed', 'The category was deleted.')
        }
        await fetchKbData()
        showDeleteModal.value = false
    } catch (error) {
        console.error('Failed to delete:', error)
        if (window.notify) window.notify.error('Error', 'Action failed. Please try again.')
    } finally {
        isDeleting.value = false
        itemToDelete.value = null
    }
}

const openCategoryModal = () => {
    showCategoryModal.value = true
}

const createCategory = async () => {
    try {
        await axios.post('/api/support/admin/kb/categories', newCategory.value)
        if (window.notify) window.notify.success('Category Added', `"${newCategory.value.name}" is now available.`)
        newCategory.value = { name: '', description: '', is_public: true }
        await fetchKbData()
    } catch (error) {
        console.error('Failed to create category:', error)
        if (window.notify) window.notify.error('Failed', 'Could not create category.')
    }
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric',
        year: 'numeric'
    })
}

onMounted(fetchKbData)
</script>

<style scoped>
.bg-agri-forest { background-color: #155724; }
.text-agri-forest { color: #155724; }
.border-agri-forest { border-color: #155724; }
.bg-agri-lush { background-color: #1e7e34; }
.bg-agri-earth { background-color: #facc15; }

.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 6px;
}
::-webkit-scrollbar-track {
    background: transparent;
}
::-webkit-scrollbar-thumb {
    background: #e5e7eb;
    border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
    background: #d1d5db;
}
</style>
