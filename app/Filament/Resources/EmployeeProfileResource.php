<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeProfileResource\Pages;
use App\Models\EmployeeProfile;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;

class EmployeeProfileResource extends Resource
{
    protected static ?string $model = EmployeeProfile::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'Munkatárs profil';

    protected static ?string $modelLabel = 'Munkatárs profil';

    protected static ?string $pluralModelLabel = 'Munkatárs profil';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Section::make('Profil adatok')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Név')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('bio')
                            ->label('Bemutatkozás')
                            ->rows(5)
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('image_path')
                            ->label('Profilkép')
                            ->image()
                            ->imageEditor()
                            ->directory('employee-profiles')
                            ->maxSize(2048)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('instagram_url')
                            ->label('Instagram URL')
                            ->url()
                            ->prefix('https://instagram.com/')
                            ->maxLength(255),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'edit' => Pages\EditEmployeeProfile::route('/edit'),
        ];
    }
}
