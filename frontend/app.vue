<script setup>
    useSeoMeta({
        title: 'MatchRoom',
    });
</script>

<template>
    <OfflineAlert />
    <NuxtLayout name="default">
        <NuxtPage />
    </NuxtLayout>
    <PwaInstall />
</template>
