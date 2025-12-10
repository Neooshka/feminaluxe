<script setup>
import { defineProps } from 'vue';
import { Link } from '@inertiajs/vue3';
import {route} from "ziggy-js";

const props = defineProps({
  post: Object,
});
</script>

<template>
  <div class="bg-white rounded-lg shadow-lg overflow-hidden transition-transform hover:scale-105">
    <img :src="post.featured_image" alt="Featured image for {{ post.title }}" class="w-full h-48 object-cover" loading="lazy">
    <div class="p-4">
      <h3 class="text-xl font-semibold mb-2">{{ post.title }}</h3>
      <p class="text-gray-600 mb-4">{{ post.excerpt }}</p>
      <p class="text-sm text-gray-500 mb-4">{{ post.published_at }}</p>
      <Link :href="route('blog.show', post.slug)" class="text-primary hover:underline">Читать полностью</Link>
    </div>
  </div>
</template>
