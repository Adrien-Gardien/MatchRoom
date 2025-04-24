export default defineNuxtRouteMiddleware(async (to) => {
    const authStore = useAuthStore();

    if (authStore.isAuthenticated && !authStore.user) {
        await authStore.fetchUser();
    }

    if (!authStore.isAuthenticated && !['/login', '/register', '/verify/email'].includes(to.path)) {
      return navigateTo('/login');
    }

    if (authStore.isAuthenticated && ['/login', '/register', '/verify/email'].includes(to.path)) {
      return navigateTo('/');
    }
});
