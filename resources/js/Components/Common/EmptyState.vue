<script setup>
import {
  FaceFrownIcon,
  MagnifyingGlassIcon,
  PhotoIcon,
  ShoppingBagIcon,
  UserGroupIcon,
  CalendarIcon,
  ChatBubbleLeftRightIcon,
  ExclamationTriangleIcon
} from '@heroicons/vue/24/outline';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  title: {
    type: String,
    default: 'Ничего не найдено'
  },
  description: {
    type: String,
    default: 'Попробуйте изменить параметры поиска или фильтрации'
  },
  icon: {
    type: String,
    default: 'search',
    validator: (value) => [
      'search', 'services', 'users', 'calendar',
      'chat', 'warning', 'photo', 'cart'
    ].includes(value)
  },
  actionText: {
    type: String,
    default: ''
  },
  actionHref: {
    type: String,
    default: ''
  },
  actionRoute: {
    type: String,
    default: ''
  },
  secondaryActionText: {
    type: String,
    default: ''
  },
  secondaryActionHref: {
    type: String,
    default: ''
  },
  compact: {
    type: Boolean,
    default: false
  }
});

const getIcon = () => {
  const icons = {
    search: MagnifyingGlassIcon,
    services: ShoppingBagIcon,
    users: UserGroupIcon,
    calendar: CalendarIcon,
    chat: ChatBubbleLeftRightIcon,
    warning: ExclamationTriangleIcon,
    photo: PhotoIcon,
    cart: ShoppingBagIcon
  };
  return icons[props.icon] || FaceFrownIcon;
};

const IconComponent = getIcon();

const iconClasses = computed(() => {
  const base = 'mx-auto';
  const size = props.compact ? 'h-16 w-16' : 'h-24 w-24';
  const colors = {
    search: 'text-gray-400',
    services: 'text-primary',
    users: 'text-blue-500',
    calendar: 'text-green-500',
    chat: 'text-purple-500',
    warning: 'text-yellow-500',
    photo: 'text-pink-500',
    cart: 'text-indigo-500'
  };
  const color = colors[props.icon] || 'text-gray-400';

  return `${base} ${size} ${color}`;
});
</script>

<template>
  <div
    :class="[
      'text-center',
      compact ? 'py-8' : 'py-16'
    ]"
    role="status"
    aria-live="polite"
  >
    <!-- Icon with animated background -->
    <div class="relative inline-block mb-6">
      <div
        :class="[
          'absolute inset-0 rounded-full opacity-10 animate-pulse',
          iconClasses.includes('text-primary') ? 'bg-primary' :
          iconClasses.includes('text-blue') ? 'bg-blue-500' :
          iconClasses.includes('text-green') ? 'bg-green-500' :
          iconClasses.includes('text-purple') ? 'bg-purple-500' :
          iconClasses.includes('text-yellow') ? 'bg-yellow-500' :
          iconClasses.includes('text-pink') ? 'bg-pink-500' :
          iconClasses.includes('text-indigo') ? 'bg-indigo-500' :
          'bg-gray-400'
        ]"
        style="animation-duration: 2s"
      />
      <IconComponent :class="iconClasses" aria-hidden="true" />
    </div>

    <!-- Title -->
    <h3
      :class="[
        'font-semibold mb-3',
        compact ? 'text-xl' : 'text-2xl'
      ]"
    >
      {{ title }}
    </h3>

    <!-- Description -->
    <p
      :class="[
        'text-gray-600 max-w-md mx-auto mb-8',
        compact ? 'text-sm' : 'text-base'
      ]"
    >
      {{ description }}
    </p>

    <!-- Actions -->
    <div
      v-if="actionText || $slots.actions"
      :class="[
        'flex flex-col sm:flex-row gap-4 justify-center',
        compact ? '' : 'mt-8'
      ]"
    >
      <!-- Primary action -->
      <template v-if="actionText">
        <Link
          v-if="actionRoute"
          :href="route(actionRoute)"
          class="inline-flex items-center justify-center px-6 py-3 bg-primary text-white font-medium rounded-lg hover:bg-primary-dark transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
        >
          {{ actionText }}
        </Link>
        <a
          v-else-if="actionHref"
          :href="actionHref"
          class="inline-flex items-center justify-center px-6 py-3 bg-primary text-white font-medium rounded-lg hover:bg-primary-dark transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
        >
          {{ actionText }}
        </a>
        <button
          v-else
          @click="$emit('action')"
          class="inline-flex items-center justify-center px-6 py-3 bg-primary text-white font-medium rounded-lg hover:bg-primary-dark transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
        >
          {{ actionText }}
        </button>
      </template>

      <!-- Secondary action -->
      <template v-if="secondaryActionText">
        <Link
          v-if="actionRoute"
          :href="route(secondaryActionRoute)"
          class="inline-flex items-center justify-center px-6 py-3 bg-white text-gray-700 font-medium rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2"
        >
          {{ secondaryActionText }}
        </Link>
        <a
          v-else-if="secondaryActionHref"
          :href="secondaryActionHref"
          class="inline-flex items-center justify-center px-6 py-3 bg-white text-gray-700 font-medium rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2"
        >
          {{ secondaryActionText }}
        </a>
        <button
          v-else
          @click="$emit('secondary-action')"
          class="inline-flex items-center justify-center px-6 py-3 bg-white text-gray-700 font-medium rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2"
        >
          {{ secondaryActionText }}
        </button>
      </template>

      <!-- Slot for custom actions -->
      <slot name="actions" />
    </div>

    <!-- Additional content slot -->
    <div v-if="$slots.default" class="mt-8">
      <slot />
    </div>

    <!-- Search suggestion (for search empty states) -->
    <div
      v-if="icon === 'search' && !compact"
      class="mt-12 pt-8 border-t border-gray-200"
    >
      <h4 class="text-sm font-medium text-gray-700 mb-4">Попробуйте поискать:</h4>
      <div class="flex flex-wrap justify-center gap-2">
        <span class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition-colors duration-200 cursor-pointer">
          Пудровые брови
        </span>
        <span class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition-colors duration-200 cursor-pointer">
          Акварельные губы
        </span>
        <span class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition-colors duration-200 cursor-pointer">
          Межресничный татуаж
        </span>
        <span class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition-colors duration-200 cursor-pointer">
          Коррекция
        </span>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Floating animation for icon */
@keyframes float {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
}

.relative.inline-block svg {
  animation: float 3s ease-in-out infinite;
}

/* Reduce motion for accessibility */
@media (prefers-reduced-motion: reduce) {
  .animate-pulse,
  svg {
    animation: none;
  }
}

/* Gradient border for action buttons */
button.bg-primary,
a.bg-primary {
  position: relative;
  overflow: hidden;
  z-index: 1;
}

button.bg-primary::before,
a.bg-primary::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(255, 255, 255, 0.2),
    transparent
  );
  transition: left 0.5s ease-out;
  z-index: -1;
}

button.bg-primary:hover::before,
a.bg-primary:hover::before {
  left: 100%;
}
</style>
