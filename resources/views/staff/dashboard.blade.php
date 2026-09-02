@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan aktivitas kamu hari ini')

@section('sidebar')
    @include('components.sidebar-staff')
@endsection

@section('content')
    @include('components.notification-popup')

    <div class="space-y-6">
        {{-- ── Stat Cards ──────────────────────────────────────── --}}
        <x-responsive-grid :cols="'grid-cols-2 lg:grid-cols-4'" :gap="'gap-2.5 sm:gap-4 md:gap-5'">

            {{-- Total --}}
            <x-responsive-card :padding="'p-3 sm:p-5'">
                <div class="flex items-center justify-between mb-2 sm:mb-3">
                    <p class="text-[11px] sm:text-sm font-medium text-gray-500 truncate">Total Tugas</p>
                    <div class="w-7 h-7 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-primary-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
                <p class="text-xl sm:text-3xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                <p class="text-[10px] sm:text-xs text-gray-400 mt-0.5 sm:mt-1 truncate">tugas hari ini</p>
            </x-responsive-card>

            {{-- Selesai --}}
            <x-responsive-card :padding="'p-3 sm:p-5'">
                <div class="flex items-center justify-between mb-2 sm:mb-3">
                    <p class="text-[11px] sm:text-sm font-medium text-gray-500 truncate">Selesai</p>
                    <div class="w-7 h-7 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>
                <p class="text-xl sm:text-3xl font-bold text-green-600">{{ $stats['completed'] }}</p>
                <p class="text-[10px] sm:text-xs text-gray-400 mt-0.5 sm:mt-1 truncate">sudah diselesaikan</p>
            </x-responsive-card>

            {{-- Belum Selesai --}}
            <x-responsive-card :padding="'p-3 sm:p-5'">
                <div class="flex items-center justify-between mb-2 sm:mb-3">
                    <p class="text-[11px] sm:text-sm font-medium text-gray-500 truncate">Belum Selesai</p>
                    <div class="w-7 h-7 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-yellow-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-xl sm:text-3xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
                <p class="text-[10px] sm:text-xs text-gray-400 mt-0.5 sm:mt-1 truncate">dalam pengerjaan</p>
            </x-responsive-card>

            {{-- Tidak Dikerjakan --}}
            <x-responsive-card :padding="'p-3 sm:p-5'">
                <div class="flex items-center justify-between mb-2 sm:mb-3">
                    <p class="text-[11px] sm:text-sm font-medium text-gray-500 truncate">Tidak Dikerjakan</p>
                    <div class="w-7 h-7 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                </div>
                <p class="text-xl sm:text-3xl font-bold text-red-600">{{ $stats['not_done'] }}</p>
                <p class="text-[10px] sm:text-xs text-gray-400 mt-0.5 sm:mt-1 truncate">lewat batas waktu</p>
            </x-responsive-card>

        </x-responsive-grid>

        {{-- ── Main Cards Grid ────────────────────────────── --}}
        <x-responsive-grid :cols="'grid-cols-1 lg:grid-cols-2'" :gap="'gap-4 md:gap-6'">

            {{-- ── Tugas Hari Ini ──────────────────────────────── --}}
            <x-responsive-card class="flex flex-col h-full">
                <div class="px-5 py-4 pt-1 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-800">Tugas Hari Ini</h2>
                    <a href="{{ route('staff.tasks.all') }}"
                       class="text-xs text-primary-600 hover:text-primary-700 font-medium transition-colors">
                        Lihat Semua →
                    </a>
                </div>
                <div class="divide-y divide-gray-100 flex-1">
                    @forelse($tasks->take(5) as $task)
                        @php
                            $assignment = $task->assignments->first();
                            $isDone     = $assignment?->is_completed === 'completed';
                            $isNotDone  = $assignment?->is_completed === 'not_done';
                        @endphp
                        <div class="px-5 py-3.5 flex items-center gap-3">
                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0
                                        {{ $isDone ? 'border-green-500 bg-green-500' : ($isNotDone ? 'border-red-400 bg-red-400' : 'border-gray-300') }}">
                                @if($isDone)
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                @elseif($isNotDone)
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs sm:text-sm font-medium truncate
                                          {{ $isDone ? 'line-through text-gray-400' : ($isNotDone ? 'text-red-400' : 'text-gray-800') }}">
                                    {{ $task->title }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    @if($task->type === 'self') Mandiri
                                    @elseif($task->type === 'default') Rutin
                                    @else {{ $task->creator?->name ?? 'Admin' }}
                                    @endif
                                </p>
                            </div>
                            @if($isDone)
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 flex-shrink-0">Selesai</span>
                            @elseif($isNotDone)
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 flex-shrink-0">Tidak Dikerjakan</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-50 text-yellow-700 flex-shrink-0">Pending</span>
                            @endif
                        </div>
                    @empty
                        <div class="px-5 py-12 text-center text-gray-400 text-xs sm:text-sm flex flex-col items-center justify-center">
                            <p>Tidak ada tugas hari ini.</p>
                        </div>
                    @endforelse
                </div>
            </x-responsive-card>

            {{-- ── Progres HR Assistant ────────────────────────── --}}
            <x-responsive-card class="flex flex-col h-full">
                <div class="px-5 py-4 pt-1 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-800">Progres HR Assistant</h2>
                </div>
                <div class="divide-y divide-gray-100 flex-1">
                    @forelse($assistants as $assistant)
                        @php
                            $pct = $assistant->total_tasks > 0
                                ? round(($assistant->completed_tasks / $assistant->total_tasks) * 100)
                                : 0;
                            $barColor = $pct === 100
                                ? 'bg-green-500'
                                : ($pct >= 50 ? 'bg-primary-500' : 'bg-yellow-500');
                        @endphp
                        <div class="px-5 py-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0">
                                        <span class="text-xs font-bold text-purple-600">
                                            {{ strtoupper(substr($assistant->name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <span class="text-xs sm:text-sm font-medium text-gray-800">{{ $assistant->name }}</span>
                                </div>
                                <span class="text-xs font-medium text-gray-500 flex-shrink-0">
                                    {{ $assistant->completed_tasks }}/{{ $assistant->total_tasks }} tugas
                                </span>
                            </div>
                            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="{{ $barColor }} h-2 rounded-full transition-all duration-300"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                            <p class="text-xs text-gray-400 mt-1.5 font-medium">{{ $pct }}% selesai</p>
                        </div>
                    @empty
                        <div class="px-5 py-12 text-center text-gray-400 text-xs sm:text-sm flex flex-col items-center justify-center">
                            <p>Tidak ada HR Assistant.</p>
                        </div>
                    @endforelse
                </div>
            </x-responsive-card>
        </x-responsive-grid>
    </div>
@endsection