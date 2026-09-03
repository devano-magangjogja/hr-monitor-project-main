@extends('layouts.app')
@section('title', 'Dashboard Sosmed')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan tugas postingan konten & akun sosmed Anda')
@section('sidebar')
    @include('components.sidebar-sosmed')
@endsection
@section('content')
    @include('components.notification-popup')

    {{-- Stats Cards --}}
    <x-responsive-grid :cols="'grid-cols-2 sm:grid-cols-4'" :gap="'gap-3 sm:gap-4 md:gap-5'" class="mb-4 md:mb-5">
        <x-responsive-card :padding="'p-3 sm:p-4 md:p-5'">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs sm:text-sm font-medium text-gray-500">Akun Dikelola</p>
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-pink-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 sm:w-5 h-4 sm:h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12H8.01M12 12H12.01M16 12H16.01M21.0039 12C21.0039 16.9706 16.9745 21 12.0039 21C9.9675 21 3.00463 21 3.00463 21C3.00463 21 4.56382 17.2561 3.93982 16.0008C3.34076 14.7956 3.00391 13.4372 3.00391 12C3.00391 7.02944 7.03334 3 12.0039 3C16.9745 3 21.0039 7.02944 21.0039 12Z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-pink-600">{{ $sosmedStats['accounts_count'] }}</p>
            <p class="text-xs text-gray-400 mt-1">tanggung jawab saya</p>
        </x-responsive-card>

        <x-responsive-card :padding="'p-3 sm:p-4 md:p-5'">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs sm:text-sm font-medium text-gray-500">Perlu Submit Bukti</p>
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 sm:w-5 h-4 sm:h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-amber-600">{{ $sosmedStats['pending_upload'] }}</p>
            <p class="text-xs text-gray-400 mt-1">belum dikerjakan</p>
        </x-responsive-card>

        <x-responsive-card :padding="'p-3 sm:p-4 md:p-5'">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs sm:text-sm font-medium text-gray-500">Menunggu Verif PM</p>
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 sm:w-5 h-4 sm:h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-blue-600">{{ $sosmedStats['waiting_pm'] }}</p>
            <p class="text-xs text-gray-400 mt-1">sudah submit link</p>
        </x-responsive-card>

        <x-responsive-card :padding="'p-3 sm:p-4 md:p-5'">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs sm:text-sm font-medium text-gray-500">Disetujui Final</p>
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 sm:w-5 h-4 sm:h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-green-600">{{ $sosmedStats['approved_final'] }}</p>
            <p class="text-xs text-gray-400 mt-1">tuntas diverifikasi</p>
        </x-responsive-card>
    </x-responsive-grid>

    {{-- Ringkasan Tugas Hari Ini --}}
    <x-responsive-card>
        <div class="px-3 sm:px-6 pt-0.5 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-xs sm:text-sm font-semibold text-gray-700">Tugas Hari Ini</h2>
            <a href="{{ route('sosmed.tasks.all') }}"
                class="text-xs text-primary-600 hover:text-primary-700 font-medium">Lihat Semua →</a>
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