import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                primary:   '#F26052',   // Coral red — CTAs, accents
                secondary: '#5F75F4',   // Blue — links, highlights
                dark:      '#24302E',   // Deep charcoal green — headers, dark sections
                light:     '#F0EFEB',   // Warm off-white — page background
            },
            fontFamily: {
                alumni: ['"Alumni Sans"', ...defaultTheme.fontFamily.sans],
                outfit: ['Outfit',        ...defaultTheme.fontFamily.sans],
            },
            fontSize: {
                'h1':         ['76px', { lineHeight: '1.2' }],
                'h2':         ['61px', { lineHeight: '1.2' }],
                'h3':         ['49px', { lineHeight: '1.2' }],
                'h4':         ['39px', { lineHeight: '1.2' }],
                'h5':         ['31px', { lineHeight: '1.2' }],
                'h6':         ['25px', { lineHeight: '1.2' }],
                'sm-header':  ['20px', { lineHeight: '1.2' }],
                'sm-header2': ['13px', { lineHeight: '1.2' }],
                'body-lg':    ['16px', { lineHeight: '1.5' }],
                'body-md':    ['13px', { lineHeight: '1.5' }],
                'body-sm':    ['10px', { lineHeight: '1.5' }],
            },
            boxShadow: {
                'card':  '0 2px 12px rgba(36,48,46,0.08)',
                'hover': '0 8px 32px rgba(36,48,46,0.14)',
            },
            borderRadius: {
                '2xl': '1rem',
                '3xl': '1.5rem',
            },
            backgroundImage: {
                'gradient-brand': 'linear-gradient(135deg, #5F75F4 0%, #F26052 100%)',
            },
        },
    },
    plugins: [forms, typography],
};
