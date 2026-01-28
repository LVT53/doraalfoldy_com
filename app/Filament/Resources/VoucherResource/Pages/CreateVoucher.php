<?php

namespace App\Filament\Resources\VoucherResource\Pages;

use App\Enums\VoucherType;
use App\Filament\Resources\VoucherResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVoucher extends CreateRecord
{
    protected static string $resource = VoucherResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($data['type'] === VoucherType::GIFT_CARD->value) {
            if (empty($data['balance'])) {
                $data['balance'] = $data['value'];
            }
        } else {
            $data['balance'] = 0;
        }

        return $data;
    }
}
