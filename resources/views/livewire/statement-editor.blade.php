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

        {{ $this->table }}
    </div>

    <x-filament-actions::modals/>
</div>
