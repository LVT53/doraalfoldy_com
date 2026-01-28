<?php

namespace App\Enums;

enum BookingTokenType: string
{
    case CANCEL = 'cancel';
    case RESCHEDULE = 'reschedule';
    case REVIEW = 'review';
}
