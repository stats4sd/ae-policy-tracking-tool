<x-filament-panels::page>

    <!-- user instructions -->
    <x-filament::section
        class="mb-4"
        heading="Page information"
        icon="heroicon-o-information-circle"
        icon-color="primary"
        collapsible="true">
        <p class="mb-4">This page is for bulk upload. Each uploaded file will become a new policy document.</p>
    </x-filament::section>


    <form wire:submit="save">

        <div class="mb-6">
            {{ $this->form }}
        </div>

        <div class="flex justify-end">
            <x-filament::button type="submit" color="primary">
                Save Documents
            </x-filament::button>
        </div>
    </form>

</x-filament-panels::page>
