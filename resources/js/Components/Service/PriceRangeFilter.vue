<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
  modelValue: String,
  min: {
    type: Number,
    default: 0
  },
  max: {
    type: Number,
    default: 10000
  }
});

const emit = defineEmits(['update:modelValue', 'change']);

const priceRanges = computed(() => [
  { label: 'Любая цена', value: null },
  { label: 'До 3 000₽', value: '0-3000' },
  { label: '3 000 - 5 000₽', value: '3000-5000' },
  { label: '5 000 - 8 000₽', value: '5000-8000' },
  { label: '8 000 - 12 000₽', value: '8000-12000' },
  { label: 'От 12 000₽', value: '12000-15000' }
]);

const selected = ref(props.modelValue);

watch(selected, (value) => {
  emit('update:modelValue', value);
  emit('change', value);
});
</script>

<template>
  <div class="relative">
    <select
      v-model="selected"
      class="pl-4 pr-10 py-3 rounded-full border border-gray-300 bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 appearance-none transition-all duration-300"
    >
      <option
        v-for="range in priceRanges"
        :key="range.value || 'all'"
        :value="range.value"
      >
        {{ range.label }}
      </option>
    </select>
    <div class="absolute right-3 top-3.5 pointer-events-none text-gray-400">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </div>
  </div>
</template>
