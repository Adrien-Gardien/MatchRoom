<template>
  <div class="bg-white rounded-2xl p-0 shadow-lg overflow-hidden transform transition-all duration-300 hover:shadow-xl hover:-translate-y-2 cursor-pointer" @click="navigateToHotel">
    <div class="relative">
      <div
        class="w-full h-48 bg-hawkes-blue bg-cover bg-center"
        :style="{ backgroundImage: `url('/hotels/${imageSrc}')` }"
      ></div>
      <div 
        class="absolute top-4 right-4 text-xs font-semibold px-3 py-1 rounded-full"
        :class="tagClass"
      >
        {{ tag }}
      </div>
    </div>
    <div class="p-6">
      <h4 class="text-xl font-bold mb-3 text-everglade">{{ title }}</h4>
      <p class="text-quincy-light text-sm mb-4">
        {{ description }}
      </p>
      <div class="flex justify-between items-center">
        <div class="flex items-center space-x-1">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#EA672D" class="w-4 h-4">
            <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
          </svg>
          <span class="text-sm font-medium text-quincy">{{ rating }}</span>
        </div>
        <a 
          :href="link" 
          class="text-sm font-semibold text-hawkes-blue-light bg-everglade px-4 py-2 rounded-lg hover:bg-everglade-light transition-colors"
        >
          {{ buttonText }}
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

const props = defineProps({
  id: {
    type: [String, Number],
    required: true
  },
  imageSrc: {
    type: String,
    required: true
  },
  tag: {
    type: String,
    required: true
  },
  tagType: {
    type: String,
    default: 'default',
    validator: (value) => ['premium', 'unique', 'longue-duree', 'exclusif', 'bien-etre', 'urbain', 'default'].includes(value)
  },
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    required: true
  },
  rating: {
    type: String,
    default: '4.7'
  },
  buttonText: {
    type: String,
    default: 'Explorer'
  },
  link: {
    type: String,
    default: '#'
  }
});

const navigateToHotel = () => {
  router.push(`/hotel/${props.id}`);
};

const tagClass = computed(() => {
  switch (props.tagType) {
    case 'premium':
    case 'exclusif':
      return 'bg-burning-orange text-white';
    case 'unique':
    case 'bien-etre':
      return 'bg-hawkes-blue text-everglade';
    case 'longue-duree':
    case 'urbain':
    default:
      return 'bg-colonial-white-dark text-quincy';
  }
});
</script> 