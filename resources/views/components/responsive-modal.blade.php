@props([
    'id' => 'modal-' . uniqid()
])

<div id="{{ $id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
    <div {{ $attributes->merge(['class' => 'bg-white rounded-2xl shadow-xl w-full max-w-sm sm:max-w-md md:max-w-lg max-h-[90vh] overflow-y-auto flex flex-col']) }}>
        {{ $slot }}
    </div>
</div>
