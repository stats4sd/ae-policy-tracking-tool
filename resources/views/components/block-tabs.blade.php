<div class="mb-0 w-full flex">
    @foreach($tabs as $tab)
        <x-block-tab
                :active="$loop->index === $activeTab"
                :title="$tab"
                :index="$loop->index"
                wire:click="$set('activeTab', {{ $loop->index }})"
            />
    @endforeach
</div>
