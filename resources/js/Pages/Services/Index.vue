<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ServiceCard from '@/Components/ServiceCard.vue';
import { ChevronRightIcon, SparklesIcon, ShieldCheckIcon, ClockIcon } from '@heroicons/vue/24/outline';

// 2. Получаем данные, КОНКРЕТНО ТЕ, что передает ServiceController
const props = defineProps({
  services: Array,     // Упорядоченный массив услуг с полем category
  categories: Array,   // Массив категорий от CategoryService
  popular_services: Array, // Популярные услуги
  meta: Object        // SEO-метаданные от ServiceSeoService
});

// 3. ПРОСТАЯ логика группировки для отображения.
//    Услуги уже пришли отсортированными по category из контроллера.
const displayServices = () => {
  const grouped = [];
  let currentCategory = null;

  props.services.forEach(service => {
    if (service.category !== currentCategory) {
      // Новая категория - создаем группу
      currentCategory = service.category;
      grouped.push({
        category: currentCategory,
        services: [service]
      });
    } else {
      // Та же категория - добавляем к последней группе
      grouped[grouped.length - 1].services.push(service);
    }
  });

  return grouped;
};
</script>

<template>
  <!-- 4. SEO: Используем метаданные из контроллера -->
  <Head>
    <title>{{ meta.title }}</title>
    <meta name="description" :content="meta.description" />
    <meta name="keywords" :content="meta.keywords?.join(', ')" />
    <!-- Структурированные данные и Open Graph уже добавлены контроллером через withViewData -->
  </Head>

  <AppLayout>
    <!-- 5. Hero-секция (можно доработать, используя popular_services) -->
    <section class="relative bg-gradient-to-r from-pink-50 to-purple-50 py-16 md:py-20 overflow-hidden">
      <div class="absolute inset-0 opacity-5 bg-grid-pattern"></div>
      <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-4xl mx-auto text-center">
          <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 mb-6 leading-tight">
            Услуги <span class="text-transparent bg-clip-text bg-gradient-to-r from-rose-600 to-purple-600">перманентного макияжа</span> в Москве
          </h1>
          <p class="text-lg md:text-xl text-gray-700 mb-10 max-w-3xl mx-auto">
            Профессиональный татуаж бровей, губ и межресничного пространства от мастеров с медицинским образованием.
            Используем только сертифицированные пигменты европейского производства.
          </p>

          <!-- Быстрые факты -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 shadow-lg">
              <div class="flex items-center justify-center mb-4">
                <ShieldCheckIcon class="h-10 w-10 text-green-500" />
              </div>
              <div class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">100%</div>
              <div class="text-gray-600">Гипоаллергенные материалы</div>
            </div>
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 shadow-lg">
              <div class="flex items-center justify-center mb-4">
                <ClockIcon class="h-10 w-10 text-blue-500" />
              </div>
              <div class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">3 года</div>
              <div class="text-gray-600">Гарантия на стойкость</div>
            </div>
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 shadow-lg">
              <div class="flex items-center justify-center mb-4">
                <SparklesIcon class="h-10 w-10 text-primary" />
              </div>
              <div class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">5000+</div>
              <div class="text-gray-600">Довольных клиенток</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 6. Основной каталог услуг -->
    <section id="services-catalog" class="py-16 bg-white">
      <div class="container mx-auto px-4">
        <!-- Простая навигация по категориям (опционально) -->
        <div class="flex flex-wrap justify-center gap-3 mb-12" v-if="categories && categories.length">
          <Link
            :href="route('services.index')"
            :class="['px-5 py-2 rounded-full font-medium transition-all duration-300', $page.url === route('services.index') ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200']"
          >
            Все услуги
          </Link>
          <Link
            v-for="cat in categories"
            :key="cat.id"
            :href="route('services.category', cat.slug)"
            :class="['px-5 py-2 rounded-full font-medium transition-all duration-300', $page.url.includes('/category/' + cat.slug) ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200']"
          >
            {{ cat.name }} ({{ cat.count }})
          </Link>
        </div>

        <!-- 7. Отображение услуг, сгруппированных по категории -->
        <div v-if="services.length">
          <template v-for="group in displayServices()" :key="group.category">
            <div class="mb-16 last:mb-0">
              <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-gray-900">{{ group.category }}</h2>
                <!-- Ссылка на страницу категории (используем slug из categories) -->
                <Link
                  v-if="categories"
                  :href="route('services.category', categories.find(c => c.name === group.category)?.slug || group.category.slug)"
                  class="flex items-center text-primary hover:text-primary-dark font-medium transition-colors duration-300"
                >
                  Все услуги категории
                  <ChevronRightIcon class="h-4 w-4 ml-1" />
                </Link>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <ServiceCard
                  v-for="service in group.services"
                  :key="service.id"
                  :service="service"
                  class="h-full"
                />
              </div>
            </div>
          </template>
        </div>

        <!-- Состояние "нет услуг" -->
        <div v-else class="text-center py-16">
          <div class="text-gray-400 mb-4">
            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h3 class="text-xl font-semibold text-gray-700 mb-2">Услуги временно недоступны</h3>
          <p class="text-gray-500">Мы обновляем прайс-лист. Пожалуйста, зайдите позже или свяжитесь с нами.</p>
        </div>

        <!-- 8. Блок популярных услуг (если передан popular_services) -->
        <div v-if="popular_services && popular_services.length" class="mt-24">
          <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">Самые популярные услуги</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <ServiceCard
              v-for="service in popular_services"
              :key="'popular-' + service.id"
              :service="service"
              :highlight="true"
              class="h-full"
            />
          </div>
        </div>

        <!-- 9. Призыв к действию -->
        <div class="mt-20 text-center">
          <div class="bg-gradient-to-r from-rose-600 to-purple-600 rounded-3xl p-8 md:p-12 text-white">
            <h3 class="text-2xl md:text-3xl font-bold mb-4">Нужна консультация мастера?</h3>
            <p class="text-lg md:text-xl mb-8 opacity-90">Подберём идеальную технику и цвет пигмента под ваш тип внешности</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
              <Link
                :href="route('bookings.create')"
                class="px-8 py-4 bg-white text-primary font-bold rounded-full hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-lg"
              >
                Записаться онлайн
              </Link>
              <a
                href="https://wa.me/79991234567"
                target="_blank"
                class="px-8 py-4 bg-transparent border-2 border-white text-white font-bold rounded-full hover:bg-white/10 transition-all duration-300"
              >
                Написать в WhatsApp
              </a>
            </div>
            <p class="mt-6 text-sm opacity-75">
              * При записи онлайн — скидка 10% на первую процедуру
            </p>
          </div>
        </div>
      </div>
    </section>
  </AppLayout>
</template>

<style scoped>
.bg-grid-pattern {
  background-image:
    linear-gradient(to right, rgba(0,0,0,0.05) 1px, transparent 1px),
    linear-gradient(to bottom, rgba(0,0,0,0.05) 1px, transparent 1px);
  background-size: 40px 40px;
}
</style>
