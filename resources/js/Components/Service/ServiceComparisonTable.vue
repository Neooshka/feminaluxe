<script setup>
import { XMarkIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  services: {
    type: Array,
    required: true
  }
});

const emit = defineEmits(['clear-comparison', 'booking-request']);

const comparisonFeatures = [
  { key: 'duration', label: 'Длительность процедуры' },
  { key: 'pain_level', label: 'Болезненность' },
  { key: 'healing_time', label: 'Время заживления' },
  { key: 'duration_effect', label: 'Стойкость результата' },
  { key: 'materials', label: 'Используемые материалы' },
  { key: 'recommended_for', label: 'Рекомендуется для' }
];
</script>

<template>
  <div class="bg-white rounded-2xl shadow-xl p-8">
    <div class="flex items-center justify-between mb-8">
      <h3 class="text-2xl font-bold text-gray-900">Сравнение услуг</h3>
      <button
        @click="$emit('clear-comparison')"
        class="flex items-center text-gray-600 hover:text-gray-900 transition-colors duration-300"
      >
        <XMarkIcon class="h-5 w-5 mr-1" />
        Очистить сравнение
      </button>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full min-w-max">
        <thead>
          <tr>
            <th class="text-left py-4 px-6 bg-gray-50 rounded-l-2xl">Характеристика</th>
            <th
              v-for="service in services"
              :key="service.id"
              class="text-center py-4 px-6 bg-gray-50"
            >
              <div class="flex flex-col items-center">
                <div class="font-bold text-lg mb-2">{{ service.name }}</div>
                <div class="text-primary font-bold text-xl mb-3">{{ service.price }}₽</div>
                <button
                  @click="$emit('booking-request', service)"
                  class="px-4 py-2 bg-primary text-white rounded-full hover:bg-primary-dark transition-colors duration-300 text-sm"
                >
                  Записаться
                </button>
              </div>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="feature in comparisonFeatures"
            :key="feature.key"
            class="border-b border-gray-200 last:border-b-0"
          >
            <td class="py-4 px-6 font-medium text-gray-700 bg-gray-50">
              {{ feature.label }}
            </td>
            <td
              v-for="service in services"
              :key="service.id"
              class="py-4 px-6 text-center"
            >
              {{ service[feature.key] || '—' }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
