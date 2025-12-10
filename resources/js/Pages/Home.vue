<script setup>
import { defineProps } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import ServiceCard from '@/Components/ServiceCard.vue';
import ReviewsTeaser from '@/Components/ReviewsTeaser.vue';
import {route} from "ziggy-js";

const props = defineProps({
  services: Array,
});
</script>

<template>
  <AppLayout>
    <section class="hero bg-cover bg-center h-96 flex items-center justify-center text-center text-white"
      style="background-image: url('/hero.jpg');">
      <div class="bg-black/50 p-8 rounded">
        <h1 class="text-4xl font-bold mb-4">Преобразите свой взгляд с Feminaluxe</h1>
        <p class="text-xl mb-6">Профессиональный перманентный макияж бровей, губ и межресничек</p>

        <Link :href="route('contact')" class="bg-accent text-primary px-6 py-3 rounded hover:bg-opacity-90 transition">
          Записаться
        </Link>
      </div>
    </section>

    <section class="services py-12">
      <h2 class="text-3xl font-bold text-center mb-8">Наши услуги</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <ServiceCard v-for="service in services" :key="service.id" :service="service" />
      </div>
    </section>

    <Suspense>
      <ReviewsTeaser />
      <template #fallback><div>Loading...</div></template>
    </Suspense>
  </AppLayout>
</template>
