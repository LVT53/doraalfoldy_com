<?php

namespace App\Filament\Resources\ReferencePhotoResource\Pages;

use App\Filament\Resources\ReferencePhotoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReferencePhoto extends EditRecord
{
    protected static string $resource = ReferencePhotoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
