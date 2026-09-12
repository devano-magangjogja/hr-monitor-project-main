@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination" class="flex items-center justify-between">

    {{-- Info --}}
    <div class="text-xs text-gray-500">
        Menampilkan
        <span class="font-medium text-gray-700">{{ $paginator->firstItem() }}</span>–<span class="font-medium text-gray-700">{{ $paginator->lastItem() }}</span>
        dari
        <span class="font-medium text-gray-700">{{ $paginator->total() }}</span>
        data
    </div>

    {{-- Tombol Navigasi --}}
    <div class="flex items-center gap-1">

        {{-- Prev --}}
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                         text-gray-300 bg-white border border-gray-200 cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
               class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                      text-gray-500 bg-white border border-gray-200
                      hover:bg-gray-50 hover:text-gray-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
        @endif

        {{-- Nomor Halaman --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                {{-- Ellipsis --}}
                <span class="inline-flex items-center justify-center w-8 h-8 text-xs text-gray-400">
                    &hellip;
                </span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                     text-xs font-semibold text-white bg-primary-600 border border-primary-600">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                  text-xs font-medium text-gray-600 bg-white border border-gray-200
                                  hover:bg-gray-50 hover:text-gray-800 transition">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                      text-gray-500 bg-white border border-gray-200
                      hover:bg-gray-50 hover:text-gray-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        @else
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                         text-gray-300 bg-white border border-gray-200 cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </span>
        @endif

    </div>
</nav>
@endif
