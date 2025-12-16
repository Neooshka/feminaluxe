<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  modelValue: String,
  categories: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['update:modelValue', 'change']);

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
      <option value="all">Все категории</option>
      <option
        v-for="category in categories"
        :key="category.slug"
        :value="category.slug"
      >
        {{ category.name }}
      </option>
    </select>
    <div class="absolute right-3 top-3.5 pointer-events-none text-gray-400">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </div>
  </div>
</template>
