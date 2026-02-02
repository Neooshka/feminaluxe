<script setup>
import { defineProps, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  service: Object,
});

const selectedImage = ref(props.service.images[0]?.full);
</script>

<template>
  <Head>
    <title>{{ service.name }} | Feminaluxe</title>
    <meta name="description" :content="$page.props.seo.description" />
  </Head>

  <AppLayout>
    <section class="py-16 bg-white">
      <div class="container mx-auto px-4">
        <h1 class="text-5xl font-bold mb-6 text-primary">{{ service.name }}</h1>
        <div class="text-xl mb-8 text-gray-700 leading-relaxed" v-html="service.description"></div>
        <p class="text-3xl font-bold text-accent mb-12">{{ service.price }} руб.</p>

        <!-- Галерея с lazy loading и modal -->
        <div class="gallery grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
          <div class="main-image sticky top-20">
            <img :src="selectedImage" :alt="`Основное фото ${service.name}`" class="w-full rounded-lg shadow-xl transition-all duration-300" loading="eager">
          </div>
          <div class="thumbnails grid grid-cols-2 gap-4">
            <img v-for="(image, index) in service.images" :key="index" :src="image.thumb" :alt="`Миниатюра ${service.name}`" class="cursor-pointer rounded-lg shadow-md hover:shadow-xl transition-shadow" @click="selectedImage = image.full" loading="lazy">
          </div>
        </div>

        <section class="reviews py-12">
          <h2 class="text-4xl font-bold mb-8 text-primary">Отзывы о услуге</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div v-for="review in service.reviews" :key="review.id" class="bg-gray-50 p-6 rounded-lg shadow-md">
              <p class="font-semibold text-lg mb-2">{{ review.author }}</p>
              <p class="text-gray-600 mb-4">{{ review.content }}</p>
              <div class="flex text-accent">{{ '★'.repeat(review.rating) }}{{ '☆'.repeat(5 - review.rating) }}</div>
            </div>
          </div>
        </section>

        <div class="text-center mt-12">
          <Link :href="route('contact')" class="bg-accent text-white px-8 py-4 rounded-full text-xl hover:bg-opacity-90 transition">Записаться на {{ service.name }}</Link>
        </div>
      </div>
    </section>
  </AppLayout>
</template>
