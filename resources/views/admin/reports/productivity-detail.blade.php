@extends('layouts.app')

@section('title', 'Detail Produktivitas — ' . $user->name)
@section('page-title', 'Detail Produktivitas')
@section('page-subtitle', 'Rekap tugas ' . $user->name . ' selama periode yang dipilih')

@section('sidebar')
    @include('components.sidebar-admin')
@endsection

@section('content')

@php
    $roleLabel = $user->role_label;
    $roleBadge = $user->role_badge_class;
@endphp

{{-- Back button --}}
<div class="mb-6">
    <a href="{{ route('admin.reports.productivity') }}?date_from={{ $dateFrom }}&date_to={{ $dateTo }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 mb-4 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Laporan Produktivitas
    </a>

    {{-- Profile card --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center flex-shrink-0">
            @if($user->image)
                <img src="{{ asset('storage/' . $user->image) }}"
                     alt="{{ $user->name }}"
                     class="w-full h-full rounded-full object-cover">
            @else
                <span class="text-lg font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
            @endif
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <h2 class="text-base font-bold text-gray-800">{{ $user->name }}</h2>
                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $roleBadge }}">
                    {{ $roleLabel }}
                </span>
            </div>
            <p class="text-xs text-gray-400 mt-0.5">
                Periode:
                <span class="font-medium text-gray-600">
                    {{ \Carbon\Carbon::parse($dateFrom)->locale('id')->translatedFormat('d M Y') }}
                    @if($dateFrom !== $dateTo)
                        — {{ \Carbon\Carbon::parse($dateTo)->locale('id')->translatedFormat('d M Y') }}
                    @endif
                </span>
            </p>
        </div>
    </div>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 px-4 py-3 flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-primary-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-400">Total Tugas</p>
            <p class="text-xl font-bold text-gray-800">{{ $total }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 px-4 py-3 flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-400">Selesai</p>
            <p class="text-xl font-bold text-green-600">{{ $completed }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 px-4 py-3 flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-yellow-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-400">Pending</p>
            <p class="text-xl font-bold text-yellow-600">{{ $pending }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 px-4 py-3 flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-400">Tdk Dikerjakan</p>
            <p class="text-xl font-bold text-red-600">{{ $notDone }}</p>
        </div>
    </div>
</div>

{{-- Progress bar keseluruhan --}}
<div class="bg-white rounded-xl border border-gray-200 px-5 py-4 mb-6">
    <div class="flex items-center justify-between mb-2">
        <p class="text-sm font-medium text-gray-700">Produktivitas Periode Ini</p>
        <span class="text-sm font-bold
            {{ $pct === 100 ? 'text-green-600' : ($pct >= 50 ? 'text-primary-600' : 'text-yellow-600') }}">
            {{ $pct }}%
        </span>
    </div>
    <div class="h-2.5 bg-gray-100 rounded-full overflow-hidden">
        <div class="h-full rounded-full transition-all duration-500
            {{ $pct === 100 ? 'bg-green-500' : ($pct >= 50 ? 'bg-primary-500' : 'bg-yellow-500') }}"
             style="width: {{ $pct }}%"></div>
    </div>
    <p class="text-xs text-gray-400 mt-1.5">
        {{ $completed }} dari {{ $total }} tugas diselesaikan
        @if($total > 0 && $notDone > 0)
            · <span class="text-red-400">{{ $notDone }} tidak dikerjakan</span>
        @endif
    </p>
</div>

{{-- Daftar Tugas dikelompokkan per Hari --}}
@if($tasksByDate->isEmpty())
    <div class="bg-white rounded-xl border border-gray-200 px-6 py-14 text-center">
        <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <p class="text-sm font-medium text-gray-500">Tidak ada tugas pada periode ini.</p>
    </div>
@else
    <div class="space-y-4">
        @foreach($tasksByDate as $date => $dayTasks)
            @php
                $dayTotal     = $dayTasks->count();
                $dayCompleted = $dayTasks->filter(fn($t) => ($t->assignments->first()?->is_completed ?? 'pending') === 'completed')->count();
                $dayNotDone   = $dayTasks->filter(fn($t) => ($t->assignments->first()?->is_completed ?? 'pending') === 'not_done')->count();
                $dayPct       = $dayTotal > 0 ? round(($dayCompleted / $dayTotal) * 100) : 0;
            @endphp

            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                {{-- Header tanggal --}}
                <div class="px-4 sm:px-6 py-3 border-b border-gray-100 flex items-center justify-between bg-gray-50">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-sm font-semibold text-gray-700">
                            {{ \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('l, d M Y') }}
                        </span>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-gray-500">
                        <span class="text-green-600 font-medium">{{ $dayCompleted }} selesai</span>
                        <span>·</span>
                        <span>{{ $dayTotal }} tugas</span>
                        <span class="font-bold
                            {{ $dayPct === 100 ? 'text-green-600' : ($dayPct >= 50 ? 'text-primary-600' : 'text-yellow-600') }}">
                            {{ $dayPct }}%
                        </span>
                    </div>
                </div>

                {{-- Tabel tugas per hari --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm min-w-[540px]">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="text-left px-4 sm:px-6 py-3 font-semibold text-gray-500 text-xs w-44">Judul</th>
                                <th class="text-left px-4 sm:px-6 py-3 font-semibold text-gray-500 text-xs">Deskripsi</th>
                                <th class="text-left px-4 sm:px-6 py-3 font-semibold text-gray-500 text-xs w-28">Sumber</th>
                                <th class="text-left px-4 sm:px-6 py-3 font-semibold text-gray-500 text-xs w-36">Status</th>
                                <th class="text-left px-4 sm:px-6 py-3 font-semibold text-gray-500 text-xs w-28">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($dayTasks as $task)
                                @php
                                    $assignment = $task->assignments->first();
                                    $status     = $assignment?->is_completed ?? 'pending';
                                @endphp
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 sm:px-6 py-3.5 w-44">
                                        <div class="font-medium text-gray-800 truncate max-w-[160px]"
                                             title="{{ $task->title }}">
                                            {{ $task->title }}
                                        </div>
                                    </td>
                                    <td class="px-4 sm:px-6 py-3.5">
                                        <div class="text-gray-500 truncate max-w-[180px]"
                                             title="{{ $task->description ?? '-' }}">
                                            @if($task->description)
                                                {!! linkify(e($task->description)) !!}
                                            @else
                                                <span class="text-gray-300">—</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 sm:px-6 py-3.5 w-28">
                                        @if($task->type === 'default')
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-purple-50 text-purple-700">Rutin</span>
                                        @elseif($task->type === 'self')
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Mandiri</span>
                                        @else
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700"
                                                  title="{{ $task->creator?->name ?? 'Atasan' }}">
                                                {{ Str::limit($task->creator?->name ?? 'Atasan', 10) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 sm:px-6 py-3.5 w-36">
                                        <x-task-status-badge :status="$status" :completedAt="$assignment?->completed_at" />
                                    </td>
                                    <td class="px-4 sm:px-6 py-3.5 w-28">
                                        @if($assignment?->note)
                                            <div class="text-xs text-gray-500 italic truncate max-w-[100px]"
                                                 title="{{ $assignment->note }}">
                                                {{ $assignment->note }}
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-300">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
@endif

@endsection
