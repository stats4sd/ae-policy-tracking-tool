@props(['scores'])

<div {{ $attributes->class(['flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-600']) }}>
    @foreach($scores as $score => $label)
        <span class="inline-flex items-center gap-1.5">
            <span class="inline-block h-3 w-3 rounded-sm score-bg-{{ $score < 0 ? 'n'.abs($score) : $score }}"></span>
            <span class="font-mono">{{ $score > 0 ? '+' : '' }}{{ $score }}</span>
            <span class="text-gray-500">{{ $label }}</span>
        </span>
    @endforeach
    {{ $slot }}
</div>
