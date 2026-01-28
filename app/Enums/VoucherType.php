<?php

namespace App\Enums;

enum VoucherType: string
{
    case PERCENTAGE = 'percentage';  // Single-use: value = percentage (10 = 10%)
    case FIXED = 'fixed';            // Single-use: value = fixed HUF amount
    case GIFT_CARD = 'gift_card';    // Multi-use: tracks balance
}
