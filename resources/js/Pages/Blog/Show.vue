<script setup>
import { defineProps } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { marked } from 'marked';
import {route} from "ziggy-js";  // Assume npm install marked for markdown parsing

const props = defineProps({
  post: Object,
});

const parsedContent = marked(props.post.content);
</script>

<template>
  <AppLayout>
    <article class="py-12 max-w-4xl mx-auto">
      <h1 class="text-4xl font-bold mb-4 text-primary">{{ post.title }}</h1>
      <p class="text-gray-500 mb-6">{{ post.published_at }}</p>
      <img v-if="post.featured_image" :src="post.featured_image" alt="Featured image for {{ post.title }}" class="w-full h-auto mb-8 rounded-lg" loading="lazy">
      <div class="prose lg:prose-xl" v-html="parsedContent"></div>
      <div class="text-center mt-12">
        <Link :href="route('blog')" class="text-primary hover:underline">Вернуться в блог</Link>
      </div>
    </article>
  </AppLayout>
</template>
