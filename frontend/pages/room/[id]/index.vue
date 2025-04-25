<script setup lang="ts">
    import type { Room } from '~/types/room';
    import HeartIcon from '~/components/atoms/icons/HeartIcon.vue';

    const route = useRoute();
    const roomId = route.params.id;
    const { $api } = useNuxtApp();
    const authStore = useAuthStore();

    // États
    const room = ref<Room | null>(null);
    const loading = ref(true);
    const error = ref<string | null>(null);
    const currentImageIndex = ref(0);
    const isBookModalOpen = ref(false);

    // Récupération des données de la chambre
    const fetchRoom = async () => {
        try {
            loading.value = true;
            const response = await useAuthFetch<Room>($api(`/api/rooms/${roomId}`));

            if (!response.data.value || response.error.value) {
                throw createError({
                    statusCode: 404,
                    statusMessage: 'Chambre non trouvée',
                });
            }

            room.value = response.data.value;
        } catch (err) {
            console.error(err);
            error.value = 'Impossible de charger les informations de la chambre.';
        } finally {
            loading.value = false;
        }
    };

    // Navigation entre les images
    const nextImage = () => {
        if (!room.value?.images.length) return;
        currentImageIndex.value = (currentImageIndex.value + 1) % room.value.images.length;
    };

    const prevImage = () => {
        if (!room.value?.images.length) return;
        currentImageIndex.value = (currentImageIndex.value - 1 + room.value.images.length) % room.value.images.length;
    };

    // Ouvrir le modal de réservation
    const openBookModal = () => {
        if (!authStore.isAuthenticated) {
            navigateTo('/login');
            return;
        }
        isBookModalOpen.value = true;
    };

    // Charger les données au chargement de la page
    onMounted(() => {
        fetchRoom();
    });
</script>

<template>
    <NuxtLayout name="customer">
        <div v-if="loading" class="w-full flex flex-col items-center justify-center py-20">
            <ULoading />
            <p class="text-primary font-semibold mt-4">Chargement...</p>
        </div>

        <div v-else-if="error" class="w-full flex flex-col items-center justify-center py-20">
            <p class="text-error font-semibold">{{ error }}</p>
            <UButton class="mt-4" @click="fetchRoom">Réessayer</UButton>
        </div>

        <div v-else-if="room" class="max-w-7xl w-full mx-auto px-4 py-24 sm:py-32">
            <div class="relative mb-6 sm:mb-8">
                <div class="w-full h-64 sm:h-80 md:h-96 lg:h-[450px] rounded-xl overflow-hidden relative">
                    <img
                        v-if="room.images && room.images.length > 0"
                        :src="room.images[currentImageIndex].url"
                        :alt="`Image ${currentImageIndex + 1} de ${room.name}`"
                        class="w-full h-full object-cover"
                    />
                    <img v-else src="/image.png" alt="Image par défaut" class="w-full h-full object-cover" />

                    <!-- Navigation d'images (si plusieurs images) -->
                    <div
                        v-if="room.images && room.images.length > 1"
                        class="absolute inset-0 flex items-center justify-between px-4"
                    >
                        <UButton
                            variant="secondary"
                            class="h-10 w-10 !rounded-full !p-0 bg-primary/80 backdrop-blur-sm shadow-sm"
                            @click.stop="prevImage"
                        >
                            <span class="sr-only">Image précédente</span>
                            <i class="i-heroicons-chevron-left-20-solid h-5 w-5"></i>
                        </UButton>
                        <UButton
                            variant="secondary"
                            class="h-10 w-10 !rounded-full !p-0 bg-primary/80 backdrop-blur-sm shadow-sm"
                            @click.stop="nextImage"
                        >
                            <span class="sr-only">Image suivante</span>
                            <i class="i-heroicons-chevron-right-20-solid h-5 w-5"></i>
                        </UButton>
                    </div>

                    <!-- Compteur d'images -->
                    <div
                        v-if="room.images && room.images.length > 1"
                        class="absolute bottom-4 right-4 bg-white/80 backdrop-blur-sm rounded-full px-3 py-1 text-sm font-medium text-primary"
                    >
                        {{ currentImageIndex + 1 }} / {{ room.images.length }}
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Informations principales de la chambre -->
                <div class="lg:col-span-2">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-primary mb-2">{{ room.name }}</h1>
                            <div class="flex items-center gap-2 text-tertiary mb-4">
                                <i class="i-heroicons-user-20-solid"></i>
                                <span>{{ room.capacity }} personnes</span>
                                <span class="text-tertiary mx-1">•</span>
                                <i class="i-heroicons-map-pin-20-solid"></i>
                                <span>{{ room.hotel.city }}</span>
                            </div>
                        </div>
                        <UButton :icon="HeartIcon" variant="secondary" class="hidden sm:flex" />
                    </div>

                    <!-- Description et détails -->
                    <div class="mt-6">
                        <h2 class="text-xl font-semibold mb-3 text-primary">À propos de cette chambre</h2>
                        <p class="text-tertiary">{{ room.description }}</p>
                    </div>

                    <!-- Informations sur l'hôtel -->
                    <div class="mt-8 p-4 border border-primary rounded-lg bg-secondary">
                        <h2 class="text-lg font-semibold mb-2 text-primary">L'hôtel</h2>
                        <p class="text-tertiary mb-3 line-clamp-2">
                            {{ room.hotel.name }} - {{ room.hotel.description }}
                        </p>
                        <UButton variant="tertiary" size="sm" @click="navigateTo(`/hotel/${room.hotel.id}`)">
                            Voir l'hôtel
                        </UButton>
                    </div>
                </div>

                <!-- Carte de réservation (fixed sur mobile, sidebar sur desktop) -->
                <div class="lg:col-span-1 mt-6 lg:mt-0">
                    <div class="sticky top-24 p-4 sm:p-6 border border-secondary rounded-xl shadow-sm bg-secondary">
                        <div class="flex items-baseline justify-between mb-4">
                            <div>
                                <span class="text-2xl font-bold text-primary">{{ room.price / 100 }}€</span>
                                <span class="text-tertiary"> / nuit</span>
                            </div>
                            <div class="text-sm">
                                <span v-if="room.available" class="text-success-primary">Disponible</span>
                                <span v-else class="text-error-primary">Non disponible</span>
                            </div>
                        </div>

                        <!-- Formulaire de date (avec composant UDatePicker) -->
                        <div class="mt-4 space-y-4">
                            <UDatePicker placeholder="Sélectionner une date d'arrivée" :min-date="new Date()" />
                            <UDatePicker placeholder="Sélectionner une date de départ" :min-date="new Date()" />
                            <UInput type="number" :min="1" :max="room.capacity" placeholder="1" class="w-full" />
                        </div>

                        <!-- Options de réservation -->
                        <div class="mt-6">
                            <UButton
                                block
                                variant="primary"
                                color="brand"
                                :disabled="!room.available"
                                class="text-white"
                                @click="openBookModal"
                            >
                                Réserver
                            </UButton>
                            <UButton
                                variant="secondary"
                                color="brand"
                                block
                                class="mt-3"
                                :disabled="!room.available"
                                @click="openBookModal"
                            >
                                Faire une offre
                            </UButton>
                        </div>

                        <!-- Informations prix -->
                        <div class="mt-6 text-sm text-tertiary">
                            <p>Des frais de service peuvent s'appliquer</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de réservation -->
        <UBookModal v-if="authStore.isAuthenticated && room" v-model="isBookModalOpen" :rooms="[room]" />
    </NuxtLayout>
</template>
