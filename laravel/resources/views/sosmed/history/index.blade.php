@extends('layouts.app')
@section('title', 'Riwayat Tugas')
@section('page-title', 'Riwayat Tugas')
@section('page-subtitle', 'Daftar seluruh tugas yang pernah dikerjakan')
@section('sidebar')
    @include('components.sidebar-sosmed')
@endsection

@section('content')
    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('sosmed.tasks.history') }}" class="mb-6 flex flex-wrap gap-3 items-center">
        <div class="flex items-center gap-2">
            <label for="date" class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</label>
            <input type="date" id="date" name="date" value="{{ $date }}" class="px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
        </div>
        <div class="flex items-center gap-2 flex-1 min-w-[200px]">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari judul tugas..." class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
        </div>
        <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg transition shadow-sm">
            Filter
        </button>
        @if($date || $search)
            <a href="{{ route('sosmed.tasks.history') }}" class="px-4 py-2 border border-gray-300 bg-white hover:bg-gray-50 text-gray-600 text-sm font-semibold rounded-lg transition">
                Reset
            </a>
        @endif
    </form>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[580px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-48">Judul</th>
                        <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-28">Tanggal</th>
                        <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Catatan Penyelesaian</th>
                        <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-28">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($tasks as $task)
                        @php
                            $assignment = $task->assignments->first();
                            $status = $assignment?->is_completed ?? 'pending';
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 w-48 font-medium text-gray-800">
                                {{ $task->title }}
                            </td>
                            <td class="px-6 py-4 w-28 text-xs text-gray-500">
                                {{ $task->task_date->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-600">
                                {{ $assignment?->note ?? '-' }}
                            </td>
                            <td class="px-6 py-4 w-28">
                                <x-task-status-badge :status="$status" :completedAt="$assignment?->completed_at" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-400 text-sm">Tidak ada riwayat tugas ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
