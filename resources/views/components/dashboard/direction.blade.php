@props(['direction'])

@php
    [$symbol, $class, $label] = match ($direction) {
        'positive' => ['▲', 'text-primary-600', 'Mostly supportive'],
        'negative' => ['▼', 'text-danger-600', 'Mostly perverse incentives'],
        'mixed' => ['◆', 'text-warning-600', 'Mixed'],
        default => ['', '', ''],
    };
@endphp

<span {{ $attributes->class(["font-semibold {$class}"]) }} title="{{ $label }}">{{ $symbol }}</span>
