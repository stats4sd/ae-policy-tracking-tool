<div {{ $attributes->class([
    'my-0 w-full border-t-bright-title-block px-16 py-20 bg-bright-title-block',
   ]) }}
>
    <div class="text-xl font-bold text-left text-gray-50 space-y-2">
        <span class="font-light">CFS POLICY RECOMMENDATION {{ $number }}:</span>
        <h3>{{ $slot }}</h3>
    </div>
</div>
