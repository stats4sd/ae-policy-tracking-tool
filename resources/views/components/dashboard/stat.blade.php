@props([
    'value',
    'label',
    'of' => null,
    'percent' => null,
])

<div {{ $attributes->class(['border border-gray-200 bg-white p-4']) }}>
    <div class="text-2xl font-semibold text-gray-900 tabular-nums">
        {{ $value }}
        @if($of !== null)
            <span class="text-base font-normal text-gray-500">/ {{ $of }}</span>
        @endif
    </div>
    <div class="mt-1 text-sm text-gray-600">{{ $label }}</div>
    @if($percent !== null)
        <div class="mt-2 h-1.5 w-full bg-gray-100">
            <div class="h-full bg-primary-600" style="width: {{ $percent }}%"></div>
        </div>
    @endif
</div>
