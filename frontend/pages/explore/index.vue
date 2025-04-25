<script setup lang="ts">
    import SearchIcon from '~/components/atoms/icons/SearchIcon.vue';

    const searchQuery = ref<string>();

    function handleSearch() {
        navigateTo(`/explore/${searchQuery.value}`)
    }

    function resetSearch() {
        searchQuery.value = undefined
    }
</script>

<template>
    <NuxtLayout name="customer">
        <section class="w-full h-full max-w-7xl mx-auto pt-20 flex flex-col">
            <div class="pt-12 flex gap-4 pb-4 border-b border-primary">
                <div class="space-y-1 flex-grow">
                    <h1 class="text-primary text-2xl font-semibold">Explorer</h1>
                    <p class="text-tertiary">Réservez votre prochain séjour dans l'une de nos propriétés.</p>
                </div>
            </div>
            <form class="mt-8 flex items-center gap-3" @submit.prevent="handleSearch">
                <UInput v-model="searchQuery" type="text" placeholder="Recherche" :icon="SearchIcon" class="flex-grow" />
                <UButton variant="secondary" type="button" @click="resetSearch">Effacer</UButton>
                <UButton :icon="SearchIcon" type="submit">Rechercher</UButton>
            </form>
            <div class="flex-grow flex flex-col items-center justify-center gap-3">
                <SearchIcon class="text-quaternary size-10" />
                <p class="text-quaternary">Effectuez votre première recherche</p>
            </div>
        </section>
    </NuxtLayout>
</template>
