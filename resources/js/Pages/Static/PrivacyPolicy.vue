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
                </div>

                <!-- Контент -->
                <div class="prose prose-lg max-w-none text-gray-700">
                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">1. Общие положения</h2>
                        <p class="mb-4">
                            Настоящая Политика конфиденциальности определяет порядок обработки персональных данных 
                            на сайте <strong>feminaluxe.ru</strong> (далее — Сайт).
                        </p>
                        <p>
                            Оператором персональных данных является {{ company.name }} (ИНН {{ company.inn }}, 
                            ОГРН {{ company.ogrn }}), далее — Оператор.
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">2. Правовые основания обработки</h2>
                        <p class="mb-4">
                            Обработка персональных данных осуществляется на следующих правовых основаниях:
                        </p>
                        <ul class="list-disc pl-6 mb-4">
                            <li>
                                <strong>Согласие субъекта персональных данных</strong> (п. 1 ч. 1 ст. 6 152-ФЗ) — 
                                при заполнении форм на Сайте.
                            </li>
                            <li>
                                <strong>Исполнение договора</strong> (п. 5 ч. 1 ст. 6 152-ФЗ) — для оказания 
                                услуг перманентного макияжа.
                            </li>
                        </ul>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">3. Состав персональных данных</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full border border-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="border border-gray-300 px-4 py-3 text-left">Категория данных</th>
                                        <th class="border border-gray-300 px-4 py-3 text-left">Цель обработки</th>
                                        <th class="border border-gray-300 px-4 py-3 text-left">Срок хранения</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="border border-gray-300 px-4 py-3">ФИО, номер телефона</td>
                                        <td class="border border-gray-300 px-4 py-3">Бронирование услуг, связь с клиентом</td>
                                        <td class="border border-gray-300 px-4 py-3">5 лет с даты бронирования</td>
                                    </tr>
                                    <tr>
                                        <td class="border border-gray-300 px-4 py-3">История бронирований</td>
                                        <td class="border border-gray-300 px-4 py-3">Исполнение договора, учет услуг</td>
                                        <td class="border border-gray-300 px-4 py-3">5 лет</td>
                                    </tr>
                                    <tr>
                                        <td class="border border-gray-300 px-4 py-3">Отзывы (публичные)</td>
                                        <td class="border border-gray-300 px-4 py-3">Информирование потенциальных клиентов</td>
                                        <td class="border border-gray-300 px-4 py-3">Бессрочно, до отзыва субъектом</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">4. Место хранения данных</h2>
                        <p class="mb-4">
                            Обработка и хранение персональных данных осуществляется на защищенных серверах, 
                            расположенных на территории Российской Федерации в соответствии с требованиями 
                            ч. 5 ст. 18 Федерального закона № 152-ФЗ «О персональных данных».
                        </p>
                        <p class="mb-4">
                            Техническая инфраструктура включает:
                        </p>
                        <ul class="list-disc pl-6 mb-4">
                            <li>Веб-сервер с шифрованием передачи данных (HTTPS/TLS 1.3)</li>
                            <li>Систему резервного копирования данных</li>
                            <li>Средства защиты от несанкционированного доступа</li>
                        </ul>
                        <p class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                            <strong>Важно:</strong> Мы не передаем персональные данные третьим лицам, за исключением:
                            <ul class="list-disc pl-6 mt-2">
                                <li>Субподрядчиков для оказания услуг (SMS-сервис)</li>
                                <li>По требованию уполномоченных государственных органов</li>
                            </ul>
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">5. Права субъекта персональных данных</h2>
                        <p class="mb-4">
                            В соответствии со статьей 14 Федерального закона № 152-ФЗ «О персональных данных» 
                            вы имеете право:
                        </p>
                        <ul class="list-disc pl-6 mb-6">
                            <li>На доступ к своим персональным данным</li>
                            <li>На уточнение, блокирование или уничтожение данных</li>
                            <li>На отзыв согласия на обработку персональных данных</li>
                            <li>На обжалование действий или бездействия Оператора</li>
                        </ul>
                        
                        <h3 class="text-xl font-medium text-gray-900 mb-3">5.1. Порядок реализации прав</h3>
                        <p class="mb-4">
                            Для реализации ваших прав направьте письменный запрос:
                        </p>
                        <div class="bg-gray-50 p-6 rounded-lg mb-4">
                            <div class="mb-4">
                                <p class="font-medium mb-2">Способы отправки запроса:</p>
                                <p class="mb-1"><strong>Электронная почта:</strong> {{ company.email }}</p>
                                <p><strong>Почтовый адрес:</strong> {{ company.address }}</p>
                            </div>
                            <div>
                                <p class="font-medium mb-2">Обязательные данные в запросе:</p>
                                <ul class="list-disc pl-6">
                                    <li>ФИО субъекта персональных данных</li>
                                    <li>Номер телефона, указанный при бронировании</li>
                                    <li>Суть обращения с указанием конкретного права</li>
                                    <li>Дата и подпись (для почтового отправления)</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <p class="font-medium text-blue-800 mb-2">Сроки рассмотрения:</p>
                            <p>
                                Оператор обязан рассмотреть ваш запрос в течение <strong>30 календарных дней</strong> 
                                с момента получения, в соответствии с частью 3 статьи 14 Федерального закона № 152-ФЗ.
                            </p>
                            <p class="mt-2 text-sm">
                                О результатах рассмотрения вы будете уведомлены по контактным данным, 
                                указанным в запросе.
                            </p>
                        </div>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">6. Контактная информация</h2>
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <p class="font-medium mb-2">{{ company.name }}</p>
                            <p class="mb-1"><strong>ИНН:</strong> {{ company.inn }}</p>
                            <p class="mb-1"><strong>ОГРН:</strong> {{ company.ogrn }}</p>
                            <p class="mb-1"><strong>Адрес:</strong> {{ company.address }}</p>
                            <p class="mb-1">
                                <strong>Email: </strong> 
                                <a :href="`mailto:${company.email}`" class="text-blue-600 hover:underline">
                                    {{ company.email }}
                                </a>
                            </p>
                            <p>
                                <strong>Телефон: </strong> 
                                <a :href="`tel:${company.phone}`" class="text-blue-600 hover:underline">
                                    {{ company.phone }}
                                </a>
                            </p>
                        </div>
                    </section>

                    <div class="mt-12 pt-8 border-t border-gray-200">
                        <p class="text-gray-600 text-sm">
                            Документ утвержден {{ formatDate(effectiveDate) }} и действует бессрочно.
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
    effectiveDate: String,
    company: Object,
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