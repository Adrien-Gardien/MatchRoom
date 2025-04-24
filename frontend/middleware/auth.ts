export default defineNuxtRouteMiddleware(async (to) => {
  const authStore = useAuthStore();

  if (!authStore.user && authStore.isAuthenticated) {
    await authStore.fetchUser();
  }

  const publicPages = ['/login', '/register', '/verify/email'];

  const isPublicPage = publicPages.includes(to.path);

  if (!authStore.isAuthenticated && !isPublicPage) {
    return navigateTo('/login');
  }

  if (authStore.isAuthenticated && isPublicPage) {
    return navigateTo('/');
  }
});
