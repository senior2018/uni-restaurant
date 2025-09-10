import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    build: {
        // Ensure assets use relative URLs that work with HTTPS
        rollupOptions: {
            output: {
                manualChunks: undefined,
            },
        },
    },
    server: {
        // For local development
        https: false,
        hmr: {
            host: 'localhost',
        },
    },
    // Handle production builds
    base: process.env.NODE_ENV === 'production' ? '/' : '/',
});
