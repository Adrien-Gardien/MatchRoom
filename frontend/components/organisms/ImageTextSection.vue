<template>
  <div class="relative py-20 w-full">
    <div class="absolute left-0 top-0 w-full h-full "></div>

    <div class="relative z-10  ">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center w-full">
        <!-- Image -->
        <div 
          :class="[
            'overflow-hidden h-[250px] md:h-[300px] lg:h-[400px]',
            imagePosition === 'left' ? 'order-1 md:order-1 rounded-r-full rounded-l-none' : 'order-1 md:order-2 rounded-l-full rounded-r-none'
          ]"
        >
          <slot name="image">
            <img
              :src="imageSrc"
              :alt="imageAlt"
              class="w-full h-full object-cover"
            />
          </slot>
        </div>

        <!-- Texte et CTA -->
        <div 
          :class="[
            imagePosition === 'left' ? 'order-2 md:order-2 md:pl-10 pr-32' : 'order-2 md:order-1 md:pr-10 pl-32'
          ]"
        >
          <h3 class="text-2xl sm:text-3xl font-bold mb-6 text-everglade">
            {{ title }}
          </h3>

          <p class="text-quincy mb-8">
            {{ description }}
          </p>

          <button 
            v-if="buttonText" 
            class="px-6 py-3 bg-burning-orange hover:bg-burning-orange-light text-white rounded-lg shadow-lg transition transform hover:scale-105"
            @click="$emit('buttonClick')"
          >
            {{ buttonText }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    required: true
  },
  buttonText: {
    type: String,
    default: ''
  },
  imageSrc: {
    type: String,
    required: true
  },
  imageAlt: {
    type: String,
    default: 'Image illustration'
  },
  imagePosition: {
    type: String,
    default: 'right',
    validator: (value) => ['left', 'right'].includes(value)
  }
});

defineEmits(['buttonClick']);
</script> 