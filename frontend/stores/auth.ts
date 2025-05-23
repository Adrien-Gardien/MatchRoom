import { defineStore } from 'pinia';

type User = {
    id: number;
    firstName: string;
    lastName: string;
    email: string;
    roles: string[];
};

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null as User | null,
        isAuthenticated: false,
        isLoading: false,
    }),

    actions: {
        async fetchUser() {
            this.isLoading = true;

            try {
                const { $api } = useNuxtApp();

                const { data, error } = await useAuthFetch<User>($api('/api/me'), {
                    method: 'GET',
                    credentials: 'include',
                });

                if (data.value && !error.value) {
                    this.user = data.value;
                    this.isAuthenticated = true;
                } else {
                    this.user = null;
                    this.isAuthenticated = false;
                }
            } catch (error) {
                this.user = null;
                this.isAuthenticated = false;
                console.error('Error fetching user:', error);
            } finally {
                this.isLoading = false;
            }
        },

        async login(email: string, password: string) {
            this.isLoading = true;

            try {
                const { $api } = useNuxtApp();

                const { error } = await useFetch($api('/api/login'), {
                    method: 'POST',
                    body: { username: email, password },
                    credentials: 'include',
                });

                if (!error.value) {
                    await this.fetchUser();
                    return { success: true };
                }

                return {
                    success: false,
                    error: error.value?.message || 'Login failed',
                };
            } catch (error) {
                console.error('Login error:', error);
                return { success: false, error: 'Authentication failed' };
            } finally {
                this.isLoading = false;
            }
        },

        async register(payload) {
            try {
              await $fetch('/api/register', {
                method: 'POST',
                body: payload,
              })

              return { success: true }
            } catch (err) {
              return {
                success: false,
                errors: err.data?.message || 'Registration failed',
              }
            }
          },
        
        async verifyEmail(token) {
            try {
              const response = await $fetch('api/verify/email', {
                method: 'POST',
                body: { token },
                credentials: 'include',
              })

              if (response.success) {
                return { success: true }
              } else {
                return {
                  success: false,
                  errors: response.message || 'Email verification failed',
                }
              }
            } catch (err) {
              const errorMessage = err?.data?.message || err.message || 'Request failed'
              return {
                success: false,
                errors: errorMessage,
              }
            }
          },

        async logout() {
            this.isLoading = true;

            try {
                const { $api } = useNuxtApp();

                await useFetch($api('/api/logout'), {
                    method: 'POST',
                    credentials: 'include',
                });
                
                this.user = null;
            } catch (error) {
                console.error('Erreur lors de la déconnexion:', error);
                throw error;
            } finally {
                this.isLoading = false;
            }
        },
        
        async refreshToken(): Promise<boolean> {
            try {
                const { $api } = useNuxtApp();
                const { error } = await useFetch($api('/api/token/refresh'), {
                    method: 'POST',
                    credentials: 'include',
                });

                return !error.value;
            } catch {
                return false;
            }
        },
    },

    persist: true,
});

