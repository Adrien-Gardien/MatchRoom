<script setup lang="ts">
    import HeartIcon from '~/components/atoms/icons/HeartIcon.vue';
    import type { Room } from '~/types/room';

    interface SearchRoomCardProps {
        room: Room;
        startDate?: Date | null;
        endDate?: Date | null;
    }

    const props = withDefaults(defineProps<SearchRoomCardProps>(), {
        startDate: null,
        endDate: null,
    });

    const firstImage = computed(() => {
        return props.room.images[0];
    });

    const numberOfNights = computed(() => {
        if (!props.startDate || !props.endDate) return undefined;

        const start = new Date(props.startDate);
        const end = new Date(props.endDate);
        const timeDiff = end.getTime() - start.getTime();
        return Math.ceil(timeDiff / (1000 * 3600 * 24));
    });
</script>

<template>
    <div
        class="w-full relative border border-primary p-4 rounded-xl flex gap-5 bg-primary hover:bg-primary-hover cursor-pointer shadow-xs"
        @click="navigateTo(`/room/${room.id}`)"
    >
        <img :src="firstImage.url" alt="Cover room image" class="rounded-lg max-w-[200px] object-cover min-h-36" />
        <div class="flex flex-col">
            <div class="flex-grow">
                <p class="text-brand-secondary font-semibold text-sm">{{ room.hotel.name }}</p>
                <h3 class="text-primary font-semibold text-lg mt-1">{{ room.name }}</h3>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex gap-2 items-center">
                    <PinIcon class="size-5" />
                    <p class="text-tertiary font-medium">{{ room.hotel.city }}</p>
                </div>
                <div class="flex gap-2 items-center">
                    <UserIcon class="size-5" />
                    <p class="text-tertiary font-medium">{{ room.capacity }} personnes</p>
                </div>
            </div>
        </div>
        <div class="absolute bottom-4 right-4">
            <p v-if="numberOfNights" class="text-primary font-semibold text-xl">
                {{ (room.price / 100) * numberOfNights }} €
                <span class="text-tertiary font-normal text-base">total</span>
            </p>
            <p v-else class="text-primary font-semibold text-xl">
                {{ room.price / 100 }} € <span class="text-tertiary font-normal text-base">/nuit</span>
            </p>
        </div>
        <UButton variant="secondary" :icon="HeartIcon" class="absolute top-4 right-4" disabled />
    </div>
</template>

