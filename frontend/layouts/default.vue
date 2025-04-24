<script setup>
import Header from '~/components/layout/Header.vue';
</script>

<template>
    <div>
        <Header />
        <slot />
        <UToastContainer position="top-right" />
    </div>
</template>
