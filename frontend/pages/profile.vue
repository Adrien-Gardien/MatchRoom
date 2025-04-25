<script setup>
import { ref, computed, onMounted } from 'vue';

const authStore = useAuthStore();
const { $api } = useNuxtApp();

const user = computed(() => authStore.user);
const isLoading = ref(true);
const error = ref(null);

// Données pour les réservations passées et à venir
const upcomingReservations = ref([]);
const pastReservations = ref([]);
const favorites = ref([]);

// Récupération des réservations
const fetchReservations = async () => {
  isLoading.value = true;
  try {
    if (!authStore.isAuthenticated) {
      navigateTo('/login');
      return;
    }
    
    // Récupérer les réservations de l'utilisateur
    const response = await useAuthFetch($api('/api/reservation/user'));
    
    const now = new Date();
    const reservations = response.data.value || [];
    
    // Séparation des réservations passées et à venir
    upcomingReservations.value = reservations.filter(reservation => {
      const checkOut = new Date(reservation.checkOut);
      return checkOut >= now;
    });
    
    pastReservations.value = reservations.filter(reservation => {
      const checkOut = new Date(reservation.checkOut);
      return checkOut < now;
    });
    
    error.value = null;
  } catch (err) {
    console.error('Erreur lors du chargement des réservations:', err);
    error.value = 'Impossible de charger vos réservations. Veuillez réessayer.';
  } finally {
    isLoading.value = false;
  }
};

const fetchFavorites = async () => {
  isLoading.value = true;
  try {
    if (!authStore.isAuthenticated) {
      navigateTo('/login');
      return;
    }

    const response = await useAuthFetch($api(`/api/favorite/user/${user.value.id}`));
    
    // Extraire les données importantes

    console.log("Favoris:", response.data.value);
    if (Array.isArray(response.data.value)) {
      favorites.value = response.data.value.map(fav => ({
        id: fav.id,
        hotelId: fav.hotelIdSerialized,
        hotelName: fav.hotelId?.name || 'Hôtel non spécifié',
        hotelAddress: fav.hotelId?.address || '',
        hotelCity: fav.hotelId?.city || '',
        hotelCountry: fav.hotelId?.country || '',
        addedDate: fav.addedDate,
        image: fav.hotelId?.image || '',
        // On peut ajouter d'autres propriétés si nécessaire
      }));
    } else {
      favorites.value = [];
    }
    
    console.log("Favoris formatés:", favorites.value);

    error.value = null;
  } catch (err) {
    console.error('Erreur lors du chargement des favoris:', err);
    error.value = 'Impossible de charger vos favoris. Veuillez réessayer.';
  } finally {
    isLoading.value = false;
  }
};

// Format de date française
const formatDate = (dateString) => {
  if (!dateString) return '';
  const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
  return new Date(dateString).toLocaleDateString('fr-FR', options);
};

// Vérifier si l'utilisateur est authentifié
onMounted(() => {
  if (!authStore.isAuthenticated) {
    navigateTo('/login');
    return;
  }
  fetchReservations();
  fetchFavorites();
});
</script>

<template>
  <div class="min-h-screen bg-cream">
    <!-- Bannière du profil -->
    <div class="relative h-[40vh] overflow-hidden rounded-b-[2rem]">
      <!-- Background gradients -->
      <div class="absolute inset-0 bg-gradient-to-r from-black/50 to-black/80 z-10"></div>
      <!-- Background image -->
      <div
        class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1571896349842-33c89424de2d?ixlib=rb-4.0.3&auto=format&fit=crop&q=80')] bg-cover bg-center animate-subtle-zoom"
      ></div>

      <!-- Text & CTA -->
      <div class="relative z-20 flex flex-col items-center justify-center h-full text-colonial-white px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-8">
          <div class="w-24 h-24 bg-everglade-light rounded-full flex items-center justify-center overflow-hidden">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-colonial-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
          </div>
          <div class="text-center md:text-left">
            <h1 class="text-3xl md:text-4xl font-normal mb-2 animate-fade-in drop-shadow-lg">
              Bonjour {{ user?.firstName }} {{ user?.lastName }}
            </h1>
            <p class="text-lg animate-slide-up">
              Bienvenue sur votre espace personnel MatchRoom
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Contenu principal -->
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Informations du profil -->
      <div class="mb-12">
        <h2 class="text-3xl font-bold mb-6 text-everglade">Mes informations</h2>
        <div class="bg-colonial-white-light rounded-xl p-8 shadow-md">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
              <h3 class="text-xl font-semibold mb-4 text-quincy">Coordonnées</h3>
              <div class="space-y-3">
                <div class="flex items-center space-x-3">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-burning-orange" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  <p class="text-quincy">{{ user?.firstName }} {{ user?.lastName }}</p>
                </div>
                <div class="flex items-center space-x-3">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-burning-orange" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                  <p class="text-quincy">{{ user?.email }}</p>
                </div>
                <div class="flex items-center space-x-3">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-burning-orange" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                  </svg>
                  <p class="text-quincy">{{ user?.phone || 'Non spécifié' }}</p>
                </div>
              </div>
            </div>
        
          </div>
        </div>
      </div>

      <!-- Statistiques -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="bg-hawkes-blue rounded-xl p-6 shadow-md text-center">
          <div class="text-4xl font-bold text-everglade mb-2">{{ upcomingReservations.length }}</div>
          <p class="text-quincy">Réservations à venir</p>
        </div>
        <div class="bg-hawkes-blue rounded-xl p-6 shadow-md text-center">
          <div class="text-4xl font-bold text-everglade mb-2">{{ pastReservations.length }}</div>
          <p class="text-quincy">Séjours passés</p>
        </div>
        <div class="bg-hawkes-blue rounded-xl p-6 shadow-md text-center">
          <div class="text-4xl font-bold text-everglade mb-2">15%</div>
          <p class="text-quincy">Économies moyennes</p>
        </div>
      </div>

      <!-- Réservations à venir -->
      <div class="mb-12">
        <h2 class="text-3xl font-bold mb-6 text-everglade">Mes réservations à venir</h2>
        
        <div v-if="isLoading" class="text-center py-8">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-everglade"></div>
          <p class="mt-2 text-quincy">Chargement de vos réservations...</p>
        </div>
        
        <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
          {{ error }}
        </div>
        
        <div v-else-if="upcomingReservations.length === 0" class="bg-colonial-white-light rounded-xl p-8 shadow-md text-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-burning-orange mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <h3 class="text-xl font-semibold mb-2 text-quincy">Aucune réservation à venir</h3>
          <p class="text-quincy-light mb-4">Vous n'avez pas encore de séjours planifiés.</p>
          <a href="/" class="px-4 py-2 bg-everglade text-colonial-white rounded-lg hover:bg-everglade-light transition-colors inline-block">
            Explorer des hôtels
          </a>
        </div>
        
        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div v-for="(reservation, index) in upcomingReservations" :key="index" class="bg-colonial-white-light rounded-xl overflow-hidden shadow-md">
            <div class="h-40 bg-hawkes-blue-light relative">
              <img 
                :src="reservation.roomImage || 'https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&auto=format&fit=crop&q=80'" 
                alt="Chambre" 
                class="w-full h-full object-cover"
              />
              <div class="absolute top-4 right-4 bg-burning-orange text-white px-3 py-1 rounded-full text-sm font-semibold">
                Confirmé
              </div>
            </div>
            <div class="p-6">
              <h3 class="text-xl font-semibold mb-2 text-quincy">{{ reservation.hotelName || 'Hôtel' }}</h3>
              <p class="text-burning-orange font-medium mb-4">{{ reservation.roomName || 'Chambre standard' }}</p>
              
              <div class="flex items-center space-x-2 mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-burning-orange" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="text-quincy">
                  <span class="font-medium">{{ formatDate(reservation.checkIn) }}</span> 
                  <span class="mx-2">→</span> 
                  <span class="font-medium">{{ formatDate(reservation.checkOut) }}</span>
                </p>
              </div>
              
              <div class="flex items-center space-x-2 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-burning-orange" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <p class="text-quincy">{{ reservation.location || 'Paris, France' }}</p>
              </div>
              
              <div class="flex justify-between items-center pt-4 border-t border-colonial-white-dark">
                <div>
                  <p class="text-quincy-light text-sm">Prix total</p>
                  <p class="text-everglade font-semibold text-xl">{{ reservation.totalPrice || '210' }}€</p>
                </div>
                <button class="px-4 py-2 bg-everglade text-colonial-white rounded-lg hover:bg-everglade-light transition-colors">
                  Voir les détails
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Historique des séjours -->
      <div class="mb-12">
        <h2 class="text-3xl font-bold mb-6 text-everglade">Historique de mes séjours</h2>
        
        <div v-if="isLoading" class="text-center py-8">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-everglade"></div>
          <p class="mt-2 text-quincy">Chargement de votre historique...</p>
        </div>
        
        <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
          {{ error }}
        </div>
        
        <div v-else-if="pastReservations.length === 0" class="bg-colonial-white-light rounded-xl p-8 shadow-md text-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-burning-orange mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <h3 class="text-xl font-semibold mb-2 text-quincy">Aucun séjour passé</h3>
          <p class="text-quincy-light">Vos séjours passés apparaîtront ici.</p>
        </div>
        
        <div v-else class="grid grid-cols-1 gap-4">
          <div v-for="(reservation, index) in pastReservations" :key="index" class="bg-colonial-white-light rounded-xl overflow-hidden shadow-md">
            <div class="p-6">
              <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                <div>
                  <h3 class="text-xl font-semibold mb-1 text-quincy">{{ reservation.hotelName || 'Hôtel des Voyageurs' }}</h3>
                  <p class="text-burning-orange font-medium mb-4">{{ reservation.roomName || 'Chambre supérieure' }}</p>
                </div>
                <div class="md:text-right mt-2 md:mt-0">
                  <p class="text-quincy-light text-sm">Séjour du {{ formatDate(reservation.checkIn) }} au {{ formatDate(reservation.checkOut) }}</p>
                  <p class="text-everglade font-semibold">{{ reservation.totalPrice || '185' }}€</p>
                </div>
              </div>
              
              <div class="flex justify-end space-x-4 mt-4">
                <button class="px-4 py-2 border border-everglade text-everglade rounded-lg hover:bg-everglade hover:text-colonial-white transition-colors">
                  Laisser un avis
                </button>
                <button class="px-4 py-2 bg-everglade text-colonial-white rounded-lg hover:bg-everglade-light transition-colors">
                  Réserver à nouveau
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Favoris -->
      <div class="mb-12">
        <h2 class="text-3xl font-bold mb-6 text-everglade">Mes hôtels favoris</h2>
        
        <div v-if="isLoading" class="text-center py-8">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-everglade"></div>
          <p class="mt-2 text-quincy">Chargement de vos favoris...</p>
        </div>
        
        <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
          {{ error }}
        </div>
        
        <div v-else-if="favorites.length === 0" class="bg-colonial-white-light rounded-xl p-8 shadow-md text-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-burning-orange mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
          </svg>
          <h3 class="text-xl font-semibold mb-2 text-quincy">Aucun hôtel favori</h3>
          <p class="text-quincy-light mb-4">Ajoutez des hôtels à vos favoris pour les retrouver facilement.</p>
          <a href="/" class="px-4 py-2 bg-everglade text-colonial-white rounded-lg hover:bg-everglade-light transition-colors inline-block">
            Explorer des hôtels
          </a>
        </div>
        
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="favorite in favorites" :key="favorite.id" class="bg-colonial-white-light rounded-xl overflow-hidden shadow-md">
            <div class="h-40 bg-hawkes-blue-light relative">
              <div 
                :style="{ backgroundImage: `url('/hotels/${favorite.image}')` }"
                class="w-full h-full bg-cover bg-center bg-no-repeat"
              ></div>
              <button class="absolute top-4 right-4 bg-burning-orange text-white p-2 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
              </button>
            </div>
            <div class="p-6">
              <h3 class="text-xl font-semibold mb-2 text-quincy">{{ favorite.hotelName }}</h3>
              
              <div class="flex items-center space-x-2 mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-burning-orange" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <p class="text-quincy">{{ favorite.hotelCity }}, {{ favorite.hotelCountry }}</p>
              </div>
              
              <div class="flex items-center space-x-2 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-burning-orange" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <p class="text-quincy">{{ favorite.hotelAddress }}</p>
              </div>
              
              <div class="flex justify-between items-center pt-4 border-t border-colonial-white-dark">
                <p class="text-xs text-quincy-light">Ajouté le {{ formatDate(favorite.addedDate) }}</p>
                <a :href="`/hotel/${favorite.hotelId}`" class="px-4 py-2 bg-everglade text-colonial-white rounded-lg hover:bg-everglade-light transition-colors">
                  Voir l'hôtel
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.bg-cream {
    background-color: #fff9e9;
}

@keyframes subtle-zoom {
    0% {
        transform: scale(1);
    }
    100% {
        transform: scale(1.05);
    }
}

@keyframes fade-in {
    0% {
        opacity: 0;
    }
    100% {
        opacity: 1;
    }
}

@keyframes slide-up {
    0% {
        opacity: 0;
        transform: translateY(20px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-subtle-zoom {
    animation: subtle-zoom 15s infinite alternate ease-in-out;
}

.animate-fade-in {
    animation: fade-in 1s ease-out forwards;
}

.animate-slide-up {
    animation: slide-up 1s ease-out forwards;
}

/* Classes de couleurs personnalisées pour Tailwind */
.bg-colonial-white {
    background-color: #ffeebc;
}
.bg-colonial-white-light {
    background-color: #fff4d6;
}
.bg-colonial-white-dark {
    background-color: #f5e0a0;
}
.bg-everglade {
    background-color: #26422a;
}
.bg-everglade-light {
    background-color: #335a3a;
}
.bg-burning-orange {
    background-color: #ea672d;
}
.bg-burning-orange-light {
    background-color: #f2845a;
}
.bg-hawkes-blue {
    background-color: #d2e8ff;
}
.bg-hawkes-blue-light {
    background-color: #e9f5ff;
}
.bg-quincy {
    background-color: #5d372a;
}
.bg-quincy-light {
    background-color: #7a4c3d;
}
.text-colonial-white {
    color: #ffeebc;
}
.text-everglade {
    color: #26422a;
}
.text-burning-orange {
    color: #ea672d;
}
.text-quincy {
    color: #5d372a;
}
.text-quincy-light {
    color: #7a4c3d;
}
.text-hawkes-blue {
    color: #d2e8ff;
}
</style> 