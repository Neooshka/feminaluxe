<!-- resources/js/Pages/Blog/Components/RelatedArticles.vue -->
<template>
    <section v-if="articles && articles.length" class="mt-16">
        <h2 class="text-3xl font-serif font-bold text-gray-900 mb-8 text-center">
            Похожие статьи
        </h2>
        <div class="grid md:grid-cols-3 gap-6">
            <div 
                v-for="article in articles"
                :key="article.id"
                class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow"
            >
                <img 
                    v-if="article.featured_image"
                    :src="article.featured_image"
                    :alt="article.title"
                    class="w-full h-48 object-contain"
                />
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">
                        {{ article.title }}
                    </h3>
                    <p 
                        class="text-gray-600 text-sm mb-4 line-clamp-3"
                        v-html="article.excerpt"
                    ></p>
                    <Link 
                        :href="route('blog.show', article.slug)"
                        class="text-pink-600 hover:text-pink-700 font-medium text-sm flex items-center gap-1"
                    >
                        Читать статью
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </Link>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    articles: Array
});
</script>