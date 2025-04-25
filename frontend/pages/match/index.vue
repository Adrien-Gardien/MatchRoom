<script setup lang="ts">
import type { Room } from '~/types/room';

const { $api } = useNuxtApp();
const toast = useToast();

// État
const rooms = ref<Room[]>([]);
const currentIndex = ref(0);
const loading = ref(true);
const currentImageIndex = ref(0);
const showNegotiationModal = ref(false);
const offerPrice = ref<number | null>(null);
const isSubmitting = ref(false);
const swipeDirection = ref<'left' | 'right' | null>(null);

// Chambre actuelle
const currentRoom = computed(() => {
  if (rooms.value.length === 0) return null;
  return rooms.value[currentIndex.value];
});

// Charger des chambres aléatoires
const loadRooms = async () => {
  try {
    loading.value = true;
    const { data, error } = await useAuthFetch<Room[]>($api('/api/rooms/random?amount=10'));
    
    if (error.value) {
      toast.error('Erreur', 'Impossible de charger les chambres', { duration: 5000 });
      loading.value = false;
      return;
    }
    
    if (data.value && data.value.length > 0) {
      rooms.value = data.value;
      currentIndex.value = 0;
      currentImageIndex.value = 0;
    } else {
      toast.info('Information', 'Aucune chambre disponible en ce moment', { duration: 3000 });
    }
    
    loading.value = false;
  } catch (error) {
    console.error('Erreur lors du chargement des chambres:', error);
    toast.error('Erreur', 'Impossible de charger les chambres', { duration: 5000 });
    loading.value = false;
  }
};

// Swipe gauche (passer)
const swipeLeft = () => {
  if (currentIndex.value < rooms.value.length - 1) {
    swipeDirection.value = 'left';
    setTimeout(() => {
      currentIndex.value++;
      currentImageIndex.value = 0;
      swipeDirection.value = null;
    }, 300);
  } else {
    swipeDirection.value = 'left';
    setTimeout(() => {
      rooms.value = [];
      swipeDirection.value = null;
    }, 300);
  }
};

// Swipe droit (intéressé)
const swipeRight = () => {
  // Afficher un toast pour indiquer l'intérêt
  toast.success('Favoris', 'Chambre ajoutée à vos favoris!', { duration: 3000 });
  
  // Passer à la chambre suivante
  if (currentIndex.value < rooms.value.length - 1) {
    swipeDirection.value = 'right';
    setTimeout(() => {
      currentIndex.value++;
      currentImageIndex.value = 0;
      swipeDirection.value = null;
    }, 300);
  } else {
    swipeDirection.value = 'right';
    setTimeout(() => {
      rooms.value = [];
      swipeDirection.value = null;
    }, 300);
  }
};

// Ouvrir le modal de négociation
const openNegotiationModal = () => {
  if (!currentRoom.value) return;
  offerPrice.value = Math.floor(currentRoom.value.price * 0.9); // Par défaut propose 90% du prix
  showNegotiationModal.value = true;
};

// Fermer le modal de négociation
const closeNegotiationModal = () => {
  showNegotiationModal.value = false;
};

// Soumettre une offre
const submitOffer = async () => {
  if (!currentRoom.value || !offerPrice.value) return;
  
  isSubmitting.value = true;
  
  try {
    const { error } = await useAuthFetch($api('/api/negotiations'), {
      method: 'POST',
      body: {
        roomId: currentRoom.value.id,
        proposedPrice: offerPrice.value
      }
    });
    
    if (error.value) {
      toast.error('Erreur', 'Impossible d\'envoyer votre offre', { duration: 5000 });
      isSubmitting.value = false;
      return;
    }
    
    // Fermer le modal et montrer un toast
    showNegotiationModal.value = false;
    toast.success('Offre envoyée', 'Votre offre a été envoyée avec succès!', { duration: 5000 });
    
    // Passer à la chambre suivante
    if (currentIndex.value < rooms.value.length - 1) {
      currentIndex.value++;
      currentImageIndex.value = 0;
    } else {
      rooms.value = [];
    }
  } catch (error) {
    console.error('Erreur lors de l\'envoi de l\'offre:', error);
    toast.error('Erreur', 'Impossible d\'envoyer votre offre', { duration: 5000 });
  } finally {
    isSubmitting.value = false;
  }
};

// Charger les chambres au chargement de la page
onMounted(() => {
  loadRooms();
});
</script>

<template>
  <NuxtLayout name="customer">
    <div class="max-w-4xl mx-auto px-4 py-10 sm:py-8">
      <h1 class="text-2xl sm:text-3xl font-bold mb-6 sm:mb-8 text-center">MatchRoom</h1>
      
      <div v-if="loading" class="flex justify-center items-center h-64">
        <ULoading />
      </div>
      
      <div v-else-if="currentRoom" class="relative">
        <!-- Card pour la chambre actuelle -->
        <div 
          ref="cardElement" 
          class="bg-primary border border-secondary rounded-xl shadow-sm overflow-hidden transform transition-all duration-300"
          :class="{ 'translate-x-[200%] opacity-0 rotate-12': swipeDirection === 'right', 'translate-x-[-200%] opacity-0 -rotate-12': swipeDirection === 'left' }"
        >
          <!-- Carousel d'images -->
          <div class="relative h-60 sm:h-80">
            <div v-if="currentRoom.images && currentRoom.images.length > 0" class="h-full w-full">
              <img 
                :src="currentRoom.images[currentImageIndex].url" 
                class="h-full w-full object-cover"
                alt="Room image"
              />
              
              <!-- Contrôles du carousel -->
              <div class="absolute bottom-4 left-0 right-0 flex justify-center gap-1">
                <button 
                  v-for="(_, index) in currentRoom.images" 
                  :key="index"
                  class="w-2 h-2 rounded-full"
                  :class="currentImageIndex === index ? 'bg-brand-solid' : 'bg-gray-400'"
                  @click="currentImageIndex = index"
                ></button>
              </div>
              
              <div class="absolute top-0 left-0 right-0 p-4 bg-gradient-to-b from-black/50 to-transparent">
                <div class="flex items-center justify-between">
                  <span class="bg-brand-solid text-white px-2 py-1 sm:px-3 sm:py-1 rounded-full text-xs sm:text-sm font-bold">
                    {{ currentRoom.price }}€ / nuit
                  </span>
                  <span class="bg-brand-solid text-white px-2 py-1 sm:px-3 sm:py-1 rounded-full text-xs sm:text-sm">
                    {{ currentRoom.hotel.name }}
                  </span>
                </div>
              </div>
            </div>
            <div v-else class="h-full w-full bg-gray-200 flex items-center justify-center">
              <span class="text-gray-500">Pas d'image disponible</span>
            </div>
          </div>
          
          <!-- Détails de la chambre -->
          <div class="p-4 sm:p-6">
            <h2 class="text-xl sm:text-2xl font-bold mb-2 text-primary">{{ currentRoom.name }}</h2>
            <div class="flex flex-wrap items-center gap-2 mb-3">
              <span class="text-tertiary text-sm sm:text-base">{{ currentRoom.capacity }} personnes</span>
              <span class="text-tertiary">•</span>
              <span class="text-tertiary text-sm sm:text-base">{{ currentRoom.hotel.city }}</span>
            </div>
            
            <p class="text-tertiary mb-4 line-clamp-3 text-sm sm:text-base">{{ currentRoom.description }}</p>
            
            <!-- Boutons d'actions -->
            <div class="flex justify-between mt-4">
              <UButton 
                variant="secondary"
                class="!rounded-full !w-12 !h-12 sm:!w-14 sm:!h-14 !p-0 !border-red-500 !text-red-500 hover:!bg-red-50"
                @click="swipeLeft"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-8 sm:w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </UButton>
              
              <UButton 
                variant="secondary"
                class="!rounded-full !w-12 !h-12 sm:!w-14 sm:!h-14 !p-0 !border-brand-solid !text-brand-solid hover:!bg-brand-50"
                @click="openNegotiationModal"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-8 sm:w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
              </UButton>
              
              <UButton 
                variant="secondary"
                class="!rounded-full !w-12 !h-12 sm:!w-14 sm:!h-14 !p-0 !border-green-500 !text-green-500 hover:!bg-green-50"
                @click="swipeRight"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-8 sm:w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
              </UButton>
            </div>
          </div>
        </div>
      </div>
      
      <div v-else class="text-center py-8">
        <h3 class="text-lg sm:text-xl font-medium mb-3 sm:mb-4 text-primary">Plus de chambres disponibles</h3>
        <p class="text-tertiary text-sm sm:text-base mb-5 sm:mb-6">Revenez plus tard pour découvrir de nouvelles chambres</p>
        <UButton 
          variant="primary"
          @click="loadRooms"
        >
          Rafraîchir
        </UButton>
      </div>
    </div>
    
    <!-- Modal de négociation avec UBaseModal -->
    <UBaseModal 
      :is-open="showNegotiationModal"
      background="circles"
      @close="closeNegotiationModal"
    >
      <div class="p-4 sm:p-6">
        <h3 class="text-lg sm:text-xl font-bold mb-3 sm:mb-4 text-primary">Faire une offre</h3>
        <p class="mb-3 sm:mb-4 text-sm sm:text-base text-tertiary">
          Prix initial: <span class="font-bold text-primary">{{ currentRoom?.price }}€</span> / nuit
        </p>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-secondary mb-1">Votre offre (€ par nuit)</label>
          <input 
            v-model="offerPrice" 
            type="number" 
            class="w-full px-3 sm:px-4 py-2 border border-secondary rounded-lg focus:ring focus:ring-brand-solid text-secondary text-sm sm:text-base bg-primary"
            :placeholder="currentRoom?.price + ''"
          />
        </div>
        
        <div class="flex gap-3 sm:gap-4 justify-end">
          <UButton
            variant="tertiary"
            @click="closeNegotiationModal"
          >
            Annuler
          </UButton>
          <UButton
            variant="primary"
            :is-loading="isSubmitting"
            :disabled="isSubmitting"
            @click="submitOffer"
          >
            {{ isSubmitting ? 'Envoi...' : 'Envoyer l\'offre' }}
          </UButton>
        </div>
      </div>
    </UBaseModal>
  </NuxtLayout>
</template>

<style scoped>
/* Animation de transition pour les cartes */
.card-enter-active,
.card-leave-active {
  transition: all 0.5s ease;
}

.card-enter-from,
.card-leave-to {
  opacity: 0;
  transform: translateY(30px);
}

/* Ajout de styles responsive pour les petits écrans */
@media (max-width: 640px) {
  .card-enter-from,
  .card-leave-to {
    transform: translateY(20px);
  }
}
</style>