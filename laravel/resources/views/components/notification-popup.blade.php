@php
    $unreadNotifications = Auth::user() ? Auth::user()->unreadNotifications()->latest()->take(5)->get() : collect();
    $popupNotifIds = $unreadNotifications->pluck('id')->toArray();
@endphp

{{-- ── MODAL POPUP NOTIFIKASI (TENGAH LAYAR & LIVE REAL-TIME) ──── --}}
<div id="notification-popup"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 transition-all duration-200">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-auto flex flex-col max-h-[85vh] overflow-hidden animate-in fade-in zoom-in-95 duration-150">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 sm:px-6 py-4 border-b border-gray-100 flex-shrink-0 bg-gray-50/50">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-gray-800">Notifikasi Baru</h3>
                    <p id="notification-popup-subtitle" class="text-xs text-gray-500">
                        <span id="notification-popup-count">{{ $unreadNotifications->count() }}</span> notifikasi belum dibaca
                    </p>
                </div>
            </div>
            <button type="button" onclick="closeNotificationPopup()"
                    class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Daftar Notifikasi --}}
        <div id="notification-popup-list" class="overflow-y-auto flex-1 divide-y divide-gray-100">
            @forelse($unreadNotifications as $notification)
                @php
                    $data     = $notification->data;
                    $isCustom = ($data['type'] ?? null) === 'custom';
                    $title    = $isCustom ? ($data['title'] ?? 'Pengumuman') : ($data['task_title'] ?? 'Tugas Baru');
                    $body     = $data['message'] ?? '';
                    $sender   = $data['sender_name'] ?? 'Admin / HR';
                @endphp
                <div class="px-5 sm:px-6 py-3.5 hover:bg-gray-50 transition" id="notif-row-{{ $notification->id }}">
                    <div class="flex items-start gap-3">
                        <div class="w-2.5 h-2.5 rounded-full flex-shrink-0 mt-1.5
                                    {{ $isCustom ? 'bg-amber-400' : 'bg-primary-500' }}"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs sm:text-sm font-semibold text-gray-800">{{ $title }}</p>
                            <p class="text-xs text-gray-600 mt-0.5 leading-relaxed">{{ $body }}</p>
                            <div class="flex items-center gap-2 mt-1.5 text-[11px] text-gray-400">
                                <span>{{ $notification->created_at->locale('id')->diffForHumans() }}</span>
                                <span>&bull;</span>
                                <span>Dari: {{ $sender }}</span>
                            </div>
                        </div>
                        <form action="{{ route('notifications.read', $notification->id) }}"
                              method="POST" class="flex-shrink-0">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="text-xs text-primary-600 hover:text-primary-700 font-medium px-2 py-1 rounded hover:bg-primary-50 transition">
                                Dibaca
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div id="notif-empty-state" class="px-6 py-8 text-center text-xs text-gray-400">
                    Tidak ada notifikasi baru yang belum dibaca
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        <div class="px-5 sm:px-6 py-3.5 border-t border-gray-100 flex items-center justify-between flex-shrink-0 bg-gray-50/50">
            <form action="{{ route('notifications.read.all') }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit"
                        class="text-xs sm:text-sm text-gray-500 hover:text-gray-700 font-medium transition">
                    Tandai Semua Dibaca
                </button>
            </form>
            <a href="{{ route('notifications.index') }}"
               onclick="closeNotificationPopup()"
               class="flex items-center gap-1.5 px-3.5 py-1.5 sm:py-2 bg-primary-600 hover:bg-primary-700
                      text-white text-xs sm:text-sm font-medium rounded-lg transition shadow-sm">
                Lihat Semua
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
</div>

<style>
@keyframes notifBellWiggle {
    0%, 100% { transform: rotate(0deg); }
    20% { transform: rotate(-15deg); }
    40% { transform: rotate(15deg); }
    60% { transform: rotate(-10deg); }
    80% { transform: rotate(10deg); }
}
.animate-bell-wiggle {
    animation: notifBellWiggle 0.6s ease-in-out 3;
}
</style>

<script>
(function () {
    var popup       = document.getElementById('notification-popup');
    var listEl      = document.getElementById('notification-popup-list');
    var countEl     = document.getElementById('notification-popup-count');
    var currentIds  = @json($popupNotifIds);
    var STORAGE_KEY = 'notif_seen_ids';
    var POLL_INTERVAL = 6000; // Cek notifikasi setiap 6 detik
    var csrfToken   = '{{ csrf_token() }}';

    function getSeenIds() {
        try {
            return JSON.parse(sessionStorage.getItem(STORAGE_KEY) || '[]');
        } catch (e) {
            return [];
        }
    }

    function markAsSeen(ids) {
        var seen = getSeenIds();
        ids.forEach(function (id) {
            if (seen.indexOf(id) === -1) seen.push(id);
        });
        if (seen.length > 100) seen = seen.slice(seen.length - 100);
        sessionStorage.setItem(STORAGE_KEY, JSON.stringify(seen));
    }

    function hasUnseen(ids) {
        var seen = getSeenIds();
        return ids.some(function (id) {
            return seen.indexOf(id) === -1;
        });
    }

    // Subtle Chime Sound via Web Audio API
    function playNotificationSound() {
        try {
            var AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;
            var ctx = new AudioContext();

            var osc1 = ctx.createOscillator();
            var gain1 = ctx.createGain();
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(880, ctx.currentTime);
            gain1.gain.setValueAtTime(0.12, ctx.currentTime);
            gain1.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);
            osc1.connect(gain1);
            gain1.connect(ctx.destination);
            osc1.start(ctx.currentTime);
            osc1.stop(ctx.currentTime + 0.3);

            var osc2 = ctx.createOscillator();
            var gain2 = ctx.createGain();
            osc2.type = 'sine';
            osc2.frequency.setValueAtTime(1318.5, ctx.currentTime + 0.12);
            gain2.gain.setValueAtTime(0.15, ctx.currentTime + 0.12);
            gain2.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.5);
            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            osc2.start(ctx.currentTime + 0.12);
            osc2.stop(ctx.currentTime + 0.5);
        } catch (e) {}
    }

    // Update Badge di Navbar
    function updateNavbarBadge(count) {
        var badge = document.getElementById('nav-notif-badge');
        var bellIcon = document.getElementById('nav-notif-bell-icon');

        if (!badge) return;

        if (count > 0) {
            badge.textContent = count > 9 ? '9+' : count;
            badge.classList.remove('hidden');
            badge.classList.add('flex');

            if (bellIcon) {
                bellIcon.classList.remove('animate-bell-wiggle');
                void bellIcon.offsetWidth;
                bellIcon.classList.add('animate-bell-wiggle');
            }
        } else {
            badge.classList.add('hidden');
            badge.classList.remove('flex');
            badge.textContent = '';
        }
    }

    // Render daftar notifikasi ke modal
    function renderPopupNotifications(notifications, totalCount) {
        if (!listEl) return;

        if (countEl) countEl.textContent = totalCount;

        if (!notifications || notifications.length === 0) {
            listEl.innerHTML = '<div id="notif-empty-state" class="px-6 py-8 text-center text-xs text-gray-400">Tidak ada notifikasi baru yang belum dibaca</div>';
            return;
        }

        var html = '';
        notifications.forEach(function(n) {
            var dotBg = n.is_custom ? 'bg-amber-400' : 'bg-primary-500';
            var sender = n.sender_name || 'Admin / HR';
            var readUrl = '/notifications/' + n.id + '/read';

            html += `
                <div class="px-5 sm:px-6 py-3.5 hover:bg-gray-50 transition" id="notif-row-${n.id}">
                    <div class="flex items-start gap-3">
                        <div class="w-2.5 h-2.5 rounded-full flex-shrink-0 mt-1.5 ${dotBg}"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs sm:text-sm font-semibold text-gray-800">${n.title}</p>
                            <p class="text-xs text-gray-600 mt-0.5 leading-relaxed">${n.message}</p>
                            <div class="flex items-center gap-2 mt-1.5 text-[11px] text-gray-400">
                                <span>${n.time_ago}</span>
                                <span>&bull;</span>
                                <span>Dari: ${sender}</span>
                            </div>
                        </div>
                        <form action="${readUrl}" method="POST" class="flex-shrink-0">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <input type="hidden" name="_method" value="PATCH">
                            <button type="submit"
                                    class="text-xs text-primary-600 hover:text-primary-700 font-medium px-2 py-1 rounded hover:bg-primary-50 transition">
                                Dibaca
                            </button>
                        </form>
                    </div>
                </div>
            `;
        });

        listEl.innerHTML = html;
    }

    if (!popup) return;

    // Cek saat halaman pertama dimuat
    if (currentIds.length > 0 && hasUnseen(currentIds)) {
        popup.classList.remove('hidden');
        markAsSeen(currentIds);
    }

    window.closeNotificationPopup = function () {
        if (popup) popup.classList.add('hidden');
    };

    // Tutup saat klik area gelap di luar modal
    popup.addEventListener('click', function (e) {
        if (e.target === popup) closeNotificationPopup();
    });

    // ── LIVE POLLING REAL-TIME ────────────────────────────────
    function checkLiveNotifications() {
        fetch('{{ route("notifications.live.check") }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(function(res) {
            if (!res.ok) throw new Error('Network error');
            return res.json();
        })
        .then(function(data) {
            if (!data) return;

            var unreadCount = data.unread_count || 0;
            updateNavbarBadge(unreadCount);

            var notifs = data.notifications || [];
            var newIds = notifs.map(function(n) { return n.id; });

            // Jika ada notifikasi yang belum pernah dilihat sama sekali
            if (newIds.length > 0 && hasUnseen(newIds)) {
                // Perbarui daftar di modal popup
                renderPopupNotifications(notifs, unreadCount);
                // Bunyikan chime
                playNotificationSound();
                // BUKA MODAL POPUP DI TENGAH LAYAR SECARA LIVE
                popup.classList.remove('hidden');
                // Catat ID sebagai sudah dilihat
                markAsSeen(newIds);
            }
        })
        .catch(function(err) {
            // Abaikan error jaringan saat polling di background
        });
    }

    // Jalankan polling setiap 6 detik
    setInterval(checkLiveNotifications, POLL_INTERVAL);
})();
</script>
