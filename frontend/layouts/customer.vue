<template>
    <NuxtLayout name="default">
        <div class="h-full w-full flex flex-col">
            <UHeader />
            <slot />
            <UFooter />
        </div>
    </NuxtLayout>
</template>
