<?php

namespace App\Filament\Resources\EmployeeProfileResource\Pages;

use App\Filament\Resources\EmployeeProfileResource;
use App\Models\EmployeeProfile;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeProfile extends EditRecord
{
    protected static string $resource = EmployeeProfileResource::class;

    protected static ?string $title = 'Munkatárs profil szerkesztése';

    public function mount(int|string|null $record = null): void
    {
        $profile = EmployeeProfile::firstOrCreate(
            [],
            [
                'name' => 'Dóra Alfoldy',
                'bio' => '',
                'image_path' => null,
                'instagram_url' => null,
            ]
        );

        $this->record = $profile;
        $this->fillForm();
    }

    public function getRecord(): EmployeeProfile
    {
        return $this->record;
    }
}
