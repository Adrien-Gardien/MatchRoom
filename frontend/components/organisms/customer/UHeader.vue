<script setup lang="ts">
    import MenuIcon from '~/components/atoms/icons/MenuIcon.vue';
    import XIcon from '~/components/atoms/icons/XIcon.vue';

    const authStore = useAuthStore();
    const mobileMenuOpen = ref(false);

    const toggleMobileMenu = () => {
        mobileMenuOpen.value = !mobileMenuOpen.value;
    };
</script>

<template>
    <header class="w-full h-20 flex items-center z-10 fixed bg-primary border-b border-secondary">
        <div class="max-w-7xl w-full mx-auto flex justify-between px-4 sm:px-6">
            <div class="cursor-pointer flex items-center" @click="navigateTo('/')">
                <p class="text-xl text-primary font-semibold">MatchRoom</p>
            </div>
            
            <!-- Navigation desktop -->
            <nav class="hidden md:flex items-center">
                <ul class="flex items-center h-full gap-4">
                    <li><ULink to="/match">Match</ULink></li>
                    <li><ULink to="/explore">Explorer</ULink></li>
                </ul>
            </nav>
            
            <!-- Boutons desktop -->
            <div class="hidden md:flex items-center gap-2">
                <UButton v-if="!authStore.isAuthenticated" @click="navigateTo('/login')">S'identifier</UButton>
                <LogoutButton v-else />
            </div>
            
            <!-- Bouton menu mobile -->
            <button 
                class="md:hidden flex items-center justify-center p-2 rounded-md text-quaternary hover:text-tertiary hover:bg-primary-hover"
                @click="toggleMobileMenu"
            >
                <MenuIcon v-if="!mobileMenuOpen" class="h-6 w-6" />
                <XIcon v-else class="h-6 w-6" />
            </button>
        </div>
        
        <!-- Menu mobile -->
        <div 
            v-show="mobileMenuOpen"
            class="md:hidden absolute top-20 left-0 right-0 bg-primary border-b border-secondary shadow-lg z-20"
        >
            <div class="px-4 py-3 space-y-4">
                <nav>
                    <ul class="flex flex-col gap-4">
                        <li><ULink to="/match" @click="mobileMenuOpen = false">Match</ULink></li>
                        <li><ULink to="/explore" @click="mobileMenuOpen = false">Explorer</ULink></li>
                    </ul>
                </nav>
                <div class="pt-2 border-t border-secondary">
                    <UButton 
                        v-if="!authStore.isAuthenticated" 
                        class="w-full" 
                        @click="navigateTo('/login'); mobileMenuOpen = false"
                    >
                        S'identifier
                    </UButton>
                    <LogoutButton v-else class="w-full" @click="mobileMenuOpen = false" />
                </div>
            </div>
        </div>
    </header>
</template>
