<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduleExceptionResource\Pages;
use App\Models\ScheduleException;
use BackedEnum;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ScheduleExceptionResource extends Resource
{
    protected static ?string $model = ScheduleException::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $navigationLabel = 'Kivételek';

    protected static ?string $modelLabel = 'Kivétel';

    protected static ?string $pluralModelLabel = 'Kivételek';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\DatePicker::make('date')
                    ->label('Dátum')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('reason')
                    ->label('Indok')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_closed')
                    ->label('Zárva')
                    ->default(true)
                    ->live(),
                Forms\Components\TimePicker::make('custom_start_time')
                    ->label('Egyedi nyitás')
                    ->visible(fn (Get $get): bool => ! $get('is_closed')),
                Forms\Components\TimePicker::make('custom_end_time')
                    ->label('Egyedi zárás')
                    ->visible(fn (Get $get): bool => ! $get('is_closed')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Dátum')
                    ->date('Y.m.d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reason')
                    ->label('Indok')
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('is_closed')
                    ->label('Zárva'),
                Tables\Columns\TextColumn::make('custom_start_time')
                    ->label('Egyedi nyitás')
                    ->time('H:i'),
                Tables\Columns\TextColumn::make('custom_end_time')
                    ->label('Egyedi zárás')
                    ->time('H:i'),
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
            ])
            ->defaultSort('date', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScheduleExceptions::route('/'),
            'create' => Pages\CreateScheduleException::route('/create'),
            'edit' => Pages\EditScheduleException::route('/{record}/edit'),
        ];
    }
}
