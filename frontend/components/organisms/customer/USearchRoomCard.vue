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
        class="w-full relative border border-primary rounded-xl flex flex-col sm:flex-row gap-3 sm:gap-5 p-3 sm:p-4 bg-primary hover:bg-primary-hover cursor-pointer shadow-xs"
        @click="navigateTo(`/room/${room.id}`)"
    >
        <img :src="firstImage.url" alt="Cover room image" class="rounded-lg w-full sm:w-auto sm:max-w-[200px] object-cover h-48 sm:h-auto sm:min-h-36" />
        <div class="flex flex-col flex-grow">
            <div class="flex-grow">
                <p class="text-brand-secondary font-semibold text-sm">{{ room.hotel.name }}</p>
                <h3 class="text-primary font-semibold text-lg mt-1">{{ room.name }}</h3>
            </div>
            <div class="flex items-center gap-4 mt-2 sm:mt-0">
                <div class="flex gap-2 items-center">
                    <PinIcon class="size-5 text-tertiary" />
                    <p class="text-tertiary font-medium">{{ room.hotel.city }}</p>
                </div>
                <div class="flex gap-2 items-center">
                    <UserIcon class="size-5 text-tertiary" />
                    <p class="text-tertiary font-medium">{{ room.capacity }} personnes</p>
                </div>
            </div>
            <div class="mt-3 sm:mt-0 sm:absolute sm:bottom-4 sm:right-4">
                <p v-if="numberOfNights" class="text-primary font-semibold text-xl">
                    {{ (room.price / 100) * numberOfNights }} €
                    <span class="text-tertiary font-normal text-base">total</span>
                </p>
                <p v-else class="text-primary font-semibold text-xl">
                    {{ room.price / 100 }} € <span class="text-tertiary font-normal text-base">/nuit</span>
                </p>
            </div>
        </div>
        <UButton variant="secondary" :icon="HeartIcon" class="absolute top-3 right-3 sm:top-4 sm:right-4" disabled />
    </div>
</template>

