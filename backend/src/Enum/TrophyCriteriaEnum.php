<?php

namespace App\Enum;

enum TrophyCriteriaEnum: string
{
    case TOTAL_BOOKINGS = 'total_bookings';
    case SUCCESSFUL_NEGOTIATIONS = 'successful_negotiations';
    case DIFFERENT_COUNTRIES = 'different_countries';
    case DIFFERENT_HOTELS = 'different_hotels';
    case LOYALTY_DAYS = 'loyalty_days';
    case EARLY_BOOKER = 'early_booker';
    case LAST_MINUTE = 'last_minute';
    case FIRST_BOOKING = 'first_booking';
    case NIGHT_OWL = 'night_owl';
    case HIGH_SPENDER = 'high_spender';
}