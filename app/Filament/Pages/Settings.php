<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Settings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Beállítások';

    protected static ?string $title = 'Beállítások';

    protected string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'cancellation_hours' => Setting::get('cancellation_hours', 24),
            'reminder_hours' => Setting::get('reminder_hours', 24),
            'default_buffer_minutes' => Setting::get('default_buffer_minutes', 0),
            'site_name' => Setting::get('site_name', 'Dóra Alfoldy'),
            'admin_email' => Setting::get('admin_email', ''),
            'booking_terms' => Setting::get('booking_terms', ''),
            'barion_pos_key' => Setting::get('barion_pos_key', ''),
            'barion_sandbox' => Setting::get('barion_sandbox', true),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Foglalás beállítások')
                    ->schema([
                        Forms\Components\TextInput::make('cancellation_hours')
                            ->label('Lemondási határidő')
                            ->numeric()
                            ->suffix('óra')
                            ->required(),
                        Forms\Components\TextInput::make('reminder_hours')
                            ->label('Emlékeztető idő')
                            ->numeric()
                            ->suffix('óra')
                            ->required(),
                        Forms\Components\TextInput::make('default_buffer_minutes')
                            ->label('Alapértelmezett szünet')
                            ->numeric()
                            ->suffix('perc')
                            ->required(),
                    ]),
                Forms\Components\Section::make('Oldal beállítások')
                    ->schema([
                        Forms\Components\TextInput::make('site_name')
                            ->label('Oldal neve')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('admin_email')
                            ->label('Admin email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('booking_terms')
                            ->label('Foglalási feltételek')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'link',
                                'orderedList',
                                'unorderedList',
                            ])
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Fizetés beállítások')
                    ->schema([
                        Forms\Components\TextInput::make('barion_pos_key')
                            ->label('Barion POS Key')
                            ->password()
                            ->revealable()
                            ->maxLength(255),
                        Forms\Components\Toggle::make('barion_sandbox')
                            ->label('Barion Sandbox mód')
                            ->default(true),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        Notification::make()
            ->title('Beállítások mentve')
            ->success()
            ->send();
    }
}
