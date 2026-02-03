<template>
    <Head title="Контакты | Feminaluxe"/>

    <AppLayout>
        <!-- Hero Section -->
        <section class="relative py-16 bg-gradient-to-r from-rose-50 to-pink-50">
            <div class="container mx-auto px-4">
                <div class="max-w-4xl mx-auto text-center">
                    <h1 class="text-4xl md:text-5xl font-bold mb-6 text-gray-900">
                        Контакты студии перманентного макияжа Feminaluxe
                    </h1>
                    <p class="text-lg md:text-xl text-gray-700 mb-8 max-w-3xl mx-auto">
                        Профессиональная студия перманентного макияжа с медицинским подходом и премиальными материалами
                    </p>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <section class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
                    <!-- Contact Information -->
                    <div class="space-y-8">
                        <div>
                            <h2 class="text-3xl font-bold mb-6 text-gray-900">Как нас найти</h2>
                            <div class="space-y-6">
                                <!-- Address -->
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 rounded-full bg-rose-100 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Адрес студии</h3>
                                        <p class="text-gray-700">{{ pageProps.config.contact.address }}</p>
                                        <p class="text-sm text-gray-500 mt-2">Вход со стороны "Пятерочки"</p>
                                    </div>
                                </div>

                                <!-- Phone -->
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 rounded-full bg-rose-100 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Телефон для записи</h3>
                                        <a :href="`tel:${pageProps.config.contact.phone}`"
                                           class="text-xl font-bold text-rose-600 hover:text-rose-700 transition-colors">
                                            {{ pageProps.config.contact.phone }}
                                        </a>
                                        <p class="text-sm text-gray-500 mt-2">Звоните для консультации и записи</p>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 rounded-full bg-rose-100 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Электронная почта</h3>
                                        <a :href="`mailto:${pageProps.config.contact.email}`"
                                           class="text-lg text-rose-600 hover:text-rose-700 transition-colors">
                                            {{ pageProps.config.contact.email }}
                                        </a>
                                        <p class="text-sm text-gray-500 mt-2">Ответим в течение рабочего дня</p>
                                    </div>
                                </div>

                                <!-- Hours -->
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 rounded-full bg-rose-100 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Режим работы</h3>
                                        <p class="text-gray-700">{{ pageProps.config.contact.hours }}</p>
                                        <p class="text-sm text-gray-500 mt-2">Запись по предварительной записи</p>
                                    </div>
                                </div>

                                <!-- Social -->
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 rounded-full bg-rose-100 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Мы в соцсетях</h3>
                                        <div class="flex space-x-4">
                                            <a v-if="pageProps.config.contact.vk"
                                               :href="`https://vk.com/${pageProps.config.contact.vk}`"
                                               target="_blank"
                                               class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center hover:bg-blue-600 transition-colors"
                                               aria-label="ВКонтакте">
                                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M15.073 2H8.938C3.332 2 2 3.333 2 8.927v6.136C2 20.667 3.323 22 8.927 22h6.136C20.667 22 22 20.677 22 15.073V8.938C22 3.332 20.677 2 15.073 2zm3.073 14.927h-1.531c-.615 0-.719-.448-1.708-1.438-.865-.865-1.198-.938-1.406-.938-.302 0-.385.083-.385.5v1.302c0 .354-.115.563-1.042.563-1.542 0-3.125-.833-4.292-2.396-1.667-1.781-2.875-4.583-2.875-4.583 0-.26.083-.5.5-.5h1.531c.375 0 .51.167.656.552.708 1.562 1.917 3.323 2.417 3.323.188 0 .27-.083.27-.552v-2.146c-.062-.979-.583-1.062-.583-1.406 0-.125.083-.208.188-.208h2.292c.313 0 .417.146.417.479v2.896c0 .313.135.417.229.417.188 0 .333-.104.677-.448 1.167-1.167 2.042-3 2.042-3 .125-.26.302-.5.656-.5h1.531c.438 0 .531.229.438.531-.208 1.009-2.177 3.677-2.177 3.677-.167.208-.24.313 0 .552.135.219.604.646 1 1.052.677.677 1.219 1.25 1.354 1.604.125.438-.125.667-.531.667z"/>
                                                </svg>
                                            </a>
                                            <a v-if="pageProps.config.contact.telegram"
                                               :href="`https://t.me/${pageProps.config.contact.telegram}`"
                                               target="_blank"
                                               class="w-10 h-10 rounded-full bg-blue-400 flex items-center justify-center hover:bg-blue-500 transition-colors"
                                               aria-label="Telegram">
                                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69.01-.03.01-.14-.06-.2-.07-.06-.17-.04-.24-.02-.1.02-1.79 1.14-5.06 3.35-.48.33-.91.49-1.3.48-.43-.01-1.27-.25-1.89-.45-.76-.25-1.36-.38-1.31-.8.03-.24.37-.48 1-.74z"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CTA Button -->
                        <div>
                            <Link :href="route('bookings.create')"
                                  class="inline-flex items-center justify-center w-full md:w-auto px-8 py-4 bg-gradient-to-r from-rose-600 to-pink-600 text-white font-bold rounded-xl hover:shadow-xl hover:shadow-rose-200 transition-all duration-300 text-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Записаться онлайн
                            </Link>
                        </div>
                    </div>

                    <!-- Яндекс Карта -->
                    <div class="rounded-2xl overflow-hidden shadow-2xl h-[500px]">
                        <div id="yandex-map" class="w-full h-full"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Transport Info -->
        <section class="py-12 bg-gray-50">
            <div class="container mx-auto px-4">
                <h2 class="text-2xl font-bold text-center mb-8 text-gray-900">Как добраться</h2>

                <div class="max-w-3xl mx-auto">
                    <div class="bg-white rounded-2xl shadow-lg p-8 md:p-10">
                        <!-- Иконка геолокации -->
                        <div class="flex justify-center mb-6">
                            <div class="w-20 h-20 rounded-full bg-gradient-to-r from-rose-100 to-pink-100 flex items-center justify-center">
                                <svg class="w-10 h-10 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Текст маршрута -->
                        <div class="text-center">
                            <div class="inline-flex flex-col md:flex-row items-center justify-center gap-4 md:gap-8 mb-6">
                                <!-- МЦД -->
                                <div class="flex items-center gap-3 bg-blue-50 px-4 py-3 rounded-lg">
                                    <div class="w-10 h-10 rounded-full bg-mcd-4 flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">D4</span>
                                    </div>
                                    <div class="text-left">
                                        <div class="text-sm text-gray-500">Станция</div>
                                        <div class="font-bold text-gray-900 text-lg">Никольская</div>
                                    </div>
                                </div>

                                <!-- Разделитель -->
                                <div class="hidden md:block">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                    </svg>
                                </div>

                                <!-- Маршрутки -->
                                <div class="flex items-center gap-3 bg-green-50 px-4 py-3 rounded-lg">
                                    <div class="w-10 h-10 rounded-full bg-green-600 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div class="text-left">
                                        <div class="text-sm text-gray-500">Маршрутки</div>
                                        <div class="font-bold text-gray-900 text-lg">
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded mr-1">9к</span>
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded">22к</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Дополнительная информация -->
                            <div class="text-gray-600 space-y-2">
                                <p class="flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Время в пути от станции: 10-15 минут</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Кнопка построения маршрута -->
                    <div class="text-center mt-8">
                        <a
                            :href="`https://yandex.ru/maps/?pt=${pageProps.config.yandex_maps.longitude},${pageProps.config.yandex_maps.latitude}&z=15&l=map`"
                            target="_blank"
                            class="inline-flex items-center gap-2 bg-rose-600 text-white px-6 py-3 rounded-lg hover:bg-rose-700 transition-colors duration-300"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                            Построить маршрут в Яндекс Картах
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { onMounted } from 'vue';

const { props: pageProps } = usePage();

// Яндекс Карта
const initYandexMap = () => {
    if (!window.ymaps) return;

    window.ymaps.ready(() => {
        const { latitude, longitude, zoom } = pageProps.config.yandex_maps;

        const map = new window.ymaps.Map('yandex-map', {
            center: [latitude, longitude],
            zoom: zoom,
            controls: ['zoomControl', 'fullscreenControl']
        });

        // Метка
        const placemark = new window.ymaps.Placemark([latitude, longitude], {
            balloonContent: `
                <div class="p-2">
                    <h3 class="font-bold text-lg mb-1">Feminaluxe</h3>
                    <p class="text-gray-700">${pageProps.config.contact.address}</p>
                    <p class="text-sm text-gray-500 mt-1">Студия перманентного макияжа</p>
                </div>
            `,
            hintContent: 'Feminaluxe'
        }, {
            preset: 'islands#redDotIcon',
            iconColor: '#e11d48'
        });

        map.geoObjects.add(placemark);
        map.controls.remove('searchControl');
        map.behaviors.disable('scrollZoom');
    });
};

const loadYandexMaps = () => {
    // Проверяем, есть ли API ключ
    const apiKey = pageProps.config.yandex_maps?.api_key;

    if (!apiKey) {
        console.warn('Yandex Maps API key is missing');

        // Показываем fallback картинку или текст
        const mapContainer = document.getElementById('yandex-map');
        if (mapContainer) {
            mapContainer.innerHTML = `
                <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                    <div class="text-center">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p class="text-gray-600">Карта временно недоступна</p>
                        <p class="text-sm text-gray-500 mt-1">${pageProps.config.contact.address}</p>
                    </div>
                </div>
            `;
        }
        return;
    }

    // Загружаем Яндекс Карты API
    if (!window.ymaps) {
        const script = document.createElement('script');
        script.src = `https://api-maps.yandex.ru/2.1/?apikey=${apiKey}&lang=ru_RU`;
        script.onload = initYandexMap;
        script.onerror = () => {
            console.error('Failed to load Yandex Maps API');
        };
        document.head.appendChild(script);
    } else {
        initYandexMap();
    }
};

onMounted(() => {
    loadYandexMaps();
});
</script>
