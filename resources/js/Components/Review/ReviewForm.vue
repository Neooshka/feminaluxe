<script setup>
import { ref, computed, onMounted } from 'vue';
import { XMarkIcon, StarIcon } from '@heroicons/vue/24/outline';
import { StarIcon as StarIconSolid } from '@heroicons/vue/24/solid';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  initialService: {
    type: Object,
    default: null
  }
});

const emit = defineEmits(['close', 'success']);

// Form state
const form = useForm({
  service_id: props.initialService?.id || '',
  author: '',
  content: '',
  rating: 5,
  email: '',
  phone: '',
  consent: false
});

// UI state
const hoverRating = ref(0);
const selectedRating = ref(5);
const isLoading = ref(false);
const submitted = ref(false);

// Services list (можно получать через props или API)
const services = ref([]);

// Fetch services on mount
const fetchServices = async () => {
  try {
    const response = await fetch(route('api.services.list'));
    services.value = await response.json();
  } catch (error) {
    console.error('Error fetching services:', error);
  }
};

// Set initial service
if (props.initialService) {
  form.service_id = props.initialService.id;
}

// Submit form
const submitForm = () => {
  if (!form.consent || !form.service_id || !form.author || !form.content) {
    return;
  }

  isLoading.value = true;
  form.rating = selectedRating.value;

  form.post(route('reviews.store'), {
    preserveScroll: true,
    onSuccess: () => {
      submitted.value = true;
      form.reset();
      setTimeout(() => {
        emit('close');
        emit('success');
      }, 3000);
    },
    onError: (errors) => {
      console.error('Review submission error:', errors);
    },
    onFinish: () => {
      isLoading.value = false;
    }
  });
};

// Rating stars
const setRating = (rating) => {
  selectedRating.value = rating;
};

// Close modal
const closeModal = () => {
  emit('close');
};

onMounted(() => {
  fetchServices();

  // Set initial service
  if (props.initialService) {
    form.service_id = props.initialService.id;
  }
});
</script>

<template>
  <div class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Overlay -->
    <div
      class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
      @click="closeModal"
    />

    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-4">
      <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden">
        <!-- Close Button -->
        <button
          @click="closeModal"
          class="absolute top-4 right-4 z-10 text-gray-400 hover:text-gray-600 transition-colors duration-300"
          aria-label="Закрыть"
        >
          <XMarkIcon class="h-6 w-6" />
        </button>

        <!-- Success Message -->
        <div
          v-if="submitted"
          class="bg-green-50 border border-green-200 rounded-lg m-6 p-8 text-center"
        >
          <div class="text-green-600 text-2xl font-bold mb-4">
            ✅ Спасибо за ваш отзыв!
          </div>
          <p class="text-green-700 mb-6 text-lg">
            Ваш отзыв отправлен на модерацию. Мы опубликуем его после проверки.
          </p>
          <button
            @click="closeModal"
            class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-300"
          >
            Закрыть
          </button>
        </div>

        <!-- Review Form -->
        <div v-else>
          <!-- Header -->
          <div class="bg-gradient-to-r from-rose-500 via-pink-500 to-purple-500 text-white p-8">
            <h2 class="text-2xl font-bold mb-2">
              Оставить отзыв
            </h2>
            <p class="opacity-90">
              Поделитесь вашим опытом и помогите другим сделать правильный выбор
            </p>
          </div>

          <!-- Form Content -->
          <div class="p-8">
            <form @submit.prevent="submitForm" class="space-y-6">
              <!-- Service Selection -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Выберите услугу *
                </label>
                <select
                  v-model="form.service_id"
                  required
                  class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all duration-300"
                >
                  <option value="">Выберите услугу...</option>
                  <option
                    v-for="service in services"
                    :key="service.id"
                    :value="service.id"
                  >
                    {{ service.name }}
                  </option>
                </select>
                <div v-if="form.errors.service_id" class="text-red-600 text-sm mt-1">
                  {{ form.errors.service_id }}
                </div>
              </div>

              <!-- Rating -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Ваша оценка *
                </label>
                <div class="flex items-center space-x-1">
                  <button
                    v-for="star in 5"
                    :key="star"
                    type="button"
                    @click="setRating(star)"
                    @mouseenter="hoverRating = star"
                    @mouseleave="hoverRating = 0"
                    class="p-1 transition-transform duration-200 hover:scale-110"
                  >
                    <component
                      :is="star <= (hoverRating || selectedRating) ? StarIconSolid : StarIcon"
                      :class="[
                        'h-8 w-8',
                        star <= (hoverRating || selectedRating) ? 'text-yellow-400' : 'text-gray-300'
                      ]"
                    />
                  </button>
                  <span class="ml-4 text-lg font-bold text-gray-900">
                    {{ selectedRating }}.0
                  </span>
                </div>
              </div>

              <!-- Author Info -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Ваше имя *
                  </label>
                  <input
                    v-model="form.author"
                    type="text"
                    required
                    placeholder="Имя"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all duration-300"
                  />
                  <div v-if="form.errors.author" class="text-red-600 text-sm mt-1">
                    {{ form.errors.author }}
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Email (для связи)
                  </label>
                  <input
                    v-model="form.email"
                    type="email"
                    placeholder="email@example.com"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all duration-300"
                  />
                </div>
              </div>

              <!-- Phone -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Телефон (необязательно)
                </label>
                <input
                  v-model="form.phone"
                  type="tel"
                  placeholder="+7 (999) 123-45-67"
                  class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all duration-300"
                />
              </div>

              <!-- Review Content -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Текст отзыва *
                </label>
                <textarea
                  v-model="form.content"
                  rows="4"
                  required
                  placeholder="Расскажите о вашем опыте: что понравилось, что можно улучшить, какие были результаты..."
                  class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all duration-300 resize-none"
                />
                <div class="text-sm text-gray-500 mt-1">
                  Минимум 10 символов, максимум 2000
                </div>
                <div v-if="form.errors.content" class="text-red-600 text-sm mt-1">
                  {{ form.errors.content }}
                </div>
              </div>

              <!-- Consent -->
              <div class="text-sm text-gray-600">
                <label class="flex items-start">
                  <input
                    v-model="form.consent"
                    type="checkbox"
                    required
                    class="mt-1 mr-2 rounded border-gray-300 text-primary focus:ring-primary"
                  />
                  <span>
                    Я соглашаюсь с
                    <a :href="route('privacy')" target="_blank" class="text-primary hover:underline">
                      политикой конфиденциальности
                    </a>
                    и даю согласие на обработку персональных данных
                  </span>
                </label>
              </div>

              <!-- Submit Button -->
              <div class="pt-4">
                <button
                  type="submit"
                  :disabled="isLoading || !form.consent"
                  :class="[
                    'w-full px-8 py-4 rounded-lg font-bold text-lg transition-all duration-300',
                    form.consent && !isLoading
                      ? 'bg-gradient-to-r from-rose-500 via-pink-500 to-purple-500 text-white hover:shadow-xl hover:shadow-primary/25'
                      : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                  ]"
                >
                  <span v-if="isLoading">Отправка...</span>
                  <span v-else>Отправить отзыв</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
