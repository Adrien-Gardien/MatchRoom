<script setup lang="ts">
    import type { Negotiation } from '~/types/negotiation';
    import { ref, onMounted } from 'vue';
    
    const { $api } = useNuxtApp();
    const { error, success } = useToast();
    const negotiations = ref<Negotiation[]>([]);
    const loading = ref(true);
    const showCounterOfferModal = ref(false);
    const selectedNegotiation = ref<Negotiation | null>(null);
    const counterPrice = ref<number>();

    // Fetch negotiations for the hotel owner
    const fetchNegotiations = async () => {
        try {
            loading.value = true;
            const response = await useAuthFetch<{ negotiations: Negotiation[] }>($api('/api/negotiations/hotel-owner'));
            
            if (response.error.value) {
                throw new Error("Erreur lors du chargement des négociations");
            }
            
            negotiations.value = response.data.value?.negotiations || [];
        } catch (err) {
            console.error(err);
            error('Erreur', 'Impossible de charger les négociations');
        } finally {
            loading.value = false;
        }
    };

    // Format price from cents to euros
    const formatPrice = (priceInCents: number): string => {
        return (priceInCents / 100).toFixed(2) + ' €';
    };

    // Format date
    const formatDate = (dateString: string): string => {
        const date = new Date(dateString);
        return date.toLocaleDateString('fr-FR', {
            day: 'numeric',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    // Get status label in French
    const getStatusLabel = (status: string): string => {
        switch (status) {
            case 'pending':
                return 'En attente';
            case 'accepted':
                return 'Acceptée';
            case 'refused':
                return 'Refusée';
            case 'counter_offer':
                return 'Contre-offre';
            default:
                return status;
        }
    };

    // Get status color
    const getStatusColor = (status: string): 'warning' | 'success' | 'error' | 'brand' => {
        switch (status) {
            case 'pending':
                return 'warning';
            case 'accepted':
                return 'success';
            case 'refused':
                return 'error';
            case 'counter_offer':
                return 'brand';
            default:
                return 'brand';
        }
    };

    // Show counter offer modal for a specific negotiation
    const showCounterOffer = (negotiation: Negotiation) => {
        selectedNegotiation.value = negotiation;
        // Initialize the counter price with the original room price - 5%
        counterPrice.value = negotiation.roomId.price * 0.95 / 100;
        showCounterOfferModal.value = true;
    };

    // Accept a negotiation
    const acceptNegotiation = async (id: number) => {
        try {
            const response = await useAuthFetch($api(`/api/negotiations/${id}`), {
                method: 'PATCH',
                body: {
                    status: 'accepted'
                }
            });

            if (response.error.value) {
                throw new Error("Erreur lors de l'acceptation de la négociation");
            }

            success('Succès', 'Négociation acceptée avec succès');
            await fetchNegotiations();
        } catch (err) {
            console.error(err);
            error('Erreur', "Impossible d'accepter la négociation");
        }
    };

    // Refuse a negotiation
    const refuseNegotiation = async (id: number) => {
        try {
            const response = await useAuthFetch($api(`/api/negotiations/${id}`), {
                method: 'PATCH',
                body: {
                    status: 'refused'
                }
            });

            if (response.error.value) {
                throw new Error("Erreur lors du refus de la négociation");
            }

            success('Succès', 'Négociation refusée');
            await fetchNegotiations();
        } catch (err) {
            console.error(err);
            error('Erreur', "Impossible de refuser la négociation");
        }
    };

    // Submit a counter offer
    const submitCounterOffer = async () => {
        if (!selectedNegotiation.value || !counterPrice.value) return;
        
        try {
            // Convert from euros to cents for the API
            const counterPriceInCents = Math.round(counterPrice.value * 100);
            
            const response = await useAuthFetch($api(`/api/negotiations/${selectedNegotiation.value.id}`), {
                method: 'PATCH',
                body: {
                    status: 'counter_offer',
                    counterPrice: counterPriceInCents
                }
            });

            if (response.error.value) {
                throw new Error("Erreur lors de l'envoi de la contre-offre");
            }

            success('Succès', 'Contre-offre envoyée avec succès');
            showCounterOfferModal.value = false;
            await fetchNegotiations();
        } catch (err) {
            console.error(err);
            error('Erreur', "Impossible d'envoyer la contre-offre");
        }
    };

    onMounted(fetchNegotiations);
</script>

<template>
    <NuxtLayout name="hub">
        <div class="p-6 w-full">
            <h1 class="text-2xl font-semibold mb-6">Gestion des négociations</h1>
            
            <div v-if="loading" class="flex justify-center items-center h-64">
                <ULoading />
            </div>
            
            <div v-else-if="negotiations.length === 0" class="bg-white rounded-lg p-8 text-center">
                <p class="text-gray-500">Aucune négociation en cours</p>
            </div>
            
            <div v-else class="bg-white rounded-lg p-6">
                <table class="w-full">
                    <thead>
                        <tr class="text-left border-b border-gray-200">
                            <th class="pb-3">Chambre</th>
                            <th class="pb-3">Hôtel</th>
                            <th class="pb-3">Prix demandé</th>
                            <th class="pb-3">Prix original</th>
                            <th class="pb-3">Date</th>
                            <th class="pb-3">Statut</th>
                            <th class="pb-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="neg in negotiations" :key="neg.id" class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3">{{ neg.roomId.name }}</td>
                            <td class="py-3">{{ neg.roomId.hotel.name || 'N/A' }}</td>
                            <td class="py-3 font-semibold">{{ formatPrice(neg.proposedPrice) }}</td>
                            <td class="py-3">{{ formatPrice(neg.roomId.price) }}</td>
                            <td class="py-3">{{ formatDate(neg.updatedAt) }}</td>
                            <td class="py-3">
                                <UBadge :color="getStatusColor(neg.status)">
                                    {{ getStatusLabel(neg.status) }}
                                </UBadge>
                            </td>
                            <td class="py-3">
                                <div class="flex space-x-2">
                                    <UButton
                                        v-if="neg.status === 'pending'"
                                        size="sm"
                                        color="success"
                                        @click="acceptNegotiation(neg.id)"
                                    >
                                        Accepter
                                    </UButton>
                                    <UButton
                                        v-if="neg.status === 'pending'"
                                        size="sm"
                                        color="danger"
                                        @click="refuseNegotiation(neg.id)"
                                    >
                                        Refuser
                                    </UButton>
                                    <UButton
                                        v-if="neg.status === 'pending'"
                                        size="sm"
                                        @click="showCounterOffer(neg)"
                                    >
                                        Contre-offre
                                    </UButton>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Modal pour la contre-offre -->
            <UModal v-model="showCounterOfferModal">
                <div class="p-4">
                    <h3 class="text-xl font-semibold mb-4">Faire une contre-offre</h3>
                    <p class="mb-2">
                        Chambre : {{ selectedNegotiation?.roomId.name }}
                    </p>
                    <p class="mb-4">
                        Prix proposé par le client : {{ selectedNegotiation?.proposedPrice ? formatPrice(selectedNegotiation.proposedPrice) : '' }}
                    </p>
                    <p class="mb-4">
                        Prix original : {{ selectedNegotiation?.roomId.price ? formatPrice(selectedNegotiation.roomId.price) : '' }}
                    </p>
                    
                    <div class="mb-6">
                        <label class="block mb-2 font-medium">Votre contre-offre (en €)</label>
                        <UInput
                            v-model="counterPrice"
                            type="number"
                            step="0.01"
                            min="0"
                            :max="selectedNegotiation?.roomId.price ? selectedNegotiation.roomId.price / 100 : 0"
                            placeholder="Entrez votre contre-offre"
                        />
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <UButton variant="secondary" @click="showCounterOfferModal = false">
                            Annuler
                        </UButton>
                        <UButton
                            :disabled="!counterPrice"
                            @click="submitCounterOffer"
                        >
                            Envoyer la contre-offre
                        </UButton>
                    </div>
                </div>
            </UModal>
        </div>
    </NuxtLayout>
</template>