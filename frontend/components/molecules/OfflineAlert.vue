<template>
  <div v-if="!isOnline" class="fixed top-0 left-0 right-0 bg-red-500 text-white p-2 text-center z-50">
    <p>Vous êtes actuellement hors ligne. Certaines fonctionnalités pourraient ne pas être disponibles.</p>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const { $isOnline } = useNuxtApp();
const isOnline = ref($isOnline());

onMounted(() => {
  window.addEventListener('online', updateOnlineStatus);
  window.addEventListener('offline', updateOnlineStatus);
});

onUnmounted(() => {
  window.removeEventListener('online', updateOnlineStatus);
  window.removeEventListener('offline', updateOnlineStatus);
});

function updateOnlineStatus() {
  isOnline.value = $isOnline();
}
</script>