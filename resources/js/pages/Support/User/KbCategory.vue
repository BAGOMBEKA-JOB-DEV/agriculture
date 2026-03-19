<template>
    <div class="min-h-screen bg-gray-50/50 pb-20">
        <div class="bg-white border-b border-gray-100 pt-16 pb-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center space-x-4 mb-6">
                    <router-link to="/support" class="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-agri-forest flex items-center transition-colors">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                        Back to Support
                    </router-link>
                </div>
                
                <div class="flex items-center space-x-6">
                    <div class="w-16 h-16 bg-agri-forest/5 rounded-2xl flex items-center justify-center text-agri-forest border border-agri-forest/10">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath(category?.icon || 'svg-book')"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-black text-gray-900 tracking-tight">{{ category?.name }}</h1>
                        <p class="text-sm font-medium text-gray-500 max-w-2xl mt-1">{{ category?.description || 'Browse all articles and resources for ' + category?.name }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
            <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div v-for="i in 4" :key="i" class="bg-white h-48 rounded-3xl border border-gray-100 animate-pulse"></div>
            </div>

            <div v-else-if="articles.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div v-for="article in articles" :key="article.id" @click="$router.push(`/support/kb/articles/${article.id}`)"
                    class="bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm hover:shadow-md hover:border-agri-forest/20 transition-all cursor-pointer group">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg font-black text-gray-900 group-hover:text-agri-forest transition-colors leading-tight">{{ article.title }}</h3>
                        <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-300 group-hover:bg-agri-forest/10 group-hover:text-agri-forest transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 leading-relaxed mb-6 line-clamp-3">{{ article.excerpt }}</p>
                    <div class="flex items-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                        <span class="flex items-center"><svg class="w-3 h-3 mr-1.5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>{{ formatDate(article.updated_at) }}</span>
                    </div>
                </div>
            </div>

            <div v-else class="text-center py-20">
                <p class="text-gray-400 font-bold uppercase tracking-widest">No articles found in this category.</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const category = ref(null)
const articles = ref([])
const loading = ref(true)

const iconPaths = {
    'svg-book': 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
    'svg-users': 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
    'svg-credit-card': 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
    'svg-box': 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
    'svg-settings': 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
    'default': 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
}

const getIconPath = (iconName) => iconPaths[iconName] || iconPaths['default']

const fetchCategoryData = async () => {
    try {
        const [catResp, articleResp] = await Promise.all([
            axios.get(`/api/support/kb/categories`),
            axios.get(`/api/support/kb/articles?category_id=${route.params.id}`)
        ])
        
        // Find the specific category from list (since there might not be a direct "show" endpoint in provided controller)
        category.value = catResp.data.find(c => c.id == route.params.id) || catResp.data.flatMap(c => c.children || []).find(c => c.id == route.params.id)
        
        articles.value = articleResp.data.data || articleResp.data
    } catch (error) {
        console.error('Failed to fetch category data:', error)
    } finally {
        loading.value = false
    }
}

const formatDate = (date) => new Date(date).toLocaleDateString()

onMounted(fetchCategoryData)
</script>

<style scoped>
.text-agri-forest { color: #155724; }
.bg-agri-forest { background-color: #155724; }
.line-clamp-3 {
    display: -webkit-box;
    line-clamp: 3;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
