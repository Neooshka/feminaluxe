<!-- resources/js/Pages/Portfolio/Components/EmptyState.vue -->
<template>
    <div class="text-center py-20">
        <div class="text-gray-400 mb-6">
            <svg class="w-24 h-24 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>
        <h3 class="text-2xl font-bold text-gray-700 mb-4">
            <span v-if="selectedService">Работ по этой услуге пока нет</span>
            <span v-else>Работ пока нет</span>
        </h3>
        <p class="text-gray-600 mb-8 max-w-md mx-auto">
            <span v-if="selectedService">
                Мы скоро добавим примеры работ по этой услуге. А пока вы можете посмотреть другие наши работы.
            </span>
            <span v-else>
                В этом разделе скоро появятся фотографии наших работ. А пока вы можете ознакомиться с нашими услугами.
            </span>
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <Link 
                v-if="selectedService"
                :href="route('portfolio')"
                class="px-6 py-3 bg-pink-500 text-white font-medium rounded-lg hover:bg-pink-600 transition"
            >
                Показать все работы
            </Link>
            <Link 
                :href="route('services.index')"
                class="px-6 py-3 bg-white border-2 border-pink-500 text-pink-600 font-medium rounded-lg hover:bg-pink-50 transition"
            >
                Посмотреть услуги
            </Link>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    selectedService: String
});
</script>