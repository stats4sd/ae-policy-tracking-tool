<div
    wire:click="$call('setActiveTab', {{ $index }})"
    class="
    px-5 py-4 border-x border-x-bright-title-block text-gray-50
    {{ $active ? 'bg-bright-title-block' : 'bg-light-title-block' }}
    "
>
    {{ $title }}
</div>
