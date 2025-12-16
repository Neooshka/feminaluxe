<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import {
  CheckCircleIcon,
  CalendarIcon,
  UserIcon,
  PhoneIcon,
  EnvelopeIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
  booking: Object,
  meta: Object,
});
</script>

<template>
  <Head>
    <title>{{ meta.title }}</title>
    <meta name="description" :content="meta.description" />
  </Head>

  <AppLayout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-white py-16 px-4 sm:px-6 lg:px-8">
      <div class="max-w-2xl mx-auto">
        <!-- Success Icon -->
        <div class="text-center mb-8">
          <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-r from-green-100 to-emerald-100 rounded-full mb-6">
            <CheckCircleIcon class="h-12 w-12 text-green-600" />
          </div>
        </div>

        <!-- Success Message -->
        <div class="text-center mb-12">
          <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            Запись успешно создана!
          </h1>
          <p class="text-lg text-gray-600 mb-6">
            Мы свяжемся с вами в течение 15 минут для подтверждения записи.
          </p>
          <div class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-700 rounded-full">
            <CalendarIcon class="h-5 w-5 mr-2" />
            <span>Номер вашей записи: <strong>#{{ booking.id }}</strong></span>
          </div>
        </div>

        <!-- Booking Details -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
          <h2 class="text-xl font-bold text-gray-900 mb-6">
            Детали записи
          </h2>

          <div class="space-y-4">
            <!-- Service -->
            <div class="flex items-start">
              <div class="flex-shrink-0">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                  <span class="text-primary font-bold">💫</span>
                </div>
              </div>
              <div class="ml-4">
                <div class="text-sm text-gray-500">Услуга</div>
                <div class="font-medium text-gray-900">{{ booking.service?.name }}</div>
              </div>
            </div>

            <!-- Date & Time -->
            <div class="flex items-start">
              <div class="flex-shrink-0">
                <CalendarIcon class="h-5 w-5 text-gray-400 mt-1" />
              </div>
              <div class="ml-4">
                <div class="text-sm text-gray-500">Дата и время</div>
                <div class="font-medium text-gray-900">
                  {{ new Date(booking.appointment_time).toLocaleDateString('ru-RU', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                  }) }}
                </div>
              </div>
            </div>

            <!-- Client Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <UserIcon class="h-5 w-5 text-gray-400 mt-1" />
                </div>
                <div class="ml-4">
                  <div class="text-sm text-gray-500">Имя</div>
                  <div class="font-medium text-gray-900">{{ booking.client_name }}</div>
                </div>
              </div>

              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <PhoneIcon class="h-5 w-5 text-gray-400 mt-1" />
                </div>
                <div class="ml-4">
                  <div class="text-sm text-gray-500">Телефон</div>
                  <div class="font-medium text-gray-900">{{ booking.client_phone }}</div>
                </div>
              </div>

              <div class="flex items-start md:col-span-2">
                <div class="flex-shrink-0">
                  <EnvelopeIcon class="h-5 w-5 text-gray-400 mt-1" />
                </div>
                <div class="ml-4">
                  <div class="text-sm text-gray-500">Email</div>
                  <div class="font-medium text-gray-900">{{ booking.client_email }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Next Steps -->
        <div class="bg-blue-50 rounded-2xl p-6 mb-8">
          <h3 class="text-lg font-bold text-gray-900 mb-4">
            Что дальше?
          </h3>
          <ul class="space-y-3">
            <li class="flex items-start">
              <div class="flex-shrink-0 text-blue-500">1.</div>
              <div class="ml-3 text-gray-700">Мы позвоним вам для подтверждения записи</div>
            </li>
            <li class="flex items-start">
              <div class="flex-shrink-0 text-blue-500">2.</div>
              <div class="ml-3 text-gray-700">Вышлем подробную инструкцию по подготовке</div>
            </li>
            <li class="flex items-start">
              <div class="flex-shrink-0 text-blue-500">3.</div>
              <div class="ml-3 text-gray-700">Ответим на все ваши вопросы</div>
            </li>
          </ul>
        </div>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row gap-4">
          <Link
            :href="route('services.index')"
            class="flex-1 px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-300 text-center font-medium"
          >
            Вернуться к услугам
          </Link>
          <a
            href="https://wa.me/79991234567"
            target="_blank"
            class="flex-1 px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-lg hover:shadow-lg transition-all duration-300 text-center font-medium"
          >
            Написать в WhatsApp
          </a>
        </div>

        <!-- FAQ -->
        <div class="mt-12 pt-8 border-t border-gray-200">
          <h3 class="text-lg font-bold text-gray-900 mb-4">
            Частые вопросы
          </h3>
          <div class="space-y-4">
            <div>
              <div class="font-medium text-gray-900 mb-1">
                Как подготовиться к процедуре?
              </div>
              <div class="text-gray-600 text-sm">
                Мы вышлем вам подробную инструкцию на email. Обычно рекомендуется не употреблять алкоголь за 2 дня до процедуры.
              </div>
            </div>
            <div>
              <div class="font-medium text-gray-900 mb-1">
                Что делать, если нужно перенести запись?
              </div>
              <div class="text-gray-600 text-sm">
                Позвоните нам по номеру +7 (999) 123-45-67 или напишите в WhatsApp. Мы поможем подобрать другое время.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
