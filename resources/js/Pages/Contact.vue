<script setup>
import { useForm } from '@inertiajs/vue3';
import { defineProps } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  services: Array,  // Pass from controller if needed
});

const form = useForm({
  service_id: null,
  client_name: '',
  client_email: '',
  client_phone: '',
  appointment_time: '',
});

const submit = () => {
  form.post(route('bookings.store'), {
    onSuccess: () => {
      form.reset();
      alert('Запись создана! Мы свяжемся с вами.');
    },
    onError: (errors) => console.error(errors),
  });
};
</script>

<template>
  <AppLayout>
    <section class="py-12">
      <h1 class="text-4xl font-bold text-center mb-8 text-primary">Контакты и бронирование</h1>
      <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-lg">
        <form @submit.prevent="submit" class="space-y-6">
          <div>
            <label for="service_id" class="block text-sm font-medium text-gray-700">Услуга</label>
            <select id="service_id" v-model="form.service_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary" required aria-required="true">
              <option value="" disabled>Выберите услугу</option>
              <option v-for="service in services" :key="service.id" :value="service.id">{{ service.name }}</option>
            </select>
            <p v-if="form.errors.service_id" class="mt-2 text-sm text-red-600">{{ form.errors.service_id }}</p>
          </div>

          <div>
            <label for="client_name" class="block text-sm font-medium text-gray-700">Имя</label>
            <input id="client_name" v-model="form.client_name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary" required aria-required="true" />
            <p v-if="form.errors.client_name" class="mt-2 text-sm text-red-600">{{ form.errors.client_name }}</p>
          </div>

          <div>
            <label for="client_email" class="block text-sm font-medium text-gray-700">Email</label>
            <input id="client_email" v-model="form.client_email" type="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary" required aria-required="true" />
            <p v-if="form.errors.client_email" class="mt-2 text-sm text-red-600">{{ form.errors.client_email }}</p>
          </div>

          <div>
            <label for="client_phone" class="block text-sm font-medium text-gray-700">Телефон</label>
            <input id="client_phone" v-model="form.client_phone" type="tel" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary" required aria-required="true" />
            <p v-if="form.errors.client_phone" class="mt-2 text-sm text-red-600">{{ form.errors.client_phone }}</p>
          </div>

          <div>
            <label for="appointment_time" class="block text-sm font-medium text-gray-700">Дата и время</label>
            <input id="appointment_time" v-model="form.appointment_time" type="datetime-local" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary" required aria-required="true" />
            <p v-if="form.errors.appointment_time" class="mt-2 text-sm text-red-600">{{ form.errors.appointment_time }}</p>
          </div>

          <button type="submit" class="w-full bg-accent text-white py-2 px-4 rounded-md hover:bg-opacity-90 transition" :disabled="form.processing">Записаться</button>
        </form>
      </div>
      <div class="text-center mt-8">
        <p>Адрес: Москва, ул. Красоты, 10</p>
        <p>Телефон: +7 (495) 123-45-67</p>
        <p>Email: info@feminaluxe.com</p>
      </div>
    </section>
  </AppLayout>
</template>
