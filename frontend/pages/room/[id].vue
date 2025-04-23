<script setup>
import { ref, onMounted, computed, onUnmounted, watch } from 'vue';
import UBaseModal from '~/components/molecules/UBaseModal.vue';
import UDatePicker from '~/components/molecules/UDatePicker.vue';

const route = useRoute();
const id = route.params.id;

// Données factices pour la chambre
const room = ref({
  id: id,
  name: "Suite Présidentielle",
  description: "Notre suite la plus luxueuse offre un espace de vie spacieux avec un salon séparé, une chambre principale avec un lit king-size ultra-confortable, et une salle de bain en marbre avec baignoire à remous et douche à l'italienne. Profitez d'une vue panoramique imprenable sur la ville depuis votre balcon privé. La suite est équipée d'une télévision à écran plat, d'un mini-bar bien approvisionné, d'un coffre-fort, et d'une connexion Wi-Fi haut débit gratuite.",
  pricePerNight: "500.00",
  capacity: 2,
  hotel: {
    id: 1,
    name: "Hôtel Le Magnifique",
    address: "123 Avenue des Champs-Élysées",
    city: "Paris",
    country: "France"
  },
  services: [
    { id: 1, name: "Wi-Fi gratuit" },
    { id: 2, name: "Climatisation" },
    { id: 3, name: "Mini-bar" },
    { id: 4, name: "Coffre-fort" },
    { id: 5, name: "Service en chambre 24h/24" }
  ],
  ambiances: [
    { id: 1, name: "Luxe" },
    { id: 2, name: "Romantique" }
  ],
  ratings: [
    { id: 1, score: 5, comment: "Séjour exceptionnel, chambre magnifique!" },
    { id: 2, score: 4, comment: "Très belle chambre, service impeccable." },
    { id: 3, score: 5, comment: "Un luxe incroyable, je recommande vivement!" }
  ],
  // Images de la chambre (simulées)
  images: [
    {
      id: 1,
      url: "https://images.unsplash.com/photo-1618773928121-c32242e63f39?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80",
      alt: "Vue principale de la suite présidentielle"
    },
    {
      id: 2,
      url: "https://images.unsplash.com/photo-1591088398332-8a7791972843?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80",
      alt: "Chambre à coucher avec lit king-size"
    },
    {
      id: 3,
      url: "https://images.unsplash.com/photo-1584622650111-993a426fbf0a?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80",
      alt: "Salle de bain en marbre luxueuse"
    },
    {
      id: 4,
      url: "https://images.unsplash.com/photo-1578683010236-d716f9a3f461?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80",
      alt: "Salon séparé avec vue"
    },
    {
      id: 5,
      url: "https://images.unsplash.com/photo-1560624052-449f5ddf0c31?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80",
      alt: "Balcon privé avec vue panoramique"
    }
  ],
  // Offre spéciale (simulée)
  specialOffer: {
    title: "Offre exceptionnelle",
    discount: 15,
    description: "Profitez de 15% de réduction pour un séjour de 3 nuits et plus! Offre valable jusqu'au 31 décembre.",
    code: "LUXE15",
    conditions: [
      "Séjour minimum de 3 nuits",
      "Non cumulable avec d'autres offres",
      "Petit-déjeuner inclus",
      "Annulation gratuite jusqu'à 48h avant le séjour"
    ]
  }
});

// État de chargement et gestion d'erreur
const isLoading = ref(true);
const error = ref(null);

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

// Calculer la note moyenne
const averageRating = computed(() => {
  if (!room.value.ratings || room.value.ratings.length === 0) {
    return 0;
  }
  const total = room.value.ratings.reduce((sum, rating) => sum + rating.score, 0);
  return (total / room.value.ratings.length).toFixed(1);
});

// Prix réduit avec l'offre spéciale
const discountedPrice = computed(() => {
  const price = parseFloat(room.value.pricePerNight);
  const discount = room.value.specialOffer.discount / 100;
  return (price * (1 - discount)).toFixed(2);
});

// Prix suggéré (légèrement inférieur au prix normal)
const suggestedPrice = computed(() => {
  const originalPrice = parseFloat(room.value.pricePerNight);
  return (originalPrice * 0.9).toFixed(2); // 10% de réduction suggérée
});

// Calcul du nombre de nuits entre deux dates
const calculateNights = (checkIn, checkOut) => {
  if (!checkIn || !checkOut) return 0;
  const start = new Date(checkIn);
  const end = new Date(checkOut);
  const diffTime = Math.abs(end - start);
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
};

// Mettre à jour le nombre de nuits et le prix total lors du changement de dates
const updateNightsAndTotal = () => {
  negotiationOffer.value.numberOfNights = calculateNights(
    selectedDates.value.checkIn, 
    selectedDates.value.checkOut
  );
  
  if (negotiationOffer.value.pricePerNight && negotiationOffer.value.numberOfNights > 0) {
    negotiationOffer.value.totalPrice = (
      parseFloat(negotiationOffer.value.pricePerNight) * negotiationOffer.value.numberOfNights
    ).toFixed(2);
  } else {
    negotiationOffer.value.totalPrice = 0;
  }
};

// Surveillance des changements de dates pour mettre à jour le nombre de nuits
watch(() => selectedDates.value.checkIn, updateNightsAndTotal);
watch(() => selectedDates.value.checkOut, updateNightsAndTotal);
watch(() => negotiationOffer.value.pricePerNight, updateNightsAndTotal);

onMounted(() => {
  // Simulation d'un temps de chargement
  setTimeout(() => {
    isLoading.value = false;
  }, 1000);
  
  window.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeyDown);
});

// Fonctions
const openNegotiationModal = () => {
  // Réinitialiser les champs
  selectedDates.value = {
    checkIn: null,
    checkOut: null
  };
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
  // Simuler l'envoi de l'offre à l'API
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

const navigateToHotel = () => {
  navigateTo(`/hotel/${room.value.hotel.id}`);
};

// Fonctions de la galerie
const openGallery = (index = 0) => {
  currentImageIndex.value = index;
  isGalleryOpen.value = true;
  // Empêcher le défilement du body quand la galerie est ouverte
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

// Gestion des touches pour la navigation
const handleKeyDown = (e) => {
  if (!isGalleryOpen.value) return;
  
  if (e.key === 'ArrowRight') nextImage();
  else if (e.key === 'ArrowLeft') prevImage();
  else if (e.key === 'Escape') closeGallery();
};
</script>

<template>
  <div v-if="isLoading" class="flex justify-center items-center h-screen">
    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
  </div>

  <div v-else-if="error" class="container mx-auto p-6 text-center">
    <div class="bg-red-100 text-red-700 p-4 rounded-lg">
      <p>{{ error }}</p>
      <button @click="navigateTo('/')" class="mt-4 bg-primary text-white px-4 py-2 rounded">
        Retour à l'accueil
      </button>
    </div>
  </div>

  <div v-else class="container mx-auto p-4 md:p-6">
    <!-- En-tête de la chambre -->
    <div class="bg-white rounded-lg overflow-hidden shadow-md">
      <!-- Galerie d'images principale -->
      <div class="relative">
        <div 
          class="relative h-64 md:h-96 bg-gradient-to-r from-indigo-500 to-purple-600 overflow-hidden cursor-pointer"
          @click="openGallery(0)"
        >
          <img 
            v-if="room.images && room.images.length > 0" 
            :src="room.images[0].url" 
            :alt="room.images[0].alt"
            class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"
          />
          <div class="absolute inset-0 bg-black opacity-20"></div>
          <div class="absolute bottom-4 right-4 bg-white bg-opacity-80 rounded-full p-2 shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
        </div>

        <!-- Miniatures -->
        <div class="flex justify-center -mt-10 relative z-10 px-4">
          <div class="bg-white p-2 rounded-lg shadow-lg flex space-x-2 overflow-x-auto max-w-full">
            <div 
              v-for="(image, index) in room.images.slice(1, 5)" 
              :key="image.id"
              class="w-20 h-20 flex-shrink-0 rounded-md overflow-hidden cursor-pointer hover:opacity-80 transition"
              @click="openGallery(index + 1)"
            >
              <img :src="image.url" :alt="image.alt" class="w-full h-full object-cover" />
            </div>
            <div 
              v-if="room.images.length > 5"
              class="w-20 h-20 flex-shrink-0 rounded-md overflow-hidden cursor-pointer bg-black bg-opacity-50 flex items-center justify-center text-white font-semibold"
              @click="openGallery(0)"
            >
              + {{ room.images.length - 5 }}
            </div>
          </div>
        </div>

        <div class="absolute top-6 left-6 text-white z-10">
          <h1 class="text-3xl md:text-4xl font-bold shadow-text">{{ room.name }}</h1>
          <div class="flex items-center mt-2">
            <div class="flex items-center mr-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
              <span class="ml-1 shadow-text">{{ averageRating }} ({{ room.ratings.length }} avis)</span>
            </div>
            <div class="flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
              </svg>
              <span class="ml-1 shadow-text">{{ room.capacity }} personne{{ room.capacity > 1 ? 's' : '' }}</span>
            </div>
          </div>
          <button 
            @click="navigateToHotel" 
            class="mt-3 text-sm bg-white bg-opacity-20 hover:bg-opacity-30 px-3 py-1 rounded flex items-center"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
            {{ room.hotel.name }}
          </button>
        </div>
      </div>

      <!-- Corps de la page -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6 mt-10">
        <!-- Colonne gauche et centrale -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Description -->
          <div class="bg-white rounded-lg p-6 shadow-sm border">
            <h2 class="text-2xl font-semibold mb-4 text-gray-800">Description</h2>
            <p class="text-gray-700 leading-relaxed">{{ room.description }}</p>
          </div>

          <!-- Services -->
          <div class="bg-white rounded-lg p-6 shadow-sm border">
            <h2 class="text-2xl font-semibold mb-4 text-gray-800">Services inclus</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
              <div v-for="service in room.services" :key="service.id" class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span>{{ service.name }}</span>
              </div>
            </div>
          </div>

          <!-- Galerie photo complète -->
          <div class="bg-white rounded-lg p-6 shadow-sm border">
            <div class="flex justify-between items-center mb-4">
              <h2 class="text-2xl font-semibold text-gray-800">Galerie Photo</h2>
              <button 
                @click="openGallery(0)" 
                class="text-primary hover:text-primary-dark text-sm flex items-center"
              >
                Voir toutes les photos
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
              </button>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
              <div 
                v-for="(image, index) in room.images.slice(0, 6)" 
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
          <div class="bg-white rounded-lg p-6 shadow-sm border">
            <h2 class="text-2xl font-semibold mb-4 text-gray-800">Ambiances</h2>
            <div class="flex flex-wrap gap-2">
              <span 
                v-for="ambiance in room.ambiances" 
                :key="ambiance.id"
                class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm"
              >
                {{ ambiance.name }}
              </span>
            </div>
          </div>

          <!-- Avis -->
          <div class="bg-white rounded-lg p-6 shadow-sm border">
            <h2 class="text-2xl font-semibold mb-4 text-gray-800">Avis des clients</h2>
            <div v-if="room.ratings.length === 0" class="text-gray-500 italic">
              Aucun avis pour le moment.
            </div>
            <div v-else class="space-y-4">
              <div v-for="rating in room.ratings" :key="rating.id" class="border-b pb-4 last:border-b-0">
                <div class="flex items-center mb-2">
                  <div class="flex">
                    <svg v-for="i in 5" :key="i" xmlns="http://www.w3.org/2000/svg" 
                      class="h-5 w-5" 
                      :class="i <= rating.score ? 'text-yellow-400' : 'text-gray-300'"
                      viewBox="0 0 20 20" 
                      fill="currentColor"
                    >
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                  </div>
                </div>
                <p class="text-gray-700">{{ rating.comment }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Colonne droite - Prix et réservation -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-lg p-6 shadow-md sticky top-6 border space-y-6">
            <!-- Prix et réservation -->
            <div>
              <div class="text-center mb-6">
                <p class="text-3xl font-bold ">{{ room.pricePerNight }}€</p>
                <p class="text-gray-600">par nuit</p>
              </div>

              <div class="space-y-4">
                <button 
                  @click="openNegotiationModal" 
                  class="w-full bg-primary text-white py-3 rounded-md hover:bg-primary-dark transition font-semibold"
                >
                  Faire une offre
                </button>
                
                <div class="border-t pt-4">
                  <h3 class="font-semibold text-lg mb-2">Détails rapides</h3>
                  <ul class="space-y-2">
                    <li class="flex items-center">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                      <span>Capacité: {{ room.capacity }} personne{{ room.capacity > 1 ? 's' : '' }}</span>
                    </li>
                    <li class="flex items-center">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                      <span>Paiement sécurisé</span>
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
        <p class="text-indigo-200 mb-6">Négociez directement avec l'hôtel pour obtenir le meilleur tarif pour votre séjour.</p>
        
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
              class="px-4 py-2 bg-white text-indigo-600 rounded-md hover:bg-indigo-100 transition font-medium"
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
            :class="index === currentImageIndex ? 'ring-2 ring-white scale-110' : 'opacity-60 hover:opacity-100'"
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
</style>
