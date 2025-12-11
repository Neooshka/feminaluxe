<script setup>
import { defineProps, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ServiceCard from '@/Components/ServiceCard.vue';
import ReviewsTeaser from '@/Components/ReviewsTeaser.vue';

const props = defineProps({
  services: Array,
});

const heroImage = ref('/hero-image.jpg');  // Fallback if video not supported
</script>

<template>
  <Head>
    <title>Feminaluxe: Перманентный макияж бровей, губ и межресничек в Москве</title>
    <meta name="description" content="Профессиональный перманентный макияж – естественная красота на годы. Пудровое напыление бровей, акварельный татуаж губ, межресничный перманент. Запишитесь онлайн!" />
    <!-- JSON-LD for SEO rich snippets -->
    <script type="application/ld+json">
      {
        "@context": "https://schema.org",
        "@type": "LocalBusiness",
        "name": "Feminaluxe",
        "description": "Студия перманентного макияжа в Москве: брови, губы, веки. Естественный результат, безопасность, премиум-пигменты.",
        "url": "{{ route('home') }}",
        "telephone": "+7 (495) 123-45-67",
        "address": {
          "@type": "PostalAddress",
          "streetAddress": "ул. Красоты, 10",
          "addressLocality": "Москва",
          "postalCode": "123456",
          "addressCountry": "RU"
        },
        "openingHoursSpecification": {
          "@type": "OpeningHoursSpecification",
          "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
          "opens": "09:00",
          "closes": "18:00"
        }
      }
    </script>
  </Head>

  <AppLayout>
    <!-- Hero section с видео/изображениями, CTA -->
    <section class="hero relative h-[80vh] flex items-center justify-center overflow-hidden bg-cover bg-center" :style="{ backgroundImage: `url(${heroImage})` }" aria-labelledby="hero-title">
      <video autoplay loop muted playsinline class="absolute w-full h-full object-cover brightness-75" poster="{{ heroImage }}">
        <source src="/hero-video.webm" type="video/webm">
        <source src="/hero-video.mp4" type="video/mp4">
        Your browser does not support the video tag.
      </video>
      <div class="relative z-10 text-center text-white px-6 md:px-0">
        <h1 id="hero-title" class="text-4xl md:text-6xl font-bold mb-6 leading-tight">Перманентный макияж бровей, губ и межресничек в Москве</h1>
        <p class="text-xl md:text-2xl mb-8">Естественная красота без ежедневного макияжа. Профессионально, безопасно, с гарантией.</p>
<!--        <Link :href="route('contact')" class="bg-accent text-primary px-8 py-4 rounded-full text-lg font-bold hover:shadow-xl transition-all duration-300">Записаться</Link>-->
      </div>
    </section>

    <!-- Description section with SEO keys -->
    <section class="description py-16 bg-white">
      <div class="container mx-auto px-4">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-8 text-primary">Перманентный макияж – революция в beauty-уходе в Москве!</h2>
        <p class="text-lg text-gray-700 leading-relaxed mb-6">🔹 Идеальная форма перманентного макияжа бровей, губ и межресничек, которая вам идет. Не просто «стрелки» или «брови», а индивидуальный рисунок, созданный с учетом овала лица, разреза глаз, цветотипа и ваших пожеланий. Мы создаем гармонию, а не шаблон для перманентного макияжа.</p>
        <p class="text-lg text-gray-700 leading-relaxed mb-6">🔹 Естественность перманентного макияжа — наш главный принцип. Пигменты подбираются так, чтобы цвет выглядел натурально, а техника нанесения — микроволосковая, пудровая — создает эффект легкой тонировки, а не нарисованного фломастером для бровей и губ.</p>
        <p class="text-lg text-gray-700 leading-relaxed mb-6">🔹 Безопасность и стерильность перманентного макияжа. Работаем только одноразовыми расходниками, используем гипоаллергенные пигменты премиум-класса, сертифицированные в ЕАЭС для услуг перманентного макияжа в Москве.</p>
        <p class="text-lg text-gray-700 leading-relaxed mb-6">💖 Выберите свой вариант перманентного макияжа: ✔ Пудровые брови – мягкая растушевка и естественная густота. ✔ Межресничка – взгляд становится глубже без ежедневной подводки. ✔ Акварельные губы – нежный оттенок и четкий контур. ✔️ Удаление татуажа ремувером в Москве.</p>
        <p class="text-lg text-gray-700 leading-relaxed">📲 Запишитесь на консультацию по перманентному макияжу и получите скидку на первую процедуру! Красота не требует компромиссов! 💄❤</p>
      </div>
    </section>

    <!-- Услуги -->
    <section class="services py-16 bg-gray-50">
      <h2 class="text-3xl md:text-4xl font-bold text-center mb-12 text-primary">Наши услуги перманентного макияжа</h2>
      <div class="container mx-auto grid grid-cols-1 md:grid-cols-3 gap-8 px-4">
        <ServiceCard v-for="service in services" :key="service.id" :service="service" />
      </div>
    </section>

    <ReviewsTeaser />
  </AppLayout>
</template>
