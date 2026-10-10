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
            colors: {
                brand: {
                    // Background & Surface (Warm/Classic)
                    cream: '#fff8cf',      // Warm Off-White / Cream background utama
                    sand: '#fbe6c2',       // Light Yellow / Beige untuk card/section alternate
                    
                    // Text & Deep Elements (Dark Greens)
                    dark: '#063b00',       // Sangat gelap, untuk teks heading/paragraf utama
                    deep: '#266210',       // Dark green untuk footer atau dark section
                    
                    // Primary Brand (Greens)
                    primary: '#2a7c13',    // Elemen UI utama, garis arsitektur, border, aksen
                    light: '#76c457',      // Ikon, badge sekunder, elemen hijau segar
                    
                    // Accent & Highlight
                    leaf: '#90b800',       // Leaf green aksen
                    cta: '#e1e100',        // Bright lime/yellow (KHUSUS CTA Button "Hubungi Kami" / "Konsultasi")
                },
            },
            fontFamily: {
                serif: ['"Playfair Display"', 'Georgia', ...defaultTheme.fontFamily.serif],
                sans: ['"Plus Jakarta Sans"', '"DM Sans"', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                'architectural': '0 20px 45px -15px rgba(6, 59, 0, 0.12)',
                'card-hover': '0 25px 50px -12px rgba(6, 59, 0, 0.18)',
                'cta-glow': '0 10px 30px -5px rgba(225, 225, 0, 0.45)',
            },
        },
    },

    plugins: [forms],
};
