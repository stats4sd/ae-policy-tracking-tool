<x-filament-panels::page >

    <div id="assessment-app" class="filament-app-layout-page-content w-full">
        <Assessment :document-id="{{$this->getRecord()->id}}"></Assessment>
    </div>

</x-filament-panels::page>
