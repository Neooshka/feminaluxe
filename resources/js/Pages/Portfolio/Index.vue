<!-- resources/js/Pages/Portfolio/Index.vue -->
<template>
    <Head title="Портфолио работ | Feminaluxe" />
    
    <AppLayout>
        <!-- Hero Section -->
        <section class="relative py-20 bg-gradient-to-r from-pink-50 to-purple-50 overflow-hidden">
            <div class="absolute inset-0 bg-[url('/storage/img/portfolio/pattern.svg')] opacity-10"></div>
            <div class="container mx-auto px-4 relative">
                <div class="max-w-3xl mx-auto text-center">
                    <h1 class="text-5xl md:text-6xl font-serif font-bold text-gray-900 mb-6">
                        Наше Портфолио
                    </h1>
                    <p class="text-xl text-gray-700 mb-8">
                        Реальные результаты наших работ. Убедитесь в профессионализме мастеров студии Feminaluxe
                    </p>
                    <div class="flex flex-wrap justify-center gap-4 mb-12">
                        <span class="px-4 py-2 bg-white rounded-full shadow-sm text-gray-700">
                            ✅ Более 500+ работ
                        </span>
                        <span class="px-4 py-2 bg-white rounded-full shadow-sm text-gray-700">
                            ⭐ Рейтинг {{ averageRating }}/5
                        </span>
                        <span class="px-4 py-2 bg-white rounded-full shadow-sm text-gray-700">
                            🎨 {{ services.length }} видов процедур
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Filters -->
        <section class="py-8 bg-white border-b">
            <div class="container mx-auto px-4">
                <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                    <!-- Service Filter -->
                    <div class="flex-1 max-w-2xl">
                        <div class="flex items-center gap-4 mb-4">
                            <span class="text-gray-600 font-medium">Фильтр по услуге:</span>
                            <select 
                                v-model="selectedService"
                                @change="filterByService"
                                class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-300 focus:border-pink-400"
                            >
                                <option value="">Все услуги</option>
                                <option 
                                    v-for="service in services" 
                                    :key="service.id"
                                    :value="service.slug"
                                >
                                    {{ service.name }} 
                                    <span v-if="service.final_price">
                                        ({{ formatPrice(service.final_price) }})
                                    </span>
                                </option>
                            </select>
                        </div>
                        
                        <!-- Category Tabs -->
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="category in uniqueCategories"
                                :key="category"
                                @click="filterByCategory(category)"
                                :class="[
                                    'px-4 py-2 rounded-full text-sm font-medium transition',
                                    selectedCategory === category
                                        ? 'bg-pink-500 text-white'
                                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                ]"
                            >
                                {{ category }}
                            </button>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900">
                            {{ portfolios.data.length }} из {{ portfolios.total }}
                        </div>
                        <div class="text-sm text-gray-600">
                            работ показано
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Portfolio Grid -->
        <section class="py-16 bg-gray-50">
            <div class="container mx-auto px-4">
                <!-- Desktop Grid -->
                <div class="hidden md:grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <PortfolioCard 
                        v-for="portfolio in portfolios.data" 
                        :key="portfolio.id"
                        :portfolio="portfolio"
                        :config="config"
                    />
                </div>

                <!-- Mobile List -->
                <div class="md:hidden space-y-6">
                    <PortfolioCardMobile 
                        v-for="portfolio in portfolios.data" 
                        :key="portfolio.id"
                        :portfolio="portfolio"
                    />
                </div>

                <!-- Pagination -->
                <Pagination 
                    v-if="portfolios.links.length > 3" 
                    :links="portfolios.links"
                    class="mt-12"
                />

                <!-- Empty State -->
                <EmptyState 
                    v-if="portfolios.data.length === 0"
                    :selectedService="selectedService"
                />
            </div>
        </section>

        <!-- Services Preview -->
        <section class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-serif font-bold text-gray-900 mb-4">
                        Наши услуги
                    </h2>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                        Выберите процедуру, чтобы увидеть больше работ в этой категории
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <div 
                        v-for="service in featuredServices" 
                        :key="service.id"
                        class="group relative bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 hover:-translate-y-1"
                    >
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-900 line-clamp-1">
                                    {{ service.name }}
                                </h3>
                                <span 
                                    v-if="service.has_promotion"
                                    class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full"
                                >
                                    Акция
                                </span>
                            </div>
                            
                            <div class="space-y-2 mb-6">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">Длительность:</span>
                                    <span class="font-bold text-gray-900">{{ service.duration }} мин</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">Цена:</span>
                                    <div class="text-right">
                                        <div 
                                            v-if="service.has_promotion"
                                            class="text-sm text-gray-500 line-through"
                                        >
                                            {{ formatPrice(service.price) }}
                                        </div>
                                        <div class="text-lg font-bold text-pink-600">
                                            {{ formatPrice(service.final_price) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex gap-2">
                                <button
                                    @click="filterByServiceSlug(service.slug)"
                                    class="flex-1 px-4 py-2 bg-pink-500 text-white font-medium rounded-lg hover:bg-pink-600 transition text-center"
                                >
                                    Смотреть работы
                                </button>
                                <Link 
                                    :href="route('services.show', service.slug)"
                                    class="px-4 py-2 border border-pink-500 text-pink-600 font-medium rounded-lg hover:bg-pink-50 transition flex items-center justify-center"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <CTASection />
    </AppLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PortfolioCard from './Components/PortfolioCard.vue';
import PortfolioCardMobile from './Components/PortfolioCardMobile.vue';
import Pagination from '@/Components/Common/Pagination.vue';
import EmptyState from './Components/EmptyState.vue';
import CTASection from './Components/CTASection.vue';

const { props: pageProps } = usePage();

const props = defineProps({
    portfolios: Object,
    filters: Object,
    averageRating: {
        type: Number,
        default: 4.9
    },
    services: {
        type: Array,
        default: () => []
    }
});

const config = computed(() => pageProps.config);

// Filter states
const selectedService = ref(props.filters.service || '');
const selectedCategory = ref(props.filters.category || '');

// Computed
const uniqueCategories = computed(() => {
    const categories = [...new Set(props.services.map(s => s.category))];
    return categories.filter(c => c).sort();
});

const featuredServices = computed(() => {
    // Take 8 services, prioritize promoted ones
    const promoted = props.services.filter(s => s.has_promotion);
    const regular = props.services.filter(s => !s.has_promotion);
    return [...promoted, ...regular].slice(0, 8);
});

// Methods
const formatPrice = (price) => {
    return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: 'RUB',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
};

const filterByService = () => {
    router.get(route('portfolio'), { 
        service: selectedService.value,
        category: selectedCategory.value
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const filterByCategory = (category) => {
    selectedCategory.value = selectedCategory.value === category ? '' : category;
    filterByService();
};

const filterByServiceSlug = (slug) => {
    selectedService.value = slug;
    filterByService();
    
    // Scroll to portfolio section
    setTimeout(() => {
        document.querySelector('section:nth-of-type(3)')?.scrollIntoView({ 
            behavior: 'smooth' 
        });
    }, 100);
};
</script>