<script setup>
import { ref, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import { StarIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
  title: {
    type: String,
    default: 'Отзывы клиентов'
  },
  limit: {
    type: Number,
    default: 3
  }
});

const testimonials = ref([]);

onMounted(async () => {
  // Fetch testimonials from API
  try {
    const response = await fetch(route('api.testimonials.service'));
    const data = await response.json();
    testimonials.value = data.slice(0, props.limit);
  } catch (error) {
    console.error('Error fetching testimonials:', error);
  }
});
</script>

<template>
  <div>
    <h3 class="text-2xl font-bold text-gray-900 mb-8">{{ title }}</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <div
        v-for="testimonial in testimonials"
        :key="testimonial.id"
        class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300"
      >
        <div class="flex items-center mb-4">
          <div class="flex">
            <StarIcon
              v-for="star in 5"
              :key="star"
              :class="[
                'h-5 w-5',
                star <= testimonial.rating ? 'text-yellow-400' : 'text-gray-300'
              ]"
            />
          </div>
          <span class="ml-2 text-sm text-gray-600">
            {{ testimonial.created_at }}
          </span>
        </div>

        <p class="text-gray-700 mb-4 line-clamp-4">{{ testimonial.content }}</p>

        <div class="flex items-center">
          <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center mr-4">
            <span class="text-lg font-bold text-gray-600">
              {{ testimonial.client_name.charAt(0) }}
            </span>
          </div>
          <div>
            <div class="font-medium text-gray-900">{{ testimonial.client_name }}</div>
            <div class="text-sm text-gray-600">{{ testimonial.service_name }}</div>
          </div>
        </div>
      </div>
    </div>

    <div class="text-center mt-8">
      <Link
        :href="route('reviews.index')"
        class="inline-flex items-center text-primary hover:text-primary-dark font-medium transition-colors duration-300"
      >
        Все отзывы
        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </Link>
    </div>
  </div>
</template>
