<script setup>
import { defineProps } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  images: Array,
});
</script>

<template>
  <Head title="Портфолио | Feminaluxe" />

  <AppLayout>
    <section class="py-16 bg-white">
      <div class="container mx-auto px-4">
        <h1 class="text-5xl font-bold text-center mb-12 text-primary">Наше портфолио</h1>
        <p class="text-xl text-center mb-16 text-gray-700">Реальные результаты: фото до и после. Убедитесь в качестве!</p>
        <div class="columns-1 sm:columns-2 md:columns-3 lg:columns-4 gap-4 space-y-4">
          <div v-for="(image, index) in images" :key="index" class="break-inside-avoid relative group overflow-hidden rounded-lg shadow-md">
            <img :src="image.thumb" alt="Портфолио {{ image.caption }}" class="w-full h-auto transition-transform duration-300 group-hover:scale-110" loading="lazy">
            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
              <p class="text-white text-center font-semibold px-4">{{ image.caption }}</p>
            </div>
          </div>
        </div>
        <div class="text-center mt-16">
          <Link :href="route('contact')" class="bg-accent text-white px-8 py-4 rounded-full text-xl hover:bg-opacity-90 transition">Записаться и стать частью портфолио</Link>
        </div>
      </div>
    </section>
  </AppLayout>
</template>
