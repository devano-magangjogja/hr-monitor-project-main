@extends('layouts.app')

@section('title', 'Tugas HR Assistant')
@section('page-title', 'Tugas HR Assistant')
@section('page-subtitle', 'Pantau seluruh tugas yang masuk ke HR Assistant hari ini')

@section('sidebar')
    @include('components.sidebar-admin')
@endsection

@section('content')

<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">
        Total: <span class="font-semibold text-gray-700">{{ $tasks->count() }}</span> tugas
    </p>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[700px]">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-48">Judul</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Deskripsi</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-32">Dibuat Oleh</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-28">Sumber</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-48">Penerima</th>
                <th class="text-right px-6 py-3.5 font-semibold text-gray-600 w-20">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($tasks as $task)
            @php
                $hasCompleted = $task->assignments->where('is_completed', 'completed')->count() > 0;
                $hasNotDone   = $task->assignments->where('is_completed', 'not_done')->count() > 0;
            @endphp
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 w-48">
                    <div class="truncate max-w-[160px] font-medium text-gray-800"
                         title="{{ $task->title }}">
                        {{ $task->title }}
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="truncate max-w-[180px] text-gray-500"
                         title="{{ $task->description ?? '-' }}">
                        @if($task->description)
                            {!! linkify(e($task->description)) !!}
                        @else
                            -
                        @endif
                    </div>
                </td>
                <td class="px-6 py-4 w-32">
                    <div class="truncate max-w-[110px] text-gray-600 text-xs font-medium">
                        {{ $task->creator?->name ?? '-' }}
                    </div>
                </td>
                <td class="px-6 py-4 w-28">
                    @if($task->type === 'self')
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium
                                     bg-gray-100 text-gray-600">Mandiri</span>
                    @elseif($task->type === 'assigned')
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium
                                     bg-blue-50 text-blue-700">
                            {{ $task->creator?->name ?? 'Admin' }}
                        </span>
                    @elseif($task->type === 'default')
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium
                                     bg-purple-50 text-purple-700">Rutin</span>
                    @endif
                </td>
        
                {{-- Penerima --}}
                <td class="px-6 py-4 w-48">
                    <div class="flex flex-wrap gap-1">
                        @forelse($task->assignedUsers as $assignee)
                            @php
                                $assignment = $task->assignments->firstWhere('user_id', $assignee->id);
                                $done       = $assignment?->is_completed === 'completed';
                                $notDone    = $assignment?->is_completed === 'not_done';
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                         text-xs font-medium
                                         {{ $done ? 'bg-green-50 text-green-700' : ($notDone ? 'bg-red-50 text-red-700' : 'bg-gray-100 text-gray-600') }}">
                                @if($done)
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                @elseif($notDone)
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                @endif
                                {{ $assignee->name }}
                            </span>
                        @empty
                            <span class="text-xs text-gray-400">
                                {{ $task->creator?->name ?? '-' }}
                            </span>
                        @endforelse
                    </div>
                </td>
        
                {{-- Aksi --}}
                <td class="px-6 py-4 w-20">
                    <div class="flex items-center justify-end whitespace-nowrap">
                        @if(!$hasCompleted && !$hasNotDone)
                            <button
                                onclick="openDeleteModal({{ $task->id }}, '{{ addslashes($task->title) }}')"
                                class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                            <button
                                onclick="openDetailModal({
                                    title: '{{ addslashes($task->title) }}',
                                    description: '{{ addslashes($task->description ?? '') }}',
                                    type: '{{ $task->type }}',
                                    date: '{{ $task->task_date->translatedFormat('d M Y') }}',
                                    source: '{{ addslashes($task->creator?->name ?? 'Sistem') }}',
                                    status: 'multiple',
                                    note: null,
                                    assignees: {{ json_encode($task->assignedUsers->map(fn($u) => [
                                        'name'   => $u->name,
                                        'role'   => $u->role === 'hr_staff' ? 'HR Staff' : 'HR Assistant',
                                        'status' => $task->assignments->firstWhere('user_id', $u->id)?->is_completed ?? 'pending',
                                    ])) }}
                                })"
                                class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                                title="Lihat Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        @elseif($hasCompleted)
                            <span class="text-xs text-gray-400 italic">Terkunci</span>
                        @else
                            <span class="text-xs text-red-400 italic">Tidak Dikerjakan</span>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">
                    Tidak ada tugas untuk HR Assistant hari ini.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
    </div>
</div>

{{-- Modal Hapus --}}
<div id="modal-delete" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4">
        <div class="px-6 py-5 text-center">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-800 mb-1">Hapus Tugas</h3>
            <p class="text-sm text-gray-500 mb-6">
                Yakin ingin menghapus
                <span id="delete-task-title" class="font-semibold text-gray-700"></span>?
            </p>
            <form id="form-delete" action="" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-center gap-3">
                    <button type="button"
                            onclick="document.getElementById('modal-delete').classList.add('hidden')"
                            class="px-5 py-2 text-sm text-gray-600 font-medium border border-gray-300 rounded-lg">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white
                                   text-sm font-medium rounded-lg transition">
                        Ya, Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openDeleteModal(id, title) {
        document.getElementById('delete-task-title').textContent = title;
        document.getElementById('form-delete').action = `/admin/tasks/${id}/force`;
        document.getElementById('modal-delete').classList.remove('hidden');
    }
</script>

@endsection