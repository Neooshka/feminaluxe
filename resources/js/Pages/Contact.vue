<script setup>
import {Head, Link, useForm, usePage} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import {route} from "ziggy-js";

const props = defineProps({
    services: Array,
});

const { props: pageProps } = usePage();

const form = useForm({
    service_id: null,
    client_name: '',
    client_email: '',
    client_phone: '',
    appointment_time: '',
});

const submit = () => {
    form.post(route('bookings.store'), {
        preserveScroll: true,  // Keep scroll position for UX
        onSuccess: () => {
            form.reset();
            // Toast or modal success
        },
        onError: () => {
            // Errors handled below
        },
    });
};
</script>

<template>
    <Head title="Контакты и бронирование | Feminaluxe"/>

    <AppLayout>
        <section class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <h1 class="text-4xl md:text-5xl font-bold text-center mb-12 text-primary">Контакты и онлайн-бронирование
                    перманентного макияжа</h1>
                <div class="max-w-3xl mx-auto text-center">
                    <img src="/images/master-1.jpg" alt="Фото мастера Feminaluxe" class="w-48 h-48 rounded-full mx-auto mb-6"
                         loading="lazy">
                    <p class="text-lg mb-6">Feminaluxe — студия перманентного макияжа с опытом более 5 лет. Наши мастера
                        сертифицированы и используют только премиум-пигменты для естественного результата.</p>
                    <p class="text-lg mb-6">Мы специализируемся на бровях, губах и межресничках, обеспечивая безопасность и
                        комфорт. Тысячи довольных клиентов!</p>
                </div>
                <div class="max-w-lg mx-auto bg-gray-50 p-8 rounded-xl shadow-xl">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label for="service_id" class="block text-sm font-medium text-gray-700 mb-1">Услуга
                                перманентного макияжа</label>
                            <select id="service_id" v-model="form.service_id"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary transition"
                                    required>
                                <option value="" disabled>Выберите услугу</option>
                                <option v-for="service in services" :key="service.id" :value="service.id">
                                    {{ service.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.service_id" class="mt-1 text-sm text-red-600">{{ form.errors.service_id }}</p>
                        </div>

                        <div>
                            <label for="client_name" class="block text-sm font-medium text-gray-700 mb-1">Имя</label>
                            <input id="client_name" v-model="form.client_name" type="text"
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary transition"
                                   required placeholder="Ваше имя"/>
                            <p v-if="form.errors.client_name" class="mt-1 text-sm text-red-600">
                                {{ form.errors.client_name }}</p>
                        </div>

                        <div>
                            <label for="client_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input id="client_email" v-model="form.client_email" type="email"
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary transition"
                                   required placeholder="your@email.com"/>
                            <p v-if="form.errors.client_email" class="mt-1 text-sm text-red-600">
                                {{ form.errors.client_email }}</p>
                        </div>

                        <div>
                            <label for="client_phone"
                                   class="block text-sm font-medium text-gray-700 mb-1">Телефон</label>
                            <input id="client_phone" v-model="form.client_phone" type="tel"
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary transition"
                                   required placeholder="+7 (XXX) XXX-XX-XX"/>
                            <p v-if="form.errors.client_phone" class="mt-1 text-sm text-red-600">
                                {{ form.errors.client_phone }}</p>
                        </div>

                        <div>
                            <label for="appointment_time" class="block text-sm font-medium text-gray-700 mb-1">Дата и
                                время</label>
                            <input id="appointment_time" v-model="form.appointment_time" type="datetime-local"
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary transition"
                                   required min="{{ now()->format('Y-m-d\TH:i') }}"/>
                            <p v-if="form.errors.appointment_time" class="mt-1 text-sm text-red-600">
                                {{ form.errors.appointment_time }}</p>
                        </div>

                        <button type="submit"
                                class="w-full bg-accent text-white py-3 px-4 rounded-md font-bold hover:bg-opacity-90 transition duration-300 disabled:opacity-50"
                                :disabled="form.processing">Записаться на перманентный макияж
                        </button>
                    </form>
                    <p v-if="form.processing" class="text-center mt-4 text-gray-600">Обработка записи...</p>
                </div>
                <div class="text-center mt-12 text-gray-700">
                    <p>Адрес: {{ pageProps.config.contact.address }}</p>
                    <p>Телефон: {{ pageProps.config.contact.phone }}</p>
                    <p>Email: {{ pageProps.config.contact.email }}</p>
                </div>
            </div>
        </section>
    </AppLayout>
</template>
