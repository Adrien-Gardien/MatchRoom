<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '~/stores/auth';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const id = route.params.id as string;

const hotel = ref<any>(null);
const rooms = ref<any[]>([]);
const error = ref<string | null>(null);
const favoriteId = ref<number | null>(null);
const isLoading = ref(true);
const isFavorite = ref(false);


const fetchHotel = async () => {
  const apiUrl = useRuntimeConfig().public.apiUrl || 'http://localhost';

  try {
    const data = await $fetch(`${apiUrl}/api/hotel/${id}`, {
      credentials: 'include',
    });
    hotel.value = data;
    rooms.value = data.rooms || [];

    if (authStore.user && data.favorites) {
      const existing = data.favorites.find((f: any) => f.userId === authStore.user.id);
      if (existing) {
        isFavorite.value = true;
        favoriteId.value = existing.id;
      }
    }
  } catch (err: any) {
    console.error('Erreur fetch:', err);
    error.value = err?.message || 'Erreur inconnue';
  } finally {
    isLoading.value = false;
  }
};

const toggleFavorite = async () => {
  const apiUrl = useRuntimeConfig().public.apiUrl || 'http://localhost';

  if (!authStore.user) {
    alert('Vous devez être connecté pour ajouter aux favoris.');
    return;
  }

  try {
    if (isFavorite.value && favoriteId.value !== null) {
      await $fetch(`${apiUrl}/api/favorite/${favoriteId.value}`, {
        method: 'DELETE',
        credentials: 'include',
      });
      isFavorite.value = false;
      favoriteId.value = null;
    } else {
      await $fetch(`${apiUrl}/api/favorite`, {
        method: 'POST',
        credentials: 'include',
        body: {
          userId: authStore.user.id,
          hotelId: hotel.value?.id,
          addedDate: new Date().toISOString(),
        },
      });

      isFavorite.value = true;
      await fetchHotel();
    }
  } catch (err: any) {
    console.error('Erreur lors du toggle favori :', err);
  }
};


const sendContactRequest = () => {
  alert('Message envoyé !');
};

const navigateToHome = () => {
  router.push('/');
};

onMounted(() => {
  fetchHotel();
});

const navigateToRoom = (roomId: number) => {
  router.push(`/room/${roomId}`);
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
    <div class="relative">
      <div class="bg-gray-100 rounded-lg overflow-hidden">
        <div class="relative h-64 md:h-80">
          <div
              class="absolute inset-0"
              :style="`background-image: url('/hotels/${hotel.image}'); background-size: cover; background-position: center;`">
          </div>
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

            <!-- Carrousel horizontal -->
            <div v-else class="overflow-x-auto">
              <div class="flex space-x-6 pb-4 min-w-fit">
                <div
                    v-for="room in rooms"
                    :key="room.id"
                    class="min-w-[300px] max-w-[300px] flex-shrink-0 border rounded-lg overflow-hidden transition-transform hover:scale-105 cursor-pointer"
                    @click="navigateToRoom(room.id)"
                >
                  <div class="h-48 bg-gray-200"
                       :style="`background-image: url('/rooms/${room.image}'); background-size: cover; background-position: center;`">
                  </div>
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
                <textarea class="w-full p-2 border rounded-md h-64"></textarea>
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

