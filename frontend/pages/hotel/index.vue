<script setup lang="ts">
import { useRoute, useRouter } from 'vue-router'; // Ajout de useRouter
import { ref, onMounted } from 'vue';
import type { Hotel } from '~/types/hotel';
import HotelCard from '~/components/organisms/HotelCard.vue';

const { $api } = useNuxtApp();
const route = useRoute();
const router = useRouter(); // Initialisation du router
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
  router.push(`/hotel/${hotelId}`); // Redirection vers la page de détails avec l'ID de l'hôtel
};

onMounted(() => {
  if (destination) fetchFilteredHotels();
});
</script>

<template>
  <div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold text-center mb-6">Résultats pour "{{ destination }}"</h1>

    <div v-if="hotels.length === 0" class="text-center text-gray-500">Aucun hôtel trouvé</div>
    <div v-else>
      <div v-for="hotel in hotels" :key="hotel.id" @click="goToHotelDetails(hotel.id)">
        <HotelCard :hotel="hotel" />
      </div>
    </div>
  </div>
</template>
