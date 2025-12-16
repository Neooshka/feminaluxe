<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Breadcrumbs from '@/Components/Common/Breadcrumbs.vue';
import ReviewCard from '@/Components/Review/ReviewCard.vue';
import ReviewFilters from '@/Components/Review/ReviewFilters.vue';
import ReviewForm from '@/Components/Review/ReviewForm.vue';
import {
  StarIcon,
  ChatBubbleLeftRightIcon,
  FaceSmileIcon,
  FireIcon,
  MagnifyingGlassIcon,
  ArrowRightIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
  reviews: {
    type: Array,
    required: true,
    default: () => []
  },
  services: {
    type: Array,
    default: () => []
  },
  stats: {
    type: Object,
    default: () => ({
      total_reviews: 0,
      average_rating: 0,
      rating_distribution: {},
      services_with_reviews: 0
    })
  },
  filters: {
    type: Object,
    default: () => ({})
  },
  meta: {
    type: Object,
    default: () => ({})
  }
});

// Local state
const activeFilters = ref({
  service: props.filters.service || null,
  rating: props.filters.rating || null,
  sort: props.filters.sort || 'newest',
  search: props.filters.search || ''
});

const showReviewForm = ref(false);
const selectedServiceForReview = ref(null);

// Breadcrumbs
const breadcrumbs = [
  { name: 'Главная', href: route('home'), current: false },
  { name: 'Отзывы', href: route('reviews.index'), current: true }
];

// Apply filters with debounce
let filterTimeout;
watch(activeFilters, (newFilters) => {
  clearTimeout(filterTimeout);
  filterTimeout = setTimeout(() => {
    applyFilters(newFilters);
  }, 300);
}, { deep: true });

const applyFilters = (filters) => {
  router.get(route('reviews.index'), {
    service: filters.service,
    rating: filters.rating,
    sort: filters.sort,
    search: filters.search
  }, {
    preserveState: true,
    preserveScroll: true,
    replace: true
  });
};

const clearFilters = () => {
  activeFilters.value = {
    service: null,
    rating: null,
    sort: 'newest',
    search: ''
  };
};

const openReviewForm = (service = null) => {
  selectedServiceForReview.value = service;
  showReviewForm.value = true;
};

// Computed properties
const hasActiveFilters = computed(() => {
  return activeFilters.value.service ||
         activeFilters.value.rating ||
         activeFilters.value.search;
});

const totalReviewsText = computed(() => {
  const total = props.stats.total_reviews;
  if (total % 10 === 1 && total % 100 !== 11) return `${total} отзыв`;
  if (total % 10 >= 2 && total % 10 <= 4 && (total % 100 < 10 || total % 100 >= 20)) return `${total} отзыва`;
  return `${total} отзывов`;
});

const averageRatingText = computed(() => {
  return props.stats.average_rating.toFixed(1).replace('.', ',');
});
</script>

<template>
  <Head>
    <title>{{ meta.title }}</title>
    <meta name="description" :content="meta.description" />
    <meta name="keywords" :content="meta.keywords" />

    <!-- Structured Data for Reviews -->
    <script type="application/ld+json">
      {
        "@context": "https://schema.org",
        "@type": "CollectionPage",
        "name": "Отзывы о перманентном макияже",
        "description": "{{ meta.description }}",
        "url": "{{ route('reviews.index') }}",
        "mainEntity": {
          "@type": "ItemList",
          "numberOfItems": {{ reviews.length }},
          "itemListElement": [
            {{ reviews.map((review, index) => `{
              "@type": "Review",
              "position": ${index + 1},
              "author": {
                "@type": "Person",
                "name": "${review.author}"
              },
              "reviewRating": {
                "@type": "Rating",
                "ratingValue": "${review.rating}",
                "bestRating": "5"
              },
              "reviewBody": "${review.content.replace(/"/g, '\\"')}",
              "datePublished": "${review.created_at}"
            }`).join(',') }}
          ]
        }
      }
    </script>
  </Head>

  <AppLayout>
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-gray-50 to-white py-12 md:py-16 overflow-hidden">
      <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-primary/5 to-transparent rounded-full -translate-y-32 translate-x-32"></div>
      <div class="absolute bottom-0 left-0 w-96 h-96 bg-gradient-to-tr from-purple-100/30 to-transparent rounded-full -translate-x-48 translate-y-48"></div>

      <div class="container mx-auto px-4 relative z-10">
        <!-- Breadcrumbs -->
        <div class="mb-8">
          <Breadcrumbs :items="breadcrumbs" />
        </div>

        <!-- Header -->
        <div class="max-w-4xl mx-auto text-center mb-12">
          <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 mb-6">
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
              Отзывы клиентов
            </span>
          </h1>

          <p class="text-lg md:text-xl text-gray-700 mb-8 max-w-3xl mx-auto leading-relaxed">
            Реальные истории и впечатления от наших клиентов. Читайте, оценивайте и делитесь своим опытом!
          </p>

          <!-- Quick Stats -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-2xl mx-auto">
            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 shadow-sm border border-gray-100">
              <div class="flex items-center justify-center mb-2">
                <ChatBubbleLeftRightIcon class="h-5 w-5 text-primary" />
              </div>
              <div class="text-2xl font-bold text-gray-900 mb-1">
                {{ totalReviewsText }}
              </div>
              <div class="text-sm text-gray-600">всего отзывов</div>
            </div>

            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 shadow-sm border border-gray-100">
              <div class="flex items-center justify-center mb-2">
                <StarIcon class="h-5 w-5 text-yellow-500" />
              </div>
              <div class="text-2xl font-bold text-gray-900 mb-1">
                {{ averageRatingText }}
              </div>
              <div class="text-sm text-gray-600">средний рейтинг</div>
            </div>

            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 shadow-sm border border-gray-100">
              <div class="flex items-center justify-center mb-2">
                <FaceSmileIcon class="h-5 w-5 text-green-500" />
              </div>
              <div class="text-2xl font-bold text-gray-900 mb-1">
                {{ stats.services_with_reviews }}
              </div>
              <div class="text-sm text-gray-600">услуг с отзывами</div>
            </div>

            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 shadow-sm border border-gray-100">
              <div class="flex items-center justify-center mb-2">
                <FireIcon class="h-5 w-5 text-orange-500" />
              </div>
              <div class="text-2xl font-bold text-gray-900 mb-1">
                98%
              </div>
              <div class="text-sm text-gray-600">довольных клиентов</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Content -->
    <section class="py-16 bg-white">
      <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-8">
          <!-- Left Sidebar - Filters -->
          <div class="lg:w-1/4">
            <div class="sticky top-6 space-y-6">
              <!-- Rating Distribution -->
              <div class="bg-gray-50 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Распределение оценок</h3>
                <div class="space-y-3">
                  <div
                    v-for="rating in 5"
                    :key="rating"
                    class="flex items-center cursor-pointer"
                    @click="activeFilters.rating = activeFilters.rating === rating ? null : rating"
                  >
                    <div class="flex items-center w-20">
                      <div class="flex mr-2">
                        <StarIcon
                          v-for="star in 5"
                          :key="star"
                          :class="[
                            'h-4 w-4',
                            star <= rating ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300'
                          ]"
                        />
                      </div>
                      <span class="text-sm text-gray-600">{{ rating }}</span>
                    </div>
                    <div class="flex-1 ml-4">
                      <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div
                          class="h-full bg-yellow-400 rounded-full"
                          :style="{ width: `${stats.rating_distribution[rating]?.percentage || 0}%` }"
                        />
                      </div>
                    </div>
                    <span class="text-sm text-gray-500 ml-2 w-10">
                      {{ stats.rating_distribution[rating]?.count || 0 }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Services Filter -->
              <div class="bg-gray-50 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Фильтр по услугам</h3>
                <div class="space-y-2">
                  <button
                    @click="activeFilters.service = null"
                    :class="[
                      'w-full text-left px-4 py-2 rounded-lg transition-colors duration-300',
                      !activeFilters.service
                        ? 'bg-primary text-white'
                        : 'bg-white text-gray-700 hover:bg-gray-100'
                    ]"
                  >
                    Все услуги
                  </button>
                  <button
                    v-for="service in services"
                    :key="service.id"
                    @click="activeFilters.service = activeFilters.service === service.id ? null : service.id"
                    :class="[
                      'w-full text-left px-4 py-2 rounded-lg transition-colors duration-300 flex justify-between items-center',
                      activeFilters.service === service.id
                        ? 'bg-primary text-white'
                        : 'bg-white text-gray-700 hover:bg-gray-100'
                    ]"
                  >
                    <span>{{ service.name }}</span>
                    <span class="text-xs px-2 py-1 rounded-full bg-gray-200 text-gray-700">
                      {{ service.reviews_count }}
                    </span>
                  </button>
                </div>
              </div>

              <!-- CTA Button -->
              <button
                @click="openReviewForm()"
                class="w-full px-6 py-3 bg-gradient-to-r from-rose-500 via-pink-500 to-purple-500 text-white font-bold rounded-lg hover:shadow-lg transition-all duration-300"
              >
                Оставить отзыв
              </button>
            </div>
          </div>

          <!-- Main Content - Reviews -->
          <div class="lg:w-3/4">
            <!-- Filters Bar -->
            <div class="mb-8">
              <ReviewFilters
                v-model:service="activeFilters.service"
                v-model:rating="activeFilters.rating"
                v-model:sort="activeFilters.sort"
                v-model:search="activeFilters.search"
                :services="services"
                @clear-filters="clearFilters"
              />

              <!-- Active Filters -->
              <div v-if="hasActiveFilters" class="flex flex-wrap gap-2 mt-4">
                <span class="text-sm text-gray-600">Активные фильтры:</span>

                <span
                  v-if="activeFilters.service"
                  class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-primary/10 text-primary"
                >
                  {{ services.find(s => s.id === activeFilters.service)?.name || 'Услуга' }}
                  <button @click="activeFilters.service = null" class="ml-2 hover:text-primary-dark">×</button>
                </span>

                <span
                  v-if="activeFilters.rating"
                  class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-yellow-100 text-yellow-800"
                >
                  {{ activeFilters.rating }} звезд
                  <button @click="activeFilters.rating = null" class="ml-2 hover:text-yellow-900">×</button>
                </span>

                <span
                  v-if="activeFilters.search"
                  class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800"
                >
                  Поиск: "{{ activeFilters.search }}"
                  <button @click="activeFilters.search = ''" class="ml-2 hover:text-green-900">×</button>
                </span>
              </div>
            </div>

            <!-- Reviews Grid -->
            <div v-if="reviews.length > 0">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <ReviewCard
                  v-for="review in reviews"
                  :key="review.id"
                  :review="review"
                  @review-service="openReviewForm"
                />
              </div>

              <!-- Load More (if paginated) -->
              <div class="text-center mt-12">
                <button
                  @click="applyFilters(activeFilters)"
                  class="px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-300 inline-flex items-center"
                >
                  Показать еще отзывы
                  <ArrowRightIcon class="h-4 w-4 ml-2" />
                </button>
              </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-16">
              <div class="text-gray-400 mb-4">
                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
              </div>
              <h3 class="text-xl font-semibold text-gray-700 mb-2">Отзывы не найдены</h3>
              <p class="text-gray-500 mb-6">Попробуйте изменить параметры фильтрации</p>
              <button
                @click="clearFilters"
                class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors duration-300"
              >
                Сбросить фильтры
              </button>
            </div>

            <!-- Call to Action -->
            <div class="mt-20 bg-gradient-to-r from-primary/5 to-pink-50 rounded-2xl p-8 border border-primary/10">
              <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                  <h3 class="text-2xl font-bold text-gray-900 mb-2">Расскажите о вашем опыте</h3>
                  <p class="text-gray-600">
                    Ваш отзыв поможет другим сделать правильный выбор и станет ценным советом для нас
                  </p>
                </div>
                <div class="flex gap-4">
                  <button
                    @click="openReviewForm()"
                    class="px-6 py-3 bg-primary text-white font-bold rounded-lg hover:bg-primary-dark transition-colors duration-300"
                  >
                    Оставить отзыв
                  </button>
                  <Link
                    :href="route('services.index')"
                    class="px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-300"
                  >
                    Посмотреть услуги
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Review Form Modal -->
    <ReviewForm
      v-if="showReviewForm"
      :initial-service="selectedServiceForReview"
      @close="showReviewForm = false"
    />
  </AppLayout>
</template>

<style scoped>
/* Smooth transitions */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
