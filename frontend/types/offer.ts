import type { Room } from './room'

export type Offer = {
    id: number
    proposedPrice: number
    status: string
    offerDate: string
    userId: number
    room: Room
    parentOffer: Offer | null
}