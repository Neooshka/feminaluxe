<template>
    <Head title="Блог о красоте | Feminaluxe" />
    
    <AppLayout>
        <!-- Hero Section -->
        <section class="relative py-20 bg-gradient-to-r from-purple-50 to-pink-50 overflow-hidden">
            <div class="absolute inset-0 bg-[url('/storage/img/blog/pattern.svg')] opacity-5"></div>
            <div class="container mx-auto px-4 relative">
                <div class="max-w-3xl mx-auto text-center">
                    <h1 class="text-5xl md:text-6xl font-serif font-bold text-gray-900 mb-6">
                        Блог о Красоте
                    </h1>
                    <p class="text-xl text-gray-700 mb-8">
                        Полезные статьи о перманентном макияже, уходе за кожей и трендах 2025 года
                    </p>
                    
                    <!-- Search Bar -->
                    <div class="max-w-md mx-auto mb-12">
                        <div class="relative">
                            <input 
                                type="text" 
                                v-model="searchQuery"
                                placeholder="Поиск статей..."
                                class="w-full px-6 py-4 pl-12 bg-white rounded-full shadow-lg border border-gray-200 focus:ring-2 focus:ring-pink-300 focus:border-pink-400 outline-none"
                            />
                            <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <div class="container mx-auto px-4 py-12">
            <div class="grid lg:grid-cols-4 gap-8">
                <!-- Sidebar with Tags -->
                <aside class="lg:col-span-1">
                    <div class="sticky top-24 space-y-8">
                        <!-- Popular Tags -->
                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-pink-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                                </svg>
                                Популярные теги
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                <Link 
                                    v-for="tag in popularTags"
                                    :key="tag.slug"
                                    :href="route('blog.index', { tag: tag.slug })"
                                    :class="[
                                        'px-3 py-1.5 rounded-full text-sm font-medium transition',
                                        filters.tag === tag.slug
                                            ? 'bg-pink-500 text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                    ]"
                                >
                                    {{ tag.name }}
                                    <span class="ml-1 text-xs opacity-75">({{ tag.count }})</span>
                                </Link>
                            </div>
                            
                            <button 
                                v-if="filters.tag"
                                @click="clearTagFilter"
                                class="mt-4 w-full px-4 py-2 text-sm text-pink-600 hover:text-pink-700 font-medium border border-pink-200 rounded-lg hover:bg-pink-50 transition"
                            >
                                ✕ Очистить фильтр
                            </button>
                        </div>

                        <!-- Newsletter -->
                        <div class="bg-gradient-to-r from-pink-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
                            <h3 class="text-xl font-bold mb-3">Подпишитесь на обновления</h3>
                            <p class="text-white/90 mb-4 text-sm">
                                Получайте новые статьи первыми
                            </p>
                            <form @submit.prevent="subscribe" class="space-y-3">
                                <input 
                                    type="email" 
                                    v-model="email"
                                    placeholder="Ваш email" 
                                    required
                                    class="w-full px-4 py-2 rounded-lg bg-white/20 placeholder-white/70 border border-white/30 text-white focus:outline-none focus:ring-2 focus:ring-white"
                                />
                                <button 
                                    type="submit"
                                    class="w-full px-4 py-2 bg-white text-pink-600 font-bold rounded-lg hover:bg-gray-100 transition"
                                >
                                    Подписаться
                                </button>
                            </form>
                        </div>
                    </div>
                </aside>

                <!-- Articles Grid -->
                <main class="lg:col-span-3">
                    <div v-if="posts.data.length === 0" class="text-center py-20">
                        <svg class="w-24 h-24 mx-auto text-gray-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                        </svg>
                        <h3 class="text-2xl font-bold text-gray-700 mb-4">
                            Статьи не найдены
                        </h3>
                        <p class="text-gray-600 mb-6">
                            Попробуйте другой тег или загляните позже
                        </p>
                        <button 
                            @click="clearTagFilter"
                            class="px-6 py-3 bg-pink-500 text-white font-medium rounded-lg hover:bg-pink-600 transition"
                        >
                            Показать все статьи
                        </button>
                    </div>

                    <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <PostCard 
                            v-for="post in posts.data" 
                            :key="post.id"
                            :post="post"
                        />
                    </div>

                    <!-- Pagination -->
                    <Pagination 
                        v-if="posts.links.length > 3" 
                        :links="posts.links"
                        class="mt-12"
                    />
                </main>
            </div>
        </div>

        <!-- CTA Section -->
        <CTASection :average-rating="averageRating"  :count-posts="countPosts" />
    </AppLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import PostCard from './Components/PostCard.vue';
import Pagination from '@/Components/Common/Pagination.vue';
import CTASection from './Components/CTASection.vue';

const props = defineProps({
    posts: Object,
    popularTags: Array,
    filters: Object,
    countPosts: {
      type: Number,
      default: 100
    },
    averageRating: {
      type: Number,
      default: 4.9
    },
});

const searchQuery = ref('');
const email = ref('');

const clearTagFilter = () => {
    router.get(route('blog.index'), {}, {
        preserveState: true,
        preserveScroll: true,
    });
};

const subscribe = () => {
    // Логика подписки
    console.log('Subscribe:', email.value);
    email.value = '';
};
</script>