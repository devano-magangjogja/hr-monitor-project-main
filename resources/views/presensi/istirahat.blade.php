@extends('layouts.app')

@section('title', 'Presensi Istirahat')
@section('page-title', 'Presensi Istirahat')
@section('page-subtitle', 'Catat kedatangan pemagang setelah masa istirahat')
@section('sidebar')
    @include($prefix === 'admin' ? 'components.sidebar-admin' : ($prefix === 'staff' ? 'components.sidebar-staff' : 'components.sidebar-assistant'))
@endsection

@section('content')
@php
    $baseQuery = array_filter([
        'tanggal' => $tanggal ?? null,
        'kantor'  => $kantor ?? null,
        'search'  => $search !== '' ? $search : null,
    ]);
    $currentFilter = request('filter', 'all');
@endphp

{{-- Header --}}
<div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
    <div class="min-w-0">
        <h2 class="text-base sm:text-lg font-bold text-gray-800">Kembali dari Istirahat</h2>
        <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
            Peserta otomatis diambil dari presensi masuk tanggal {{ $formattedDate }}.
        </p>
    </div>
    <div class="flex flex-wrap items-center gap-2 shrink-0">
        <button type="button" onclick="openBulkReturnModal()"
                class="h-10 w-full sm:w-auto rounded-lg bg-emerald-600 px-4 text-sm font-semibold text-white hover:bg-emerald-700">
            Tandai Semua Kembali
        </button>
    </div>
</div>

{{-- STAT CARDS = FILTER (proporsional 3 kolom) --}}
<div class="grid grid-cols-1 xs:grid-cols-3 sm:grid-cols-3 gap-3 mb-6">
    <a href="{{ route($prefix . '.presensi.istirahat', array_merge($baseQuery, ['filter' => 'all'])) }}"
       class="rounded-xl border p-3.5 sm:p-4 shadow-sm transition hover:shadow-md
              {{ $currentFilter === 'all' ? 'border-primary-400 bg-primary-50 ring-2 ring-primary-400/30' : 'border-gray-200 bg-white' }}">
        <p class="text-[11px] sm:text-xs font-medium {{ $currentFilter === 'all' ? 'text-primary-700' : 'text-gray-500' }}">
            Peserta Istirahat
        </p>
        <p class="mt-1 text-xl sm:text-2xl font-bold {{ $currentFilter === 'all' ? 'text-primary-700' : 'text-gray-800' }}">
            {{ $stats['peserta'] }}
        </p>
        <p class="text-[10px] text-gray-400 mt-0.5">semua peserta masuk</p>
    </a>

    <a href="{{ route($prefix . '.presensi.istirahat', array_merge($baseQuery, ['filter' => 'kembali'])) }}"
       class="rounded-xl border p-3.5 sm:p-4 shadow-sm transition hover:shadow-md
              {{ $currentFilter === 'kembali' ? 'border-emerald-400 bg-emerald-50 ring-2 ring-emerald-400/30' : 'border-emerald-200 bg-emerald-50/80' }}">
        <p class="text-[11px] sm:text-xs font-medium text-emerald-700">Sudah Kembali</p>
        <p class="mt-1 text-xl sm:text-2xl font-bold text-emerald-700">{{ $stats['kembali'] }}</p>
        <p class="text-[10px] text-emerald-600/70 mt-0.5">tepat waktu</p>
    </a>

    <a href="{{ route($prefix . '.presensi.istirahat', array_merge($baseQuery, ['filter' => 'terlambat'])) }}"
       class="rounded-xl border p-3.5 sm:p-4 shadow-sm transition hover:shadow-md
              {{ $currentFilter === 'terlambat' ? 'border-amber-400 bg-amber-50 ring-2 ring-amber-400/30' : 'border-amber-200 bg-amber-50/80' }}">
        <p class="text-[11px] sm:text-xs font-medium text-amber-700">Terlambat Kembali</p>
        <p class="mt-1 text-xl sm:text-2xl font-bold text-amber-700">{{ $stats['terlambat'] }}</p>
        <p class="text-[10px] text-amber-600/70 mt-0.5">perlu perhatian</p>
    </a>
</div>

{{-- Form cari terlambat --}}
@if($search !== '')
<div class="mb-6 rounded-xl border border-amber-200 bg-amber-50/60 p-4">
    <h3 class="text-sm font-bold text-amber-800">Tandai Pemagang Terlambat</h3>
    @forelse($lateCandidates as $candidate)
        <form method="POST" action="{{ route($prefix . '.presensi.istirahat.late') }}"
              class="mt-3 flex flex-col gap-2 rounded-lg border border-amber-200 bg-white p-3 sm:flex-row sm:items-center">
            @csrf
            <input type="hidden" name="entry_id" value="{{ $candidate->id }}">
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold text-gray-800">{{ $candidate->pemagang?->nama_lengkap }}</p>
                <p class="text-xs text-gray-500">{{ $candidate->kantor }} &middot; Presensi masuk {{ substr($candidate->waktu_masuk, 0, 5) }}</p>
            </div>
            <input type="time" name="waktu_kembali" lang="id" value="{{ now()->format('H:i') }}" required
                   class="h-8 rounded border border-gray-300 px-2 text-xs w-full sm:w-auto">
            <button class="h-8 rounded bg-amber-600 px-3 text-xs font-semibold text-white hover:bg-amber-700 w-full sm:w-auto">
                Tandai Terlambat
            </button>
        </form>
    @empty
        <p class="mt-2 text-xs text-amber-700">Tidak ada peserta presensi masuk yang cocok atau semuanya sudah dicatat.</p>
    @endforelse
</div>
@endif

{{-- TABEL + SEARCH --}}
<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 bg-gray-50 px-4 sm:px-5 py-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <h3 class="font-bold text-gray-800 text-sm sm:text-base">Status Kembali Pemagang</h3>
                <p class="text-xs text-gray-500 mt-0.5">Gunakan aksi per-orang untuk mencatat pemagang yang terlambat kembali.</p>
            </div>

            <form method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                @if(!empty($tanggal))
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                @endif
                @if(!empty($kantor))
                    <input type="hidden" name="kantor" value="{{ $kantor }}">
                @endif
                @if($currentFilter !== 'all')
                    <input type="hidden" name="filter" value="{{ $currentFilter }}">
                @endif
                <input type="search" name="search" value="{{ $search }}"
                       placeholder="Cari nama, HP, kampus..."
                       class="h-9 flex-1 sm:w-52 rounded-lg border border-gray-300 px-3 text-sm focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500">
                <button type="submit"
                        class="h-9 rounded-lg bg-primary-600 px-3 text-sm font-semibold text-white hover:bg-primary-700 whitespace-nowrap shrink-0">
                    Cari
                </button>
                @if($search !== '')
                    <a href="{{ route($prefix . '.presensi.istirahat', array_merge($baseQuery, ['filter' => $currentFilter, 'search' => null])) }}"
                       class="h-9 w-9 inline-flex items-center justify-center rounded-lg bg-gray-100 text-gray-500 hover:bg-gray-200 hover:text-gray-700 shrink-0"
                       title="Reset pencarian">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                @endif
            </form>
        </div>
    </div>

    {{-- Desktop table (md+) --}}
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full min-w-[700px] text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-5 py-3">Pemagang</th>
                    <th class="px-5 py-3">Kantor</th>
                    <th class="px-5 py-3">Presensi Masuk</th>
                    <th class="px-5 py-3">Status Istirahat</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($entries as $entry)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-4">
                        <div class="font-semibold text-gray-800">{{ $entry->pemagang?->nama_lengkap ?? 'Pemagang Dihapus' }}</div>
                        <div class="text-xs text-gray-400">{{ $entry->pemagang?->kampus ?? '-' }}</div>
                    </td>
                    <td class="px-5 py-4 text-xs text-gray-600">{{ $entry->kantor ?: '-' }}</td>
                    <td class="px-5 py-4 text-xs text-gray-600">
                        {{ $entry->entry ? substr($entry->entry->waktu_masuk, 0, 5) : '-' }} WIB
                    </td>
                    <td class="px-5 py-4">
                        @if($entry->keterangan === 'Terlambat')
                            <span class="rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                Terlambat, {{ substr($entry->waktu_masuk, 0, 5) }}
                            </span>
                        @else
                            <span class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                Kembali {{ substr($entry->waktu_masuk, 0, 5) }}
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-right">
                        @if($entry->keterangan === 'Terlambat')
                            <form method="POST" action="{{ route($prefix . '.presensi.istirahat.correct-late') }}" class="inline">
                                @csrf
                                <input type="hidden" name="break_id" value="{{ $entry->id }}">
                                <button type="submit"
                                        class="h-8 rounded bg-gray-100 px-3 text-xs font-semibold text-gray-600 hover:bg-gray-200"
                                        onclick="return confirm('Batalkan status terlambat untuk pemagang ini?')">
                                    Batalkan Terlambat
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route($prefix . '.presensi.istirahat.late') }}"
                                  class="inline-flex items-center gap-2">
                                @csrf
                                <input type="hidden" name="break_id" value="{{ $entry->id }}">
                                @if($entry->entry)
                                    <input type="hidden" name="entry_id" value="{{ $entry->entry->id }}">
                                @endif
                                <input type="time" name="waktu_kembali" lang="id"
                                        value="{{ $entry->waktu_masuk ? substr($entry->waktu_masuk, 0, 5) : now()->format('H:i') }}"
                                        required class="h-8 rounded border border-gray-300 px-2 text-xs">
                                <button class="h-8 rounded bg-amber-600 px-3 text-xs font-semibold text-white hover:bg-amber-700">
                                    Tandai Terlambat
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-12 text-center text-sm text-gray-400">
                        @if($currentFilter === 'kembali')
                            Tidak ada pemagang yang sudah kembali.
                        @elseif($currentFilter === 'terlambat')
                            Tidak ada pemagang yang terlambat kembali.
                        @else
                            Belum ada pemagang yang dicatat kembali dari istirahat.
                        @endif
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile cards (< md) --}}
    <div class="md:hidden divide-y divide-gray-100">
        @forelse($entries as $entry)
            <div class="p-4 space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-800 text-sm truncate">
                            {{ $entry->pemagang?->nama_lengkap ?? 'Pemagang Dihapus' }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $entry->pemagang?->kampus ?? '-' }}</p>
                    </div>
                    @if($entry->keterangan === 'Terlambat')
                        <span class="shrink-0 rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-[11px] font-semibold text-amber-700">
                            Terlambat, {{ substr($entry->waktu_masuk, 0, 5) }}
                        </span>
                    @else
                        <span class="shrink-0 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">
                            Kembali {{ substr($entry->waktu_masuk, 0, 5) }}
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <p class="text-gray-400">Kantor</p>
                        <p class="font-medium text-gray-700 mt-0.5">{{ $entry->kantor ?: '-' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-400">Presensi Masuk</p>
                        <p class="font-medium text-gray-700 mt-0.5">
                            {{ $entry->entry ? substr($entry->entry->waktu_masuk, 0, 5) : '-' }} WIB
                        </p>
                    </div>
                </div>

                <div class="pt-1">
                    @if($entry->keterangan === 'Terlambat')
                        <form method="POST" action="{{ route($prefix . '.presensi.istirahat.correct-late') }}">
                            @csrf
                            <input type="hidden" name="break_id" value="{{ $entry->id }}">
                            <button type="submit"
                                    class="w-full h-9 rounded-lg bg-gray-100 text-xs font-semibold text-gray-600 hover:bg-gray-200"
                                    onclick="return confirm('Batalkan status terlambat untuk pemagang ini?')">
                                Batalkan Terlambat
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route($prefix . '.presensi.istirahat.late') }}"
                              class="flex items-center gap-2">
                            @csrf
                            <input type="hidden" name="break_id" value="{{ $entry->id }}">
                            @if($entry->entry)
                                <input type="hidden" name="entry_id" value="{{ $entry->entry->id }}">
                            @endif
                            <input type="time" name="waktu_kembali" lang="id"
                                   value="{{ $entry->waktu_masuk ? substr($entry->waktu_masuk, 0, 5) : now()->format('H:i') }}"
                                   required class="h-9 flex-1 rounded-lg border border-gray-300 px-2 text-xs">
                            <button class="h-9 rounded-lg bg-amber-600 px-3 text-xs font-semibold text-white hover:bg-amber-700 whitespace-nowrap">
                                Tandai Terlambat
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="px-4 py-12 text-center text-sm text-gray-400">
                @if($currentFilter === 'kembali')
                    Tidak ada pemagang yang sudah kembali.
                @elseif($currentFilter === 'terlambat')
                    Tidak ada pemagang yang terlambat kembali.
                @else
                    Belum ada pemagang yang dicatat kembali dari istirahat.
                @endif
            </div>
        @endforelse
    </div>

    @if($entries->hasPages())
        <div class="border-t border-gray-100 px-4 sm:px-5 py-4">{{ $entries->links() }}</div>
    @endif
</div>

{{-- Modal Bulk Return --}}
<div id="modal-bulk-return" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
    <form method="POST" action="{{ route($prefix . '.presensi.istirahat.bulk') }}"
          class="w-full max-w-lg max-h-[90vh] overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-2xl flex flex-col">
        @csrf
        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
        @if($kantor)<input type="hidden" name="kantor" value="{{ $kantor }}">@endif

        <div class="flex items-start justify-between border-b border-gray-200 bg-gray-50 px-4 sm:px-5 py-4 shrink-0">
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 text-sm sm:text-base">Konfirmasi Kembali</h3>
                    <p class="mt-0.5 text-xs text-gray-500">Catat semua peserta sebagai kembali tepat waktu.</p>
                </div>
            </div>
            <button type="button" onclick="closeBulkReturnModal()"
                    class="rounded-lg p-1 text-xl leading-none text-gray-400 hover:bg-gray-200 hover:text-gray-700">
                &times;
            </button>
        </div>

        <div class="px-4 sm:px-5 py-4 overflow-y-auto flex-1">
            {{-- Pilih Waktu Kembali --}}
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                    Waktu Kembali <span class="text-red-500">*</span>
                </label>
                <input type="time" id="modal-bulk-waktu-kembali" name="waktu_kembali"
                    lang="id"
                    value="{{ ($defaultBreakTime ?? null) ?: now()->format('H:i') }}"
                    required
                    class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500">
                
                {{-- Info AM / PM --}}
                <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-[11px] text-gray-500">
                    <span><strong class="text-gray-700">AM</strong>: 00.00 – 12.00</span>
                    <span><strong class="text-gray-700">PM</strong>: 12.01 – 23.59</span>
                </div>
            </div>

            <div class="mb-3 flex items-center justify-between rounded-xl border border-emerald-100 bg-emerald-50 px-3 py-2.5">
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-emerald-800">Peserta yang akan dicatat</p>
                    <p class="text-[11px] text-emerald-700">
                        Waktu kembali:
                        <span id="modal-bulk-time-label">{{ ($defaultBreakTime ?? null) ?: now()->format('H:i') }}</span> WIB
                    </p>
                </div>
                <span class="rounded-full bg-white px-2.5 py-1 text-xs font-bold text-emerald-700 shadow-sm shrink-0">
                    {{ $pendingEntries->count() }} orang
                </span>
            </div>

            <div class="max-h-64 sm:max-h-[23rem] overflow-y-auto rounded-xl border border-gray-200 bg-gray-50 px-3 py-1">
                @forelse($pendingEntries as $pending)
                    <div class="flex items-center justify-between gap-3 border-b border-gray-200 py-2.5 last:border-0">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-gray-800">
                                {{ $pending->pemagang?->nama_lengkap ?? 'Pemagang Dihapus' }}
                            </p>
                            <p class="truncate text-xs text-gray-500">
                                {{ $pending->kantor ?: '-' }} &middot; Masuk {{ substr($pending->waktu_masuk, 0, 5) }}
                            </p>
                        </div>
                        <span class="shrink-0 rounded-full bg-white px-2 py-1 text-[11px] text-gray-500">Pending</span>
                    </div>
                @empty
                    <p class="py-8 text-center text-sm text-gray-400">Semua peserta sudah dicatat kembali.</p>
                @endforelse
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 border-t border-gray-200 px-4 sm:px-5 py-4 shrink-0">
            <button type="button" onclick="closeBulkReturnModal()"
                    class="w-full sm:w-auto rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                Batal
            </button>
            <button type="submit"
                    @if($pendingEntries->isEmpty()) disabled @endif
                    class="w-full sm:w-auto rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50">
                Konfirmasi &amp; Tandai Semua
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    (() => {
        const storageKey = `presensi-istirahat-waktu:{{ $prefix }}:{{ $tanggal }}:{{ $kantor ?: 'all' }}`;
        const timeInput = document.getElementById('modal-bulk-waktu-kembali');
        const timeLabel = document.getElementById('modal-bulk-time-label');

        const updateTimeUI = (val) => {
            if (timeLabel && val) {
                timeLabel.textContent = val;
            }
        };

        window.openBulkReturnModal = () => {
            const modal = document.getElementById('modal-bulk-return');

            // Ambil dari localStorage kalau ada
            const savedTime = localStorage.getItem(storageKey);
            if (savedTime && timeInput) {
                timeInput.value = savedTime;
            }

            // Update label
            if (timeInput) {
                updateTimeUI(timeInput.value);
            }

            modal?.classList.remove('hidden');
        };

        window.closeBulkReturnModal = () => {
            document.getElementById('modal-bulk-return')?.classList.add('hidden');
        };

        // Update label + simpan ke localStorage saat waktu diubah (input & change)
        if (timeInput) {
            ['input', 'change'].forEach(evt => {
                timeInput.addEventListener(evt, function () {
                    updateTimeUI(this.value);
                    if (this.value) localStorage.setItem(storageKey, this.value);
                });
            });
        }

        // Tutup modal kalau klik area gelap
        document.getElementById('modal-bulk-return')?.addEventListener('click', function (e) {
            if (e.target === this) closeBulkReturnModal();
        });
    })();
</script>
@endpush