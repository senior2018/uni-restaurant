import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
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
        rollupOptions: {
            output: {
                // Disable code splitting for now to avoid chunk loading issues
                manualChunks: undefined,
            },
        },
        // Ensure assets are built with correct base path
        assetsDir: 'assets',
        outDir: 'public/build',
    },
    server: {
        https: false,
        hmr: {
            host: 'localhost',
        },
    },
    // Ensure correct base path in production
    base: '/',
});
