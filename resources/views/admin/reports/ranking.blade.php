@extends('layouts.app')

@section('title', 'Ranking Produktivitas')
@section('page-title', 'Ranking Produktivitas')
@section('page-subtitle', 'Peringkat anggota tim berdasarkan total tugas selesai')

@section('sidebar')
    @include('components.sidebar-admin')
@endsection

@section('content')

{{-- Filter Period --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5 mb-6 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div class="flex items-center gap-2">
        <span class="text-xs sm:text-sm font-semibold text-gray-700">Pilih Periode:</span>
        <span class="text-xs text-gray-400">({{ $period === 'week' ? 'Minggu Ini' : 'Bulan Ini' }})</span>
    </div>
    <div class="inline-flex p-1 bg-gray-100/90 rounded-xl border border-gray-200 w-full sm:w-auto">
        <a href="{{ route('admin.reports.ranking', ['period' => 'week']) }}"
           class="flex-1 sm:flex-none text-center px-4 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all {{ $period === 'week' ? 'bg-white text-primary-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
            Minggu Ini
        </a>
        <a href="{{ route('admin.reports.ranking', ['period' => 'month']) }}"
           class="flex-1 sm:flex-none text-center px-4 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all {{ $period === 'month' ? 'bg-white text-primary-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
            Bulan Ini
        </a>
    </div>
</div>

{{-- Ranking --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[580px]">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-16">Rank</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Nama</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-28">Role</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-32">Skor</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Progress</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($rankings as $index => $item)
                @php
                    $rank      = $index + 1;
                    $maxScore  = $rankings->first()['score'] ?? 1;
                    $pct       = $maxScore > 0 ? round(($item['score'] / $maxScore) * 100) : 0;
                    $medalColor = match($rank) {
                        1 => 'text-yellow-500',
                        2 => 'text-gray-400',
                        3 => 'text-amber-600',
                        default => 'text-gray-300',
                    };
                @endphp
                <tr class="hover:bg-gray-50 transition {{ $rank <= 3 ? 'bg-gray-50/50' : '' }}">
                    <td class="px-6 py-4 w-16">
                        @if($rank <= 3)
                            <svg class="w-6 h-6 {{ $medalColor }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @else
                            <span class="text-sm font-semibold text-gray-400">#{{ $rank }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center">
                                <span class="text-xs font-semibold text-primary-600">
                                    {{ strtoupper(substr($item['user']->name, 0, 1)) }}
                                </span>
                            </div>
                            <span class="font-medium text-gray-800">{{ $item['user']->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 w-36">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium {{ $item['user']->role_badge_class }}">
                            {{ $item['user']->role_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4 w-32">
                        <span class="text-lg font-bold text-gray-800">{{ $item['score'] }}</span>
                        <span class="text-xs text-gray-400 ml-1">poin</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="{{ $rank === 1 ? 'bg-yellow-500' : 'bg-primary-500' }}
                                            h-2 rounded-full transition-all duration-300"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                            <span class="text-xs text-gray-400 w-8">{{ $pct }}%</span>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">
                        Belum ada data ranking.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection