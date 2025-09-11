<template>
    <img
        :src="logoSrc"
        alt="Our Restaurant Logo"
        class="h-9 w-auto"
        @error="handleImageError"
    />
</template>

<script setup>
import { ref, onMounted } from 'vue';

const logoSrc = ref('/storage/image/logo.png');
const fallbackOptions = [
    '/storage/image/logo.jpg',
    '/storage/image/logo.svg',
    '/favicon.png',
    '/favicon.ico'
];
let currentFallbackIndex = 0;

const handleImageError = () => {
    console.log('Logo failed to load, trying fallback...');
    if (currentFallbackIndex < fallbackOptions.length) {
        logoSrc.value = fallbackOptions[currentFallbackIndex];
        currentFallbackIndex++;
    } else {
        console.log('All logo fallbacks failed');
    }
};

onMounted(() => {
    // Preload the logo to check if it exists
    const img = new Image();
    img.onload = () => {
        console.log('Logo loaded successfully');
    };
    img.onerror = () => {
        console.log('Logo failed to load, using fallback');
        logoSrc.value = fallbackLogoSrc.value;
    };
    img.src = '/storage/image/logo.png';
});
</script>
