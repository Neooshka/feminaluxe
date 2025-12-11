<script setup>
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';

const reviews = ref([]);
const page = usePage();

onMounted(async () => {
  const response = await axios.get('/api/reviews', { params: { approved: true, limit: 6 } });  // Assume API
  reviews.value = response.data;
});
</script>

<template>
  <section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
      <h2 class="text-4xl font-bold text-center mb-12 text-primary">Что говорят наши клиенты</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div v-for="review in reviews" :key="review.id" class="bg-white p-6 rounded-lg shadow-xl transition-shadow hover:shadow-2xl">
          <p class="font-semibold text-lg mb-2">{{ review.author }}</p>
          <p class="text-gray-600 mb-4 italic">"{{ review.content }}"</p>
          <div class="flex text-accent text-2xl justify-center">{{ '★'.repeat(review.rating) }}{{ '☆'.repeat(5 - review.rating) }}</div>
        </div>
      </div>
      <div class="text-center mt-12">
        <Link :href="route('services.index')" class="bg-accent text-white px-8 py-4 rounded-full text-xl hover:bg-opacity-90 transition">Читать все отзывы</Link>
      </div>
    </div>
  </section>
</template>
