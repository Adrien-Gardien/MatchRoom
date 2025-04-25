<script setup lang="ts">
    const authStore = useAuthStore();
</script>

<template>
    <header class="w-full h-20 flex items-center z-10 fixed bg-primary">
        <div class="max-w-7xl w-full mx-auto flex justify-between">
            <div class="cursor-pointer" @click="navigateTo('/')">
                <p class="text-xl text-primary font-semibold">MatchRoom</p>
            </div>
            <nav class="flex items-center">
                <ul class="flex items-center h-full gap-2">
                    <li><ULink to="/match">Match</ULink></li>
                    <li><ULink to="/explore">Explorer</ULink></li>
                </ul>
            </nav>
            <div class="flex items-center gap-2">
                <UButton v-if="!authStore.isAuthenticated" @click="navigateTo('/login')">S'identifier</UButton>
                <LogoutButton v-else />
            </div>
        </div>
    </header>
</template>
