<!-- resources/js/Pages/Portfolio/Components/PortfolioCardMobile.vue -->
<template>
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <!-- Before/After Swipe -->
        <div class="relative h-56">
            <div class="flex w-full h-full">
                <div class="w-1/2 relative">
                    <img 
                        :src="portfolio.before_url" 
                        :alt="portfolio.title + ' - До'"
                        class="absolute inset-0 w-full h-full object-cover"
                        loading="lazy"
                    />
                    <div class="absolute bottom-2 left-2 px-2 py-1 bg-black/70 text-white text-xs rounded">
                        До
                    </div>
                </div>
                <div class="w-1/2 relative">
                    <img 
                        :src="portfolio.after_url" 
                        :alt="portfolio.title + ' - После'"
                        class="absolute inset-0 w-full h-full object-cover"
                        loading="lazy"
                    />
                    <div class="absolute bottom-2 right-2 px-2 py-1 bg-pink-600 text-white text-xs rounded">
                        После
                    </div>
                </div>
            </div>
        </div>

        <div class="p-4">
            <div class="flex items-center justify-between mb-2 gap-2">
                <span class="px-3 py-1 bg-pink-100 text-pink-700 text-sm font-medium rounded-full line-clamp-1">
                    {{ portfolio.service_name }}
                </span>
                <span class="text-sm text-gray-500">
                    {{ portfolio.published_at }}
                </span>
            </div>
            
            <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-1">
                {{ portfolio.title }}
            </h3>
            
            <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                {{ portfolio.description }}
            </p>
            
            <Link 
                :href="route('portfolio.show', portfolio.slug)"
                class="block w-full text-center mt-4 px-4 py-2 bg-pink-500 text-white font-medium rounded-lg hover:bg-pink-600 transition"
            >
                Посмотреть детали
            </Link>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    portfolio: Object
});
</script>