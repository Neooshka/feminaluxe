<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { route } from "ziggy-js";
</script>

<template>
  <AppLayout>
    <section class="py-12">
      <h1 class="text-4xl font-bold text-center mb-8 text-primary">О нас</h1>
      <div class="max-w-3xl mx-auto text-center">
        <img src="/master-photo.jpg" alt="Фото мастера Feminaluxe" class="w-48 h-48 rounded-full mx-auto mb-6" loading="lazy">
        <p class="text-lg mb-6">Feminaluxe — студия перманентного макияжа с опытом более 5 лет. Наши мастера сертифицированы и используют только премиум-пигменты для естественного результата.</p>
        <p class="text-lg mb-6">Мы специализируемся на бровях, губах и межресничках, обеспечивая безопасность и комфорт. Тысячи довольных клиентов!</p>
        <div class="text-center">
          <Link :href="route('contact')" class="bg-accent text-white px-6 py-3 rounded-lg text-lg hover:bg-opacity-90 transition">Связаться с нами</Link>
        </div>
      </div>
    </section>
  </AppLayout>
</template>
