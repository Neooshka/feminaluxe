<script setup>
import { defineProps } from 'vue';
const props = defineProps({
  service: Object,
});
</script>

<template>
  <div class="bg-white shadow-lg rounded-lg overflow-hidden transition-transform hover:scale-105" role="article" aria-labelledby="service-title">
    <img :src="service.images[0]" alt="Фото до/после для {{ service.name }}" class="w-full h-48 object-cover lazy" loading="lazy">
    <div class="p-4">
      <h3 id="service-title" class="text-xl font-semibold mb-2">{{ service.name }}</h3>
      <p class="text-gray-600 mb-4">{{ service.description }}</p>
      <p class="text-accent font-bold">{{ service.price }} руб.</p>
      <Link :href="`/services/${service.id}`" class="text-primary hover:underline">Подробнее</Link>
    </div>
  </div>
</template>
