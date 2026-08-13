@extends('layouts.app')

@section('title', 'Detail Progres — ' . $user->name)
@section('page-title', 'Detail Progres Tim')
@section('page-subtitle', 'Daftar tugas hari ini milik ' . $user->name)

@section('sidebar')
    @include('components.sidebar-admin')
@endsection

@section('content')

@php
    $roleLabel = match($user->role) {
        'hr_staff'      => 'HR Staff',
        'hr_assistant'  => 'HR Assistant',
        default         => $user->role,
    };
    $roleColor = $user->role === 'hr_staff'
        ? ['bg' => 'bg-blue-100',   'text' => 'text-blue-700',   'avatar' => 'bg-blue-100 text-blue-600']
        : ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'avatar' => 'bg-purple-100 text-purple-600'];

    $total     = $tasks->count();
    $completed = 0;
    $pending   = 0;
    $notDone   = 0;
    foreach ($tasks as $task) {
        $s = $task->assignments->first()?->is_completed ?? 'pending';
        if ($s === 'completed')  $completed++;
        elseif ($s === 'not_done') $notDone++;
        else $pending++;
    }
    $pct = $total > 0 ? round(($completed / $total) * 100) : 0;
@endphp

{{-- Back button --}}
<div class="mb-6">
    <a href="{{ route('admin.dashboard') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 mb-4 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Dashboard
    </a>

    {{-- Profile card --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full {{ $roleColor['avatar'] }} flex items-center justify-center flex-shrink-0">
            @if($user->image)
                <img src="{{ asset('storage/' . $user->image) }}"
                     alt="{{ $user->name }}"
                     class="w-full h-full rounded-full object-cover">
            @else
                <span class="text-lg font-bold">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </span>
            @endif
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <h2 class="text-base font-bold text-gray-800">{{ $user->name }}</h2>
                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium
                             {{ $roleColor['bg'] }} {{ $roleColor['text'] }}">
                    {{ $roleLabel }}
                </span>
            </div>
            <p class="text-xs text-gray-400 mt-0.5">{{ $user->email }}</p>
        </div>
        <div class="hidden sm:flex items-center gap-6 flex-shrink-0">
            <div class="text-center">
                <p class="text-xs text-gray-400">Skor Minggu</p>
                <p class="text-base font-bold text-yellow-600">{{ $scoreWeek }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-400">Skor Bulan</p>
                <p class="text-base font-bold text-purple-600">{{ $scoreMonth }}</p>
            </div>
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
            <p class="text-xs text-gray-400">Total</p>
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
            <p class="text-xs text-gray-400">Tidak Dikerjakan</p>
            <p class="text-xl font-bold text-red-600">{{ $notDone }}</p>
        </div>
    </div>
</div>

{{-- Progress bar keseluruhan --}}
<div class="bg-white rounded-xl border border-gray-200 px-5 py-4 mb-6">
    <div class="flex items-center justify-between mb-2">
        <p class="text-sm font-medium text-gray-700">Progress Hari Ini</p>
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
    <p class="text-xs text-gray-400 mt-1.5">{{ $completed }} dari {{ $total }} tugas diselesaikan</p>
</div>

{{-- Daftar Tugas --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="px-4 sm:px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-800">Daftar Tugas Hari Ini</h3>
        <span class="text-xs text-gray-400">
            {{ \Carbon\Carbon::today()->locale('id')->translatedFormat('d M Y') }}
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[560px]">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-4 sm:px-6 py-3.5 font-semibold text-gray-600 w-44">Judul</th>
                    <th class="text-left px-4 sm:px-6 py-3.5 font-semibold text-gray-600">Deskripsi</th>
                    <th class="text-left px-4 sm:px-6 py-3.5 font-semibold text-gray-600 w-28">Sumber</th>
                    <th class="text-left px-4 sm:px-6 py-3.5 font-semibold text-gray-600 w-36">Status</th>
                    <th class="text-left px-4 sm:px-6 py-3.5 font-semibold text-gray-600 w-28">Catatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($tasks as $task)
                    @php
                        $assignment = $task->assignments->first();
                        $status     = $assignment?->is_completed ?? 'pending';
                    @endphp
                    <tr class="hover:bg-gray-50 transition">

                        {{-- Judul --}}
                        <td class="px-4 sm:px-6 py-4 w-44">
                            <div class="truncate max-w-[160px] font-medium text-gray-800"
                                 title="{{ $task->title }}">
                                {{ $task->title }}
                            </div>
                        </td>

                        {{-- Deskripsi --}}
                        <td class="px-4 sm:px-6 py-4">
                            <div class="truncate max-w-[180px] text-gray-500"
                                 title="{{ $task->description ?? '-' }}">
                                @if($task->description)
                                    {!! linkify(e($task->description)) !!}
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </div>
                        </td>

                        {{-- Sumber --}}
                        <td class="px-4 sm:px-6 py-4 w-28">
                            @if($task->type === 'default')
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700">
                                    Rutin
                                </span>
                            @elseif($task->type === 'self')
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    Mandiri
                                </span>
                            @else
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700"
                                      title="{{ $task->creator?->name ?? 'Atasan' }}">
                                    {{ Str::limit($task->creator?->name ?? 'Atasan', 10) }}
                                </span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="px-4 sm:px-6 py-4 w-36">
                            <x-task-status-badge :status="$status" :completedAt="$assignment?->completed_at" />
                        </td>

                        {{-- Catatan --}}
                        <td class="px-4 sm:px-6 py-4 w-28">
                            @if($assignment?->note)
                                <div class="truncate max-w-[100px] text-xs text-gray-500 italic"
                                     title="{{ $assignment->note }}">
                                    {{ $assignment->note }}
                                </div>
                            @else
                                <span class="text-xs text-gray-300">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-14 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="text-sm font-medium text-gray-500">Tidak ada tugas hari ini.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
