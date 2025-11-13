<x-filament-panels::page.simple>
    <form id="form" wire:submit="register" class="">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </form>

</x-filament-panels::page.simple>
