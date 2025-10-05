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
                    <div class="space-y-8">
                        @foreach(\App\Models\Type::all() as $type)
                            <livewire:statement-editor
                                :statements="$action->statements->where('type_id', $type->id)"
                                :priority-action="$action"
                                :type="$type"
                                :wire:key='"{$action->id}_{$type->id}"'
                                :first="$loop->first"
                            />
                        @endforeach
                    </div>
                </x-filament::section>
            @endforeach
        </div>
    </div>

</x-filament-panels::page>
