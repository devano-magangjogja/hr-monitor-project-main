@extends('layouts.app')

@section('title', 'Laporan Produktivitas')
@section('page-title', 'Laporan Produktivitas')
@section('page-subtitle', 'Rekap penyelesaian tugas per pengguna berdasarkan periode')

@section('sidebar')
    @include('components.sidebar-admin')
@endsection

@section('content')

{{-- Filter Rentang Tanggal --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
    <form method="GET" action="{{ route('admin.reports.productivity') }}"
          class="flex flex-wrap items-end gap-3">

        <div class="flex flex-col gap-1">
            <label class="text-xs font-medium text-gray-500">Dari Tanggal</label>
            <input type="date" name="date_from" value="{{ $dateFrom }}"
                   max="{{ $today }}"
                   class="px-3 py-2 border border-gray-300 rounded-lg text-sm
                          focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>

        <div class="flex flex-col gap-1">
            <label class="text-xs font-medium text-gray-500">Sampai Tanggal</label>
            <input type="date" name="date_to" value="{{ $dateTo }}"
                   max="{{ $today }}"
                   class="px-3 py-2 border border-gray-300 rounded-lg text-sm
                          focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>

        <button type="submit"
                class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white
                       text-sm font-medium rounded-lg transition">
            Tampilkan
        </button>

        @if($dateFrom !== $today || $dateTo !== $today)
            <a href="{{ route('admin.reports.productivity') }}"
               class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800
                      border border-gray-300 rounded-lg transition">
                Hari Ini
            </a>
        @endif
    </form>

    {{-- Info periode aktif --}}
    <p class="text-xs text-gray-400 mt-3">
        Menampilkan produktivitas periode:
        <span class="font-medium text-gray-600">
            {{ \Carbon\Carbon::parse($dateFrom)->locale('id')->translatedFormat('d M Y') }}
            @if($dateFrom !== $dateTo)
                — {{ \Carbon\Carbon::parse($dateTo)->locale('id')->translatedFormat('d M Y') }}
            @endif
        </span>
    </p>
</div>

{{-- Tabel Ringkasan Produktivitas --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-700">Ringkasan per Pengguna</h2>
        <span class="text-xs text-gray-400">{{ $report->count() }} pengguna</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[640px]">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-4 sm:px-6 py-3.5 font-semibold text-gray-600">Nama</th>
                    <th class="text-left px-4 sm:px-6 py-3.5 font-semibold text-gray-600 w-24">Role</th>
                    <th class="text-left px-4 sm:px-6 py-3.5 font-semibold text-gray-600 w-20">Total</th>
                    <th class="text-left px-4 sm:px-6 py-3.5 font-semibold text-gray-600 w-24">Selesai</th>
                    <th class="text-left px-4 sm:px-6 py-3.5 font-semibold text-gray-600 w-24">Pending</th>
                    <th class="text-left px-4 sm:px-6 py-3.5 font-semibold text-gray-600 w-28">Tdk Dikerjakan</th>
                    <th class="text-left px-4 sm:px-6 py-3.5 font-semibold text-gray-600 w-36">Produktivitas</th>
                    <th class="text-right px-4 sm:px-6 py-3.5 font-semibold text-gray-600 w-20">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($report as $item)
                    @php
                        $barColor = $item['pct'] === 100
                            ? 'bg-green-500'
                            : ($item['pct'] >= 50 ? 'bg-primary-500' : 'bg-yellow-500');
                        $pctColor = $item['pct'] === 100
                            ? 'text-green-600'
                            : ($item['pct'] >= 50 ? 'text-primary-600' : 'text-yellow-600');
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 sm:px-6 py-3.5">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-semibold text-primary-600">
                                        {{ strtoupper(substr($item['user']->name, 0, 1)) }}
                                    </span>
                                </div>
                                <span class="font-medium text-gray-800 truncate">{{ $item['user']->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 sm:px-6 py-3.5 w-36">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $item['user']->role_badge_class }}">
                                {{ $item['user']->role_label }}
                            </span>
                        </td>
                        <td class="px-4 sm:px-6 py-3.5 w-20">
                            <span class="text-gray-700 font-medium">{{ $item['total'] }}</span>
                        </td>
                        <td class="px-4 sm:px-6 py-3.5 w-24">
                            <span class="text-green-600 font-semibold">{{ $item['completed'] }}</span>
                        </td>
                        <td class="px-4 sm:px-6 py-3.5 w-24">
                            <span class="text-yellow-600 font-medium">{{ $item['pending'] }}</span>
                        </td>
                        <td class="px-4 sm:px-6 py-3.5 w-28">
                            <span class="text-red-500 font-medium">{{ $item['notDone'] }}</span>
                        </td>
                        <td class="px-4 sm:px-6 py-3.5 w-36">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden min-w-[50px]">
                                    <div class="{{ $barColor }} h-full rounded-full"
                                         style="width: {{ $item['pct'] }}%"></div>
                                </div>
                                <span class="text-xs font-bold {{ $pctColor }} w-8 text-right">
                                    {{ $item['pct'] }}%
                                </span>
                            </div>
                        </td>
                        <td class="px-4 sm:px-6 py-3.5 w-20 text-right">
                            <a href="{{ route('admin.reports.productivity.detail', $item['user']->id) }}?date_from={{ $dateFrom }}&date_to={{ $dateTo }}"
                               class="p-1.5 inline-flex text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                               title="Lihat Detail Tugas">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943
                                             9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-400 text-sm">
                            Tidak ada data pengguna aktif.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
