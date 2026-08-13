<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table {{ $attributes->merge(['class' => 'w-full text-xs sm:text-sm min-w-full']) }}>
            {{ $slot }}
        </table>
    </div>
</div>
