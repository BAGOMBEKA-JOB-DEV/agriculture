<template>
    <div class="min-h-screen bg-gray-50/50 pb-20">
        <div class="bg-white border-b border-gray-100 pt-16 pb-12 text-center">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-center space-x-2 text-agri-forest/60 mb-4">
                    <router-link to="/support" class="text-[10px] font-black uppercase tracking-widest hover:text-agri-forest transition-colors">Support Center</router-link>
                    <span class="opacity-30">/</span>
                    <span class="text-[10px] font-black uppercase tracking-widest text-agri-forest">Knowledge Base</span>
                </div>
                <h1 class="text-4xl font-black text-gray-900 tracking-tight mb-4">All Articles</h1>
                <p class="text-gray-500 font-medium">Explore our comprehensive guides and resources for everything agriculture.</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar: Categories -->
            <div class="lg:col-span-1 space-y-4">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-4">Categories</h3>
                <div class="space-y-1">
                    <button v-for="cat in categories" :key="cat.id"
                        @click="selectedCategory = cat.id"
                        :class="selectedCategory === cat.id ? 'bg-agri-forest text-white shadow-lg shadow-agri-forest/20' : 'text-gray-600 hover:bg-gray-100'"
                        class="w-full text-left px-5 py-3 rounded-2xl text-xs font-bold transition-all duration-300 flex items-center justify-between group">
                        <span>{{ cat.name }}</span>
                        <span class="text-[10px] opacity-60">{{ cat.articles_count }}</span>
                    </button>
                    <button @click="selectedCategory = null"
                        :class="selectedCategory === null ? 'bg-agri-forest text-white shadow-lg shadow-agri-forest/20' : 'text-gray-600 hover:bg-gray-100'"
                        class="w-full text-left px-5 py-3 rounded-2xl text-xs font-bold transition-all duration-300">
                        All Articles
                    </button>
                </div>
            </div>

            <!-- Content: Articles -->
            <div class="lg:col-span-3">
                <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div v-for="i in 6" :key="i" class="h-40 bg-white rounded-3xl border border-gray-100 animate-pulse"></div>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div v-for="article in filteredArticles" :key="article.id"
                        @click="$router.push(`/support/kb/articles/${article.id}`)"
                        class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all cursor-pointer group">
                        <div class="flex items-center space-x-3 mb-3">
                            <span class="px-2 py-0.5 bg-agri-forest/5 text-agri-forest rounded text-[9px] font-black uppercase tracking-wider">{{ article.category?.name }}</span>
                        </div>
                        <h4 class="text-sm font-black text-gray-900 group-hover:text-agri-forest transition-colors mb-2 line-clamp-2">
                            {{ article.title }}
                        </h4>
                        <p class="text-[11px] text-gray-500 line-clamp-2 leading-relaxed mb-4">{{ article.excerpt }}</p>
                        <div class="flex items-center justify-between text-[10px] font-bold text-gray-400">
                            <span>{{ formatDate(article.updated_at) }}</span>
                            <div class="flex items-center">
                                <svg class="w-3 h-3 mr-1 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                {{ article.views_count || 0 }}
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="!loading && filteredArticles.length === 0" class="text-center py-20 bg-white rounded-3xl border border-gray-100 border-dashed">
                    <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">No articles found in this selection.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const articles = ref([])
const categories = ref([])
const loading = ref(true)
const selectedCategory = ref(null)

const filteredArticles = computed(() => {
    if (!selectedCategory.value) return articles.value
    return articles.value.filter(a => a.category_id === selectedCategory.value)
})

const fetchData = async () => {
    try {
        const [artResp, catResp] = await Promise.all([
            axios.get('/api/support/kb/articles?limit=100'),
            axios.get('/api/support/kb/categories')
        ])
        articles.value = artResp.data.data || artResp.data
        categories.value = catResp.data
    } catch (error) {
        console.error('Failed to fetch KB data:', error)
    } finally {
        loading.value = false
    }
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

onMounted(fetchData)
</script>

<style scoped>
.text-agri-forest { color: #155724; }
.bg-agri-forest { background-color: #155724; }
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
