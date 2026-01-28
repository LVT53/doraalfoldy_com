<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationLabel = 'Értékelések';

    protected static ?string $modelLabel = 'Értékelés';

    protected static ?string $pluralModelLabel = 'Értékelések';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('appointment_id')
                    ->label('Időpont')
                    ->relationship('appointment', 'id')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Forms\Components\TextInput::make('customer_name')
                    ->label('Ügyfél neve')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('customer_email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('rating')
                    ->label('Értékelés')
                    ->options([
                        1 => '1 csillag',
                        2 => '2 csillag',
                        3 => '3 csillag',
                        4 => '4 csillag',
                        5 => '5 csillag',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('content')
                    ->label('Tartalom')
                    ->rows(5)
                    ->required()
                    ->maxLength(65535),
                Forms\Components\Toggle::make('is_approved')
                    ->label('Jóváhagyva')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Ügyfél')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer_email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Értékelés')
                    ->formatStateUsing(fn (int $state): string => str_repeat('★', $state).str_repeat('☆', 5 - $state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('content')
                    ->label('Tartalom')
                    ->limit(50)
                    ->tooltip(fn (Review $record): string => $record->content),
                Tables\Columns\ToggleColumn::make('is_approved')
                    ->label('Jóváhagyva')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Létrehozva')
                    ->dateTime('Y.m.d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('Jóváhagyás státusza')
                    ->placeholder('Összes')
                    ->trueLabel('Jóváhagyott')
                    ->falseLabel('Jóváhagyásra vár'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('approve')
                        ->label('Jóváhagyás')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records): void {
                            foreach ($records as $record) {
                                $record->update(['is_approved' => true]);
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
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
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
