<script setup>
import { ref, computed, watch } from 'vue';
import { XMarkIcon, CalendarDaysIcon, ClockIcon, UserIcon, PhoneIcon } from '@heroicons/vue/24/outline';
import { useForm, usePage } from '@inertiajs/vue3';
import { format, addDays, isWeekend, parseISO } from 'date-fns';
import { ru } from 'date-fns/locale';

const props = defineProps({
  service: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['close']);

// Form state
const form = useForm({
  service_id: props.service?.id,
  date: null,
  time_slot: null,
  name: '',
  phone: '',
  email: '',
  comment: '',
  source: 'services_page_modal'
});

// Time slots (9:00 - 21:00, every 30 minutes)
const timeSlots = computed(() => {
  const slots = [];
  for (let hour = 9; hour <= 21; hour++) {
    for (let minute of [0, 30]) {
      if (hour === 21 && minute === 30) break;
      const time = `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;
      slots.push({
        value: time,
        label: time,
        disabled: false
      });
    }
  }
  return slots;
});

// Available dates (next 14 days, excluding weekends)
const availableDates = computed(() => {
  const dates = [];
  const today = new Date();

  for (let i = 0; i < 14; i++) {
    const date = addDays(today, i + 1); // Start from tomorrow
    if (!isWeekend(date)) {
      dates.push({
        value: format(date, 'yyyy-MM-dd'),
        label: format(date, 'd MMMM', { locale: ru }),
        weekday: format(date, 'EEEE', { locale: ru })
      });
    }
  }

  return dates;
});

// Form validation
const isValid = computed(() => {
  return form.date &&
         form.time_slot &&
         form.name.trim().length >= 2 &&
         form.phone.trim().length >= 11 &&
         form.email.includes('@');
});

// Form submission
const submit = () => {
  form.post(route('bookings.store'), {
    preserveScroll: true,
    onSuccess: () => {
      emit('close');
      showSuccessMessage.value = true;

      // Reset form
      form.reset();

      // Track conversion
      if (window.dataLayer) {
        window.dataLayer.push({
          event: 'booking_submit',
          service_name: props.service.name,
          service_price: props.service.price,
          booking_source: 'modal'
        });
      }
    },
    onError: (errors) => {
      console.error('Booking error:', errors);
    }
  });
};

// Phone formatting
const formatPhone = (value) => {
  const cleaned = value.replace(/\D/g, '');
  const match = cleaned.match(/^(\d{1})(\d{3})(\d{3})(\d{2})(\d{2})$/);

  if (match) {
    return `+${match[1]} (${match[2]}) ${match[3]}-${match[4]}-${match[5]}`;
  }

  return value;
};

watch(() => form.phone, (value) => {
  form.phone = formatPhone(value);
});

// Success message
const showSuccessMessage = ref(false);
</script>

<template>
  <div class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Overlay -->
    <div
      class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
      @click="emit('close')"
    />

    <!-- Modal container -->
    <div class="flex min-h-full items-center justify-center p-4">
      <!-- Modal content -->
      <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b">
          <div>
            <h3 class="text-2xl font-bold text-gray-900">
              Запись на услугу
            </h3>
            <p class="text-gray-600 mt-1">
              {{ service.name }} • {{ service.price }}₽
            </p>
          </div>
          <button
            @click="emit('close')"
            class="text-gray-400 hover:text-gray-600 transition-colors duration-300"
          >
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>

        <!-- Success message -->
        <div
          v-if="showSuccessMessage"
          class="bg-green-50 border border-green-200 rounded-lg m-6 p-6 text-center"
        >
          <div class="text-green-600 text-lg font-medium mb-2">
            ✅ Запись успешно оформлена!
          </div>
          <p class="text-green-700 mb-4">
            Мы свяжемся с вами в течение 15 минут для подтверждения записи.
            Наш специалист пришлет вам подробную инструкцию по подготовке.
          </p>
          <button
            @click="showSuccessMessage = false"
            class="text-green-600 hover:text-green-800 underline"
          >
            Создать новую запись
          </button>
        </div>

        <!-- Booking form -->
        <div v-else class="p-6">
          <form @submit.prevent="submit" class="space-y-6">
            <!-- Date selection -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                <CalendarDaysIcon class="inline-block h-5 w-5 mr-1" />
                Выберите дату
              </label>
              <div class="grid grid-cols-3 md:grid-cols-7 gap-2">
                <button
                  v-for="date in availableDates"
                  :key="date.value"
                  type="button"
                  @click="form.date = date.value"
                  :class="[
                    'py-3 rounded-lg border transition-all duration-300',
                    form.date === date.value
                      ? 'bg-primary border-primary text-white'
                      : 'border-gray-300 hover:border-primary hover:bg-primary/5'
                  ]"
                >
                  <div class="font-medium">{{ date.label }}</div>
                  <div class="text-xs opacity-75">{{ date.weekday }}</div>
                </button>
              </div>
              <div v-if="form.errors.date" class="text-red-600 text-sm mt-1">
                {{ form.errors.date }}
              </div>
            </div>

            <!-- Time slots -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                <ClockIcon class="inline-block h-5 w-5 mr-1" />
                Выберите время
              </label>
              <div class="grid grid-cols-4 md:grid-cols-6 gap-2">
                <button
                  v-for="slot in timeSlots"
                  :key="slot.value"
                  type="button"
                  @click="form.time_slot = slot.value"
                  :class="[
                    'py-2 rounded-lg border transition-all duration-300',
                    form.time_slot === slot.value
                      ? 'bg-primary border-primary text-white'
                      : slot.disabled
                      ? 'border-gray-200 bg-gray-100 text-gray-400 cursor-not-allowed'
                      : 'border-gray-300 hover:border-primary hover:bg-primary/5'
                  ]"
                  :disabled="slot.disabled"
                >
                  {{ slot.label }}
                </button>
              </div>
              <div v-if="form.errors.time_slot" class="text-red-600 text-sm mt-1">
                {{ form.errors.time_slot }}
              </div>
            </div>

            <!-- Contact information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Name -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  <UserIcon class="inline-block h-5 w-5 mr-1" />
                  Ваше имя
                </label>
                <input
                  v-model="form.name"
                  type="text"
                  required
                  placeholder="Иван Иванов"
                  class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all duration-300"
                />
                <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">
                  {{ form.errors.name }}
                </div>
              </div>

              <!-- Phone -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  <PhoneIcon class="inline-block h-5 w-5 mr-1" />
                  Телефон
                </label>
                <input
                  v-model="form.phone"
                  type="tel"
                  required
                  placeholder="+7 (999) 123-45-67"
                  class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all duration-300"
                />
                <div v-if="form.errors.phone" class="text-red-600 text-sm mt-1">
                  {{ form.errors.phone }}
                </div>
              </div>
            </div>

            <!-- Email -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Email для подтверждения
              </label>
              <input
                v-model="form.email"
                type="email"
                required
                placeholder="example@mail.ru"
                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all duration-300"
              />
              <div v-if="form.errors.email" class="text-red-600 text-sm mt-1">
                {{ form.errors.email }}
              </div>
            </div>

            <!-- Comment -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Комментарий к записи (необязательно)
              </label>
              <textarea
                v-model="form.comment"
                rows="3"
                placeholder="Особые пожелания, аллергии, дополнительные вопросы..."
                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all duration-300 resize-none"
              />
              <div v-if="form.errors.comment" class="text-red-600 text-sm mt-1">
                {{ form.errors.comment }}
              </div>
            </div>

            <!-- Service info -->
            <div class="bg-gray-50 rounded-xl p-4">
              <div class="text-sm text-gray-600 mb-2">Выбранная услуга:</div>
              <div class="flex justify-between items-center">
                <div>
                  <div class="font-medium">{{ service.name }}</div>
                  <div class="text-sm text-gray-600">{{ service.duration }} минут</div>
                </div>
                <div class="text-xl font-bold text-primary">{{ service.price }}₽</div>
              </div>

              <div v-if="service.benefits" class="mt-3 space-y-1">
                <div v-for="benefit in service.benefits" :key="benefit" class="text-sm text-gray-600">
                  • {{ benefit }}
                </div>
              </div>
            </div>

            <!-- Privacy policy -->
            <div class="text-sm text-gray-600">
              <label class="flex items-start">
                <input
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

            <!-- Submit button -->
            <div class="flex gap-4 pt-4">
              <button
                type="button"
                @click="emit('close')"
                class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-300"
              >
                Отмена
              </button>
              <button
                type="submit"
                :disabled="!isValid || form.processing"
                :class="[
                  'flex-1 px-6 py-3 rounded-lg font-medium transition-all duration-300',
                  isValid && !form.processing
                    ? 'bg-primary text-white hover:bg-primary-dark'
                    : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                ]"
              >
                <span v-if="form.processing">Отправка...</span>
                <span v-else>Записаться на услугу</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
input[type="tel"] {
  font-family: monospace;
  letter-spacing: 0.5px;
}

/* Animation for modal appearance */
@keyframes modal-appear {
  from {
    opacity: 0;
    transform: scale(0.95) translateY(10px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.fixed.overflow-y-auto > .flex {
  animation: modal-appear 0.3s ease-out;
}
</style>
