<script setup lang="ts">
    import type { Room } from '~/types/room';

    interface BookModalProps {
        modelValue: boolean;
        rooms: Room[];
    }

    const _props = defineProps<BookModalProps>();
    const selectedRoom = ref();
    const offer = ref<number>();
    const { $api } = useNuxtApp();
    const { error, success } = useToast();

    const _emits = defineEmits<{
        'update:modelValue': [value: boolean];
    }>();

    const formattedRooms = computed(() => {
        return _props.rooms.map((room) => ({
            label: room.name + ' - ' + room.capacity, // Assuming 'name' is the property you want to show in the SelectBox
            value: room.id, // Assuming 'id' is the unique identifier for each room
        }));
    });

    const getSelectedRoom = computed(() => {
        return _props.rooms.find((value) => {
            if (selectedRoom.value === value.id) return value;
        });
    });

    const invalidOffer = computed<boolean>(() => {
        if (!getSelectedRoom.value || !offer.value) return false;
        const originalPrice = getSelectedRoom.value.price / 100;
        const maxDiscount = originalPrice * 0.7; // 30% discount means price should be at least 70% of original
        return offer.value < maxDiscount;
    });

    const formReady = computed(() => {
        return offer.value && !invalidOffer.value;
    });

    const handleForm = async () => {
        try {
            const response = await useAuthFetch($api('/api/negotiations'), {
                method: 'POST',
                body: {
                    roomId: getSelectedRoom.value?.id,
                    proposedPrice: offer.value! * 100,
                },
            });

            if (response.error.value) {
                console.log(error)
                error('Une erreur est survenue lors de la création de l\'offre', 'Veuillez réessayer')
            } else {
                success('Cool bien joué !', 'Votre offre a été envoyé !');
            }

        } catch (error) {
            console.error(error);
            throw error;
        }
    };
</script>

<template>
    <UBaseModal :is-open="modelValue" @close="$emit('update:modelValue', false)">
        <form class="pt-6" @submit.prevent="handleForm">
            <div class="space-y-6 px-6">
                <h2 class="font-semibold text-primary text-2xl">Faire une offre</h2>
                <USelectBox
                    v-model="selectedRoom"
                    placeholder="Choisissez une chambre"
                    :options="formattedRooms"
                    :search-input="true"
                />
            </div>

            <div v-if="getSelectedRoom" class="mt-6 px-6">
                <h3 class="text-xl font-semibold text-primary">Offre</h3>
                <p v-if="getSelectedRoom">Prix original : {{ getSelectedRoom.price / 100 || ' -' }} €</p>
                <div class="flex items-center">
                    <UInput
                        v-model="offer"
                        :destructive="invalidOffer"
                        class="mt-4 w-full"
                        type="number"
                        placeholder="Votre offre"
                        hint-text="En €"
                    />
                </div>
            </div>
            <div class="pt-8">
                <div class="flex items-center justify-between gap-3 px-6 pb-6">
                    <UButton variant="secondary" class="w-full" @click="$emit('update:modelValue', false)"
                        >Annuler</UButton
                    >
                    <UButton class="w-full" type="submit" :disabled="!formReady">Confirmer</UButton>
                </div>
            </div>
        </form>
    </UBaseModal>
</template>

