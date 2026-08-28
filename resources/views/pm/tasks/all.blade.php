@extends('layouts.app')

@section('title', 'Semua Tugas')
@section('page-title', 'Semua Tugas Hari Ini')
@section('page-subtitle', 'Gabungan seluruh tugas yang harus kamu selesaikan hari ini')

@section('sidebar')
    @include('components.sidebar-pm')
@endsection

@section('content')

    @php
        $total = $tasks->count();
        $completed = 0;
        $pending = 0;
        $notDone = 0;
        foreach ($tasks as $task) {
            $s = $task->assignments->first()?->is_completed ?? 'pending';
            if ($s === 'completed')
                $completed++;
            elseif ($s === 'not_done')
                $notDone++;
            else
                $pending++;
        }
    @endphp

    {{-- Stat ringkas --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 px-4 py-3 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-primary-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-400">Total</p>
                <p class="text-lg font-bold text-gray-800">{{ $total }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 px-4 py-3 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-400">Selesai</p>
                <p class="text-lg font-bold text-green-600">{{ $completed }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 px-4 py-3 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-yellow-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-400">Pending</p>
                <p class="text-lg font-bold text-yellow-600">{{ $pending }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 px-4 py-3 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-400">Tidak Dikerjakan</p>
                <p class="text-lg font-bold text-red-600">{{ $notDone }}</p>
            </div>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[620px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-44">Judul</th>
                        <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-28">Kantor</th>
                        <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Deskripsi</th>
                        <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-28">Sumber</th>
                        <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-32">Status</th>
                        <th class="text-right px-6 py-3.5 font-semibold text-gray-600 w-20">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($tasks as $task)
                        @php
                            $assignment = $task->assignments->first();
                            $status = $assignment?->is_completed ?? 'pending';
                        @endphp
                        <tr class="hover:bg-gray-50 transition" data-task="{{ json_encode([
                           'title' => $task->title,
                            'kantor'      => $task->kantor,
                            'description' => $task->description ?? '',
                            'type' => $task->type,
                            'date' => $task->task_date->translatedFormat('d M Y'),
                            'source' => $task->creator?->name ?? 'Sistem',
                            'status' => $status,
                            'note' => $assignment?->note ?? '',
                            'assignees' => [],
                        ]) }}">

                            {{-- Judul --}}
                            <td class="px-6 py-4 w-44">
                                <div class="truncate max-w-[160px] font-medium text-gray-800" title="{{ $task->title }}">
                                    {{ $task->title }}
                                </div>
                            </td>
                            <td class="px-6 py-4 w-28">
                                <span class="text-sm text-gray-600">{{ $task->kantor ?: '-' }}</span>
                            </td>

                            {{-- Deskripsi --}}
                            <td class="px-6 py-4">
                                <div class="truncate max-w-[220px] text-gray-500" title="{{ $task->description ?? '-' }}">
                                    @if($task->description)
                                        {!! linkify(e($task->description)) !!}
                                    @else
                                        -
                                    @endif
                                </div>
                            </td>

                            {{-- Sumber --}}
                            <td class="px-6 py-4 w-28">
                                @if($task->type === 'default')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Rutin
                                    </span>
                                @elseif($task->type === 'self')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Mandiri
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700"
                                        title="{{ $task->creator?->name ?? 'Admin' }}">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        {{ Str::limit($task->creator?->name ?? 'Admin', 10) }}
                                    </span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4 w-32">
                                <x-task-status-badge :status="$status" :completedAt="$assignment?->completed_at" />
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 w-20">
                                <div class="flex items-center justify-end gap-1.5 whitespace-nowrap">
                                    {{-- Tombol Detail --}}
                                    <button onclick="openDetailModal(JSON.parse(this.closest('tr').dataset.task))"
                                        class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                                        title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>

                                    @if($status === 'pending')
                                        <button onclick="openCompleteModal({{ $task->id }}, '{{ addslashes($task->title) }}')"
                                            class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition"
                                            title="Tandai Selesai">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-14 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2m-6 9l2 2 4-4" />
                                    </svg>
                                    <p class="text-sm font-medium text-gray-500">Tidak ada tugas hari ini.</p>
                                    <p class="text-xs text-gray-400">Tugas akan muncul setelah diberikan atau dibuat.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Checklist Selesai --}}
    <div id="modal-complete" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Tandai Selesai</h3>
                    <p id="complete-task-title" class="text-xs text-gray-500 mt-0.5"></p>
                </div>
                <button onclick="document.getElementById('modal-complete').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="form-complete" action="" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Catatan Penyelesaian
                        <span class="text-gray-400 font-normal">(opsional)</span>
                    </label>
                    <textarea name="note" rows="4"
                        placeholder="Tuliskan laporan singkat atau catatan penyelesaian tugas ini..." class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                                         focus:outline-none focus:ring-2 focus:ring-green-500 resize-none"></textarea>
                    <p class="mt-1 text-xs text-gray-400">Setelah ditandai selesai, tugas tidak dapat diubah kembali.</p>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-complete').classList.add('hidden')"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 font-medium">
                        Batal
                    </button>
                    <button type="submit" class="flex items-center gap-2 px-5 py-2 bg-green-600 hover:bg-green-700
                                       text-white text-sm font-medium rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Selesai
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCompleteModal(id, title) {
            document.getElementById('complete-task-title').textContent = title;
            document.getElementById('form-complete').action = `/pm/tasks/all/${id}/complete`;
            document.getElementById('modal-complete').classList.remove('hidden');
        }
    </script>

@endsection
