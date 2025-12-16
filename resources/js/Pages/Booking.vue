<script setup>
import {Head, useForm, router} from '@inertiajs/vue3';
import {computed, ref, watch, onMounted} from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import {
    XMarkIcon,
    CalendarDaysIcon,
    ClockIcon,
    UserIcon,
    PhoneIcon,
    EnvelopeIcon,
    ChatBubbleLeftRightIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    prefilledService: Object,
    services: Array,
    availableSlots: Array,
    meta: Object,
    csrf_token: String,
});

// Form handling
const form = useForm({
    _token: props.csrf_token,
    service_id: props.prefilledService?.id || '',
    client_name: '',
    client_email: '',
    client_phone: '',
    appointment_date: '',
    appointment_time: '',
    message: '',
    source: 'booking_page',
    utm_source: '',
    utm_medium: '',
    utm_campaign: '',
});

// State
const selectedDate = ref('');
const availableTimes = ref([]);
const showSuccess = ref(false);
const isLoading = ref(false);
const formErrors = ref({});

// Set prefilled service
if (props.prefilledService) {
    form.service_id = props.prefilledService.id;
}

// Initialize UTM parameters from URL
onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    form.utm_source = urlParams.get('utm_source') || '';
    form.utm_medium = urlParams.get('utm_medium') || '';
    form.utm_campaign = urlParams.get('utm_campaign') || '';

    // Set source based on referrer
    const referrer = document.referrer;
    if (referrer.includes('instagram')) form.source = 'instagram';
    else if (referrer.includes('facebook')) form.source = 'facebook';
    else if (referrer.includes('vk')) form.source = 'vk';
    else if (referrer.includes('yandex')) form.source = 'yandex';
    else if (referrer.includes('google')) form.source = 'google';

    // Set today's date as default
    if (props.availableSlots.length > 0) {
        const firstAvailableDate = props.availableSlots.find(slot =>
            slot.slots.some(s => s.available)
        );
        if (firstAvailableDate) {
            selectDate(firstAvailableDate.date);
        }
    }
});

// When date changes, update available times
watch(selectedDate, (date) => {
    if (date) {
        const selectedSlot = props.availableSlots.find(slot => slot.date === date);
        availableTimes.value = selectedSlot?.slots || [];
        form.appointment_time = '';
        clearErrors(['appointment_time']);
    }
});

// Date selection
const selectDate = (date) => {
    selectedDate.value = date;
    form.appointment_date = date;

    const selectedSlot = props.availableSlots.find(slot => slot.date === date);
    availableTimes.value = selectedSlot?.slots || [];
    form.appointment_time = '';
    clearErrors(['appointment_date', 'appointment_time']);
};

// Time selection
const selectTime = (time) => {
    form.appointment_time = time;
    clearErrors(['appointment_time']);
};

// Phone formatting
const formatPhone = (value) => {
    if (!value) return '';

    const cleaned = value.replace(/\D/g, '');

    // Если начинается с 8, заменяем на 7
    let formatted = cleaned;
    if (cleaned.startsWith('8') && cleaned.length === 11) {
        formatted = '7' + cleaned.substring(1);
    }

    // Форматирование: +7 (XXX) XXX-XX-XX
    const match = formatted.match(/^(\d{1})(\d{3})(\d{3})(\d{2})(\d{2})$/);

    if (match) {
        return `+${match[1]} (${match[2]}) ${match[3]}-${match[4]}-${match[5]}`;
    }

    return value;
};

// Watch phone input for formatting
watch(() => form.client_phone, (value) => {
    if (value && !value.startsWith('+')) {
        form.client_phone = formatPhone(value);
    }
});

// Form validation
const isValid = computed(() => {
    return form.service_id &&
        form.client_name.trim().length >= 2 &&
        form.client_email.includes('@') &&
        form.client_phone.replace(/\D/g, '').length >= 11 &&
        form.appointment_date &&
        form.appointment_time;
});

// Clear specific errors
const clearErrors = (fields) => {
    fields.forEach(field => {
        if (form.errors[field]) {
            form.clearErrors(field);
        }
    });
};

// Submit form
const submitForm = () => {
    if (!isValid.value || form.processing) return;

    isLoading.value = true;

    const headers = {
        'X-CSRF-TOKEN': props.csrf_token,
        'X-Requested-With': 'XMLHttpRequest',
    };

    form.post(route('bookings.store'), {
        headers: headers,
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            showSuccess.value = true;

            // Track conversion
            if (window.dataLayer) {
                window.dataLayer.push({
                    event: 'booking_completed',
                    service_id: form.service_id,
                    appointment_date: form.appointment_date,
                    appointment_time: form.appointment_time,
                });
            }
        },
        onError: (errors) => {
            console.log('Form errors:', errors);
            formErrors.value = errors;

            // Scroll to first error
            setTimeout(() => {
                const firstError = Object.keys(errors)[0];
                if (firstError) {
                    const element = document.querySelector(`[name="${firstError}"]`);
                    if (element) {
                        element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        element.focus();
                    }
                }
            }, 100);
        },
        onFinish: () => {
            isLoading.value = false;
        }
    });
};

// Get selected service details
const selectedService = computed(() => {
    if (!form.service_id) return null;
    return props.services.find(s => s.id == form.service_id);
});

// Close modal and redirect
const closeModal = () => {
    router.visit(route('services.index'));
};

// Quick select today/tomorrow
const quickSelectDate = (daysFromNow) => {
    const date = new Date();
    date.setDate(date.getDate() + daysFromNow);
    const dateStr = date.toISOString().split('T')[0];

    // Find available date (skip weekends)
    let availableDate = props.availableSlots.find(slot => slot.date === dateStr);
    if (!availableDate) {
        // Find next available date
        availableDate = props.availableSlots.find(slot =>
            new Date(slot.date) > new Date(dateStr)
        );
    }

    if (availableDate) {
        selectDate(availableDate.date);
    }
};

// Check slot availability via API
const checkSlotAvailability = async (date, time) => {
    try {
        const response = await fetch(route('bookings.check-availability'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': props.csrf_token,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ date, time }),
        });

        const data = await response.json();
        return data.available;
    } catch (error) {
        console.error('Error checking availability:', error);
        return true; // Default to available on error
    }
};
</script>

<template>
    <Head>
        <title>{{ meta.title }}</title>
        <meta name="description" :content="meta.description"/>

        <!-- Structured Data for Booking -->
        <script type="application/ld+json">
            {
                "@context": "https://schema.org",
                "@type": "BookingPage",
                "name": "Запись на консультацию по перманентному макияжу",
                "description": "{{ meta.description }}",
                "url": "{{ route('bookings.create') }}",
                "potentialAction": {
                    "@type": "BookAction",
                    "target": {
                        "@type": "EntryPoint",
                        "urlTemplate": "{{ route('bookings.store') }}",
                        "actionPlatform": [
                            "http://schema.org/DesktopWebPlatform",
                            "http://schema.org/IOSPlatform",
                            "http://schema.org/AndroidPlatform"
                        ]
                    }
                }
            }
        </script>
    </Head>

    <AppLayout>
        <!-- Modal Overlay -->
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <!-- Modal Backdrop -->
                <div
                    class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
                    @click="closeModal"
                />

                <!-- Modal Content -->
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden">
                    <!-- Close Button -->
                    <button
                        @click="closeModal"
                        class="absolute top-4 right-4 z-10 text-gray-400 hover:text-gray-600 transition-colors duration-300"
                        aria-label="Закрыть"
                    >
                        <XMarkIcon class="h-6 w-6"/>
                    </button>

                    <!-- Success Message -->
                    <div
                        v-if="showSuccess"
                        class="bg-green-50 border border-green-200 rounded-lg m-6 p-8 text-center"
                    >
                        <div class="text-green-600 text-2xl font-bold mb-4">
                            ✅ Запись успешно создана!
                        </div>
                        <p class="text-green-700 mb-6 text-lg">
                            Мы свяжемся с вами в течение 15 минут для подтверждения записи.
                            Наш специалист пришлет вам подробную инструкцию по подготовке.
                        </p>
                        <div class="flex gap-4 justify-center">
                            <button
                                @click="closeModal"
                                class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-300"
                            >
                                Вернуться к услугам
                            </button>
                            <a
                                href="https://wa.me/79991234567"
                                target="_blank"
                                class="px-6 py-3 bg-white border border-green-600 text-green-600 rounded-lg hover:bg-green-50 transition-colors duration-300"
                            >
                                Написать в WhatsApp
                            </a>
                        </div>
                    </div>

                    <!-- Booking Form -->
                    <div v-else>
                        <!-- Header -->
                        <div class="bg-gradient-to-r from-rose-500 via-pink-500 to-purple-500 text-white p-8">
                            <h1 class="text-3xl font-bold mb-2">
                                Запись на консультацию
                            </h1>
                            <p class="opacity-90">
                                Оставьте заявку и мы перезвоним вам в течение 15 минут
                            </p>
                        </div>

                        <!-- Form Content -->
                        <div class="p-8">
                            <form @submit.prevent="submitForm" class="space-y-8">
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
                                        <optgroup
                                            v-for="(group, category) in services.reduce((acc, service) => {
                        const cat = service.category || 'Другое';
                        if (!acc[cat]) acc[cat] = [];
                        acc[cat].push(service);
                        return acc;
                      }, {})"
                                            :key="category"
                                            :label="category"
                                        >
                                            <option
                                                v-for="service in group"
                                                :key="service.id"
                                                :value="service.id"
                                            >
                                                {{ service.name }} - {{ service.final_price?.toLocaleString('ru-RU') }}₽
                                            </option>
                                        </optgroup>
                                    </select>
                                    <div v-if="form.errors.service_id" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.service_id }}
                                    </div>

                                    <!-- Selected Service Info -->
                                    <div
                                        v-if="selectedService"
                                        class="mt-4 bg-gray-50 rounded-xl p-4"
                                    >
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <div class="font-medium">{{ selectedService.name }}</div>
                                                <div class="text-sm text-gray-600">{{ selectedService.category }}</div>
                                            </div>
                                            <div class="text-xl font-bold text-primary">
                                                {{ selectedService.final_price?.toLocaleString('ru-RU') }}₽
                                            </div>
                                        </div>
                                        <div v-if="selectedService.duration" class="text-sm text-gray-600 mt-2">
                                            ⏱️ Длительность: ~{{ selectedService.duration }} минут
                                        </div>
                                    </div>
                                </div>

                                <!-- Contact Information -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <UserIcon class="inline-block h-5 w-5 mr-1"/>
                                            Ваше имя *
                                        </label>
                                        <input
                                            v-model="form.client_name"
                                            type="text"
                                            required
                                            placeholder="Иван Иванов"
                                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all duration-300"
                                        />
                                        <div v-if="form.errors.client_name" class="text-red-600 text-sm mt-1">
                                            {{ form.errors.client_name }}
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <PhoneIcon class="inline-block h-5 w-5 mr-1"/>
                                            Телефон *
                                        </label>
                                        <input
                                            v-model="form.client_phone"
                                            type="tel"
                                            required
                                            placeholder="+7 (999) 123-45-67"
                                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all duration-300"
                                        />
                                        <div v-if="form.errors.client_phone" class="text-red-600 text-sm mt-1">
                                            {{ form.errors.client_phone }}
                                        </div>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <EnvelopeIcon class="inline-block h-5 w-5 mr-1"/>
                                            Email *
                                        </label>
                                        <input
                                            v-model="form.client_email"
                                            type="email"
                                            required
                                            placeholder="example@mail.ru"
                                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all duration-300"
                                        />
                                        <div v-if="form.errors.client_email" class="text-red-600 text-sm mt-1">
                                            {{ form.errors.client_email }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Date Selection -->
                                <div>
                                    <div class="flex items-center justify-between mb-4">
                                        <label class="block text-sm font-medium text-gray-700">
                                            <CalendarDaysIcon class="inline-block h-5 w-5 mr-1"/>
                                            Выберите дату *
                                        </label>
                                        <div class="flex gap-2">
                                            <button
                                                type="button"
                                                @click="quickSelectDate(1)"
                                                class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded transition-colors duration-300"
                                            >
                                                Завтра
                                            </button>
                                            <button
                                                type="button"
                                                @click="quickSelectDate(3)"
                                                class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded transition-colors duration-300"
                                            >
                                                Через 3 дня
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Date Picker -->
                                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-2 mb-4">
                                        <button
                                            v-for="slot in availableSlots"
                                            :key="slot.date"
                                            type="button"
                                            @click="selectDate(slot.date)"
                                            :class="[
                        'py-3 rounded-lg border transition-all duration-300 text-center',
                        selectedDate === slot.date
                          ? 'bg-primary border-primary text-white'
                          : 'border-gray-300 hover:border-primary hover:bg-primary/5'
                      ]"
                                        >
                                            <div class="font-medium">{{ slot.display_date.split(' ')[0] }}</div>
                                            <div class="text-xs opacity-75">{{ slot.weekday }}</div>
                                            <div class="text-xs mt-1">{{ slot.slots.filter(s => s.available).length }}
                                                слотов
                                            </div>
                                        </button>
                                    </div>
                                    <div v-if="form.errors.appointment_date" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.appointment_date }}
                                    </div>
                                </div>

                                <!-- Time Selection -->
                                <div v-if="selectedDate">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <ClockIcon class="inline-block h-5 w-5 mr-1"/>
                                        Выберите время *
                                    </label>
                                    <div class="grid grid-cols-4 sm:grid-cols-6 gap-2">
                                        <button
                                            v-for="timeSlot in availableTimes"
                                            :key="timeSlot.time"
                                            type="button"
                                            @click="selectTime(timeSlot.time)"
                                            :class="[
                        'py-2 rounded-lg border transition-all duration-300',
                        form.appointment_time === timeSlot.time
                          ? 'bg-primary border-primary text-white'
                          : !timeSlot.available
                          ? 'border-gray-200 bg-gray-100 text-gray-400 cursor-not-allowed'
                          : 'border-gray-300 hover:border-primary hover:bg-primary/5'
                      ]"
                                            :disabled="!timeSlot.available"
                                        >
                                            {{ timeSlot.time }}
                                        </button>
                                    </div>
                                    <div v-if="form.errors.appointment_time" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.appointment_time }}
                                    </div>
                                </div>

                                <!-- Message -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <ChatBubbleLeftRightIcon class="inline-block h-5 w-5 mr-1"/>
                                        Комментарий (необязательно)
                                    </label>
                                    <textarea
                                        v-model="form.message"
                                        rows="3"
                                        placeholder="Особые пожелания, аллергии, дополнительные вопросы..."
                                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all duration-300 resize-none"
                                    />
                                    <div v-if="form.errors.message" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.message }}
                                    </div>
                                </div>

                                <!-- Privacy Policy -->
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

                                <!-- Submit Button -->
                                <div class="pt-4">
                                    <button
                                        type="submit"
                                        :disabled="!isValid || form.processing"
                                        :class="[
                      'w-full px-8 py-4 rounded-lg font-bold text-lg transition-all duration-300',
                      isValid && !form.processing
                        ? 'bg-gradient-to-r from-rose-500 via-pink-500 to-purple-500 text-white hover:shadow-xl hover:shadow-primary/25 hover:scale-105'
                        : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                    ]"
                                    >
                                        <span v-if="isLoading">Отправка...</span>
                                        <span v-else>Записаться на консультацию</span>
                                    </button>
                                </div>

                                <!-- Additional Info -->
                                <div class="text-center text-sm text-gray-500">
                                    <p>✅ Гарантируем конфиденциальность ваших данных</p>
                                    <p>⭐ При записи онлайн — скидка 10% на первую процедуру</p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
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

/* Phone input styling */
input[type="tel"] {
    font-family: monospace;
    letter-spacing: 0.5px;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
