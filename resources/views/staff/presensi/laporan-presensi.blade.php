@extends('layouts.app')

@section('title', 'Laporan Presensi Pemagang')
@section('page-title', 'Laporan Presensi Pemagang')
@section('page-subtitle', 'Rekapitulasi dan analisis kedisiplinan kehadiran anak magang')

@section('sidebar')
    @include('components.sidebar-staff')
@endsection

@section('content')

    {{-- ── Action Header & Filter (Sembunyi saat Print) ─────────── --}}
    <div class="print:hidden flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <p class="text-sm text-gray-500">
                Menampilkan data rekapitulasi untuk <span
                    class="font-semibold text-gray-700">{{ $stats['total_pemagang'] }}</span> anak magang.
            </p>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('staff.presensi.index') }}"
                class="flex items-center gap-2 px-3.5 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs sm:text-sm font-medium rounded-lg transition shadow-sm">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kelola Presensi</span>
            </a>

            <button onclick="window.print()"
                class="flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs sm:text-sm font-medium rounded-lg transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak / Ekspor Laporan</span>
            </button>
        </div>
    </div>

    {{-- ── Stat Cards Summary ─────────────────────────────────── --}}
    <div
        class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 print:grid-cols-4 gap-4 print:gap-3 mb-6 print:mb-5 print-avoid-break">

        {{-- Disiplin Rate --}}
        <div
            class="bg-white rounded-xl border border-gray-200 print:border-gray-300 p-4 sm:p-5 print:p-3 shadow-sm print:shadow-none">
            <div class="flex items-center justify-between mb-2 print:mb-1">
                <p class="text-xs sm:text-sm print:text-xs font-medium text-gray-500">Tingkat Kedisiplinan</p>
                <div class="w-8 h-8 print:hidden rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl print:text-xl font-bold text-green-600">{{ $stats['avg_rate'] }}%</p>
            <div class="w-full bg-gray-100 rounded-full h-1.5 mt-2 print:hidden overflow-hidden">
                <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ min(100, $stats['avg_rate']) }}%"></div>
            </div>
            <p class="text-[11px] print:text-[10px] text-gray-400 mt-1">Hadir tepat waktu & awal</p>
        </div>

        {{-- Total Hadir Tepat Waktu & Awal --}}
        <div
            class="bg-white rounded-xl border border-gray-200 print:border-gray-300 p-4 sm:p-5 print:p-3 shadow-sm print:shadow-none">
            <div class="flex items-center justify-between mb-2 print:mb-1">
                <p class="text-xs sm:text-sm print:text-xs font-medium text-gray-500">Hadir Tepat / Awal</p>
                <div class="w-8 h-8 print:hidden rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl print:text-xl font-bold text-gray-800">
                {{ $stats['datang_awal'] + $stats['tepat_waktu'] }}
            </p>
            <p class="text-[11px] print:text-[10px] text-gray-400 mt-1">
                {{ $stats['datang_awal'] }} awal &bull; {{ $stats['tepat_waktu'] }} tepat
            </p>
        </div>

        {{-- Total Terlambat --}}
        <div
            class="bg-white rounded-xl border border-gray-200 print:border-gray-300 p-4 sm:p-5 print:p-3 shadow-sm print:shadow-none">
            <div class="flex items-center justify-between mb-2 print:mb-1">
                <p class="text-xs sm:text-sm print:text-xs font-medium text-gray-500">Total Terlambat</p>
                <div class="w-8 h-8 print:hidden rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl print:text-xl font-bold text-amber-600">{{ $stats['terlambat'] }}</p>
            <p class="text-[11px] print:text-[10px] text-gray-400 mt-1">Presensi terlambat</p>
        </div>

        {{-- Total Tidak Hadir --}}
        <div
            class="bg-white rounded-xl border border-gray-200 print:border-gray-300 p-4 sm:p-5 print:p-3 shadow-sm print:shadow-none">
            <div class="flex items-center justify-between mb-2 print:mb-1">
                <p class="text-xs sm:text-sm print:text-xs font-medium text-gray-500">Tidak Hadir</p>
                <div class="w-8 h-8 print:hidden rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl print:text-xl font-bold text-red-600">{{ $stats['tidak_hadir'] }}</p>
            <p class="text-[11px] print:text-[10px] text-gray-400 mt-1">Alpa / izin / sakit</p>
        </div>

    </div>

    {{-- ── Filter Bar Laporan (Sembunyi saat Print) ─────────────── --}}
    <div class="print:hidden bg-white rounded-xl border border-gray-200 p-4 mb-6 shadow-sm">
        <form method="GET" action="{{ route('staff.presensi.laporan') }}"
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
            <input type="hidden" name="tab" value="{{ request('tab', 'rekapitulasi') }}">

            {{-- Search input --}}
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama pemagang / no hp..."
                    class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition">
            </div>

            {{-- Filter Kantor --}}
            <div>
                <select name="kantor" onchange="this.form.submit()"
                    class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition text-gray-700">
                    <option value="">Semua Lokasi Kantor</option>
                    @foreach ($kantorList as $k)
                        <option value="{{ $k }}" {{ request('kantor') == $k ? 'selected' : '' }}>
                            {{ $k }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Divisi --}}
            <div>
                <select name="divisi" onchange="this.form.submit()"
                    class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition text-gray-700">
                    <option value="">Semua Divisi</option>
                    @foreach ($divisiList as $div)
                        <option value="{{ $div }}" {{ request('divisi') == $div ? 'selected' : '' }}>
                            {{ $div }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Shift --}}
            <div class="flex items-center gap-2">
                <select name="shift" onchange="this.form.submit()"
                    class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition text-gray-700">
                    <option value="">Semua Shift</option>
                    <option value="Pagi" {{ request('shift') == 'Pagi' ? 'selected' : '' }}>Shift Pagi</option>
                    <option value="Middle" {{ request('shift') == 'Middle' ? 'selected' : '' }}>Shift Middle</option>
                    <option value="Siang" {{ request('shift') == 'Siang' ? 'selected' : '' }}>Shift Siang</option>
                </select>

                @if (request()->hasAny(['search', 'divisi', 'shift', 'keterangan']))
                    <a href="{{ route('staff.presensi.laporan', ['tab' => request('tab', 'rekapitulasi')]) }}"
                        class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                        title="Reset Filter">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ── TAB NAVIGATION ─────────────────────────────────── --}}
    <div class="print:hidden bg-white rounded-t-xl border border-b-0 border-gray-200 overflow-hidden mb-0 shadow-sm">
        <div class="flex border-b border-gray-200 overflow-x-auto scrollbar-none">
            <a href="{{ route('staff.presensi.laporan') }}"
                class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 transition
                  {{ !request('tab') || request('tab') === 'rekapitulasi' ? 'border-primary-600 text-primary-600 bg-primary-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Rekapitulasi Pemagang
            </a>
            <a href="{{ route('staff.presensi.laporan', ['tab' => 'presensi_masuk']) }}"
                class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 transition
                  {{ request('tab') === 'presensi_masuk' ? 'border-emerald-600 text-emerald-600 bg-emerald-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                Presensi Masuk
            </a>
            <a href="{{ route('staff.presensi.laporan', ['tab' => 'presensi_istirahat']) }}"
                class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 transition
                  {{ request('tab') === 'presensi_istirahat' ? 'border-violet-600 text-violet-600 bg-violet-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Presensi Istirahat
            </a>
            <a href="{{ route('staff.presensi.laporan', ['tab' => 'riwayat']) }}"
                class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 transition
                  {{ request('tab') === 'riwayat' ? 'border-primary-600 text-primary-600 bg-primary-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Riwayat Detail Log
            </a>
        </div>
    </div>

    {{-- ── TAB 1: REKAPITULASI PEMAGANG ──────────────────── --}}
    @if (!request('tab') || request('tab') === 'rekapitulasi')
        <div
            class="bg-white rounded-b-xl border border-t-0 border-gray-200 print:rounded-xl print:border-gray-300 overflow-hidden shadow-sm print:shadow-none mb-8 print:mb-6 scroll-mt-6 print-avoid-break">
            <div id="tabel-rekap-pemagang"
                class="px-6 py-4 print:px-4 print:py-2.5 border-b border-gray-200 print:border-gray-300 bg-gray-50/60 print:bg-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm sm:text-base print:text-sm font-bold text-gray-800">Rekapitulasi Kehadiran per
                        Pemagang</h2>
                    <p class="text-xs print:text-[11px] text-gray-500">Ringkasan performa dan tingkat kedisiplinan
                        kehadiran setiap individu</p>
                </div>
                <span
                    class="text-xs font-semibold px-2.5 py-1 bg-primary-50 text-primary-700 rounded-full print:bg-transparent print:text-gray-700 print:border print:border-gray-300">
                    Total: {{ $rekapPemagang->total() }} Pemagang
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm print:text-xs min-w-[720px] print:min-w-full">
                    <thead>
                        <tr
                            class="bg-gray-50 print:bg-gray-100 border-b border-gray-200 print:border-gray-300 text-xs print:text-[11px] font-semibold text-gray-600 print:text-gray-800 uppercase tracking-wider">
                            <th class="px-3 sm:px-6 py-3 print:px-3 print:py-2">Pemagang</th>
                            <th class="px-3 sm:px-6 py-3 print:px-3 print:py-2">Divisi</th>
                            <th class="px-2 sm:px-4 py-3 print:px-2 print:py-2 text-center">Awal</th>
                            <th class="px-2 sm:px-4 py-3 print:px-2 print:py-2 text-center">Tepat</th>
                            <th class="px-2 sm:px-4 py-3 print:px-2 print:py-2 text-center">Telat</th>
                            <th class="px-2 sm:px-4 py-3 print:px-2 print:py-2 text-center">Alpa</th>
                            <th class="px-2 sm:px-4 py-3 print:px-2 print:py-2 text-center">Total</th>
                            <th class="px-3 sm:px-6 py-3 print:px-3 print:py-2 text-right">Kedisiplinan</th>
                            <th class="px-3 sm:px-6 py-3 print:px-3 print:py-2 text-center print:hidden">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 print:divide-gray-200">
                        @forelse($rekapPemagang as $item)
                            @php
                                $p = $item->pemagang;
                                $rateColor =
                                    $item->rate >= 80
                                        ? 'text-green-600'
                                        : ($item->rate >= 60
                                            ? 'text-amber-600'
                                            : 'text-red-600');
                                $barColor =
                                    $item->rate >= 80
                                        ? 'bg-green-500'
                                        : ($item->rate >= 60
                                            ? 'bg-amber-500'
                                            : 'bg-red-500');
                                $terlambatPagi = $item->terlambat_pagi ?? 0;
                                $terlambatIstirahat = $item->terlambat_istirahat ?? 0;
                            @endphp
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="px-3 sm:px-6 py-3 print:px-3 print:py-2">
                                    <p class="font-semibold text-gray-800 text-sm print:text-xs">{{ $p->nama_lengkap }}
                                    </p>
                                    <p class="text-xs print:text-[10px] text-gray-400">{{ $p->kampus }}</p>
                                </td>
                                <td class="px-3 sm:px-6 py-3 print:px-3 print:py-2">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs print:text-[10px] font-medium bg-gray-100 text-gray-700 print:border print:border-gray-200">
                                        {{ $p->divisi }}
                                    </span>
                                </td>
                                <td class="px-2 sm:px-4 py-3 print:px-2 print:py-2 text-center">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs print:text-[11px] font-semibold bg-indigo-50 text-indigo-700 print:bg-transparent print:text-indigo-800">
                                        {{ $item->datang_awal }}
                                    </span>
                                </td>
                                <td class="px-2 sm:px-4 py-3 print:px-2 print:py-2 text-center">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs print:text-[11px] font-semibold bg-green-50 text-green-700 print:bg-transparent print:text-green-800">
                                        {{ $item->tepat_waktu }}
                                    </span>
                                </td>
                                <td class="px-2 sm:px-4 py-3 print:px-2 print:py-2 text-center">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs print:text-[11px] font-semibold {{ $item->terlambat > 0 ? 'bg-amber-50 text-amber-700 print:text-amber-800' : 'text-gray-400' }}">
                                        {{ $item->terlambat }}
                                    </span>
                                </td>
                                <td class="px-2 sm:px-4 py-3 print:px-2 print:py-2 text-center">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs print:text-[11px] font-semibold {{ $item->tidak_hadir > 0 ? 'bg-red-50 text-red-700 print:text-red-800' : 'text-gray-400' }}">
                                        {{ $item->tidak_hadir }}
                                    </span>
                                </td>
                                <td
                                    class="px-2 sm:px-4 py-3 print:px-2 print:py-2 text-center font-bold text-gray-800 print:text-xs">
                                    {{ $item->total }}
                                </td>
                                <td class="px-3 sm:px-6 py-3 print:px-3 print:py-2 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <span
                                            class="font-bold text-sm print:text-xs {{ $rateColor }}">{{ $item->rate }}%</span>
                                        <div
                                            class="w-12 sm:w-14 bg-gray-100 rounded-full h-1.5 overflow-hidden print:hidden">
                                            <div class="{{ $barColor }} h-1.5 rounded-full"
                                                style="width: {{ min(100, $item->rate) }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                {{-- Aksi: Lihat Detail --}}
                                <td class="px-3 sm:px-6 py-3 print:hidden text-center">
                                    <button type="button" onclick="openPresensiDetailModal(this)"
                                        data-nama="{{ $p->nama_lengkap }}" data-kampus="{{ $p->kampus }}"
                                        data-divisi="{{ $p->divisi }}" data-awal="{{ $item->datang_awal }}"
                                        data-tepat="{{ $item->tepat_waktu }}" data-telat="{{ $item->terlambat }}"
                                        data-alpa="{{ $item->tidak_hadir }}" data-total="{{ $item->total }}"
                                        data-rate="{{ $item->rate }}" data-telat-pagi="{{ $terlambatPagi }}"
                                        data-telat-istirahat="{{ $terlambatIstirahat }}"
                                        class="inline-flex items-center justify-center p-2 sm:p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                                        title="Lihat Detail Presensi">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-8 text-center text-gray-400 text-xs">
                                    Tidak ada data presensi pemagang yang sesuai dengan filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($rekapPemagang->hasPages())
                <div class="px-6 py-4 pr-20 sm:pr-24 border-t border-gray-200 bg-gray-50/50 print:hidden">
                    {{ $rekapPemagang->links() }}
                </div>
            @endif
        </div>
    @endif

    {{-- ── TAB 2: RIWAYAT DETAIL LOG PRESENSI ──────────────── --}}
    @if (request('tab') === 'riwayat')
        <div
            class="bg-white rounded-b-xl border border-t-0 border-gray-200 print:rounded-xl print:border-gray-300 overflow-hidden shadow-sm print:shadow-none scroll-mt-6 print-avoid-break">
            <div id="tabel-log-presensi"
                class="px-6 py-4 print:px-4 print:py-2.5 border-b border-gray-200 print:border-gray-300 bg-gray-50/60 print:bg-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm sm:text-base print:text-sm font-bold text-gray-800">Riwayat Detail Log Presensi</h2>
                    <p class="text-xs print:text-[11px] text-gray-500">Catatan waktu presensi masuk dan status kehadiran
                        pemagang</p>
                </div>
                <span
                    class="text-xs font-semibold px-2.5 py-1 bg-gray-100 text-gray-700 rounded-full print:bg-transparent print:border print:border-gray-300">
                    Total Log: {{ $logs->total() }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm print:text-xs min-w-[750px] print:min-w-full">
                    <thead>
                        <tr
                            class="bg-gray-50 print:bg-gray-100 border-b border-gray-200 print:border-gray-300 text-xs print:text-[11px] font-semibold text-gray-600 print:text-gray-800 uppercase tracking-wider">
                            <th class="px-6 py-3.5 print:px-3 print:py-2">Pemagang</th>
                            <th class="px-6 py-3.5 print:px-3 print:py-2">Divisi</th>
                            <th class="px-6 py-3.5 print:px-3 print:py-2">Lokasi</th>
                            <th class="px-6 py-3.5 print:px-3 print:py-2">Shift</th>
                            <th class="px-6 py-3.5 print:px-3 print:py-2">Waktu Masuk</th>
                            <th class="px-6 py-3.5 print:px-3 print:py-2">Status</th>
                            <th class="px-6 py-3.5 print:px-3 print:py-2">Pencatat</th>
                            <th class="px-6 py-3.5 print:px-3 print:py-2">Catatan</th>

                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 print:divide-gray-200">
                        @forelse($logs as $log)
                            @php
                                $pemagang = $log->pemagang;
                                $badgeStyle = match ($log->keterangan) {
                                    'Lebih Awal' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                    'Tepat Waktu' => 'bg-green-50 text-green-700 border-green-200',
                                    'Terlambat' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'Tidak Hadir' => 'bg-red-50 text-red-700 border-red-200',
                                    default => 'bg-gray-100 text-gray-700 border-gray-200',
                                };
                                $shiftStyle = match ($log->shift) {
                                    'Pagi' => 'bg-sky-50 text-sky-700',
                                    'Middle' => 'bg-purple-50 text-purple-700',
                                    'Siang' => 'bg-orange-50 text-orange-700',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="px-6 py-3 print:px-3 print:py-1.5">
                                    <p class="font-medium text-gray-800 text-xs sm:text-sm print:text-xs">
                                        {{ $pemagang ? $pemagang->nama_lengkap : 'Pemagang Dihapus' }}</p>
                                    <p class="text-[11px] print:text-[10px] text-gray-400">
                                        {{ $pemagang ? $pemagang->kampus : '-' }}</p>
                                </td>
                                <td class="px-6 py-3 print:px-3 print:py-1.5">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs print:text-[10px] font-medium bg-gray-100 text-gray-700 print:border print:border-gray-200">
                                        {{ $pemagang ? $pemagang->divisi : '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 print:px-3 print:py-1.5">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs print:text-[10px] font-medium bg-blue-50 text-blue-700 print:border print:border-blue-200">
                                        {{ $log->kantor ?? 'Kantor 1' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 print:px-3 print:py-1.5">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs print:text-[10px] font-medium {{ $shiftStyle }}">
                                        {{ $log->shift }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 print:px-3 print:py-1.5 text-xs text-gray-700 font-medium">
                                    {{ substr($log->waktu_masuk, 0, 5) }} WIB
                                </td>
                                <td class="px-6 py-3 print:px-3 print:py-1.5">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs print:text-[10px] font-medium border {{ $badgeStyle }}">
                                        {{ $log->keterangan }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 print:px-3 print:py-1.5 text-xs text-gray-600 font-medium">
                                    {{ $log->creator?->name ?? 'Staff/Admin' }}
                                </td>
                                <td class="px-6 py-3 print:px-3 print:py-1.5 text-xs text-gray-500 max-w-[200px] truncate">
                                    {{ $log->notes ?: '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-6 text-center text-gray-400 text-xs">
                                    Belum ada riwayat log presensi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($logs->hasPages())
                <div class="px-6 py-4 pr-20 sm:pr-24 border-t border-gray-200 bg-gray-50/50 print:hidden">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    @endif

    {{-- ── TAB 3: PRESENSI MASUK ──────────────────────── --}}
    @if (request('tab') === 'presensi_masuk')
        <div id="tabel-presensi-masuk"
            class="bg-white rounded-b-xl border border-t-0 border-gray-200 overflow-hidden shadow-sm mb-8 scroll-mt-6">
            {{-- Header --}}
            <div
                class="px-6 py-4 border-b border-gray-200 bg-gray-50/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-gray-800">Presensi Masuk Harian</h2>
                    <p class="text-xs text-gray-500">Data pemagang yang melakukan presensi masuk pada tanggal yang dipilih
                    </p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full">
                    Total: {{ $totalPresensiMasuk }} Pemagang
                </span>
            </div>

            {{-- Filter --}}
            <div class="px-5 sm:px-6 py-3 border-b border-gray-100 bg-white">
                <form method="GET" action="{{ route('staff.presensi.laporan') }}"
                    class="flex flex-wrap items-center gap-3">
                    <input type="hidden" name="tab" value="presensi_masuk">

                    {{-- Tanggal --}}
                    <div class="flex items-center gap-2">
                        <label class="text-xs font-medium text-gray-600 whitespace-nowrap">Tanggal:</label>
                        <input type="date" name="tanggal_masuk" value="{{ $tanggalMasuk }}"
                            onchange="this.form.submit()"
                            class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
                    </div>

                    {{-- Search Nama --}}
                    <div class="relative flex-1 min-w-[180px] max-w-xs">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama pemagang..."
                            class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
                    </div>

                    <button type="submit"
                        class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition">
                        Cari
                    </button>

                    @if (request()->hasAny(['tanggal_masuk', 'search']))
                        <a href="{{ route('staff.presensi.laporan', ['tab' => 'presensi_masuk']) }}"
                            {{-- ganti route untuk Staff --}}
                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                            title="Reset">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm min-w-[500px]">
                    <thead>
                        <tr
                            class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="px-3 sm:px-6 py-3">No</th>
                            <th class="px-3 sm:px-6 py-3">Nama Pemagang</th>
                            <th class="px-3 sm:px-6 py-3">Divisi</th>
                            <th class="px-3 sm:px-6 py-3">Kantor</th> {{-- BARU --}}
                            <th class="px-3 sm:px-6 py-3">Waktu Masuk</th>
                            <th class="px-3 sm:px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($presensiMasuk as $index => $item)
                            @php
                                $p = $item->pemagang;
                                $badgeStyle = match ($item->keterangan) {
                                    'Lebih Awal' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                    'Tepat Waktu' => 'bg-green-50 text-green-700 border-green-200',
                                    'Terlambat' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'Tidak Hadir' => 'bg-red-50 text-red-700 border-red-200',
                                    default => 'bg-gray-100 text-gray-700 border-gray-200',
                                };
                            @endphp
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="px-3 sm:px-6 py-3 text-xs text-gray-400 font-medium">
                                    {{ $presensiMasuk->firstItem() + $index }}
                                </td>
                                <td class="px-3 sm:px-6 py-3">
                                    <p class="font-semibold text-gray-800 text-sm">
                                        {{ $p?->nama_lengkap ?? 'Pemagang Dihapus' }}</p>
                                    <p class="text-xs text-gray-400">{{ $p?->kampus ?? '-' }}</p>
                                </td>
                                <td class="px-3 sm:px-6 py-3">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                        {{ $p?->divisi ?? '-' }}
                                    </span>
                                </td>
                                {{-- Kolom Kantor --}}
                                <td class="px-3 sm:px-6 py-3">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700">
                                        {{ $item->kantor ?? 'Kantor 1' }}
                                    </span>
                                </td>
                                <td class="px-3 sm:px-6 py-3 text-xs text-gray-700 font-medium">
                                    {{ $item->waktu_masuk ? substr($item->waktu_masuk, 0, 5) . ' WIB' : '-' }}
                                </td>
                                <td class="px-3 sm:px-6 py-3">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $badgeStyle }}">
                                        {{ $item->keterangan }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center"> {{-- colspan jadi 6 --}}
                                    <div class="flex flex-col items-center gap-2 text-gray-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <p class="text-xs">Belum ada data presensi masuk untuk tanggal ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($presensiMasuk->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                    {{ $presensiMasuk->links() }}
                </div>
            @endif
        </div>
    @endif

    {{-- ── TAB 4: PRESENSI ISTIRAHAT ────────────────────── --}}
    @if (request('tab') === 'presensi_istirahat')
        <div id="tabel-presensi-istirahat"
            class="bg-white rounded-b-xl border border-t-0 border-gray-200 overflow-hidden shadow-sm mb-8 scroll-mt-6">
            {{-- Header --}}
            <div
                class="px-6 py-4 border-b border-gray-200 bg-gray-50/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-gray-800">Presensi Kembali Istirahat Harian</h2>
                    <p class="text-xs text-gray-500">Data pemagang yang telah kembali dari istirahat pada tanggal yang
                        dipilih</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 bg-violet-50 text-violet-700 rounded-full">
                    Total: {{ $totalPresensiIstirahat }} Pemagang
                </span>
            </div>

            {{-- Filter --}}
            <div class="px-5 sm:px-6 py-3 border-b border-gray-100 bg-white">
                <form method="GET" action="{{ route('staff.presensi.laporan') }}"
                    class="flex flex-wrap items-center gap-3">
                    <input type="hidden" name="tab" value="presensi_istirahat">

                    <div class="flex items-center gap-2">
                        <label class="text-xs font-medium text-gray-600 whitespace-nowrap">Tanggal:</label>
                        <input type="date" name="tanggal_istirahat" value="{{ $tanggalIstirahat }}"
                            onchange="this.form.submit()"
                            class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:bg-white transition">
                    </div>

                    <div class="relative flex-1 min-w-[180px] max-w-xs">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama pemagang..."
                            class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:bg-white transition">
                    </div>

                    <button type="submit"
                        class="px-3 py-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-medium rounded-lg transition">
                        Cari
                    </button>

                    @if (request()->hasAny(['tanggal_istirahat', 'search']))
                        <a href="{{ route('staff.presensi.laporan', ['tab' => 'presensi_istirahat']) }}"
                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                            title="Reset">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm min-w-[500px]">
                    <thead>
                        <tr
                            class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="px-3 sm:px-6 py-3">No</th>
                            <th class="px-3 sm:px-6 py-3">Nama Pemagang</th>
                            <th class="px-3 sm:px-6 py-3">Divisi</th>
                            <th class="px-3 sm:px-6 py-3">Kantor</th> {{-- BARU --}}
                            <th class="px-3 sm:px-6 py-3">Waktu Kembali</th>
                            <th class="px-3 sm:px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($presensiIstirahat as $index => $item)
                            @php
                                $p = $item->pemagang;
                                $badgeStyleIs = match ($item->keterangan) {
                                    'Tepat Waktu' => 'bg-green-50 text-green-700 border-green-200',
                                    'Terlambat' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    default => 'bg-gray-100 text-gray-700 border-gray-200',
                                };
                            @endphp
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="px-3 sm:px-6 py-3 text-xs text-gray-400 font-medium">
                                    {{ $presensiIstirahat->firstItem() + $index }}
                                </td>
                                <td class="px-3 sm:px-6 py-3">
                                    <p class="font-semibold text-gray-800 text-sm">
                                        {{ $p?->nama_lengkap ?? 'Pemagang Dihapus' }}</p>
                                    <p class="text-xs text-gray-400">{{ $p?->kampus ?? '-' }}</p>
                                </td>
                                <td class="px-3 sm:px-6 py-3">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                        {{ $p?->divisi ?? '-' }}
                                    </span>
                                </td>
                                {{-- Kolom Kantor --}}
                                <td class="px-3 sm:px-6 py-3">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700">
                                        {{ $item->kantor ?? 'Kantor 1' }}
                                    </span>
                                </td>
                                <td class="px-3 sm:px-6 py-3 text-xs text-gray-700 font-medium">
                                    {{ $item->waktu_masuk ? substr($item->waktu_masuk, 0, 5) . ' WIB' : '-' }}
                                </td>
                                <td class="px-3 sm:px-6 py-3">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $badgeStyleIs }}">
                                        {{ $item->keterangan }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center"> {{-- colspan jadi 6 --}}
                                    ...
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($presensiIstirahat->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                    {{ $presensiIstirahat->links() }}
                </div>
            @endif
        </div>
    @endif

    {{-- ── Lembar Tanda Tangan / Pengesahan (Hanya muncul saat cetak) ── --}}
    <div class="hidden print:block mt-8 pt-6 border-t border-gray-300 print-avoid-break">
        <div class="flex justify-between items-start text-xs text-gray-700">
            <div class="text-center w-52">
                <p class="text-gray-500 mb-1">Dicetak & Diverifikasi Oleh,</p>
                <div class="h-16"></div>
                <p class="font-bold underline text-gray-900">{{ Auth::user()->name }}</p>
                <p class="text-[10px] text-gray-500">{{ Auth::user()->role_label }}</p>
            </div>
            <div class="text-center w-60">
                <p class="text-gray-500 mb-1">Yogyakarta,
                    {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</p>
                <p class="text-gray-500">Mengetahui, HR Department</p>
                <div class="h-14"></div>
                <p class="font-bold underline text-gray-900">( ............................................ )</p>
                <p class="text-[10px] text-gray-500">Penanggung Jawab</p>
            </div>
        </div>
    </div>

    {{-- ── MODAL DETAIL PRESENSI ──────────────────── --}}
    <div id="presensiDetailModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-primary-50 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-primary-800">Detail Presensi</h3>
                    <p class="text-sm text-primary-600 mt-0.5" id="detailNama">—</p>
                </div>
                <button type="button" onclick="closePresensiDetailModal()"
                    class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-white/60 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-5">
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-xs text-gray-500">Kampus</p>
                        <p class="font-medium text-gray-800" id="detailKampus">—</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">Divisi</p>
                        <p class="font-medium text-gray-800" id="detailDivisi">—</p>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Ringkasan Kehadiran</p>
                    <div class="grid grid-cols-4 gap-2 text-center">
                        <div>
                            <p class="text-lg font-bold text-indigo-600" id="detailAwal">0</p>
                            <p class="text-[11px] text-gray-500">Awal</p>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-green-600" id="detailTepat">0</p>
                            <p class="text-[11px] text-gray-500">Tepat</p>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-amber-600" id="detailTelat">0</p>
                            <p class="text-[11px] text-gray-500">Telat</p>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-red-600" id="detailAlpa">0</p>
                            <p class="text-[11px] text-gray-500">Alpa</p>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-200 flex justify-between items-center text-sm">
                        <span class="text-gray-500">Total Presensi</span>
                        <span class="font-bold text-gray-800" id="detailTotal">0</span>
                    </div>
                    <div class="mt-1 flex justify-between items-center text-sm">
                        <span class="text-gray-500">Tingkat Kedisiplinan</span>
                        <span class="font-bold" id="detailRate">0%</span>
                    </div>
                </div>

                <div class="border border-amber-200 bg-amber-50/50 rounded-lg p-4">
                    <p
                        class="text-xs font-semibold text-amber-800 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Detail Keterlambatan
                    </p>
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">Presensi Masuk</span>
                            <span id="detailTelatPagi"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold">—</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">Presensi Istirahat</span>
                            <span id="detailTelatIstirahat"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold">—</span>
                        </div>
                    </div>
                    <p class="text-[11px] text-amber-700/80 mt-3 leading-relaxed">
                        Informasi ini menjelaskan sumber status “Terlambat” sehingga lebih mudah dilacak penyebabnya.
                    </p>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                <button type="button" onclick="closePresensiDetailModal()"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-lg transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function openPresensiDetailModal(btn) {
                const modal = document.getElementById('presensiDetailModal');
                if (!modal) return;

                document.getElementById('detailNama').textContent = btn.dataset.nama || '—';
                document.getElementById('detailKampus').textContent = btn.dataset.kampus || '—';
                document.getElementById('detailDivisi').textContent = btn.dataset.divisi || '—';
                document.getElementById('detailAwal').textContent = btn.dataset.awal || '0';
                document.getElementById('detailTepat').textContent = btn.dataset.tepat || '0';
                document.getElementById('detailTelat').textContent = btn.dataset.telat || '0';
                document.getElementById('detailAlpa').textContent = btn.dataset.alpa || '0';
                document.getElementById('detailTotal').textContent = btn.dataset.total || '0';
                document.getElementById('detailRate').textContent = (btn.dataset.rate || '0') + '%';

                const telatPagi = parseInt(btn.dataset.telatPagi || 0);
                const telatIst = parseInt(btn.dataset.telatIstirahat || 0);

                const elPagi = document.getElementById('detailTelatPagi');
                const elIst = document.getElementById('detailTelatIstirahat');

                if (telatPagi > 0) {
                    elPagi.textContent = telatPagi + ' kali';
                    elPagi.className =
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800';
                } else {
                    elPagi.textContent = 'Tidak terlambat';
                    elPagi.className =
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800';
                }

                if (telatIst > 0) {
                    elIst.textContent = telatIst + ' kali';
                    elIst.className =
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800';
                } else {
                    elIst.textContent = 'Tidak terlambat';
                    elIst.className =
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800';
                }

                const rate = parseFloat(btn.dataset.rate || 0);
                document.getElementById('detailRate').className = 'font-bold ' + (rate >= 80 ? 'text-green-600' : (rate >= 60 ?
                    'text-amber-600' : 'text-red-600'));

                modal.classList.remove('hidden');
            }

            function closePresensiDetailModal() {
                document.getElementById('presensiDetailModal')?.classList.add('hidden');
            }

            document.getElementById('presensiDetailModal')?.addEventListener('click', function(e) {
                if (e.target === this) closePresensiDetailModal();
            });
        </script>
    @endpush

@endsection
