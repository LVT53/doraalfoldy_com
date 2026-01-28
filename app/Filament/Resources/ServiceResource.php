<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationLabel = 'Szolgáltatások';

    protected static ?string $modelLabel = 'Szolgáltatás';

    protected static ?string $pluralModelLabel = 'Szolgáltatások';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('category_id')
                    ->label('Kategória')
                    ->relationship('category', 'name')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('Név')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->label('URL azonosító')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('duration_minutes')
                    ->label('Időtartam')
                    ->numeric()
                    ->suffix('perc')
                    ->required(),
                Forms\Components\TextInput::make('buffer_minutes')
                    ->label('Szünet')
                    ->numeric()
                    ->suffix('perc')
                    ->nullable(),
                Forms\Components\TextInput::make('price')
                    ->label('Ár')
                    ->numeric()
                    ->suffix('Ft')
                    ->required(),
                Forms\Components\TextInput::make('deposit_fee')
                    ->label('Előleg')
                    ->numeric()
                    ->suffix('Ft')
                    ->default(0),
                Forms\Components\Textarea::make('description')
                    ->label('Leírás')
                    ->rows(3)
                    ->maxLength(65535),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktív')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Név')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategória'),
                Tables\Columns\TextColumn::make('duration_minutes')
                    ->label('Időtartam')
                    ->suffix(' perc'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Ár')
                    ->money('HUF'),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktív'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
