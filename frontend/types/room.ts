import type { Hotel } from "./hotel"
import type { Image } from "./image"

export type Room = {
    id: number
    name: string
    capacity: string
    description: string
    price: number
    images: Image[]
    hotel: Hotel
    available: boolean
}