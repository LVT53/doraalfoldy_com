<?php

namespace App\Filament\Resources\ScheduleExceptionResource\Pages;

use App\Filament\Resources\ScheduleExceptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditScheduleException extends EditRecord
{
    protected static string $resource = ScheduleExceptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
