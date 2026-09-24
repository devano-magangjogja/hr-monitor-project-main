@extends('layouts.app')

@section('title', 'Presensi Pemagang')
@section('page-title', 'Presensi Pemagang')
@section('page-subtitle', 'Presensi tanggal: ' . $formattedDate)

@section('sidebar')
    @include('components.sidebar-admin')
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
            <button type="button" onclick="openCreatePemagangModal()"
                class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-xs sm:text-sm font-semibold rounded-xl transition shadow-sm hover:shadow">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Pemagang</span>
            </button>
            <button onclick="openCreateModal()"
                class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2.5 text-white text-xs sm:text-sm font-semibold rounded-xl transition shadow-sm hover:shadow"
                style="background-color: #DC2626;" onmouseover="this.style.backgroundColor='#B91C1C'"
                onmouseout="this.style.backgroundColor='#DC2626'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Catat Presensi</span>
            </button>
        </div>
    </div>

    @if ($asistenKantors->count() > 0)
        <div
            class="mb-5 flex items-center gap-2 flex-wrap bg-blue-50/80 border border-blue-200 rounded-xl px-4 py-2.5 text-xs text-blue-900 shadow-2xs">
            <span class="font-bold flex items-center gap-1">Asisten Bertugas Hari Ini:</span>
            @foreach ($asistenKantors as $ak)
                <span
                    class="inline-flex items-center gap-1 bg-white border border-blue-200 px-2.5 py-1 rounded-lg font-medium text-blue-800 shadow-2xs">
                    <strong>{{ $ak['name'] }}</strong> &rarr; <span
                        class="text-blue-600 font-bold">{{ $ak['kantor'] }}</span>
                </span>
            @endforeach
        </div>
    @endif

    {{-- ── Stat Cards Ringkasan Tanggal Ini ────────────────────── --}}
    @php
        $activeKet = request('keterangan');
    @endphp
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
        <div onclick="filterKet('Lebih Awal')" title="Klik untuk filter tabel Lebih Awal"
            class="rounded-xl border p-4 shadow-sm cursor-pointer transition-all select-none
                    {{ $activeKet === 'Lebih Awal'
                        ? 'bg-indigo-50 border-indigo-400 ring-2 ring-indigo-200 shadow-indigo-100'
                        : 'bg-white border-gray-200 hover:border-indigo-300 hover:shadow-md' }}">
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
            <p
                class="text-[11px] mt-0.5 {{ $activeKet === 'Lebih Awal' ? 'text-indigo-500 font-semibold' : 'text-gray-400' }}">
                {{ $activeKet === 'Lebih Awal' ? '▼ filter aktif' : 'hadir lebih awal' }}
            </p>
        </div>

        {{-- Tepat Waktu --}}
        <div onclick="filterKet('Tepat Waktu')" title="Klik untuk filter tabel Tepat Waktu"
            class="rounded-xl border p-4 shadow-sm cursor-pointer transition-all select-none
                    {{ $activeKet === 'Tepat Waktu'
                        ? 'bg-green-50 border-green-400 ring-2 ring-green-200 shadow-green-100'
                        : 'bg-white border-gray-200 hover:border-green-300 hover:shadow-md' }}">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500">Tepat Waktu</p>
                <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-green-600">{{ $stats['tepat_waktu'] }}</p>
            <p
                class="text-[11px] mt-0.5 {{ $activeKet === 'Tepat Waktu' ? 'text-green-500 font-semibold' : 'text-gray-400' }}">
                {{ $activeKet === 'Tepat Waktu' ? '▼ filter aktif' : 'hadir tepat waktu' }}
            </p>
        </div>

        {{-- Terlambat --}}
        <div onclick="filterKet('Terlambat')" title="Klik untuk filter tabel Terlambat"
            class="rounded-xl border p-4 shadow-sm cursor-pointer transition-all select-none
                    {{ $activeKet === 'Terlambat'
                        ? 'bg-amber-50 border-amber-400 ring-2 ring-amber-200 shadow-amber-100'
                        : 'bg-white border-gray-200 hover:border-amber-300 hover:shadow-md' }}">
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
            <p
                class="text-[11px] mt-0.5 {{ $activeKet === 'Terlambat' ? 'text-amber-500 font-semibold' : 'text-gray-400' }}">
                {{ $activeKet === 'Terlambat' ? '▼ filter aktif' : 'terlambat masuk' }}
            </p>
        </div>

        {{-- Tidak Hadir --}}
        <div onclick="scrollToTidakHadir()" title="Klik untuk langsung ke daftar tidak hadir"
            class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm cursor-pointer transition-all select-none hover:border-red-300 hover:shadow-md hover:bg-red-50/30">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500">Tidak Hadir</p>
                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-red-600">{{ $stats['tidak_hadir'] }}</p>
            <p class="text-[11px] text-gray-400 mt-0.5">↓ lihat daftar</p>
        </div>

    </div>

    {{-- ── Tanggal Picker & Filter Header ──────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            {{-- Filter Form --}}
            <form method="GET" action="{{ route('admin.presensi.index') }}"
                class="flex-1 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                <input type="hidden" name="tab" value="{{ $tab }}">

                {{-- Filter Kantor --}}
                <div>
                    <label
                        class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Kantor</label>
                    <select name="kantor" onchange="this.form.submit()"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition text-gray-700">
                        <option value="">Semua Kantor</option>
                        @foreach ($kantorList as $k)
                            <option value="{{ $k }}" {{ request('kantor') == $k ? 'selected' : '' }}>
                                {{ $k }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Shift --}}
                <div>
                    <label
                        class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Shift</label>
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
                        @foreach ($divisiList as $div)
                            <option value="{{ $div }}" {{ request('divisi') == $div ? 'selected' : '' }}>
                                {{ $div }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Input Tanggal (PALING KANAN) --}}
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Pilih
                        Tanggal</label>
                    <input type="date" name="tanggal" value="{{ $tanggal }}"
                        max="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" onchange="this.form.submit()"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs sm:text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition">
                </div>
            </form>

            @if (!$isToday)
                <div class="flex items-center gap-2 flex-shrink-0 self-end lg:self-center">
                    <a href="{{ route('admin.presensi.index') }}"
                        class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs sm:text-sm font-semibold rounded-lg transition"
                        title="Kembali ke Hari Ini">
                        Hari Ini
                    </a>
                </div>
            @endif
        </div>
    </div>


    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- ── TAB PRESENSI: Sudah / Belum / Tidak Hadir ──────────── --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <x-presensi-tabs
        :tab="$tab"
        :presensi-hadir="$presensiHadir"
        :pemagang-belum="$pemagangBelum"
        :presensi-tidak-hadir="$presensiTidakHadir"
        :active-ket="request('keterangan')"
        index-route="admin.presensi.index"
    />

    {{-- ── MODAL CATAT PRESENSI ──────────────────────────────── --}}
    <div id="modal-create" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
        <div
            class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-200 bg-gray-50/50">
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-gray-800">Catat Presensi Pemagang</h3>
                    <p class="text-[11px] text-gray-500 mt-0.5">Input kehadiran pemagang hari ini</p>
                </div>
                <button onclick="document.getElementById('modal-create').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.presensi.store') }}" method="POST" class="px-5 pt-3 pb-4 space-y-3">
                @csrf

                {{-- Tanggal Presensi (Otomatis Hari Ini & Terkunci) --}}
                <div class="p-2.5 bg-primary-50/70 border border-primary-100 rounded-xl flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div
                            class="w-7 h-7 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-medium text-gray-500">Tanggal Presensi (Hari Ini)</p>
                            <p class="text-xs sm:text-sm font-bold text-gray-800">
                                {{ Carbon\Carbon::today()->locale('id')->translatedFormat('l, d F Y') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Pilih Pemagang dengan Fitur Pencarian Real-Time --}}
                <div class="relative" id="searchable-pemagang-container">
                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Nama Pemagang <span class="text-red-500">*</span>
                    </label>

                    <input type="hidden" name="pemagang_id" id="create-pemagang-id" required>

                    {{-- Trigger Box --}}
                    <div id="pemagang-select-trigger" onclick="togglePemagangDropdown()"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs sm:text-sm flex items-center justify-between cursor-pointer hover:bg-white hover:border-primary-500 transition">
                        <span id="selected-pemagang-text" class="text-gray-400">-- Cari & Pilih Nama Pemagang --</span>
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                <input type="text" id="pemagang-search-input"
                                    oninput="filterPemagangOptions(this.value)"
                                    placeholder="Ketik nama / kampus / divisi..."
                                    class="w-full pl-8 pr-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>

                        {{-- List Opsi Pemagang --}}
                        <div id="pemagang-options-list" class="max-h-44 overflow-y-auto divide-y divide-gray-50">
                            @foreach ($pemagangs as $p)
                                <div onclick="selectPemagang('{{ $p->id }}', '{{ addslashes($p->nama_lengkap) }}', '{{ addslashes($p->kampus) }}', '{{ $p->divisi }}')"
                                    data-search="{{ strtolower($p->nama_lengkap . ' ' . $p->kampus . ' ' . $p->divisi) }}"
                                    class="pemagang-option px-3 py-2 hover:bg-primary-50/80 cursor-pointer transition flex items-center justify-between group">
                                    <div>
                                        <p class="text-xs font-semibold text-gray-800 group-hover:text-primary-700">
                                            {{ $p->nama_lengkap }}
                                        </p>
                                        <p class="text-[10px] text-gray-400">{{ $p->kampus }}</p>
                                    </div>
                                    <span
                                        class="text-[9px] font-medium px-1.5 py-0.5 rounded bg-gray-100 text-gray-600 group-hover:bg-primary-100 group-hover:text-primary-800">
                                        {{ $p->divisi }}
                                    </span>
                                </div>
                            @endforeach
                            <div id="pemagang-no-result" class="hidden px-3 py-3 text-center text-xs text-gray-400">
                                Tidak ada pemagang yang cocok
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Shift Kerja & Lokasi Kantor --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Shift Kerja <span class="text-red-500">*</span>
                        </label>
                        <select name="shift" required
                            class="w-full border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                            <option value="Pagi">Shift Pagi</option>
                            <option value="Middle">Shift Middle</option>
                            <option value="Siang">Shift Siang</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Lokasi Kantor <span class="text-red-500">*</span>
                        </label>
                        <select name="kantor" required
                            class="w-full border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                            @foreach ($kantorList as $k)
                                <option value="{{ $k }}">{{ $k }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Waktu Masuk (24 Jam Ketik Langsung) --}}
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider">
                            Waktu Masuk (24 Jam) <span class="text-red-500">*</span>
                        </label>
                        <span class="text-[10px] text-gray-400 font-medium">Format: 00:00 - 23:59</span>
                    </div>
                    <div class="flex items-center gap-2">
                        {{-- Input Jam --}}
                        <div class="relative flex-1">
                            <input type="text" id="create-waktu-jam" inputmode="numeric" maxlength="2"
                                value="{{ date('H') }}" placeholder="08"
                                oninput="validateTimeInput(this, 23, 'create-waktu-menit')"
                                onblur="formatTimeBlur(this, '08')"
                                class="w-full px-3 py-2 text-center text-sm font-semibold tracking-wider bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition hover:bg-white hover:border-primary-500">
                        </div>

                        <span class="text-base font-extrabold text-gray-500 select-none">:</span>

                        {{-- Input Menit --}}
                        <div class="relative flex-1">
                            <input type="text" id="create-waktu-menit" inputmode="numeric" maxlength="2"
                                value="{{ date('i') }}" placeholder="00" oninput="validateTimeInput(this, 59, null)"
                                onblur="formatTimeBlur(this, '00')"
                                class="w-full px-3 py-2 text-center text-sm font-semibold tracking-wider bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition hover:bg-white hover:border-primary-500">
                        </div>

                        {{-- Tombol Jam Sekarang --}}
                        <button type="button" onclick="setCreateTimeToNow()" title="Set waktu saat ini"
                            class="px-2.5 py-2 bg-gray-100 hover:bg-primary-50 hover:text-primary-600 text-gray-600 text-[11px] font-medium border border-gray-200 rounded-lg transition flex items-center gap-1 flex-shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="hidden sm:inline">Sekarang</span>
                        </button>

                        <input type="hidden" name="waktu_masuk" id="create-waktu-hidden" value="{{ date('H:i') }}">
                    </div>
                </div>

                {{-- Status Kehadiran --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Status Keterangan <span class="text-red-500">*</span>
                    </label>
                    <select name="keterangan" required
                        class="w-full border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="Tepat Waktu">Tepat Waktu</option>
                        <option value="Lebih Awal">Lebih Awal</option>
                        <option value="Terlambat">Terlambat</option>
                        <option value="Tidak Hadir">Tidak Hadir (Alpa / Izin / Sakit)</option>
                    </select>
                </div>

                {{-- Catatan / Notes --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Catatan / Keterangan Tambahan
                    </label>
                    <textarea name="notes" rows="2" placeholder="Contoh: Izin terlambat karena kendala transportasi, dsb."
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition hover:bg-white hover:border-primary-500"></textarea>
                </div>

                <div class="flex justify-end gap-2.5 pt-2.5 border-t border-gray-100">
                    <button type="button" onclick="document.getElementById('modal-create').classList.add('hidden')"
                        class="px-3.5 py-2 text-xs sm:text-sm text-gray-600 font-medium border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs sm:text-sm font-medium rounded-lg transition shadow-sm">
                        Simpan Presensi
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ── MODAL EDIT PRESENSI ────────────────────────────────── --}}
    <div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
        <div
            class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-200 bg-gray-50/50">
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-gray-800">Edit Data Presensi</h3>
                    <p id="edit-pemagang-name" class="text-xs text-primary-600 font-medium mt-0.5"></p>
                </div>
                <button onclick="document.getElementById('modal-edit').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="form-edit" action="" method="POST" class="px-5 pt-3 pb-4 space-y-3">
                @csrf
                @method('PATCH')

                <input type="hidden" id="edit-pemagang-id" name="pemagang_id">
                <input type="hidden" id="edit-tanggal" name="tanggal">

                {{-- Tanggal Presensi (Terkunci) --}}
                <div class="p-2.5 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div
                            class="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 flex-shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Tanggal
                                Presensi</span>
                            <p id="edit-tanggal-display" class="text-xs sm:text-sm font-bold text-gray-800"></p>
                        </div>
                    </div>
                    <span
                        class="px-2 py-0.5 bg-gray-100 text-gray-600 text-[10px] rounded-full font-semibold border border-gray-200">
                        Terkunci
                    </span>
                </div>

                {{-- Shift Kerja & Lokasi Kantor --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Shift Kerja <span class="text-red-500">*</span>
                        </label>
                        <select id="edit-shift" name="shift" required
                            class="w-full border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                            <option value="Pagi">Shift Pagi</option>
                            <option value="Middle">Shift Middle</option>
                            <option value="Siang">Shift Siang</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Lokasi Kantor <span class="text-red-500">*</span>
                        </label>
                        <select id="edit-kantor" name="kantor" required
                            class="w-full border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                            @foreach ($kantorList as $k)
                                <option value="{{ $k }}">{{ $k }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Waktu Masuk (24 Jam Ketik Langsung) --}}
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider">
                            Waktu Masuk (24 Jam) <span class="text-red-500">*</span>
                        </label>
                        <span class="text-[10px] text-gray-400 font-medium">Format: 00:00 - 23:59</span>
                    </div>
                    <div class="flex items-center gap-2">
                        {{-- Input Jam --}}
                        <div class="relative flex-1">
                            <input type="text" id="edit-waktu-jam" inputmode="numeric" maxlength="2"
                                placeholder="08" oninput="validateEditTimeInput(this, 23, 'edit-waktu-menit')"
                                onblur="formatEditTimeBlur(this, '08')"
                                class="w-full px-3 py-2 text-center text-sm font-semibold tracking-wider bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition hover:bg-white hover:border-primary-500">
                        </div>

                        <span class="text-base font-extrabold text-gray-500 select-none">:</span>

                        {{-- Input Menit --}}
                        <div class="relative flex-1">
                            <input type="text" id="edit-waktu-menit" inputmode="numeric" maxlength="2"
                                placeholder="00" oninput="validateEditTimeInput(this, 59, null)"
                                onblur="formatEditTimeBlur(this, '00')"
                                class="w-full px-3 py-2 text-center text-sm font-semibold tracking-wider bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition hover:bg-white hover:border-primary-500">
                        </div>

                        {{-- Tombol Jam Sekarang --}}
                        <button type="button" onclick="setEditTimeToNow()" title="Set waktu saat ini"
                            class="px-2.5 py-2 bg-gray-100 hover:bg-primary-50 hover:text-primary-600 text-gray-600 text-[11px] font-medium border border-gray-200 rounded-lg transition flex items-center gap-1 flex-shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="hidden sm:inline">Sekarang</span>
                        </button>

                        <input type="hidden" name="waktu_masuk" id="edit-waktu" value="">
                    </div>
                </div>

                {{-- Status Kehadiran --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Status Keterangan <span class="text-red-500">*</span>
                    </label>
                    <select id="edit-keterangan" name="keterangan" required
                        class="w-full border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="Lebih Awal">Lebih Awal</option>
                        <option value="Tepat Waktu">Tepat Waktu</option>
                        <option value="Terlambat">Terlambat</option>
                        <option value="Tidak Hadir">Tidak Hadir</option>
                    </select>
                </div>

                {{-- Catatan / Notes --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Catatan / Notes
                    </label>
                    <textarea id="edit-notes" name="notes" rows="2"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition hover:bg-white hover:border-primary-500"></textarea>
                </div>

                <div class="flex justify-end gap-2.5 pt-2.5 border-t border-gray-100">
                    <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')"
                        class="px-3.5 py-2 text-xs sm:text-sm text-gray-600 font-medium border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs sm:text-sm font-medium rounded-lg transition shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ── MODAL TAMBAH PEMAGANG BARU ────────────────────────── --}}
    <div id="modal-create-pemagang"
        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 transition-all duration-200">
        <div
            class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-visible animate-in fade-in zoom-in-95 duration-150">

            {{-- Header Modal --}}
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
                <button type="button" onclick="closeCreatePemagangModal()"
                    class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Form Tambah Pemagang --}}
            <form action="{{ route(Auth::user()->role === 'admin' ? 'admin.pemagang.store' : 'staff.pemagang.store') }}"
                method="POST" class="px-5 py-4 pt-1 space-y-3 rounded-b-2xl">
                @csrf

                {{-- Nama Lengkap --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_lengkap" required placeholder="Contoh: John Doe"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition hover:bg-white hover:border-primary-500">
                </div>

                {{-- Nomor WhatsApp --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Nomor WhatsApp / HP <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="no_hp" required placeholder="Contoh: 081234567890" inputmode="numeric"
                        maxlength="14" minlength="10"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 14)"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition hover:bg-white hover:border-primary-500">
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
                <div class="relative" id="searchable-divisi-container">
                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Divisi Magang <span class="text-red-500">*</span>
                    </label>

                    <input type="hidden" name="divisi" id="create-pemagang-divisi" required>

                    {{-- Trigger Box --}}
                    <div id="divisi-select-trigger" onclick="toggleDivisiDropdown()"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs sm:text-sm flex items-center justify-between cursor-pointer hover:bg-white hover:border-primary-500 transition">
                        <span id="selected-divisi-text" class="text-gray-400">-- Cari & Pilih Divisi Magang --</span>
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>

                    {{-- Dropdown Menu Popover --}}
                    <div id="divisi-dropdown-menu"
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
                                <input type="text" id="divisi-search-input" oninput="filterDivisiOptions(this.value)"
                                    placeholder="Ketik untuk mencari divisi..."
                                    class="w-full pl-8 pr-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>

                        {{-- List Opsi Divisi --}}
                        <div id="divisi-options-list" class="max-h-48 overflow-y-auto divide-y divide-gray-50">
                            @foreach ($divisiList as $div)
                                <div onclick="selectDivisi('{{ addslashes($div) }}')"
                                    data-search="{{ strtolower($div) }}"
                                    class="divisi-option px-3 py-2 hover:bg-primary-50 cursor-pointer transition flex items-center justify-between text-xs font-medium text-gray-700 hover:text-primary-700">
                                    <span>{{ $div }}</span>
                                    <svg class="w-3.5 h-3.5 text-primary-600 hidden check-icon" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            @endforeach
                            <div id="divisi-no-result" class="hidden px-3 py-3 text-center text-xs text-gray-400">
                                Tidak ada divisi yang cocok
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Actions --}}
                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100">
                    <button type="button" onclick="closeCreatePemagangModal()"
                        class="px-4 py-2 border border-gray-200 text-gray-700 text-xs sm:text-sm font-semibold rounded-xl hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs sm:text-sm font-semibold rounded-xl transition shadow-sm">
                        Simpan Pemagang
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

        function selectPemagang(id, name, kampus, divisi) {
            document.getElementById('create-pemagang-id').value = id;
            const textEl = document.getElementById('selected-pemagang-text');
            textEl.innerHTML =
                `<strong class="text-gray-800 font-semibold">${name}</strong> <span class="text-xs text-gray-500">(${kampus}) - Div. ${divisi}</span>`;
            document.getElementById('pemagang-dropdown-menu').classList.add('hidden');
        }

        function openCreatePemagangModal() {
            const modal = document.getElementById('modal-create-pemagang');
            if (modal) modal.classList.remove('hidden');
        }

        function closeCreatePemagangModal() {
            const modal = document.getElementById('modal-create-pemagang');
            if (modal) modal.classList.add('hidden');
        }

        function toggleDivisiDropdown() {
            const menu = document.getElementById('divisi-dropdown-menu');
            if (!menu) return;
            const isHidden = menu.classList.contains('hidden');
            if (isHidden) {
                menu.classList.remove('hidden');
                const searchInput = document.getElementById('divisi-search-input');
                if (searchInput) {
                    searchInput.value = '';
                    filterDivisiOptions('');
                    setTimeout(() => searchInput.focus(), 50);
                }
            } else {
                menu.classList.add('hidden');
            }
        }

        function selectDivisi(name) {
            const input = document.getElementById('create-pemagang-divisi');
            if (input) input.value = name;
            const textEl = document.getElementById('selected-divisi-text');
            if (textEl) {
                textEl.textContent = name;
                textEl.classList.remove('text-gray-400');
                textEl.classList.add('text-gray-800', 'font-semibold');
            }

            document.querySelectorAll('.divisi-option').forEach(el => {
                const checkIcon = el.querySelector('.check-icon');
                if (el.textContent.trim() === name) {
                    checkIcon?.classList.remove('hidden');
                    el.classList.add('bg-primary-50');
                } else {
                    checkIcon?.classList.add('hidden');
                    el.classList.remove('bg-primary-50');
                }
            });

            const menu = document.getElementById('divisi-dropdown-menu');
            if (menu) menu.classList.add('hidden');
        }

        function filterDivisiOptions(query) {
            const q = query.toLowerCase().trim();
            const options = document.querySelectorAll('.divisi-option');
            let visibleCount = 0;

            options.forEach(option => {
                const text = option.getAttribute('data-search') || option.textContent.toLowerCase();
                if (!q || text.includes(q)) {
                    option.classList.remove('hidden');
                    visibleCount++;
                } else {
                    option.classList.add('hidden');
                }
            });

            const noResult = document.getElementById('divisi-no-result');
            if (noResult) {
                if (visibleCount === 0) {
                    noResult.classList.remove('hidden');
                } else {
                    noResult.classList.add('hidden');
                }
            }
        }

        // Tutup dropdown saat klik di luar area
        document.addEventListener('click', function(e) {
            const pemagangContainer = document.getElementById('searchable-pemagang-container');
            if (pemagangContainer && !pemagangContainer.contains(e.target)) {
                const dropdown = document.getElementById('pemagang-dropdown-menu');
                if (dropdown) dropdown.classList.add('hidden');
            }

            const divisiContainer = document.getElementById('searchable-divisi-container');
            if (divisiContainer && !divisiContainer.contains(e.target)) {
                const dropdown = document.getElementById('divisi-dropdown-menu');
                if (dropdown) dropdown.classList.add('hidden');
            }
        });

        function validateTimeInput(input, maxVal, nextFieldId) {
            let val = input.value.replace(/[^0-9]/g, '');
            if (val !== '') {
                let num = parseInt(val, 10);
                if (num > maxVal) {
                    val = String(maxVal);
                }
            }
            input.value = val;

            if (val.length === 2 && nextFieldId) {
                const nextEl = document.getElementById(nextFieldId);
                if (nextEl) {
                    nextEl.focus();
                    nextEl.select();
                }
            }
            syncCreateWaktu();
        }

        function formatTimeBlur(input, defaultVal) {
            let val = input.value.replace(/[^0-9]/g, '');
            if (val === '') {
                val = defaultVal;
            } else {
                val = val.padStart(2, '0');
            }
            input.value = val;
            syncCreateWaktu();
        }

        function setCreateTimeToNow() {
            const now = new Date();
            const jam = String(now.getHours()).padStart(2, '0');
            const menit = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('create-waktu-jam').value = jam;
            document.getElementById('create-waktu-menit').value = menit;
            syncCreateWaktu();
        }

        function validateEditTimeInput(input, maxVal, nextFieldId) {
            let val = input.value.replace(/[^0-9]/g, '');
            if (val !== '') {
                let num = parseInt(val, 10);
                if (num > maxVal) {
                    val = String(maxVal);
                }
            }
            input.value = val;

            if (val.length === 2 && nextFieldId) {
                const nextEl = document.getElementById(nextFieldId);
                if (nextEl) {
                    nextEl.focus();
                    nextEl.select();
                }
            }
            syncEditWaktu();
        }

        function formatEditTimeBlur(input, defaultVal) {
            let val = input.value.replace(/[^0-9]/g, '');
            if (val === '') {
                val = defaultVal;
            } else {
                val = val.padStart(2, '0');
            }
            input.value = val;
            syncEditWaktu();
        }

        function setEditTimeToNow() {
            const now = new Date();
            const jam = String(now.getHours()).padStart(2, '0');
            const menit = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('edit-waktu-jam').value = jam;
            document.getElementById('edit-waktu-menit').value = menit;
            syncEditWaktu();
        }

        function syncCreateWaktu() {
            const jam = (document.getElementById('create-waktu-jam').value || '00').padStart(2, '0');
            const menit = (document.getElementById('create-waktu-menit').value || '00').padStart(2, '0');
            document.getElementById('create-waktu-hidden').value = `${jam}:${menit}`;
        }

        function syncEditWaktu() {
            const jam = (document.getElementById('edit-waktu-jam').value || '00').padStart(2, '0');
            const menit = (document.getElementById('edit-waktu-menit').value || '00').padStart(2, '0');
            document.getElementById('edit-waktu').value = `${jam}:${menit}`;
        }

        function openEditModal(id, pemagangId, pemagangName, tanggal, shift, waktu, keterangan, notes, kantor) {
            document.getElementById('edit-pemagang-id').value = pemagangId;
            document.getElementById('edit-pemagang-name').textContent = pemagangName;
            document.getElementById('edit-tanggal').value = tanggal;

            const displayEl = document.getElementById('edit-tanggal-display');
            if (displayEl) {
                displayEl.textContent = tanggal;
            }

            document.getElementById('edit-shift').value = shift;
            const editKantorEl = document.getElementById('edit-kantor');
            if (editKantorEl) {
                editKantorEl.value = kantor || 'Kantor 1';
            }

            // Sync waktu 24 jam ke input jam & menit
            if (waktu) {
                const parts = waktu.split(':');
                const jam = parts[0] ? parts[0].padStart(2, '0') : '08';
                const menit = parts[1] ? parts[1].padStart(2, '0') : '00';
                const jamEl = document.getElementById('edit-waktu-jam');
                const menitEl = document.getElementById('edit-waktu-menit');
                if (jamEl) jamEl.value = jam;
                if (menitEl) menitEl.value = menit;
                document.getElementById('edit-waktu').value = `${jam}:${menit}`;
            }

            document.getElementById('edit-keterangan').value = keterangan;
            document.getElementById('edit-notes').value = notes;
            document.getElementById('form-edit').action = `/admin/presensi/${id}`;
            document.getElementById('modal-edit').classList.remove('hidden');
        }

        function openDeleteModal(id, pemagangName, shift, waktu) {
            document.getElementById('delete-pemagang-name').textContent = pemagangName;
            document.getElementById('delete-info').textContent = `${shift} - ${waktu}`;
            document.getElementById('form-delete').action = `/admin/presensi/${id}`;
            document.getElementById('modal-delete').classList.remove('hidden');
        }

        // ── Stat Card Filter Helpers ──────────────────────────────────
        function filterKet(keterangan) {
            const url = new URL(window.location.href);
            if (url.searchParams.get('keterangan') === keterangan) {
                url.searchParams.delete('keterangan'); // toggle off
            } else {
                url.searchParams.set('keterangan', keterangan);
            }
            url.searchParams.set('tab', 'hadir');
            url.searchParams.delete('page_hadir'); // reset halaman
            window.location.href = url.toString();
        }

        function scrollToTidakHadir() {
            const url = new URL(window.location.href);
            url.searchParams.set('tab', 'tidak_hadir');
            url.searchParams.delete('keterangan');
            url.searchParams.delete('page_tidak_hadir');
            window.location.href = url.toString();
        }

        // Buka modal catat presensi langsung untuk pemagang tertentu (tab Belum)
        function openCreateModalFor(id, name, kampus, divisi) {
            openCreateModal();
            selectPemagang(id, name, kampus || '-', divisi || '-');
        }
    </script>
@endpush
