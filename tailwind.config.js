import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

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
                sans: ['Inter var', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', 'Monaco', 'Cascadia Code', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                // Palette personnalisée pour l'app N8N
                'n8n': {
                    50: '#f0f9ff',
                    100: '#e0f2fe',
                    200: '#bae6fd',
                    300: '#7dd3fc',
                    400: '#38bdf8',
                    500: '#0ea5e9',
                    600: '#0284c7',
                    700: '#0369a1',
                    800: '#075985',
                    900: '#0c4a6e',
                },
            },
            animation: {
                'fade-in': 'fadeIn 0.5s ease-in-out',
                'slide-up': 'slideUp 0.3s ease-out',
                'scale-in': 'scaleIn 0.2s ease-out',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { transform: 'translateY(10px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                scaleIn: {
                    '0%': { transform: 'scale(0.95)', opacity: '0' },
                    '100%': { transform: 'scale(1)', opacity: '1' },
                },
            },
            spacing: {
                '18': '4.5rem',
                '88': '22rem',
                '128': '32rem',
            },
            maxWidth: {
                '8xl': '88rem',
                '9xl': '96rem',
            },
            zIndex: {
                '60': '60',
                '70': '70',
                '80': '80',
                '90': '90',
                '100': '100',
            },
            backdropBlur: {
                xs: '2px',
            },
            typography: (theme) => ({
                DEFAULT: {
                    css: {
                        color: theme('colors.gray.700'),
                        maxWidth: 'none',
                        h1: {
                            color: theme('colors.gray.900'),
                            fontWeight: '700',
                        },
                        h2: {
                            color: theme('colors.gray.900'),
                            fontWeight: '600',
                        },
                        h3: {
                            color: theme('colors.gray.900'),
                            fontWeight: '600',
                        },
                        'code::before': {
                            content: '""',
                        },
                        'code::after': {
                            content: '""',
                        },
                        code: {
                            backgroundColor: theme('colors.gray.100'),
                            color: theme('colors.gray.800'),
                            fontWeight: '500',
                            fontSize: '0.875em',
                            padding: '0.25rem 0.375rem',
                            borderRadius: '0.25rem',
                        },
                        pre: {
                            backgroundColor: theme('colors.gray.900'),
                            color: theme('colors.gray.100'),
                        },
                        'pre code': {
                            backgroundColor: 'transparent',
                            color: 'inherit',
                            padding: '0',
                        },
                        blockquote: {
                            borderLeftColor: theme('colors.indigo.500'),
                            backgroundColor: theme('colors.indigo.50'),
                            padding: '1rem 1.5rem',
                            borderRadius: '0.5rem',
                        },
                        'blockquote p:first-of-type::before': {
                            content: '""',
                        },
                        'blockquote p:last-of-type::after': {
                            content: '""',
                        },
                        a: {
                            color: theme('colors.indigo.600'),
                            textDecoration: 'none',
                            fontWeight: '500',
                            '&:hover': {
                                color: theme('colors.indigo.700'),
                                textDecoration: 'underline',
                            },
                        },
                        'ul > li': {
                            paddingLeft: '1.5rem',
                        },
                        'ol > li': {
                            paddingLeft: '1.5rem',
                        },
                        'ul > li::marker': {
                            color: theme('colors.indigo.500'),
                        },
                        'ol > li::marker': {
                            color: theme('colors.indigo.500'),
                        },
                        table: {
                            fontSize: '0.875rem',
                        },
                        'thead th': {
                            backgroundColor: theme('colors.gray.50'),
                            fontWeight: '600',
                        },
                        'tbody tr': {
                            borderBottomColor: theme('colors.gray.200'),
                        },
                        'tbody tr:hover': {
                            backgroundColor: theme('colors.gray.50'),
                        },
                    },
                },
                sm: {
                    css: {
                        fontSize: '0.875rem',
                        lineHeight: '1.5',
                    },
                },
            }),
        },
    },

    plugins: [
        forms({
            strategy: 'class', // or 'base'
        }),
        typography,
        // Plugin personnalisé pour les composants workflow
        function({ addComponents, theme }) {
            addComponents({
                '.workflow-card': {
                    '@apply bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200 hover:border-gray-300': {},
                },
                '.workflow-badge': {
                    '@apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium': {},
                },
                '.workflow-badge-primary': {
                    '@apply bg-indigo-100 text-indigo-800': {},
                },
                '.workflow-badge-secondary': {
                    '@apply bg-gray-100 text-gray-800': {},
                },
                '.workflow-badge-success': {
                    '@apply bg-green-100 text-green-800': {},
                },
                '.workflow-badge-warning': {
                    '@apply bg-yellow-100 text-yellow-800': {},
                },
                '.workflow-badge-error': {
                    '@apply bg-red-100 text-red-800': {},
                },
                '.json-viewer': {
                    '@apply bg-gray-900 text-gray-100 rounded-lg overflow-hidden font-mono text-sm': {},
                },
                '.json-syntax-key': {
                    '@apply text-blue-300': {},
                },
                '.json-syntax-string': {
                    '@apply text-green-300': {},
                },
                '.json-syntax-number': {
                    '@apply text-orange-300': {},
                },
                '.json-syntax-boolean': {
                    '@apply text-yellow-300': {},
                },
                '.json-syntax-null': {
                    '@apply text-red-300': {},
                },
                '.btn': {
                    '@apply inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150': {},
                },
                '.btn-primary': {
                    '@apply bg-indigo-600 text-white hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:ring-indigo-500': {},
                },
                '.btn-secondary': {
                    '@apply bg-gray-600 text-white hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:ring-gray-500': {},
                },
                '.btn-success': {
                    '@apply bg-green-600 text-white hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:ring-green-500': {},
                },
                '.btn-danger': {
                    '@apply bg-red-600 text-white hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:ring-red-500': {},
                },
                '.btn-outline': {
                    '@apply bg-white border-gray-300 text-gray-700 hover:bg-gray-50 focus:ring-indigo-500': {},
                },
            });
        },
    ],
};
