@extends('layouts.app')

@section('title', 'Progres HR Assistant')
@section('page-title', 'Progres HR Assistant')
@section('page-subtitle', 'Pantau aktivitas dan progres seluruh HR Assistant')

@section('sidebar')
    @include('components.sidebar-staff')
@endsection

@section('content')

{{-- Filter Rentang Tanggal --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5 mb-6 shadow-sm">
    <form method="GET" action="{{ route('staff.assistant-progress') }}"
          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">

        {{-- Dari Tanggal --}}
        <div class="lg:col-span-4">
            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">
                Dari Tanggal
            </label>
            <input type="date" name="date_from" value="{{ $dateFrom }}" max="{{ $today }}"
                   class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs sm:text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition">
        </div>

        {{-- Sampai Tanggal --}}
        <div class="lg:col-span-4">
            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">
                Sampai Tanggal
            </label>
            <input type="date" name="date_to" value="{{ $dateTo }}" max="{{ $today }}"
                   class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs sm:text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition">
        </div>

        {{-- Tombol Aksi --}}
        <div class="sm:col-span-2 lg:col-span-4 flex items-center gap-2">
            <button type="submit"
                    class="flex-1 flex items-center justify-center gap-1.5 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs sm:text-sm font-semibold rounded-lg transition shadow-sm hover:shadow">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <span>Tampilkan</span>
            </button>

            @if($dateFrom !== $today || $dateTo !== $today)
                <a href="{{ route('staff.assistant-progress') }}"
                   class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs sm:text-sm font-semibold rounded-lg transition text-center flex-shrink-0"
                   title="Kembali ke Hari Ini">
                    Hari Ini
                </a>
            @endif
        </div>
    </form>

    {{-- Info periode aktif --}}
    <div class="mt-3 pt-3 border-t border-gray-100 flex items-center gap-2 text-xs text-gray-500">
        <svg class="w-4 h-4 text-primary-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <span>Menampilkan periode:
            <strong class="font-semibold text-gray-800">
                {{ \Carbon\Carbon::parse($dateFrom)->locale('id')->translatedFormat('d M Y') }}
                @if($dateFrom !== $dateTo)
                    — {{ \Carbon\Carbon::parse($dateTo)->locale('id')->translatedFormat('d M Y') }}
                @endif
            </strong>
        </span>
    </div>
</div>

{{-- Stat Cards Ringkasan --}}
@php
    $totalAssistants = $assistants->count();
    $allDone         = $assistants->filter(fn($a) => $a->total_tasks > 0 && $a->completed_tasks === $a->total_tasks)->count();
    $totalTasks      = $assistants->sum('total_tasks');
    $totalCompleted  = $assistants->sum('completed_tasks');
@endphp

<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-medium text-gray-500">Total Assistant</p>
            <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-gray-800">{{ $totalAssistants }}</p>
        <p class="text-xs text-gray-400 mt-1">HR Assistant aktif</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-medium text-gray-500">Semua Selesai</p>
            <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-green-600">{{ $allDone }}</p>
        <p class="text-xs text-gray-400 mt-1">dari {{ $totalAssistants }} assistant</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-medium text-gray-500">Total Tugas</p>
            <div class="w-9 h-9 rounded-lg bg-primary-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-gray-800">{{ $totalTasks }}</p>
        <p class="text-xs text-gray-400 mt-1">total assignment</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-medium text-gray-500">Sudah Diselesaikan</p>
            <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-green-600">{{ $totalCompleted }}</p>
        <p class="text-xs text-gray-400 mt-1">
            {{ $totalTasks > 0 ? round(($totalCompleted / $totalTasks) * 100) : 0 }}% dari total
        </p>
    </div>
</div>

{{-- Tabel Detail per Assistant --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-700">Detail Progres per Assistant</h2>
        <span class="text-xs text-gray-400">{{ $assistants->count() }} assistant</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[500px]">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Nama</th>
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-24">Selesai</th>
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-24">Total</th>
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-24">Tdk Dikerjakan</th>
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Produktivitas</th>
                    <th class="text-right px-6 py-3.5 font-semibold text-gray-600 w-20">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($assistants as $assistant)
                    @php
                        $pct = $assistant->total_tasks > 0
                            ? round(($assistant->completed_tasks / $assistant->total_tasks) * 100)
                            : 0;
                        $barColor = $pct === 100 ? 'bg-green-500'
                            : ($pct >= 50 ? 'bg-primary-500' : 'bg-yellow-500');
                        $pctColor = $pct === 100 ? 'text-green-600'
                            : ($pct >= 50 ? 'text-primary-600' : 'text-yellow-600');
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-semibold text-purple-600">
                                        {{ strtoupper(substr($assistant->name, 0, 1)) }}
                                    </span>
                                </div>
                                <span class="font-medium text-gray-800">{{ $assistant->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 w-24">
                            <span class="font-semibold text-green-600">{{ $assistant->completed_tasks }}</span>
                        </td>
                        <td class="px-6 py-4 w-24">
                            <span class="text-gray-600">{{ $assistant->total_tasks }}</span>
                        </td>
                        <td class="px-6 py-4 w-24">
                            <span class="font-semibold text-red-500">{{ $assistant->not_done_tasks }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden min-w-[60px]">
                                    <div class="{{ $barColor }} h-2 rounded-full transition-all duration-300"
                                         style="width: {{ $pct }}%"></div>
                                </div>
                                <span class="text-xs font-bold {{ $pctColor }} w-8 text-right">{{ $pct }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 w-20 text-right">
                            <a href="{{ route('staff.assistant-progress.detail', $assistant->id) }}?date_from={{ $dateFrom }}&date_to={{ $dateTo }}"
                               class="p-1.5 inline-flex text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                               title="Lihat Detail Tugas">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">
                            Tidak ada HR Assistant aktif.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
