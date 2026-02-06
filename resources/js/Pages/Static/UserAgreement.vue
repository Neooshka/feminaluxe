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
                        Дата вступления в силу: {{ formatDate(effectiveDate) }}
                    </p>
                </div>

                <!-- Контент -->
                <div class="prose prose-lg max-w-none text-gray-700">
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">1. Общие положения</h2>
                        <p class="mb-4">
                            Настоящее Пользовательское соглашение (далее — Соглашение) регулирует отношения между 
                            <strong>{{ company.name }}</strong> (далее — Исполнитель) и пользователем сайта 
                            <strong>feminaluxe.ru</strong> (далее — Пользователь).
                        </p>
                        <p class="bg-gray-50 p-4 rounded-lg">
                            <strong>Важно:</strong> Используя Сайт, вы подтверждаете, что ознакомились с условиями 
                            Соглашения, <Link :href="links.privacy" class="text-blue-600 hover:underline">Политикой конфиденциальности</Link> 
                            и принимаете их в полном объеме.
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">2. Услуги и порядок их оказания</h2>
                        <h3 class="text-xl font-medium text-gray-900 mb-3">2.1. Бронирование услуг</h3>
                        <p class="mb-4">
                            Для предварительного бронирования необходимо:
                        </p>
                        <ol class="list-decimal pl-6 mb-4">
                            <li>Заполнить форму бронирования на Сайте</li>
                            <li>Дождаться подтверждения бронирования по телефону или SMS</li>
                            <li>Согласовать время, услуги и предварительную стоимость</li>
                        </ol>
                        <p class="mb-4 text-red-600 font-medium">
                            Предварительная стоимость может быть изменена после очной консультации и осмотра.
                        </p>

                        <h3 class="text-xl font-medium text-gray-900 mb-3 mt-6">2.2. Порядок оплаты</h3>
                        <ul class="list-disc pl-6 mb-4">
                            <li>Полный расчет производится по окончании процедуры</li>
                            <li>Формы оплаты: наличные (рубли) или СБП (система быстрых платежей)</li>
                            <li>Окончательная стоимость озвучивается до начала работ</li>
                        </ul>

                        <h3 class="text-xl font-medium text-gray-900 mb-3 mt-6">2.3. Предоплата</h3>
                        <p class="mb-4">
                            В некоторых случаях может потребоваться внесение предоплаты. Условия возврата:
                        </p>
                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200 mb-4">
                            <p class="font-medium">Предоплата НЕ ВОЗВРАЩАЕТСЯ полностью в случаях:</p>
                            <ol class="list-decimal pl-6 mt-2">
                                <li>Неявки в указанное время без предупреждения не менее чем за 48 часов</li>
                                <li>Опоздания на 30 минут и более без предварительного предупреждения</li>
                            </ol>
                        </div>
                        <p>
                            Во всех остальных случаях предоплата возвращается в полном объеме.
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">3. Права и обязанности сторон</h2>
                        <h3 class="text-xl font-medium text-gray-900 mb-3">3.1. Обязанности Исполнителя</h3>
                        <ul class="list-disc pl-6 mb-6">
                            <li>Оказать услуги в соответствии с предварительной договоренностью</li>
                            <li>Обеспечить безопасные условия оказания услуг</li>
                            <li>Не разглашать конфиденциальную информацию о Пользователе</li>
                        </ul>

                        <h3 class="text-xl font-medium text-gray-900 mb-3">3.2. Обязанности Пользователя</h3>
                        <ul class="list-disc pl-6 mb-6">
                            <li>Предоставить достоверную информацию при бронировании</li>
                            <li>Своевременно прибыть на процедуру или предупредить об отмене</li>
                            <li>Соблюдать рекомендации по уходу после процедуры</li>
                        </ul>

                        <h3 class="text-xl font-medium text-gray-900 mb-3">3.3. Возрастные ограничения</h3>
                        <p class="bg-gray-50 p-4 rounded-lg">
                            Услуги могут быть оказаны лицам, достигшим 18 лет.
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">4. Ответственность</h2>
                        <h3 class="text-xl font-medium text-gray-900 mb-3">4.1. Коррекция услуг</h3>
                        <p class="mb-4">
                            Коррекция перманентного макияжа всегда оказывается на платной основе 
                            и не входит в стоимость первоначальных услуг.
                        </p>

                        <h3 class="text-xl font-medium text-gray-900 mb-3">4.2. Место оказания услуг</h3>
                        <p>
                            Все услуги оказываются на территории Мастера по адресу: 
                            {{ pageProps.config.contact.address }}
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">5. Порядок разрешения споров</h2>
                        <p class="mb-4">
                            Все споры разрешаются в претензионном порядке. Срок рассмотрения претензии — 
                            30 календарных дней с момента получения.
                        </p>
                        <p class="mb-4">
                            Претензии направляются на email: 
                            <a :href="`mailto:${company.email}`" class="text-blue-600 hover:underline">
                                {{ company.email }}
                            </a>
                        </p>
                        <p>
                            В случае неурегулирования спора в претензионном порядке, дело передается в суд 
                            по месту нахождения Исполнителя.
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">6. Реквизиты Исполнителя</h2>
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <p class="font-medium mb-2">{{ company.name }}</p>
                            <p class="mb-1"><strong>ИНН:</strong> {{ company.inn }}</p>
                            <p class="mb-1"><strong>ОГРН:</strong> {{ company.ogrn }}</p>
                            <p class="mb-1"><strong>Юридический адрес:</strong> {{ company.address }}</p>
                            <p class="mb-1">
                                <strong>Email: </strong> 
                                <a :href="`mailto:${company.email}`" class="text-blue-600 hover:underline">
                                    {{ company.email }}
                                </a>
                            </p>
                            <p class="mb-3">
                                <strong>Телефон: </strong> 
                                <a :href="`tel:${company.phone}`" class="text-blue-600 hover:underline">
                                    {{ company.phone }}
                                </a>
                            </p>
                            
                            <div class="mt-4 pt-4 border-t border-gray-300">
                                <p class="font-medium mb-2">Банковские реквизиты:</p>
                                <p class="mb-1"><strong>Р/с:</strong> {{ company.bank.account }}</p>
                                <p class="mb-1"><strong>Банк:</strong> {{ company.bank.bank_name }}</p>
                                <p class="mb-1"><strong>БИК:</strong> {{ company.bank.bik }}</p>
                                <p><strong>К/с:</strong> {{ company.bank.correspondent }}</p>
                            </div>
                        </div>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">7. Ссылки на страницы</h2>
                        <ul class="list-disc pl-6">
                            <li>
                                <Link :href="links.booking" class="text-blue-600 hover:underline">
                                    Бронирование услуг
                                </Link>
                            </li>
                            <li>
                                <Link :href="links.contact" class="text-blue-600 hover:underline">
                                    Контактная информация
                                </Link>
                            </li>
                            <li>
                                <Link :href="links.agreement" class="text-blue-600 hover:underline">
                                    Пользовательское соглашение (текущая страница)
                                </Link>
                            </li>
                            <li>
                                <Link :href="links.privacy" class="text-blue-600 hover:underline">
                                    Политика конфиденциальности
                                </Link>
                            </li>
                        </ul>
                    </section>

                    <div class="mt-12 pt-8 border-t border-gray-200">
                        <p class="text-gray-600 text-sm">
                            Документ вступил в силу {{ formatDate(effectiveDate) }}.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { defineProps } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { format } from 'date-fns'
import { ru } from 'date-fns/locale'

const { props: pageProps } = usePage();

const props = defineProps({
    title: String,
    effectiveDate: String,
    company: Object,
    links: Object,
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