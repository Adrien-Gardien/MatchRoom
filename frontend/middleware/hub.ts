export default defineNuxtRouteMiddleware((_to, _from) => {
    const { user } = useAuthStore();

    if (!user || !user.roles.includes('ROLE_ADMIN')) {
        throw createError({
            statusCode: 401,
            statusMessage: 'Unauthorized',
        });
    }
})