import defaultTheme from 'tailwindcss/defaultTheme';

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
                    50: '#eef4ff',
                    100: '#dce7fd',
                    200: '#c0d4fc',
                    300: '#94b8f9',
                    400: '#6192f4',
                    500: '#3d6cee',
                    600: '#274ce2',
                    700: '#1f3ad0',
                    800: '#2031a9',
                    900: '#1f2e85',
                    950: '#171f51',
                },
            },
        },
    },
    plugins: [],
};
