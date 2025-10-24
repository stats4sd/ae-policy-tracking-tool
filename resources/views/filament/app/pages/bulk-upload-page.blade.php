<x-filament-panels::page>

    <!-- user instructions -->
    <x-filament::section
        class="mb-4"
        heading="Page information"
        icon="heroicon-o-information-circle"
        icon-color="primary"
        collapsible>
        <p class="mb-4">This page is for bulk upload. Each uploaded file will become a new policy document.</p>
    </x-filament::section>


    <!-- wire form submission to call save() function in custom page -->
    <x-filament-panels::form wire:submit="save">

        <!-- show filament form defined in form() function custom page -->
        {{ $this->form }}

        <!-- show filament actions (buttons) defined in getFormActions() function custom page -->
        <x-filament-panels::form.actions :actions="$this->getFormActions()"/> 

    </x-filament-panels::form>

</x-filament-panels::page>
