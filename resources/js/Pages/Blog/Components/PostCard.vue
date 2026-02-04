<!-- resources/js/Pages/Blog/Components/PostCard.vue -->
<template>
    <article class="group relative bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
        <!-- Featured Image -->
        <div class="relative h-48 overflow-hidden">
            <img
                :src="post.featured_image"
                :alt="post.title"
                class="absolute inset-0 w-full h-full object-contain group-hover:scale-110 transition-transform duration-500"
                loading="lazy"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

            <!-- Reading Time -->
            <div class="absolute top-4 right-4">
                <span class="px-3 py-1 bg-white/90 backdrop-blur-sm text-gray-700 text-xs font-bold rounded-full flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                    {{ post.reading_time }} мин
                </span>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <!-- Date -->
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                {{ post.published_at_full }}
            </div>

            <!-- Title -->
            <h3 class="text-xl font-serif font-bold text-gray-900 mb-3 line-clamp-2">
                {{ post.title }}
            </h3>

            <!-- Excerpt -->
            <p class="text-gray-600 mb-4 line-clamp-3">
                {{ post.excerpt }}
            </p>

            <!-- Tags -->
            <div v-if="post.tags && post.tags.length > 0" class="flex flex-wrap gap-2 mb-4">
                <span
                    v-for="tag in post.tags.slice(0, 2)"
                    :key="tag.slug"
                    class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded hover:bg-gray-200 transition"
                >
                    {{ tag.name }}
                </span>
                <span v-if="post.tags.length > 2" class="px-2 py-1 text-gray-400 text-xs">
                    +{{ post.tags.length - 2 }}
                </span>
            </div>

            <!-- Read More -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
                <span class="text-sm text-gray-600">
                          {{ post.views_count }} просмотров
                      </span>
              </div>
              <Link
                  :href="route('blog.show', post.slug)"
                  class="inline-flex items-center gap-2 text-pink-600 hover:text-pink-700 font-medium"
              >
                Читать статью
                <svg class="w-4 h-4 group-hover/read:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
              </Link>
            </div>
        </div>
    </article>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    post: Object
});
</script>

<style scoped>
.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.line-clamp-3 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
}
</style>
