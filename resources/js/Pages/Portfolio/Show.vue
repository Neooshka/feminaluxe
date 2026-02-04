<!-- resources/js/Pages/Portfolio/Show.vue -->
<template>
    <Head :title="portfolio.seo_title || portfolio.title" />
    
    <AppLayout>
        <!-- Hero Section with Before/After -->
        <section class="relative py-12 bg-gradient-to-b from-gray-50 to-white">
            <div class="container mx-auto px-4">
                <!-- Breadcrumbs -->
                <nav class="mb-8">
                    <ol class="flex items-center gap-2 text-sm text-gray-600">
                        <li>
                            <Link :href="route('home')" class="hover:text-pink-600 transition">
                                Главная
                            </Link>
                        </li>
                        <li class="text-gray-400">/</li>
                        <li>
                            <Link :href="route('portfolio')" class="hover:text-pink-600 transition">
                                Портфолио
                            </Link>
                        </li>
                        <li class="text-gray-400">/</li>
                        <li class="font-medium text-gray-900 truncate">
                            {{ portfolio.title }}
                        </li>
                    </ol>
                </nav>

                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <!-- Before/After Comparison -->
                    <div class="relative">
                        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                            <div class="relative h-[500px]">
                                <!-- Before Image -->
                                <div 
                                    class="absolute inset-0 transition-all duration-500"
                                    :style="{ clipPath: `inset(0 ${100 - sliderPosition}% 0 0)` }"
                                >
                                    <img 
                                        :src="portfolio.before_url" 
                                        :alt="portfolio.title + ' - До процедуры'"
                                        class="w-full h-full object-cover"
                                        loading="lazy"
                                    />
                                    <div class="absolute top-4 left-4 px-4 py-2 bg-black/70 text-white text-lg font-bold rounded-full">
                                        До
                                    </div>
                                </div>
                                
                                <!-- After Image -->
                                <div 
                                    class="absolute inset-0 transition-all duration-500"
                                    :style="{ clipPath: `inset(0 0 0 ${sliderPosition}%)` }"
                                >
                                    <img 
                                        :src="portfolio.after_url" 
                                        :alt="portfolio.title + ' - После процедуры'"
                                        class="w-full h-full object-cover"
                                        loading="lazy"
                                    />
                                    <div class="absolute top-4 right-4 px-4 py-2 bg-pink-600 text-white text-lg font-bold rounded-full">
                                        После
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Slider Control -->
                            <div class="relative px-8 py-6 bg-gray-900">
                                <input 
                                    type="range" 
                                    v-model="sliderPosition"
                                    min="0" 
                                    max="100" 
                                    class="w-full h-2 bg-gray-700 rounded-lg appearance-none cursor-pointer slider-thumb"
                                />
                                <div class="flex justify-between mt-2 text-sm text-gray-400">
                                    <span>Передвиньте ползунок</span>
                                    <span>{{ sliderPosition }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Portfolio Details -->
                    <div class="space-y-8">
                        <!-- Service Badge -->
                        <div>
                            <Link 
                                :href="route('services.show', portfolio.service.slug)"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-pink-100 text-pink-700 font-bold rounded-full hover:bg-pink-200 transition"
                            >
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path>
                                </svg>
                                {{ portfolio.service.name }}
                            </Link>
                        </div>

                        <!-- Title -->
                        <h1 class="text-4xl md:text-5xl font-serif font-bold text-gray-900 leading-tight">
                            {{ portfolio.title }}
                        </h1>

                        <!-- Stats -->
                        <div class="flex flex-wrap gap-6 py-4 border-y border-gray-200">
                            <div class="flex items-center gap-2">
                                <svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <div>
                                    <div class="font-bold text-gray-900">{{ portfolio.views_count }}</div>
                                    <div class="text-sm text-gray-600">просмотров</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <div>
                                    <div class="font-bold text-gray-900">{{ portfolio.published_at }}</div>
                                    <div class="text-sm text-gray-600">дата публикации</div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="prose prose-lg max-w-none">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Описание работы:</h3>
                            <div class="text-gray-700 leading-relaxed space-y-4" v-html="portfolio.description"></div>
                        </div>

                        <!-- CTA Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 pt-6">
                            <Link 
                                :href="route('bookings.create', { service: portfolio.service.id })"
                                class="flex-1 px-8 py-4 bg-gradient-to-r from-pink-500 to-purple-600 text-white font-bold text-lg rounded-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 text-center"
                            >
                                🗓️ Записаться на эту процедуру
                            </Link>
                            <Link 
                                :href="route('services.show', portfolio.service.slug)"
                                class="flex-1 px-8 py-4 bg-white border-2 border-pink-500 text-pink-600 font-bold text-lg rounded-xl hover:bg-pink-50 transition text-center"
                            >
                                ℹ️ Подробнее об услуге
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Gallery -->
        <section v-if="portfolio.gallery && portfolio.gallery.length > 0" class="py-20 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-serif font-bold text-gray-900 mb-4">
                        Дополнительные фото
                    </h2>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                        Подробные фотографии процесса и результата
                    </p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <div 
                        v-for="(image, index) in portfolio.gallery" 
                        :key="index"
                        class="relative group overflow-hidden rounded-xl shadow-lg bg-white"
                        @click="openLightbox(index)"
                    >
                        <img 
                            :src="image.thumb" 
                            :alt="`Фото ${index + 1} - ${portfolio.title}`"
                            class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-300"
                            loading="lazy"
                        />
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Related Portfolio -->
        <section v-if="related && related.length > 0" class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-serif font-bold text-gray-900 mb-4">
                        Похожие работы
                    </h2>
                    <p class="text-xl text-gray-600">
                        Другие примеры наших работ в этой категории
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div 
                        v-for="item in related" 
                        :key="item.id"
                        class="group relative bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300"
                    >
                        <div class="relative h-48 overflow-hidden">
                            <img 
                                :src="item.before_url" 
                                :alt="item.title"
                                class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                loading="lazy"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                            <div class="absolute top-3 left-3">
                                <span class="px-3 py-1 bg-pink-500 text-white text-xs font-medium rounded-full">
                                    {{ item.service_name }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="p-4">
                            <h3 class="font-bold text-gray-900 mb-2 line-clamp-1">
                                {{ item.title }}
                            </h3>
                            <Link 
                                :href="route('portfolio.show', item.slug)"
                                class="inline-flex items-center gap-1 text-pink-600 hover:text-pink-700 font-medium text-sm"
                            >
                                Смотреть работу
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 bg-gradient-to-r from-pink-500 to-purple-600">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-4xl md:text-5xl font-serif font-bold text-white mb-6">
                    Вдохновились результатом?
                </h2>
                <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
                    Запишитесь на консультацию к мастеру и получите индивидуальный план процедуры
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a 
                        :href="`tel:${config.contact.phone.replace(/[^0-9+]/g, '')}`"
                        class="px-8 py-4 bg-white text-pink-600 font-bold text-lg rounded-xl hover:bg-gray-100 transition shadow-xl hover:shadow-2xl"
                    >
                        📞 Позвонить: {{ config.contact.phone }}
                    </a>
                    <Link 
                        :href="route('bookings.create')"
                        class="px-8 py-4 bg-transparent border-2 border-white text-white font-bold text-lg rounded-xl hover:bg-white/10 transition"
                    >
                        📝 Онлайн запись
                    </Link>
                </div>
            </div>
        </section>

        <!-- Lightbox Modal -->
        <div 
            v-if="showLightbox"
            class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-4"
            @click.self="closeLightbox"
        >
            <button 
                @click="closeLightbox"
                class="absolute top-4 right-4 text-white hover:text-gray-300 transition z-10"
            >
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            
            <button 
                v-if="portfolio.gallery.length > 1"
                @click="prevImage"
                class="absolute left-4 top-1/2 -translate-y-1/2 text-white hover:text-gray-300 transition z-10"
            >
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            
            <button 
                v-if="portfolio.gallery.length > 1"
                @click="nextImage"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-white hover:text-gray-300 transition z-10"
            >
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
            
            <div class="relative max-w-4xl max-h-[90vh]">
                <img 
                    :src="portfolio.gallery[currentImageIndex].url" 
                    :alt="`Фото ${currentImageIndex + 1} - ${portfolio.title}`"
                    class="max-w-full max-h-[90vh] object-contain"
                />
                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 text-white text-lg">
                    {{ currentImageIndex + 1 }} / {{ portfolio.gallery.length }}
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const { props: pageProps } = usePage();
const config = computed(() => pageProps.config);

const props = defineProps({
    portfolio: Object,
    related: Array,
});

// Before/After Slider
const sliderPosition = ref(50);

// Lightbox
const showLightbox = ref(false);
const currentImageIndex = ref(0);

const openLightbox = (index) => {
    currentImageIndex.value = index;
    showLightbox.value = true;
    document.body.style.overflow = 'hidden';
};

const closeLightbox = () => {
    showLightbox.value = false;
    document.body.style.overflow = '';
};

const prevImage = () => {
    currentImageIndex.value = currentImageIndex.value === 0 
        ? props.portfolio.gallery.length - 1 
        : currentImageIndex.value - 1;
};

const nextImage = () => {
    currentImageIndex.value = currentImageIndex.value === props.portfolio.gallery.length - 1 
        ? 0 
        : currentImageIndex.value + 1;
};

// Keyboard navigation for lightbox
const handleKeydown = (event) => {
    if (!showLightbox.value) return;
    
    switch(event.key) {
        case 'Escape':
            closeLightbox();
            break;
        case 'ArrowLeft':
            prevImage();
            break;
        case 'ArrowRight':
            nextImage();
            break;
    }
};

onMounted(() => {
    window.addEventListener('keydown', handleKeydown);
});

onBeforeUnmount(() => {
    window.removeEventListener('keydown', handleKeydown);
});
</script>

<style scoped>
.slider-thumb {
    -webkit-appearance: none;
}

.slider-thumb::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: white;
    cursor: pointer;
    border: 4px solid #ec4899;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23ec4899'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: center;
    background-size: 20px;
}

.slider-thumb::-moz-range-thumb {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: white;
    cursor: pointer;
    border: 4px solid #ec4899;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.line-clamp-1 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
}
</style>