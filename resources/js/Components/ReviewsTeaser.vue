<script setup>
import { ref } from 'vue';

const reviews = ref([
  {
    id: 1,
    author: 'Анна К.',
    rating: 5,
    date: '15.12.2024',
    content: 'Делала пудровые брови у мастера Марии. Очень довольна результатом! Брови выглядят естественно, цвет идеально подобрали. Процедура практически безболезненная, мастер все подробно объясняла.',
    service: 'Пудровые брови'
  },
  {
    id: 2,
    author: 'Елена С.',
    rating: 5,
    date: '10.12.2024',
    content: 'Акварельные губы - это просто волшебство! Цвет нежный, естественный. Уже прошла коррекцию, результат превзошел все ожидания. Спасибо Feminaluxe за профессионализм!',
    service: 'Акварельные губы'
  },
  {
    id: 3,
    author: 'Ольга М.',
    rating: 5,
    date: '05.12.2024',
    content: 'Межресничный перманент - мечта любой девушки! Просыпаться с идеальным макияжем - это реальность. Мастер Анастасия - настоящий профессионал, все сделала аккуратно и качественно.',
    service: 'Межресничный перманент'
  }
]);

const currentIndex = ref(0);

const nextReview = () => {
  currentIndex.value = (currentIndex.value + 1) % reviews.value.length;
};

const prevReview = () => {
  currentIndex.value = (currentIndex.value - 1 + reviews.value.length) % reviews.value.length;
};
</script>

<template>
  <section id="reviews" class="py-16 lg:py-24 bg-gradient-to-b from-white to-gray-50">
    <div class="container mx-auto px-4 lg:px-8">
      <!-- Заголовок -->
      <div class="text-center mb-12 lg:mb-16">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-r from-rose-100 to-pink-100 mb-4">
          <svg class="w-6 h-6 text-rose-500" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
          </svg>
        </div>
        <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
          Отзывы наших клиенток
        </h2>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
          Более 1000 довольных клиенток, которые доверили нам свою красоту
        </p>
      </div>

      <!-- Отзывы -->
      <div class="relative max-w-4xl mx-auto">
        <!-- Навигация -->
        <button
          @click="prevReview"
          class="absolute left-0 lg:-left-12 top-1/2 -translate-y-1/2 z-10 w-10 h-10 lg:w-12 lg:h-12 bg-white rounded-full shadow-lg flex items-center justify-center hover:shadow-xl transition-shadow group"
        >
          <svg class="w-5 h-5 lg:w-6 lg:h-6 text-gray-600 group-hover:text-rose-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          <span class="sr-only">Предыдущий отзыв</span>
        </button>

        <button
          @click="nextReview"
          class="absolute right-0 lg:-right-12 top-1/2 -translate-y-1/2 z-10 w-10 h-10 lg:w-12 lg:h-12 bg-white rounded-full shadow-lg flex items-center justify-center hover:shadow-xl transition-shadow group"
        >
          <svg class="w-5 h-5 lg:w-6 lg:h-6 text-gray-600 group-hover:text-rose-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
          <span class="sr-only">Следующий отзыв</span>
        </button>

        <!-- Карточки отзывов -->
        <div class="overflow-hidden">
          <div
            class="flex transition-transform duration-500"
            :style="{ transform: `translateX(-${currentIndex * 100}%)` }"
          >
            <div
              v-for="(review, index) in reviews"
              :key="review.id"
              class="w-full flex-shrink-0 px-4"
            >
              <div class="bg-white rounded-2xl shadow-xl p-8 lg:p-10">
                <!-- Рейтинг -->
                <div class="flex items-center mb-6">
                  <div class="flex">
                    <svg
                      v-for="star in 5"
                      :key="star"
                      class="w-5 h-5"
                      :class="star <= review.rating ? 'text-amber-400' : 'text-gray-300'"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                  </div>
                  <span class="ml-3 text-sm text-gray-500">{{ review.date }}</span>
                </div>

                <!-- Текст отзыва -->
                <div class="mb-8">
                  <p class="text-lg text-gray-700 italic mb-4 relative">
                    <span class="absolute -top-2 -left-2 text-3xl text-rose-200">"</span>
                    {{ review.content }}
                    <span class="absolute -bottom-2 -right-2 text-3xl text-rose-200">"</span>
                  </p>
                  <div class="text-sm text-gray-500">
                    Услуга: <span class="font-semibold text-rose-600">{{ review.service }}</span>
                  </div>
                </div>

                <!-- Автор -->
                <div class="flex items-center">
                  <div class="w-12 h-12 rounded-full bg-gradient-to-r from-rose-100 to-pink-100 flex items-center justify-center mr-4">
                    <span class="text-lg font-semibold text-rose-600">
                      {{ review.author.charAt(0) }}
                    </span>
                  </div>
                  <div>
                    <div class="font-semibold text-gray-900">{{ review.author }}</div>
                    <div class="text-sm text-gray-500">Постоянная клиентка</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Индикаторы -->
        <div class="flex justify-center space-x-2 mt-8">
          <button
            v-for="(_, index) in reviews"
            :key="index"
            @click="currentIndex = index"
            class="w-2 h-2 rounded-full transition-all duration-300"
            :class="currentIndex === index
              ? 'w-8 bg-gradient-to-r from-rose-500 to-pink-500'
              : 'bg-gray-300 hover:bg-gray-400'"
            :aria-label="`Перейти к отзыву ${index + 1}`"
          />
        </div>
      </div>

      <!-- CTA под отзывами -->
      <div class="text-center mt-12 lg:mt-16">
        <p class="text-lg text-gray-600 mb-8">
          Присоединяйтесь к тысячам довольных клиенток Feminaluxe
        </p>
        <a
          href="#contacts"
          class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-rose-500 to-pink-500 text-white font-semibold rounded-full hover:shadow-xl hover:shadow-rose-200 transition-all duration-300 transform hover:-translate-y-0.5"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          Записаться на консультацию
        </a>
      </div>
    </div>
  </section>
</template>
