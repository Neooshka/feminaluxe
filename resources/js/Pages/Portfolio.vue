<script setup>
import { ref, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';  // Assume installed

const images = ref([]);

onMounted(async () => {
  const response = await axios.get('/api/portfolio-images');  // Assume API endpoint aggregating from services
  images.value = response.data;
});
</script>

<template>
  <AppLayout>
    <section class="py-12">
      <h1 class="text-4xl font-bold text-center mb-8 text-primary">Портфолио</h1>
      <p class="text-center text-lg mb-12">Наши лучшие работы: фото до и после перманентного макияжа.</p>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <div v-for="(image, index) in images" :key="index" class="relative group">
          <img :src="image.thumb" alt="Портфолио фото" class="w-full h-auto rounded-lg shadow-lg transition-transform group-hover:scale-105" loading="lazy">
          <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
            <p class="text-white text-center">{{ image.caption }}</p>
          </div>
        </div>
      </div>
      <div class="text-center mt-12">
        <Link :href="'/contact'" class="bg-accent text-white px-6 py-3 rounded-lg text-lg hover:bg-opacity-90 transition">Записаться</Link>
      </div>
    </section>
  </AppLayout>
</template>
