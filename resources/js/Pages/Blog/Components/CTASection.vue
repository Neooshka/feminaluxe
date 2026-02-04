<!-- resources/js/Pages/Blog/Components/CTASection.vue -->
<template>
    <section class="py-20 bg-gradient-to-r from-purple-600 to-pink-500 overflow-hidden">
        <!-- Декоративные элементы -->
        <div class="absolute top-0 left-0 w-64 h-64 bg-white/10 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-white/5 rounded-full translate-x-1/3 translate-y-1/3"></div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <!-- Иконка -->
                <div class="mb-8">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-2xl backdrop-blur-sm">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                </div>
                
                <!-- Заголовок -->
                <h2 class="text-4xl md:text-5xl font-serif font-bold text-white mb-6">
                    Готовы к преображению?
                </h2>
                
                <!-- Описание -->
                <p class="text-xl text-white/90 mb-10 max-w-2xl mx-auto">
                    Прочитали статьи и вдохновились? Запишитесь на консультацию к нашему мастеру 
                    и получите индивидуальный план процедуры
                </p>
                
                <!-- Статистика -->
                <div class="flex flex-wrap justify-center gap-8 mb-12">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-white mb-1">500+</div>
                        <div class="text-white/80 text-sm">довольных клиентов</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-white mb-1">4.9</div>
                        <div class="text-white/80 text-sm">средний рейтинг</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-white mb-1">100+</div>
                        <div class="text-white/80 text-sm">статей в блоге</div>
                    </div>
                </div>
                
                <!-- Кнопки действий -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <!-- Основная кнопка записи -->
                    <Link 
                        :href="route('bookings.create')"
                        class="group relative px-8 py-4 bg-white text-purple-600 font-bold text-lg rounded-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 overflow-hidden"
                    >
                        <span class="relative z-10 flex items-center justify-center gap-3">
                            🗓️ Записаться на консультацию
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </span>
                        <div class="absolute inset-0 bg-gradient-to-r from-white to-gray-100 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </Link>
                    
                    <!-- Вторичная кнопка -->
                    <Link 
                        :href="route('portfolio')"
                        class="px-8 py-4 bg-transparent border-2 border-white text-white font-bold text-lg rounded-xl hover:bg-white/10 transition flex items-center justify-center gap-3"
                    >
                        👁️ Посмотреть портфолио
                    </Link>
                </div>
                
                <!-- Дополнительная информация -->
                <div class="mt-12 pt-8 border-t border-white/20">
                    <div class="flex flex-col md:flex-row items-center justify-center gap-6">
                        <!-- Телефон -->
                        <a 
                            :href="`tel:${config.contact.phone.replace(/[^0-9+]/g, '')}`"
                            class="flex items-center gap-3 text-white hover:text-gray-200 transition group"
                        >
                            <div class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center group-hover:bg-white/20 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div class="text-left">
                                <div class="text-sm text-white/70">Позвоните нам</div>
                                <div class="font-bold text-lg">{{ config.contact.phone }}</div>
                            </div>
                        </a>
                        
                        <!-- Часы работы -->
                        <div class="flex items-center gap-3 text-white">
                            <div class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="text-left">
                                <div class="text-sm text-white/70">Часы работы</div>
                                <div class="font-bold">{{ config.contact.hours }}</div>
                            </div>
                        </div>
                        
                        <!-- Соцсети -->
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-white/20 transition">
                                <a :href="`https://vk.com/${config.contact.vk}`" target="_blank" class="text-white">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M15.684 0H8.316C1.592 0 0 1.592 0 8.316v7.368C0 22.408 1.592 24 8.316 24h7.368C22.408 24 24 22.408 24 15.684V8.316C24 1.592 22.408 0 15.684 0zm3.692 17.123h-1.744c-.66 0-.864-.525-2.05-1.727-1.033-1-1.49-1.135-1.744-1.135-.356 0-.458.102-.458.593v1.575c0 .424-.135.678-1.253.678-1.846 0-3.896-1.118-5.335-3.202C4.624 10.857 4.03 8.57 4.03 8.096c0-.254.102-.491.593-.491h1.744c.44 0 .61.203.78.677.863 2.49 2.303 4.675 2.896 4.675.22 0 .322-.102.322-.661V9.721c-.068-1.186-.695-1.287-.695-1.71 0-.203.17-.407.44-.407h2.744c.373 0 .508.203.508.661v3.575c0 .373.17.508.254.508.22 0 .407-.136.813-.542 1.254-1.406 2.151-3.574 2.151-3.574.119-.254.305-.491.78-.491h1.744c.542 0 .66.271.542.661-.22.678-2.354 4.065-2.354 4.065-.186.305-.254.44 0 .78.186.254.796.78 1.203 1.253.745.847 1.32 1.558 1.473 2.05.17.491-.085.744-.576.744z"/>
                                    </svg>
                                </a>
                            </div>
                            <div class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-white/20 transition">
                                <a :href="`https://t.me/${config.contact.telegram}`" target="_blank" class="text-white">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69.01-.03.01-.14-.06-.2-.07-.06-.17-.04-.24-.02-.1.02-1.79 1.14-5.06 3.34-.48.33-.91.49-1.3.48-.43-.01-1.27-.24-1.89-.44-.76-.24-1.36-.37-1.31-.78.03-.24.27-.48.74-.73 2.9-1.26 4.84-2.1 5.83-2.51 2.78-1.16 3.36-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .48z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const { props: pageProps } = usePage();
const config = computed(() => pageProps.config);
</script>