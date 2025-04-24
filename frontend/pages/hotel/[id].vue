<script setup>
import { ref, onMounted } from 'vue';

const route = useRoute();
const id = route.params.id;

// Données factices pour l'hôtel
const hotel = ref({
  id: id,
  name: "Hôtel Le Magnifique",
  address: "123 Avenue des Champs-Élysées",
  city: "Paris",
  country: "France",
  description: "Situé au cœur de Paris, l'Hôtel Le Magnifique offre une expérience de séjour exceptionnelle. Avec sa façade historique, ses chambres élégantes et son service personnalisé, notre établissement combine le charme parisien classique avec le confort moderne. Profitez de notre restaurant gastronomique, de notre spa de luxe et de notre vue imprenable sur la ville. À quelques pas des principales attractions touristiques, des boutiques de luxe et des restaurants renommés, l'Hôtel Le Magnifique est l'endroit idéal pour découvrir la Ville Lumière."
});

// Données factices pour les chambres
const rooms = ref([
  {
    id: 1,
    name: "Chambre Classique",
    description: "Chambre confortable avec vue sur la cour intérieure, lit double et salle de bain privative.",
    price: 150
  },
  {
    id: 2,
    name: "Suite Junior",
    description: "Suite spacieuse avec salon séparé, lit king-size et grande salle de bain avec baignoire.",
    price: 250
  },
  {
    id: 3,
    name: "Suite Présidentielle",
    description: "Notre suite la plus luxueuse avec vue panoramique, salon, salle à manger et jacuzzi privé.",
    price: 500
  },
  {
    id: 4,
    name: "Chambre Familiale",
    description: "Chambre spacieuse pouvant accueillir jusqu'à 4 personnes avec 2 lits doubles.",
    price: 220
  },
  {
    id: 5,
    name: "Chambre Deluxe",
    description: "Chambre élégante avec balcon privé, lit queen-size et salle de bain en marbre.",
    price: 180
  }
]);

const isLoading = ref(true);
const error = ref(null);
const isHotelFavorite = ref(false);
const isFavorite = ref(false);

// Fonctions 
const toggleFavorite = () => {
  isFavorite.value = !isFavorite.value;
  // Simulation d'API call
  console.log(`Hotel ${id} ${isFavorite.value ? 'ajouté aux' : 'retiré des'} favoris`);
};

onMounted(() => {
  // Simulation d'un temps de chargement
  setTimeout(() => {
    isLoading.value = false;
  }, 1000);
});

const sendContactRequest = () => {
  // Simulation d'envoi du formulaire
  alert('Votre message a été envoyé (simulation)');
}
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
    <div class="relative">
      <div class="bg-gray-100 rounded-lg overflow-hidden">
        <div class="relative h-64 md:h-80">
          <!-- Image placeholder - à remplacer par l'image réelle de l'hôtel -->
          <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-purple-600"></div>
          <div class="absolute inset-0 bg-black opacity-40"></div>
          <div class="absolute bottom-0 left-0 p-6 text-white">
            <h1 class="text-3xl md:text-4xl font-bold">{{ hotel.name }}</h1>
            <div class="flex items-center mt-2">
              <span class="text-yellow-400 mr-2">
                <i class="fas fa-map-marker-alt"></i>
              </span>
              <p>{{ hotel.address }}, {{ hotel.city }}, {{ hotel.country }}</p>
            </div>
          </div>
        </div>
      </div>
      
      <div class="absolute top-4 right-4">
        <button 
          @click="toggleFavorite" 
          class="flex items-center justify-center p-2 rounded-full bg-white shadow-md hover:bg-gray-100 transition-colors"
          :class="{ 'text-red-500': isFavorite, 'text-gray-400': !isFavorite }"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" :fill="isFavorite ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
          </svg>
        </button>
      </div>
    </div>
    
    <div class="mt-6">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
          <div class="bg-white rounded-lg p-6 shadow-md my-6">
            <h2 class="text-2xl font-semibold mb-4 text-gray-800">À propos de cet établissement</h2>
            <p class="text-gray-700 leading-relaxed">{{ hotel.description }}</p>
          </div>
          
          <div class="bg-white rounded-lg p-6 shadow-md my-6">
            <h2 class="text-2xl font-semibold mb-4 text-gray-800">Chambres disponibles</h2>
            
            <div v-if="rooms.length === 0" class="text-gray-500 italic">
              Aucune chambre disponible pour le moment.
            </div>
            
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div v-for="room in rooms" :key="room.id" class="border rounded-lg overflow-hidden transition-transform hover:scale-105">
                <div class="h-48 bg-gray-200"></div>
                <div class="p-4">
                  <h3 class="font-semibold text-lg mb-2">{{ room.name || 'Chambre' }}</h3>
                  <p class="text-gray-700 text-sm mb-4">{{ room.description || 'Description non disponible' }}</p>
                  <div class="flex justify-between items-center">
                    <span class="font-bold text-primary">{{ room.price ? `${room.price}€` : 'Prix sur demande' }}</span>
                    <button class="bg-primary text-white px-4 py-2 rounded-md hover:bg-primary-dark transition">
                      Réserver
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="lg:col-span-1">
          <div class="bg-white rounded-lg p-6 shadow-md sticky top-6">
            <h2 class="text-xl font-semibold mb-4">Contactez cet établissement</h2>
            <form @submit.prevent="sendContactRequest" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Votre nom</label>
                <input type="text" class="w-full p-2 border rounded-md" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Votre email</label>
                <input type="email" class="w-full p-2 border rounded-md" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                <textarea class="w-full p-2 border rounded-md h-24"></textarea>
              </div>
              <button type="submit" class="w-full bg-primary text-white py-2 rounded-md hover:bg-primary-dark transition">
                Envoyer
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

