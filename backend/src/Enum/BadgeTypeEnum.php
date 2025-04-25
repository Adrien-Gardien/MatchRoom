<?php

namespace App\Enum;

enum BadgeTypeEnum: string
{
    case BOOKING = 'booking';
    case NEGOTIATION = 'negotation';
    case LOYALTY = 'loyalty';
}