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
        <section v-if="hotel" class="py-6 sm:py-10 md:py-16 lg:py-20 h-fit w-full">
            <div class="max-w-7xl pt-6 sm:pt-8 md:pt-10 lg:pt-12 w-full mx-auto px-3 sm:px-4 md:px-6">
                <img class="w-full rounded-xl max-h-32 sm:max-h-40 md:max-h-48 lg:max-h-60 object-cover" :src="hotel.images[0].url" />
                <div class="flex flex-col sm:flex-row justify-between mt-4 sm:mt-6 gap-4 sm:gap-0">
                    <div class="w-full sm:w-2/3">
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-semibold text-primary">{{ hotel.name }}</h1>
                        <p class="text-sm sm:text-base text-secondary">{{ hotel.city }}, {{ hotel.country }}</p>
                        <p class="mt-3 sm:mt-4 md:mt-6 w-full text-sm sm:text-base text-tertiary">{{ hotel.description }}</p>
                    </div>
                    <div class="flex flex-row sm:flex-col h-fit items-center gap-2 mt-3 sm:mt-0">
                        <UButton :icon="HeartIcon" variant="secondary" class="w-full sm:w-auto text-sm sm:text-base" />
                        <UButton class="h-fit w-full sm:w-auto text-sm sm:text-base" @click="handleBookClick">Réserver</UButton>
                        <UBookModal v-if="authStore.isAuthenticated" v-model="isBookModalOpen" :rooms="rooms ?? []" />
                    </div>
                </div>

                <div class="mt-6 sm:mt-8 md:mt-10 lg:mt-12">
                    <h2 class="text-lg sm:text-xl md:text-2xl font-semibold text-primary">Chambres</h2>
                    <div v-if="rooms" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mt-3 sm:mt-4 md:mt-6">
                        <URoomCard v-for="room in rooms" :key="room.id" :room="room" />
                    </div>
                    <div v-else class="flex items-center justify-center flex-col gap-2 mt-6">
                        <ULoading />
                        <p class="text-primary font-semibold text-sm sm:text-base">Chargement</p>
                    </div>
                </div>
            </div>
        </section>
        <section v-else class="w-full h-full flex flex-col items-center justify-center gap-2 py-12 sm:py-16 md:py-20">
            <ULoading />
            <p class="text-primary font-semibold text-sm sm:text-base">Chargement...</p>
        </section>
    </NuxtLayout>
</template>

