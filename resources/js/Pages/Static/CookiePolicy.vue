<template>
    <AppLayout :title="title">
        <div class="bg-white">
            <div class="container mx-auto px-4 py-12 max-w-4xl">
                <!-- Хлебные крошки -->
                <nav class="mb-8">
                    <ol class="flex items-center space-x-2 text-sm text-gray-600">
                        <li>
                            <Link :href="route('home')" class="hover:text-gray-900">Главная</Link>
                        </li>
                        <li>/</li>
                        <li class="text-gray-900 font-medium">{{ title }}</li>
                    </ol>
                </nav>

                <!-- Заголовок -->
                <div class="mb-10">
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        {{ title }}
                    </h1>
                    <p class="text-gray-600">
                        Последнее обновление: {{ formatDate(lastUpdated) }}
                    </p>
                </div>

                <!-- Контент -->
                <div class="prose prose-lg max-w-none text-gray-700">
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">1. Что такое файлы cookie?</h2>
                        <p>
                            Файлы cookie — это небольшие текстовые файлы, которые сохраняются на вашем устройстве 
                            при посещении сайта. Они необходимы для корректной работы сайта и не содержат 
                            персональных данных.
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">2. Какие cookie мы используем?</h2>
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-xl font-medium text-gray-900 mb-3">Технические (сессионные) cookie</h3>
                            <p class="mb-3">
                                <strong>Назначение:</strong> обеспечение работы основных функций сайта 
                                (сохранение данных форм, управление сессиями пользователя).
                            </p>
                            <p>
                                <strong>Срок хранения:</strong> до закрытия браузера или в течение 24 часов.
                            </p>
                        </div>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">3. Управление cookie</h2>
                        <p class="mb-4">
                            Вы можете управлять файлами cookie через настройки вашего браузера:
                        </p>
                        <ul class="list-disc pl-6 mb-4">
                            <li>Google Chrome: Настройки → Конфиденциальность и безопасность → Файлы cookie</li>
                            <li>Mozilla Firefox: Настройки → Приватность и защита → Куки и данные сайтов</li>
                            <li>Safari: Настройки → Конфиденциальность → Управление данными сайтов</li>
                            <li>Яндекс.Браузер: Настройки → Сайты → Дополнительные настройки контента → Файлы cookie</li>
                        </ul>
                        <p class="text-red-600 font-medium">
                            Внимание: отключение технических cookie может привести к некорректной работе сайта.
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">4. Правовая основа</h2>
                        <p>
                            Использование технических cookie осуществляется на основании п. 5 ст. 6 Федерального 
                            закона № 152-ФЗ «О персональных данных» — для исполнения договора, стороной которого 
                            является субъект персональных данных.
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">5. Контакты</h2>
                        <p>
                            По вопросам использования файлов cookie обращайтесь:
                        </p>
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                            <p><strong>ИП Украинцев Дмитрий Вячеславович</strong></p>
                            <p>Email: <a href="mailto:neeeo@mail.ru" class="text-blue-600 hover:underline">neeeo@mail.ru</a></p>
                            <p>Телефон: <a href="tel:+79937598180" class="text-blue-600 hover:underline">+7 (993) 759-81-80</a></p>
                        </div>
                    </section>

                    <div class="mt-12 pt-8 border-t border-gray-200">
                        <p class="text-gray-600 text-sm">
                            Документ вступил в силу {{ formatDate(lastUpdated) }}.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { defineProps } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { format } from 'date-fns'
import { ru } from 'date-fns/locale'

const props = defineProps({
    title: String,
    lastUpdated: String,
})

const formatDate = (dateString) => {
    return format(new Date(dateString), 'dd.MM.yyyy', { locale: ru })
}
</script>

<style scoped>
.prose a {
    color: #2563eb;
    text-decoration: underline;
}

.prose a:hover {
    color: #1d4ed8;
}
</style>