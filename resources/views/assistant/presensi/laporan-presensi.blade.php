@extends('layouts.app')

@section('title', 'Laporan Presensi Pemagang')
@section('page-title', 'Laporan Presensi Pemagang')
@section('page-subtitle', 'Rekapitulasi dan analisis kedisiplinan kehadiran anak magang')

@section('sidebar')
    @include('components.sidebar-assistant')
@endsection

@section('content')

{{-- ── Print Header / Kop Surat Resmi (Hanya muncul saat cetak) ── --}}
<div class="hidden print:block mb-6 border-b-2 border-gray-800 pb-4">
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            @php
                $useStorageLogo = !empty($appLogo) && \Illuminate\Support\Facades\Storage::disk('public')->exists($appLogo);
            @endphp
            @if($useStorageLogo)
                <img src="{{ asset('storage/' . $appLogo) }}" alt="{{ $appName ?? 'Seven Inc' }}" class="w-12 h-12 object-contain">
            @else
                <img src="{{ asset('images/seveninc_logo.png') }}" alt="{{ $appName ?? 'Seven Inc' }}" class="w-12 h-12 object-contain">
            @endif
            <div>
                <h1 class="text-base font-bold text-gray-900 uppercase tracking-wide">{{ $appName ?? 'Seven Inc' }}</h1>
                <p class="text-xs font-semibold text-gray-700">HUMAN RESOURCES DEPARTMENT &bull; MONITORING PRESENSI MAGANG</p>
                <p class="text-[11px] text-gray-500">Laporan Rekapitulasi Presensi Pemagang Harian</p>
            </div>
        </div>
        <div class="text-right text-[11px] text-gray-600 leading-tight">
            <p><span class="font-semibold text-gray-800">Tanggal Cetak:</span> {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y, H:i') }} WIB</p>
            <p><span class="font-semibold text-gray-800">Petugas Asisten:</span> {{ Auth::user()->name }}</p>
            <p><span class="font-semibold text-gray-800">Lokasi Penugasan:</span> {{ $assignedKantor ?: ($selectedKantor ?? '-') }}</p>
            <p><span class="font-semibold text-gray-800">Tanggal Presensi:</span> {{ $formattedDate }}</p>
        </div>
    </div>
</div>

{{-- ── Action Header & Filter (Sembunyi saat Print) ─────────── --}}
<div class="print:hidden flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <p class="text-sm text-gray-500">
            Menampilkan data rekapitulasi untuk <span
                class="font-semibold text-gray-700">{{ $stats['total_pemagang'] }}</span> anak magang.
            <span class="ml-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                📍 {{ $selectedKantor }} &bull; {{ $formattedDate }}
            </span>
        </p>
    </div>
    <div class="flex items-center gap-2.5 flex-wrap">
        <a href="{{ route('assistant.presensi.index') }}"
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
<div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 print:grid-cols-4 gap-4 print:gap-3 mb-6 print:mb-5 print-avoid-break">

    {{-- Disiplin Rate --}}
    <div class="bg-white rounded-xl border border-gray-200 print:border-gray-300 p-4 sm:p-5 print:p-3 shadow-sm print:shadow-none">
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
    <div class="bg-white rounded-xl border border-gray-200 print:border-gray-300 p-4 sm:p-5 print:p-3 shadow-sm print:shadow-none">
        <div class="flex items-center justify-between mb-2 print:mb-1">
            <p class="text-xs sm:text-sm print:text-xs font-medium text-gray-500">Hadir Tepat / Awal</p>
            <div class="w-8 h-8 print:hidden rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 13l4 4L19 7" />
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
    <div class="bg-white rounded-xl border border-gray-200 print:border-gray-300 p-4 sm:p-5 print:p-3 shadow-sm print:shadow-none">
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
    <div class="bg-white rounded-xl border border-gray-200 print:border-gray-300 p-4 sm:p-5 print:p-3 shadow-sm print:shadow-none">
        <div class="flex items-center justify-between mb-2 print:mb-1">
            <p class="text-xs sm:text-sm print:text-xs font-medium text-gray-500">Tidak Hadir</p>
            <div class="w-8 h-8 print:hidden rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
        </div>
        <p class="text-2xl sm:text-3xl print:text-xl font-bold text-red-600">{{ $stats['tidak_hadir'] }}</p>
        <p class="text-[11px] print:text-[10px] text-gray-400 mt-1">Alpa / izin / sakit</p>
    </div>

</div>

{{-- ── Filter Bar Laporan (Sembunyi saat Print) ─────────────── --}}
<div class="print:hidden bg-white rounded-xl border border-gray-200 p-4 mb-6 shadow-sm">
    <form method="GET" action="{{ route('assistant.presensi.laporan') }}"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        
        {{-- Tanggal Filter --}}
        <div>
            <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()"
                class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition text-gray-700">
        </div>

        {{-- Filter Kantor --}}
        <div>
            <select name="kantor" onchange="this.form.submit()"
                class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition text-gray-700 font-medium">
                @foreach ($kantorList as $k)
                    <option value="{{ $k }}" {{ $selectedKantor == $k ? 'selected' : '' }}>
                        {{ $k }}</option>
                @endforeach
            </select>
        </div>

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
                <a href="{{ route('assistant.presensi.laporan', ['tanggal' => $tanggal]) }}"
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

{{-- ── TABEL 1: Rekapitulasi Per Pemagang ──────────────────── --}}
<div id="tabel-rekap-pemagang"
    class="bg-white rounded-xl border border-gray-200 print:border-gray-300 overflow-hidden shadow-sm print:shadow-none mb-8 print:mb-6 scroll-mt-6 print-avoid-break">
    <div class="px-6 py-4 print:px-4 print:py-2.5 border-b border-gray-200 print:border-gray-300 bg-gray-50/60 print:bg-gray-100 flex items-center justify-between">
        <div>
            <h2 class="text-sm sm:text-base print:text-sm font-bold text-gray-800">I. Rekapitulasi Kehadiran per Pemagang</h2>
            <p class="text-xs print:text-[11px] text-gray-500">Ringkasan performa dan tingkat kedisiplinan kehadiran setiap individu di {{ $assignedKantor }}</p>
        </div>
        <span class="text-xs font-semibold px-2.5 py-1 bg-primary-50 text-primary-700 rounded-full print:bg-transparent print:text-gray-700 print:border print:border-gray-300">
            Total: {{ $rekapPemagang->total() }} Pemagang
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm print:text-xs min-w-[750px] print:min-w-full">
            <thead>
                <tr class="bg-gray-50 print:bg-gray-100 border-b border-gray-200 print:border-gray-300 text-xs print:text-[11px] font-semibold text-gray-600 print:text-gray-800 uppercase tracking-wider">
                    <th class="px-6 py-3.5 print:px-3 print:py-2">Pemagang</th>
                    <th class="px-6 py-3.5 print:px-3 print:py-2">Divisi</th>
                    <th class="px-4 py-3.5 print:px-2 print:py-2 text-center">Awal</th>
                    <th class="px-4 py-3.5 print:px-2 print:py-2 text-center">Tepat</th>
                    <th class="px-4 py-3.5 print:px-2 print:py-2 text-center">Telat</th>
                    <th class="px-4 py-3.5 print:px-2 print:py-2 text-center">Alpa</th>
                    <th class="px-4 py-3.5 print:px-2 print:py-2 text-center">Total</th>
                    <th class="px-6 py-3.5 print:px-3 print:py-2 text-right">Kedisiplinan</th>
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
                    @endphp
                    <tr class="hover:bg-gray-50/80 transition">

                        {{-- Nama & Info --}}
                        <td class="px-6 py-3.5 print:px-3 print:py-2">
                            <p class="font-semibold text-gray-800 text-sm print:text-xs">{{ $p->nama_lengkap }}</p>
                            <p class="text-xs print:text-[10px] text-gray-400">{{ $p->kampus }}</p>
                        </td>

                        {{-- Divisi --}}
                        <td class="px-6 py-3.5 print:px-3 print:py-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs print:text-[10px] font-medium bg-gray-100 text-gray-700 print:border print:border-gray-200">
                                {{ $p->divisi }}
                            </span>
                        </td>

                        {{-- Lebih Awal --}}
                        <td class="px-4 py-3.5 print:px-2 print:py-2 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs print:text-[11px] font-semibold bg-indigo-50 text-indigo-700 print:bg-transparent print:text-indigo-800">
                                {{ $item->datang_awal }}
                            </span>
                        </td>

                        {{-- Tepat Waktu --}}
                        <td class="px-4 py-3.5 print:px-2 print:py-2 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs print:text-[11px] font-semibold bg-green-50 text-green-700 print:bg-transparent print:text-green-800">
                                {{ $item->tepat_waktu }}
                            </span>
                        </td>

                        {{-- Terlambat --}}
                        <td class="px-4 py-3.5 print:px-2 print:py-2 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs print:text-[11px] font-semibold {{ $item->terlambat > 0 ? 'bg-amber-50 text-amber-700 print:text-amber-800' : 'text-gray-400' }}">
                                {{ $item->terlambat }}
                            </span>
                        </td>

                        {{-- Tidak Hadir --}}
                        <td class="px-4 py-3.5 print:px-2 print:py-2 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs print:text-[11px] font-semibold {{ $item->tidak_hadir > 0 ? 'bg-red-50 text-red-700 print:text-red-800' : 'text-gray-400' }}">
                                {{ $item->tidak_hadir }}
                            </span>
                        </td>

                        {{-- Total --}}
                        <td class="px-4 py-3.5 print:px-2 print:py-2 text-center font-bold text-gray-800 print:text-xs">
                            {{ $item->total }}
                        </td>

                        {{-- Rate --}}
                        <td class="px-6 py-3.5 print:px-3 print:py-2 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <span class="font-bold text-sm print:text-xs {{ $rateColor }}">{{ $item->rate }}%</span>
                                <div class="w-14 bg-gray-100 rounded-full h-1.5 overflow-hidden print:hidden">
                                    <div class="{{ $barColor }} h-1.5 rounded-full" style="width: {{ min(100, $item->rate) }}%"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-400 text-xs">
                            Tidak ada data presensi pemagang di kantor ini pada tanggal tersebut.
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

{{-- ── TABEL 2: Detail Riwayat Log Presensi ───────────────── --}}
<div id="tabel-log-presensi"
    class="bg-white rounded-xl border border-gray-200 print:border-gray-300 overflow-hidden shadow-sm print:shadow-none scroll-mt-6 print-avoid-break">
    <div class="px-6 py-4 print:px-4 print:py-2.5 border-b border-gray-200 print:border-gray-300 bg-gray-50/60 print:bg-gray-100 flex items-center justify-between">
        <div>
            <h2 class="text-sm sm:text-base print:text-sm font-bold text-gray-800">II. Riwayat Detail Log Presensi</h2>
            <p class="text-xs print:text-[11px] text-gray-500">Catatan waktu presensi masuk dan status kehadiran pemagang di {{ $assignedKantor }}</p>
        </div>
        <span class="text-xs font-semibold px-2.5 py-1 bg-gray-100 text-gray-700 rounded-full print:bg-transparent print:border print:border-gray-300">
            Total Log: {{ $logs->total() }}
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm print:text-xs min-w-[750px] print:min-w-full">
            <thead>
                <tr class="bg-gray-50 print:bg-gray-100 border-b border-gray-200 print:border-gray-300 text-xs print:text-[11px] font-semibold text-gray-600 print:text-gray-800 uppercase tracking-wider">
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
                            <p class="text-[11px] print:text-[10px] text-gray-400">{{ $pemagang ? $pemagang->kampus : '-' }}</p>
                        </td>
                        <td class="px-6 py-3 print:px-3 print:py-1.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs print:text-[10px] font-medium bg-gray-100 text-gray-700 print:border print:border-gray-200">
                                {{ $pemagang ? $pemagang->divisi : '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-3 print:px-3 print:py-1.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs print:text-[10px] font-medium bg-blue-50 text-blue-700 print:border print:border-blue-200">
                                {{ $log->kantor ?? 'Kantor 1' }}
                            </span>
                        </td>
                        <td class="px-6 py-3 print:px-3 print:py-1.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs print:text-[10px] font-medium {{ $shiftStyle }}">
                                {{ $log->shift }}
                            </span>
                        </td>
                        <td class="px-6 py-3 print:px-3 print:py-1.5 text-xs text-gray-700 font-medium">
                            {{ substr($log->waktu_masuk, 0, 5) }} WIB
                        </td>
                        <td class="px-6 py-3 print:px-3 print:py-1.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs print:text-[10px] font-medium border {{ $badgeStyle }}">
                                {{ $log->keterangan }}
                            </span>
                        </td>
                        <td class="px-6 py-3 print:px-3 print:py-1.5 text-xs text-gray-600 font-medium">
                            {{ $log->creator?->name ?? 'Asisten' }}
                        </td>
                        <td class="px-6 py-3 print:px-3 print:py-1.5 text-xs text-gray-500 max-w-[200px] truncate">
                            {{ $log->notes ?: '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-6 text-center text-gray-400 text-xs">
                            Belum ada riwayat log presensi di kantor ini pada tanggal tersebut.
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

{{-- ── Lembar Tanda Tangan / Pengesahan (Hanya muncul saat cetak) ── --}}
<div class="hidden print:block mt-8 pt-6 border-t border-gray-300 print-avoid-break">
    <div class="flex justify-between items-start text-xs text-gray-700">
        <div class="text-center w-52">
            <p class="text-gray-500 mb-1">Asisten Bertugas Lapangan,</p>
            <div class="h-16"></div>
            <p class="font-bold underline text-gray-900">{{ Auth::user()->name }}</p>
            <p class="text-[10px] text-gray-500">Asisten Penugasan ({{ $assignedKantor }})</p>
        </div>
        <div class="text-center w-60">
            <p class="text-gray-500 mb-1">Yogyakarta, {{ \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('d F Y') }}</p>
            <p class="text-gray-500">Mengetahui, HR Department</p>
            <div class="h-14"></div>
            <p class="font-bold underline text-gray-900">( ............................................ )</p>
            <p class="text-[10px] text-gray-500">Penanggung Jawab / HR Staff</p>
        </div>
    </div>
</div>

@endsection