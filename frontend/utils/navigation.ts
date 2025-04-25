import BuildingIcon from "~/components/atoms/icons/BuildingIcon.vue";
import ChartIcon from "~/components/atoms/icons/ChartIcon.vue";
import CoinsHandIcon from "~/components/atoms/icons/CoinsHandIcon.vue";
import DiamondIcon from "~/components/atoms/icons/DiamondIcon.vue";
import HomeIcon from "~/components/atoms/icons/HomeIcon.vue";
import TicketIcon from "~/components/atoms/icons/TicketIcon.vue";
import UserIcon from "~/components/atoms/icons/UserIcon.vue";
import MessageIcon from '~/components/atoms/icons/MessageIcon.vue';

type Link = {
    label: string
    link: string
    icon?: Component
}

export const links: Link[] = [
    {
        label: 'Accueil',
        link: '/',
        icon: HomeIcon
    },
    {
        label: 'Dashboard',
        link: '/hub/dashboard',
        icon: ChartIcon
    },
    {
        label: 'Hotels',
        link: '/hub/hotels',
        icon: BuildingIcon
    },
    {
        label: 'Chambres',
        link: '/hub/rooms',
        icon: DiamondIcon
    },
    {
        label: 'Réservation',
        link: '/hub/reservations',
        icon: TicketIcon
    },
    {
        label: 'Négociations',
        link: '/hub/negotiations',
        icon: MessageIcon
    },
    {
        label: 'Offres',
        link: '/hub/offers',
        icon: CoinsHandIcon
    },
    {
        label: 'Utilisateurs',
        link: '/hub/users',
        icon: UserIcon
    }
]

export default links;