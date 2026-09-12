@extends('layouts.app')

@section('title', 'Daftar Data Pemagang')
@section('page-title', 'Daftar Data Pemagang')
@section('page-subtitle', 'Kelola data seluruh anak magang, asal kampus, divisi, dan riwayat kehadiran')

@section('sidebar')
    @if(auth()->user()->isAdmin())
        @include('components.sidebar-admin')
    @elseif(auth()->user()->isHrStaff())
        @include('components.sidebar-staff')
    @else
        @include('components.sidebar-assistant')
    @endif
@endsection

@section('content')

    @php
        $isAdmin = auth()->user()->isAdmin();
        $routePrefix = $isAdmin ? 'admin' : (auth()->user()->isHrStaff() ? 'staff' : 'assistant');
    @endphp

    {{-- ── 1. STATISTIK KARTU RINGKASAN ───────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Total Pemagang --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Pemagang</p>
                    <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ number_format($stats['total_pemagang']) }}</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Anak magang terdaftar</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total Kampus --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Asal Kampus / Sekolah</p>
                    <p class="text-2xl font-extrabold text-purple-700 mt-1">{{ number_format($stats['total_kampus']) }}</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Institusi mitra</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total Divisi --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Divisi Aktif</p>
                    <p class="text-2xl font-extrabold text-amber-600 mt-1">{{ number_format($stats['total_divisi']) }}</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Bidang penempatan</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Hadir Hari Ini --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Hadir Hari Ini</p>
                    <p class="text-2xl font-extrabold text-emerald-600 mt-1">{{ number_format($stats['hadir_hari_ini']) }}
                    </p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Pemagang aktif</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- ── 2. TOOLBAR PENCARIAN & FILTER ──────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5 mb-6 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-end gap-4">
            {{-- FORM FILTER --}}
            <form method="GET" action="{{ route($routePrefix . '.pemagang.index') }}"
                class="flex-1 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-3 items-end">
                {{-- SEARCH --}}
                <div class="md:col-span-5">
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Cari Pemagang
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" name="search" value="{{ $search ?? '' }}"
                            placeholder="Cari nama atau nomor WhatsApp / HP..."
                            class="w-full h-[42px] pl-9 pr-3 bg-gray-50 border border-gray-200 rounded-lg text-xs sm:text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition">
                    </div>
                </div>

                {{-- DIVISI --}}
                <div class="md:col-span-3">
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Divisi
                    </label>
                    <select name="divisi"
                        class="w-full h-[42px] px-3 bg-gray-50 border border-gray-200 rounded-lg text-xs sm:text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition">
                        <option value="">Semua Divisi</option>
                        @foreach($divisiList as $d)
                            <option value="{{ $d }}" {{ ($divisi ?? '') === $d ? 'selected' : '' }}>
                                {{ $d }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- KAMPUS / SEKOLAH --}}
                <div class="md:col-span-2">
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Kampus / Sekolah
                    </label>
                    <select name="kampus"
                        class="w-full h-[42px] px-3 bg-gray-50 border border-gray-200 rounded-lg text-xs sm:text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition">
                        <option value="">Semua Asal</option>
                        @foreach($kampusList as $k)
                            <option value="{{ $k }}" {{ ($kampus ?? '') === $k ? 'selected' : '' }}>
                                {{ $k }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- FILTER & RESET BUTTONS --}}
                <div class="md:col-span-2 flex items-center gap-2">
                    <button type="submit"
                        class="h-[42px] flex-1 inline-flex items-center justify-center gap-1.5 px-4 bg-primary-600 hover:bg-primary-700 text-white text-xs sm:text-sm font-semibold rounded-lg transition shadow-sm whitespace-nowrap">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span>Filter</span>
                    </button>

                    @if($search || $divisi || $kampus)
                        <a href="{{ route($routePrefix . '.pemagang.index') }}"
                            class="h-[42px] px-3 inline-flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs sm:text-sm font-semibold rounded-lg transition whitespace-nowrap">
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            {{-- BUTTON TAMBAH PEMAGANG --}}
            <div class="w-full lg:w-auto flex-shrink-0">
                <button type="button" onclick="openAddPemagangModal()"
                    class="w-full lg:w-auto h-[42px] inline-flex items-center justify-center gap-2 px-5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-semibold rounded-lg transition shadow-sm hover:shadow whitespace-nowrap">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Pemagang Baru</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ── Sticky Bulk Action Bar ────────────────────────────────── --}}
    @if(auth()->user()->isAdmin() || auth()->user()->isHrStaff())
        <div id="bulk-action-bar"
            class="hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-40 flex items-center justify-between sm:justify-start gap-2 sm:gap-3 px-4 sm:px-5 py-3 bg-white rounded-2xl shadow-2xl border border-gray-700 transition-all duration-300 w-[92%] sm:w-auto max-w-md">
            <div class="flex items-center gap-2 text-sm whitespace-nowrap">
                <span class="w-5 h-5 rounded-full bg-red-500 text-white flex items-center justify-center text-[11px] font-bold"
                    id="bulk-count-badge">0</span>
                <span class="font-medium hidden sm:inline">Pemagang dipilih</span>
                <span class="font-medium sm:hidden">dipilih</span>
            </div>
            <div class="w-px h-5 bg-gray-600 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="openBulkDeleteModal()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-1.5 bg-red-600 hover:bg-red-500 text-white text-xs font-bold rounded-xl transition active:scale-95 whitespace-nowrap">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus
                </button>
                <button type="button" onclick="clearAllSelections()"
                    class="p-1.5 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition"
                    title="Batalkan Pilihan">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    @endif

    {{-- ── 3. TABEL DAFTAR PEMAGANG ───────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div
            class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 bg-gray-50/50">
            <div>
                <h2 class="text-sm font-bold text-gray-900">Data Seluruh Anak Magang</h2>
                <p class="text-xs text-gray-500 mt-0.5">Daftar lengkap pemagang, asal kampus, divisi, dan riwayat presensi
                </p>
            </div>
            <span
                class="text-xs font-semibold px-3 py-1 bg-primary-50 text-primary-700 rounded-full border border-primary-200 self-start sm:self-auto">
                {{ $pemagangs->total() }} Pemagang
            </span>
        </div>

        {{-- DESKTOP VIEW: Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[750px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-600 tracking-wider">
                        @if(auth()->user()->isAdmin() || auth()->user()->isHrStaff())
                            <th class="pl-5 pr-2 py-3.5 w-10">
                                <input type="checkbox"
                                    class="select-all-checkbox w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 cursor-pointer"
                                    title="Pilih semua di halaman ini">
                            </th>
                        @endif
                        <th class="px-6 py-3.5">Nama Lengkap</th>
                        <th class="px-6 py-3.5">WhatsApp</th>
                        <th class="px-6 py-3.5">Asal Kampus / Sekolah</th>
                        <th class="px-6 py-3.5">Divisi Magang</th>
                        <th class="px-6 py-3.5 text-center">Riwayat</th>
                        <th class="px-6 py-3.5 text-right w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pemagangs as $index => $p)
                        <tr class="hover:bg-gray-50/80 transition align-middle row-item" data-id="{{ $p->id }}">
                            @if(auth()->user()->isAdmin() || auth()->user()->isHrStaff())
                                <td class="pl-5 pr-2 py-4">
                                    <input type="checkbox" name="row-check" value="{{ $p->id }}"
                                        class="row-checkbox w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 cursor-pointer">
                                </td>
                            @endif
                            {{-- Nama --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 font-bold flex items-center justify-center text-xs flex-shrink-0">
                                        {{ strtoupper(substr($p->nama_lengkap, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-900 text-sm truncate" title="{{ $p->nama_lengkap }}">
                                            {{ $p->nama_lengkap }}
                                        </p>
                                        <p class="text-[11px] text-gray-400 mt-0.5">ID: #{{ $p->id }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- No HP --}}
                            <td class="px-6 py-4">
                                <span class="text-xs font-medium text-gray-700 font-mono">
                                    {{ $p->no_hp ?: '-' }}
                                </span>
                            </td>

                            {{-- Kampus --}}
                            <td class="px-6 py-4">
                                <span class="text-xs text-gray-800 font-medium block truncate max-w-[200px]"
                                    title="{{ $p->kampus }}">
                                    {{ $p->kampus }}
                                </span>
                            </td>

                            {{-- Divisi --}}
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-700">
                                    {{ $p->divisi }}
                                </span>
                            </td>

                            {{-- Riwayat Presensi --}}
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ $p->presensis_count ?? 0 }} kali presensi
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    {{-- Edit Button --}}
                                    <button type="button"
                                        onclick="openEditPemagangModal({{ $p->id }}, '{{ addslashes($p->nama_lengkap) }}', '{{ addslashes($p->no_hp) }}', '{{ addslashes($p->kampus) }}', '{{ addslashes($p->divisi) }}')"
                                        class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                        title="Edit Data Pemagang">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>

                                    {{-- Delete Button --}}
                                    <form method="POST" action="{{ route($routePrefix . '.pemagang.destroy', $p) }}"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus pemagang {{ addslashes($p->nama_lengkap) }}? Seluruh riwayat presensi yang terkait juga akan terhapus.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                            title="Hapus Pemagang">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ (auth()->user()->isAdmin() || auth()->user()->isHrStaff()) ? 7 : 6 }}"
                                class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <p class="text-sm font-semibold text-gray-600">Tidak ada data pemagang yang ditemukan</p>
                                    <p class="text-xs text-gray-400 mt-1">Coba sesuaikan kata kunci pencarian atau filter Anda.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- MOBILE VIEW: Card List --}}
        <div class="md:hidden divide-y divide-gray-100">
            @if(auth()->user()->isAdmin() || auth()->user()->isHrStaff())
                <div class="p-4 bg-gray-50 flex items-center gap-3">
                    <input type="checkbox"
                        class="select-all-checkbox w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 cursor-pointer">
                    <label class="text-sm font-semibold text-gray-700">Pilih Semua di Halaman Ini</label>
                </div>
            @endif

            @forelse($pemagangs as $index => $p)
                <div class="p-4 hover:bg-gray-50/80 transition row-item" data-id="{{ $p->id }}">
                    <div class="flex items-start gap-3">
                        @if(auth()->user()->isAdmin() || auth()->user()->isHrStaff())
                            <div class="pt-1">
                                <input type="checkbox" name="row-check" value="{{ $p->id }}"
                                    class="row-checkbox w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 cursor-pointer">
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div
                                        class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 font-bold flex items-center justify-center text-xs flex-shrink-0">
                                        {{ strtoupper(substr($p->nama_lengkap, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-900 text-sm truncate">{{ $p->nama_lengkap }}</p>
                                        <p class="text-[11px] text-gray-500 truncate">{{ $p->divisi }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 flex-shrink-0">
                                    <button type="button"
                                        onclick="openEditPemagangModal({{ $p->id }}, '{{ addslashes($p->nama_lengkap) }}', '{{ addslashes($p->no_hp) }}', '{{ addslashes($p->kampus) }}', '{{ addslashes($p->divisi) }}')"
                                        class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                        title="Edit Data Pemagang">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <form method="POST" action="{{ route($routePrefix . '.pemagang.destroy', $p) }}"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus pemagang {{ addslashes($p->nama_lengkap) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                            title="Hapus Pemagang">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="mt-3 space-y-1.5 pl-10">
                                <div class="flex items-center gap-1.5 text-xs text-gray-600">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <span class="truncate">{{ $p->kampus }}</span>
                                </div>
                                <div class="flex items-center gap-1.5 text-xs text-gray-600 font-mono">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <span>{{ $p->no_hp ?: '-' }}</span>
                                </div>
                                <div class="flex items-center gap-1.5 text-xs pt-1">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        {{ $p->presensis_count ?? 0 }} kali presensi
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center text-gray-400">
                    <div class="flex flex-col items-center justify-center">
                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <p class="text-sm font-semibold text-gray-600">Tidak ada data pemagang</p>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($pemagangs->hasPages() || $pemagangs->total() > 0)
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                @if($pemagangs->hasPages())
                    {{-- Pagination dengan navigasi --}}
                    {{ $pemagangs->links() }}
                @else
                    {{-- Info jumlah data tanpa pagination --}}
                    <div class="text-xs text-gray-600 text-center">
                        Menampilkan
                        <span class="font-semibold text-gray-800">{{ $pemagangs->total() }}</span>
                        data pemagang
                    </div>
                @endif
            </div>
        @endif
    </div>

    {{-- ── Hidden Bulk Delete Form ─────────────────────────────────── --}}
    @if(auth()->user()->isAdmin() || auth()->user()->isHrStaff())
        <form id="form-bulk-delete" method="POST" action="{{ route($routePrefix . '.pemagang.bulk-destroy') }}" class="hidden">
            @csrf
            @method('DELETE')
            {{-- IDs will be appended dynamically via JS --}}
        </form>

        {{-- ── Modal Konfirmasi Bulk Delete ───────────────────────────── --}}
        <div id="modal-bulk-delete" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeBulkDeleteModal()"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm z-10 overflow-hidden">
                <div class="px-5 py-4 border-b border-rose-100 bg-rose-50 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-800">Konfirmasi Hapus Massal</h3>
                        <p class="text-[11px] text-gray-500 mt-0.5">Tindakan ini tidak dapat dibatalkan</p>
                    </div>
                </div>
                <div class="p-5 space-y-3">
                    <p class="text-sm text-gray-700">
                        Anda akan menghapus <strong id="bulk-delete-count" class="text-rose-600">0</strong> data pemagang.
                        Seluruh riwayat presensi yang terkait juga akan ikut terhapus.
                    </p>
                    <div
                        class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-800 flex items-start gap-2">
                        <svg class="w-4 h-4 shrink-0 mt-0.5 text-amber-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Data yang dihapus <strong>tidak dapat dipulihkan</strong> kembali. Pastikan Anda sudah
                            yakin.</span>
                    </div>
                </div>
                <div class="px-5 pb-5 flex gap-3">
                    <button type="button" onclick="closeBulkDeleteModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="button" onclick="submitBulkDelete()"
                        class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-bold shadow-sm transition active:scale-95">
                        Ya
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ── 4. MODAL TAMBAH PEMAGANG ───────────────────────────────── --}}
    <div id="modal-add-pemagang"
        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 transition-all duration-200">
        <div
            class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-visible animate-in fade-in zoom-in-95 duration-150">

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 bg-gray-50/50 rounded-t-2xl">
                <div class="flex items-center gap-2.5">
                    <div
                        class="w-8 h-8 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-gray-800">Tambah Data Pemagang</h3>
                        <p class="text-[11px] text-gray-500">Daftarkan pemagang baru ke sistem</p>
                    </div>
                </div>
                <button type="button" onclick="closeAddPemagangModal()"
                    class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route($routePrefix . '.pemagang.store') }}"
                class="px-5 py-4 pt-1 space-y-3 rounded-b-2xl">
                @csrf

                {{-- Nama Lengkap --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_lengkap" required placeholder="Contoh: Budi Pratama"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition hover:bg-white hover:border-primary-500">
                </div>

                {{-- Nomor WhatsApp --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Nomor WhatsApp / HP <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="no_hp" id="add-no-hp" required placeholder="Contoh: 081234567890"
                        inputmode="numeric" maxlength="14" minlength="10"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 14)"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs sm:text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition hover:bg-white hover:border-primary-500">
                    <p class="text-[10px] text-gray-400 mt-1">Hanya angka, maksimal 14 digit</p>
                </div>

                {{-- Asal Kampus / Sekolah --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Asal Kampus / Sekolah <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="kampus" required placeholder="Contoh: Universitas Indonesia / SMK 1"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition hover:bg-white hover:border-primary-500">
                </div>

                {{-- Divisi dengan Fitur Pencarian Real-Time --}}
                <div class="relative" id="add-searchable-divisi-container">
                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Divisi Magang <span class="text-red-500">*</span>
                    </label>

                    <input type="hidden" name="divisi" id="add-pemagang-divisi" required>

                    <div id="add-divisi-select-trigger" onclick="toggleAddDivisiDropdown()"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs sm:text-sm flex items-center justify-between cursor-pointer hover:bg-white hover:border-primary-500 transition">
                        <span id="add-selected-divisi-text" class="text-gray-400">-- Cari & Pilih Divisi Magang --</span>
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>

                    <div id="add-divisi-dropdown-menu"
                        class="hidden absolute z-50 left-0 right-0 mt-1 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden animate-in fade-in duration-100">
                        <div class="p-2 border-b border-gray-100 bg-gray-50/90 sticky top-0 z-10">
                            <div class="relative">
                                <span
                                    class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none text-gray-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </span>
                                <input type="text" id="add-divisi-search-input" oninput="filterAddDivisiOptions(this.value)"
                                    placeholder="Ketik untuk mencari divisi..."
                                    class="w-full pl-8 pr-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                        <div id="add-divisi-options-list" class="max-h-48 overflow-y-auto divide-y divide-gray-50">
                            @foreach($divisiList as $div)
                                <div onclick="selectAddDivisi('{{ addslashes($div) }}')" data-search="{{ strtolower($div) }}"
                                    class="add-divisi-option px-3 py-2 hover:bg-primary-50 cursor-pointer transition flex items-center justify-between text-xs font-medium text-gray-700 hover:text-primary-700">
                                    <span>{{ $div }}</span>
                                </div>
                            @endforeach
                            <div id="add-divisi-no-result" class="hidden px-3 py-3 text-center text-xs text-gray-400">
                                Tidak ada divisi yang cocok
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100">
                    <button type="button" onclick="closeAddPemagangModal()"
                        class="px-4 py-2 border border-gray-200 text-gray-700 text-xs sm:text-sm font-semibold rounded-xl hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-sm transition">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── 5. MODAL EDIT PEMAGANG ─────────────────────────────────── --}}
    <div id="modal-edit-pemagang" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEditPemagangModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10 overflow-visible">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50/50">
                <h3 class="text-base font-bold text-gray-800">Edit Data Pemagang</h3>
                <button type="button" onclick="closeEditPemagangModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="form-edit-pemagang" method="POST" action="" class="px-5 py-4 pt-1 space-y-3">
                @csrf
                @method('PATCH')

                {{-- Nama Lengkap --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_lengkap" id="edit-nama" required
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition hover:bg-white hover:border-primary-500">
                </div>

                {{-- No WhatsApp / HP --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Nomor WhatsApp / HP <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="no_hp" id="edit-nohp" required inputmode="numeric" maxlength="14"
                        minlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 14)"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs sm:text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition hover:bg-white hover:border-primary-500">
                    <p class="text-[10px] text-gray-400 mt-1">Hanya angka, maksimal 14 digit</p>
                </div>

                {{-- Asal Kampus / Sekolah --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Asal Kampus / Sekolah <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="kampus" id="edit-kampus" required
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition hover:bg-white hover:border-primary-500">
                </div>

                {{-- Divisi dengan Fitur Pencarian Real-Time --}}
                <div class="relative" id="edit-searchable-divisi-container">
                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Divisi Magang <span class="text-red-500">*</span>
                    </label>

                    <input type="hidden" name="divisi" id="edit-pemagang-divisi" required>

                    <div id="edit-divisi-select-trigger" onclick="toggleEditDivisiDropdown()"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs sm:text-sm flex items-center justify-between cursor-pointer hover:bg-white hover:border-primary-500 transition">
                        <span id="edit-selected-divisi-text" class="text-gray-400">-- Cari & Pilih Divisi Magang --</span>
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>

                    <div id="edit-divisi-dropdown-menu"
                        class="hidden absolute z-50 left-0 right-0 mt-1 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden animate-in fade-in duration-100">
                        <div class="p-2 border-b border-gray-100 bg-gray-50/90 sticky top-0 z-10">
                            <div class="relative">
                                <span
                                    class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none text-gray-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </span>
                                <input type="text" id="edit-divisi-search-input"
                                    oninput="filterEditDivisiOptions(this.value)"
                                    placeholder="Ketik untuk mencari divisi..."
                                    class="w-full pl-8 pr-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                        <div id="edit-divisi-options-list" class="max-h-48 overflow-y-auto divide-y divide-gray-50">
                            @foreach($divisiList as $div)
                                <div onclick="selectEditDivisi('{{ addslashes($div) }}')" data-search="{{ strtolower($div) }}"
                                    class="edit-divisi-option px-3 py-2 hover:bg-primary-50 cursor-pointer transition flex items-center justify-between text-xs font-medium text-gray-700 hover:text-primary-700">
                                    <span>{{ $div }}</span>
                                </div>
                            @endforeach
                            <div id="edit-divisi-no-result" class="hidden px-3 py-3 text-center text-xs text-gray-400">
                                Tidak ada divisi yang cocok
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeEditPemagangModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const routePrefix = '{{ $routePrefix }}';

        // ─────────────────────────────────────────────
        // Add / Edit modal helpers
        // ─────────────────────────────────────────────
        function openAddPemagangModal() {
            // Reset form state
            document.getElementById('add-no-hp').value = '';
            document.getElementById('add-pemagang-divisi').value = '';
            document.getElementById('add-selected-divisi-text').textContent = '-- Cari & Pilih Divisi Magang --';
            const searchInput = document.getElementById('add-divisi-search-input');
            if (searchInput) { searchInput.value = ''; filterAddDivisiOptions(''); }
            const dropdown = document.getElementById('add-divisi-dropdown-menu');
            if (dropdown) dropdown.classList.add('hidden');
            document.getElementById('modal-add-pemagang').classList.remove('hidden');
        }
        function closeAddPemagangModal() {
            document.getElementById('modal-add-pemagang').classList.add('hidden');
        }
        function openEditPemagangModal(id, nama, noHp, kampus, divisi) {
            document.getElementById('form-edit-pemagang').action = `/${routePrefix}/pemagang/${id}`;
            document.getElementById('edit-nama').value = nama;
            document.getElementById('edit-nohp').value = noHp;
            document.getElementById('edit-kampus').value = kampus;

            // Set divisi searchable dropdown
            document.getElementById('edit-pemagang-divisi').value = divisi;
            document.getElementById('edit-selected-divisi-text').textContent = divisi;
            document.getElementById('edit-selected-divisi-text').classList.remove('text-gray-400');
            document.getElementById('edit-selected-divisi-text').classList.add('text-gray-800', 'font-medium');

            // Reset search and close dropdown
            const searchInput = document.getElementById('edit-divisi-search-input');
            if (searchInput) { searchInput.value = ''; filterEditDivisiOptions(''); }
            const dropdown = document.getElementById('edit-divisi-dropdown-menu');
            if (dropdown) dropdown.classList.add('hidden');

            document.getElementById('modal-edit-pemagang').classList.remove('hidden');
        }
        function closeEditPemagangModal() {
            document.getElementById('modal-edit-pemagang').classList.add('hidden');
        }

        // ─────────────────────────────────────────────
        // Bulk Select Feature
        // ─────────────────────────────────────────────
        const STORAGE_KEY = 'pemagang_selected_ids';

        /** Return the current Set of selected IDs from sessionStorage */
        function getSelectedIds() {
            try {
                const raw = sessionStorage.getItem(STORAGE_KEY);
                return raw ? new Set(JSON.parse(raw)) : new Set();
            } catch { return new Set(); }
        }

        /** Persist the given Set back to sessionStorage */
        function saveSelectedIds(set) {
            sessionStorage.setItem(STORAGE_KEY, JSON.stringify([...set]));
        }

        /** Refresh the sticky bulk-action bar and header checkbox state */
        function refreshBulkBar() {
            const ids = getSelectedIds();
            const count = ids.size;
            const bar = document.getElementById('bulk-action-bar');
            const badge = document.getElementById('bulk-count-badge');

            if (!bar) return;

            if (count > 0) {
                bar.classList.remove('hidden');
                if (badge) badge.textContent = count;
            } else {
                bar.classList.add('hidden');
            }

            // Sync header checkboxes
            const allBoxes = document.querySelectorAll('.select-all-checkbox');
            // get only desktop checkboxes to determine page completion (since mobile duplicates them, counting by unique ID is better)
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');

            if (allBoxes.length > 0 && rowCheckboxes.length > 0) {
                // Get unique checked IDs on current page
                const uniqueIdsOnPage = new Set(
                    Array.from(rowCheckboxes).map(cb => Number(cb.value))
                );

                let checkedCount = 0;
                uniqueIdsOnPage.forEach(id => {
                    if (ids.has(id)) checkedCount++;
                });

                const totalOnPage = uniqueIdsOnPage.size;

                allBoxes.forEach(allBox => {
                    allBox.checked = (checkedCount === totalOnPage && totalOnPage > 0);
                    allBox.indeterminate = (checkedCount > 0 && checkedCount < totalOnPage);
                });
            }
        }

        /** Tick checkboxes that are in sessionStorage on page load */
        function restoreCheckedState() {
            const ids = getSelectedIds();
            document.querySelectorAll('.row-checkbox').forEach(cb => {
                const id = Number(cb.value);
                if (ids.has(id)) {
                    cb.checked = true;
                    cb.closest('.row-item')?.classList.add('bg-primary-50/60');
                }
            });
            refreshBulkBar();
        }

        /** Handle individual row checkbox toggle */
        function handleRowCheck(cb) {
            const ids = getSelectedIds();
            const id = Number(cb.value);
            const isChecked = cb.checked;

            if (isChecked) ids.add(id);
            else ids.delete(id);

            // Sync all checkboxes matching this ID (desktop & mobile)
            document.querySelectorAll(`.row-checkbox[value="${id}"]`).forEach(twin => {
                twin.checked = isChecked;
                if (isChecked) {
                    twin.closest('.row-item')?.classList.add('bg-primary-50/60');
                } else {
                    twin.closest('.row-item')?.classList.remove('bg-primary-50/60');
                }
            });

            saveSelectedIds(ids);
            refreshBulkBar();
        }

        /** Handle Select-All checkbox (only for current page) */
        function handleSelectAll(allCb) {
            const ids = getSelectedIds();
            const isChecked = allCb.checked;

            document.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.checked = isChecked;
                const id = Number(cb.value);
                if (isChecked) {
                    ids.add(id);
                    cb.closest('.row-item')?.classList.add('bg-primary-50/60');
                } else {
                    ids.delete(id);
                    cb.closest('.row-item')?.classList.remove('bg-primary-50/60');
                }
            });

            saveSelectedIds(ids);
            refreshBulkBar();
        }

        /** Clear all selections globally */
        function clearAllSelections() {
            sessionStorage.removeItem(STORAGE_KEY);
            document.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.checked = false;
                cb.closest('.row-item')?.classList.remove('bg-primary-50/60');
            });
            document.querySelectorAll('.select-all-checkbox').forEach(allBox => {
                allBox.checked = false;
                allBox.indeterminate = false;
            });
            refreshBulkBar();
        }

        // Bulk delete modal
        function openBulkDeleteModal() {
            const count = getSelectedIds().size;
            const el = document.getElementById('bulk-delete-count');
            if (el) el.textContent = count;
            document.getElementById('modal-bulk-delete')?.classList.remove('hidden');
        }
        function closeBulkDeleteModal() {
            document.getElementById('modal-bulk-delete')?.classList.add('hidden');
        }

        /** Build the form inputs and submit */
        function submitBulkDelete() {
            const ids = getSelectedIds();
            const form = document.getElementById('form-bulk-delete');
            if (!form || ids.size === 0) return;

            // Remove any previously appended inputs
            form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());

            ids.forEach(id => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'ids[]';
                inp.value = id;
                form.appendChild(inp);
            });

            // Clear storage before submit so no stale selections remain
            sessionStorage.removeItem(STORAGE_KEY);
            form.submit();
        }

        // ─────────────────────────────────────────────
        // Wire-up event listeners on DOM ready
        // ─────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', () => {
            restoreCheckedState();

            document.querySelectorAll('.select-all-checkbox').forEach(allBox => {
                allBox.addEventListener('change', () => handleSelectAll(allBox));
            });

            document.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.addEventListener('change', () => handleRowCheck(cb));
            });

            // Close bulk delete modal on Escape
            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') closeBulkDeleteModal();
            });
        });
        // ─────────────────────────────────────────────
        // Searchable Divisi Dropdown (Modal Tambah)
        // ─────────────────────────────────────────────
        function toggleAddDivisiDropdown() {
            const dropdown = document.getElementById('add-divisi-dropdown-menu');
            dropdown.classList.toggle('hidden');
            if (!dropdown.classList.contains('hidden')) {
                const searchInput = document.getElementById('add-divisi-search-input');
                if (searchInput) { searchInput.value = ''; filterAddDivisiOptions(''); setTimeout(() => searchInput.focus(), 50); }
            }
        }

        function filterAddDivisiOptions(keyword) {
            const kw = keyword.toLowerCase().trim();
            const options = document.querySelectorAll('.add-divisi-option');
            let visibleCount = 0;
            options.forEach(opt => {
                const text = opt.getAttribute('data-search') || '';
                if (text.includes(kw)) { opt.classList.remove('hidden'); visibleCount++; }
                else { opt.classList.add('hidden'); }
            });
            const noResult = document.getElementById('add-divisi-no-result');
            if (noResult) noResult.classList.toggle('hidden', visibleCount > 0);
        }

        function selectAddDivisi(value) {
            document.getElementById('add-pemagang-divisi').value = value;
            document.getElementById('add-selected-divisi-text').textContent = value;
            document.getElementById('add-selected-divisi-text').classList.remove('text-gray-400');
            document.getElementById('add-selected-divisi-text').classList.add('text-gray-800', 'font-medium');
            document.getElementById('add-divisi-dropdown-menu').classList.add('hidden');
        }

        // Tutup dropdown saat klik di luar
        document.addEventListener('click', function (e) {
            const container = document.getElementById('add-searchable-divisi-container');
            if (container && !container.contains(e.target)) {
                const dropdown = document.getElementById('add-divisi-dropdown-menu');
                if (dropdown) dropdown.classList.add('hidden');
            }

            const editContainer = document.getElementById('edit-searchable-divisi-container');
            if (editContainer && !editContainer.contains(e.target)) {
                const editDropdown = document.getElementById('edit-divisi-dropdown-menu');
                if (editDropdown) editDropdown.classList.add('hidden');
            }
        });

        // ─────────────────────────────────────────────
        // Searchable Divisi Dropdown (Modal Edit)
        // ─────────────────────────────────────────────
        function toggleEditDivisiDropdown() {
            const dropdown = document.getElementById('edit-divisi-dropdown-menu');
            dropdown.classList.toggle('hidden');
            if (!dropdown.classList.contains('hidden')) {
                const searchInput = document.getElementById('edit-divisi-search-input');
                if (searchInput) { searchInput.value = ''; filterEditDivisiOptions(''); setTimeout(() => searchInput.focus(), 50); }
            }
        }

        function filterEditDivisiOptions(keyword) {
            const kw = keyword.toLowerCase().trim();
            const options = document.querySelectorAll('.edit-divisi-option');
            let visibleCount = 0;
            options.forEach(opt => {
                const text = opt.getAttribute('data-search') || '';
                if (text.includes(kw)) { opt.classList.remove('hidden'); visibleCount++; }
                else { opt.classList.add('hidden'); }
            });
            const noResult = document.getElementById('edit-divisi-no-result');
            if (noResult) noResult.classList.toggle('hidden', visibleCount > 0);
        }

        function selectEditDivisi(value) {
            document.getElementById('edit-pemagang-divisi').value = value;
            document.getElementById('edit-selected-divisi-text').textContent = value;
            document.getElementById('edit-selected-divisi-text').classList.remove('text-gray-400');
            document.getElementById('edit-selected-divisi-text').classList.add('text-gray-800', 'font-medium');
            document.getElementById('edit-divisi-dropdown-menu').classList.add('hidden');
        }

    </script>
@endpush