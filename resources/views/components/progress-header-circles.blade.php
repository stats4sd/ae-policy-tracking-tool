<div class="items-center flex flex-col align-middle">
    <div class="flex w-10 h-10 rounded-full overflow-hidden
        @if($status === 'In Progress') bg-blue-600
        @elseif($status==='Complete') bg-green-600
        @else bg-gray-300
        @endif
     "
         role="progressbar"
         aria-valuenow="{{ $percent }}"
         aria-valuemin="0"
         aria-valuemax="100"
    >
    </div>
    <div class="mt-2 flex flex-col justify-between items-center gap-y-1">
        <h3 class="text-sm font-semibold text-gray-800 dark:text-white">{{ $stepName }}</h3>
        <span class="text-sm text-gray-800 dark:text-white">{{ $status }}</span>
    </div>
</div>
