<?php

namespace App\Filament\Resources;

use App\Enums\VoucherType;
use App\Filament\Resources\VoucherResource\Pages;
use App\Models\Voucher;
use BackedEnum;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class VoucherResource extends Resource
{
    protected static ?string $model = Voucher::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'Kuponok';

    protected static ?string $modelLabel = 'Kupon';

    protected static ?string $pluralModelLabel = 'Kuponok';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('code')
                    ->label('Kód')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->suffixAction(
                        Forms\Components\Actions\Action::make('generate')
                            ->label('Generálás')
                            ->icon('heroicon-m-arrow-path')
                            ->action(function (Set $set) {
                                $set('code', strtoupper(Str::random(10)));
                            })
                    ),
                Forms\Components\Select::make('type')
                    ->label('Típus')
                    ->options([
                        VoucherType::PERCENTAGE->value => 'Százalékos kedvezmény',
                        VoucherType::FIXED->value => 'Fix összegű kedvezmény',
                        VoucherType::GIFT_CARD->value => 'Ajándékkártya',
                    ])
                    ->default(VoucherType::PERCENTAGE->value)
                    ->live()
                    ->required(),
                Forms\Components\TextInput::make('value')
                    ->label('Érték')
                    ->numeric()
                    ->suffix(fn (Get $get): string => match ($get('type')) {
                        VoucherType::PERCENTAGE->value => '%',
                        VoucherType::FIXED->value => 'Ft',
                        VoucherType::GIFT_CARD->value => 'Ft',
                        default => '',
                    })
                    ->required(),
                Forms\Components\TextInput::make('balance')
                    ->label('Egyenleg')
                    ->numeric()
                    ->suffix('Ft')
                    ->visible(fn (Get $get): bool => $get('type') === VoucherType::GIFT_CARD->value)
                    ->required(fn (Get $get): bool => $get('type') === VoucherType::GIFT_CARD->value),
                Forms\Components\TextInput::make('recipient_email')
                    ->label('Címzett email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('expires_at')
                    ->label('Lejárat')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kód')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Típus')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        VoucherType::PERCENTAGE->value => 'Százalékos',
                        VoucherType::FIXED->value => 'Fix összegű',
                        VoucherType::GIFT_CARD->value => 'Ajándékkártya',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        VoucherType::PERCENTAGE->value => 'info',
                        VoucherType::FIXED->value => 'success',
                        VoucherType::GIFT_CARD->value => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('value')
                    ->label('Érték')
                    ->formatStateUsing(function (Voucher $record): string {
                        return match ($record->type) {
                            VoucherType::PERCENTAGE => $record->value.'%',
                            VoucherType::FIXED, VoucherType::GIFT_CARD => number_format($record->value, 0, ',', ' ').' Ft',
                        };
                    }),
                Tables\Columns\TextColumn::make('balance')
                    ->label('Egyenleg')
                    ->money('HUF')
                    ->visible(fn (): bool => true),
                Tables\Columns\TextColumn::make('recipient_email')
                    ->label('Címzett email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Lejárat')
                    ->dateTime('Y.m.d H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('used_at')
                    ->label('Felhasználva')
                    ->dateTime('Y.m.d H:i')
                    ->sortable(),
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
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVouchers::route('/'),
            'create' => Pages\CreateVoucher::route('/create'),
            'edit' => Pages\EditVoucher::route('/{record}/edit'),
        ];
    }
}
