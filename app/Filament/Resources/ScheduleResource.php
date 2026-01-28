<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduleResource\Pages;
use App\Models\Schedule;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Nyitvatartás';

    protected static ?string $modelLabel = 'Nyitvatartás';

    protected static ?string $pluralModelLabel = 'Nyitvatartás';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('day_of_week')
                    ->label('Nap')
                    ->options([
                        0 => 'Vasárnap',
                        1 => 'Hétfő',
                        2 => 'Kedd',
                        3 => 'Szerda',
                        4 => 'Csütörtök',
                        5 => 'Péntek',
                        6 => 'Szombat',
                    ])
                    ->required(),
                Forms\Components\TimePicker::make('start_time')
                    ->label('Nyitás')
                    ->required(),
                Forms\Components\TimePicker::make('end_time')
                    ->label('Zárás')
                    ->required()
                    ->after('start_time'),
                Forms\Components\Toggle::make('is_off')
                    ->label('Szabadnap')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('day_of_week')
                    ->label('Nap')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => 'Vasárnap',
                        1 => 'Hétfő',
                        2 => 'Kedd',
                        3 => 'Szerda',
                        4 => 'Csütörtök',
                        5 => 'Péntek',
                        6 => 'Szombat',
                        default => 'Ismeretlen',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Nyitás')
                    ->time('H:i'),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('Zárás')
                    ->time('H:i'),
                Tables\Columns\ToggleColumn::make('is_off')
                    ->label('Szabadnap'),
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
            ->defaultSort('day_of_week', 'asc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchedules::route('/'),
            'create' => Pages\CreateSchedule::route('/create'),
            'edit' => Pages\EditSchedule::route('/{record}/edit'),
        ];
    }
}
