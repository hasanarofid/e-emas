import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            colors: {
                gold: {
                    50: '#FDFBF7',
                    100: '#FAF4E8',
                    200: '#F3E4C6',
                    300: '#EBD19B',
                    400: '#E0BD6E',
                    500: '#D4AF37', // Classic Jewelry Gold
                    600: '#B89225',
                    700: '#8F6E18',
                    800: '#695010',
                    900: '#47360A',
                },
            },
            fontFamily: {
                serif: ['"Playfair Display"', 'Georgia', ...defaultTheme.fontFamily.serif],
                sans: ['"Plus Jakarta Sans"', '"DM Sans"', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
