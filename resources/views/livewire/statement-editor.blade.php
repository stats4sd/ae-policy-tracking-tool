<div class="grid grid-cols-12 space-x-8 {{ $first ? '' : 'mt-8 border-t border-t-gray-500 pt-8' }}">
    <div class="col-span-12 lg:col-span-3 border-r border-r-gray-500 p-4 place-content-center text-right font-bold text-lg">
        <p>{{ \Illuminate\Support\Str::upper($type->name) }}</p>
    </div>
    <div class="col-span-12 lg:col-span-7 space-y-3 text-sm">

        @if(!$editing)
            @foreach($assessmentPriorityAction->statements->where('type_id', $this->type->id) as $statement)
                <p>{{ $statement->name }}</p>
                @foreach($statement->policies as $policy)
                    <span class="badge badge-primary">{{ $policy->name }}</span>
                @endforeach

                @if(!$loop->last)
                    <hr/>
                @endif
            @endforeach

        @else
            {{ $this->form }}
        @endif
    </div>
    <div class="col-span-12 lg:col-span-2 place-content-center">
        @if(!$editing)
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" wire:click="$set('editing', true)">Edit</button>
        @else
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" wire:click="update()">Save</button>
        @endif

    </div>
</div>
