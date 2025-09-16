<script setup>
import { computed } from 'vue';
import { useTheme } from '@/Composables/useTheme';

const props = defineProps({
  layout: {
    type: String,
    default: 'horizontal',
    validator: (value) => ['horizontal', 'vertical', 'grid'].includes(value)
  },
  alignment: {
    type: String,
    default: 'center',
    validator: (value) => ['left', 'center', 'right', 'between', 'around'].includes(value)
  },
  spacing: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  },
  fullWidth: {
    type: Boolean,
    default: false
  }
});

const { getColor } = useTheme();

const containerClasses = computed(() => {
  const base = 'flex';

  // Layout classes
  const layoutClasses = {
    horizontal: 'flex-row',
    vertical: 'flex-col',
    grid: 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3'
  };

  // Alignment classes
  const alignmentClasses = {
    left: 'justify-start',
    center: 'justify-center',
    right: 'justify-end',
    between: 'justify-between',
    around: 'justify-around'
  };

  // Spacing classes
  const spacingClasses = {
    sm: props.layout === 'horizontal' ? 'space-x-2' : 'space-y-2',
    md: props.layout === 'horizontal' ? 'space-x-4' : 'space-y-4',
    lg: props.layout === 'horizontal' ? 'space-x-6' : 'space-y-6'
  };

  // Grid spacing
  const gridSpacingClasses = {
    sm: 'gap-2',
    md: 'gap-4',
    lg: 'gap-6'
  };

  const classes = [
    base,
    layoutClasses[props.layout],
    props.layout === 'grid' ? gridSpacingClasses[props.spacing] : spacingClasses[props.spacing],
    props.layout !== 'grid' ? alignmentClasses[props.alignment] : '',
    props.fullWidth ? 'w-full' : ''
  ];

  return classes.filter(Boolean).join(' ');
});
</script>

<template>
  <div :class="containerClasses">
    <slot />
  </div>
</template>
