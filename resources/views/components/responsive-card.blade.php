@props([
    'padding' => 'p-3 sm:p-4 md:p-5 lg:p-6'
])

<div {{ $attributes->merge(['class' => "bg-white rounded-xl border border-gray-200 $padding"]) }}>
    {{ $slot }}
</div>
