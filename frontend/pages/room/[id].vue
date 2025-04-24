<script setup>
import { ref, onMounted, computed, onUnmounted, watch } from 'vue';
import { useRoute, navigateTo } from '#app';
import UBaseModal from '~/components/molecules/UBaseModal.vue';
import UDatePicker from '~/components/molecules/UDatePicker.vue';

const route = useRoute();
const id = route.params.id;

// État de chargement et gestion d'erreur
const isLoading = ref(true);
const error = ref(null);

// Données de la chambre
const room = ref({
  name: "",
  description: "",
  pricePerNight: 0,
  capacity: 0,
  images: [],
  ratings: [],
  services: [],
  ambiances: [],
  hotel: { name: "" }
});
// État pour les actions utilisateur
const isNegotiationModalOpen = ref(false);
const selectedDates = ref({
  checkIn: null,
  checkOut: null
});
const negotiationOffer = ref({
  pricePerNight: "",
  numberOfNights: 0,
  totalPrice: 0
});

// État pour la galerie d'images
const currentImageIndex = ref(0);
const isGalleryOpen = ref(false);

// État pour l'offre spéciale
const offerCode = ref("");
const isOfferCopied = ref(false);

// Récupération des données de la chambre
const fetchRoom = async () => {
  try {
    isLoading.value = true;
    const response = await $fetch(`/api/room/${id}`);

    room.value = response;
    error.value = null;

  } catch (err) {
    console.error(err);
    error.value = "Erreur lors du chargement des données de la chambre.";
  } finally {
    isLoading.value = false;
  }
};

// Calculer la note moyenne
const averageRating = computed(() => {
  if (!room.value?.ratings?.length) return 0;
  const total = room.value.ratings.reduce((sum, rating) => sum + rating.score, 0);
  return (total / room.value.ratings.length).toFixed(1);
});

// Prix réduit avec l'offre spéciale
const discountedPrice = computed(() => {
  const price = parseFloat(room.value?.pricePerNight || 0);
  const discount = room.value?.specialOffer?.discount || 0;
  return (price * (1 - discount / 100)).toFixed(2);
});

// Prix suggéré (10% de réduction)
const suggestedPrice = computed(() => {
  const price = parseFloat(room.value?.pricePerNight || 0);
  return (price * 0.9).toFixed(2);
});

// Calcul du nombre de nuits
const calculateNights = (checkIn, checkOut) => {
  if (!checkIn || !checkOut) return 0;
  const start = new Date(checkIn);
  const end = new Date(checkOut);
  const diffTime = Math.abs(end - start);
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
};

// Mise à jour des nuits et du total
const updateNightsAndTotal = () => {
  negotiationOffer.value.numberOfNights = calculateNights(
      selectedDates.value.checkIn,
      selectedDates.value.checkOut
  );

  if (negotiationOffer.value.pricePerNight && negotiationOffer.value.numberOfNights > 0) {
    negotiationOffer.value.totalPrice = (
        parseFloat(negotiationOffer.value.pricePerNight) *
        negotiationOffer.value.numberOfNights
    ).toFixed(2);
  } else {
    negotiationOffer.value.totalPrice = 0;
  }
};

// Watchers
watch(() => selectedDates.value.checkIn, updateNightsAndTotal);
watch(() => selectedDates.value.checkOut, updateNightsAndTotal);
watch(() => negotiationOffer.value.pricePerNight, updateNightsAndTotal);

// Montage et démontage
onMounted(() => {
  fetchRoom();
  window.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeyDown);
});

// Modale de négociation
const openNegotiationModal = () => {
  selectedDates.value = { checkIn: null, checkOut: null };
  negotiationOffer.value = {
    pricePerNight: suggestedPrice.value,
    numberOfNights: 0,
    totalPrice: 0
  };
  isNegotiationModalOpen.value = true;
};

const closeNegotiationModal = () => {
  isNegotiationModalOpen.value = false;
};

const submitNegotiationOffer = () => {
  console.log("Offre envoyée:", {
    roomId: id,
    checkIn: selectedDates.value.checkIn,
    checkOut: selectedDates.value.checkOut,
    pricePerNight: negotiationOffer.value.pricePerNight,
    numberOfNights: negotiationOffer.value.numberOfNights,
    totalPrice: negotiationOffer.value.totalPrice
  });

  alert(`Votre offre de ${negotiationOffer.value.totalPrice}€ (${negotiationOffer.value.pricePerNight}€/nuit) a été envoyée à l'hôtel. Vous recevrez une réponse sous 24h.`);
  closeNegotiationModal();
};

// Redirection vers l'hôtel
const navigateToHotel = () => {
  navigateTo(`/hotel/${room.value.hotel.id}`);
};

// Galerie d'images
const openGallery = (index = 0) => {
  currentImageIndex.value = index;
  isGalleryOpen.value = true;
  document.body.style.overflow = 'hidden';
};

const closeGallery = () => {
  isGalleryOpen.value = false;
  document.body.style.overflow = '';
};

const nextImage = () => {
  currentImageIndex.value = (currentImageIndex.value + 1) % room.value.images.length;
};

const prevImage = () => {
  currentImageIndex.value = (currentImageIndex.value - 1 + room.value.images.length) % room.value.images.length;
};

// Gestion clavier pour la galerie
const handleKeyDown = (e) => {
  if (!isGalleryOpen.value) return;

  if (e.key === 'ArrowRight') nextImage();
  else if (e.key === 'ArrowLeft') prevImage();
  else if (e.key === 'Escape') closeGallery();
};

console.log(room);
</script>


<template>
  <div v-if="isLoading" class="flex justify-center items-center h-screen bg-cream">
    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-everglade"></div>
  </div>

  <div v-else-if="error" class="container mx-auto p-6 text-center bg-cream">
    <div class="bg-red-100 text-red-700 p-4 rounded-lg">
      <p>{{ error }}</p>
      <button @click="navigateTo('/')" class="mt-4 bg-everglade text-colonial-white px-4 py-2 rounded-lg hover:bg-everglade-light transition">
        Retour à l'accueil
      </button>
    </div>
  </div>

  <div v-else class="min-h-screen bg-cream">
    <!-- Header section -->
    <div class="relative h-[75vh] overflow-hidden rounded-b-[2rem]">
      <!-- Background gradients -->
      <div class="absolute inset-0 bg-gradient-to-r from-black/50 to-black/80 z-10"></div>
      <!-- Background image -->
      <div 
        v-if="room.images && room.images.length > 0" 
        class="absolute inset-0 bg-cover bg-center animate-subtle-zoom"
        :style="`background-image: url('${room.images[0].url}')`"
      ></div>

      <!-- Text & CTA -->
      <div class="relative z-20 flex flex-col items-start justify-end h-full text-colonial-white px-6 sm:px-8 lg:px-12 pb-20">
        <h1 class="text-4xl sm:text-5xl md:text-6xl font-normal mb-4 animate-fade-in drop-shadow-lg">
          {{ room.name }}
        </h1>
        <div class="flex flex-wrap items-center gap-4 mb-6 animate-slide-up">
          <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400 mr-1" viewBox="0 0 20 20" fill="currentColor">
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
            <span class="text-colonial-white drop-shadow-lg">{{ averageRating }} ({{ room.ratings.length }} avis)</span>
          </div>
          <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-colonial-white mr-1" viewBox="0 0 20 20" fill="currentColor">
              <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
            </svg>
            <span class="text-colonial-white drop-shadow-lg">{{ room.capacity }} personne{{ room.capacity > 1 ? 's' : '' }}</span>
          </div>
          <button 
            @click="navigateToHotel" 
            class="flex items-center bg-[#FFF9E9]/20 hover:bg-[#FFF9E9]/30 px-3 py-1 rounded-lg transition-all"
          >
            <span class="mr-2">{{ room?.hotel?.name }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Miniatures -->
    <div class="relative -mt-12 z-30 flex justify-center px-4">
      <div class="bg-[#FFF9E9]/80 backdrop-blur-sm shadow-lg rounded-xl overflow-hidden p-3 max-w-5xl">
        <div class="flex space-x-3 overflow-x-auto py-2">
          <div 
            v-for="(image, index) in room.images?.slice(0, 5)"
            :key="image.id"
            class="w-20 h-20 flex-shrink-0 rounded-lg overflow-hidden cursor-pointer hover:opacity-80 transition transform hover:scale-105"
            @click="openGallery(index)"
          >
            <img :src="image.url" :alt="image.alt" class="w-full h-full object-cover" />
          </div>
          <div 
            v-if="room.images?.length > 5"
            class="w-20 h-20 flex-shrink-0 rounded-lg overflow-hidden cursor-pointer bg-everglade bg-opacity-70 flex items-center justify-center text-colonial-white font-semibold transition transform hover:scale-105 hover:bg-opacity-80"
            @click="openGallery(0)"
          >
            + {{ room.images?.length - 5 }}
          </div>
        </div>
      </div>
    </div>
    
    <!-- Corps de la page -->
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Colonne gauche et centrale -->
        <div class="lg:col-span-2 space-y-8">
          <!-- Description -->
          <div class="bg-[#FFF9E9] rounded-xl p-6 shadow-sm">
            <h2 class="text-3xl font-semibold mb-4 text-everglade">{{ room.name }}</h2>
            <p class="text-quincy leading-relaxed">{{ room.description }}</p>
          </div>

          <!-- Services -->
          <div class="bg-[#FFF9E9] rounded-xl p-6 shadow-sm">
            <h2 class="text-3xl font-semibold mb-6 text-everglade">Services inclus</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
              <div v-for="service in room.services" :key="service.id" class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-burning-orange mr-2" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="text-quincy">{{ service.name }}</span>
              </div>
            </div>
          </div>

          <!-- Galerie photo -->
          <div class="bg-[#FFF9E9] rounded-xl p-6 shadow-sm">
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-3xl font-semibold text-everglade">Galerie Photo</h2>
              <button 
                @click="openGallery(0)" 
                class="text-everglade hover:text-everglade-light flex items-center"
              >
                Voir toutes les photos
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
              </button>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
              <div 
                v-for="(image, index) in room.images?.slice(0, 6)"
                :key="image.id" 
                class="relative aspect-square rounded-lg overflow-hidden cursor-pointer group"
                @click="openGallery(index)"
              >
                <img :src="image.url" :alt="image.alt" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
                <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-20 transition-opacity"></div>
              </div>
            </div>
          </div>

          <!-- Ambiances -->
          <div class="bg-[#FFF9E9] rounded-xl p-6 shadow-sm">
            <h2 class="text-3xl font-semibold mb-6 text-everglade">Ambiances</h2>
            <div class="flex flex-wrap gap-3">
              <span 
                v-for="ambiance in room.ambiances" 
                :key="ambiance.id"
                class="bg-hawkes-blue text-everglade px-4 py-2 rounded-full text-sm font-medium"
              >
                {{ ambiance.name }}
              </span>
            </div>
          </div>

          <!-- Avis -->
          <div class="bg-[#FFF9E9] rounded-xl p-6 shadow-sm">
            <h2 class="text-3xl font-semibold mb-6 text-everglade">Avis des clients</h2>
            <div v-if="room.ratings.length === 0" class="text-quincy-light italic">
              Aucun avis pour le moment.
            </div>
            <div v-else class="space-y-6">
              <div v-for="rating in room.ratings" :key="rating.id" class="border-b border-colonial-white-dark pb-6 last:border-b-0">
                <div class="flex items-center mb-2">
                  <div class="flex">
                    <svg v-for="i in 5" :key="i" xmlns="http://www.w3.org/2000/svg" 
                      class="h-5 w-5" 
                      :class="i <= rating.score ? 'text-burning-orange' : 'text-gray-300'"
                      viewBox="0 0 20 20" 
                      fill="currentColor"
                    >
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                  </div>
                </div>
                <p class="text-quincy">{{ rating.comment }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Colonne droite - Prix et réservation -->
        <div class="lg:col-span-1">
          <div class="bg-[#FFF9E9] rounded-xl shadow-md sticky top-6 space-y-6 p-6">
            <!-- Prix et réservation -->
            <div>
              <div class="text-center mb-6">
                <p class="text-4xl font-bold text-burning-orange">{{ room.pricePerNight }}€</p>
                <p class="text-quincy-light">par nuit</p>
              </div>

              <div class="space-y-4">
                <button 
                  @click="openNegotiationModal" 
                  class="w-full bg-everglade text-colonial-white py-3 rounded-lg hover:bg-everglade-light transition font-semibold"
                >
                  Faire une offre
                </button>
                
                <div class="border-t border-colonial-white-dark pt-4">
                  <h3 class="font-semibold text-lg mb-3 text-everglade">Détails rapides</h3>
                  <ul class="space-y-3">
                    <li class="flex items-center">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-burning-orange mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                      <span class="text-quincy">Capacité: {{ room.capacity }} personne{{ room.capacity > 1 ? 's' : '' }}</span>
                    </li>
                    <li class="flex items-center">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-burning-orange mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                      <span class="text-quincy">Paiement sécurisé</span>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de négociation -->
    <UBaseModal 
      :is-open="isNegotiationModalOpen" 
      backgroundSize="md"
      @close="closeNegotiationModal"
    >
      <div class="p-8">
        <h2 class="text-2xl font-bold text-white mb-1">Proposer un prix</h2>
        <p class="text-hawkes-blue-light mb-6">Négociez directement avec l'hôtel pour obtenir le meilleur tarif pour votre séjour.</p>
        
        <form class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <UDatePicker 
                v-model="selectedDates.checkIn" 
                label="Date d'arrivée" 
                placeholder="Sélectionner une date" 
                :required="true" 
                :min-date="new Date()"
                :max-date="selectedDates.checkOut"
              />
            </div>
            <div>
              <UDatePicker 
                v-model="selectedDates.checkOut" 
                label="Date de départ" 
                placeholder="Sélectionner une date" 
                :required="true" 
                :min-date="selectedDates.checkIn"
              />
            </div>
          </div>
          
          <div v-if="negotiationOffer.numberOfNights > 0" class="bg-white/10 backdrop-blur-sm rounded-lg p-3 text-center">
            <span class="text-white">
              {{ negotiationOffer.numberOfNights }} 
              nuit{{ negotiationOffer.numberOfNights > 1 ? 's' : '' }} 
              sélectionnée{{ negotiationOffer.numberOfNights > 1 ? 's' : '' }}
            </span>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-white mb-1">Votre offre par nuit*</label>
            <UInputNumber 
              v-model="negotiationOffer.pricePerNight" 
              placeholder="Saisissez le prix par nuit"
              :required="true"
              :min="0"
              :step="5"
              :max="room.pricePerNight"
            />
          </div>
          
          <div v-if="negotiationOffer.totalPrice > 0" class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
            <div class="flex justify-between items-center mb-2">
              <span class="text-white">Prix par nuit</span>
              <span class="text-white">{{ negotiationOffer.pricePerNight }}€</span>
            </div>
            <div class="flex justify-between items-center mb-2">
              <span class="text-white">Nombre de nuits</span>
              <span class="text-white">{{ negotiationOffer.numberOfNights }}</span>
            </div>
            <div class="flex justify-between items-center pt-2 border-t border-white/20">
              <span class="text-white font-semibold">Prix total</span>
              <span class="text-white font-semibold">{{ negotiationOffer.totalPrice }}€</span>
            </div>
          </div>
          
          <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4">
            <button 
              type="button" 
              @click="closeNegotiationModal" 
              class="px-4 py-2 border border-white/30 text-white rounded-md hover:bg-white/10 transition"
            >
              Annuler
            </button>
            <button 
              type="submit" 
              @click.prevent="submitNegotiationOffer"
              class="px-4 py-2 bg-white text-everglade rounded-md hover:bg-colonial-white transition font-medium"
              :disabled="negotiationOffer.numberOfNights === 0 || !negotiationOffer.pricePerNight"
            >
              Envoyer ma proposition
            </button>
          </div>
        </form>
      </div>
    </UBaseModal>

    <!-- Galerie d'images plein écran -->
    <div v-if="isGalleryOpen" class="fixed inset-0 bg-black bg-opacity-90 z-50 flex flex-col">
      <!-- Barre d'outils supérieure -->
      <div class="flex justify-between items-center p-4 text-white">
        <div class="text-lg font-medium">{{ currentImageIndex + 1 }} / {{ room.images.length }}</div>
        <button @click="closeGallery" class="p-2 hover:bg-white hover:bg-opacity-10 rounded-full transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      
      <!-- Container de l'image principale -->
      <div class="flex-1 flex items-center justify-center p-4 relative">
        <button 
          @click="prevImage"
          class="absolute left-4 p-3 bg-black bg-opacity-50 hover:bg-opacity-70 rounded-full text-white transition"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>
        
        <img 
          :src="room.images[currentImageIndex].url" 
          :alt="room.images[currentImageIndex].alt"
          class="max-h-full max-w-full object-contain"
        />
        
        <button 
          @click="nextImage"
          class="absolute right-4 p-3 bg-black bg-opacity-50 hover:bg-opacity-70 rounded-full text-white transition"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>
      
      <!-- Miniatures en bas -->
      <div class="p-4 bg-black bg-opacity-50">
        <div class="flex space-x-2 overflow-x-auto py-2">
          <div 
            v-for="(image, index) in room.images" 
            :key="image.id"
            class="w-16 h-16 flex-shrink-0 rounded overflow-hidden cursor-pointer transition-all duration-200"
            :class="index === currentImageIndex ? 'ring-2 ring-burning-orange scale-110' : 'opacity-60 hover:opacity-100'"
            @click="currentImageIndex = index"
          >
            <img :src="image.url" :alt="image.alt" class="w-full h-full object-cover" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.shadow-text {
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
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
    transform: translateY(20px);
    opacity: 0;
  }
  100% {
    transform: translateY(0);
    opacity: 1;
  }
}

.animate-subtle-zoom {
  animation: subtle-zoom 15s ease-in-out infinite alternate;
}

.animate-fade-in {
  animation: fade-in 1.5s ease-out;
}

.animate-slide-up {
  animation: slide-up 1s ease-out;
}
</style>
