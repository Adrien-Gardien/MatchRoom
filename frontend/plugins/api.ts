export default defineNuxtPlugin(() => {
    const config = useRuntimeConfig();

    return {
        provide: {
            api: (path: string) => config.public.apiUrl + path,
        },
    };
});
