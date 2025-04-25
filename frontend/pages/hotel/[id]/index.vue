<script setup lang="ts">
    import HeartIcon from '~/components/atoms/icons/HeartIcon.vue';
import type { Hotel } from '~/types/hotel';
    import type { Room } from '~/types/room';

    const route = useRoute();
    const hotelId = route.params.id;
    const { $api } = useNuxtApp();
    const authStore = useAuthStore();

    const hotel = ref<Hotel>();
    const rooms = ref<Room[]>();
    const isBookModalOpen = ref<boolean>(false);

    const fetchHotel = async () => {
        try {
            const response = await useAuthFetch<Hotel>($api(`/api/hotels/${hotelId}`));

            if (response.data.value === null || response.error.value) {
                throw createError({
                    statusCode: 404,
                    statusMessage: 'Hotel not found',
                });
            }

            hotel.value = response.data.value;
        } catch (error) {
            console.log(error);
            throw error;
        }
    };

    const fetchRooms = async () => {
        try {
            const response = await useAuthFetch<Room[]>($api(`/api/hotels/${hotelId}/rooms`));

            if (response.data.value === null || response.error.value) {
                throw createError({
                    statusCode: 404,
                    statusMessage: 'Hotel not found',
                });
            }

            rooms.value = response.data.value;
        } catch (error) {
            console.error(error);
            throw error;
        }
    };

    onMounted(async () => {
        await fetchHotel();
        await fetchRooms();
    });

    function handleBookClick() {
        if (!authStore.isAuthenticated) return navigateTo('/login')

        isBookModalOpen.value = true;
    }
</script>

<template>
    <NuxtLayout name="customer">
        <section v-if="hotel" class="my-20 h-full w-full">
            <div class="max-w-7xl w-full mx-auto">
                <img class="w-full rounded-2xl max-h-[550px] object-cover" :src="hotel.images[0].url" />
                <div class="flex justify-between mt-6">
                    <div class="w-2/3">
                        <h1 class="text-3xl font-semibold text-primary">{{ hotel.name }}</h1>
                        <p class="text-secondary">{{ hotel.city }}, {{ hotel.country }}</p>
                        <p class="mt-6 w-full text-tertiary">{{ hotel.description }}</p>
                    </div>
                    <div class="flex h-fit items-center gap-2">
                        <UButton :icon="HeartIcon" variant="secondary" />
                        <UButton class="h-fit" @click="handleBookClick">Réserver</UButton>
                        <UBookModal v-if="authStore.isAuthenticated" v-model="isBookModalOpen" :rooms="rooms ?? []" />
                    </div>
                </div>

                <div class="mt-12">
                    <h2 class="text-2xl font-semibold text-primary">Chambres</h2>
                    <div v-if="rooms" class="flex items-center gap-12 mt-6 overflow-auto">
                        <URoomCard v-for="room in rooms" :key="room.id" :room="room" />
                    </div>
                    <div v-else class="flex items-center justify-center flex-col gap-2 mt-6">
                        <ULoading />
                        <p class="text-primary font-semibold">Chargement</p>
                    </div>
                </div>
            </div>
        </section>
        <section v-else class="w-full h-full flex flex-col items-center justify-center gap-2">
            <ULoading />
            <p class="text-primary font-semibold">Chargement...</p>
        </section>
    </NuxtLayout>
</template>

