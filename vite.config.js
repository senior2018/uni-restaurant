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
        // Ensure assets are built to the correct location
        outDir: 'public/build',
        emptyOutDir: true,
        manifest: 'manifest.json', // Put manifest in root of build dir, not .vite subdir
        rollupOptions: {
            output: {
                // Use more predictable asset naming for production
                assetFileNames: (assetInfo) => {
                    const info = assetInfo.name.split('.');
                    const ext = info[info.length - 1];
                    if (/\.(css)$/.test(assetInfo.name)) {
                        return `assets/[name]-[hash].${ext}`;
                    }
                    return `assets/[name]-[hash].${ext}`;
                },
                chunkFileNames: 'assets/[name]-[hash].js',
                entryFileNames: 'assets/[name]-[hash].js',
            },
        },
        // Generate source maps for debugging
        sourcemap: false,
        // Asset directory
        assetsDir: 'assets',
        // Ensure proper base path
        assetsInlineLimit: 0,
        // Ensure consistent builds
        minify: 'esbuild',
    },
    server: {
        https: false,
        hmr: {
            host: 'localhost',
        },
    },
    // Ensure correct base path
    base: process.env.NODE_ENV === 'production' ? '/' : '/',
});
