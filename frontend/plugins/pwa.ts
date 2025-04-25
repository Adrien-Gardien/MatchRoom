import { ref } from 'vue';

export default defineNuxtPlugin({
  name: 'pwa-plugin',
  setup() {
    const online = ref(true);

    if (import.meta.client) {
      online.value = navigator.onLine;

      window.addEventListener('online', () => {
        online.value = true;
      });

      window.addEventListener('offline', () => {
        online.value = false;
      });
    }

    return {
      provide: {
        isOnline: () => online.value
      }
    };
  }
});