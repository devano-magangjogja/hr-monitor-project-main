@extends('layouts.app')

@section('title', 'Tugas Mandiri')
@section('page-title', 'Tugas Mandiri')
@section('page-subtitle', 'Catat pekerjaan yang kamu inisiasi sendiri hari ini')

@section('sidebar')
    @include('components.sidebar-cs')
@endsection

@section('content')

<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">
        Tugas mandiri hari ini:
        <span class="font-semibold text-gray-700">{{ $tasks->count() }}</span> tugas
    </p>
    <button onclick="document.getElementById('modal-create').classList.remove('hidden')"
            class="flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700
                   text-white text-sm font-medium rounded-lg transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Tugas Mandiri
    </button>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[580px]">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-48">Judul</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Deskripsi</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-32">Sumber</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-28">Status</th>
                <th class="text-right px-6 py-3.5 font-semibold text-gray-600 w-32">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($tasks as $task)
                @php
                    $assignment = $task->assignments->first();
                    $status     = $assignment?->is_completed ?? 'pending';
                @endphp
                <tr class="hover:bg-gray-50 transition"
                    data-task="{{ json_encode([
                        'title'       => $task->title,
                        'description' => $task->description ?? '',
                        'type'        => $task->type,
                        'date'        => $task->task_date->translatedFormat('d M Y'),
                        'source'      => $task->creator?->name ?? 'Sistem',
                        'status'      => $status,
                        'note'        => $assignment?->note ?? '',
                        'assignees'   => [],
                    ]) }}"
                    data-edit-title="{{ $task->title }}"
                    data-edit-desc="{{ $task->description ?? '' }}">
            
                    {{-- Judul --}}
                    <td class="px-6 py-4 w-48">
                        <div class="truncate max-w-[170px] font-medium text-gray-800"
                            title="{{ $task->title }}">
                            {{ $task->title }}
                        </div>
                    </td>
            
                    {{-- Deskripsi --}}
                    <td class="px-6 py-4">
                        <div class="truncate max-w-[220px] text-gray-500"
                            title="{{ $task->description ?? '-' }}">
                            @if($task->description)
                                {!! linkify(e($task->description)) !!}
                            @else
                                -
                            @endif
                        </div>
                    </td>
            
                    {{-- Sumber --}}
                    <td class="px-6 py-4 w-32">
                        @if($task->type === 'self')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                        bg-gray-100 text-gray-600">
                                Mandiri
                            </span>
                        @elseif($task->type === 'assigned')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                        bg-blue-50 text-blue-700"
                                title="Dari: {{ $task->creator?->name ?? 'Admin' }}">
                                {{ $task->creator?->name ?? 'Admin' }}
                            </span>
                        @elseif($task->type === 'default')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                        bg-purple-50 text-purple-700">
                                Rutin
                            </span>
                        @endif
                    </td>
            
                    {{-- Status --}}
                    <td class="px-6 py-4 w-28">
                        <x-task-status-badge :status="$status" :completedAt="$assignment?->completed_at" />
                    </td>
            
                    {{-- Aksi --}}
                    <td class="px-6 py-4 w-32">
                        <div class="flex items-center justify-end gap-2 whitespace-nowrap">
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

                            @if($status === 'pending')
                                {{-- Tombol Selesai --}}
                                <button
                                    onclick="openCompleteModal({{ $task->id }}, '{{ addslashes($task->title) }}')"
                                    class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition"
                                    title="Tandai Selesai">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
            
                                {{-- Edit & Hapus hanya untuk tugas mandiri --}}
                                @if($task->type === 'self')
                                    <button
                                        onclick="openEditModal({{ $task->id }}, this.closest('tr').dataset.editTitle, this.closest('tr').dataset.editDesc)"
                                        class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                                        title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button
                                        onclick="openDeleteModal({{ $task->id }}, '{{ addslashes($task->title) }}')"
                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                        title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">
                        Belum ada tugas hari ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

{{-- â”€â”€ MODAL TAMBAH â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
<div id="modal-create" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-800">Tambah Tugas Mandiri</h3>
            <button onclick="document.getElementById('modal-create').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form action="{{ route('cs.tasks.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Tugas</label>
                <input type="text" name="title" value="{{ old('title') }}" required maxlength="200"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                              focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Deskripsi <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <textarea name="description" rows="3"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                                 focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none">{{ old('description') }}</textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button"
                        onclick="document.getElementById('modal-create').classList.add('hidden')"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 font-medium">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm
                               font-medium rounded-lg transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- â”€â”€ MODAL EDIT â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
<div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-800">Edit Tugas Mandiri</h3>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="form-edit" action="" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Tugas</label>
                <input type="text" id="edit-title" name="title" required maxlength="200"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                              focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Deskripsi <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <textarea id="edit-description" name="description" rows="3"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                                 focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button"
                        onclick="document.getElementById('modal-edit').classList.add('hidden')"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 font-medium">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm
                               font-medium rounded-lg transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- â”€â”€ MODAL HAPUS â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
<div id="modal-delete" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4">
        <div class="px-6 py-5 text-center">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-800 mb-1">Hapus Tugas Mandiri</h3>
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
                            class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-sm
                                   font-medium rounded-lg transition">
                        Ya, Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- â”€â”€ MODAL CHECKLIST SELESAI â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
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
                <textarea name="note" rows="4" placeholder="Tuliskan laporan singkat atau catatan penyelesaian tugas ini..."
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
    function openEditModal(id, title, description) {
        document.getElementById('edit-title').value = title;
        document.getElementById('edit-description').value = description;
        document.getElementById('form-edit').action = `/cs/tasks/${id}`;
        document.getElementById('modal-edit').classList.remove('hidden');
    }

    function openDeleteModal(id, title) {
        document.getElementById('delete-task-title').textContent = title;
        document.getElementById('form-delete').action = `/cs/tasks/${id}`;
        document.getElementById('modal-delete').classList.remove('hidden');
    }
    
    function openCompleteModal(id, title) {
    document.getElementById('complete-task-title').textContent = title;
    document.getElementById('form-complete').action = `/cs/tasks/${id}/complete`;
    document.getElementById('modal-complete').classList.remove('hidden');
    }

</script>

@endsection
