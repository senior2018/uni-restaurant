<script setup>
import { computed } from 'vue';
import { useTheme } from '@/Composables/useTheme';

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  },
  background: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'white', 'gray', 'transparent'].includes(value)
  },
  size: {
    type: String,
    default: 'lg',
    validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value)
  },
  centered: {
    type: Boolean,
    default: true
  }
});

const { getColor } = useTheme();

const sectionClasses = computed(() => {
  const base = 'text-center';
  const sizeClasses = {
    sm: 'pt-16 pb-12',
    md: 'pt-24 pb-16',
    lg: 'pt-32 pb-20',
    xl: 'pt-40 pb-24'
  };
  return `${base} ${sizeClasses[props.size]}`;
});

const containerClasses = computed(() => {
  const base = 'mx-auto px-4 sm:px-6 lg:px-8';
  const maxWidthClasses = {
    sm: 'max-w-2xl',
    md: 'max-w-3xl',
    lg: 'max-w-4xl',
    xl: 'max-w-6xl'
  };
  return `${base} ${maxWidthClasses[props.size]}`;
});

const titleClasses = computed(() => {
  const base = 'font-extrabold text-gray-900 mb-6 leading-tight';
  const sizeClasses = {
    sm: 'text-3xl',
    md: 'text-4xl',
    lg: 'text-5xl',
    xl: 'text-6xl'
  };
  return `${base} ${sizeClasses[props.size]}`;
});

const subtitleClasses = computed(() => {
  const base = 'text-gray-600 mb-8';
  const sizeClasses = {
    sm: 'text-base',
    md: 'text-lg',
    lg: 'text-lg',
    xl: 'text-xl'
  };
  return `${base} ${sizeClasses[props.size]}`;
});

const backgroundClasses = computed(() => {
  const backgroundClasses = {
    primary: 'bg-green-50',
    white: 'bg-white',
    gray: 'bg-gray-50',
    transparent: 'bg-transparent'
  };
  return backgroundClasses[props.background];
});
</script>

<template>
  <section :class="[sectionClasses, backgroundClasses]">
    <div :class="containerClasses">
      <h1 :class="titleClasses">{{ title }}</h1>
      <p v-if="subtitle" :class="subtitleClasses">{{ subtitle }}</p>
      <div v-if="$slots.actions" class="flex justify-center space-x-4">
        <slot name="actions" />
      </div>
      <div v-if="$slots.content" class="mt-8">
        <slot name="content" />
      </div>
    </div>
  </section>
</template>
