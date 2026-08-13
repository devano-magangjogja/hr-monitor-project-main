@props([
    'cols' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4',
    'gap' => 'gap-4 md:gap-5 lg:gap-6'
])

<div {{ $attributes->merge(['class' => "grid $cols $gap"]) }}>
    {{ $slot }}
</div>
