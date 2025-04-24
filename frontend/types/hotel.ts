import type { Room } from "./room"

export type Hotel = {
    id: number
    name: string
    description: string
    city: string
    country: string
    image: string
    rooms: Room[]
    favorites: unknown
    owners: unknown
}