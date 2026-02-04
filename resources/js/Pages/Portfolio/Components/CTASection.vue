<!-- resources/js/Pages/Portfolio/Components/CTASection.vue -->
<template>
    <section class="py-20 bg-gradient-to-r from-pink-500 to-purple-600">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl md:text-5xl font-serif font-bold text-white mb-6">
                Хотите стать частью нашего портфолио?
            </h2>
            <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
                Запишитесь на бесплатную консультацию и получите индивидуальный план процедуры
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <Link 
                    :href="route('bookings.create')"
                    class="px-8 py-4 bg-white text-pink-600 font-bold text-lg rounded-xl hover:bg-gray-100 transition shadow-xl hover:shadow-2xl"
                >
                    🗓️ Записаться онлайн
                </Link>
                <Link 
                    :href="route('services.index')"
                    class="px-8 py-4 bg-transparent border-2 border-white text-white font-bold text-lg rounded-xl hover:bg-white/10 transition"
                >
                    📋 Узнать об услугах
                </Link>
            </div>
            <p class="mt-6 text-white/80">
                Или позвоните нам: 
                <a 
                    :href="`tel:${config.contact.phone.replace(/[^0-9+]/g, '')}`"
                    class="font-bold hover:underline"
                >
                    {{ config.contact.phone }}
                </a>
            </p>
        </div>
    </section>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const { props: pageProps } = usePage();
const config = computed(() => pageProps.config);
</script>