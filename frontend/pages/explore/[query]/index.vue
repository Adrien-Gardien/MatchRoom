<script setup lang="ts">
    import SearchIcon from '~/components/atoms/icons/SearchIcon.vue';
    import type { Room } from '~/types/room';

    const numberOfResults = ref(0);
    const route = useRoute();
    const query = route.params.query;
    const { $api } = useNuxtApp();
    const rooms = ref<Room[]>();
    const searchQuery = ref<string>();

    const fetchData = async () => {
        try {
            const response = await useAuthFetch<{ data: Room[], total: number }>($api(`/api/search?query=${query}`));

            if (response.data.value) {
                rooms.value = response.data.value.data;
                numberOfResults.value = response.data.value.total;
            }
        } catch (error) {
            console.error(error);
            throw createError({
                statusCode: 500,
                statusMessage: 'Search failed',
            });
        }
    };

    function handleSearch() {
        navigateTo(`/explore/${searchQuery.value}`)
    }

    function resetSearch() {
        searchQuery.value = undefined
    }

    onMounted(async () => {
        if (query) {
            await fetchData();
        }
    })
</script>

<template>
    <NuxtLayout name="customer">
        <section class="w-full h-fit max-w-7xl mx-auto pt-20 flex flex-col">
            <div class="pt-12 flex gap-4 pb-4 border-b border-primary">
                <div class="space-y-1 flex-grow">
                    <h1 class="text-primary text-2xl font-semibold">{{ numberOfResults }} lieux dans {{ query }}</h1>
                    <p class="text-tertiary">Réservez votre prochain séjour dans l'une de nos propriétés.</p>
                </div>
                <div class="flex items-center gap-3 w-fit">
                    <UButton variant="secondary">Partager</UButton>
                    <UButton class="w-fit">Enregistrer votre recherche</UButton>
                </div>
            </div>
            <form class="mt-8 flex items-center gap-3" @submit.prevent="handleSearch">
                <UInput type="text" placeholder="Recherche" :icon="SearchIcon" class="flex-grow" />
                <UButton variant="secondary" type="button" @click="resetSearch">Effacer</UButton>
                <UButton :icon="SearchIcon" type="submit">Rechercher</UButton>
            </form>
            <div class="flex-grow flex flex-col mt-6">
                <div v-if="rooms" class="flex flex-col gap-4">
                    <USearchRoomCard v-for="room in rooms" :key="room.id" :room="room" />
                </div>
                <div v-else class="flex-grow flex flex-col items-center justify-center">
                    <p class="text-quaternary font-semibold text-xl">Aucun résultat trouvé pour cette recherche</p>
                </div>
            </div>
        </section>
    </NuxtLayout>
</template>

