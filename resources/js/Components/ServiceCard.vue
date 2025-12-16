<template>
    <div class="group card-gradient-border rounded-2xl overflow-hidden card-hover">
        <div class="relative h-48 lg:h-56 overflow-hidden bg-gradient-to-br from-rose-50/30 to-pink-50/30">
            <!-- SVG placeholder вместо изображения -->
            <img v-if="!service.primary_image_url"
                 src="/images/placeholder-service.svg"
                 :alt="service.name"
                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
            />

            <!-- Реальное изображение если есть -->
            <img v-else
                 :src="service.primary_image_url"
                 :alt="service.name"
                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
            />

            <!-- Бейдж категории -->
            <div class="absolute top-4 left-4">
        <span
            class="px-3 py-1 bg-white/90 backdrop-blur-sm text-[var(--color-rose-600)] text-xs font-semibold rounded-full">
          {{ service.category || 'Услуга' }}
        </span>
            </div>

            <!-- Бейдж скидки -->
            <div v-if="discountPercentage > 0" class="absolute top-4 right-4">
        <span class="px-3 py-1 bg-gradient-rose-pink text-white text-sm font-bold rounded-full shadow-rose-strong">
          -{{ discountPercentage }}%
        </span>
            </div>

            <!-- Бейдж популярности -->
            <div v-if="service.is_popular" class="absolute bottom-4 right-4">
        <span class="px-3 py-1 btn-gradient-accent text-white text-xs font-bold rounded-full shadow-rose-soft">
          ★ Популярно
        </span>
            </div>
        </div>

        <!-- Контент -->
        <div class="p-6">
            <div class="mb-4">
                <h3 class="text-xl font-bold text-[var(--color-gray-900)] mb-2 line-clamp-1">
                    {{ service.name }}
                </h3>
                <p class="text-[var(--color-gray-600)] text-sm line-clamp-2 mb-4">
                    {{ service.short_description || service.description }}
                </p>
            </div>

            <!-- Цены -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <div class="text-2xl font-bold text-[var(--color-gray-900)]">
                        {{ finalPrice.toLocaleString('ru-RU') }} ₽
                    </div>
                    <div v-if="discountPercentage > 0" class="text-sm text-[var(--color-gray-500)] line-through">
                        {{ service.price.toLocaleString('ru-RU') }} ₽
                    </div>
                </div>

                <div class="text-sm text-[var(--color-gray-500)]">
                    <div class="flex items-center space-x-1">
                        <svg class="w-4 h-4 text-[var(--color-amber-400)]" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <span>{{ service.rating || 'Новый' }}</span>
                        <span class="text-[var(--color-gray-400)]">({{ service.reviews_count || 0 }})</span>
                    </div>
                </div>
            </div>

            <!-- Дополнительная информация -->
            <div class="flex items-center justify-between text-sm text-[var(--color-gray-500)] mb-6">
                <div class="flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ service.duration || '2-3 часа' }}</span>
                </div>
                <div v-if="service.views_count > 0" class="flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <span>{{ service.views_count.toLocaleString('ru-RU') }}</span>
                </div>
            </div>

            <!-- Кнопки -->
            <div class="flex space-x-3">
                <Link
                    :href="route('services.show', service.slug)"
                    class="flex-1 btn-gradient-primary text-center flex items-center justify-center"
                >
                    Подробнее
                </Link>
                <button
                    class="px-4 py-3 border-2 border-[var(--color-rose-100)] text-[var(--color-rose-600)] font-semibold rounded-lg hover:bg-[var(--color-rose-50)] transition-colors"
                    @click="$inertia.visit(route('services.index'))"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import {Link} from '@inertiajs/vue3';
import {computed} from 'vue';

const props = defineProps({
    service: {
        type: Object,
        required: true
    }
});

const finalPrice = computed(() => {
    return props.service.discounted_price || props.service.price;
});

const discountPercentage = computed(() => {
    if (!props.service.discounted_price) return 0;
    return Math.round((1 - props.service.discounted_price / props.service.price) * 100);
});
</script>
