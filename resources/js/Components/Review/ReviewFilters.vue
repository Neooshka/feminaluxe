<script setup>
import { MagnifyingGlassIcon, FunnelIcon, XMarkIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  service: [String, Number],
  rating: [String, Number],
  sort: String,
  search: String,
  services: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits([
  'update:service',
  'update:rating',
  'update:sort',
  'update:search',
  'clear-filters'
]);

const sortOptions = [
  { value: 'newest', label: 'Сначала новые' },
  { value: 'oldest', label: 'Сначала старые' },
  { value: 'rating_desc', label: 'Высокий рейтинг' },
  { value: 'rating_asc', label: 'Низкий рейтинг' }
];
</script>

<template>
  <div class="bg-gray-50 rounded-xl p-4">
    <div class="flex flex-col md:flex-row md:items-center gap-4">
      <!-- Search -->
      <div class="flex-1">
        <div class="relative">
          <input
            :value="search"
            @input="$emit('update:search', $event.target.value)"
            type="search"
            placeholder="Поиск по отзывам..."
            class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all duration-300"
          />
          <MagnifyingGlassIcon class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" />
        </div>
      </div>

      <!-- Service Filter -->
      <div class="w-full md:w-48">
        <select
          :value="service"
          @change="$emit('update:service', $event.target.value)"
          class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all duration-300"
        >
          <option value="">Все услуги</option>
          <option
            v-for="serviceItem in services"
            :key="serviceItem.id"
            :value="serviceItem.id"
          >
            {{ serviceItem.name }} ({{ serviceItem.reviews_count }})
          </option>
        </select>
      </div>

      <!-- Rating Filter -->
      <div class="w-full md:w-48">
        <select
          :value="rating"
          @change="$emit('update:rating', $event.target.value)"
          class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all duration-300"
        >
          <option value="">Все оценки</option>
          <option value="5">5 звёзд</option>
          <option value="4">4 звезды</option>
          <option value="3">3 звезды</option>
          <option value="2">2 звезды</option>
          <option value="1">1 звезда</option>
        </select>
      </div>

      <!-- Sort -->
      <div class="w-full md:w-48">
        <select
          :value="sort"
          @change="$emit('update:sort', $event.target.value)"
          class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all duration-300"
        >
          <option
            v-for="option in sortOptions"
            :key="option.value"
            :value="option.value"
          >
            {{ option.label }}
          </option>
        </select>
      </div>

      <!-- Clear Filters -->
      <button
        @click="$emit('clear-filters')"
        class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-300 flex items-center justify-center"
      >
        <XMarkIcon class="h-5 w-5 mr-2" />
        Сбросить
      </button>
    </div>
  </div>
</template>
