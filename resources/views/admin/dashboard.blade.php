@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan aktivitas tim HR hari ini')

@section('sidebar')
    @include('components.sidebar-admin')
@endsection

@section('content')
    @include('components.notification-popup')

    {{-- ── Stat Card ──────────────────────────────────────── --}}
    <x-responsive-grid :cols="'grid-cols-2 lg:grid-cols-4'" :gap="'gap-3 sm:gap-4 md:gap-5'" class="mb-8">

        {{-- Total Tugas --}}
        <x-responsive-card :padding="'p-3.5 sm:p-4 md:p-5'" class="h-full flex flex-col justify-between hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-3 gap-2">
                <p class="text-xs sm:text-sm font-medium text-gray-500 truncate" title="Total Tugas Hari Ini">Total Tugas</p>
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-primary-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 sm:w-5 h-4 sm:h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                <p class="text-[11px] sm:text-xs text-gray-400 mt-1 truncate">assignment aktif hari ini</p>
            </div>
        </x-responsive-card>

        {{-- Selesai --}}
        <x-responsive-card :padding="'p-3.5 sm:p-4 md:p-5'" class="h-full flex flex-col justify-between hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-3 gap-2">
                <p class="text-xs sm:text-sm font-medium text-gray-500 truncate" title="Selesai">Selesai</p>
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 sm:w-5 h-4 sm:h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-2xl sm:text-3xl font-bold text-green-600">{{ $stats['completed'] }}</p>
                <p class="text-[11px] sm:text-xs text-gray-400 mt-1 truncate">sudah diselesaikan</p>
            </div>
        </x-responsive-card>

        {{-- Belum Selesai --}}
        <x-responsive-card :padding="'p-3.5 sm:p-4 md:p-5'" class="h-full flex flex-col justify-between hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-3 gap-2">
                <p class="text-xs sm:text-sm font-medium text-gray-500 truncate" title="Belum Selesai">Belum Selesai</p>
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-yellow-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 sm:w-5 h-4 sm:h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-2xl sm:text-3xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
                <p class="text-[11px] sm:text-xs text-gray-400 mt-1 truncate">masih dalam pengerjaan</p>
            </div>
        </x-responsive-card>

        {{-- Tidak Dikerjakan --}}
        <x-responsive-card :padding="'p-3.5 sm:p-4 md:p-5'" class="h-full flex flex-col justify-between hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-3 gap-2">
                <p class="text-xs sm:text-sm font-medium text-gray-500 truncate" title="Tidak Dikerjakan">Tidak Dikerjakan</p>
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 sm:w-5 h-4 sm:h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-2xl sm:text-3xl font-bold text-red-600">{{ $stats['not_done'] }}</p>
                <p class="text-[11px] sm:text-xs text-gray-400 mt-1 truncate">melewati batas waktu</p>
            </div>
        </x-responsive-card>
    </x-responsive-grid>

    {{-- ── Ranking Minggu Ini ──────────────────────────────── --}}
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4 px-3 sm:px-0">
            <h2 class="text-xs sm:text-sm font-semibold text-gray-700">Ranking Minggu Ini</h2>
            <a href="{{ route('admin.reports.ranking') }}"
            class="text-xs text-primary-600 hover:text-primary-700 font-medium">
                Lihat Semua →
            </a>
        </div>

        <x-responsive-grid :cols="'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3'" :gap="'gap-3 sm:gap-4 md:gap-5'">
            @forelse($rankings as $index => $item)
                @php
                    $rank = $index + 1;
                    $medal = match($rank) {
                        1 => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-200', 'icon' => 'text-yellow-500', 'score' => 'text-yellow-600'],
                        2 => ['bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'icon' => 'text-gray-400', 'score' => 'text-gray-600'],
                        3 => ['bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'icon' => 'text-amber-600', 'score' => 'text-amber-600'],
                        default => ['bg' => 'bg-white', 'border' => 'border-gray-200', 'icon' => 'text-gray-300', 'score' => 'text-gray-600'],
                    };
                @endphp
                <div class="{{ $medal['bg'] }} border {{ $medal['border'] }} rounded-xl p-3 sm:p-4 md:p-5">
                    <div class="flex items-center justify-between mb-3">
                        {{-- Medal Icon --}}
                        <svg class="w-6 sm:w-7 h-6 sm:h-7 {{ $medal['icon'] }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        {{-- Skor --}}
                        <div class="text-right">
                            <p class="text-xl sm:text-2xl font-bold {{ $medal['score'] }}">{{ $item['score'] }}</p>
                            <p class="text-xs text-gray-400">poin</p>
                        </div>
                    </div>
                    {{-- User Info --}}
                    <div class="flex items-center gap-3 mt-2">
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-white border border-gray-200
                                    flex items-center justify-center flex-shrink-0">
                            <span class="text-xs sm:text-sm font-semibold text-gray-600">
                                {{ strtoupper(substr($item['user']->name, 0, 1)) }}
                            </span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs sm:text-sm font-semibold text-gray-800 truncate">
                                {{ $item['user']->name }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ $item['user']->role_label }}
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="sm:col-span-2 lg:col-span-3 bg-white border border-gray-200 rounded-xl px-4 sm:px-6 py-8 text-center">
                    <p class="text-xs sm:text-sm text-gray-400">Belum ada data ranking minggu ini.</p>
                </div>
            @endforelse
        </x-responsive-grid>
    </div>

    {{-- ── Progress Per User ───────────────────────────────── --}}
    <x-team-progress
        :rows="$perUser"
        :unfinished="$progressUnfinished"
        :search="$progressSearch"
        :status="$progressStatus"
        :action-route="route('admin.dashboard')"
        :reminder-route="route('admin.dashboard.reminder')"
        detail-route="admin.dashboard.team-progress-detail"
    />

@endsection
