@props([
    'variant' => 'primary', // primary, secondary, danger, success
    'size' => 'md', // sm, md, lg
    'type' => 'button'
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition duration-200 whitespace-nowrap focus:outline-none focus:ring-2 focus:ring-offset-2';
    
    $sizeClasses = match($size) {
        'sm' => 'px-3 py-1.5 sm:px-4 sm:py-2 text-xs sm:text-sm gap-1.5 sm:gap-2',
        'lg' => 'px-4 sm:px-6 py-2.5 sm:py-3 text-sm md:text-base gap-2 sm:gap-3',
        'md' => 'px-3 sm:px-5 py-2 sm:py-2.5 text-xs sm:text-sm gap-1.5 sm:gap-2',
    };
    
    $variantClasses = match($variant) {
        'primary' => 'bg-primary-600 hover:bg-primary-700 text-white focus:ring-primary-500',
        'secondary' => 'bg-gray-100 hover:bg-gray-200 text-gray-700 focus:ring-gray-500',
        'danger' => 'bg-red-600 hover:bg-red-700 text-white focus:ring-red-500',
        'success' => 'bg-green-600 hover:bg-green-700 text-white focus:ring-green-500',
    };
@endphp

<button {{ $attributes->merge(['type' => $type, 'class' => "$baseClasses $sizeClasses $variantClasses"]) }}>
    {{ $slot }}
</button>
