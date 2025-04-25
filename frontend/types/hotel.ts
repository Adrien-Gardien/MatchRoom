import type { Image } from "./image"

export type Hotel = {
    id: number
    name: string
    description: string
    address: string
    city: string
    zipCode: number
    country: string
    images: Image[]
}