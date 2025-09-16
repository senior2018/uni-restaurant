<script setup>
import { computed } from 'vue';
import { useTheme } from '@/Composables/useTheme';

const props = defineProps({
  background: {
    type: String,
    default: 'gray',
    validator: (value) => ['white', 'gray', 'primary', 'transparent'].includes(value)
  },
  padding: {
    type: String,
    default: 'responsive',
    validator: (value) => ['none', 'sm', 'md', 'lg', 'xl', 'responsive'].includes(value)
  },
  maxWidth: {
    type: String,
    default: 'full',
    validator: (value) => ['sm', 'md', 'lg', 'xl', '2xl', 'full'].includes(value)
  },
  centered: {
    type: Boolean,
    default: false
  }
});

const { getColor } = useTheme();

const containerClasses = computed(() => {
  const base = 'min-h-screen w-full';

  // Background classes
  const backgroundClasses = {
    white: 'bg-white',
    gray: 'bg-gray-50',
    primary: 'bg-green-50',
    transparent: 'bg-transparent'
  };

  // Padding classes
  const paddingClasses = {
    none: '',
    sm: 'py-4',
    md: 'py-8',
    lg: 'py-12',
    xl: 'py-16',
    responsive: 'py-responsive'
  };

  // Max width classes
  const maxWidthClasses = {
    sm: 'max-w-sm',
    md: 'max-w-md',
    lg: 'max-w-lg',
    xl: 'max-w-xl',
    '2xl': 'max-w-2xl',
    full: 'max-w-full'
  };

  const classes = [
    base,
    backgroundClasses[props.background],
    paddingClasses[props.padding]
  ];

  return classes.filter(Boolean).join(' ');
});

const contentClasses = computed(() => {
  const base = 'container-responsive';

  const maxWidthClasses = {
    sm: 'max-w-sm',
    md: 'max-w-md',
    lg: 'max-w-lg',
    xl: 'max-w-xl',
    '2xl': 'max-w-2xl',
    full: 'max-w-full'
  };

  const classes = [
    base,
    props.maxWidth !== 'full' ? maxWidthClasses[props.maxWidth] : '',
    props.centered ? 'mx-auto' : ''
  ];

  return classes.filter(Boolean).join(' ');
});
</script>

<template>
  <main :class="containerClasses">
    <div :class="contentClasses">
      <slot />
    </div>
  </main>
</template>
