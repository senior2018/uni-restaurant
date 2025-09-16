<script setup>
import { computed } from 'vue';
import { useTheme } from '@/Composables/useTheme';

const props = defineProps({
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value)
  },
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'centered', 'full-width'].includes(value)
  },
  background: {
    type: String,
    default: 'white',
    validator: (value) => ['white', 'gray', 'transparent'].includes(value)
  },
  padding: {
    type: String,
    default: 'lg',
    validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value)
  },
  shadow: {
    type: String,
    default: 'lg',
    validator: (value) => ['none', 'sm', 'md', 'lg', 'xl'].includes(value)
  },
  border: {
    type: Boolean,
    default: true
  }
});

const { getColor } = useTheme();

const containerClasses = computed(() => {
  const base = 'w-full';

  // Size classes
  const sizeClasses = {
    sm: 'max-w-sm',
    md: 'max-w-md',
    lg: 'max-w-lg',
    xl: 'max-w-xl',
    '2xl': 'max-w-2xl',
    '3xl': 'max-w-3xl',
    '4xl': 'max-w-4xl',
    '5xl': 'max-w-5xl',
    '6xl': 'max-w-6xl',
    '7xl': 'max-w-7xl'
  };

  // Variant classes
  const variantClasses = {
    default: '',
    centered: 'mx-auto',
    'full-width': 'max-w-full'
  };

  // Background classes
  const backgroundClasses = {
    white: 'bg-white',
    gray: 'bg-gray-50',
    transparent: 'bg-transparent'
  };

  // Padding classes
  const paddingClasses = {
    sm: 'p-4',
    md: 'p-6',
    lg: 'p-8',
    xl: 'p-10',
    '2xl': 'p-12'
  };

  // Shadow classes
  const shadowClasses = {
    none: '',
    sm: 'shadow-sm',
    md: 'shadow-md',
    lg: 'shadow-lg',
    xl: 'shadow-xl'
  };

  // Border classes
  const borderClasses = props.border ? 'border border-green-100' : '';

  const classes = [
    base,
    sizeClasses[props.size],
    variantClasses[props.variant],
    backgroundClasses[props.background],
    paddingClasses[props.padding],
    shadowClasses[props.shadow],
    borderClasses,
    'rounded-2xl'
  ];

  return classes.filter(Boolean).join(' ');
});

const wrapperClasses = computed(() => {
  if (props.variant === 'centered') {
    return 'flex justify-center pt-32 pb-12 px-4 sm:px-6 lg:px-8';
  }
  return '';
});
</script>

<template>
  <div v-if="variant === 'centered'" class="min-h-screen" style="background-color: #ECFDF5;">
    <div :class="wrapperClasses">
      <div :class="containerClasses">
        <slot />
      </div>
    </div>
  </div>
  <div v-else :class="containerClasses">
    <slot />
  </div>
</template>
