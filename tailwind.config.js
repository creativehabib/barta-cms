import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './app/**/*.php',
        './themes/**/*.blade.php',
        './plugins/**/*.blade.php',
        './storage/framework/views/*.php',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                sans: ['"Hind Siliguri"', '"Noto Sans Bengali"', ...defaultTheme.fontFamily.sans],
                bengali: ['"Hind Siliguri"', '"Noto Sans Bengali"', ...defaultTheme.fontFamily.sans],
                serif: ['"Tiro Bangla"', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                brand: {
                    50: '#fef2f2', 100: '#fee2e2', 200: '#fecaca', 300: '#fca5a5',
                    400: '#f87171', 500: '#e11d2a', 600: '#c81420', 700: '#a5121b',
                    800: '#88131b', 900: '#71151c', 950: '#3e060a',
                },
                ink: {
                    50: '#f7f7f8', 100: '#eceef1', 200: '#d6dae0', 300: '#b1b8c2',
                    400: '#858f9e', 500: '#667082', 600: '#515a6b', 700: '#434a58',
                    800: '#2b3039', 900: '#16181d', 950: '#0b0c0f',
                },
            },
        },
    },
    plugins: [forms, typography],
};
