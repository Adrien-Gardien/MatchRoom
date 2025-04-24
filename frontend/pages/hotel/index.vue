<script setup lang="ts">
import { useRoute, useRouter } from 'vue-router';
import { ref, onMounted } from 'vue';
import type { Hotel } from '~/types/hotel';

const { $api } = useNuxtApp();
const route = useRoute();
const router = useRouter();
const destination = route.query.country?.toString() || '';

const hotels = ref<Hotel[]>([]);
const error = ref<string>('');

const fetchFilteredHotels = async () => {
  try {
    let query = '';

    if (destination.includes(',')) {
      const [city, country] = destination.split(',').map(x => x.trim());
      if (city) query += `city=${encodeURIComponent(city)}`;
      if (country) query += `&country=${encodeURIComponent(country)}`;
    } else {
      query = `country=${encodeURIComponent(destination)}`;
    }

    const response = await useAuthFetch<Hotel[]>($api(`/api/hotel?${query}`));
    hotels.value = response.data.value ?? [];
  } catch (err) {
    console.error(err);
    error.value = 'Erreur lors de la recherche des hôtels.';
  }
};

// Rediriger vers la page de détails d'un hôtel
const goToHotelDetails = (hotelId: string) => {
  router.push(`/hotel/${hotelId}`);
};

onMounted(() => {
  if (destination) fetchFilteredHotels();
});
</script>

<template>
  <div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold text-center mb-6">Résultats pour "{{ destination }}"</h1>

    <div v-if="hotels.length === 0" class="text-center text-gray-500">Aucun hôtel trouvé</div>
    <div v-else  class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 mb-20">
      <div v-for="hotel in hotels" :key="hotel.id" @click="goToHotelDetails(hotel.id)">
        <div class="bg-white rounded-2xl p-0 shadow-lg overflow-hidden transform transition-all duration-300 hover:shadow-xl hover:-translate-y-2 cursor-pointer">
          <div class="relative">
            <!-- Image d'arrière-plan -->
            <div class="w-full h-48 bg-cover bg-center" :style="{ backgroundImage: `url(${hotel.image})` }"></div>
            <!-- Badge de l'hôtel -->
            <div class="absolute top-4 right-4 text-xs font-semibold px-3 py-1 rounded-full bg-burning-orange text-white">
              {{ hotel.name }}
            </div>
          </div>
          <div class="p-6">
            <!-- Nom de l'hôtel -->
            <h4 class="text-xl font-bold mb-3 text-everglade">{{ hotel.name }}</h4>
            <!-- Description de l'hôtel -->
            <p class="text-quincy-light text-sm mb-4">{{ hotel.description }}</p>
            <div class="flex justify-between items-center">
              <!-- Évaluation de l'hôtel -->
              <div class="flex items-center space-x-1">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#EA672D" class="w-4 h-4">
                  <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-sm font-medium text-quincy">{{ hotel.rating }}</span>
              </div>
              <!-- Bouton explorer -->
              <a href="#" class="text-sm font-semibold text-hawkes-blue-light bg-everglade px-4 py-2 rounded-lg hover:bg-everglade-light transition-colors">
                Explorer
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.bg-burning-orange {
  background-color: #EA672D;
}
.text-everglade {
  color: #2B7A4C;
}
.text-quincy-light {
  color: #9E9E9E;
}
.text-quincy {
  color: #616161;
}
.text-hawkes-blue-light {
  color: #6F88C4;
}
.bg-everglade {
  background-color: #2B7A4C;
}
.hover\:bg-everglade-light:hover {
  background-color: #4D9F73;
}
</style>
