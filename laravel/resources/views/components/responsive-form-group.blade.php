@props([
    'label' => '',
    'required' => false,
    'error' => null,
    'helperText' => null
])

<div {{ $attributes->merge(['class' => 'space-y-1']) }}>
    @if($label)
        <label class="block text-xs sm:text-sm font-medium text-gray-700">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <div class="slot-content">
        {{ $slot }}
    </div>

    @if($error)
        <p class="mt-1 text-xs text-red-500">{{ $error }}</p>
    @endif

    @if($helperText)
        <p class="mt-1 text-xs text-gray-400">{{ $helperText }}</p>
    @endif
</div>

<style>
    .slot-content > * {
        @apply px-3 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm;
    }
</style>
