<?php

namespace App\Filament\Resources\ReferencePhotoResource\Pages;

use App\Filament\Resources\ReferencePhotoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReferencePhotos extends ListRecords
{
    protected static string $resource = ReferencePhotoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
