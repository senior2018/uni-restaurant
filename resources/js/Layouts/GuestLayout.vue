<script setup>
import { onMounted, ref } from 'vue';
import { Head } from '@inertiajs/vue3';

// Optional: fade-in animation
const show = ref(false);
onMounted(() => {
  setTimeout(() => (show.value = true), 50);
});
</script>

<template>
  <div class="relative min-h-screen flex flex-col items-center justify-center overflow-x-hidden">
    <!-- Blurred, emerald/blue overlay background image -->
    <div
      class="fixed inset-0 z-0 bg-cover bg-center bg-no-repeat"
      :style="{
        backgroundImage: 'url(/storage/Images/rest3.jpeg)',
        filter: 'blur(4px) brightness(0.7)',
      }"
      aria-hidden="true"
    ></div>
    <!-- Emerald to sky blue gradient overlay for contrast -->
    <div class="fixed inset-0 z-10 bg-gradient-to-br from-emerald-800/90 via-emerald-700/80 to-sky-700/70"></div>

    <!-- Header slot (optional) -->
    <header class="relative z-20 w-full flex justify-center pt-8 pb-4">
      <slot name="header">
        <div class="flex items-center gap-2">
          <svg class="h-10 w-10" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <linearGradient id="logoGrad6" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" style="stop-color:#10b981;stop-opacity:1" />
                <stop offset="100%" style="stop-color:#059669;stop-opacity:1" />
              </linearGradient>
            </defs>
            <circle cx="16" cy="16" r="15" fill="url(#logoGrad6)" stroke="#ffffff" stroke-width="1"/>
            <g fill="white" stroke="white" stroke-width="0.5">
              <line x1="8" y1="8" x2="8" y2="20" stroke-width="1"/>
              <line x1="7" y1="8" x2="9" y2="8" stroke-width="1"/>
              <line x1="7" y1="10" x2="9" y2="10" stroke-width="1"/>
              <line x1="7" y1="12" x2="9" y2="12" stroke-width="1"/>
              <line x1="24" y1="8" x2="24" y2="20" stroke-width="1"/>
              <polygon points="24,8 26,10 24,12" fill="white"/>
              <circle cx="16" cy="22" r="4" fill="none" stroke-width="1"/>
              <circle cx="16" cy="22" r="2" fill="white"/>
            </g>
          </svg>
          <span class="text-2xl font-bold text-white tracking-wide drop-shadow">Our Restaurant</span>
        </div>
      </slot>
    </header>

    <!-- Main content area -->
    <main
      class="relative z-20 flex-1 w-full flex flex-col items-center justify-center px-4 py-8"
    >
      <div
        class="w-full max-w-lg md:max-w-xl lg:max-w-2xl xl:max-w-3xl bg-white/70 dark:bg-gray-900/80 rounded-2xl shadow-2xl p-8 md:p-12 backdrop-blur-md border border-emerald-900/20 transition-all duration-700 text-emerald-900"
        :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
      >
        <slot />
      </div>
    </main>

    <!-- Footer slot (optional) -->
    <footer class="relative z-20 w-full flex justify-center py-6 mt-auto">
      <slot name="footer">
        <span class="text-sm text-white/80">&copy; {{ new Date().getFullYear() }} Our Restaurant. All rights reserved.</span>
      </slot>
    </footer>
  </div>
</template>

<style scoped>
/* Ensure background image is lightweight and doesn't interfere with content */
@media (max-width: 640px) {
  .bg-cover {
    background-position: center top;
  }
}
</style>
