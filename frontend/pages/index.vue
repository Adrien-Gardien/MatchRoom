<script setup lang="ts">
    import type { Room } from '~/types/room';
    import type { Hotel } from '~/types/hotel';
    import SearchIcon from '~/components/atoms/icons/SearchIcon.vue';

    const { $api } = useNuxtApp();
    const amountTravelers = ref(1);
    const arrivalDate = ref();
    const departureDate = ref();
    const rooms = ref<Room[]>([]);
    const hotels = ref<Hotel[]>([]);
    const error = ref<string>('');

    const fetchRooms = async () => {
        try {
            const response = await useAuthFetch<Room[]>($api('/api/rooms?amount=10'));
            rooms.value = response.data.value ?? [];
            error.value = '';
        } catch (err) {
            console.error(err);
            error.value = 'Erreur lors du chargement des chambres.';
        }
    };

    const fetchHotels = async () => {
        try {
            const response = await useAuthFetch<Hotel[]>($api('/api/hotels?amount=10'));
            hotels.value = response.data.value ?? [];
            console.log('Hotels', hotels.value);
        } catch (err) {
            console.error(err);
            error.value = 'Erreur lors du chargement des hôtels.';
        }
    };

    onMounted(() => {
        fetchRooms();
        fetchHotels();
    });
</script>

<template>
    <NuxtLayout name="customer">
        <section class="max-w-7xl w-full mx-auto">
            <section class="w-full pt-32">
                <div class="py-20 rounded-2xl flex items-center justify-center bg-red-200 relative">
                    <div class="w-full h-full rounded-2xl overflow-hidden absolute top-0 left-0">
                        <img
                            src="https://toploc.com/blog/wp-content/uploads/2016/06/Maison-de-hobbit-achevee-%C2%A9simondale.net_.jpg"
                            class="w-full h-full object-cover blur-sm"
                        />
                    </div>
                    <div class="text-center z-10">
                        <h1 class="text-4xl font-semibold text-primary-on-brand">Négociez votre chambre d’hôtel</h1>
                        <p class="text-primary-on-brand text-lg">
                            Recherchez une chambre disponible aux dates et destinations de votre choix, Négociez et
                            Réservez.
                        </p>

                        <div class="mt-14 max-w-screen-sm space-y-3">
                            <UInput placeholder="Destination, thématique" type="text" :icon="SearchIcon" />
                            <div class="flex w-full justify-between gap-2">
                                <UDatePicker v-model="arrivalDate" placeholder="Arrivée" type="date" class="w-full" />
                                <UDatePicker v-model="departureDate" placeholder="Départ" type="date" class="w-full" />
                                <UInputNumber
                                    v-model="amountTravelers"
                                    placeholder="Voyageurs"
                                    type="number"
                                    :min="1"
                                    class="w-full"
                                />
                            </div>
                        </div>

                        <UButton :icon="SearchIcon" icon-position="leading" class="mx-auto mt-8">Rechercher</UButton>
                    </div>
                </div>
            </section>
        </section>
        <section class="mx-auto max-w-7xl w-full mt-16">
            <h2 class="text-2xl font-semibold">Découvrez ces hotels !</h2>
            <div class="flex items-center overflow-x-auto gap-12 mt-4">
                <UHotelCard v-for="hotel in hotels" :key="hotel.id" :hotel="hotel" />
            </div>
        </section>
        <section class="mx-auto max-w-7xl w-full mt-16">
            <h2 class="text-2xl font-semibold">Découvrez ces chambres !</h2>
            <div class="flex items-center overflow-x-auto gap-12 mt-4">
                <UHotelCard v-for="hotel in hotels" :key="hotel.id" :hotel="hotel" />
            </div>
        </section>
    </NuxtLayout>
</template>

