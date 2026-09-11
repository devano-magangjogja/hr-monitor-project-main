@extends('layouts.app')

@section('title', 'Tugas Harian')
@section('page-title', 'Tugas Harian')
@section('page-subtitle', 'Tugas rutin yang di-generate otomatis hari ini')

@section('sidebar')
    @include('components.sidebar-ob')
@endsection

@section('content')

<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">
        Total: <span class="font-semibold text-gray-700">{{ $tasks->count() }}</span> tugas harian
    </p>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
    <table class="w-full text-sm text-left border-collapse min-w-[700px]">
        <thead>
            <tr class="bg-gray-50/80 border-b border-gray-200 text-xs font-semibold uppercase tracking-wider text-gray-500">
                <th scope="col" class="px-5 py-3.5 w-[28%] min-w-[140px] text-left">Judul</th>
                <th scope="col" class="px-5 py-3.5 w-[12%] min-w-[80px] text-left">Kantor</th>
                <th scope="col" class="px-5 py-3.5 text-left">Deskripsi</th>
                <th scope="col" class="px-5 py-3.5 w-[12%] min-w-[80px] text-left">Status</th>
                <th scope="col" class="px-5 py-3.5 w-[10%] min-w-[60px] text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
            @forelse($tasks as $task)
                @php
                    $assignment = $task->assignments->first();
                    $status     = $assignment?->is_completed ?? 'pending';
                @endphp
                <tr class="hover:bg-gray-50/80 transition-colors duration-150"
                    data-task="{{ json_encode([
                       'title'       => $task->title,
                        'kantor'      => $task->kantor,
                        'description' => $task->description ?? '',
                        'type'        => $task->type,
                        'date'        => $task->task_date->translatedFormat('d M Y'),
                        'source'      => 'Sistem',
                        'status'      => $status,
                        'note'        => $assignment?->note ?? '',
                        'assignees'   => [],
                    ]) }}">
                    <td class="px-5 py-3.5 whitespace-nowrap">
                        <div class="truncate max-w-[200px] font-medium text-gray-900"
                             title="{{ $task->title }}">
                            {{ $task->title }}
                        </div>
                    </td>
                    <td class="px-5 py-3.5 whitespace-nowrap text-sm text-gray-600">{{ $task->kantor ?: '-' }}</td>
                    <td class="px-5 py-3.5">
                        <div class="truncate max-w-[260px] text-gray-500"
                             title="{{ $task->description ?? '-' }}">
                            @if($task->description)
                                {!! linkify(e($task->description)) !!}
                            @else
                                -
                            @endif
                        </div>
                    </td>
                    <td class="px-5 py-3.5 whitespace-nowrap">
                        <x-task-status-badge :status="$status" :completedAt="$assignment?->completed_at" />
                    </td>
                    <td class="px-5 py-3.5 whitespace-nowrap">
                        <div class="inline-flex items-center justify-center gap-1">
                            {{-- Tombol Detail --}}
                            <button
                                onclick="openDetailModal(JSON.parse(this.closest('tr').dataset.task))"
                                class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                                title="Lihat Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>

                            {{-- Tombol Selesai / Label --}}
                            @if($status === 'pending')
                                <button
                                    onclick="openCompleteModal({{ $task->id }}, '{{ addslashes($task->title) }}')"
                                    class="p-1.5 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition"
                                    title="Tandai Selesai">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                            @elseif($status === 'completed')
                                <span class="text-xs text-gray-400 italic">Selesai</span>
                            @else
                                <span class="text-xs text-rose-400 italic">Tidak Dikerjakan</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-400 text-sm">
                        Tidak ada tugas harian hari ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

{{-- â”€â”€ Modal Checklist Selesai â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
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
                          placeholder="Tuliskan laporan singkat atau catatan penyelesaian tugas ini..."
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                                 focus:outline-none focus:ring-2 focus:ring-green-500 resize-none"></textarea>
                <p class="mt-1 text-xs text-gray-400">
                    Setelah ditandai selesai, tugas tidak dapat diubah kembali.
                </p>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button"
                        onclick="document.getElementById('modal-complete').classList.add('hidden')"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 font-medium">
                    Batal
                </button>
                <button type="submit"
                        class="flex items-center gap-2 px-5 py-2 bg-green-600 hover:bg-green-700
                               text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
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
        document.getElementById('form-complete').action = `/ob/tasks/daily/${id}/complete`;
        document.getElementById('modal-complete').classList.remove('hidden');
    }
</script>

@endsection
