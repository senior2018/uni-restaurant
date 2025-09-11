<template>
    <img
        :src="logoSrc"
        alt="Our Restaurant Logo"
        class="h-9 w-auto"
        @error="handleImageError"
    />
</template>

<script setup>
import { ref } from 'vue';

const logoSrc = ref('/storage/image/logo.png');

const handleImageError = () => {
    console.log('Logo failed to load, using fallback');
    // Simple fallback to favicon
    logoSrc.value = '/favicon.png';
};
</script>
