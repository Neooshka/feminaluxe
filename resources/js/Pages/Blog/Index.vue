<script setup>
import { defineProps } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import PostCard from '@/Components/PostCard.vue';

const props = defineProps({
  posts: Array,
  pagination: Object,
});
</script>

<template>
  <AppLayout>
    <section class="py-12">
      <h1 class="text-4xl font-bold text-center mb-8 text-primary">Блог Feminaluxe</h1>
      <p class="text-center text-lg mb-12">Советы по уходу, тренды макияжа и истории успеха.</p>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <PostCard v-for="post in posts" :key="post.id" :post="post" />
      </div>
      <div class="pagination mt-12 text-center" v-html="pagination"></div>
    </section>
  </AppLayout>
</template>
