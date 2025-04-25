import type { Room } from './room';

export type NegotiationStatus = 'pending' | 'accepted' | 'refused' | 'counter_offer';

export interface Negotiation {
  id: number;
  proposedPrice: number;
  status: NegotiationStatus;
  createdAt: string;
  updatedAt: string;
  roomId: Room;
}