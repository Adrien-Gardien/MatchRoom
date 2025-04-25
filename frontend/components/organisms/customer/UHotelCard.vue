<script setup lang="ts">
import type { Hotel } from '~/types/hotel';

interface HotelCardProps {
    hotel: Hotel
}

const props = defineProps<HotelCardProps>();

const image = computed(() => props.hotel.images[0])
</script>

<template>
    <div class="min-w-96 cursor-pointer" @click="navigateTo(`/hotel/${hotel.id}`)">
        <img :src="image.url" alt="Hotel cover image" class="rounded-2xl object-cover min-h-64 max-h-64 h-full w-full">
        <div class="mt-4">
            <p class="text-brand-secondary text-xs mb-2">Hotel</p>
            <p class="text-lg font-semibold text-primary mb-1">{{ hotel.name }}</p>
            <p class="text-tertiary line-clamp-2 text-ellipsis">{{ hotel.description }}</p>
        </div>
    </div>
</template>