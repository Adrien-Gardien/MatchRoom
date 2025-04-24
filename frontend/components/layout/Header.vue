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
        
        <!-- Navigation -->
        <nav class="hidden md:flex items-center space-x-8">
          <a href="/" class="text-quincy hover:text-burning-orange transition-colors">Accueil</a>
          <a href="#" class="text-quincy hover:text-burning-orange transition-colors">Destinations</a>
          <a href="#" class="text-quincy hover:text-burning-orange transition-colors">Comment ça marche</a>
          <a href="#" class="text-quincy hover:text-burning-orange transition-colors">À propos</a>
        </nav>
        
        <!-- Auth Buttons -->
        <div class="flex items-center space-x-4">
          <template v-if="isAuthenticated">
            <div class="flex items-center space-x-4">
              <span class="text-quincy" v-if="user">{{ user.first_name }}</span>
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