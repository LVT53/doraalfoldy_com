<?php

namespace App\Filament\Resources\ScheduleExceptionResource\Pages;

use App\Filament\Resources\ScheduleExceptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListScheduleExceptions extends ListRecords
{
    protected static string $resource = ScheduleExceptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
