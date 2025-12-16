<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { ChevronUpIcon } from '@heroicons/vue/24/outline';

const isVisible = ref(false);
const scrollProgress = ref(0);

const checkScrollPosition = () => {
  const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
  const windowHeight = window.innerHeight;
  const documentHeight = document.documentElement.scrollHeight - windowHeight;

  // Show button after 300px scroll
  isVisible.value = scrollTop > 300;

  // Calculate scroll progress (0-100%)
  scrollProgress.value = Math.min(
    (scrollTop / documentHeight) * 100,
    100
  );
};

const scrollToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  });

  // Track analytics event
  if (window.dataLayer) {
    window.dataLayer.push({
      event: 'scroll_to_top_click'
    });
  }
};

// Debounce scroll event
let scrollTimeout;
const handleScroll = () => {
  clearTimeout(scrollTimeout);
  scrollTimeout = setTimeout(checkScrollPosition, 50);
};

onMounted(() => {
  window.addEventListener('scroll', handleScroll);
  checkScrollPosition(); // Initial check
});

onBeforeUnmount(() => {
  window.removeEventListener('scroll', handleScroll);
  clearTimeout(scrollTimeout);
});
</script>

<template>
  <button
    v-show="isVisible"
    @click="scrollToTop"
    aria-label="Прокрутить наверх"
    class="fixed bottom-8 right-8 z-40 flex items-center justify-center w-14 h-14 bg-primary text-white rounded-full shadow-lg hover:shadow-xl hover:scale-110 active:scale-95 transition-all duration-300 group focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
    role="button"
    tabindex="0"
  >
    <!-- Animated progress circle -->
    <svg class="absolute inset-0 w-full h-full transform -rotate-90">
      <circle
        cx="50%"
        cy="50%"
        r="48%"
        fill="none"
        stroke="rgba(255, 255, 255, 0.2)"
        stroke-width="1"
      />
      <circle
        cx="50%"
        cy="50%"
        r="48%"
        fill="none"
        stroke="currentColor"
        :stroke-dasharray="2 * Math.PI * 48"
        :stroke-dashoffset="2 * Math.PI * 48 * (1 - scrollProgress / 100)"
        stroke-width="1"
        class="transition-all duration-300"
      />
    </svg>

    <!-- Icon with hover animation -->
    <ChevronUpIcon class="h-6 w-6 transform group-hover:-translate-y-0.5 transition-transform duration-300" />

    <!-- Tooltip on hover -->
    <div class="absolute bottom-full right-1/2 translate-x-1/2 mb-2 px-3 py-1 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap pointer-events-none">
      Наверх
      <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
    </div>
  </button>
</template>

<style scoped>
/* Pulse animation for attention */
@keyframes pulse-attention {
  0%, 100% {
    box-shadow: 0 10px 25px rgba(236, 72, 153, 0.3);
  }
  50% {
    box-shadow: 0 10px 25px rgba(236, 72, 153, 0.6);
  }
}

button {
  animation: pulse-attention 2s infinite;
}

/* Reduce animation on reduced motion preference */
@media (prefers-reduced-motion: reduce) {
  button {
    animation: none;
    transition: none;
  }

  .group-hover\:-translate-y-0\.5 {
    transform: none;
  }
}

/* Ripple effect on click */
button:active::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  transform: translate(-50%, -50%) scale(0);
  animation: ripple 0.6s ease-out;
}

@keyframes ripple {
  to {
    transform: translate(-50%, -50%) scale(2);
    opacity: 0;
  }
}
</style>
