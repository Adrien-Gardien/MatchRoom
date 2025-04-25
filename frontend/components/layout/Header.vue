<script setup>
const authStore = useAuthStore();

const isAuthenticated = computed(() => authStore.isAuthenticated);
const user = computed(() => authStore.user);

const logout = async () => {
  try {
    // Appel à la méthode logout du store auth
    await authStore.logout();
    
    // Suppression manuelle des cookies (redondant mais pour être sûr)
    const authCookie = useCookie('auth');
    const bearerCookie = useCookie('BEARER');
    
    authCookie.value = null;
    bearerCookie.value = null;
    
    // Force un rechargement complet de la page
    window.location.href = '/';
  } catch (error) {
    console.error('Erreur lors de la déconnexion:', error);
  }
};
</script>

<template>
  <header class="bg-cream shadow-md">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center py-4">
        <!-- Logo -->
        <div class="flex items-center">
          <a href="/" class="text-2xl font-bold text-everglade">MatchRoom</a>
        </div>
        
    
        
        <!-- Auth Buttons -->
        <div class="flex items-center space-x-4">
          <template v-if="isAuthenticated">
            <div class="flex items-center space-x-4">
              <span class="text-quincy" v-if="user">{{ user.first_name }}</span>
              <a href="/profile" class="p-2 rounded-full bg-cream text-everglade transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </a>
              <button 
                @click="logout"
                class="px-4 py-2 rounded-lg bg-everglade text-colonial-white hover:bg-everglade-light transition-colors">
                Déconnexion
              </button>
            </div>
          </template>
          <template v-else>
            <a 
              href="/login"
              class="px-4 py-2 rounded-lg border border-everglade text-everglade hover:bg-everglade hover:text-colonial-white transition-colors">
              Connexion
            </a>
            <a 
              href="/register"
              class="px-4 py-2 rounded-lg bg-everglade text-colonial-white hover:bg-everglade-light transition-colors">
              Inscription
            </a>
          </template>
        </div>
      </div>
    </div>
  </header>
</template> 