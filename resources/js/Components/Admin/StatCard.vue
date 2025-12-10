<template>
  <div class="bg-white rounded-xl shadow p-6">
    <div class="flex items-center">
      <div :class="`bg-${color}-500`" class="w-12 h-12 rounded-lg flex items-center justify-center mr-4">
        <component :is="icon" class="w-6 h-6 text-white" />
      </div>
      <div>
        <p class="text-sm font-medium text-gray-600">{{ title }}</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ value }}</p>
        <p v-if="subtitle" class="text-xs text-gray-500 mt-1">{{ subtitle }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: [String, Number],
    required: true
  },
  icon: {
    type: Object,
    required: true
  },
  color: {
    type: String,
    default: 'blue'
  },
  subtitle: {
    type: String,
    default: ''
  }
})
</script>
