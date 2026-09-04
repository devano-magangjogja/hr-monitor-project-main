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
        <div class="px-3 sm:px-6 pt-0.5 py-4 border-b border-gray-200 flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 class="text-xs sm:text-sm font-semibold text-gray-700">Tugas Hari Ini</h2>
                <p class="text-[11px] text-gray-400 mt-0.5">Tugas pengelolaan akun sosmed & tugas harian Anda</p>
            </div>
            <div class="flex items-center gap-3">
                @if($sosmedAccountTasks->isNotEmpty())
                    <a href="{{ route('sosmed.sosmed.index') }}"
                        class="text-xs text-primary-600 hover:text-primary-700 font-medium">Kelola Sosmed →</a>
                @endif
                <a href="{{ route('sosmed.tasks.all') }}"
                    class="text-xs text-gray-500 hover:text-gray-700 font-medium">Lihat Semua Tugas →</a>
            </div>
        </div>

        <div class="divide-y divide-gray-100">
            {{-- 1. Tugas Kelola Akun Sosmed --}}
            @if($sosmedAccountTasks->isNotEmpty())
                <div class="px-3 sm:px-6 py-2 bg-gradient-to-r from-pink-50/50 to-transparent text-[11px] font-semibold text-pink-700 flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12H8.01M12 12H12.01M16 12H16.01M21.0039 12C21.0039 16.9706 16.9745 21 12.0039 21C9.9675 21 3.00463 21 3.00463 21C3.00463 21 4.56382 17.2561 3.93982 16.0008C3.34076 14.7956 3.00391 13.4372 3.00391 12C3.00391 7.02944 7.03334 3 12.0039 3C16.9745 3 21.0039 7.02944 21.0039 12Z" />
                        </svg>
                        Tugas Kelola Akun Sosial Media ({{ $sosmedAccountTasks->count() }} Akun)
                    </span>
                    <span class="text-[10px] text-gray-400 font-normal">Wajib submit bukti posting hari ini</span>
                </div>

                @foreach($sosmedAccountTasks as $item)
                    <div class="px-3 sm:px-6 py-3 flex items-center gap-3 hover:bg-gray-50/70 transition">
                        {{-- Status Check Circle --}}
                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0
                            {{ $item['is_done'] ? 'border-emerald-500 bg-emerald-500 text-white' : ($item['is_waiting'] ? 'border-blue-400 bg-blue-50 text-blue-600' : ($item['is_rejected'] ? 'border-rose-500 bg-rose-50 text-rose-600' : 'border-amber-400 bg-amber-50 text-amber-600')) }}">
                            @if($item['is_done'])
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            @elseif($item['is_waiting'])
                                <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @elseif($item['is_rejected'])
                                <svg class="w-3 h-3 text-rose-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            @else
                                <div class="w-1.5 h-1.5 rounded-full bg-amber-500"></div>
                            @endif
                        </div>

                        {{-- Main Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="text-xs sm:text-sm font-semibold truncate {{ $item['is_done'] ? 'line-through text-gray-400' : 'text-gray-800' }}">
                                    {{ $item['title'] }}
                                </p>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-medium border whitespace-nowrap {{ $item['platform_color'] }}">
                                    {{ $item['platform_icon'] }} {{ $item['platform'] }}
                                </span>
                            </div>
                            <div class="flex items-center gap-3 text-xs text-gray-400 mt-0.5">
                                <span>Tanggung Jawab: <strong class="text-gray-600 font-medium">Pengelola Akun</strong></span>
                                @if($item['task'] && $item['task']->hasLinks())
                                    <span>· {{ $item['task']->link_count }} link disubmit</span>
                                @endif
                            </div>
                            @if($item['is_rejected'] && $item['task']?->rejection_note)
                                <p class="text-xs text-rose-600 mt-1 bg-rose-50 px-2 py-1 rounded border border-rose-200">
                                    Catatan Revisi: "{{ $item['task']->rejection_note }}"
                                </p>
                            @endif
                        </div>

                        {{-- Action / Status Badge --}}
                        <div class="flex items-center gap-2 flex-shrink-0">
                            @if($item['status'] === 'approved_hr')
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Disetujui Final ✓
                                </span>
                            @elseif($item['status'] === 'verified_by_pm')
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-200">
                                    Menunggu HR
                                </span>
                            @elseif($item['status'] === 'done_by_staff')
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                    Menunggu PM
                                </span>
                            @elseif($item['status'] === 'rejected')
                                <a href="{{ route('sosmed.sosmed.index') }}"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-rose-600 hover:bg-rose-700 text-white shadow-sm transition">
                                    Revisi Bukti →
                                </a>
                            @else
                                <a href="{{ route('sosmed.sosmed.index') }}"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-primary-600 hover:bg-primary-700 text-white shadow-sm transition">
                                    Submit Bukti →
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif

            {{-- 2. Tugas Rutin / Mandiri Lainnya --}}
            @if($tasks->isNotEmpty())
                @if($sosmedAccountTasks->isNotEmpty())
                    <div class="px-3 sm:px-6 py-2 bg-gray-50/70 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                        Tugas Lainnya ({{ $tasks->count() }})
                    </div>
                @endif

                @foreach($tasks->take(5) as $task)
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
                @endforeach
            @endif

            @if($sosmedAccountTasks->isEmpty() && $tasks->isEmpty())
                <div class="px-3 sm:px-6 py-8 text-center text-gray-400 text-xs sm:text-sm">Tidak ada tugas hari ini.</div>
            @endif
        </div>
    </x-responsive-card>
@endsection