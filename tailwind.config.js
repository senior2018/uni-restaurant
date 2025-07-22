import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: '#22c55e', // Tailwind green-500
                    light: '#4ade80',  // green-400
                    dark: '#16a34a',   // green-600
                    contrast: '#fff',
                },
                accent: {
                    yellow: '#fde68a', // yellow-200
                    blue: '#38bdf8',  // sky-400
                    purple: '#a78bfa', // purple-400
                    pink: '#f472b6',  // pink-400
                },
            },
            maxWidth: {
                '7xl': '100%',
            },
        },
    },

    plugins: [forms],
};
