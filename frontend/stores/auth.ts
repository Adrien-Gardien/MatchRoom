import { defineStore } from 'pinia';

type User = {
    id: number;
    name: string;
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

        async logout() {
            this.isLoading = true;

            try {
                const { $api } = useNuxtApp();

                await useFetch($api('/api/logout'), {
                    method: 'POST',
                    credentials: 'include',
                });

                this.user = null;
                this.isAuthenticated = false;
            } catch (error) {
                console.error(error);
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

        async register(fullName: string, email: string, password: string) {
            try {
                const { $api } = useNuxtApp();
                const { error } = await useFetch($api('/api/register'), {
                    method: 'POST',
                    body: {
                        fullName,
                        email,
                        password
                    },
                    credentials: 'include'
                });

                if (!error.value) {
                    return { success: true }
                }


            } catch (error) {
                console.log(error)
            }
        }
    },

    persist: true,
});

