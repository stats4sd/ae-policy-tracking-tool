<x-filament-panels::page>

    <div>
        <livewire:block-tabs
                :tabs='$recommendations->pluck("short_title", "id")->toArray()'
                :active-tab="$activeTab"
                class="text-gray-50"/>

        <x-block-title :number="$activeTab">
            {{ $recommendations->find($activeTab)->name }}
        </x-block-title>

        <div class="space-y-4 mt-4">
            @foreach($activeRecommendation->priorityActions as $action)
                <x-filament::section
                        wire:key="{{ $action->id }}"
                        class="header-dark"
                        heading="PRIORITY ACTION {{ $action->id }}"
                        description="{{ $action->name }}"
                        :collapsible="true"
                        :collapsed="!$loop->first"
                >
                    <div class="px-0 py-0">
                            <livewire:statement-editor
                                    :statements="$action->statements"
                                    :priority-action="$action"
                                    :wire:key='"{$action->id}_statement_editor"'
                                    :first="$loop->first"
                            />
                    </div>
                </x-filament::section>
            @endforeach
        </div>
    </div>

</x-filament-panels::page>
