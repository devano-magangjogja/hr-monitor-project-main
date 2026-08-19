@extends('layouts.app')

@section('title', 'Presensi Pemagang')
@section('page-title', 'Presensi Pemagang')
@section('page-subtitle', 'Presensi tanggal: ' . $formattedDate)

@section('sidebar')
    @include('components.sidebar-staff')
@endsection

@section('content')

    {{-- ── Banner Judul Tanggal Presensi ───────────────────────── --}}
    <div
        class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5 mb-6 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center gap-3.5">
            <div
                class="w-10 h-10 rounded-xl bg-primary-50 border border-primary-100 flex items-center justify-center text-primary-600 flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-base sm:text-lg font-bold text-gray-800">
                        Presensi: <span class="text-primary-600">{{ $formattedDate }}</span>
                    </h2>
                </div>
            </div>
        </div>
        <div class="w-full sm:w-auto flex items-center gap-2">
            <button onclick="openCreateModal()"
                class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-xs sm:text-sm font-semibold rounded-xl transition shadow-sm hover:shadow">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Catat Presensi</span>
            </button>
        </div>
    </div>

    {{-- ── Stat Cards Ringkasan Tanggal Ini ────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 mb-8">

        {{-- Total Pemagang (Melebar penuh 2 kolom di mobile) --}}
        <div class="col-span-2 sm:col-span-1 lg:col-span-1 bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500">Total Pemagang</p>
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total_pemagang'] }}</p>
            <p class="text-[11px] text-gray-400 mt-0.5">anak magang aktif</p>
        </div>

        {{-- Datang Lebih Awal --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500">Lebih Awal</p>
                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-indigo-600">{{ $stats['datang_awal'] }}</p>
            <p class="text-[11px] text-gray-400 mt-0.5">hadir lebih awal</p>
        </div>

        {{-- Tepat Waktu --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500">Tepat Waktu</p>
                <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-green-600">{{ $stats['tepat_waktu'] }}</p>
            <p class="text-[11px] text-gray-400 mt-0.5">hadir tepat waktu</p>
        </div>

        {{-- Terlambat --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500">Terlambat</p>
                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-amber-600">{{ $stats['terlambat'] }}</p>
            <p class="text-[11px] text-gray-400 mt-0.5">terlambat masuk</p>
        </div>

        {{-- Tidak Hadir --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500">Tidak Hadir</p>
                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-red-600">{{ $stats['tidak_hadir'] }}</p>
            <p class="text-[11px] text-gray-400 mt-0.5">perlu dikonfirmasi</p>
        </div>

    </div>

    {{-- ── Tanggal Picker & Filter Header ──────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            {{-- Filter Form --}}
            <form method="GET" action="{{ route('staff.presensi.index') }}"
                class="flex-1 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                {{-- Input Tanggal --}}
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Pilih
                        Tanggal</label>
                    <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs sm:text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition">
                </div>

                {{-- Filter Shift --}}
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Shift</label>
                    <select name="shift" onchange="this.form.submit()"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition text-gray-700">
                        <option value="">Semua Shift</option>
                        <option value="Pagi" {{ request('shift') == 'Pagi' ? 'selected' : '' }}>Shift Pagi</option>
                        <option value="Middle" {{ request('shift') == 'Middle' ? 'selected' : '' }}>Shift Middle</option>
                        <option value="Siang" {{ request('shift') == 'Siang' ? 'selected' : '' }}>Shift Siang</option>
                    </select>
                </div>

                {{-- Filter Divisi --}}
                <div>
                    <label
                        class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Divisi</label>
                    <select name="divisi" onchange="this.form.submit()"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition text-gray-700">
                        <option value="">Semua Divisi</option>
                        @foreach($divisiList as $div)
                            <option value="{{ $div }}" {{ request('divisi') == $div ? 'selected' : '' }}>{{ $div }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Search input --}}
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Cari
                        Pemagang</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Nama / NIM / Kampus..."
                            class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition">
                    </div>
                </div>
            </form>

            @if(!$isToday)
                <div class="flex items-center gap-2 flex-shrink-0 self-end lg:self-center">
                    <a href="{{ route('staff.presensi.index') }}"
                        class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs sm:text-sm font-semibold rounded-lg transition"
                        title="Kembali ke Hari Ini">
                        Hari Ini
                    </a>
                </div>
            @endif

        </div>
    </div>


    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- ── TABEL 1: DAFTAR PEMAGANG HADIR ─────────────────────── --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="tabel-hadir" class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm mb-8 scroll-mt-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/70 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-gray-800">Daftar Pemagang Hadir</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Pemagang yang hadir (Lebih Awal, Tepat Waktu, Terlambat)
                    </p>
                </div>
            </div>
            <span
                class="text-xs font-semibold px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full border border-emerald-200">
                {{ $presensiHadir->total() }} Hadir
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[700px]">
                <thead>
                    <tr
                        class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <th class="px-6 py-3.5 w-12 text-center">No</th>
                        <th class="px-6 py-3.5">Nama Pemagang</th>
                        <th class="px-6 py-3.5">Divisi</th>
                        <th class="px-6 py-3.5">Shift</th>
                        <th class="px-6 py-3.5">Waktu Masuk</th>
                        <th class="px-6 py-3.5">Status Kehadiran</th>
                        <th class="px-6 py-3.5">Catatan</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
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

                            {{-- No --}}
                            <td class="px-6 py-4 text-center text-xs text-gray-400 font-medium">
                                {{ $presensiHadir->firstItem() ? ($presensiHadir->firstItem() + $index) : ($index + 1) }}
                            </td>

                            {{-- Nama Pemagang (Cukup Nama, tanpa icon) --}}
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-800 text-sm">
                                    {{ $pemagang ? $pemagang->nama_lengkap : 'Pemagang Dihapus' }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    NIM: {{ $pemagang ? $pemagang->nim : '-' }} &bull; {{ $pemagang ? $pemagang->kampus : '-' }}
                                </p>
                            </td>

                            {{-- Divisi --}}
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-700">
                                    {{ $pemagang ? $pemagang->divisi : '-' }}
                                </span>
                            </td>

                            {{-- Shift --}}
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $shiftStyle }}">
                                    {{ $presensi->shift }}
                                </span>
                            </td>

                            {{-- Waktu Masuk --}}
                            <td class="px-6 py-4">
                                <span class="text-xs font-medium text-gray-700">
                                    {{ substr($presensi->waktu_masuk, 0, 5) }} WIB
                                </span>
                            </td>

                            {{-- Status Kehadiran --}}
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $badgeStyle }}">
                                    @if($presensi->keterangan == 'Tepat Waktu' || $presensi->keterangan == 'Lebih Awal')
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                    @elseif($presensi->keterangan == 'Terlambat')
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                    @endif
                                    {{ $presensi->keterangan }}
                                </span>
                            </td>

                            {{-- Catatan --}}
                            <td class="px-6 py-4 text-xs text-gray-500 max-w-[200px] truncate" title="{{ $presensi->notes }}">
                                {{ $presensi->notes ?: '-' }}
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    {{-- Edit --}}
                                    <button type="button" onclick="openEditModal(
                                                                                                    {{ $presensi->id }},
                                                                                                    {{ $presensi->pemagang_id }},
                                                                                                    '{{ addslashes($pemagang ? $pemagang->nama_lengkap : '') }}',
                                                                                                    '{{ $presensi->tanggal }}',
                                                                                                    '{{ $presensi->shift }}',
                                                                                                    '{{ substr($presensi->waktu_masuk, 0, 5) }}',
                                                                                                    '{{ $presensi->keterangan }}',
                                                                                                    '{{ addslashes($presensi->notes ?: '') }}'
                                                                                                )"
                                        class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                                        title="Edit Presensi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>

                                    {{-- Hapus --}}
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
                                <p class="text-xs text-gray-400 mt-1">Gunakan tombol "Catat Presensi" untuk menginput data
                                    kehadiran.</p>
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


    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- ── TABEL 2: DAFTAR PEMAGANG TIDAK HADIR ───────────────── --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="tabel-tidak-hadir" class="bg-white rounded-xl border border-red-200 overflow-hidden shadow-sm scroll-mt-6">
        <div
            class="px-6 py-4 border-b border-red-100 bg-red-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div class="flex items-center gap-2.5">
                <div class="w-2.5 h-2.5 rounded-full bg-red-500"></div>
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-gray-900">Daftar Pemagang Tidak Hadir</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Pemagang yang tidak hadir hari ini &bull; Klik tombol WhatsApp
                        untuk konfirmasi</p>
                </div>
            </div>
            <span
                class="text-xs font-semibold px-3 py-1 bg-red-100 text-red-700 rounded-full border border-red-200 self-start sm:self-auto">
                {{ $presensiTidakHadir->total() }} Tidak Hadir
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[750px]">
                <thead>
                    <tr
                        class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
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

                            {{-- No --}}
                            <td class="px-6 py-4 text-center text-xs text-gray-400 font-medium">
                                {{ $presensiTidakHadir->firstItem() ? ($presensiTidakHadir->firstItem() + $index) : ($index + 1) }}
                            </td>

                            {{-- Nama Pemagang (Cukup Nama, tanpa icon) --}}
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-900 text-sm">
                                    {{ $pemagang ? $pemagang->nama_lengkap : 'Pemagang Dihapus' }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    NIM: {{ $pemagang ? $pemagang->nim : '-' }} &bull; {{ $pemagang ? $pemagang->kampus : '-' }}
                                </p>
                            </td>

                            {{-- Divisi --}}
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-700">
                                    {{ $pemagang ? $pemagang->divisi : '-' }}
                                </span>
                            </td>

                            {{-- Shift --}}
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $shiftStyle }}">
                                    {{ $presensi->shift }}
                                </span>
                            </td>

                            {{-- No. WhatsApp --}}
                            <td class="px-6 py-4">
                                <span class="text-xs font-medium text-gray-700 font-mono">
                                    {{ $pemagang ? $pemagang->no_hp : '-' }}
                                </span>
                            </td>

                            {{-- Keterangan / Alasan --}}
                            <td class="px-6 py-4 text-xs text-red-600 max-w-[200px] truncate" title="{{ $presensi->notes }}">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>{{ $presensi->notes ?: 'Tidak ada keterangan' }}</span>
                                </span>
                            </td>

                            {{-- Tombol Hubungi WhatsApp (Dibuat Sangat Jelas) --}}
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

                            {{-- Aksi --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    {{-- Edit --}}
                                    <button type="button" onclick="openEditModal(
                                                                                                    {{ $presensi->id }},
                                                                                                    {{ $presensi->pemagang_id }},
                                                                                                    '{{ addslashes($pemagang ? $pemagang->nama_lengkap : '') }}',
                                                                                                    '{{ $presensi->tanggal }}',
                                                                                                    '{{ $presensi->shift }}',
                                                                                                    '{{ substr($presensi->waktu_masuk, 0, 5) }}',
                                                                                                    '{{ $presensi->keterangan }}',
                                                                                                    '{{ addslashes($presensi->notes ?: '') }}'
                                                                                                )"
                                        class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                                        title="Ubah Status Presensi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>

                                    {{-- Hapus --}}
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


    {{-- ── MODAL CATAT PRESENSI ──────────────────────────────── --}}
    <div id="modal-create" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
        <div
            class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50/50">
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Catat Presensi Pemagang</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Input kehadiran pemagang untuk tanggal yang dipilih</p>
                </div>
                <button onclick="document.getElementById('modal-create').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('staff.presensi.store') }}" method="POST" class="px-6 pt-2 pb-5 space-y-3.5">
                @csrf

                {{-- Tanggal Presensi (Otomatis Hari Ini & Terkunci) --}}
                <div class="p-3 bg-primary-50/70 border border-primary-100 rounded-xl flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div
                            class="w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-medium text-gray-500">Tanggal Presensi (Hari Ini)</p>
                            <p class="text-xs sm:text-sm font-bold text-gray-800">
                                {{ Carbon\Carbon::today()->locale('id')->translatedFormat('l, d F Y') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Pilih Pemagang dengan Fitur Pencarian Real-Time --}}
                <div class="relative" id="searchable-pemagang-container">
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                        Nama Pemagang <span class="text-red-500">*</span>
                    </label>

                    <input type="hidden" name="pemagang_id" id="create-pemagang-id" required>

                    {{-- Trigger Box --}}
                    <div id="pemagang-select-trigger" onclick="togglePemagangDropdown()"
                        class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-xs sm:text-sm flex items-center justify-between cursor-pointer hover:bg-white hover:border-primary-500 transition">
                        <span id="selected-pemagang-text" class="text-gray-400">-- Cari & Pilih Nama Pemagang --</span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>

                    {{-- Dropdown Menu Popover --}}
                    <div id="pemagang-dropdown-menu"
                        class="hidden absolute z-50 left-0 right-0 mt-1 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden animate-in fade-in duration-100">
                        {{-- Search Box di dalam dropdown --}}
                        <div class="p-2 border-b border-gray-100 bg-gray-50/90 sticky top-0 z-10">
                            <div class="relative">
                                <span
                                    class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none text-gray-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </span>
                                <input type="text" id="pemagang-search-input" oninput="filterPemagangOptions(this.value)"
                                    placeholder="Ketik nama / NIM / divisi..."
                                    class="w-full pl-8 pr-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>

                        {{-- List Opsi Pemagang --}}
                        <div id="pemagang-options-list" class="max-h-48 overflow-y-auto divide-y divide-gray-50">
                            @foreach($pemagangs as $p)
                                <div onclick="selectPemagang('{{ $p->id }}', '{{ addslashes($p->nama_lengkap) }}', '{{ $p->nim }}', '{{ $p->divisi }}')"
                                    data-search="{{ strtolower($p->nama_lengkap . ' ' . $p->nim . ' ' . $p->divisi) }}"
                                    class="pemagang-option px-3.5 py-2.5 hover:bg-primary-50/80 cursor-pointer transition flex items-center justify-between group">
                                    <div>
                                        <p class="text-xs sm:text-sm font-semibold text-gray-800 group-hover:text-primary-700">
                                            {{ $p->nama_lengkap }}
                                        </p>
                                        <p class="text-[11px] text-gray-400">NIM: {{ $p->nim }} &bull; {{ $p->kampus }}</p>
                                    </div>
                                    <span
                                        class="text-[10px] font-medium px-2 py-0.5 rounded bg-gray-100 text-gray-600 group-hover:bg-primary-100 group-hover:text-primary-800">
                                        {{ $p->divisi }}
                                    </span>
                                </div>
                            @endforeach
                            <div id="pemagang-no-result" class="hidden px-3.5 py-4 text-center text-xs text-gray-400">
                                Tidak ada pemagang yang cocok dengan kata kunci
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Grid Shift & Waktu Masuk --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Shift Kerja <span class="text-red-500">*</span>
                        </label>
                        <select name="shift" required
                            class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition">
                            <option value="Pagi">Shift Pagi (08:00 - 16:00)</option>
                            <option value="Middle">Shift Middle (10:00 - 18:00)</option>
                            <option value="Siang">Shift Siang (13:00 - 21:00)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Waktu Masuk <span class="text-red-500">*</span>
                        </label>
                        <input type="time" name="waktu_masuk" value="{{ date('H:i') }}" required
                            class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition">
                    </div>
                </div>

                {{-- Status Kehadiran --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                        Status Keterangan <span class="text-red-500">*</span>
                    </label>
                    <select name="keterangan" required
                        class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition">
                        <option value="Tepat Waktu">Tepat Waktu</option>
                        <option value="Lebih Awal">Lebih Awal</option>
                        <option value="Terlambat">Terlambat</option>
                        <option value="Tidak Hadir">Tidak Hadir (Alpa / Izin / Sakit)</option>
                    </select>
                </div>

                {{-- Catatan / Notes --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                        Catatan / Keterangan Tambahan
                    </label>
                    <textarea name="notes" rows="3"
                        placeholder="Contoh: Izin terlambat karena kendala transportasi, hadir tepat waktu, dsb."
                        class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-gray-100">
                    <button type="button" onclick="document.getElementById('modal-create').classList.add('hidden')"
                        class="px-4 py-2.5 text-sm text-gray-600 font-medium border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                        Simpan Presensi
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ── MODAL EDIT PRESENSI ────────────────────────────────── --}}
    <div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
        <div
            class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50/50">
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Edit Data Presensi</h3>
                    <p id="edit-pemagang-name" class="text-xs text-primary-600 font-medium mt-0.5"></p>
                </div>
                <button onclick="document.getElementById('modal-edit').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="form-edit" action="" method="POST" class="px-6 pt-3.5 pb-5 space-y-3.5">
                @csrf
                @method('PATCH')

                <input type="hidden" id="edit-pemagang-id" name="pemagang_id">
                <input type="hidden" id="edit-tanggal" name="tanggal">

                {{-- Tanggal Presensi (Terkunci) --}}
                <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div
                            class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Tanggal
                                Presensi</span>
                            <p id="edit-tanggal-display" class="text-xs sm:text-sm font-bold text-gray-800"></p>
                        </div>
                    </div>
                    <span
                        class="px-2 py-0.5 bg-gray-100 text-gray-600 text-[10px] rounded-full font-semibold border border-gray-200">
                        Terkunci
                    </span>
                </div>

                {{-- Grid Shift & Waktu Masuk --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Shift Kerja <span class="text-red-500">*</span>
                        </label>
                        <select id="edit-shift" name="shift" required
                            class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition">
                            <option value="Pagi">Shift Pagi</option>
                            <option value="Middle">Shift Middle</option>
                            <option value="Siang">Shift Siang</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Waktu Masuk <span class="text-red-500">*</span>
                        </label>
                        <input type="time" id="edit-waktu" name="waktu_masuk" required
                            class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition">
                    </div>
                </div>

                {{-- Status Kehadiran --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                        Status Keterangan <span class="text-red-500">*</span>
                    </label>
                    <select id="edit-keterangan" name="keterangan" required
                        class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition">
                        <option value="Lebih Awal">Lebih Awal</option>
                        <option value="Tepat Waktu">Tepat Waktu</option>
                        <option value="Terlambat">Terlambat</option>
                        <option value="Tidak Hadir">Tidak Hadir</option>
                    </select>
                </div>

                {{-- Catatan / Notes --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                        Catatan / Notes
                    </label>
                    <textarea id="edit-notes" name="notes" rows="3"
                        class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-gray-100">
                    <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')"
                        class="px-4 py-2.5 text-sm text-gray-600 font-medium border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ── MODAL HAPUS PRESENSI ──────────────────────────────── --}}
    <div id="modal-delete" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
        <div
            class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden animate-in fade-in zoom-in-95 duration-150">
            <div class="px-6 py-6 text-center">
                <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-800 mb-1">Hapus Catatan Presensi?</h3>
                <p class="text-xs text-gray-500 mb-6 leading-relaxed">
                    Yakin ingin menghapus catatan presensi untuk
                    <span id="delete-pemagang-name" class="font-semibold text-gray-700"></span> (<span id="delete-info"
                        class="text-gray-600"></span>)?
                    Tindakan ini tidak dapat dibatalkan.
                </p>
                <form id="form-delete" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="flex justify-center gap-3">
                        <button type="button" onclick="document.getElementById('modal-delete').classList.add('hidden')"
                            class="px-4 py-2.5 text-xs sm:text-sm text-gray-600 font-medium border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-xs sm:text-sm font-medium rounded-lg transition shadow-sm">
                            Ya, Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function openCreateModal() {
            document.getElementById('create-pemagang-id').value = '';
            const textEl = document.getElementById('selected-pemagang-text');
            if (textEl) textEl.textContent = '-- Cari & Pilih Nama Pemagang --';
            const searchInput = document.getElementById('pemagang-search-input');
            if (searchInput) searchInput.value = '';
            filterPemagangOptions('');
            const dropdown = document.getElementById('pemagang-dropdown-menu');
            if (dropdown) dropdown.classList.add('hidden');
            document.getElementById('modal-create').classList.remove('hidden');
        }

        function togglePemagangDropdown() {
            const dropdown = document.getElementById('pemagang-dropdown-menu');
            dropdown.classList.toggle('hidden');
            if (!dropdown.classList.contains('hidden')) {
                const searchInput = document.getElementById('pemagang-search-input');
                searchInput.value = '';
                filterPemagangOptions('');
                setTimeout(() => searchInput.focus(), 50);
            }
        }

        function filterPemagangOptions(keyword) {
            const kw = keyword.toLowerCase().trim();
            const options = document.querySelectorAll('.pemagang-option');
            let visibleCount = 0;

            options.forEach(opt => {
                const text = opt.getAttribute('data-search') || '';
                if (text.includes(kw)) {
                    opt.classList.remove('hidden');
                    visibleCount++;
                } else {
                    opt.classList.add('hidden');
                }
            });

            const noResult = document.getElementById('pemagang-no-result');
            if (visibleCount === 0) {
                noResult.classList.remove('hidden');
            } else {
                noResult.classList.add('hidden');
            }
        }

        function selectPemagang(id, name, nim, divisi) {
            document.getElementById('create-pemagang-id').value = id;
            const textEl = document.getElementById('selected-pemagang-text');
            textEl.innerHTML = `<strong class="text-gray-800 font-semibold">${name}</strong> <span class="text-xs text-gray-500">(${nim}) - Div. ${divisi}</span>`;
            document.getElementById('pemagang-dropdown-menu').classList.add('hidden');
        }

        // Tutup dropdown saat klik di luar area
        document.addEventListener('click', function (e) {
            const container = document.getElementById('searchable-pemagang-container');
            if (container && !container.contains(e.target)) {
                const dropdown = document.getElementById('pemagang-dropdown-menu');
                if (dropdown) dropdown.classList.add('hidden');
            }
        });

        function openEditModal(id, pemagangId, pemagangName, tanggal, shift, waktu, keterangan, notes) {
            document.getElementById('edit-pemagang-id').value = pemagangId;
            document.getElementById('edit-pemagang-name').textContent = pemagangName;
            document.getElementById('edit-tanggal').value = tanggal;

            const displayEl = document.getElementById('edit-tanggal-display');
            if (displayEl) {
                displayEl.textContent = tanggal;
            }

            document.getElementById('edit-shift').value = shift;
            document.getElementById('edit-waktu').value = waktu;
            document.getElementById('edit-keterangan').value = keterangan;
            document.getElementById('edit-notes').value = notes;
            document.getElementById('form-edit').action = `/staff/presensi/${id}`;
            document.getElementById('modal-edit').classList.remove('hidden');
        }

        function openDeleteModal(id, pemagangName, shift, waktu) {
            document.getElementById('delete-pemagang-name').textContent = pemagangName;
            document.getElementById('delete-info').textContent = `${shift} - ${waktu}`;
            document.getElementById('form-delete').action = `/staff/presensi/${id}`;
            document.getElementById('modal-delete').classList.remove('hidden');
        }
    </script>
@endpush