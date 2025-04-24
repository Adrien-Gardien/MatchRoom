<template>
  <div class="py-16 bg-cream">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="text-3xl font-bold mb-10 text-everglade">{{ title }}</h2>
      
      <p class="text-quincy mb-10">{{ description }}</p>
      
      <div class="relative overflow-hidden">
        <!-- Navigation buttons -->
        <button 
          class="absolute left-4 top-1/2 transform -translate-y-1/2 z-10 bg-colonial-white rounded-full w-12 h-12 flex items-center justify-center shadow-md hover:bg-colonial-white-light transition-colors"
          @click="prevSlide"
        >
          <span class="text-everglade text-2xl">&lt;</span>
        </button>
        
        <button 
          class="absolute right-4 top-1/2 transform -translate-y-1/2 z-10 bg-colonial-white rounded-full w-12 h-12 flex items-center justify-center shadow-md hover:bg-colonial-white-light transition-colors"
          @click="nextSlide"
        >
          <span class="text-everglade text-2xl">&gt;</span>
        </button>
        
        <!-- Cards container -->
        <div 
          class="flex transition-transform duration-300 " 
          :style="{ transform: `translateX(-${currentSlide * (100 / visibleSlides)}%)` }"
        >
          <div 
            v-for="(room, index) in rooms" 
            :key="index"
            class="w-full md:w-1/2 lg:w-1/4 flex-shrink-0 px-3 rounded-b-3xl"
          >
            <div class="relative bg-transparent rounded-3xl overflow-hidden shadow-md room-card">
              <!-- Image couvrant toute la card -->
              <div class="absolute inset-0 z-0 rounded-3xl">
                <img :src="room.image" :alt="room.title" class="w-full h-full object-cover rounded-b-3xl">
                <div class="absolute inset-0 bg-gradient-to-b from-black/20 via-transparent to-black/70 rounded-b-3xl"></div>
              </div>
              
              <!-- Contenu en haut de la card -->
              <div class="relative z-10 p-4">
                <h3 class="text-xl text-white font-semibold">{{ room.title }}</h3>
              </div>
              
              <!-- Note et avis -->
              <div class="absolute z-10 right-4 top-4 flex items-center bg-black bg-opacity-50 px-2 py-1 rounded-xl">
                <span class="text-burning-orange mr-1">★</span>
                <span class="text-white font-bold">{{ room.rating }}</span>
                <span class="text-white text-sm ml-1">({{ room.reviewCount }})</span>
              </div>
              
              <!-- Section en bas avec fond semi-transparent -->
              <div class="relative z-10 mt-auto bg-white/60  p-4 pt-3 rounded-3xl">
                <div class="mb-2">
                  <h5 class="text-sm font-semibold text-everglade">{{ room.h5Title }}</h5>
                  <h4 class="text-lg font-semibold text-everglade my-1">{{ room.h4Title }}</h4>
                  <h6 class="text-sm font-light text-everglade mb-2">{{ room.h6Title }}</h6>
                </div>
                
                <div class="flex items-center justify-between pt-2 border-t border-gray-200">
                  <div class="text-xl font-bold text-everglade">{{ room.price }}</div>
                  <div class="px-3 py-1 bg-burning-orange rounded text-sm text-white discount-badge">
                    {{ room.discount }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  title: {
    type: String,
    default: 'Chambres disponibles'
  },
  description: {
    type: String,
    default: 'Découvrez nos meilleures offres pour votre prochain séjour'
  },
  rooms: {
    type: Array,
    default: () => [
      {
        title: 'H3- Titre',
        h5Title: 'H5- Titre',
        h4Title: 'H4- Titre',
        h6Title: 'H6- Titre',
        image: 'https://images.unsplash.com/photo-1566073771259-6a8506099945',
        rating: '9.4',
        reviewCount: '164',
        price: '170€ / nuits',
        discount: '-15% à -30%'
      },
      {
        title: 'H3- Titre',
        h5Title: 'H5- Titre',
        h4Title: 'H4- Titre',
        h6Title: 'H6- Titre',
        image: 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461',
        rating: '9.4',
        reviewCount: '164',
        price: '170€ / nuits',
        discount: '-15% à -30%'
      },
      {
        title: 'H3- Titre',
        h5Title: 'H5- Titre',
        h4Title: 'H4- Titre',
        h6Title: 'H6- Titre',
        image: 'https://images.unsplash.com/photo-1596394516093-501ba68a0ba6',
        rating: '9.4',
        reviewCount: '164',
        price: '170€ / nuits',
        discount: '-15% à -30%'
      },
      {
        title: 'H3- Titre',
        h5Title: 'H5- Titre',
        h4Title: 'H4- Titre',
        h6Title: 'H6- Titre',
        image: 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa',
        rating: '9.4',
        reviewCount: '164',
        price: '170€ / nuits',
        discount: '-15% à -30%'
      }
    ]
  }
});

// Responsive handling
const visibleSlides = computed(() => {
  // This would ideally be reactive to screen size
  return 4; // For larger screens
});

const currentSlide = ref(0);
const maxSlide = computed(() => props.rooms.length - visibleSlides.value);

function nextSlide() {
  if (currentSlide.value < maxSlide.value) {
    currentSlide.value++;
  } else {
    currentSlide.value = 0; // Loop back to start
  }
}

function prevSlide() {
  if (currentSlide.value > 0) {
    currentSlide.value--;
  } else {
    currentSlide.value = maxSlide.value; // Loop to end
  }
}

// Auto-advance slides
let autoplayInterval = null;

onMounted(() => {
  autoplayInterval = setInterval(() => {
    nextSlide();
  }, 5000);
});

// Clear interval on component destroy
onUnmounted(() => {
  if (autoplayInterval) {
    clearInterval(autoplayInterval);
  }
});
</script>

<style scoped>
.room-card {
  transition: transform 0.3s ease;
  height: 400px;
  display: flex;
  flex-direction: column;
}

.room-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.discount-badge {
  background-color: #EA672D;
  border-radius: 4px;
}
</style> 