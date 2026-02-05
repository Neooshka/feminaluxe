<!-- resources/js/Pages/Blog/Show.vue -->
<template>
    <Head :title="post.seo_title || post.title" />
    
    <AppLayout>
        <article class="max-w-4xl mx-auto px-4 py-12">
            <!-- Хлебные крошки -->
            <nav class="mb-8">
                <ol class="flex items-center gap-2 text-sm text-gray-600">
                    <li>
                        <Link :href="route('home')" class="hover:text-pink-600 transition">
                            Главная
                        </Link>
                    </li>
                    <li class="text-gray-400">/</li>
                    <li>
                        <Link :href="route('blog.index')" class="hover:text-pink-600 transition">
                            Блог
                        </Link>
                    </li>
                    <li class="text-gray-400">/</li>
                    <li class="font-medium text-gray-900 truncate">
                        {{ post.title }}
                    </li>
                </ol>
            </nav>
            
            <!-- Заголовок статьи -->
            <header class="mb-8">
                <div class="flex items-center gap-2 mb-4">
                    <span class="px-3 py-1 bg-pink-100 text-pink-700 text-sm font-medium rounded-full">
                        {{ post.category || 'Статья' }}
                    </span>
                    <time :datetime="post.published_at" class="text-gray-500">
                        {{ post.published_at_full }}
                    </time>
                    <span class="text-gray-500 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ post.reading_time }} мин
                    </span>
                </div>
                
                <h1 class="text-4xl md:text-5xl font-serif font-bold text-gray-900 mb-6">
                    {{ post.title }}
                </h1>
                
                <!-- Автор -->
                <div class="flex items-center gap-3 mb-8">
                    <img 
                        :src="post.author.avatar" 
                        :alt="post.author.name"
                        class="w-12 h-12 rounded-full object-cover"
                    />
                    <div>
                        <div class="font-bold text-gray-900">{{ post.author.name }}</div>
                        <div class="text-sm text-gray-600">{{ post.author.description }}</div>
                    </div>
                </div>
                
                <!-- Обложка -->
                <div v-if="post.featured_image" class="mb-8">
                    <img 
                        :src="post.featured_image" 
                        :alt="post.title"
                        class="w-full h-auto max-h-[500px] object-contain rounded-2xl shadow-lg"
                        loading="eager"
                    />
                    <p v-if="post.featured_caption" class="text-center text-sm text-gray-500 mt-2">
                        {{ post.featured_caption }}
                    </p>
                </div>
            </header>
            
            <!-- Контент статьи -->
            <div class="prose prose-lg max-w-none 
              prose-headings:font-serif 
              prose-p:text-justify
              prose-a:text-pink-600">
              <div v-html="post.content"></div>
            </div>
            
            <!-- Теги -->
            <div v-if="post.tags && post.tags.length" class="mt-12 pt-8 border-t border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Теги статьи:</h3>
                <div class="flex flex-wrap gap-2">
                    <Link 
                        v-for="tag in post.tags"
                        :key="tag.slug"
                        :href="route('blog.index', { tag: tag.slug })"
                        class="px-3 py-1 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-full text-sm transition"
                    >
                        {{ tag.name }}
                    </Link>
                </div>
            </div>
            
            <!-- Навигация по статьям -->
            <div class="mt-12 pt-8 border-t border-gray-200">
                <div class="grid md:grid-cols-2 gap-8">
                    <div v-if="previousPost">
                        <div class="text-sm text-gray-500 mb-2">← Предыдущая статья</div>
                        <Link 
                            :href="route('blog.show', previousPost.slug)"
                            class="text-lg font-bold text-gray-900 hover:text-pink-600 transition"
                        >
                            {{ previousPost.title }}
                        </Link>
                    </div>
                    <div v-if="nextPost" class="md:text-right">
                        <div class="text-sm text-gray-500 mb-2">Следующая статья →</div>
                        <Link 
                            :href="route('blog.show', nextPost.slug)"
                            class="text-lg font-bold text-gray-900 hover:text-pink-600 transition"
                        >
                            {{ nextPost.title }}
                        </Link>
                    </div>
                </div>
            </div>
            
            <!-- Похожие статьи -->
            <RelatedArticles :articles="related" />
        </article>
    </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import RelatedArticles from './Components/RelatedArticles.vue';

const props = defineProps({
    post: Object,
    related: Array,
    previousPost: Object,
    nextPost: Object,
});

const parsedContent = props.post.content;
</script>
