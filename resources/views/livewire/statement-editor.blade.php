<div class="grid grid-cols-12 space-x-8 {{ $first ? '' : 'mt-8 border-t border-t-gray-500 pt-8' }}">
    <div class="col-span-12 lg:col-span-3 border-r border-r-gray-500 p-4 place-content-center text-right font-bold text-lg">
        <p>{{ \Illuminate\Support\Str::upper($type->name) }}</p>
    </div>
    <div @class([
            "col-span-12 space-y-3 text-sm",
            "lg:col-span-9" => $editing,
            "lg:col-span-7" => !$editing,
            ])
    >

        @if(!$editing)
            @foreach($priorityAction->statements->where('type_id', $this->type->id) as $statement)
                <p>{{ $statement->name }}</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($statement->policyDocuments as $policyDocument)
                        <x-filament::badge :href="\App\Filament\App\Resources\PolicyDocumentResource::getUrl('index')" tag="a">
                            {{ $policyDocument->name }}
                        </x-filament::badge>
                    @endforeach
                </div>

                @if(!$loop->last)
                    <hr/>
                @endif
            @endforeach

        @else
            {{ $this->form }}
            <div class="flex place-content-end">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" wire:click="update()">Save</button>
            </div>
        @endif
    </div>
    @if(!$editing)
        <div class="col-span-12 lg:col-span-2 place-content-center">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" wire:click="$set('editing', true)">Edit</button>
        </div>
    @endif

    <x-filament-actions::modals/>
</div>
