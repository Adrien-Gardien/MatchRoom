<?php

namespace App\Enum;

enum NegotiationStatusEnum: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REFUSED = 'refused';
    case COUNTER_OFFER = 'counter_offer';
}