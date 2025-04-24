<script setup lang="ts">
import { useRoute } from 'vue-router';
import { ref, onMounted } from 'vue';
import type { Hotel } from '~/types/hotel';
import HotelCard from '~/components/organisms/HotelCard.vue';

const { $api } = useNuxtApp();
const route = useRoute();  // Récupérer la route
const destination = route.query.destination?.toString() || '';  // La valeur de la query 'destination'

const hotels = ref<Hotel[]>([]);
const error = ref<string>('');

const fetchFilteredHotels = async () => {
  try {
    let query = '';

    // Si destination contient une virgule (pour séparer ville et pays)
    if (destination.includes(',')) {
      const [city, country] = destination.split(',').map(x => x.trim());
      if (city) query += `city=${encodeURIComponent(city)}`;
      if (country) query += `&country=${encodeURIComponent(country)}`;
    } else {
      // Si destination ne contient pas de virgule, on considère que c'est un pays
      query = `country=${encodeURIComponent(destination)}`;
    }

    const response = await useAuthFetch<Hotel[]>($api(`/api/hotel?${query}`));
    hotels.value = response.data.value ?? [];
  } catch (err) {
    console.error(err);
    error.value = 'Erreur lors de la recherche des hôtels.';
  }
};

onMounted(() => {
  if (destination) fetchFilteredHotels();  // Lancer la recherche si une destination existe
});
</script>

<template>
  <div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold text-center mb-6">Résultats pour "{{ destination }}"</h1>

    <!-- Si aucun hôtel trouvé -->
    <div v-if="hotels.length === 0" class="text-center text-gray-500">Aucun hôtel trouvé</div>

    <!-- Si des hôtels sont trouvés -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <HotelCard v-for="hotel in hotels" :key="hotel.id" :hotel="hotel" />
    </div>
  </div>
</template>
