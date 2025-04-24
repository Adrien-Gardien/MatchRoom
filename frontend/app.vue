<script setup>
    useSeoMeta({
        title: 'MatchRoom',
    });

    useHead({
        link: [
            {
                rel: 'stylesheet',
                href: 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
            },
            {
                rel: 'preconnect',
                href: 'https://fonts.googleapis.com',
            },
            {
                rel: 'preconnect',
                href: 'https://fonts.gstatic.com',
                crossorigin: '',
            },
            {
                rel: 'stylesheet',
                href: 'https://fonts.googleapis.com/css2?family=Abril+Fatface&family=Roboto:wght@300;400;500;700&display=swap',
            },
        ],
    });
</script>

<template>
    <NuxtLayout>
        <NuxtPage />
    </NuxtLayout>
</template>

<style>
@import '~/assets/css/variables.css';

body {
    font-family: 'Roboto', sans-serif;
}

h1, .h1 {
    font-family: 'Abril Fatface', cursive;
}

h2, .h2, h3, .h3 {
    font-family: 'Roboto', sans-serif;
}
</style>
