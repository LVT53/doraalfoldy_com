<x-filament::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}

        <x-filament::button type="submit" size="md">
            Mentés
        </x-filament::button>
    </x-filament-panels::form>
</x-filament::page>
