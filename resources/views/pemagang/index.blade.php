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
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
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
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
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
                          d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Hadir Hari Ini --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Hadir Hari Ini</p>
                <p class="text-2xl font-extrabold text-emerald-600 mt-1">{{ number_format($stats['hadir_hari_ini']) }}</p>
                <p class="text-[11px] text-gray-400 mt-0.5">Pemagang aktif</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

{{-- ── 2. TOOLBAR PENCARIAN & FILTER ──────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5 mb-6 shadow-sm">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        {{-- Form Filter --}}
        <form method="GET" action="{{ route($routePrefix . '.pemagang.index') }}"
              class="flex-1 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-3 items-end">

            {{-- Input Search --}}
            <div class="md:col-span-5">
                <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Cari Pemagang</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                           placeholder="Cari nama atau nomor WhatsApp / HP..."
                           class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition">
                </div>
            </div>

            {{-- Filter Divisi --}}
            <div class="md:col-span-3">
                <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Divisi</label>
                <select name="divisi"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition">
                    <option value="">Semua Divisi</option>
                    @foreach($divisiList as $d)
                        <option value="{{ $d }}" {{ ($divisi ?? '') === $d ? 'selected' : '' }}>{{ $d }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Kampus --}}
            <div class="md:col-span-2">
                <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Kampus / Sekolah</label>
                <select name="kampus"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition">
                    <option value="">Semua Asal</option>
                    @foreach($kampusList as $k)
                        <option value="{{ $k }}" {{ ($kampus ?? '') === $k ? 'selected' : '' }}>{{ $k }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Buttons --}}
            <div class="md:col-span-2 flex items-center gap-2">
                <button type="submit"
                        class="flex-1 flex items-center justify-center gap-1.5 px-3.5 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs sm:text-sm font-semibold rounded-lg transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>Filter</span>
                </button>
                @if($search || $divisi || $kampus)
                    <a href="{{ route($routePrefix . '.pemagang.index') }}"
                       class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs sm:text-sm font-semibold rounded-lg transition"
                       title="Reset Filter">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        {{-- Button Tambah Pemagang --}}
        <div class="shrink-0 pt-2 lg:pt-0 border-t lg:border-t-0 border-gray-100 flex items-end">
            <button type="button" onclick="openAddPemagangModal()"
                    class="w-full lg:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-semibold rounded-lg transition shadow-sm hover:shadow">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Pemagang Baru</span>
            </button>
        </div>
    </div>
</div>

{{-- ── 3. TABEL DAFTAR PEMAGANG ───────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 bg-gray-50/50">
        <div>
            <h2 class="text-sm font-bold text-gray-900">Data Seluruh Anak Magang</h2>
            <p class="text-xs text-gray-500 mt-0.5">Daftar lengkap pemagang, asal kampus, divisi, dan riwayat presensi</p>
        </div>
        <span class="text-xs font-semibold px-3 py-1 bg-primary-50 text-primary-700 rounded-full border border-primary-200 self-start sm:self-auto">
            {{ $pemagangs->total() }} Pemagang
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm min-w-[750px]">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    <th class="px-6 py-3.5 w-14 text-center">No</th>
                    <th class="px-6 py-3.5">Nama Lengkap</th>
                    <th class="px-6 py-3.5">No. WhatsApp / HP</th>
                    <th class="px-6 py-3.5">Asal Kampus / Sekolah</th>
                    <th class="px-6 py-3.5">Divisi Magang</th>
                    <th class="px-6 py-3.5 text-center">Riwayat Presensi</th>
                    <th class="px-6 py-3.5 text-right w-36">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pemagangs as $index => $p)
                    <tr class="hover:bg-gray-50/80 transition align-middle">
                        {{-- No --}}
                        <td class="px-6 py-4 text-center text-xs text-gray-400 font-medium">
                            {{ $pemagangs->firstItem() ? ($pemagangs->firstItem() + $index) : ($index + 1) }}
                        </td>

                        {{-- Nama --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 font-bold flex items-center justify-center text-xs flex-shrink-0">
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
                            <span class="text-xs text-gray-800 font-medium block truncate max-w-[200px]" title="{{ $p->kampus }}">
                                {{ $p->kampus }}
                            </span>
                        </td>

                        {{-- Divisi --}}
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-700">
                                {{ $p->divisi }}
                            </span>
                        </td>

                        {{-- Riwayat Presensi --}}
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
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
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
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
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <p class="text-sm font-semibold text-gray-600">Tidak ada data pemagang yang ditemukan</p>
                                <p class="text-xs text-gray-400 mt-1">Coba sesuaikan kata kunci pencarian atau filter Anda.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($pemagangs->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $pemagangs->links() }}
        </div>
    @endif
</div>

{{-- ── 4. MODAL TAMBAH PEMAGANG ───────────────────────────────── --}}
<div id="modal-add-pemagang" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeAddPemagangModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50/50">
            <h3 class="text-base font-bold text-gray-800">Tambah Pemagang Baru</h3>
            <button type="button" onclick="closeAddPemagangModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route($routePrefix . '.pemagang.store') }}" class="p-6 space-y-4">
            @csrf

            {{-- Nama Lengkap --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="nama_lengkap" required placeholder="Contoh: Budi Pratama"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
            </div>

            {{-- No WhatsApp / HP --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor WhatsApp / HP <span class="text-red-500">*</span></label>
                <input type="text" name="no_hp" required placeholder="Contoh: 081234567890"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none font-mono">
            </div>

            {{-- Asal Kampus / Sekolah --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Asal Kampus / Sekolah <span class="text-red-500">*</span></label>
                <input type="text" name="kampus" required placeholder="Contoh: Universitas Gadjah Mada"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
            </div>

            {{-- Divisi --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Divisi Magang <span class="text-red-500">*</span></label>
                <select name="divisi" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    <option value="">-- Pilih Divisi --</option>
                    @foreach($divisiList as $d)
                        <option value="{{ $d }}">{{ $d }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeAddPemagangModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── 5. MODAL EDIT PEMAGANG ─────────────────────────────────── --}}
<div id="modal-edit-pemagang" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEditPemagangModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50/50">
            <h3 class="text-base font-bold text-gray-800">Edit Data Pemagang</h3>
            <button type="button" onclick="closeEditPemagangModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="form-edit-pemagang" method="POST" action="" class="p-6 space-y-4">
            @csrf
            @method('PATCH')

            {{-- Nama Lengkap --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="nama_lengkap" id="edit-nama" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
            </div>

            {{-- No WhatsApp / HP --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor WhatsApp / HP <span class="text-red-500">*</span></label>
                <input type="text" name="no_hp" id="edit-nohp" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none font-mono">
            </div>

            {{-- Asal Kampus / Sekolah --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Asal Kampus / Sekolah <span class="text-red-500">*</span></label>
                <input type="text" name="kampus" id="edit-kampus" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
            </div>

            {{-- Divisi --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Divisi Magang <span class="text-red-500">*</span></label>
                <select name="divisi" id="edit-divisi" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    <option value="">-- Pilih Divisi --</option>
                    @foreach($divisiList as $d)
                        <option value="{{ $d }}">{{ $d }}</option>
                    @endforeach
                </select>
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

    function openAddPemagangModal() {
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
        document.getElementById('edit-divisi').value = divisi;
        document.getElementById('modal-edit-pemagang').classList.remove('hidden');
    }

    function closeEditPemagangModal() {
        document.getElementById('modal-edit-pemagang').classList.add('hidden');
    }
</script>
@endpush
