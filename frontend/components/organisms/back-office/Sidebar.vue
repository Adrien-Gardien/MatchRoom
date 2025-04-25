<script setup lang="ts">
    import LogoutIcon from '~/components/atoms/icons/LogoutIcon.vue';
    import SearchIcon from '~/components/atoms/icons/SearchIcon.vue';
    import links from '~/utils/navigation';

    const authStore = useAuthStore();
    const { success, error } = useToast();

    const roleName = computed(() => {
        if (!authStore.user) return;

        if (authStore.user.roles.includes('ROLE_ADMIN')) return 'Administrateur';
        if (authStore.user.roles.includes('ROLE_HOTEL')) return 'Hotelier';

        return 'Utilisateur';
    });

    async function handleLogoutButton() {
        if (!authStore.isAuthenticated) return;

        try {
            await authStore.logout();

            success('Déconnexion réussie', 'Vous avez bien été déconnecté.');
            navigateTo('/login');
        } catch {
            error('Une erreur est survenue', 'La déconnexion a échouée.');
        }
    }
</script>

<template>
    <section class="max-w-82 w-full h-full border-r border-primary pt-6 flex flex-col fixed left-0 top-0">
        <div class="px-5 w-full space-y-5">
            <p class="text-xl font-semibold text-primary">MatchRoom</p>
            <UInput :icon="SearchIcon" icon-position="leading" type="search" placeholder="Recherche" />
        </div>
        <div class="w-full px-3 mt-5 flex-grow">
            <UNavItem v-for="link in links" :key="link.label" :icon="link.icon" :to="link.link">
                {{ link.label }}
            </UNavItem>
        </div>
        <div class="border-t border-primary px-4 py-5">
            <div class="flex items-center gap-3 relative">
                <UAvatar size="lg" />
                <div>
                    <p class="font-semibold text-primary">
                        {{ authStore.user?.name }}
                    </p>
                    <p class="text-secondary text-sm">{{ roleName }}</p>
                </div>
                <UButton
                    variant="tertiary"
                    :icon="LogoutIcon"
                    icon-position="leading"
                    class="absolute right-0"
                    @click="handleLogoutButton"
                />
            </div>
        </div>
    </section>
</template>

