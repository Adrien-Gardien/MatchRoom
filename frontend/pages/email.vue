<template>
  <main class="w-full h-full flex justify-center pt-40">
    <UGridBackgroundPattern class="absolute top-0" />
    <section class="flex flex-col items-center space-y-8 relative">
      <div class="text-center space-y-3">
        <h1 class="text-primary font-semibold text-3xl">Vérification de l'Email</h1>
        <p class="text-tertiary font-normal">Entrez le code de vérification envoyé à votre email</p>
      </div>

      <form class="w-96" @submit.prevent="submitForm">
        <div class="space-y-5">
          <UInput
            v-model="token"
            type="text"
            name="token"
            placeholder="Ex: 123456"
            label="Code de vérification"
            required
          />
          <p v-if="flashMessage" :class="flashMessage.type" class="text-sm">{{ flashMessage.message }}</p>
        </div>

        <UButton class="mt-6 w-full justify-center" type="submit">Vérifier</UButton>
      </form>
    </section>
  </main>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '~/stores/auth'
import { useRouter } from 'vue-router'

const token = ref('')
const flashMessage = ref(null)

const authStore = useAuthStore()
const router = useRouter()

const submitForm = async () => {
  const result = await authStore.verifyEmail(token.value)
  if (result.success) {
    router.push('/login')
  } else {
    // Si erreur, afficher un message flash
    flashMessage.value = { type: 'error', message: result.errors }
  }
}
</script>
