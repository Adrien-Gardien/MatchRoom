<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter, navigateTo } from '#imports';
import ImageTextSection from '~/components/organisms/ImageTextSection.vue';
import RoomCarousel from '~/components/organisms/RoomCarousel.vue';
import HotelCard from '~/components/organisms/HotelCard.vue';
import type { Room } from '~/types/room';
import type { Hotel } from '~/types/hotel';

const { $api } = useNuxtApp();

// Dates pour la recherche
const searchDates = ref({
  checkIn: '',
  checkOut: '',
});
const today = new Date().toISOString().split('T')[0];
const minCheckoutDate = computed(() => searchDates.value.checkIn || today);

// Formater les dates pour l’affichage
const formatDate = (dateString: string) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  const day = date.getDate().toString().padStart(2, '0');
  const month = (date.getMonth() + 1).toString().padStart(2, '0');
  return `${day}/${month}`;
};

// Données pour les chambres et hôtels
const room = ref<Room[]>([]);
const hotels = ref<Hotel[]>([]);
const error = ref<string>('');

// Fetch chambres
const fetchRooms = async () => {
  try {
    const response = await useAuthFetch<Room[]>($api('/api/room/first-20'));
    room.value = response.data.value ?? [];
    error.value = '';
  } catch (err) {
    console.error(err);
    error.value = 'Erreur lors du chargement des chambres.';
  }
};

// Fetch hôtels
const fetchHotels = async () => {
  try {
    const response = await useAuthFetch<Hotel[]>($api('/api/hotel/first-20'));
    hotels.value = response.data.value ?? [];
  } catch (err) {
    console.error(err);
    error.value = 'Erreur lors du chargement des hôtels.';
  }
};

onMounted(() => {
  fetchRooms();
  fetchHotels();
});

// Formattage des chambres pour RoomCarousel
const formattedRooms = computed(() => {
  if (!room.value || room.value.length === 0) return [];
  return room.value.map((r) => ({
    id: r.id,
    title: r.name,
    h5Title: r.hotelName || 'Hôtel',
    h4Title: `Capacité: ${r.capacity} personne(s)`,
    h6Title: r.description?.length > 60 ? r.description.substring(0, 60) + '...' : r.description || '',
    image: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80',
    rating: '9.0',
    reviewCount: '50',
    price: `${r.pricePerNight}€ / nuit`,
    discount: '-15% à -30%',
  }));
});

// Formattage des hôtels pour HotelCard
const formattedHotels = computed(() => {
  if (!hotels.value || hotels.value.length === 0) return [];
  return hotels.value.map((hotel) => ({
    id: hotel.id,
    title: hotel.name,
    description: hotel.description,
    city: hotel.city,
    country: hotel.country,
    image: hotel.image || 'https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&auto=format&fit=crop&q=80',
    rooms: hotel.rooms,
    favorites: hotel.favorites,
    owners: hotel.owners,
    tag: hotel.city || 'Premium',
    tagType: 'premium',
    rating: '4.8',
  }));
});

const destination = ref('');
const router = useRouter();

const goToHotelSearch = () => {
  const value = destination.value.trim();
  if (!value) return;

  const query = encodeURIComponent(value);
  router.push(`/hotel?country=${query}`);
};
</script>

<template>
    <div class="min-h-screen bg-cream">
        <!-- Header section -->
        <div class="relative h-[75vh] overflow-hidden rounded-b-[2rem]">
            <!-- Background gradients -->
            <div class="absolute inset-0 bg-gradient-to-r from-black/50 to-black/80 z-10"></div>
            <!-- Background image -->
            <div
                class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80')] bg-cover bg-center animate-subtle-zoom"
            ></div>

            <!-- Text & CTA -->
            <div
                class="relative z-20 flex flex-col items-center justify-center h-full text-colonial-white px-4 sm:px-6 lg:px-8"
            >
                <h1
                    class="text-4xl sm:text-5xl md:text-6xl font-normal text-center mb-4 animate-fade-in drop-shadow-lg"
                >
                    Enchérissez sur votre Séjour de Rêve
                </h1>
                <p class="text-lg sm:text-xl md:text-2xl text-center max-w-3xl mb-8 animate-slide-up">
                    Proposez votre prix pour des chambres d'hôtel de luxe et économisez jusqu'à 40%
                </p>

                <button
                    class="px-6 py-3 bg-hawkes-blue hover:bg-hawkes-blue-light text-black rounded-xl shadow-lg transition transform hover:scale-105 mb-12"
                >
                    Matcher ma room
                </button>
            </div>
        </div>

        <!-- Barre de recherche responsive -->
        <div class="relative -mt-12 z-30 flex justify-center px-4">
            <div class="w-full max-w-4xl bg-[#FFF9E9]/80 shadow-lg rounded-xl overflow-hidden backdrop-blur-sm">
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 divide-y sm:divide-y-0 sm:divide-x divide-colonial-white-dark"
                >
                    <!-- Destination -->
                    <div class="p-3 text-center">
                        <p class="text-quincy-light text-sm mb-1">Destination</p>
                      <input
                          v-model="destination"
                          type="text"
                          placeholder="Paris, Nice..."
                          class="w-full text-sm bg-transparent border-0 focus:ring-0 text-quincy text-center placeholder:text-gray-400"
                      />
                    </div>

                    <!-- Date de départ -->
                    <div class="p-3 text-center">
                        <p class="text-quincy-light text-sm mb-1">Date de départ</p>
                        <div class="relative w-full">
                            <div class="flex items-center justify-center">
                                <input
                                    v-model="searchDates.checkIn"
                                    type="date"
                                    :min="today"
                                    class="w-full text-sm bg-transparent border-0 focus:ring-0 text-quincy placeholder:text-gray-400 opacity-0 absolute inset-0 cursor-pointer z-10"
                                />
                                <div class="flex items-center space-x-2 w-full justify-center">
                                    <svg
                                        class="w-5 h-5 text-quincy-light"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                        <line x1="16" y1="2" x2="16" y2="6" />
                                        <line x1="8" y1="2" x2="8" y2="6" />
                                        <line x1="3" y1="10" x2="21" y2="10" />
                                    </svg>
                                    <span class="text-sm text-quincy">{{
                                        searchDates.checkIn ? formatDate(searchDates.checkIn) : 'JJ/MM'
                                    }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Date d'arrivée -->
                    <div class="p-3 text-center">
                        <p class="text-quincy-light text-sm mb-1">Date d'arrivée</p>
                        <div class="relative w-full">
                            <div class="flex items-center justify-center">
                                <input
                                    v-model="searchDates.checkOut"
                                    type="date"
                                    :min="minCheckoutDate"
                                    class="w-full text-sm bg-transparent border-0 focus:ring-0 text-quincy placeholder:text-gray-400 opacity-0 absolute inset-0 cursor-pointer z-10"
                                    :disabled="!searchDates.checkIn"
                                />
                                <div class="flex items-center space-x-2 w-full justify-center">
                                    <svg
                                        class="w-5 h-5 text-quincy-light"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                        <line x1="16" y1="2" x2="16" y2="6" />
                                        <line x1="8" y1="2" x2="8" y2="6" />
                                        <line x1="3" y1="10" x2="21" y2="10" />
                                    </svg>
                                    <span class="text-sm text-quincy">{{
                                        searchDates.checkOut ? formatDate(searchDates.checkOut) : 'JJ/MM/AA'
                                    }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Voyageurs -->
                    <div class="p-3 text-center">
                        <p class="text-quincy-light text-sm mb-1">Voyageurs</p>
                        <input
                            type="number"
                            min="1"
                            placeholder="2"
                            class="w-full text-sm bg-transparent border-0 focus:ring-0 text-quincy text-center"
                        />
                    </div>

                    <!-- Bouton Rechercher -->
                    <div
                        class="flex items-center justify-center text-center m-4 rounded-xl bg-[#D2E8FF]/70 text-everglade transition-colors"
                    >
                      <button @click="goToHotelSearch" class="w-full py-3 font-semibold text-sm">
                        Rechercher
                      </button>

                    </div>
                </div>
            </div>
        </div>

        <!-- Section H2 avec image et stats -->
        <div class="py-16 bg-cream">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <div class="bg-hawkes-blue rounded-lg overflow-hidden h-96">
                        <!-- Placeholder pour l'image -->
                        <div
                            class="w-full h-full bg-hawkes-blue-light bg-[url('https://images.unsplash.com/photo-1582719508461-905c673771fd?q=80&w=1500&auto=format&fit=crop')] bg-cover bg-center"
                        ></div>
                    </div>

                    <div>
                        <h2 class="text-3xl sm:text-4xl font-bold mb-8 text-everglade">
                            Une nouvelle façon de réserver
                        </h2>

                        <p class="text-quincy mb-10">
                            MatchRoom révolutionne le monde de l'hôtellerie en vous permettant de faire des offres
                            directement aux établissements. Négociez votre tarif idéal pour des chambres d'exception et
                            obtenez des réductions exclusives sur des hôtels qui correspondent exactement à vos
                            attentes.
                        </p>

                        <div class="grid grid-cols-3 gap-8">
                            <div class="text-center">
                                <p class="text-3xl font-bold text-burning-orange mb-2">40%</p>
                                <p class="text-quincy-light text-sm">D'économies moyennes sur vos réservations</p>
                            </div>

                            <div class="text-center">
                                <p class="text-3xl font-bold text-burning-orange mb-2">2h</p>
                                <p class="text-quincy-light text-sm">Temps de réponse moyen des hôteliers</p>
                            </div>

                            <div class="text-center">
                                <p class="text-3xl font-bold text-burning-orange mb-2">92%</p>
                                <p class="text-quincy-light text-sm">
                                    De nos clients satisfaits recommandent MatchRoom
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section H2 avec descriptif -->
        <div class="py-16 bg-cream">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl sm:text-4xl font-bold mb-4 text-everglade">
                    Découvrez nos établissements partenaires
                </h2>

                <p class="text-quincy mb-10">
                    Des hôtels de charme aux palaces prestigieux, nous avons sélectionné les meilleurs établissements
                    pour garantir une expérience inoubliable.<br />
                    Chaque partenaire est rigoureusement choisi pour la qualité de son service et son cadre
                    exceptionnel.
                </p>

                <!-- Cartes H4 -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 mb-20">
                    <HotelCard
                        v-for="hotel in formattedHotels.slice(0, 6)"
                        :id="hotel.id"
                        :key="hotel.id"
                        :image-src="hotel.image"
                        :tag="hotel.city"
                        tag-type="premium"
                        :title="hotel.title"
                        :description="
                            hotel.description
                                ? hotel.description.length > 100
                                    ? hotel.description.substring(0, 100) + '...'
                                    : hotel.description
                                : 'Découvrez cet établissement exceptionnel.'
                        "
                        :rating="hotel.rating"
                    />
                </div>

                <h2 class="text-3xl sm:text-4xl font-bold mb-4 text-everglade">Pourquoi choisir MatchRoom ?</h2>

                <p class="text-quincy mb-10">
                    MatchRoom vous permet de prendre le contrôle de vos dépenses de voyage en faisant des offres
                    adaptées à votre budget.<br />
                    Les hôteliers adorent notre approche qui leur permet d'optimiser leur taux d'occupation avec des
                    clients de qualité.
                </p>
            </div>
        </div>

        <ImageTextSection
            title="Comment fonctionne MatchRoom ?"
            description="En trois étapes simples, trouvez et réservez la chambre parfaite au prix qui vous convient. Recherchez parmi notre sélection d'hôtels premium, proposez votre prix, et si l'établissement accepte votre offre, confirmez instantanément votre réservation sans frais cachés."
            button-text="Découvrir le fonctionnement"
            image-src="https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80"
            image-alt="Illustration MatchRoom"
            image-position="left"
        />

        <ImageTextSection
            title="Des offres adaptées à votre budget"
            description="Notre algorithme intelligent vous propose uniquement des établissements correspondant à vos critères. Faites votre offre et négociez directement avec l'hôtelier. Vous gardez le contrôle total sur vos dépenses tout en découvrant des lieux d'exception."
            button-text="Voir les offres"
            image-src="https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80"
            image-alt="Offres spéciales"
            image-position="right"
            @button-click="showOffers"
        />

        <ImageTextSection
            title="Comment fonctionne MatchRoom ?"
            description="En trois étapes simples, trouvez et réservez la chambre parfaite au prix qui vous convient. Recherchez parmi notre sélection d'hôtels premium, proposez votre prix, et si l'établissement accepte votre offre, confirmez instantanément votre réservation sans frais cachés."
            button-text="Découvrir le fonctionnement"
            image-src="https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80"
            image-alt="Illustration MatchRoom"
            image-position="left"
        />

        <ImageTextSection
            title="Des offres adaptées à votre budget"
            description="Notre algorithme intelligent vous propose uniquement des établissements correspondant à vos critères. Faites votre offre et négociez directement avec l'hôtelier. Vous gardez le contrôle total sur vos dépenses tout en découvrant des lieux d'exception."
            button-text="Voir les offres"
            image-src="https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80"
            image-alt="Offres spéciales"
            image-position="right"
            @button-click="showOffers"
        />

        <!-- Carrousel des chambres -->
        <RoomCarousel
            title="Nos chambres disponibles"
            description="Découvrez notre sélection d'hébergements de luxe à des prix négociables. Choisissez celui qui vous convient et faites votre offre."
            :rooms="formattedRooms"
        />

        <!-- Section Avis Clients -->
        <div class="py-16 bg-cream">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl sm:text-4xl font-bold mb-4 text-center text-everglade">
                    Témoignages de nos clients
                </h2>
                <p class="text-quincy text-center mb-12 max-w-3xl mx-auto">
                    Découvrez ce que nos utilisateurs pensent de leur expérience MatchRoom.<br />
                    Rejoignez notre communauté de voyageurs satisfaits.
                </p>

                <!-- Cartes d'avis -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Avis 1 -->
                    <div class="bg-colonial-white-light rounded-xl p-8 shadow-md">
                        <div class="flex justify-center mb-4">
                            <div
                                class="rounded-full w-24 h-24 bg-white/50 overflow-hidden flex items-center justify-center"
                            >
                                <img
                                    src="https://images.unsplash.com/photo-1580489944761-15a19d654956?ixlib=rb-4.0.3&auto=format&fit=crop&q=80"
                                    alt="Avatar"
                                    class="w-full h-full object-cover"
                                />
                            </div>
                        </div>
                        <h4 class="text-xl font-bold text-center mb-2 text-quincy">Marie Dubois</h4>
                        <div class="flex justify-center mb-3">
                            <div class="flex space-x-1">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="#EA672D"
                                    class="w-5 h-5"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="#EA672D"
                                    class="w-5 h-5"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="#EA672D"
                                    class="w-5 h-5"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="#EA672D"
                                    class="w-5 h-5"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="#F5E0A0"
                                    class="w-5 h-5"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="#F5E0A0"
                                    class="w-5 h-5"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm text-quincy text-center">
                            J'ai économisé près de 30% sur mon séjour à Nice grâce à MatchRoom ! J'avais un budget
                            limité mais je tenais à séjourner dans un hôtel de qualité. L'hôtelier a accepté mon offre
                            en moins d'une heure. Expérience à refaire absolument !
                        </p>
                    </div>

                    <!-- Avis 2 -->
                    <div class="bg-colonial-white-light rounded-xl p-8 shadow-md">
                        <div class="flex justify-center mb-4">
                            <div
                                class="rounded-full w-24 h-24 bg-white/50 overflow-hidden flex items-center justify-center"
                            >
                                <img
                                    src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&q=80"
                                    alt="Avatar"
                                    class="w-full h-full object-cover"
                                />
                            </div>
                        </div>
                        <h4 class="text-xl font-bold text-center mb-2 text-quincy">Thomas Martin</h4>
                        <div class="flex justify-center mb-3">
                            <div class="flex space-x-1">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="#EA672D"
                                    class="w-5 h-5"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="#EA672D"
                                    class="w-5 h-5"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="#EA672D"
                                    class="w-5 h-5"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="#EA672D"
                                    class="w-5 h-5"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="#F5E0A0"
                                    class="w-5 h-5"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm text-quincy text-center">
                            La transparence et la simplicité de MatchRoom m'ont impressionné. J'ai fait des offres pour
                            3 hôtels différents à Paris et j'ai reçu des propositions de chacun, me permettant de
                            choisir celle qui correspondait le mieux à mes besoins. Service exceptionnel !
                        </p>
                    </div>

                    <!-- Avis 3 -->
                    <div class="bg-colonial-white-light rounded-xl p-8 shadow-md">
                        <div class="flex justify-center mb-4">
                            <div
                                class="rounded-full w-24 h-24 bg-white/50 overflow-hidden flex items-center justify-center"
                            >
                                <img
                                    src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?ixlib=rb-4.0.3&auto=format&fit=crop&q=80"
                                    alt="Avatar"
                                    class="w-full h-full object-cover"
                                />
                            </div>
                        </div>
                        <h4 class="text-xl font-bold text-center mb-2 text-quincy">Sophie Leroux</h4>
                        <div class="flex justify-center mb-3">
                            <div class="flex space-x-1">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="#EA672D"
                                    class="w-5 h-5"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="#EA672D"
                                    class="w-5 h-5"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="#EA672D"
                                    class="w-5 h-5"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="#EA672D"
                                    class="w-5 h-5"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="#EA672D"
                                    class="w-5 h-5"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm text-quincy text-center">
                            MatchRoom a révolutionné ma façon de réserver des hôtels. Après plusieurs utilisations, je
                            ne voyage plus sans consulter leurs offres. J'ai pu séjourner dans un boutique hôtel à Lyon
                            qui était normalement hors de mon budget. Service client réactif et professionnel.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section FAQ -->
        <div class="py-16 bg-cream">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl sm:text-4xl font-bold mb-4 text-center text-everglade">Foire aux questions</h2>
                <p class="text-quincy text-center mb-12 max-w-3xl mx-auto">
                    Vous avez des questions sur notre service ? Consultez nos réponses aux questions les plus
                    fréquentes.<br />
                    Notre équipe reste disponible pour toute information complémentaire.
                </p>

                <!-- Accordéon FAQ -->
                <div class="">
                    <!-- Question 1 -->
                    <div class="mb-4">
                        <details class="group">
                            <summary
                                class="flex justify-between items-center bg-hawkes-blue p-6 rounded-xl cursor-pointer"
                            >
                                <h4 class="text-xl font-semibold text-quincy">
                                    Comment fonctionne le système d'enchères de MatchRoom ?
                                </h4>
                                <span
                                    class="bg-white rounded-full w-8 h-8 flex items-center justify-center group-open:rotate-45 transition-transform"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="w-5 h-5 text-everglade"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M12 4.5v15m7.5-7.5h-15"
                                        />
                                    </svg>
                                </span>
                            </summary>
                            <div class="p-6 bg-white rounded-b-xl shadow-sm text-quincy">
                                <p>
                                    Notre système d'enchères inversées permet aux voyageurs de proposer le prix qu'ils
                                    sont prêts à payer pour une chambre. Contrairement aux enchères traditionnelles,
                                    vous ne surenchérissez pas contre d'autres utilisateurs.
                                </p>
                                <p class="mt-3">Voici comment cela fonctionne :</p>
                                <ul class="list-disc pl-5 mt-2 space-y-2">
                                    <li>Sélectionnez l'hôtel qui vous intéresse</li>
                                    <li>Proposez votre tarif pour les dates souhaitées</li>
                                    <li>L'établissement évalue votre offre en fonction de son taux d'occupation</li>
                                    <li>Vous recevez une réponse dans un délai moyen de 2 heures</li>
                                    <li>Si votre offre est acceptée, votre réservation est immédiatement confirmée</li>
                                </ul>
                                <p class="mt-3">
                                    Ce système vous permet de bénéficier de tarifs avantageux tout en offrant aux
                                    hôteliers la flexibilité d'optimiser leur taux d'occupation.
                                </p>
                            </div>
                        </details>
                    </div>

                    <!-- Question 2 -->
                    <div class="mb-4">
                        <details class="group">
                            <summary
                                class="flex justify-between items-center bg-hawkes-blue p-6 rounded-xl cursor-pointer"
                            >
                                <h4 class="text-xl font-semibold text-quincy">
                                    Quelles garanties ai-je sur la qualité des établissements partenaires ?
                                </h4>
                                <span
                                    class="bg-white rounded-full w-8 h-8 flex items-center justify-center group-open:rotate-45 transition-transform"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="w-5 h-5 text-everglade"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M12 4.5v15m7.5-7.5h-15"
                                        />
                                    </svg>
                                </span>
                            </summary>
                            <div class="p-6 bg-white rounded-b-xl shadow-sm text-quincy">
                                <p>
                                    Chez MatchRoom, nous sommes intransigeants sur la qualité des établissements avec
                                    lesquels nous collaborons. Notre processus de sélection rigoureux garantit que tous
                                    nos partenaires répondent à des standards élevés.
                                </p>
                                <p class="mt-3">Nos critères de sélection incluent :</p>
                                <ul class="list-disc pl-5 mt-2 space-y-2">
                                    <li>Une note minimale de 4/5 sur les principales plateformes d'avis</li>
                                    <li>Des inspections régulières par notre équipe qualité</li>
                                    <li>Un engagement écrit sur le respect de notre charte d'excellence</li>
                                    <li>Une attention particulière à la propreté et à l'accueil</li>
                                </ul>
                                <p class="mt-3">
                                    De plus, notre système de notation interne et les avis vérifiés de nos utilisateurs
                                    vous permettent de faire votre choix en toute confiance. Si vous rencontrez le
                                    moindre problème, notre service client est disponible 24/7 pour vous accompagner.
                                </p>
                            </div>
                        </details>
                    </div>

                    <!-- Question 3 -->
                    <div class="mb-4">
                        <details class="group">
                            <summary
                                class="flex justify-between items-center bg-hawkes-blue p-6 rounded-xl cursor-pointer"
                            >
                                <h4 class="text-xl font-semibold text-quincy">
                                    Est-il possible d'annuler une réservation effectuée via MatchRoom ?
                                </h4>
                                <span
                                    class="bg-white rounded-full w-8 h-8 flex items-center justify-center group-open:rotate-45 transition-transform"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="w-5 h-5 text-everglade"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M12 4.5v15m7.5-7.5h-15"
                                        />
                                    </svg>
                                </span>
                            </summary>
                            <div class="p-6 bg-white rounded-b-xl shadow-sm text-quincy">
                                <p>
                                    Oui, MatchRoom offre des conditions d'annulation flexibles qui s'adaptent aux
                                    besoins des voyageurs modernes. Nous comprenons que les plans peuvent changer et
                                    nous avons conçu notre politique d'annulation pour vous offrir un maximum de
                                    flexibilité.
                                </p>
                                <p class="mt-3">Nos conditions d'annulation varient selon les établissements :</p>
                                <ul class="list-disc pl-5 mt-2 space-y-2">
                                    <li>
                                        <strong>Flexibles</strong> : Annulation gratuite jusqu'à 24h avant l'arrivée
                                    </li>
                                    <li>
                                        <strong>Standards</strong> : Annulation gratuite jusqu'à 7 jours avant l'arrivée
                                    </li>
                                    <li>
                                        <strong>Restrictives</strong> : Annulation gratuite jusqu'à 14 jours avant
                                        l'arrivée
                                    </li>
                                </ul>
                                <p class="mt-3">
                                    Les conditions spécifiques sont clairement indiquées avant la finalisation de votre
                                    offre. En cas d'imprévu majeur (maladie, grève des transports, etc.), notre service
                                    client étudiera votre situation avec bienveillance, même hors des délais standards.
                                </p>
                                <p class="mt-3">
                                    Nous proposons également une assurance annulation premium qui vous permet d'annuler
                                    sans justificatif jusqu'à 24h avant votre séjour, quelle que soit la politique de
                                    l'établissement.
                                </p>
                            </div>
                        </details>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div>
            <!-- Ligne de séparation -->
            <div class="border-t border-gray-300 mx-auto max-w-7xl my-8"></div>

            <!-- Section navigation -->
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 pb-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <!-- Logo et Descriptif -->
                    <div class="md:col-span-1">
                        <h2 class="text-3xl font-bold mb-4 text-everglade">MatchRoom</h2>
                        <p class="text-quincy text-sm mb-6">
                            L'approche innovante qui révolutionne la façon dont vous réservez vos séjours en permettant
                            de négocier directement avec les établissements.
                        </p>
                    </div>

                    <!-- Liens - Colonne 1 -->
                    <div class="md:col-span-1">
                        <h4 class="text-lg font-semibold mb-4 text-quincy">Voyageurs</h4>
                        <ul class="space-y-2 text-quincy-light text-sm">
                            <li><a href="#" class="hover:text-burning-orange transition">Nos garanties</a></li>
                            <li><a href="#" class="hover:text-burning-orange transition">Comment ça marche</a></li>
                            <li><a href="#" class="hover:text-burning-orange transition">Partenaires hôteliers</a></li>
                            <li>
                                <a href="#" class="hover:text-burning-orange transition">Destinations populaires</a>
                            </li>
                        </ul>
                    </div>

                    <!-- Liens - Colonne 2 -->
                    <div class="md:col-span-1">
                        <h4 class="text-lg font-semibold mb-4 text-quincy">À propos</h4>
                        <ul class="space-y-2 text-quincy-light text-sm">
                            <li><a href="#" class="hover:text-burning-orange transition">À propos de nous</a></li>
                            <li><a href="#" class="hover:text-burning-orange transition">Contactez-nous</a></li>
                            <li><a href="#" class="hover:text-burning-orange transition">Mentions légales</a></li>
                            <li>
                                <a href="#" class="hover:text-burning-orange transition"
                                    >Politique de confidentialité</a
                                >
                            </li>
                        </ul>
                    </div>

                    <!-- Liens - Colonne 3 -->
                    <div class="md:col-span-1">
                        <h4 class="text-lg font-semibold mb-4 text-quincy">Services</h4>
                        <ul class="space-y-2 text-quincy-light text-sm">
                            <li>
                                <a href="#" class="hover:text-burning-orange transition">Blog et conseils voyage</a>
                            </li>
                            <li><a href="#" class="hover:text-burning-orange transition">Programme de fidélité</a></li>
                            <li><a href="#" class="hover:text-burning-orange transition">Devenir partenaire</a></li>
                            <li><a href="#" class="hover:text-burning-orange transition">Aide et support</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Ligne de séparation -->
            <div class="border-t border-gray-300 mx-auto max-w-7xl"></div>

            <!-- Copyright & Social -->
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="text-quincy-light text-sm mb-4 md:mb-0">
                        Transformez votre façon de voyager. Proposez votre prix, séjournez dans des lieux d'exception,
                        économisez jusqu'à 40%.
                    </div>

                    <!-- Social Icons -->
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-white flex items-center justify-center">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                fill="#26422A"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"
                                />
                            </svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-white flex items-center justify-center">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                fill="#26422A"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"
                                />
                            </svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-white flex items-center justify-center">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                fill="#26422A"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"
                                />
                            </svg>
                        </a>
                    </div>
                </div>

                <p class="text-quincy-light text-center text-xs mt-8">
                    © 2023 MatchRoom. Tous droits réservés. MatchRoom n'est pas responsable du contenu des sites
                    externes.
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .bg-cream {
        background-color: #fff9e9;
    }

    .bg-pattern {
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.2'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
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

@keyframes fade-in-delayed {
  0%, 30% {
    opacity: 0;
  }
  100% {
    opacity: 1;
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

    .animate-fade-in-delayed {
        animation: fade-in-delayed 1.5s ease-out forwards;
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
