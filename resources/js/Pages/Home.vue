<script setup>
import {Head, Link, usePage} from '@inertiajs/vue3';
import {ref, onMounted, computed} from 'vue';
import {route} from "ziggy-js";
import AppLayout from '@/Layouts/AppLayout.vue';
import ServiceCard from '@/Components/ServiceCard.vue';
import ReviewsTeaser from '@/Components/ReviewsTeaser.vue';

const props = defineProps({
    services: Array,
    popularServices: Array,
    categories: Array,
});

const { props: pageProps } = usePage();

const activeCategory = ref('all');
const stats = ref([
    {value: '1000+', label: 'Довольных клиенток', icon: '😊'},
    {value: '9 лет', label: 'Опыта работы', icon: '⭐'},
    {value: '96%', label: 'Положительных отзывов', icon: '❤️'},
    {value: '100%', label: 'Поддержка клиентов', icon: '📱'},
]);

// Категории услуг
const dynamicCategories = computed(() => {
    if (!props.categories || props.categories.length === 0) {
        return [];
    }

    // Сортируем категории: сначала с наибольшим количеством услуг
    props.categories.sort((a, b) => b.count - a.count);

    // Добавляем "Все услуги" в начало
    return [
        {
            id: 'all',
            name: 'Все услуги',
            slug: 'all',
            count: props.services.length,
            icon: '✨',
            description: 'Полный перечень всех услуг',
        },
        ...props.categories,
    ];
});

// Фильтрация услуг по категории
const filteredServices = ref([]);

const filterServices = (category) => {
    activeCategory.value = category;
    if (category === 'all') {
        filteredServices.value = props.services || [];
    } else {
        filteredServices.value = (props.services || []).filter(service => service.category === category);
    }
};

// Инициализация
onMounted(() => {
    filteredServices.value = props.services || [];
});
</script>

<template>
    <Head>
        <title>Feminaluxe: Перманентный макияж бровей, губ и межресничек в Балашихе</title>
        <meta name="description"
              content="Профессиональный перманентный макияж – естественная красота на годы. Пудровое напыление бровей, акварельный татуаж губ, межресничный перманент. Запишитесь онлайн!"/>
        <!-- Open Graph теги -->
        <meta property="og:title" content="Feminaluxe: Перманентный макияж премиум-класса в Балашихе"/>
        <meta property="og:description"
              content="Профессиональный перманентный макияж – естественная красота на годы. Пудровое напыление бровей, акварельный татуаж губ, межресничный перманент."/>
        <meta property="og:image" content="/images/og-image.jpg"/>
        <meta property="og:type" content="website"/>
        <meta property="og:url" :content="route('home')"/>

        <!-- JSON-LD для структурированных данных -->
        <script type="application/ld+json">
            {
                "@context": "https://schema.org",
                "@type": "BeautySalon",
                "name": "Feminaluxe",
                "description": "Студия перманентного макияжа премиум-класса в Балашихе",
                "url": "{{ route('home') }}",
                "telephone": "{{ pageProps.config.contact.phone }}",
                "address": {
                    "@type": "PostalAddress",
                    "streetAddress": "{{ pageProps.config.contact.address }}",
                    "addressLocality": "Балашиха",
                    "postalCode": "143914",
                    "addressCountry": "RU"
                },
                "openingHoursSpecification": [
                    {
                        "@type": "OpeningHoursSpecification",
                        "dayOfWeek": [
                            "Monday",
                            "Tuesday",
                            "Wednesday",
                            "Thursday",
                            "Friday",
                            "Saturday"
                        ],
                        "opens": "09:00",
                        "closes": "21:00"
                    }
                ],
                "priceRange": "$$",
                "image": "/images/og-image.jpg",
                "aggregateRating": {
                    "@type": "AggregateRating",
                    "ratingValue": "4.9",
                    "reviewCount": "237"
                }
            }
        </script>
    </Head>

    <AppLayout>
        <!-- Hero секция -->
        <section class="relative min-h-[90vh] flex items-center overflow-hidden">
            <!-- Фон -->
            <div class="absolute inset-0 bg-gradient-to-br from-rose-400/20 via-pink-300/10 to-purple-400/20"></div>

            <!-- Контент -->
            <div class="relative z-10 container mx-auto px-4 lg:px-8 py-20">
                <div class="max-w-3xl">
                    <!-- Бейдж -->
                    <div
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-rose-100 to-pink-100 rounded-full mb-8">
                        <span class="w-2 h-2 bg-gradient-to-r from-rose-500 to-pink-500 rounded-full mr-2"></span>
                        <span class="text-sm font-semibold text-rose-600">Студя перманентного макияжа в Балашихе</span>
                    </div>

                    <!-- Заголовок -->
                    <h1 class="text-4xl lg:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                        Перманентный макияж
                        <span class="bg-gradient-to-r from-rose-600 to-pink-500 bg-clip-text text-transparent">
              премиум-класса
            </span>
                        в Балашихе
                    </h1>

                    <!-- Подзаголовок -->
                    <p class="text-xl lg:text-2xl text-gray-600 mb-10 max-w-2xl">
                        Просыпайтесь с идеальным макияжем каждый день.
                        <span
                            class="font-semibold text-rose-600">Естественная красота, которая сохраняется годами</span>
                    </p>

                    <!-- CTA кнопки -->
                    <div class="flex flex-col sm:flex-row gap-4 mb-12">
                        <Link
                            :href="route('services.index')"
                            class="px-8 py-4 bg-gradient-to-r from-rose-500 to-pink-500 text-white font-semibold rounded-full hover:shadow-xl hover:shadow-rose-200 transition-all duration-300 transform hover:-translate-y-0.5 text-center"
                        >
                            Выбрать услугу
                            <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </Link>
                        <a
                            href="#benefits"
                            class="px-8 py-4 bg-white text-gray-700 font-semibold rounded-full border-2 border-gray-200 hover:border-rose-200 hover:bg-rose-50 transition-all duration-300 text-center"
                        >
                            Узнать больше
                        </a>
                    </div>

                    <!-- Статистика -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <div
                            v-for="stat in stats"
                            :key="stat.label"
                            class="bg-white/80 backdrop-blur-sm rounded-xl p-4 border border-gray-100 shadow-sm"
                        >
                            <div class="text-2xl lg:text-3xl font-bold text-gray-900 mb-1">
                                {{ stat.value }}
                            </div>
                            <div class="text-sm text-gray-600 flex items-center">
                                <span class="mr-2">{{ stat.icon }}</span>
                                {{ stat.label }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Декоративный элемент -->
            <div class="absolute right-0 top-1/2 -translate-y-1/2 hidden xl:block">
                <div class="relative w-96 h-96">
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-rose-400/30 to-pink-400/30 rounded-full blur-3xl"></div>
                    <div class="absolute inset-10 bg-gradient-to-br from-rose-200 to-pink-200 rounded-full"></div>
                </div>
            </div>
        </section>

        <!-- Преимущества -->
        <section id="benefits" class="py-16 lg:py-24 bg-gradient-to-b from-white to-gray-50">
            <div class="container mx-auto px-4 lg:px-8">
                <div class="text-center mb-12 lg:mb-16">
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                        Почему выбирают Feminaluxe
                    </h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Мы создаем не просто перманентный макияж, а произведения искусства, которые подчеркивают вашу
                        естественную красоту
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Преимущество 1 -->
                    <div
                        class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow border border-gray-100">
                        <div
                            class="w-14 h-14 rounded-xl bg-gradient-to-br from-rose-100 to-pink-100 flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Безопасность и стерильность</h3>
                        <p class="text-gray-600">
                            Используем только одноразовые расходные материалы и гипоаллергенные пигменты премиум-класса,
                            сертифицированные в ЕАЭС
                        </p>
                    </div>

                    <!-- Преимущество 2 -->
                    <div
                        class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow border border-gray-100">
                        <div
                            class="w-14 h-14 rounded-xl bg-gradient-to-br from-rose-100 to-pink-100 flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Индивидуальный подход</h3>
                        <p class="text-gray-600">
                            Каждый дизайн создается с учетом анатомических особенностей, цветотипа и пожеланий клиента.
                            Мы создаем гармонию, а не шаблон
                        </p>
                    </div>

                    <!-- Преимущество 3 -->
                    <div
                        class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow border border-gray-100">
                        <div
                            class="w-14 h-14 rounded-xl bg-gradient-to-br from-rose-100 to-pink-100 flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Пожизненная гарантия</h3>
                        <p class="text-gray-600">
                            Бесплатная коррекция через 1 месяц и пожизненная гарантия на работу. Мы всегда готовы помочь
                            и довести результат до идеала
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Услуги -->
        <section id="pricing" class="py-16 lg:py-24 bg-white">
            <div class="container mx-auto px-4 lg:px-8">
                <div class="text-center mb-12 lg:mb-16">
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                        Наши услуги перманентного макияжа
                    </h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Выберите процедуру, которая подчеркнет вашу естественную красоту
                    </p>
                </div>

                <!-- Фильтры категорий -->
                <div class="flex flex-wrap justify-center gap-2 mb-12">
                    <button
                        v-for="category in dynamicCategories"
                        :key="category.id"
                        @click="filterServices(category.id)"
                        class="px-6 py-3 rounded-full text-sm font-semibold transition-all duration-300"
                        :class="activeCategory === category.id
              ? 'bg-gradient-to-r from-rose-500 to-pink-500 text-white shadow-lg'
              : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        :title="category.description"
                    >
                        <span class="text-lg">{{ category.icon }}</span>
                        <span>{{ category.name }}</span>
                        <span v-if="category.count > 0" class="text-xs opacity-75 bg-white/30 px-2 py-0.5 rounded-full">{{ category.count }}</span>
                    </button>
                </div>

                <!-- Карточки услуг -->
                <div v-if="filteredServices.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <ServiceCard
                        v-for="service in filteredServices"
                        :key="service.id"
                        :service="service"
                    />
                </div>
                <div v-else class="text-center py-12">
                    <div
                        class="w-24 h-24 mx-auto mb-6 rounded-full bg-gradient-to-r from-rose-100 to-pink-100 flex items-center justify-center">
                        <svg class="w-12 h-12 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">
                        {{ activeCategory === 'all' ? 'Услуги не найдены' : `Нет услуг в категории "${activeCategory}"` }}
                    </h3>
                    <p class="text-gray-600">{{ activeCategory === 'all'
                        ? 'Попробуйте обновить страницу позже'
                        : 'Попробуйте выбрать другую категорию'
                        }}
                    </p>
                    <button
                        v-if="activeCategory !== 'all'"
                        @click="activeCategory = 'all'"
                        class="mt-4 px-6 py-2 bg-gradient-rose-pink text-white rounded-full text-sm font-semibold hover:shadow-lg transition-shadow"
                    >
                        Вернуться ко всем услугам
                    </button>
                </div>

                <!-- CTA под услугами -->
                <div class="text-center mt-12 lg:mt-16">
                    <div v-if="dynamicCategories.length > 1" class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Все категории услуг</h3>
                        <div class="flex flex-wrap justify-center gap-2">
                            <Link
                                v-for="category in dynamicCategories.filter(c => c.id !== 'all')"
                                :key="category.id"
                                :href="route('services.category', category.slug)"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full text-sm font-medium transition-colors"
                            >
                                {{ category.name }}
                                <span class="text-xs opacity-60 ml-1">({{ category.count }})</span>
                            </Link>
                        </div>
                    </div>

                    <p class="text-lg text-gray-600 mb-8">
                        Не можете определиться с выбором? Запишитесь на бесплатную консультацию
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a
                            href="tel:+74951234567"
                            class="px-8 py-4 bg-gradient-to-r from-rose-500 to-pink-500 text-white font-semibold rounded-full hover:shadow-xl hover:shadow-rose-200 transition-all duration-300 transform hover:-translate-y-0.5 inline-flex items-center justify-center"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            Позвонить сейчас
                        </a>
                        <Link
                            :href="route('services.index')"
                            class="px-8 py-4 bg-white text-gray-700 font-semibold rounded-full border-2 border-gray-200 hover:border-rose-200 hover:bg-rose-50 transition-all duration-300 inline-flex items-center justify-center"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Все услуги →
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Специалисты -->
        <section id="specialists" class="py-16 lg:py-24 bg-gradient-to-b from-gray-50 to-white">
            <div class="container mx-auto px-4 lg:px-8">
                <div class="text-center mb-12 lg:mb-16">
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                        Наши мастера
                    </h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Профессионалы с медицинским образованием и многолетним опытом работы
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Мастер 1 -->
                    <div
                        class="group relative overflow-hidden rounded-2xl bg-white shadow-lg hover:shadow-2xl transition-all duration-500">
                        <div class="h-64 overflow-hidden">
                            <img
                                src="/images/master-1.jpg"
                                alt="Мастер Мария"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                        </div>
                        <div class="p-6 relative -mt-12">
                            <div
                                class="w-24 h-24 mx-auto rounded-full border-4 border-white overflow-hidden mb-4 shadow-lg">
                                <img src="/images/master-1-avatar.jpg" alt="Мария" class="w-full h-full object-cover"/>
                            </div>
                            <div class="text-center">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Мария Иванова</h3>
                                <div class="text-rose-600 font-semibold mb-3">Ведущий мастер по бровям</div>
                                <p class="text-gray-600 text-sm mb-4">
                                    Опыт работы 7 лет. Специализация: пудровые брови, микроблейдинг, коррекция
                                </p>
                                <div class="flex justify-center space-x-2">
                  <span class="px-3 py-1 bg-rose-100 text-rose-600 text-xs font-semibold rounded-full">
                    1500+ процедур
                  </span>
                                    <span
                                        class="px-3 py-1 bg-amber-100 text-amber-600 text-xs font-semibold rounded-full">
                    ★ 4.9
                  </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Мастер 2 -->
                    <div
                        class="group relative overflow-hidden rounded-2xl bg-white shadow-lg hover:shadow-2xl transition-all duration-500">
                        <div class="h-64 overflow-hidden">
                            <img
                                src="/images/master-2.jpg"
                                alt="Мастер Анастасия"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                        </div>
                        <div class="p-6 relative -mt-12">
                            <div
                                class="w-24 h-24 mx-auto rounded-full border-4 border-white overflow-hidden mb-4 shadow-lg">
                                <img src="/images/master-2-avatar.jpg" alt="Анастасия"
                                     class="w-full h-full object-cover"/>
                            </div>
                            <div class="text-center">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Анастасия Петрова</h3>
                                <div class="text-rose-600 font-semibold mb-3">Эксперт по губам</div>
                                <p class="text-gray-600 text-sm mb-4">
                                    Опыт работы 5 лет. Специализация: акварельные губы, контур губ, удаление татуажа
                                </p>
                                <div class="flex justify-center space-x-2">
                  <span class="px-3 py-1 bg-rose-100 text-rose-600 text-xs font-semibold rounded-full">
                    1200+ процедур
                  </span>
                                    <span
                                        class="px-3 py-1 bg-amber-100 text-amber-600 text-xs font-semibold rounded-full">
                    ★ 4.8
                  </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Мастер 3 -->
                    <div
                        class="group relative overflow-hidden rounded-2xl bg-white shadow-lg hover:shadow-2xl transition-all duration-500">
                        <div class="h-64 overflow-hidden">
                            <img
                                src="/images/master-3.jpg"
                                alt="Мастер Елена"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                        </div>
                        <div class="p-6 relative -mt-12">
                            <div
                                class="w-24 h-24 mx-auto rounded-full border-4 border-white overflow-hidden mb-4 shadow-lg">
                                <img src="/images/master-3-avatar.jpg" alt="Елена" class="w-full h-full object-cover"/>
                            </div>
                            <div class="text-center">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Елена Смирнова</h3>
                                <div class="text-rose-600 font-semibold mb-3">Специалист по векам</div>
                                <p class="text-gray-600 text-sm mb-4">
                                    Опыт работы 6 лет. Специализация: межресничный перманент, стрелки, удаление
                                </p>
                                <div class="flex justify-center space-x-2">
                  <span class="px-3 py-1 bg-rose-100 text-rose-600 text-xs font-semibold rounded-full">
                    1000+ процедур
                  </span>
                                    <span
                                        class="px-3 py-1 bg-amber-100 text-amber-600 text-xs font-semibold rounded-full">
                    ★ 4.9
                  </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Отзывы -->
        <ReviewsTeaser/>

        <!-- Контакты -->
        <section id="contacts" class="py-16 lg:py-24 bg-gradient-to-br from-rose-50 via-pink-50 to-purple-50">
            <div class="container mx-auto px-4 lg:px-8">
                <div class="max-w-6xl mx-auto">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                        <!-- Левая колонка: Форма -->
                        <div>
                            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-6">
                                Запишитесь на бесплатную консультацию
                            </h2>
                            <p class="text-lg text-gray-600 mb-8">
                                Оставьте заявку и мы перезвоним вам в течение 15 минут, чтобы ответить на все вопросы и
                                подобрать удобное время
                            </p>

                            <form @submit.prevent="submitForm" class="space-y-6">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                            Ваше имя *
                                        </label>
                                        <input
                                            type="text"
                                            id="name"
                                            required
                                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-rose-500 focus:ring-2 focus:ring-rose-200 transition-colors"
                                            placeholder="Имя"
                                        />
                                    </div>
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                            Телефон *
                                        </label>
                                        <input
                                            type="tel"
                                            id="phone"
                                            required
                                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-rose-500 focus:ring-2 focus:ring-rose-200 transition-colors"
                                            placeholder="+7 (___) ___-__-__"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label for="service" class="block text-sm font-medium text-gray-700 mb-2">
                                        Интересующая услуга
                                    </label>
                                    <select
                                        id="service"
                                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-rose-500 focus:ring-2 focus:ring-rose-200 transition-colors"
                                    >
                                        <option value="">Выберите услугу</option>
                                        <option value="brows">Пудровые брови</option>
                                        <option value="lips">Акварельные губы</option>
                                        <option value="eyeliner">Межресничный перманент</option>
                                        <option value="correction">Коррекция татуажа</option>
                                        <option value="removal">Удаление татуажа</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                                        Комментарий
                                    </label>
                                    <textarea
                                        id="message"
                                        rows="4"
                                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-rose-500 focus:ring-2 focus:ring-rose-200 transition-colors"
                                        placeholder="Опишите ваши пожелания или вопросы..."
                                    ></textarea>
                                </div>

                                <div class="flex items-center">
                                    <input
                                        type="checkbox"
                                        id="privacy"
                                        required
                                        class="w-4 h-4 text-rose-500 border-gray-300 rounded focus:ring-rose-500"
                                    />
                                    <label for="privacy" class="ml-2 text-sm text-gray-600">
                                        Соглашаюсь с
                                        <a href="#" class="text-rose-600 hover:text-rose-700 underline">
                                            политикой конфиденциальности
                                        </a>
                                    </label>
                                </div>

                                <button
                                    type="submit"
                                    class="w-full px-8 py-4 bg-gradient-to-r from-rose-500 to-pink-500 text-white font-semibold rounded-lg hover:shadow-xl hover:shadow-rose-200 transition-all duration-300 transform hover:-translate-y-0.5"
                                >
                                    Записаться на консультацию
                                </button>
                            </form>
                        </div>

                        <!-- Правая колонка: Контактная информация -->
                        <div class="lg:pl-12">
                            <div class="bg-white rounded-2xl shadow-xl p-8 lg:p-10">
                                <h3 class="text-2xl font-bold text-gray-900 mb-8">
                                    Контактная информация
                                </h3>

                                <div class="space-y-8">
                                    <!-- Адрес -->
                                    <div class="flex items-start">
                                        <div
                                            class="w-12 h-12 rounded-xl bg-gradient-to-r from-rose-100 to-pink-100 flex items-center justify-center flex-shrink-0 mr-4">
                                            <svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900 mb-2">Адрес студии</h4>
                                            <p class="text-gray-600">
                                                {{ pageProps.config.contact.address }}<br>
                                                Метро: ЖД ст. Никольская (МЦД-4)
                                            </p>
                                            <a
                                                href="https://yandex.ru/maps/-/CPAsbD9F"
                                                target="_blank"
                                                class="inline-flex items-center text-rose-600 hover:text-rose-700 font-medium mt-2"
                                            >
                                                Построить маршрут
                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Телефоны -->
                                    <div class="flex items-center">
                                        <div
                                            class="w-12 h-12 rounded-xl bg-gradient-to-r from-rose-100 to-pink-100 flex items-center justify-center flex-shrink-0 mr-4">
                                            <svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900 mb-2">Контакты</h4>
                                            <div class="space-y-1">
                                                <a
                                                    :href="`tel:${pageProps.config.contact.phone}`"
                                                    class="block text-gray-600 hover:text-rose-600 transition-colors"
                                                >
                                                    {{ pageProps.config.contact.phone }}
                                                </a>
                                                <a
                                                    :href="`mailto:${pageProps.config.contact.email}`"
                                                    class="block text-gray-600 hover:text-rose-600 transition-colors"
                                                >
                                                    {{ pageProps.config.contact.email }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Часы работы -->
                                    <div class="flex items-center">
                                        <div
                                            class="w-12 h-12 rounded-xl bg-gradient-to-r from-rose-100 to-pink-100 flex items-center justify-center flex-shrink-0 mr-4">
                                            <svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900 mb-2">Часы работы</h4>
                                            <p class="text-gray-600">
                                                {{ pageProps.config.contact.hours }}
                                                <span class="text-sm text-rose-600">Принимаем по записи</span>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Социальные сети -->
                                    <div class="flex items-center">
                                        <div
                                            class="w-12 h-12 rounded-xl bg-gradient-to-r from-rose-100 to-pink-100 flex items-center justify-center flex-shrink-0 mr-4">
                                            <svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900 mb-2">Мы в соцсетях</h4>
                                            <div class="flex space-x-3">
                                                <a :href="`https://vk.com/${pageProps.config.contact.vk}`" class="text-gray-600 hover:text-rose-600 transition-colors">
                                                    <span class="sr-only">VK</span>
                                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                                        <path
                                                            d="M15.684 0H8.316C1.592 0 0 1.592 0 8.316v7.368C0 22.408 1.592 24 8.316 24h7.368C22.408 24 24 22.408 24 15.684V8.316C24 1.592 22.408 0 15.684 0zm3.692 17.123h-1.744c-.66 0-.864-.525-2.05-1.727-1.033-1-1.49-1.135-1.744-1.135-.356 0-.458.102-.458.593v1.575c0 .424-.135.678-1.253.678-1.846 0-3.896-1.118-5.335-3.202C4.624 12.75 3.802 9.944 3.802 9.944c0-.254.102-.491.593-.491h1.744c.44 0 .61.203.78.677.863 2.49 2.303 4.675 2.896 4.675.22 0 .322-.102.322-.66V9.964c-.068-1.186-.695-1.287-.695-1.71 0-.203.17-.407.44-.407h2.744c.373 0 .508.203.508.643v3.473c0 .372.17.508.271.508.22 0 .407-.136.813-.542 1.254-1.406 2.151-3.574 2.151-3.574.119-.254.305-.491.762-.491h1.744c.525 0 .644.271.525.643-.22.73-2.354 4.065-2.354 4.065-.186.305-.254.44 0 .78.186.254.796.78 1.203 1.253.746.847 1.32 1.558 1.473 2.05.17.525-.085.795-.576.795z"/>
                                                    </svg>
                                                </a>
                                                <a :href="`https://t.me/${pageProps.config.contact.telegram}`" class="text-gray-600 hover:text-rose-600 transition-colors">
                                                    <span class="sr-only">Telegram</span>
                                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                                        <path
                                                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.41 1-.67 1-.58.05-1.02-.38-1.58-.75-.88-.57-1.38-.93-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69.01-.03.01-.14-.06-.2-.07-.06-.17-.04-.24-.02-.1.02-1.79 1.14-5.06 3.35-.48.33-.91.49-1.3.48-.43-.01-1.25-.24-1.86-.44-.75-.25-1.35-.38-1.3-.8.03-.24.37-.48 1.02-.74 4.05-1.73 6.75-2.88 8.1-3.45 3.85-1.62 4.64-1.9 5.17-1.9.11 0 .38.03.55.18.13.12.16.28.18.43.01.1.01.32.01.55-.01.22-.01.41-.02.56z"/>
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA баннер -->
        <section class="py-16 lg:py-20 bg-gradient-to-r from-rose-500 via-pink-500 to-purple-500">
            <div class="container mx-auto px-4 lg:px-8 text-center">
                <h2 class="text-3xl lg:text-4xl font-bold text-white mb-6">
                    Готовы изменить свою жизнь к лучшему?
                </h2>
                <p class="text-xl text-white/90 mb-10 max-w-3xl mx-auto">
                    Запишитесь на первую процедуру со скидкой 20% и получите бесплатную консультацию от ведущего мастера
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a
                        href="tel:+74951234567"
                        class="px-8 py-4 bg-white text-rose-600 font-bold rounded-full hover:bg-rose-50 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5"
                    >
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        Позвонить и записаться
                    </a>
                    <Link
                        :href="route('services.index')"
                        class="px-8 py-4 bg-transparent border-2 border-white text-white font-bold rounded-full hover:bg-white/10 transition-all duration-300"
                    >
                        Выбрать услугу онлайн
                    </Link>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<style scoped>
@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

/* Плавный скролл */
html {
    scroll-behavior: smooth;
}

/* Градиентные эффекты */
.gradient-border {
    position: relative;
}

.gradient-border::before {
    content: '';
    position: absolute;
    inset: -1px;
    border-radius: inherit;
    background: linear-gradient(45deg, #ec4899, #8b5cf6, #3b82f6);
    z-index: -1;
}
</style>
