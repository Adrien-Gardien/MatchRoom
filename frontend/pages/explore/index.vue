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
        <section class="w-full h-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 sm:pt-20 flex flex-col">
            <div class="pt-8 sm:pt-12 flex flex-col sm:flex-row gap-4 pb-4 border-b border-primary">
                <div class="space-y-1 flex-grow">
                    <h1 class="text-primary text-xl sm:text-2xl font-semibold">Explorer</h1>
                    <p class="text-tertiary text-sm sm:text-base">Réservez votre prochain séjour dans l'une de nos propriétés.</p>
                </div>
            </div>
            <form class="mt-6 sm:mt-8 flex flex-col sm:flex-row sm:items-center gap-3" @submit.prevent="handleSearch">
                <UInput v-model="searchQuery" type="text" placeholder="Recherche" :icon="SearchIcon" class="flex-grow" />
                <div class="flex gap-2 sm:gap-3 mt-2 sm:mt-0">
                    <UButton variant="secondary" type="button" class="flex-1 sm:flex-none" @click="resetSearch">Effacer</UButton>
                    <UButton :icon="SearchIcon" type="submit" class="flex-1 sm:flex-none">Rechercher</UButton>
                </div>
            </form>
            <div class="flex-grow flex flex-col items-center justify-center gap-3 mt-8 sm:mt-12">
                <SearchIcon class="text-quaternary size-8 sm:size-10" />
                <p class="text-quaternary text-sm sm:text-base text-center">Effectuez votre première recherche</p>
            </div>
        </section>
    </NuxtLayout>
</template>
