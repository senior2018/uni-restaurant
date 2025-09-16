<script setup>
import { computed } from 'vue';
import { useTheme } from '@/Composables/useTheme';

const props = defineProps({
  type: {
    type: String,
    default: 'success',
    validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
  },
  message: {
    type: String,
    required: true
  },
  closable: {
    type: Boolean,
    default: true
  },
  showIcon: {
    type: Boolean,
    default: true
  }
});

const emit = defineEmits(['close']);

const { getColor } = useTheme();

const messageClasses = computed(() => {
  const base = 'px-4 py-3 rounded mb-4 flex items-center justify-between shadow';
  const typeClasses = {
    success: 'bg-green-100 border border-green-400 text-green-700',
    error: 'bg-red-100 border border-red-400 text-red-700',
    warning: 'bg-yellow-100 border border-yellow-400 text-yellow-700',
    info: 'bg-blue-100 border border-blue-400 text-blue-700'
  };
  return `${base} ${typeClasses[props.type]}`;
});

const iconClasses = computed(() => {
  const typeIcons = {
    success: 'fas fa-check-circle',
    error: 'fas fa-exclamation-circle',
    warning: 'fas fa-exclamation-triangle',
    info: 'fas fa-info-circle'
  };
  return typeIcons[props.type];
});

const closeButtonClasses = computed(() => {
  const typeClasses = {
    success: 'text-green-700 hover:text-green-900',
    error: 'text-red-700 hover:text-red-900',
    warning: 'text-yellow-700 hover:text-yellow-900',
    info: 'text-blue-700 hover:text-blue-900'
  };
  return typeClasses[props.type];
});

const handleClose = () => {
  emit('close');
};
</script>

<template>
  <div class="w-full px-responsive mt-4 relative z-10">
    <div :class="messageClasses">
      <span v-if="showIcon">
        <i :class="[iconClasses, 'mr-2']"></i>{{ message }}
      </span>
      <span v-else>{{ message }}</span>
      <button
        v-if="closable"
        @click="handleClose"
        :class="closeButtonClasses"
        class="text-lg font-bold"
        aria-label="Close message"
      >
        &times;
      </button>
    </div>
  </div>
</template>
