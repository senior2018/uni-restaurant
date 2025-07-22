<template>
  <div class="flex items-center gap-1">
    <button
      v-for="n in max"
      :key="n"
      type="button"
      @click="setRating(n)"
      @mouseover="hoverRating = n"
      @mouseleave="hoverRating = 0"
      class="focus:outline-none"
    >
      <i
        class="fas fa-star"
        :class="n <= (hoverRating || modelValue) ? 'text-yellow-400' : 'text-gray-300'"
        style="font-size: 2em;"
      ></i>
    </button>
  </div>
</template>

<script setup>
import { ref, watch, defineProps, defineEmits } from 'vue';
const props = defineProps({
  modelValue: { type: Number, default: 0 },
  max: { type: Number, default: 5 },
});
const emit = defineEmits(['update:modelValue']);
const hoverRating = ref(0);
function setRating(n) {
  emit('update:modelValue', n);
}
</script>
