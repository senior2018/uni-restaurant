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
const fallbackLogoSrc = ref('/storage/image/logo.jpg');

const handleImageError = () => {
    console.log('Logo failed to load, trying fallback...');
    logoSrc.value = fallbackLogoSrc.value;
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
