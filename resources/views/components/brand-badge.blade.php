@props(['acc', 'logos'])

@if($acc->brand)
    @if(($logos[$acc->brand] ?? null))
        <img src="{{ asset('storage/' . $logos[$acc->brand]) }}"
            alt="Brand: {{ $acc->brand }}"
            title="Brand: {{ $acc->brand }}"
            class="w-5 h-5 shrink-0 rounded object-contain bg-white border border-gray-200 p-px">
    @else
        <span class="inline-flex shrink-0 items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200 whitespace-nowrap"
            title="Brand: {{ $acc->brand }}">
            <svg class="w-2.5 h-2.5 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            {{ $acc->brand }}
        </span>
    @endif
@endif
