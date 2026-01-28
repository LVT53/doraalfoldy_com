<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReferencePhotoResource\Pages;
use App\Models\ReferencePhoto;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ReferencePhotoResource extends Resource
{
    protected static ?string $model = ReferencePhoto::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Referencia fotók';

    protected static ?string $modelLabel = 'Referencia fotó';

    protected static ?string $pluralModelLabel = 'Referencia fotók';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('category_id')
                    ->label('Kategória')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\FileUpload::make('image_path')
                    ->label('Kép')
                    ->image()
                    ->disk('public')
                    ->directory('photos')
                    ->visibility('public')
                    ->required()
                    ->imageEditor()
                    ->maxSize(5120),
                Forms\Components\TextInput::make('caption')
                    ->label('Felirat')
                    ->maxLength(255),
                Forms\Components\TextInput::make('sort_order')
                    ->label('Sorrend')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Kép')
                    ->disk('public')
                    ->width(100)
                    ->height(100),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategória')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('caption')
                    ->label('Felirat')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Sorrend')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Létrehozva')
                    ->dateTime('Y.m.d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Kategória')
                    ->relationship('category', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReferencePhotos::route('/'),
            'create' => Pages\CreateReferencePhoto::route('/create'),
            'edit' => Pages\EditReferencePhoto::route('/{record}/edit'),
        ];
    }
}
