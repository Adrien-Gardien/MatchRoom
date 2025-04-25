<script setup lang="ts">
    import type { Room } from '~/types/room';

    interface RoomCardProps {
        room: Room;
    }

    const props = defineProps<RoomCardProps>();

    const image = computed(() => props.room.images[0])
</script>

<template>
    <div class="min-w-96 cursor-pointer" @click="navigateTo(`/room/${room.id}`)">
        <img :src="image.url" alt="Hotel cover image" class="rounded-2xl object-cover min-h-64 max-h-64 h-full w-full">
        <div class="mt-4">
            <p class="text-brand-secondary text-xs mb-2">Hotel</p>
            <p class="text-lg font-semibold text-primary mb-1">{{ room.name }}</p>
            <p class="text-tertiary line-clamp-2 text-ellipsis">{{ room.description }}</p>
            <p class="text-brand-primary font-semibold mt-6">{{ room.price / 100 }}€ <span class="text-xs text-primary">/nuit</span></p>
        </div>
    </div>
</template>
