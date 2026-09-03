@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan aktivitas kamu hari ini')
@section('sidebar')
    @include('components.sidebar-pm')
@endsection
@section('content')
    @include('components.notification-popup')
    <x-responsive-grid :cols="'grid-cols-2 sm:grid-cols-4'" :gap="'gap-3 sm:gap-4 md:gap-5'" class="mb-4 md:mb-5">
        <x-responsive-card :padding="'p-3 sm:p-4 md:p-5'">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs sm:text-sm font-medium text-gray-500">Total Tugas</p>
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-primary-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 sm:w-5 h-4 sm:h-5 text-primary-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-400 mt-1">tugas hari ini</p>
        </x-responsive-card>
        <x-responsive-card :padding="'p-3 sm:p-4 md:p-5'">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs sm:text-sm font-medium text-gray-500">Selesai</p>
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 sm:w-5 h-4 sm:h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-green-600">{{ $stats['completed'] }}</p>
            <p class="text-xs text-gray-400 mt-1">sudah diselesaikan</p>
        </x-responsive-card>
        <x-responsive-card :padding="'p-3 sm:p-4 md:p-5'">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs sm:text-sm font-medium text-gray-500">Belum Selesai</p>
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-yellow-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 sm:w-5 h-4 sm:h-5 text-yellow-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
            <p class="text-xs text-gray-400 mt-1">masih dalam pengerjaan</p>
        </x-responsive-card>
        <x-responsive-card :padding="'p-3 sm:p-4 md:p-5'">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs sm:text-sm font-medium text-gray-500">Tidak Dikerjakan</p>
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 sm:w-5 h-4 sm:h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-red-600">{{ $stats['not_done'] }}</p>
            <p class="text-xs text-gray-400 mt-1">melewati batas waktu</p>
        </x-responsive-card>
    </x-responsive-grid>

    <x-responsive-card>
        <div class="px-3 sm:px-6 py-4 pt-1 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-xs sm:text-sm font-semibold text-gray-700">Tugas Hari Ini</h2>
            <a href="{{ route('pm.tasks.all') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium">Lihat
                Semua →</a>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($tasks->take(5) as $task)
                @php
                    $assignment = $task->assignments->first();
                    $isDone = $assignment?->is_completed === 'completed';
                    $isNotDone = $assignment?->is_completed === 'not_done';
                @endphp
                <div class="px-3 sm:px-6 py-2.5 sm:py-3.5 flex items-center gap-3">
                    <div
                        class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0
                                {{ $isDone ? 'border-green-500 bg-green-500' : ($isNotDone ? 'border-red-400 bg-red-400' : 'border-gray-300') }}">
                        @if($isDone)
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        @elseif($isNotDone)
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p
                            class="text-xs sm:text-sm font-medium truncate {{ $isDone ? 'line-through text-gray-400' : ($isNotDone ? 'text-red-400' : 'text-gray-800') }}">
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
                        <span
                            class="inline-flex px-1.5 sm:px-2 py-0.5 rounded-full text-xs bg-green-50 text-green-700 flex-shrink-0">Selesai</span>
                    @elseif($isNotDone)
                        <span
                            class="inline-flex px-1.5 sm:px-2 py-0.5 rounded-full text-xs bg-red-50 text-red-700 flex-shrink-0">Tidak
                            Dikerjakan</span>
                    @else
                        <span
                            class="inline-flex px-1.5 sm:px-2 py-0.5 rounded-full text-xs bg-yellow-50 text-yellow-700 flex-shrink-0">Pending</span>
                    @endif
                </div>
            @empty
                <div class="px-3 sm:px-6 py-8 text-center text-gray-400 text-xs sm:text-sm">Tidak ada tugas hari ini.</div>
            @endforelse
        </div>
    </x-responsive-card>
@endsection