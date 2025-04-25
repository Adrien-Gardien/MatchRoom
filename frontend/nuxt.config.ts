// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
    compatibilityDate: '2024-11-01',
    devtools: { enabled: true },
    modules: [
        '@nuxt/eslint',
        '@nuxt/image',
        '@nuxtjs/tailwindcss',
        '@pinia/nuxt',
        'pinia-plugin-persistedstate/nuxt',
        '@nuxtjs/color-mode',
        '@nuxtjs/google-fonts',
        '@nuxtjs/i18n',
        '@vite-pwa/nuxt',
    ],
    i18n: {
        locales: [
            { code: 'fr', language: 'fr-FR' },
            { code: 'en', language: 'en-US' },
            { code: 'es', language: 'es-ES' },
        ],
        defaultLocale: 'fr',
        bundle: {
            optimizeTranslationDirective: false,
        },
    },
    tailwindcss: {
        exposeConfig: true,
        viewer: true,
    },
    runtimeConfig: {
        public: {
            apiUrl: 'http://localhost',
        },
    },
    colorMode: {
        preference: 'system',
        fallback: 'light',
        classSuffix: '',
    },
    vite: {
        server: {
            allowedHosts: ['matchroom.com'],
        },
    },
    components: {
        dirs: [
            {
                path: '~/components',
                pathPrefix: false,
            },
        ],
    },
    googleFonts: {
        download: true,
        preload: true,
        families: {
            "DM Sans": {
                'ital,opsz,wght': '0,9..40,100..1000;1,9..40,100..1000',
            },
        },
    },
    pwa: {
        registerType: 'autoUpdate',
        manifest: {
            name: 'MatchRoom',
            short_name: 'MatchRoom',
            description: 'MatchRoom - Trouvez la chambre d\'hôtel idéale',
            theme_color: '#ffffff',
            background_color: '#ffffff',
            display: 'standalone',
            orientation: 'portrait',
            categories: ['hotel', 'travel', 'booking'],
            lang: 'fr',
            icons: [
                {
                    src: 'favicon-16x16.png',
                    sizes: '16x16',
                    type: 'image/png'
                },
                {
                    src: 'favicon-32x32.png',
                    sizes: '32x32',
                    type: 'image/png'
                },
                {
                    src: 'apple-touch-icon.png',
                    sizes: '180x180',
                    type: 'image/png'
                },
                {
                    src: 'android-chrome-192x192.png',
                    sizes: '192x192',
                    type: 'image/png'
                },
                {
                    src: 'android-chrome-512x512.png',
                    sizes: '512x512',
                    type: 'image/png'
                },
                {
                    src: 'android-chrome-512x512.png',
                    sizes: '512x512',
                    type: 'image/png',
                    purpose: 'maskable'
                }
            ]
        },
        workbox: {
            navigateFallback: '/',
            globPatterns: ['**/*.{js,css,html,ico,png,svg,jpg,jpeg,webp}'],
            runtimeCaching: [
                {
                    urlPattern: /^https:\/\/fonts\.googleapis\.com\/.*/i,
                    handler: 'CacheFirst',
                    options: {
                        cacheName: 'google-fonts-cache',
                        expiration: {
                            maxEntries: 10,
                            maxAgeSeconds: 60 * 60 * 24 * 365 // 1 an
                        }
                    }
                },
                {
                    urlPattern: /^https:\/\/api\.matchroom\.com\/api\/.*/i,
                    handler: 'NetworkFirst',
                    options: {
                        cacheName: 'api-cache',
                        expiration: {
                            maxEntries: 100,
                            maxAgeSeconds: 60 * 60 * 24 // 24 heures
                        }
                    }
                }
            ]
        },
        devOptions: {
            enabled: true,
            type: 'module'
        }
    }
});
