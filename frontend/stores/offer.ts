import type { Offer } from '../types/offer'

export const useOfferStore = defineStore('offer', {
  state: (): { offers: Offer[]; userOffer: Offer | null; childrenOffers: Offer[] } => ({
    offers: [],
    userOffer: null,
    childrenOffers: [], // ✅ tableau ici
  }),

  actions: {
    async fetchUserOffer({ roomId, userId }: { roomId: string; userId: string }) {
      try {
        const { data, error } = await useFetch(`/api/offer?roomId=${roomId}&userId=${userId}`);
        if (!error.value && data.value) {
          this.userOffer = data.value;
        }
      } catch (err) {
        console.error('Error fetching user offer:', err);
      }
    },

    async getChildrenOffers({ offerId }: { offerId: number }) {
      try {
        const { data, error } = await useFetch(`/api/offer/${offerId}/children`);
        if (!error.value && data.value) {
          this.childrenOffers = [data.value];
        } else {
          this.childrenOffers = [];
        }
      } catch (err) {
        console.error('Error fetching children offers:', err);
        this.childrenOffers = [];
      }
    }
  }
});
