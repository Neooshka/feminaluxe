<!-- resources/js/Pages/Portfolio/Components/PortfolioCard.vue -->
<template>
    <div class="group relative bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
        <!-- Before/After Comparison -->
        <div class="relative h-64 overflow-hidden">
            <img 
                :src="portfolio.before_url" 
                :alt="portfolio.title + ' - До'"
                class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500 group-hover:opacity-0"
                loading="lazy"
            />
            <img 
                :src="portfolio.after_url" 
                :alt="portfolio.title + ' - После'"
                class="absolute inset-0 w-full h-full object-cover opacity-0 group-hover:opacity-100 transition-opacity duration-500"
                loading="lazy"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            
            <!-- Service Badge -->
            <div class="absolute top-4 left-4 right-4 bottom-4">
                <span class="px-3 py-1 bg-pink-500 text-white text-sm font-medium rounded-full line-clamp-1">
                    {{ portfolio.service_name }}
                </span>
            </div>
            
            <!-- Quick View Button -->
            <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
                <button 
                    @click="quickView"
                    class="p-2 bg-white/90 backdrop-blur-sm rounded-full hover:bg-white transition"
                >
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Portfolio Info -->
        <div class="p-6">
            <h3 class="text-xl font-serif font-bold text-gray-900 mb-2 line-clamp-1">
                {{ portfolio.title }}
            </h3>
            <p class="text-gray-600 mb-4 line-clamp-2">
                {{ portfolio.description }}
            </p>
            
            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    <span class="text-sm text-gray-600">
                        {{ portfolio.views_count }} просмотров
                    </span>
                </div>
                <Link 
                    :href="route('portfolio.show', portfolio.slug)"
                    class="inline-flex items-center gap-2 text-pink-600 hover:text-pink-700 font-medium"
                >
                    Подробнее
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    portfolio: Object,
    config: Object
});

const quickView = () => {
    // Можно добавить модальное окно быстрого просмотра
    console.log('Quick view:', props.portfolio.id);
};
</script>

<style scoped>
.line-clamp-1 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
}

.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}
</style>