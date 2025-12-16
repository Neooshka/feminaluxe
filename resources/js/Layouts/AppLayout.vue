<template>
    <div class="min-h-screen bg-gradient-to-b from-[var(--color-gray-50)] to-white font-sans">
        <!-- Хедер -->
        <header class="sticky top-0 z-50 glass-effect shadow-rose-soft">
            <div class="container mx-auto px-4 lg:px-8">
                <div class="flex items-center justify-between h-16 lg:h-20">
                    <!-- Логотип -->
                    <Link :href="route('home')" class="flex items-center space-x-3 group">
                        <div
                            class="w-10 h-10 bg-gradient-rose-pink rounded-2xl flex items-center justify-center transform group-hover:rotate-12 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xl lg:text-2xl font-bold text-gradient-rose-pink">
                                Feminaluxe
                            </div>
                            <div class="text-xs text-[var(--color-gray-500)] hidden md:block">Перманентный макияж
                                премиум-класса
                            </div>
                        </div>
                    </Link>

                    <!-- Десктопная навигация -->
                    <nav class="hidden lg:flex items-center space-x-8">
                        <Link
                            v-for="item in navItems"
                            :key="item.name"
                            :href="item.href"
                            class="text-[var(--color-gray-700)] hover:text-[var(--color-rose-600)] font-medium transition-colors duration-200 relative group"
                        >
                            {{ item.name }}
                            <span
                                class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-rose-pink group-hover:w-full transition-all duration-300"></span>
                        </Link>
                    </nav>

                    <!-- Кнопки действий -->
                    <div class="hidden lg:flex items-center space-x-4">
                        <a
                            href="tel:+74951234567"
                            class="flex items-center space-x-2 text-[var(--color-gray-700)] hover:text-[var(--color-rose-600)] transition-colors"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span class="font-semibold">+7 (495) 123-45-67</span>
                        </a>
                        <Link
                            :href="route('services.index')"
                            class="btn-gradient-primary px-6 py-3 hover:shadow-rose-strong"
                        >
                            Записаться онлайн
                        </Link>
                    </div>

                    <!-- Мобильное меню -->
                    <button
                        @click="isMenuOpen = !isMenuOpen"
                        class="lg:hidden p-2 rounded-lg hover:bg-[var(--color-gray-100)] transition-colors"
                    >
                        <svg
                            class="w-6 h-6 text-[var(--color-gray-700)]"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            :class="{ 'hidden': isMenuOpen, 'block': !isMenuOpen }"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg
                            class="w-6 h-6 text-[var(--color-gray-700)]"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            :class="{ 'hidden': !isMenuOpen, 'block': isMenuOpen }"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Мобильное меню (выпадающее) -->
            <div
                v-if="isMenuOpen"
                class="lg:hidden bg-white border-t shadow-lg animate-fade-in"
            >
                <div class="container mx-auto px-4 py-6">
                    <nav class="flex flex-col space-y-4">
                        <Link
                            v-for="item in navItems"
                            :key="item.name"
                            :href="item.href"
                            @click="isMenuOpen = false"
                            class="py-3 text-[var(--color-gray-700)] hover:text-[var(--color-rose-600)] font-medium border-b border-[var(--color-gray-100)] last:border-0"
                        >
                            {{ item.name }}
                        </Link>
                    </nav>
                    <div class="mt-6 pt-6 border-t border-[var(--color-gray-100)]">
                        <a
                            href="tel:+74951234567"
                            class="flex items-center justify-center space-x-2 text-[var(--color-gray-700)] hover:text-[var(--color-rose-600)] mb-4"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span class="font-semibold">+7 (495) 123-45-67</span>
                        </a>
                        <Link
                            :href="route('services.index')"
                            @click="isMenuOpen = false"
                            class="block w-full btn-gradient-primary text-center"
                        >
                            Записаться онлайн
                        </Link>
                    </div>
                </div>
            </div>
        </header>

        <!-- Основной контент -->
        <main>
            <slot/>
        </main>

        <!-- Футер -->
        <footer class="bg-gradient-rose-pink-dark text-white mt-20">
            <div class="container mx-auto px-4 lg:px-8 py-12">
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                    <!-- Колонка 1: О компании -->
                    <div class="lg:col-span-2">
                        <Link :href="route('home')" class="flex items-center space-x-3 mb-6">
                            <div class="w-12 h-12 bg-gradient-rose-pink rounded-2xl flex items-center justify-center">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-2xl font-bold">Feminaluxe</div>
                                <div class="text-sm text-white/80">Перманентный макияж премиум-класса</div>
                            </div>
                        </Link>
                        <p class="text-white/80 mb-6 max-w-lg">
                            Студия профессионального перманентного макияжа в Москве.
                            Мы создаем естественную красоту, которая сохраняется годами.
                        </p>
                        <div class="flex space-x-4">
                            <a href="#"
                               class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-colors">
                                <span class="sr-only">Instagram</span>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073z"/>
                                </svg>
                            </a>
                            <a href="#"
                               class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-colors">
                                <span class="sr-only">VK</span>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M15.684 0H8.316C1.592 0 0 1.592 0 8.316v7.368C0 22.408 1.592 24 8.316 24h7.368C22.408 24 24 22.408 24 15.684V8.316C24 1.592 22.408 0 15.684 0zm3.692 17.123h-1.744c-.66 0-.864-.525-2.05-1.727-1.033-1-1.49-1.135-1.744-1.135-.356 0-.458.102-.458.593v1.575c0 .424-.135.678-1.253.678-1.846 0-3.896-1.118-5.335-3.202C4.624 12.75 3.802 9.944 3.802 9.944c0-.254.102-.491.593-.491h1.744c.44 0 .61.203.78.677.863 2.49 2.303 4.675 2.896 4.675.22 0 .322-.102.322-.66V9.964c-.068-1.186-.695-1.287-.695-1.71 0-.203.17-.407.44-.407h2.744c.373 0 .508.203.508.643v3.473c0 .372.17.508.271.508.22 0 .407-.136.813-.542 1.254-1.406 2.151-3.574 2.151-3.574.119-.254.305-.491.762-.491h1.744c.525 0 .644.271.525.643-.22.73-2.354 4.065-2.354 4.065-.186.305-.254.44 0 .78.186.254.796.78 1.203 1.253.746.847 1.32 1.558 1.473 2.05.17.525-.085.795-.576.795z"/>
                                </svg>
                            </a>
                            <a href="#"
                               class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-colors">
                                <span class="sr-only">Telegram</span>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.41 1-.67 1-.58.05-1.02-.38-1.58-.75-.88-.57-1.38-.93-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69.01-.03.01-.14-.06-.2-.07-.06-.17-.04-.24-.02-.1.02-1.79 1.14-5.06 3.35-.48.33-.91.49-1.3.48-.43-.01-1.25-.24-1.86-.44-.75-.25-1.35-.38-1.3-.8.03-.24.37-.48 1.02-.74 4.05-1.73 6.75-2.88 8.1-3.45 3.85-1.62 4.64-1.9 5.17-1.9.11 0 .38.03.55.18.13.12.16.28.18.43.01.1.01.32.01.55-.01.22-.01.41-.02.56z"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Колонка 2: Навигация -->
                    <div>
                        <h3 class="text-lg font-semibold mb-6">Навигация</h3>
                        <ul class="space-y-3">
                            <li v-for="item in navItems" :key="item.name">
                                <Link
                                    :href="item.href"
                                    class="text-white/80 hover:text-white transition-colors"
                                >
                                    {{ item.name }}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Колонка 3: Контакты -->
                    <div>
                        <h3 class="text-lg font-semibold mb-6">Контакты</h3>
                        <div class="space-y-4">
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-white/90 mt-0.5 flex-shrink-0" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="text-white/80">Москва, ул. Красоты, 10</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <svg class="w-5 h-5 text-white/90 flex-shrink-0" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                <a href="tel:+74951234567" class="text-white/80 hover:text-white transition-colors">
                                    +7 (495) 123-45-67
                                </a>
                            </div>
                            <div class="flex items-center space-x-3">
                                <svg class="w-5 h-5 text-white/90 flex-shrink-0" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-white/80">Ежедневно с 9:00 до 21:00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Копирайт -->
                <div class="border-t border-white/20 mt-8 pt-8 text-center text-white/60 text-sm">
                    <p>&copy; 2025 Feminaluxe. Все права защищены.</p>
                    <p class="mt-2">
                        <Link :href="'#'" class="hover:text-white/80 transition-colors">Политика конфиденциальности
                        </Link>
                        <span class="mx-2">•</span>
                        <Link :href="'#'" class="hover:text-white/80 transition-colors">Пользовательское соглашение
                        </Link>
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import {Link} from '@inertiajs/vue3';
import {ref, computed} from 'vue';
import {route} from "ziggy-js";

const isMenuOpen = ref(false);
const isScrolled = ref(false);

// Навигационные пункты
const navItems = [
    {name: 'Услуги', href: route('services.index')},
    {name: 'Цены', href: '#pricing'},
    {name: 'Работы', href: '#portfolio'},
    {name: 'Отзывы', href: '#reviews'},
    {name: 'Специалисты', href: '#specialists'},
    {name: 'Контакты', href: '#contacts'},
];

// Обработка скролла для изменения хедера
if (typeof window !== 'undefined') {
    window.addEventListener('scroll', () => {
        isScrolled.value = window.scrollY > 50;
    });
}
</script>

<style>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fadeIn {
    animation: fadeIn 0.3s ease-out;
}

/* Плавный скролл для якорных ссылок */
html {
    scroll-behavior: smooth;
}
</style>
