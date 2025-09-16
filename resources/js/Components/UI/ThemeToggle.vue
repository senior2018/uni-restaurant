<script setup>
import { useTheme } from '@/Composables/useTheme';
import { onMounted } from 'vue';

const { currentTheme, isDarkMode, toggleTheme, initializeTheme } = useTheme();

onMounted(() => {
  initializeTheme();
});
</script>

<template>
  <button
    @click="toggleTheme"
    :class="[
      'relative inline-flex h-6 w-11 items-center rounded-full transition-colors',
      'focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2',
      isDarkMode ? 'bg-green-600' : 'bg-gray-200'
    ]"
    role="switch"
    :aria-checked="isDarkMode"
    aria-label="Toggle dark mode"
  >
    <span
      :class="[
        'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
        isDarkMode ? 'translate-x-6' : 'translate-x-1'
      ]"
    />
    <span class="sr-only">
      {{ isDarkMode ? 'Switch to light mode' : 'Switch to dark mode' }}
    </span>
  </button>
</template>
