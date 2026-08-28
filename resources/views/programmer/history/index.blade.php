@extends('layouts.app')

@section('title', 'Riwayat Tugas')
@section('page-title', 'Riwayat Tugas')
@section('page-subtitle', 'Rekap seluruh tugas harian kamu')

@section('sidebar')
    @include('components.sidebar-programmer')
@endsection

@section('content')

    {{-- Filter Tanggal & Pencarian --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5 mb-6 shadow-sm">
        <form method="GET" action="{{ route('programmer.tasks.history') }}"
              class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">

            {{-- Cari Tugas --}}
            <div class="lg:col-span-6">
                <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Cari Tugas</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                           placeholder="Cari judul / deskripsi..."
                           class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition">
                </div>
            </div>

            {{-- Filter Tanggal --}}
            <div class="lg:col-span-3">
                <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Tanggal</label>
                <input type="date" name="date" value="{{ $date ?? '' }}"
                       class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs sm:text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition">
            </div>

            {{-- Tombol Aksi --}}
            <div class="sm:col-span-2 lg:col-span-3 flex items-center gap-2">
                <button type="submit"
                        class="flex-1 flex items-center justify-center gap-1.5 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs sm:text-sm font-semibold rounded-lg transition shadow-sm hover:shadow">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>Cari</span>
                </button>

                @if($date || $search)
                    <a href="{{ route('programmer.tasks.history') }}"
                       class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs sm:text-sm font-semibold rounded-lg transition text-center flex-shrink-0"
                       title="Reset Filter">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs sm:text-sm min-w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-3 sm:px-6 py-2.5 sm:py-3.5 font-semibold text-gray-600 w-20 sm:w-32">Tanggal
                        </th>
                        <th class="text-left px-3 sm:px-6 py-2.5 sm:py-3.5 font-semibold text-gray-600 w-32 sm:w-48">Judul
                        </th>
                        <th class="text-left px-3 sm:px-6 py-2.5 sm:py-3.5 font-semibold text-gray-600 w-20 sm:w-32">Sumber
                        </th>
                        <th
                            class="text-left px-3 sm:px-6 py-2.5 sm:py-3.5 font-semibold text-gray-600 hidden sm:table-cell">
                            Catatan</th>
                        <th class="text-left px-3 sm:px-6 py-2.5 sm:py-3.5 font-semibold text-gray-600 w-20 sm:w-28">Status
                        </th>
                        <th class="text-right px-3 sm:px-6 py-2.5 sm:py-3.5 font-semibold text-gray-600 w-12 sm:w-16">Detail
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($tasks as $task)
                        @php
                            $assignment = $task->assignments->first();
                        @endphp
                        <tr class="hover:bg-gray-50 transition">

                            {{-- Tanggal --}}
                            <td class="px-3 sm:px-6 py-3 sm:py-4 w-20 sm:w-32 text-gray-500 text-xs">
                                {{ $task->task_date->translatedFormat('d M Y') }}
                            </td>

                            {{-- Judul --}}
                            <td class="px-3 sm:px-6 py-3 sm:py-4 w-32 sm:w-48">
                                <div class="truncate max-w-[100px] sm:max-w-[170px] font-medium text-gray-800 text-xs sm:text-sm"
                                    title="{{ $task->title }}">
                                    {{ $task->title }}
                                </div>
                            </td>

                            {{-- Sumber --}}
                            <td class="px-3 sm:px-6 py-3 sm:py-4 w-20 sm:w-32">
                                @if($task->type === 'self')
                                    <span class="inline-flex items-center px-1.5 sm:px-2.5 py-0.5 sm:py-1 rounded-full
                                                                             text-xs font-medium bg-gray-100 text-gray-600">
                                        Mandiri
                                    </span>
                                @elseif($task->type === 'assigned')
                                    <span class="inline-flex items-center px-1.5 sm:px-2.5 py-0.5 sm:py-1 rounded-full
                                                                             text-xs font-medium bg-blue-50 text-blue-700"
                                        title="Dari: {{ $task->creator?->name ?? 'Admin' }}">
                                        <span class="hidden sm:inline">{{ $task->creator?->name ?? 'Admin' }}</span>
                                        <span class="sm:hidden">Tugas</span>
                                    </span>
                                @elseif($task->type === 'default')
                                    <span class="inline-flex items-center px-1.5 sm:px-2.5 py-0.5 sm:py-1 rounded-full
                                                                             text-xs font-medium bg-purple-50 text-purple-700">
                                        Rutin
                                    </span>
                                @endif
                            </td>

                            {{-- Catatan --}}
                            <td class="px-3 sm:px-6 py-3 sm:py-4 hidden sm:table-cell">
                                <div class="truncate max-w-[100px] sm:max-w-[220px] text-gray-500 text-xs"
                                    title="{{ $assignment?->note ?? '-' }}">
                                    {{ $assignment?->note ?? '-' }}
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="px-3 sm:px-6 py-3 sm:py-4 w-20 sm:w-28">
                                <x-task-status-badge :status="$assignment?->is_completed ?? 'pending'"
                                    :completedAt="$assignment?->completed_at" />
                            </td>
                            {{-- Detail --}}
                            <td class="px-3 sm:px-6 py-3 sm:py-4 w-12 sm:w-16">
                                <div class="flex justify-end">
                                    <button
                                        onclick="openDetailModal(JSON.parse(this.dataset.detail))"
                                        data-detail="{{ json_encode([
                                            'title'       => $task->title,
                                            'description' => $task->description ?? '',
                                            'type'        => $task->type,
                                            'date'        => $task->task_date->translatedFormat('d M Y'),
                                            'source'      => $task->creator?->name ?? 'Sistem',
                                            'status'      => $assignment?->is_completed ?? 'pending',
                                            'note'        => $assignment?->note ?? '',
                                            'assignees'   => [],
                                        ]) }}"
                                        class="p-1 sm:p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                                        title="Lihat Detail">
                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 sm:px-6 py-14 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-sm font-medium text-gray-500">
                                        {{ $date || $search ? 'Tidak ada tugas yang cocok dengan pencarian.' : 'Belum ada riwayat tugas.' }}
                                    </p>
                                    @if(!$date && !$search)
                                        <p class="text-xs text-gray-400">Riwayat akan muncul setelah kamu menyelesaikan tugas hari ini.</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection