@props([
    'status',
    'completedAt' => null,
])

@if($status === 'completed')
    <div class="inline-flex flex-col gap-0.5">
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                     text-xs font-medium bg-green-50 text-green-700">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                      clip-rule="evenodd"/>
            </svg>
            Selesai
        </span>
        @if($completedAt)
            <span class="inline-flex items-center gap-1 text-[10px] text-gray-400 pl-1">
                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ \Carbon\Carbon::parse($completedAt)->timezone('Asia/Jakarta')->format('H:i') }} WIB
            </span>
        @endif
    </div>

@elseif($status === 'not_done')
    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                 text-xs font-medium bg-red-50 text-red-700">
        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                  clip-rule="evenodd"/>
        </svg>
        Tidak Dikerjakan
    </span>

@else
    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium
                 bg-yellow-50 text-yellow-700">
        Belum Selesai
    </span>
@endif
