<!-- resources/js/Components/Common/Pagination.vue -->
<template>
    <nav v-if="links.length > 3" class="flex justify-center">
        <ul class="flex flex-wrap items-center gap-2">
            <li v-for="(link, index) in links" :key="index">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    :class="[
                        'px-4 py-2 rounded-lg font-medium transition whitespace-nowrap',
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