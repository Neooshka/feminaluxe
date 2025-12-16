<script setup>
import { Link } from '@inertiajs/vue3';
import { StarIcon } from '@heroicons/vue/24/solid';
import { ChatBubbleLeftRightIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  review: {
    type: Object,
    required: true,
    default: () => ({
      id: null,
      author: '',
      content: '',
      rating: 5,
      service_name: '',
      service_slug: '',
      created_at: '',
      created_at_human: '',
      avatar_color: 'from-pink-500 to-rose-500'
    })
  },
  compact: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['review-service']);

const openReviewForService = () => {
  emit('review-service', {
    id: props.review.service_id,
    name: props.review.service_name,
    slug: props.review.service_slug
  });
};
</script>

<template>
  <div
    :class="[
      'bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100',
      compact ? 'p-4' : 'p-6'
    ]"
  >
    <!-- Header -->
    <div class="flex items-start justify-between mb-4">
      <!-- Author Info -->
      <div class="flex items-center">
        <div
          :class="[
            'w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg',
            `bg-gradient-to-br ${review.avatar_color}`
          ]"
        >
          {{ review.author.charAt(0).toUpperCase() }}
        </div>
        <div class="ml-4">
          <div class="font-bold text-gray-900">{{ review.author }}</div>
          <div class="text-sm text-gray-500">{{ review.created_at_human }}</div>
        </div>
      </div>

      <!-- Rating -->
      <div class="flex items-center">
        <div class="flex">
          <StarIcon
            v-for="star in 5"
            :key="star"
            :class="[
              'h-5 w-5',
              star <= review.rating ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300'
            ]"
          />
        </div>
        <span class="ml-2 text-lg font-bold text-gray-900">{{ review.rating }}.0</span>
      </div>
    </div>

    <!-- Content -->
    <div class="mb-4">
      <p class="text-gray-700 leading-relaxed" :class="compact ? 'text-sm line-clamp-3' : 'text-base'">
        {{ review.content }}
      </p>
    </div>

    <!-- Service Info -->
    <div class="pt-4 border-t border-gray-100">
      <div class="flex items-center justify-between">
        <div class="flex items-center text-sm text-gray-600">
          <ChatBubbleLeftRightIcon class="h-4 w-4 mr-2" />
          <span>Об услуге:</span>
          <Link
            v-if="review.service_slug"
            :href="route('services.show', review.service_slug )"
            class="ml-2 font-medium text-primary hover:text-primary-dark hover:underline"
          >
            {{ review.service_name }}
          </Link>
          <span v-else class="ml-2 font-medium">{{ review.service_name }}</span>
        </div>

        <button
          v-if="!compact"
          @click="openReviewForService"
          class="text-sm text-primary hover:text-primary-dark font-medium hover:underline"
        >
          Оставить отзыв
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
