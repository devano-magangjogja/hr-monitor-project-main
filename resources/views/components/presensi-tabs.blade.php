@props([
    'tab' => 'hadir',
    'presensiHadir',
    'pemagangBelum',
    'presensiTidakHadir',
    'activeKet' => null,
    'indexRoute',
])

@php
    $tabUrl = function ($t) use ($indexRoute) {
        return route($indexRoute, array_filter([
            'tanggal' => request('tanggal'),
            'kantor' => request('kantor'),
            'shift' => request('shift'),
            'divisi' => request('divisi'),
            'search' => request('search'),
            'keterangan' => $t === 'hadir' ? request('keterangan') : null,
            'tab' => $t,
        ]));
    };

    $tabs = [
        'hadir' => ['label' => 'Sudah Dipresensi', 'count' => $presensiHadir->total(), 'dot' => 'bg-emerald-500'],
        'belum' => ['label' => 'Belum Dipresensi', 'count' => $pemagangBelum->total(), 'dot' => 'bg-gray-400'],
        'tidak_hadir' => ['label' => 'Tidak Hadir', 'count' => $presensiTidakHadir->total(), 'dot' => 'bg-red-500'],
    ];
@endphp

{{-- ── Tab Bar ─────────────────────────────────────────────────── --}}
<div class="flex items-center gap-1 sm:gap-2 border-b border-gray-200 mb-5 overflow-x-auto overflow-y-hidden -mx-1 px-1">
    @foreach($tabs as $key => $t)
        <a href="{{ $tabUrl($key) }}"
            class="inline-flex items-center gap-2 px-3 sm:px-4 py-2.5 text-xs sm:text-sm font-semibold whitespace-nowrap border-b-2 -mb-px transition
                {{ $tab === $key
                    ? 'border-primary-600 text-primary-700'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            <span class="w-2 h-2 rounded-full {{ $t['dot'] }} flex-shrink-0"></span>
            {{ $t['label'] }}
            <span
                class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $tab === $key ? 'bg-primary-100 text-primary-700' : 'bg-gray-100 text-gray-500' }}">
                {{ $t['count'] }}
            </span>
        </a>
    @endforeach
</div>

{{-- ══ TAB 1: SUDAH DIPRESENSI ══════════════════════════════════ --}}
@if($tab === 'hadir')
    <div id="tabel-hadir" class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm scroll-mt-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/70 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-gray-800">Daftar Pemagang Hadir</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Pemagang yang hadir (Lebih Awal, Tepat Waktu, Terlambat)</p>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-wrap justify-end">
                @if($activeKet)
                    <a href="{{ request()->fullUrlWithoutQuery(['keterangan', 'page_hadir']) }}"
                        class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-full border border-gray-300 transition"
                        title="Hapus filter">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Reset filter
                    </a>
                @endif
                {{-- Search khusus tab Hadir --}}
                <form method="GET" action="{{ route($indexRoute) }}" class="flex items-center">
                    <input type="hidden" name="tab" value="hadir">
                    <input type="hidden" name="tanggal" value="{{ e(request('tanggal')) }}">
                    <input type="hidden" name="kantor" value="{{ e(request('kantor')) }}">
                    <input type="hidden" name="shift" value="{{ e(request('shift')) }}">
                    <input type="hidden" name="divisi" value="{{ e(request('divisi')) }}">
                    <input type="hidden" name="keterangan" value="{{ e(request('keterangan')) }}">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none text-gray-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="search" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama pemagang..."
                            class="w-36 lg:w-48 pl-8 pr-2.5 py-1.5 text-xs bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <button type="submit"
                        class="ml-1.5 px-2.5 py-1.5 text-xs font-semibold bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition">
                        Cari
                    </button>
                </form>
                <span class="text-xs font-semibold px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full border border-emerald-200">
                    {{ $presensiHadir->total() }} Hadir
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[700px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <th class="px-6 py-3.5 w-12 text-center">No</th>
                        <th class="px-6 py-3.5">Nama Pemagang</th>
                        <th class="px-6 py-3.5">Divisi</th>
                        <th class="px-6 py-3.5">Kantor</th>
                        <th class="px-6 py-3.5">Shift</th>
                        <th class="px-6 py-3.5">Waktu Masuk</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($presensiHadir as $index => $presensi)
                        @php
                            $pemagang = $presensi->pemagang;
                            $badgeStyle = match ($presensi->keterangan) {
                                'Lebih Awal' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                'Tepat Waktu' => 'bg-green-50 text-green-700 border-green-200',
                                'Terlambat' => 'bg-amber-50 text-amber-700 border-amber-200',
                                default => 'bg-gray-50 text-gray-700 border-gray-200',
                            };
                            $shiftStyle = match ($presensi->shift) {
                                'Pagi' => 'bg-blue-50 text-blue-700',
                                'Middle' => 'bg-purple-50 text-purple-700',
                                'Siang' => 'bg-orange-50 text-orange-700',
                                default => 'bg-gray-100 text-gray-700',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="px-6 py-4 text-center text-xs text-gray-400 font-medium">
                                {{ $presensiHadir->firstItem() ? ($presensiHadir->firstItem() + $index) : ($index + 1) }}
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-800 text-sm">
                                    {{ $pemagang ? $pemagang->nama_lengkap : 'Pemagang Dihapus' }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $pemagang ? $pemagang->kampus : '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-700">
                                    {{ $pemagang ? $pemagang->divisi : '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-50 text-blue-700">
                                    {{ $presensi->kantor ?? 'Kantor 1' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $shiftStyle }}">
                                    {{ $presensi->shift }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-medium text-gray-700">{{ substr($presensi->waktu_masuk, 0, 5) }} WIB</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $badgeStyle }}">
                                    @if($presensi->keterangan == 'Tepat Waktu' || $presensi->keterangan == 'Lebih Awal')
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                    @elseif($presensi->keterangan == 'Terlambat')
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                    @endif
                                    {{ $presensi->keterangan }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" onclick="openEditModal(
                                            {{ $presensi->id }},
                                            {{ $presensi->pemagang_id }},
                                            '{{ addslashes($pemagang ? $pemagang->nama_lengkap : '') }}',
                                            '{{ $presensi->tanggal }}',
                                            '{{ $presensi->shift }}',
                                            '{{ substr($presensi->waktu_masuk, 0, 5) }}',
                                            '{{ $presensi->keterangan }}',
                                            '{{ addslashes($presensi->notes ?: '') }}',
                                            '{{ $presensi->kantor ?? 'Kantor 1' }}'
                                        )"
                                        class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                                        title="Edit Presensi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button type="button" onclick="openDeleteModal(
                                            {{ $presensi->id }},
                                            '{{ addslashes($pemagang ? $pemagang->nama_lengkap : 'Pemagang') }}',
                                            '{{ $presensi->shift }}',
                                            '{{ substr($presensi->waktu_masuk, 0, 5) }}'
                                        )"
                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                        title="Hapus Presensi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-gray-400">
                                <p class="font-medium text-gray-600 text-sm">Belum ada pemagang yang hadir pada tanggal ini</p>
                                <p class="text-xs text-gray-400 mt-1">Gunakan tombol "Catat Presensi" untuk menginput data kehadiran.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($presensiHadir->hasPages())
            <div class="px-6 py-4 pr-20 sm:pr-24 border-t border-gray-200 bg-gray-50/50 print:hidden">
                {{ $presensiHadir->links() }}
            </div>
        @endif
    </div>
@endif

{{-- ══ TAB 2: BELUM DIPRESENSI ══════════════════════════════════ --}}
@if($tab === 'belum')
    <div id="tabel-belum" class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm scroll-mt-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/70 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-2.5 h-2.5 rounded-full bg-gray-400"></div>
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-gray-800">Daftar Pemagang Belum Dipresensi</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Pemagang aktif yang belum tercatat presensinya pada tanggal ini</p>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-wrap justify-end">
                {{-- Search khusus tab Belum --}}
                <form method="GET" action="{{ route($indexRoute) }}" class="flex items-center">
                    <input type="hidden" name="tab" value="belum">
                    <input type="hidden" name="tanggal" value="{{ e(request('tanggal')) }}">
                    <input type="hidden" name="kantor" value="{{ e(request('kantor')) }}">
                    <input type="hidden" name="shift" value="{{ e(request('shift')) }}">
                    <input type="hidden" name="divisi" value="{{ e(request('divisi')) }}">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none text-gray-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="search" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama pemagang..."
                            class="w-36 lg:w-48 pl-8 pr-2.5 py-1.5 text-xs bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <button type="submit"
                        class="ml-1.5 px-2.5 py-1.5 text-xs font-semibold bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition">
                        Cari
                    </button>
                </form>
                <span class="text-xs font-semibold px-3 py-1 bg-gray-100 text-gray-600 rounded-full border border-gray-200">
                    {{ $pemagangBelum->total() }} Belum
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[650px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <th class="px-6 py-3.5 w-12 text-center">No</th>
                        <th class="px-6 py-3.5">Nama Pemagang</th>
                        <th class="px-6 py-3.5">No. WhatsApp</th>
                        <th class="px-6 py-3.5">Asal Kampus</th>
                        <th class="px-6 py-3.5">Divisi</th>
                        <th class="px-6 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pemagangBelum as $index => $p)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="px-6 py-4 text-center text-xs text-gray-400 font-medium">
                                {{ $pemagangBelum->firstItem() ? ($pemagangBelum->firstItem() + $index) : ($index + 1) }}
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-800 text-sm">{{ $p->nama_lengkap }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-medium text-gray-700 font-mono">{{ $p->no_hp ?: '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-600">{{ $p->kampus ?: '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-700">
                                    {{ $p->divisi ?: '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button type="button"
                                    onclick="openCreateModalFor({{ $p->id }}, '{{ addslashes($p->nama_lengkap) }}', '{{ addslashes($p->kampus ?? '') }}', '{{ addslashes($p->divisi ?? '') }}')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-lg transition shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Catat
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400">
                                <div class="flex items-center justify-center gap-2 text-emerald-600 font-medium text-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Semua pemagang sudah tercatat presensinya pada tanggal ini!</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pemagangBelum->hasPages())
            <div class="px-6 py-4 pr-20 sm:pr-24 border-t border-gray-200 bg-gray-50/50 print:hidden">
                {{ $pemagangBelum->links() }}
            </div>
        @endif
    </div>
@endif

{{-- ══ TAB 3: TIDAK HADIR ═══════════════════════════════════════ --}}
@if($tab === 'tidak_hadir')
    <div id="tabel-tidak-hadir" class="bg-white rounded-xl border border-red-200 overflow-hidden shadow-sm scroll-mt-6">
        <div class="px-6 py-4 border-b border-red-100 bg-red-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div class="flex items-center gap-2.5">
                <div class="w-2.5 h-2.5 rounded-full bg-red-500"></div>
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-gray-900">Daftar Pemagang Tidak Hadir</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Pemagang yang tidak hadir hari ini &bull; Klik tombol WhatsApp untuk konfirmasi</p>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-wrap justify-end">
                {{-- Search khusus tab Tidak Hadir --}}
                <form method="GET" action="{{ route($indexRoute) }}" class="flex items-center">
                    <input type="hidden" name="tab" value="tidak_hadir">
                    <input type="hidden" name="tanggal" value="{{ e(request('tanggal')) }}">
                    <input type="hidden" name="kantor" value="{{ e(request('kantor')) }}">
                    <input type="hidden" name="shift" value="{{ e(request('shift')) }}">
                    <input type="hidden" name="divisi" value="{{ e(request('divisi')) }}">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none text-gray-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="search" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama pemagang..."
                            class="w-36 lg:w-48 pl-8 pr-2.5 py-1.5 text-xs bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <button type="submit"
                        class="ml-1.5 px-2.5 py-1.5 text-xs font-semibold bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition">
                        Cari
                    </button>
                </form>
                <span class="text-xs font-semibold px-3 py-1 bg-red-100 text-red-700 rounded-full border border-red-200 self-start sm:self-auto">
                    {{ $presensiTidakHadir->total() }} Tidak Hadir
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[750px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <th class="px-6 py-3.5 w-12 text-center">No</th>
                        <th class="px-6 py-3.5">Nama Pemagang</th>
                        <th class="px-6 py-3.5">Divisi</th>
                        <th class="px-6 py-3.5">Shift</th>
                        <th class="px-6 py-3.5">No. WhatsApp</th>
                        <th class="px-6 py-3.5">Keterangan / Alasan</th>
                        <th class="px-6 py-3.5 text-center">Hubungi Pemagang</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($presensiTidakHadir as $index => $presensi)
                        @php
                            $pemagang = $presensi->pemagang;
                            $shiftStyle = match ($presensi->shift) {
                                'Pagi' => 'bg-blue-50 text-blue-700',
                                'Middle' => 'bg-purple-50 text-purple-700',
                                'Siang' => 'bg-orange-50 text-orange-700',
                                default => 'bg-gray-100 text-gray-700',
                            };
                        @endphp
                        <tr class="hover:bg-red-50/30 transition">
                            <td class="px-6 py-4 text-center text-xs text-gray-400 font-medium">
                                {{ $presensiTidakHadir->firstItem() ? ($presensiTidakHadir->firstItem() + $index) : ($index + 1) }}
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-900 text-sm">
                                    {{ $pemagang ? $pemagang->nama_lengkap : 'Pemagang Dihapus' }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $pemagang ? $pemagang->kampus : '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-700">
                                    {{ $pemagang ? $pemagang->divisi : '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $shiftStyle }}">
                                    {{ $presensi->shift }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-medium text-gray-700 font-mono">{{ $pemagang ? $pemagang->no_hp : '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-xs text-red-600 max-w-[200px] truncate" title="{{ $presensi->notes }}">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>{{ $presensi->notes ?: 'Tidak ada keterangan' }}</span>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($pemagang && $pemagang->no_hp)
                                    <a href="{{ $pemagang->wa_url }}" target="_blank"
                                        class="inline-flex items-center gap-2 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-sm transition hover:shadow-md">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                                        </svg>
                                        <span>WhatsApp</span>
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400 italic">No. HP tidak ada</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" onclick="openEditModal(
                                            {{ $presensi->id }},
                                            {{ $presensi->pemagang_id }},
                                            '{{ addslashes($pemagang ? $pemagang->nama_lengkap : '') }}',
                                            '{{ $presensi->tanggal }}',
                                            '{{ $presensi->shift }}',
                                            '{{ substr($presensi->waktu_masuk, 0, 5) }}',
                                            '{{ $presensi->keterangan }}',
                                            '{{ addslashes($presensi->notes ?: '') }}',
                                            '{{ $presensi->kantor ?? 'Kantor 1' }}'
                                        )"
                                        class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                                        title="Ubah Status Presensi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button type="button" onclick="openDeleteModal(
                                            {{ $presensi->id }},
                                            '{{ addslashes($pemagang ? $pemagang->nama_lengkap : 'Pemagang') }}',
                                            '{{ $presensi->shift }}',
                                            '{{ substr($presensi->waktu_masuk, 0, 5) }}'
                                        )"
                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                        title="Hapus Data">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-400">
                                <div class="flex items-center justify-center gap-2 text-emerald-600 font-medium text-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Semua pemagang hadir pada tanggal ini! Tidak ada yang absen.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($presensiTidakHadir->hasPages())
            <div class="px-6 py-4 pr-20 sm:pr-24 border-t border-red-100 bg-red-50/30 print:hidden">
                {{ $presensiTidakHadir->links() }}
            </div>
        @endif
    </div>
@endif
