<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  animation: {
    type: String,
    default: 'fadeIn',
    validator: (value) => ['fadeIn', 'slideUp', 'slideDown', 'slideLeft', 'slideRight', 'scale', 'bounce'].includes(value)
  },
  delay: {
    type: Number,
    default: 0
  },
  duration: {
    type: String,
    default: '0.5s'
  },
  trigger: {
    type: String,
    default: 'onMount',
    validator: (value) => ['onMount', 'onScroll', 'onHover'].includes(value)
  }
});

const emit = defineEmits(['animationStart', 'animationEnd']);

const cardRef = ref(null);
const isVisible = ref(false);
const isAnimating = ref(false);

const animationClasses = {
  fadeIn: {
    initial: 'opacity-0',
    animate: 'opacity-100',
    transition: 'transition-opacity duration-500 ease-in-out'
  },
  slideUp: {
    initial: 'opacity-0 transform translate-y-8',
    animate: 'opacity-100 transform translate-y-0',
    transition: 'transition-all duration-500 ease-out'
  },
  slideDown: {
    initial: 'opacity-0 transform -translate-y-8',
    animate: 'opacity-100 transform translate-y-0',
    transition: 'transition-all duration-500 ease-out'
  },
  slideLeft: {
    initial: 'opacity-0 transform translate-x-8',
    animate: 'opacity-100 transform translate-x-0',
    transition: 'transition-all duration-500 ease-out'
  },
  slideRight: {
    initial: 'opacity-0 transform -translate-x-8',
    animate: 'opacity-100 transform translate-x-0',
    transition: 'transition-all duration-500 ease-out'
  },
  scale: {
    initial: 'opacity-0 transform scale-95',
    animate: 'opacity-100 transform scale-100',
    transition: 'transition-all duration-500 ease-out'
  },
  bounce: {
    initial: 'opacity-0 transform scale-95',
    animate: 'opacity-100 transform scale-100',
    transition: 'transition-all duration-500 ease-bounce'
  }
};

const startAnimation = () => {
  if (isAnimating.value) return;

  isAnimating.value = true;
  emit('animationStart');

  setTimeout(() => {
    isVisible.value = true;
    emit('animationEnd');
  }, props.delay);
};

const handleScroll = () => {
  if (props.trigger !== 'onScroll' || !cardRef.value) return;

  const rect = cardRef.value.getBoundingClientRect();
  const isInView = rect.top < window.innerHeight && rect.bottom > 0;

  if (isInView && !isVisible.value) {
    startAnimation();
  }
};

onMounted(() => {
  if (props.trigger === 'onMount') {
    startAnimation();
  } else if (props.trigger === 'onScroll') {
    window.addEventListener('scroll', handleScroll);
    handleScroll(); // Check initial position
  }
});

onUnmounted(() => {
  if (props.trigger === 'onScroll') {
    window.removeEventListener('scroll', handleScroll);
  }
});

const cardClasses = computed(() => {
  const animation = animationClasses[props.animation];
  return [
    animation.transition,
    isVisible.value ? animation.animate : animation.initial
  ];
});
</script>

<template>
  <div
    ref="cardRef"
    :class="cardClasses"
    :style="{ transitionDuration: duration }"
    @mouseenter="trigger === 'onHover' && !isVisible ? startAnimation() : null"
  >
    <slot />
  </div>
</template>
