@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan aktivitas kamu hari ini')

@section('sidebar')
    @include('components.sidebar-assistant')
@endsection

@section('content')
    @include('components.notification-popup')

    {{-- ── Stat Cards ──────────────────────────────────────── --}}
    <x-responsive-grid :cols="'grid-cols-2 sm:grid-cols-3 lg:grid-cols-5'" :gap="'gap-4'" class="mb-8">
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
            <p class="text-3xl font-bold text-gray-800">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-400 mt-1">tugas hari ini</p>
        </div>

        {{-- Selesai --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-gray-500">Selesai</p>
                <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-green-600">{{ $stats['completed'] }}</p>
            <p class="text-xs text-gray-400 mt-1">sudah diselesaikan</p>
        </div>

        {{-- Belum Selesai --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-gray-500">Belum Selesai</p>
                <div class="w-9 h-9 rounded-lg bg-yellow-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
            <p class="text-xs text-gray-400 mt-1">masih dalam pengerjaan</p>
        </div>

        {{-- Tidak Dikerjakan --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-gray-500">Tidak Dikerjakan</p>
                <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-red-600">{{ $stats['not_done'] }}</p>
            <p class="text-xs text-gray-400 mt-1">melewati batas waktu</p>
        </div>

        {{--<div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-gray-500">Skor Minggu Ini</p>
                <div class="w-9 h-9 rounded-lg bg-yellow-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-yellow-600">{{ $scoreWeek }}</p>
            <p class="text-xs text-gray-400 mt-1">poin minggu ini</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-gray-500">Skor Bulan Ini</p>
                <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-purple-600">{{ $scoreMonth }}</p>
            <p class="text-xs text-gray-400 mt-1">poin bulan ini</p>
        </div>--}}
    </x-responsive-grid>

    {{-- ── Tugas Hari Ini ──────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700">Tugas Hari Ini</h2>
            <a href="{{ route('assistant.tasks.all') }}"
               class="text-xs text-primary-600 hover:text-primary-700 font-medium">
                Lihat Semua →
            </a>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($tasks as $task)
                @php
                    $assignment = $task->assignments->first();
                    $isDone     = $assignment?->is_completed === 'completed';
                    $isNotDone  = $assignment?->is_completed === 'not_done';
                @endphp
                <div class="px-6 py-3.5 flex items-center gap-3">
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
                        <p class="text-sm font-medium truncate
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
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-green-50 text-green-700 flex-shrink-0">Selesai</span>
                    @elseif($isNotDone)
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-red-50 text-red-700 flex-shrink-0">Tidak Dikerjakan</span>
                    @else
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-yellow-50 text-yellow-700 flex-shrink-0">Pending</span>
                    @endif
                </div>
            @empty
                <div class="px-6 py-8 text-center text-gray-400 text-sm">
                    Tidak ada tugas hari ini.
                </div>
            @endforelse
        </div>
    </div>

@endsection