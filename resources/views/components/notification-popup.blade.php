@php
    $unreadNotifications = Auth::user()->unreadNotifications()->latest()->take(5)->get();
@endphp

@if($unreadNotifications->count() > 0)
@php
    // Kumpulkan semua ID notifikasi yang belum dibaca di halaman ini
    $popupNotifIds = $unreadNotifications->pluck('id')->toArray();
@endphp

<div id="notification-popup"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 flex flex-col max-h-[85vh]">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Notifikasi Baru</h3>
                    <p class="text-xs text-gray-500">
                        {{ $unreadNotifications->count() }} notifikasi belum dibaca
                    </p>
                </div>
            </div>
            <button onclick="closeNotificationPopup()"
                    class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Daftar Notifikasi --}}
        <div class="overflow-y-auto flex-1 divide-y divide-gray-100">
            @foreach($unreadNotifications as $notification)
                @php
                    $data     = $notification->data;
                    $isCustom = ($data['type'] ?? null) === 'custom';
                    $title    = $isCustom ? ($data['title'] ?? 'Pengumuman') : ($data['task_title'] ?? 'Tugas Baru');
                    $body     = $data['message'] ?? '';
                @endphp
                <div class="px-6 py-4 hover:bg-gray-50 transition">
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 rounded-full flex-shrink-0 mt-2
                                    {{ $isCustom ? 'bg-yellow-400' : 'bg-primary-500' }}"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800">{{ $title }}</p>
                            <p class="text-sm text-gray-500 mt-0.5">{{ $body }}</p>
                            <div class="flex items-center gap-3 mt-1.5">
                                <span class="text-xs text-gray-400">
                                    {{ $notification->created_at->locale('id')->diffForHumans() }}
                                </span>
                                @if(!$isCustom && isset($data['task_date']))
                                    <span class="text-xs text-gray-400">•</span>
                                    <span class="text-xs text-gray-400">
                                        {{ \Carbon\Carbon::parse($data['task_date'])->locale('id')->translatedFormat('d M Y') }}
                                    </span>
                                @endif
                                @if($isCustom && !empty($data['sender_name']))
                                    <span class="text-xs text-gray-400">•</span>
                                    <span class="text-xs text-gray-400">Dari: {{ $data['sender_name'] }}</span>
                                @endif
                            </div>
                        </div>
                        <form action="{{ route('notifications.read', $notification->id) }}"
                              method="POST" class="flex-shrink-0">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="text-xs text-primary-600 hover:text-primary-700
                                           font-medium whitespace-nowrap">
                                Dibaca
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between flex-shrink-0">
            <form action="{{ route('notifications.read.all') }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit"
                        class="text-sm text-gray-500 hover:text-gray-700 font-medium transition">
                    Tandai Semua Dibaca
                </button>
            </form>
            <a href="{{ route('notifications.index') }}"
               onclick="closeNotificationPopup()"
               class="flex items-center gap-1.5 px-4 py-2 bg-primary-600 hover:bg-primary-700
                      text-white text-sm font-medium rounded-lg transition">
                Lihat Semua
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
</div>

<script>
(function () {
    var popup       = document.getElementById('notification-popup');
    // ID notifikasi yang ada di halaman ini (dari server)
    var currentIds  = @json($popupNotifIds);
    var STORAGE_KEY = 'notif_seen_ids';

    /**
     * Ambil array ID yang sudah pernah ditampilkan dari sessionStorage.
     */
    function getSeenIds() {
        try {
            return JSON.parse(sessionStorage.getItem(STORAGE_KEY) || '[]');
        } catch (e) {
            return [];
        }
    }

    /**
     * Simpan ID yang sudah ditampilkan ke sessionStorage.
     * Hanya menyimpan ID yang benar-benar ada agar storage tidak membengkak.
     */
    function markAsSeen(ids) {
        var seen = getSeenIds();
        ids.forEach(function (id) {
            if (seen.indexOf(id) === -1) seen.push(id);
        });
        // Batasi maksimal 100 entri agar storage tidak membengkak
        if (seen.length > 100) seen = seen.slice(seen.length - 100);
        sessionStorage.setItem(STORAGE_KEY, JSON.stringify(seen));
    }

    /**
     * Cek apakah semua ID notifikasi saat ini sudah pernah ditampilkan.
     * Jika ada SATU SAJA yang belum pernah dilihat → popup harus muncul.
     */
    function hasUnseen() {
        var seen = getSeenIds();
        return currentIds.some(function (id) {
            return seen.indexOf(id) === -1;
        });
    }

    if (!popup) return;

    if (hasUnseen()) {
        // Ada notifikasi yang belum pernah ditampilkan → tampilkan popup
        popup.classList.remove('hidden');
        // Catat semua ID ini sebagai sudah ditampilkan
        markAsSeen(currentIds);
    }
    // Jika semua sudah pernah dilihat → popup tetap hidden, tidak perlu action

    window.closeNotificationPopup = function () {
        popup.classList.add('hidden');
        // Tidak perlu set flag tambahan — ID sudah dicatat di markAsSeen() saat tampil
    };

    // Tutup saat klik area gelap di luar modal
    popup.addEventListener('click', function (e) {
        if (e.target === popup) closeNotificationPopup();
    });
})();
</script>
@endif
