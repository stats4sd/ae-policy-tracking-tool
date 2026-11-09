@props(['score'])

@php
    $class = $score === null ? 'none' : ($score < 0 ? 'n'.abs($score) : $score);
    $text = $score !== null && ($score <= -2 || $score >= 3) ? 'score-text-light' : 'score-text-dark';
@endphp

<span {{ $attributes->class(["inline-flex items-center justify-center rounded-sm font-mono text-xs score-bg-{$class} {$text}"]) }}>
    @if($score !== null)
        {{ $score > 0 ? '+' : '' }}{{ $score }}
    @endif
</span>
