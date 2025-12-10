<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { route } from "ziggy-js";

const reviews = ref([]);

onMounted(async () => {
  const response = await axios.get('/api/reviews-teaser');  // Assume endpoint: Review::approved()->latest()->take(3)->get()
  reviews.value = response.data;
});
</script>

<template>
  <section class="py-12 bg-beige-100">
    <h2 class="text-3xl font-bold text-center mb-8 text-primary">Что говорят наши клиенты</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div v-for="review in reviews" :key="review.id" class="bg-white p-6 rounded-lg shadow-lg text-center">
        <p class="font-semibold mb-2">{{ review.author }}</p>
        <p class="text-gray-600 mb-4">"{{ review.content }}"</p>
        <div class="stars text-accent text-2xl">{{ '★'.repeat(review.rating) }}{{ '☆'.repeat(5 - review.rating) }}</div>
      </div>
    </div>
    <div class="text-center mt-8">
      <Link :href="route('services.index')" class="bg-accent text-white px-6 py-3 rounded-lg hover:bg-opacity-90 transition">Узнать больше</Link>
    </div>
  </section>
</template>
