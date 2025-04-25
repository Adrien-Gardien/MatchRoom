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
            apiUrl: '',
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
});
