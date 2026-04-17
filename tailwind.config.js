import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: '#1a4731',
                    dark: '#0f2b1e',
                    light: '#2d6a4f',
                },
                secondary: {
                    DEFAULT: '#f5e6cc',
                    dark: '#e8d4b0',
                    light: '#faf3e6',
                },
                accent: {
                    DEFAULT: '#ff6b6b',
                    dark: '#e05555',
                    light: '#ff8585',
                },
            },
        },
    },

    plugins: [forms],
};
