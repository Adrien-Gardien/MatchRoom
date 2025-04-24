<template>
    <main class="w-full h-full flex justify-center pt-40">
      <UGridBackgroundPattern class="absolute top-0" />
      <section class="flex flex-col items-center space-y-8 relative">
        <div class="text-center space-y-3">
          <h1 class="text-primary font-semibold text-3xl">Create your account</h1>
          <p class="text-tertiary font-normal">Please fill in your information to register</p>
        </div>
  
        <form class="w-96" @submit.prevent="register">
          <div class="space-y-5">
            <UInput v-model="firstName" type="text" name="firstName" placeholder="First name" label="First name" required />
            <UInput v-model="lastName" type="text" name="lastName" placeholder="Last name" label="Last name" required />
            <UInput v-model="email" type="email" name="email" placeholder="Email" label="Email" required />
            <UInput
              v-model="password"
              type="password"
              name="password"
              placeholder="••••••••"
              label="Password"
              required
            />
            <UInput
              v-model="confirmPassword"
              type="password"
              name="confirmPassword"
              placeholder="••••••••"
              label="Confirm Password"
              required
            />
            <p v-if="error" class="text-red-500 text-sm">{{ error }}</p>
          </div>
  
          <UButton class="mt-6 w-full justify-center" type="submit">Register</UButton>
        </form>
      </section>
    </main>
  </template>
  
  <script setup>
  definePageMeta({
    middleware: 'auth',
  })
  
  const authStore = useAuthStore()
  const router = useRouter()
  
  const firstName = ref('')
  const lastName = ref('')
  const email = ref('')
  const password = ref('')
  const confirmPassword = ref('')
  const error = ref('')
  
  const register = async () => {
    error.value = ''
  
    if (password.value !== confirmPassword.value) {
      error.value = 'Passwords do not match.'
      return
    }
  
    const result = await authStore.register({
      first_name: firstName.value,
      last_name: lastName.value,
      email: email.value,
      password: password.value,
      password_confirmation: confirmPassword.value,
    })
  
    if (result.success) {
      router.push('/login')
    } else {
      error.value = result.errors || 'Registration failed.'
    }
  }
  </script>
  