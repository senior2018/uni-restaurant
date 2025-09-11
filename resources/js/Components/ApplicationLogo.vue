<template>
    <img
        :src="currentLogoSrc"
        alt="Our Restaurant Logo"
        class="h-8 w-8 mr-2"
        @error="handleImageError"
        @load="handleImageLoad"
    />
</template>

<script setup>
import { ref, onMounted } from 'vue';

const logoSources = [
    '/storage/image/logo.png',
    '/storage/image/Logo.png',  // Case sensitivity fallback
    '/storage/image/logo.jpg',
    '/storage/image/logo.svg',
    '/favicon.png',
    'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICA8cmVjdCB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIGZpbGw9IiMxMGI5ODEiIHJ4PSI0Ii8+CiAgPHRleHQgeD0iMTYiIHk9IjIwIiBmb250LWZhbWlseT0iQXJpYWwsIHNhbnMtc2VyaWYiIGZvbnQtc2l6ZT0iMTIiIGZvbnQtd2VpZ2h0PSJib2xkIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBmaWxsPSJ3aGl0ZSI+UjwvdGV4dD4KICA8L3N2Zz4K'  // Fallback SVG logo
];

const currentLogoSrc = ref(logoSources[0]);
const currentSourceIndex = ref(0);

const handleImageError = () => {
    console.log(`Logo failed to load: ${currentLogoSrc.value}`);

    // Try next source
    if (currentSourceIndex.value < logoSources.length - 1) {
        currentSourceIndex.value++;
        currentLogoSrc.value = logoSources[currentSourceIndex.value];
        console.log(`Trying fallback: ${currentLogoSrc.value}`);
    } else {
        console.log('All logo sources failed, using final fallback');
    }
};

const handleImageLoad = () => {
    console.log(`Logo loaded successfully: ${currentLogoSrc.value}`);
};

onMounted(() => {
    console.log('ApplicationLogo mounted, trying to load:', currentLogoSrc.value);
});
</script>
