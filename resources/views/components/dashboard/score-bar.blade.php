@props([
    'dist',
    'height' => 'h-3',
])

@php
    $total = array_sum($dist);
@endphp

<div {{ $attributes->class(["flex w-full overflow-hidden rounded-sm score-bg-none {$height}"]) }}
     title="{{ collect($dist)->filter()->map(fn ($count, $score) => ($score > 0 ? '+' : '').$score.': '.$count)->implode(' · ') ?: 'No verified evidence' }}">
    @if($total > 0)
        @foreach($dist as $score => $count)
            @if($count > 0)
                <span class="block h-full score-bg-{{ $score < 0 ? 'n'.abs($score) : $score }}"
                      style="width: {{ round($count / $total * 100, 2) }}%"></span>
            @endif
        @endforeach
    @endif
</div>
