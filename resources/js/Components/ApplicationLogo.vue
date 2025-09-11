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
    '/storage/image/logo.svg',   // Try SVG first (more reliable)
    '/storage/image/logo.png',
    '/storage/image/Logo.png',   // Case sensitivity fallback
    '/storage/image/logo.jpg',
    '/favicon.png',
    'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICA8ZGVmcz4KICAgIDxsaW5lYXJHcmFkaWVudCBpZD0iZ3JhZDEiIHgxPSIwJSIgeTE9IjAlIiB4Mj0iMTAwJSIgeTI9IjEwMCUiPgogICAgICA8c3RvcCBvZmZzZXQ9IjAlIiBzdHlsZT0ic3RvcC1jb2xvcjojMTBiOTgxO3N0b3Atb3BhY2l0eToxIiAvPgogICAgICA8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0eWxlPSJzdG9wLWNvbG9yOiMwNTk2Njk7c3RvcC1vcGFjaXR5OjEiIC8+CiAgICA8L2xpbmVhckdyYWRpZW50PgogIDwvZGVmcz4KICA8cmVjdCB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIGZpbGw9InVybCgjZ3JhZDEpIiByeD0iNiIvPgogIDxjaXJjbGUgY3g9IjE2IiBjeT0iMTIiIHI9IjMiIGZpbGw9IndoaXRlIi8+CiAgPHJlY3QgeD0iMTMiIHk9IjE2IiB3aWR0aD0iNiIgaGVpZ2h0PSIyIiBmaWxsPSJ3aGl0ZSIgcng9IjEiLz4KICA8cmVjdCB4PSIxMSIgeT0iMTkiIHdpZHRoPSIxMCIgaGVpZ2h0PSIyIiBmaWxsPSJ3aGl0ZSIgcng9IjEiLz4KICA8cmVjdCB4PSI5IiB5PSIyMiIgd2lkdGg9IjE0IiBoZWlnaHQ9IjIiIGZpbGw9IndoaXRlIiByeD0iMSIvPgo8L3N2Zz4K'
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
