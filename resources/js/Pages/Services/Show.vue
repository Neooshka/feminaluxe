<script setup>
import { defineProps, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  service: Object,
});

const selectedImage = ref(props.service.images[0]?.full);
</script>

<template>
  <AppLayout>
    <section class="py-12">
      <h1 class="text-4xl font-bold mb-4 text-primary">{{ service.name }}</h1>
      <p class="text-xl mb-6 text-gray-700">{{ service.description }}</p>
      <p class="text-2xl font-bold text-accent mb-8">{{ service.price }} руб.</p>

      <div class="gallery grid grid-cols-1 md:grid-cols-2 gap-4 mb-12">
        <div class="main-image">
          <img :src="selectedImage" alt="Основное фото услуги {{ service.name }}" class="w-full h-auto rounded-lg shadow-lg" loading="lazy">
        </div>
        <div class="thumbnails grid grid-cols-3 gap-2">
          <img v-for="(image, index) in service.images" :key="index" :src="image.thumb" alt="Миниатюра фото до/после" class="cursor-pointer rounded" @click="selectedImage = image.full" loading="lazy">
        </div>
      </div>

      <section class="reviews mb-12">
        <h2 class="text-3xl font-bold mb-6">Отзывы клиентов</h2>
        <div v-for="review in service.reviews" :key="review.id" class="bg-white p-4 rounded-lg shadow mb-4">
          <p class="font-semibold">{{ review.author }}</p>
          <p class="text-gray-600">{{ review.content }}</p>
          <div class="stars text-accent">Rating: {{ '★'.repeat(review.rating) }}{{ '☆'.repeat(5 - review.rating) }}</div>
        </div>
      </section>

      <div class="text-center">
        <Link :href="'/contact'" class="bg-accent text-white px-6 py-3 rounded-lg text-lg hover:bg-opacity-90 transition">Записаться на {{ service.name }}</Link>
      </div>
    </section>
  </AppLayout>
</template>
