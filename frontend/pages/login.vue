<script setup>
    definePageMeta({
        middleware: 'auth',
    });

    const authStore = useAuthStore();
    const router = useRouter();

    const fullName = ref('');
    const email = ref('');
    const password = ref('');

    const { success, error } = useToast();

    const login = async () => {
        const result = await authStore.login(email.value, password.value);

        if (result.success) {
            success('Connexion réussie', 'Redirection en cours...');
            router.push('/');
        } else {
            error('Connexion échouée', 'Vérifiez vos informations.');
        }
    };

    const register = async () => {
        const result = await authStore.register(fullName.value, email.value, password.value);

        if (result.success) {
            success('Inscription réussie', 'Vous pouvez maintenant vous connecter.');
        } else {
            error('Inscription échouée', 'Veuillez vérifier vos informations.');
        }
    };
</script>

<template>
    <NuxtLayout name="customer">
        <main class="w-full h-full flex justify-center pt-40">
            <UGridBackgroundPattern class="absolute top-0" />
            <section class="flex flex-col items-center space-y-8 relative">
                <div class="text-center space-y-3">
                    <h1 class="text-primary font-semibold text-3xl">Bon retour</h1>
                    <p class="text-tertiary font-normal">Bon retour ! Veuillez saisir vos informations</p>
                </div>
                <UTabs
                    full-width
                    variant="border"
                    :items="[
                        { name: 'register', label: 'S\'inscrire' },
                        { name: 'login', label: 'Se connecter' },
                    ]"
                >
                    <template #login>
                        <form class="w-96" @submit.prevent="login">
                            <div class="space-y-5">
                                <UInput
                                    v-model="email"
                                    type="email"
                                    name="email"
                                    placeholder="Email"
                                    label="Email"
                                    required
                                />
                                <UInput
                                    v-model="password"
                                    type="password"
                                    name="password"
                                    placeholder="••••••••"
                                    label="Mot de passe"
                                    required
                                />
                            </div>
                            <UButton class="mt-6 w-full justify-center" type="submit">Se connecter</UButton>
                        </form>
                    </template>
                    <template #register>
                        <form class="w-96" @submit.prevent="register">
                            <div class="space-y-5">
                                <UInput
                                    v-model="fullName"
                                    type="text"
                                    name="fullName"
                                    placeholder="Nom complet"
                                    label="Nom"
                                    required
                                />
                                <UInput
                                    v-model="email"
                                    type="email"
                                    name="email"
                                    placeholder="Email"
                                    label="Email"
                                    required
                                />
                                <UInput
                                    v-model="password"
                                    type="password"
                                    name="password"
                                    placeholder="••••••••"
                                    label="Mot de passe"
                                    required
                                />
                            </div>
                            <UButton class="mt-6 w-full justify-center" type="submit">S'inscrire</UButton>
                        </form>
                    </template>
                </UTabs>
            </section>
        </main>
    </NuxtLayout>
</template>

