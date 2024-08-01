<div class="mb-0 w-full flex">
    @foreach($tabs as $id => $tab)
        <x-block-tab
                :active="$id === $activeTab"
                :title='"{$id}. {$tab}"'
                :id="$id"
                wire:click="$set('activeTab', {{ $id }})"
            />
    @endforeach
</div>
