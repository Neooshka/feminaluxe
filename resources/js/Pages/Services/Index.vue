<script setup>
import { defineProps } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import ServiceCard from '@/Components/ServiceCard.vue';
import { route } from "ziggy-js";

const props = defineProps({
  services: Array,
});
</script>

<template>
  <AppLayout>
    <section class="py-12">
      <h1 class="text-4xl font-bold text-center mb-8 text-primary">Наши услуги</h1>
      <p class="text-center text-lg mb-12">Выберите идеальный перманентный макияж для вашего образа. Фото до/после и цены ниже.</p>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <ServiceCard v-for="service in services" :key="service.id" :service="service" />
      </div>
      <div class="text-center mt-12">
        <Link href="/contact" class="bg-accent text-white px-6 py-3 rounded-lg text-lg hover:bg-opacity-90 transition">Записаться на консультацию</Link>
      </div>
    </section>
  </AppLayout>
</template>
