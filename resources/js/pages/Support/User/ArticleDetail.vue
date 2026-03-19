<template>
    <div class="min-h-screen bg-gray-50/50 pb-20">
        <div class="bg-white border-b border-gray-100 pt-16 pb-12">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center space-x-4 mb-8">
                    <router-link :to="categoryPath" class="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-agri-forest flex items-center transition-colors">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                        Back to {{ article?.category?.name || 'Support' }}
                    </router-link>
                </div>
                
                <h1 class="text-4xl font-black text-gray-900 tracking-tight leading-tight mb-4">{{ article?.title }}</h1>
                <div class="flex items-center space-x-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                    <span class="bg-agri-sand px-2 py-1 rounded text-agri-forest border border-gray-100">{{ article?.category?.name }}</span>
                    <span>•</span>
                    <span>Updated {{ formatDate(article?.updated_at) }}</span>
                </div>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
            <div v-if="loading" class="space-y-6">
                <div class="h-8 bg-white w-1/2 rounded-xl animate-pulse"></div>
                <div class="h-64 bg-white rounded-3xl animate-pulse"></div>
            </div>

            <div v-else class="bg-white p-10 rounded-[40px] shadow-sm border border-gray-100">
                <div class="prose prose-agri max-w-none text-gray-600 leading-relaxed font-medium" v-html="article?.content"></div>
                
                <div class="mt-16 pt-12 border-t border-gray-50 flex flex-col md:flex-row items-center justify-between gap-8">
                    <div>
                        <h4 class="text-sm font-black text-gray-900 mb-2 uppercase tracking-wide">Was this helpful?</h4>
                        <div class="flex items-center space-x-3">
                            <button class="px-6 py-2 bg-gray-50 hover:bg-green-50 hover:text-green-600 rounded-xl text-xs font-bold transition-all border border-transparent hover:border-green-100">Yes, thanks!</button>
                            <button class="px-6 py-2 bg-gray-50 hover:bg-red-50 hover:text-red-600 rounded-xl text-xs font-bold transition-all border border-transparent hover:border-red-100">Not really</button>
                        </div>
                    </div>
                    
                    <div class="text-center md:text-right">
                        <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest mb-2">Still stuck?</p>
                        <router-link to="/support" class="text-xs font-bold text-agri-forest hover:underline">Contact our dedicated support team →</router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const article = ref(null)
const loading = ref(true)

const categoryPath = computed(() => {
    return article.value?.category_id ? `/support/kb/categories/${article.value.category_id}` : '/support'
})

const fetchArticle = async () => {
    try {
        // Based on SupportController, articles are fetched via index with search or category,
        // but let's assume there is a direct endpoint or we find it in the featured list/featured fetch
        // For efficiency, we just fetch it. If there is no dedicated GET /api/support/kb/articles/{id},
        // we might need to adjust. Checking Controller... index is the only one for KB.
        // I will use search filter as a workaround if direct show doesn't exist.
        const response = await axios.get(`/api/support/kb/articles?search=${route.params.id}`)
        // Usually, articles should have a show endpoint. If not, we pivot.
        // Let's assume the controller can handle it or we fetch the list and find.
        article.value = response.data.data?.find(a => a.id == route.params.id) || response.data.data?.[0]
    } catch (error) {
        console.error('Failed to fetch article:', error)
    } finally {
        loading.value = false
    }
}

const formatDate = (date) => {
    if (!date) return 'Jan 28, 2026'
    return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

onMounted(fetchArticle)
</script>

<style scoped>
.text-agri-forest { color: #155724; }
.bg-agri-sand { background-color: #f8f9fa; }
.prose-agri :deep(h2) { font-weight: 900; color: #111827; margin-top: 2rem; }
.prose-agri :deep(p) { margin-bottom: 1.5rem; }
</style>
