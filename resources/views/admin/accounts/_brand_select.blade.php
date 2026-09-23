{{-- Dropdown brand dengan search. Props: $inputId, $resetEvent (opsional), $setEvent (opsional) --}}
<div class="relative"
    :class="open && 'mb-52'"
    x-data='brandSelect({ brands: @json($brands->values()->all()) })'
    @if(!empty($resetEvent))
        x-on:{{ $resetEvent }}.window="selected = ''; query = ''; appliedQuery = ''; open = false"
    @endif
    @if(!empty($setEvent))
        x-on:{{ $setEvent }}.window="selected = $event.detail || ''; query = ''; appliedQuery = ''; open = false"
    @endif
    @click.outside="open = false">
    <input type="hidden" name="brand" id="{{ $inputId }}" x-model="selected">

    <button type="button" @click="open = !open; if (open) $nextTick(() => $refs.brandSearch?.focus())"
        class="w-full flex items-center justify-between gap-2 border border-gray-300 rounded-lg px-3 py-2 text-sm text-left bg-white hover:border-gray-400 focus:ring-2 focus:ring-primary-500 focus:outline-none">
        <span class="truncate" :class="selected ? 'text-gray-800' : 'text-gray-400'"
            x-text="selected || 'Pilih Brand'"></span>
        <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform" :class="open && 'rotate-180'" fill="none"
            stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div x-show="open" x-cloak x-transition.origin.top
        class="absolute z-30 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
        <div class="p-2 border-b border-gray-100 flex items-center gap-2">
            <div class="relative flex-1 min-w-0">
                <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none text-gray-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" x-ref="brandSearch" x-model="query" @keydown.enter.prevent="applySearch()"
                    placeholder="Cari nama brand..."
                    class="w-full h-9 pl-8 pr-2 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:bg-white focus:outline-none">
            </div>
            <button type="button" @click="applySearch()"
                class="h-9 px-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shrink-0">
                Cari
            </button>
        </div>

        <ul class="max-h-44 overflow-y-auto py-1">
            <template x-for="b in filtered" :key="b">
                <li>
                    <button type="button" @click="select(b)"
                        class="w-full text-left px-3 py-2 text-sm hover:bg-indigo-50 transition"
                        :class="selected === b ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-700'"
                        x-text="b"></button>
                </li>
            </template>
            <li x-show="brands.length === 0" class="px-3 py-3 text-center text-xs text-gray-400">
                Belum ada brand terdaftar
            </li>
            <li x-show="brands.length > 0 && filtered.length === 0" class="px-3 py-3 text-center text-xs text-gray-400">
                Brand tidak ditemukan
            </li>
        </ul>

        <button type="button" @click="addBrand()"
            class="w-full flex items-center justify-center gap-1.5 px-3 py-2.5 text-sm font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border-t border-indigo-100 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Brand Baru
        </button>
    </div>
</div>
<p class="text-[11px] text-gray-400 mt-1">Pilih brand yang sudah terdaftar, atau tambahkan brand baru.</p>
