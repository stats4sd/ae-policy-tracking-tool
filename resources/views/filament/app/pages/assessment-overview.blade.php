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
            @foreach($assessmentPriorityActions->filter(fn(\App\Models\AssessmentPriorityAction $action) => $action->priorityAction->recommendation_id === $activeTab) as $action)
                <x-filament::section class="header-dark"
                                     heading="PRIORITY ACTION {{ $action->priorityAction->id }}"
                                     description="{{ $action->priorityAction->name }}"
                                     :collapsible="true"
                                     :collapsed="!$loop->first"
                >
                    <div class="space-y-8">
                        @foreach($action->statementsByType as $type => $statements)

                            <div class="grid grid-cols-12 space-x-8 {{ $loop->first ? '' : 'mt-8 border-t border-t-gray-500 pt-8' }}">
                                <div class="col-span-12 lg:col-span-3 border-r border-r-gray-500 p-4 place-content-center text-right font-bold text-lg">
                                    <p>{{ \Illuminate\Support\Str::upper($type) }}</p>
                                </div>
                                <div class="col-span-12 lg:col-span-7 space-y-3 text-sm">
                                    <p>{!! $statements->pluck('name')->join('</p><hr/><p>') !!}</p>
                                </div>
                                <div class="col-span-12 lg:col-span-2 place-content-center">
                                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Edit</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-filament::section>
            @endforeach
        </div>
    </div>

</x-filament-panels::page>
