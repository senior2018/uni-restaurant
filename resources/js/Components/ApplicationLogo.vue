<template>
    <!-- Simple, reliable SVG logo that works everywhere -->
    <svg
        class="h-8 w-8 mr-2"
        viewBox="0 0 32 32"
        xmlns="http://www.w3.org/2000/svg"
    >
        <defs>
            <linearGradient id="logoGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" style="stop-color:#10b981;stop-opacity:1" />
                <stop offset="100%" style="stop-color:#059669;stop-opacity:1" />
            </linearGradient>
        </defs>
        <!-- Background circle -->
        <circle cx="16" cy="16" r="15" fill="url(#logoGrad)" stroke="#ffffff" stroke-width="1"/>
        <!-- Restaurant icon - fork and knife -->
        <g fill="white" stroke="white" stroke-width="0.5">
            <!-- Fork -->
            <line x1="8" y1="8" x2="8" y2="20" stroke-width="1"/>
            <line x1="7" y1="8" x2="9" y2="8" stroke-width="1"/>
            <line x1="7" y1="10" x2="9" y2="10" stroke-width="1"/>
            <line x1="7" y1="12" x2="9" y2="12" stroke-width="1"/>
            <!-- Knife -->
            <line x1="24" y1="8" x2="24" y2="20" stroke-width="1"/>
            <polygon points="24,8 26,10 24,12" fill="white"/>
            <!-- Plate -->
            <circle cx="16" cy="22" r="4" fill="none" stroke-width="1"/>
            <circle cx="16" cy="22" r="2" fill="white"/>
        </g>
    </svg>
</template>

<script setup>
// No JavaScript needed - pure SVG that always works
</script>
