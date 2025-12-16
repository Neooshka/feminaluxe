<script setup>
import { computed } from 'vue';

const props = defineProps({
  size: {
    type: String,
    default: 'medium',
    validator: (value) => ['small', 'medium', 'large', 'xlarge'].includes(value)
  },
  color: {
    type: String,
    default: 'primary'
  },
  text: {
    type: String,
    default: ''
  },
  fullScreen: {
    type: Boolean,
    default: false
  }
});

const sizeClasses = computed(() => {
  const sizes = {
    small: 'w-8 h-8',
    medium: 'w-12 h-12',
    large: 'w-16 h-16',
    xlarge: 'w-24 h-24'
  };
  return sizes[props.size] || sizes.medium;
});

const colorClasses = computed(() => {
  const colors = {
    primary: 'text-primary',
    white: 'text-white',
    gray: 'text-gray-600',
    pink: 'text-pink-600',
    purple: 'text-purple-600'
  };
  return colors[props.color] || colors.primary;
});

const spinnerClasses = computed(() => {
  return `${sizeClasses.value} ${colorClasses.value}`;
});
</script>

<template>
  <div
    :class="[
      'flex flex-col items-center justify-center',
      fullScreen ? 'fixed inset-0 z-50 bg-white/80 backdrop-blur-sm' : 'py-20'
    ]"
    role="status"
    aria-live="polite"
    :aria-label="text || 'Загрузка...'"
  >
    <!-- Spinner -->
    <div class="relative">
      <!-- Outer ring -->
      <div
        :class="[
          spinnerClasses,
          'animate-spin rounded-full border-4 border-solid border-current border-r-transparent'
        ]"
        style="animation-duration: 1s"
      />

      <!-- Inner dots (optional) -->
      <div
        v-if="['large', 'xlarge'].includes(size)"
        class="absolute inset-0 flex items-center justify-center"
      >
        <div class="flex space-x-1">
          <div
            class="w-1 h-1 rounded-full bg-current animate-bounce"
            style="animation-delay: -0.32s"
          />
          <div
            class="w-1 h-1 rounded-full bg-current animate-bounce"
            style="animation-delay: -0.16s"
          />
          <div
            class="w-1 h-1 rounded-full bg-current animate-bounce"
          />
        </div>
      </div>
    </div>

    <!-- Loading text -->
    <div
      v-if="text"
      class="mt-4 text-center"
    >
      <p class="text-gray-600 font-medium">{{ text }}</p>
      <p v-if="fullScreen" class="text-sm text-gray-500 mt-2">
        Пожалуйста, подождите...
      </p>
    </div>

    <!-- Percentage loader (for known progress) -->
    <slot name="progress">
      <div
        v-if="$slots.progress"
        class="mt-6 w-48 bg-gray-200 rounded-full h-2 overflow-hidden"
      >
        <div
          class="bg-primary h-full rounded-full transition-all duration-300"
          :style="{ width: $slots.progress() }"
        />
      </div>
    </slot>

    <!-- Additional content slot -->
    <div v-if="$slots.default" class="mt-6">
      <slot />
    </div>

    <!-- Loading dots animation for text -->
    <div
      v-if="!text && !$slots.default"
      class="mt-4 flex space-x-1"
    >
      <div
        class="w-2 h-2 rounded-full bg-gray-400 animate-pulse"
        style="animation-delay: -0.32s"
      />
      <div
        class="w-2 h-2 rounded-full bg-gray-400 animate-pulse"
        style="animation-delay: -0.16s"
      />
      <div
        class="w-2 h-2 rounded-full bg-gray-400 animate-pulse"
      />
    </div>
  </div>
</template>

<style scoped>
/* Custom spin animation */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

/* Reduce motion for accessibility */
@media (prefers-reduced-motion: reduce) {
  .animate-spin,
  .animate-bounce,
  .animate-pulse {
    animation: none;
  }

  .animate-spin {
    animation: spin 2s linear infinite;
  }
}

/* Progress bar animation */
@keyframes progress-shimmer {
  0% {
    background-position: -1000px 0;
  }
  100% {
    background-position: 1000px 0;
  }
}

.bg-primary {
  background-image: linear-gradient(
    90deg,
    transparent 0%,
    rgba(255, 255, 255, 0.3) 50%,
    transparent 100%
  );
  background-size: 1000px 100%;
  animation: progress-shimmer 2s infinite linear;
}

/* Fade in animation for fullscreen loader */
.fixed.inset-0 {
  animation: fade-in 0.3s ease-out;
}

@keyframes fade-in {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}
</style>
