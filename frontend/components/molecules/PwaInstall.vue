<template>
  <div v-if="canInstall" class="fixed bottom-4 right-4 z-50">
    <button
      class="flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-white shadow-lg hover:bg-primary-dark transition-colors"
      @click="installPwa"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
      </svg>
      Installer l'application
    </button>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const deferredPrompt = ref(null);
const canInstall = ref(false);

const handleBeforeInstallPrompt = (e) => {
  // Prevent the mini-infobar from appearing on mobile
  e.preventDefault();
  // Store the event so it can be triggered later
  deferredPrompt.value = e;
  // Show the install button
  canInstall.value = true;
};

const installPwa = async () => {
  if (!deferredPrompt.value) {
    return;
  }
  
  // Show the install prompt
  deferredPrompt.value.prompt();
  
  // Wait for the user to respond to the prompt
  const { outcome } = await deferredPrompt.value.userChoice;
  
  // We no longer need the prompt. Clear it
  deferredPrompt.value = null;
  
  // Hide the install button
  canInstall.value = false;
};

// Add event listener for beforeinstallprompt event
onMounted(() => {
  window.addEventListener('beforeinstallprompt', handleBeforeInstallPrompt);
});

// Clean up event listener
onUnmounted(() => {
  window.removeEventListener('beforeinstallprompt', handleBeforeInstallPrompt);
});
</script>