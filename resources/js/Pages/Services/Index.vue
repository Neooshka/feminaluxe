<script setup>
import { defineProps, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ServiceCard from '@/Components/ServiceCard.vue';

const props = defineProps({
  services: Array,
});

// Group by category in Vue (best practice: reactive, efficient with computed; allows filters/sorts)
const groupedServices = computed(() => {
  return props.services.reduce((acc, service) => {
    const cat = service.category || 'Другое';
    if (!acc[cat]) acc[cat] = [];
    acc[cat].push(service);
    return acc;
  }, {});
});

// For selling: Add schema for OfferCatalog (SEO rich snippets for services list)
const schema = {
  '@context': 'https://schema.org',
  '@type': 'OfferCatalog',
  'name': 'Услуги перманентного макияжа Feminaluxe',
  'itemListElement': Object.entries(groupedServices.value).map(([category, services]) => ({
    '@type': 'OfferCatalog',
    'name': category,
    'itemListElement': services.map(service => ({
      '@type': 'Offer',
      'itemOffered': {
        '@type': 'Service',
        'name': service.name,
        'description': service.description,
      },
      'price': service.price,
      'priceCurrency': 'RUB',
    })),
  })),
};
</script>

<template>
  <Head>
    <title>Услуги перманентного макияжа бровей, губ и межресничек | Feminaluxe Москва</title>
    <meta name="description" content="Прайс-лист: пудровое напыление бровей 5000 руб, акварельный татуаж губ 5000 руб, межресничный татуаж 3500 руб. Коррекция и удаление. Фото до/после." />
    <script type="application/ld+json" v-html="JSON.stringify(schema)"></script>
  </Head>

  <AppLayout>
    <section class="py-16 bg-white">
      <div class="container mx-auto px-4">
        <h1 class="text-4xl md:text-5xl font-bold text-center mb-12 text-primary">Услуги перманентного макияжа в Москве</h1>
        <p class="text-xl text-center mb-16 text-gray-700">Выберите услугу: пудровое напыление бровей, акварельный татуаж губ, межресничный перманент. Цены, описания, фото до/после. Запишитесь онлайн для скидки!</p>

        <!-- Grouped sections with ARIA for accessibility (selling: trust via structure) -->
        <div v-for="(services, category) in groupedServices" :key="category" class="mb-12" role="region" :aria-labelledby="`category-${category}`">
          <h2 :id="`category-${category}`" class="text-3xl font-bold mb-8 text-primary capitalize">{{ category }}</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <ServiceCard v-for="service in services" :key="service.id" :service="service" />
          </div>
        </div>

        <div class="text-center mt-16">
          <Link :href="route('contact')" class="bg-accent text-white px-8 py-4 rounded-full text-xl font-bold hover:shadow-xl transition-all duration-300">Записаться на услугу перманентного макияжа</Link>
        </div>
      </div>
    </section>
  </AppLayout>
</template>
