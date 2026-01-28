<?php

namespace App\Filament\Resources;

use App\Enums\AppointmentStatus;
use App\Filament\Resources\AppointmentResource\Pages;
use App\Models\Appointment;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Időpontok';

    protected static ?string $modelLabel = 'Időpont';

    protected static ?string $pluralModelLabel = 'Időpontok';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('service_id')
                    ->label('Szolgáltatás')
                    ->relationship('service', 'name')
                    ->required(),
                Forms\Components\TextInput::make('user_name')
                    ->label('Ügyfél neve')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('user_email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('user_phone')
                    ->label('Telefon')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('start_time')
                    ->label('Kezdés időpontja')
                    ->required(),
                Forms\Components\DateTimePicker::make('end_time')
                    ->label('Befejezés időpontja')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Státusz')
                    ->options([
                        AppointmentStatus::PENDING->value => 'Függőben',
                        AppointmentStatus::CONFIRMED->value => 'Megerősítve',
                        AppointmentStatus::CANCELLED->value => 'Lemondva',
                        AppointmentStatus::COMPLETED->value => 'Teljesítve',
                        AppointmentStatus::NO_SHOW->value => 'Nem jelent meg',
                    ])
                    ->default(AppointmentStatus::PENDING->value)
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->label('Megjegyzések')
                    ->rows(3)
                    ->maxLength(65535),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Időpont')
                    ->dateTime('Y.m.d H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_name')
                    ->label('Ügyfél')
                    ->searchable(),
                Tables\Columns\TextColumn::make('service.name')
                    ->label('Szolgáltatás'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Státusz')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        AppointmentStatus::PENDING->value => 'Függőben',
                        AppointmentStatus::CONFIRMED->value => 'Megerősítve',
                        AppointmentStatus::CANCELLED->value => 'Lemondva',
                        AppointmentStatus::COMPLETED->value => 'Teljesítve',
                        AppointmentStatus::NO_SHOW->value => 'Nem jelent meg',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        AppointmentStatus::PENDING->value => 'gray',
                        AppointmentStatus::CONFIRMED->value => 'success',
                        AppointmentStatus::CANCELLED->value => 'danger',
                        AppointmentStatus::COMPLETED->value => 'info',
                        AppointmentStatus::NO_SHOW->value => 'warning',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Státusz')
                    ->options([
                        AppointmentStatus::PENDING->value => 'Függőben',
                        AppointmentStatus::CONFIRMED->value => 'Megerősítve',
                        AppointmentStatus::CANCELLED->value => 'Lemondva',
                        AppointmentStatus::COMPLETED->value => 'Teljesítve',
                        AppointmentStatus::NO_SHOW->value => 'Nem jelent meg',
                    ]),
                SelectFilter::make('service_id')
                    ->label('Szolgáltatás')
                    ->relationship('service', 'name'),
                Filter::make('date_range')
                    ->label('Időszak')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Ettől'),
                        Forms\Components\DatePicker::make('to')
                            ->label('Eddig'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_time', '>=', $date),
                            )
                            ->when(
                                $data['to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_time', '<=', $date),
                            );
                    }),
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
            ->defaultSort('start_time', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}
