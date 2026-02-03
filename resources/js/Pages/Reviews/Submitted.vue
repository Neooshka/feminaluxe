<!-- resources/js/Pages/Reviews/Submitted.vue -->
<template>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <!-- Overlay -->
        <div
            class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
            @click="goToHome"
        />

        <!-- Modal Content -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
                <!-- Success Content -->
                <div class="text-center p-8 md:p-10">
                    <!-- Success Icon -->
                    <div class="mx-auto w-20 h-20 md:w-24 md:h-24 mb-6">
                        <div class="w-full h-full rounded-full bg-gradient-to-r from-green-400 to-emerald-500 flex items-center justify-center">
                            <svg class="w-10 h-10 md:w-12 md:h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Title -->
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3">
                        Спасибо за отзыв!
                    </h2>

                    <!-- Message -->
                    <p class="text-gray-600 mb-6 md:mb-8">
                        {{ message }}
                    </p>

                    <!-- Review Info -->
                    <div v-if="review" class="bg-gray-50 rounded-xl p-4 md:p-5 mb-6 md:mb-8">
                        <div class="text-sm text-gray-500 mb-2">Ваш отзыв #{{ review.id }}</div>

                        <!-- Rating -->
                        <div class="flex items-center justify-center mb-3">
                            <div class="flex">
                                <svg
                                    v-for="star in 5"
                                    :key="star"
                                    :class="[
                                        'w-5 h-5 md:w-6 md:h-6',
                                        star <= review.rating ? 'text-yellow-400 fill-current' : 'text-gray-300'
                                    ]"
                                    viewBox="0 0 20 20"
                                >
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </div>
                            <span class="ml-2 font-bold text-gray-900">
                                {{ review.rating }}.0
                            </span>
                        </div>

                        <!-- Service Name -->
                        <div v-if="review.service" class="text-center">
                            <div class="text-sm text-gray-500 mb-1">Услуга</div>
                            <div class="font-medium text-gray-900 truncate">
                                {{ review.service.name }}
                            </div>
                        </div>
                    </div>

                    <!-- Additional Info -->
                    <div class="space-y-3 mb-6 md:mb-8">
                        <div class="flex items-center justify-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Модерация занимает до 24 часов</span>
                        </div>
                        <div class="flex items-center justify-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span>Мы проверяем все отзывы вручную</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="space-y-3">
                        <!-- Main Button - Back to Site -->
                        <button
                            @click="goToHome"
                            class="w-full bg-gradient-to-r from-rose-500 via-pink-500 to-purple-500 text-white font-semibold py-3 px-6 rounded-lg hover:shadow-xl hover:shadow-primary/25 transition-all duration-300"
                        >
                            Вернуться на сайт
                        </button>

                        <!-- Secondary Button - Share -->
                        <button
                            v-if="canShare"
                            @click="shareReview"
                            class="w-full border-2 border-gray-300 text-gray-700 font-semibold py-3 px-6 rounded-lg hover:bg-gray-50 transition-all duration-300 flex items-center justify-center"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                            </svg>
                            Поделиться
                        </button>

                        <!-- View Reviews Link -->
                        <button
                            @click="goToReviews"
                            class="w-full text-primary hover:text-primary/80 font-medium py-2 transition-colors duration-300"
                        >
                            Посмотреть другие отзывы →
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3'; // ← импортируем router

const props = defineProps({
    review: {
        type: Object,
        default: null
    },
    message: {
        type: String,
        default: 'Спасибо за ваш отзыв! Он будет опубликован после проверки модератором.'
    }
});

// Проверяем поддержку Web Share API
const canShare = computed(() => {
    return typeof navigator !== 'undefined' && navigator.share;
});

// На главную
const goToHome = () => {
    router.visit('/'); // ← используем router.visit()
};

// Перейти к странице отзывов
const goToReviews = () => {
    router.visit(route('reviews.index')); // ← используем router.visit()
};

// Поделиться отзывом
const shareReview = async () => {
    if (canShare.value) {
        try {
            await navigator.share({
                title: 'Мой отзыв о Femina Luxe',
                text: `Я оставил отзыв о перманентном макияже в Femina Luxe!`,
                url: window.location.origin + route('reviews.index'),
            });
        } catch (error) {
            console.log('Поделиться отменено', error);
        }
    }
};

// Автоматический редирект через 10 секунд
onMounted(() => {
    setTimeout(() => {
        goToHome();
    }, 10000); // 10 секунд
});
</script>

<style scoped>
/* Анимация появления */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.fixed.inset-0 {
    animation: fadeIn 0.3s ease-out;
}

/* Анимация иконки успеха */
@keyframes checkmark {
    0% {
        stroke-dashoffset: 50;
    }
    100% {
        stroke-dashoffset: 0;
    }
}

svg path {
    stroke-dasharray: 50;
    stroke-dashoffset: 0;
    animation: checkmark 0.5s ease-in-out 0.3s forwards;
}
</style>
