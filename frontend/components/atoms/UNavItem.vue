<script setup lang="ts">
    interface NavItemProps {
        icon?: Component | null;
        to?: string;
        disabled?: boolean;
    }

    const props = withDefaults(defineProps<NavItemProps>(), {
        icon: null,
        to: '',
        disabled: false,
    });

    const route = useRoute();
    const current = computed(() => {
        return props.to === route.path
    });

    function handleClick() {
        if (props.disabled || props.to === '') return;

        navigateTo(props.to);
    }
</script>

<template>
    <button
        :class="[
            'px-3 py-2.5 w-full rounded-md bg-primary flex items-center gap-3 text-secondary hover:bg-primary-hover hover:text-secondary-hover font-semibold',
            { '!bg-active !text-secondary-hover': current },
        ]"
        @click="handleClick"
    >
        <component :is="icon" v-if="icon" class="text-fg-quaternary" />
        <slot />
    </button>
</template>

