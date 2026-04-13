import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.tsx',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Calistoga', 'serif'],
                mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                primary: {
                    DEFAULT: '#0284C7', // sky-600  — main brand color
                    light:   '#38BDF8', // sky-400
                    dark:    '#0369A1', // sky-700
                },
                secondary: {
                    DEFAULT: '#0F172A', // slate-900 — dark panels / nav
                    light:   '#1E293B', // slate-800
                    muted:   '#64748B', // slate-500
                },
                accent: {
                    DEFAULT: '#7C3AED', // violet-600 — premium tech accent
                },
                surface: {
                    DEFAULT: '#F8FAFC', // slate-50  — page background
                    card:    '#FFFFFF',
                    border:  '#E2E8F0', // slate-200
                },
                danger: {
                    DEFAULT: '#E11D48', // rose-600
                },
                success: {
                    DEFAULT: '#059669', // emerald-600
                },
            },
        },
    },

    plugins: [forms],
};
