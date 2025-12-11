<script setup>
import { defineProps } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  service: Object,
});
</script>

<template>
  <div class="bg-white rounded-xl shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl hover:scale-105" role="article" aria-labelledby="service-title">
    <img v-if="service.images && service.images.length" :src="service.images[0].thumb" alt="Фото до/после для {{ service.name }} - перманентный макияж {{ service.category }}" class="w-full h-48 object-cover" loading="lazy">
    <div class="p-6">
      <!-- Category display as badge (selling: quick scan, trust via categorization) -->
      <span class="inline-block bg-primary/10 text-primary px-3 py-1 rounded-full text-sm font-medium mb-2">{{ service.category }}</span>
      <h3 id="service-title" class="text-xl font-bold mb-2 text-gray-900">{{ service.name }}</h3>
      <p class="text-gray-600 mb-4 line-clamp-3">{{ service.description }}</p>
      <p class="text-2xl font-bold text-accent mb-4">{{ service.price }} ₽</p>
      <Link :href="`/services/${service.id}`" class="bg-accent text-white px-6 py-2 rounded-full font-bold hover:bg-opacity-90 transition duration-300">Подробнее</Link>
    </div>
  </div>
</template>
