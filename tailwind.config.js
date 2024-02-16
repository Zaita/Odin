import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.jsx',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Roboto', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
              basedarkdarkred: "#700000",
              basedarkred : "#800000",
              basered : "#AB0000",
              themeMain : "var(--theme-main-colour)",
            },
            font: {
              '11': "font-size: 11px;"
            }
        },
    },

    plugins: [forms],
};
