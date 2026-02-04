<!-- resources/js/Components/Common/Pagination.vue -->
<template>
    <nav v-if="links.length > 3" class="flex justify-center" aria-label="Навигация по страницам">
        <ul class="flex flex-wrap items-center gap-2">
            <!-- Previous Button -->
            <li v-if="links[0].url">
                <Link
                    :href="links[0].url"
                    class="px-4 py-2 rounded-lg font-medium transition whitespace-nowrap bg-white text-gray-700 hover:bg-gray-100 border border-gray-200 flex items-center gap-2"
                    preserve-scroll
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Назад
                </Link>
            </li>
            <li v-else>
                <span class="px-4 py-2 text-gray-400 cursor-not-allowed flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Назад
                </span>
            </li>

            <!-- Page Numbers -->
            <li v-for="(link, index) in links.slice(1, -1)" :key="index">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    :class="[
                        'px-4 py-2 rounded-lg font-medium transition whitespace-nowrap min-w-[40px] text-center',
                        link.active 
                            ? 'bg-pink-500 text-white' 
                            : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'
                    ]"
                    v-html="link.label"
                    preserve-scroll
                />
                <span 
                    v-else
                    class="px-4 py-2 text-gray-400"
                    v-html="link.label"
                />
            </li>

            <!-- Next Button -->
            <li v-if="links[links.length - 1].url">
                <Link
                    :href="links[links.length - 1].url"
                    class="px-4 py-2 rounded-lg font-medium transition whitespace-nowrap bg-white text-gray-700 hover:bg-gray-100 border border-gray-200 flex items-center gap-2"
                    preserve-scroll
                >
                    Вперед
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </Link>
            </li>
            <li v-else>
                <span class="px-4 py-2 text-gray-400 cursor-not-allowed flex items-center gap-2">
                    Вперед
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </span>
            </li>
        </ul>
    </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: {
        type: Array,
        default: () => []
    }
});
</script>