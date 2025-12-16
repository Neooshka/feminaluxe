<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import ServiceCard from '@/Components/ServiceCard.vue';
import Breadcrumbs from '@/Components/Common/Breadcrumbs.vue';
import EmptyState from '@/Components/Common/EmptyState.vue';
import {
  ArrowLeftIcon,
  StarIcon,
  ClockIcon,
  ShieldCheckIcon,
  SparklesIcon,
  EyeIcon,
  CurrencyDollarIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
  services: {
    type: Array,
    required: true,
    default: () => []
  },
  category: {
    type: String,
    required: true
  },
  category_slug: {
    type: String,
    required: true
  },
  category_info: {
    type: Object,
    default: () => ({
      icon: '🎀',
      description: '',
      services_count: 0,
      total_views: 0,
      average_rating: 0,
      min_price: 0,
      max_price: 0
    })
  },
  meta: {
    type: Object,
    default: () => ({})
  }
});

// Хлебные крошки для навигации
const breadcrumbs = computed(() => [
  { name: 'Главная', href: route('home'), current: false },
  { name: 'Услуги', href: route('services.index'), current: false },
  {
    name: props.category,
    href: route('services.category', props.category_slug),
    current: true
  }
]);

// Статистика категории из данных контроллера
const categoryStats = computed(() => {
  const services = props.services;

  return {
    minPrice: props.category_info.min_price || Math.min(...services.map(s => s.final_price)),
    maxPrice: props.category_info.max_price || Math.max(...services.map(s => s.final_price)),
    averageRating: props.category_info.average_rating ||
      (services.length > 0
        ? (services.reduce((sum, s) => sum + (s.rating || 0), 0) / services.length).toFixed(1)
        : 0),
    totalServices: props.category_info.services_count || services.length,
    totalViews: props.category_info.total_views || 0,
    hasDiscount: services.some(s => s.discount_percentage > 0),
    averageDuration: services.length > 0
      ? Math.round(services.reduce((sum, s) => sum + (s.duration || 60), 0) / services.length)
      : 60
  };
});

// Описание категории (из category_info или fallback)
const categoryDescription = computed(() =>
  props.category_info.description ||
  `Профессиональные услуги по категории "${props.category}". Высокое качество, гарантированный результат, безопасные материалы.`
);

// Иконка категории (из category_info или fallback)
const categoryIcon = computed(() => props.category_info.icon || '🎀');
</script>

<template>
  <Head>
    <title>{{ meta.title || `${category} | Услуги перманентного макияжа` }}</title>
    <meta
      name="description"
      :content="meta.description || categoryDescription"
    />
    <link rel="canonical" :href="route('services.category', category_slug)" />

    <!-- Open Graph -->
    <meta property="og:title" :content="meta.title || `${category} | Feminaluxe`" />
    <meta property="og:description" :content="meta.description || categoryDescription" />
    <meta property="og:type" content="website" />
    <meta property="og:url" :content="route('services.category', category_slug)" />
    <meta property="og:image" :content="services[0]?.primary_image_url || '/images/og/category-default.jpg'" />

    <!-- Structured Data для категории услуг -->
    <script type="application/ld+json">
      {
        "@context": "https://schema.org",
        "@type": "CollectionPage",
        "name": "{{ category }} - Услуги перманентного макияжа",
        "description": "{{ categoryDescription }}",
        "url": "{{ route('services.category', category_slug) }}",
        "mainEntity": {
          "@type": "ItemList",
          "numberOfItems": {{ services.length }},
          "itemListElement": [
            {{ services.map((service, index) => `{
              "@type": "Service",
              "position": ${index + 1},
              "url": "${route('services.show', { service: service.slug })}",
              "name": "${service.name.replace(/"/g, '\\"')}",
              "description": "${(service.short_description || '').replace(/"/g, '\\"')}",
              "offers": {
                "@type": "Offer",
                "price": ${service.final_price},
                "priceCurrency": "RUB"
              }
            }`).join(',') }}
          ]
        }
      }
    </script>
  </Head>

  <AppLayout>
    <!-- Hero секция категории -->
    <section class="relative bg-gradient-to-br from-gray-50 to-white py-12 md:py-16 overflow-hidden">
      <!-- Декоративные элементы -->
      <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-primary/5 to-transparent rounded-full -translate-y-32 translate-x-32"></div>
      <div class="absolute bottom-0 left-0 w-96 h-96 bg-gradient-to-tr from-purple-100/30 to-transparent rounded-full -translate-x-48 translate-y-48"></div>

      <div class="container mx-auto px-4 relative z-10">
        <!-- Хлебные крошки -->
        <div class="mb-8">
          <Breadcrumbs :items="breadcrumbs" />
        </div>

        <!-- Заголовок категории -->
        <div class="max-w-4xl mx-auto text-center mb-12">
          <div class="inline-flex items-center justify-center w-20 h-20 text-4xl mb-6 bg-white rounded-2xl shadow-lg">
            {{ categoryIcon }}
          </div>

          <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 mb-6">
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
              {{ category }}
            </span>
          </h1>

          <p class="text-lg md:text-xl text-gray-700 mb-8 max-w-3xl mx-auto leading-relaxed">
            {{ categoryDescription }}
          </p>

          <!-- Статистика категории -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-2xl mx-auto">
            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 shadow-sm border border-gray-100">
              <div class="flex items-center justify-center mb-2">
                <SparklesIcon class="h-5 w-5 text-primary" />
              </div>
              <div class="text-2xl font-bold text-gray-900 mb-1">
                {{ categoryStats.totalServices }}
              </div>
              <div class="text-sm text-gray-600">услуг</div>
            </div>

            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 shadow-sm border border-gray-100">
              <div class="flex items-center justify-center mb-2">
                <CurrencyDollarIcon class="h-5 w-5 text-green-500" />
              </div>
              <div class="text-2xl font-bold text-gray-900 mb-1">
                от {{ categoryStats.minPrice.toLocaleString('ru-RU') }}₽
              </div>
              <div class="text-sm text-gray-600">цена</div>
            </div>

            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 shadow-sm border border-gray-100">
              <div class="flex items-center justify-center mb-2">
                <StarIcon class="h-5 w-5 text-yellow-500" />
              </div>
              <div class="text-2xl font-bold text-gray-900 mb-1">
                {{ isNaN(categoryStats.averageRating) ? 0 : categoryStats.averageRating }}
              </div>
              <div class="text-sm text-gray-600">рейтинг</div>
            </div>

            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 shadow-sm border border-gray-100">
              <div class="flex items-center justify-center mb-2">
                <EyeIcon class="h-5 w-5 text-blue-500" />
              </div>
              <div class="text-2xl font-bold text-gray-900 mb-1">
                {{ categoryStats.totalViews.toLocaleString('ru-RU') }}
              </div>
              <div class="text-sm text-gray-600">просмотров</div>
            </div>
          </div>
        </div>

        <!-- Кнопка возврата -->
        <div class="text-center">
          <Link
            :href="route('services.index')"
            class="inline-flex items-center text-primary hover:text-primary-dark font-medium transition-colors duration-300"
          >
            <ArrowLeftIcon class="h-5 w-5 mr-2" />
            Все услуги
          </Link>
        </div>
      </div>
    </section>

    <!-- Основной контент - услуги категории -->
    <section class="py-16 bg-white">
      <div class="container mx-auto px-4">
        <!-- Заголовок раздела -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-12">
          <div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">
              Услуги категории "{{ category }}"
            </h2>
            <p class="text-gray-600">
              Выберите подходящую услугу из {{ services.length }} вариантов
            </p>
          </div>

          <!-- Индикатор скидок -->
          <div v-if="categoryStats.hasDiscount" class="mt-4 md:mt-0">
            <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 rounded-full">
              <SparklesIcon class="h-4 w-4 mr-2" />
              <span class="font-medium">Есть услуги со скидкой!</span>
            </div>
          </div>
        </div>

        <!-- Услуги категории -->
        <div v-if="services.length > 0">
          <!-- Сетка услуг -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <ServiceCard
              v-for="service in services"
              :key="service.id"
              :service="service"
              class="h-full transition-all duration-300 hover:shadow-xl hover:-translate-y-1"
            />
          </div>

          <!-- Информационный блок о категории -->
          <div class="mt-20 bg-gradient-to-r from-gray-50 to-white rounded-2xl p-8 border border-gray-200">
            <h3 class="text-2xl font-bold text-gray-900 mb-6">
              💫 Почему выбирают {{ category.toLowerCase() }} в Feminaluxe?
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <ShieldCheckIcon class="h-8 w-8 text-green-500 mr-4" />
                </div>
                <div>
                  <h4 class="font-semibold text-gray-900 mb-2">Безопасность</h4>
                  <p class="text-gray-600 text-sm">
                    Используем только сертифицированные гипоаллергенные пигменты европейского производства
                  </p>
                </div>
              </div>

              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <ClockIcon class="h-8 w-8 text-blue-500 mr-4" />
                </div>
                <div>
                  <h4 class="font-semibold text-gray-900 mb-2">Долговечность</h4>
                  <p class="text-gray-600 text-sm">
                    Результат сохраняется от 1 до 3 лет в зависимости от типа кожи и ухода
                  </p>
                </div>
              </div>

              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <SparklesIcon class="h-8 w-8 text-primary mr-4" />
                </div>
                <div>
                  <h4 class="font-semibold text-gray-900 mb-2">Естественность</h4>
                  <p class="text-gray-600 text-sm">
                    Создаем максимально натуральный эффект, подбирая цвет и технику индивидуально
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Если услуг нет -->
        <div v-else>
          <EmptyState
            title="Услуги не найдены"
            description="В данной категории пока нет доступных услуг. Попробуйте выбрать другую категорию или свяжитесь с нами для индивидуальной консультации."
            icon="services"
          >
            <div class="flex flex-col sm:flex-row gap-4 justify-center mt-6">
              <Link
                :href="route('services.index')"
                class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors duration-300"
              >
                Все услуги
              </Link>
              <Link
                :href="route('contact')"
                class="px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-300"
              >
                Написать нам
              </Link>
            </div>
          </EmptyState>
        </div>

        <!-- Призыв к действию -->
        <div class="mt-20 text-center">
          <div class="bg-gradient-to-r from-rose-500 via-pink-500 to-purple-500 rounded-3xl p-8 md:p-12 text-white relative overflow-hidden">
            <!-- Декоративные элементы -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16"></div>
            <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/5 rounded-full -translate-x-20 translate-y-20"></div>

            <div class="relative z-10">
              <h3 class="text-2xl md:text-3xl font-bold mb-4">
                Готовы преобразиться?
              </h3>
              <p class="text-lg md:text-xl mb-8 opacity-90 max-w-2xl mx-auto">
                Запишитесь на бесплатную консультацию по услугам {{ category.toLowerCase() }}.
                Наш мастер подберет идеальную технику и цвет пигмента специально для вас.
              </p>

              <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <Link
                  :href="route('bookings.create', { category: category_slug })"
                  class="px-8 py-4 bg-white text-primary font-bold rounded-full hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-lg"
                >
                  Записаться на {{ category.toLowerCase() }}
                </Link>
                <a
                  href="https://wa.me/79991234567"
                  target="_blank"
                  class="px-8 py-4 bg-transparent border-2 border-white text-white font-bold rounded-full hover:bg-white/10 transition-all duration-300"
                >
                  Консультация в WhatsApp
                </a>
              </div>

              <p class="mt-8 text-sm opacity-75">
                ⭐ При записи онлайн — скидка 10% на первую процедуру по категории "{{ category }}"
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </AppLayout>
</template>

<style scoped>
/* Анимации и стили */
@keyframes fade-in-up {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

section {
  animation: fade-in-up 0.6s ease-out;
}
</style>
