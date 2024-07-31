<div class="mb-0 w-full flex">
    @foreach($tabs as $index => $tab)
        <x-block-tab
                :active="$index === $activeTab"
                :title="$tab"
                :index="$loop->index"
            />
    @endforeach
</div>
