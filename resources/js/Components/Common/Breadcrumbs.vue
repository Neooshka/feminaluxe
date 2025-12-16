<script setup>
import { ChevronRightIcon, HomeIcon } from '@heroicons/vue/24/outline';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  items: {
    type: Array,
    required: true,
    validator: (items) => {
      return items.every(item =>
        typeof item === 'object' &&
        'name' in item &&
        'href' in item
      );
    }
  },
  showHome: {
    type: Boolean,
    default: true
  },
  separator: {
    type: String,
    default: 'chevron',
    validator: (value) => ['chevron', 'slash', 'arrow'].includes(value)
  }
});

const getSeparator = () => {
  switch (props.separator) {
    case 'slash':
      return '/';
    case 'arrow':
      return '→';
    case 'chevron':
    default:
      return null;
  }
};

const separator = getSeparator();
</script>

<template>
  <nav aria-label="Хлебные крошки" class="flex items-center space-x-2 text-sm">
    <!-- Home link -->
    <Link
      v-if="showHome"
      :href="route('home')"
      class="inline-flex items-center text-gray-500 hover:text-primary transition-colors duration-300"
      aria-label="Главная страница"
    >
      <HomeIcon class="h-4 w-4" />
    </Link>

    <!-- Breadcrumb items -->
    <template v-for="(item, index) in items" :key="item.name">
      <!-- Separator -->
      <div v-if="index > 0 || showHome" class="flex items-center text-gray-400">
        <template v-if="separator">
          <span class="mx-2">{{ separator }}</span>
        </template>
        <template v-else>
          <ChevronRightIcon class="h-4 w-4 mx-1" />
        </template>
      </div>

      <!-- Item -->
      <div class="flex items-center">
        <Link
          v-if="!item.current && item.href"
          :href="item.href"
          class="text-gray-600 hover:text-primary transition-colors duration-300"
          :aria-current="item.current ? 'page' : null"
        >
          {{ item.name }}
        </Link>
        <span
          v-else
          class="text-gray-900 font-medium"
          :aria-current="item.current ? 'page' : null"
        >
          {{ item.name }}
        </span>
      </div>
    </template>
  </nav>
</template>

<style scoped>
/* Micro-interaction for hover effect */
a {
  position: relative;
  padding: 2px 4px;
  border-radius: 4px;
}

a:hover {
  background-color: rgba(var(--primary-rgb), 0.05);
}

/* Animation for current page */
[aria-current="page"] {
  position: relative;
}

[aria-current="page"]::after {
  content: '';
  position: absolute;
  bottom: -2px;
  left: 0;
  right: 0;
  height: 2px;
  background-color: var(--primary-color, #ec4899);
  animation: underline-appear 0.3s ease-out;
}

@keyframes underline-appear {
  from {
    transform: scaleX(0);
    opacity: 0;
  }
  to {
    transform: scaleX(1);
    opacity: 1;
  }
}
</style>
