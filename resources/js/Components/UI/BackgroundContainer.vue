<script setup>
import { computed } from 'vue';
import { useTheme } from '@/Composables/useTheme';

const props = defineProps({
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'white', 'gray', 'dark', 'gradient'].includes(value)
  },
  minHeight: {
    type: String,
    default: 'screen',
    validator: (value) => ['auto', 'screen', 'full'].includes(value)
  },
  padding: {
    type: String,
    default: 'none',
    validator: (value) => ['none', 'sm', 'md', 'lg', 'xl'].includes(value)
  }
});

const { getColor } = useTheme();

const containerClasses = computed(() => {
  const base = 'w-full';

  // Min height classes
  const minHeightClasses = {
    auto: '',
    screen: 'min-h-screen',
    full: 'min-h-full'
  };

  // Background classes
  const backgroundClasses = {
    primary: 'bg-green-50',
    white: 'bg-white',
    gray: 'bg-gray-50',
    dark: 'bg-gray-900',
    gradient: 'bg-gradient-to-br from-green-50 to-blue-50'
  };

  // Padding classes
  const paddingClasses = {
    none: '',
    sm: 'p-4',
    md: 'p-6',
    lg: 'p-8',
    xl: 'p-12'
  };

  const classes = [
    base,
    minHeightClasses[props.minHeight],
    backgroundClasses[props.variant],
    paddingClasses[props.padding]
  ];

  return classes.filter(Boolean).join(' ');
});
</script>

<template>
  <div :class="containerClasses">
    <slot />
  </div>
</template>
