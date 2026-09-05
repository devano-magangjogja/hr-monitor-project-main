@extends('layouts.app')

@section('title', 'Buat Tugas')
@section('page-title', 'Buat Tugas')
@section('page-subtitle', 'Distribusikan tugas harian kepada seluruh anggota tim')

@section('sidebar')
    @include('components.sidebar-admin')
@endsection

@section('content')

<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">
        Tugas hari ini:
        <span class="font-semibold text-gray-700">{{ $tasks->count() }}</span> tugas
    </p>
    <button onclick="document.getElementById('modal-create').classList.remove('hidden')"
            class="flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700
                   text-white text-sm font-medium rounded-lg transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Buat Tugas
    </button>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-xs sm:text-sm min-w-[600px]">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="text-left px-3 sm:px-6 py-2.5 sm:py-3.5 font-semibold text-gray-600 text-xs sm:text-sm w-32 sm:w-48">Judul</th>
                <th class="text-left px-3 sm:px-6 py-2.5 sm:py-3.5 font-semibold text-gray-600 text-xs sm:text-sm w-28 sm:w-32">Kantor</th>
                <th class="text-left px-3 sm:px-6 py-2.5 sm:py-3.5 font-semibold text-gray-600 text-xs sm:text-sm">Deskripsi</th>
                <th class="text-left px-3 sm:px-6 py-2.5 sm:py-3.5 font-semibold text-gray-600 text-xs sm:text-sm w-24 sm:w-48">Penerima</th>
                <th class="text-right px-3 sm:px-6 py-2.5 sm:py-3.5 font-semibold text-gray-600 text-xs sm:text-sm w-16 sm:w-20">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($tasks as $task)
                @php
                    $hasCompleted = $task->assignments->where('is_completed', 'completed')->count() > 0;
                    $hasNotDone   = $task->assignments->where('is_completed', 'not_done')->count() > 0;
                @endphp
                <tr class="hover:bg-gray-50 transition">

                    {{-- Judul --}}
                    <td class="px-3 sm:px-6 py-4 w-36 sm:w-48">
                        <span class="font-medium text-gray-800 text-xs sm:text-sm" title="{{ $task->title }}">
                            {{ $task->title }}
                        </span>
                    </td>

                    {{-- Kantor --}}
                    <td class="px-3 sm:px-6 py-4 w-28 sm:w-32">
                        @if($task->kantor)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $task->kantor }}
                            </span>
                        @else
                            <span class="text-gray-400 text-xs sm:text-sm font-medium">-</span>
                        @endif
                    </td>

                    {{-- Deskripsi --}}
                    <td class="px-3 sm:px-6 py-4">
                        <div class="truncate max-w-[80px] sm:max-w-[220px] text-gray-500 text-xs sm:text-sm"
                             title="{{ $task->description ?? '-' }}">
                            @if($task->description)
                                {!! linkify(e($task->description)) !!}
                            @else
                                -
                            @endif
                        </div>
                    </td>

                    {{-- Penerima --}}
                    <td class="px-3 sm:px-6 py-4 w-24 sm:w-48">
                        <div class="flex flex-wrap gap-1">
                            @foreach($task->assignedUsers as $assignee)
                                @php
                                    $assignment = $task->assignments->firstWhere('user_id', $assignee->id);
                                    $done       = $assignment?->is_completed === 'completed';
                                    $notDone    = $assignment?->is_completed === 'not_done';
                                @endphp
                                <span class="inline-flex items-center gap-0.5 sm:gap-1 px-1.5 sm:px-2 py-0.5 rounded-full
                                             text-xs font-medium
                                             {{ $done ? 'bg-green-50 text-green-700' : ($notDone ? 'bg-red-50 text-red-700' : 'bg-gray-100 text-gray-600') }}">
                                    @if($done)
                                        <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                  clip-rule="evenodd"/>
                                        </svg>
                                    @elseif($notDone)
                                        <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                  clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                <span class="inline">{{ $assignee->name }}</span>
                                </span>
                            @endforeach
                        </div>
                    </td>

                    {{-- Aksi --}}
                    <td class="px-3 sm:px-6 py-4 w-16 sm:w-20">
                        <div class="flex items-center justify-end gap-1 whitespace-nowrap">
                            {{-- Tombol Detail: Selalu Ada --}}
                            <button
                                type="button"
                                onclick="openDetailModal(JSON.parse(this.dataset.detail))"
                                data-detail="{{ json_encode([
                                    'title'       => $task->title,
                                    'description' => $task->description ?? '',
                                    'kantor'      => $task->kantor,
                                    'type'        => $task->type,
                                    'date'        => $task->task_date->translatedFormat('d M Y'),
                                    'source'      => $task->creator?->name ?? 'Sistem',
                                    'status'      => 'multiple',
                                    'note'        => null,
                                    'assignees'   => $task->assignedUsers->map(fn($u) => [
                                        'name'   => $u->name,
                                        'role'   => $u->role_label,
                                        'status' => $task->assignments->firstWhere('user_id', $u->id)?->is_completed ?? 'pending',
                                    ]),
                                ]) }}"
                                class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                                title="Lihat Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>

                            {{-- Tombol Edit & Hapus: Hanya jika belum dikerjakan --}}
                            @if(!$hasCompleted && !$hasNotDone)
                                <button
                                    onclick="openEditModal(
                                        {{ $task->id }},
                                        '{{ addslashes($task->title) }}',
                                        '{{ addslashes($task->description ?? '') }}',
                                        {{ json_encode($task->assignedUsers->pluck('id')) }},
                                        '{{ $task->kantor ?? '' }}'
                                    )"
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
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-400 text-sm">
                        Belum ada tugas hari ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ── MODAL TAMBAH ───────────────────────────────────── --}}
<x-responsive-modal id="modal-create">
    <div class="flex items-center justify-between px-3 sm:px-6 py-4 border-b border-gray-200 flex-shrink-0">
        <h3 class="text-base font-semibold text-gray-800">Buat Tugas</h3>
        <button onclick="document.getElementById('modal-create').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <form action="{{ route('admin.tasks.store') }}" method="POST"
          class="px-3 sm:px-6 py-5 space-y-4 overflow-y-auto flex-1"
          id="form-create-task"
          onsubmit="return validateCreateRecipients()">
        @csrf
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Judul Tugas</label>
            <input type="text" name="title" value="{{ old('title') }}" required maxlength="200"
                   class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm
                          focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">
                Deskripsi / Instruksi <span class="text-gray-400 font-normal">(opsional)</span>
            </label>
            <textarea name="description" rows="3"
                      class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm
                             focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none">{{ old('description') }}</textarea>
        </div>
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">
                Lokasi Kantor Penugasan <span class="text-gray-400 font-normal">(opsional)</span>
            </label>
            <select name="kantor"
                    class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm
                           focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                <option value="">-- Tanpa Lokasi Kantor Khusus --</option>
                <option value="Kantor 1" {{ old('kantor') == 'Kantor 1' ? 'selected' : '' }}>Kantor 1</option>
                <option value="Kantor 2" {{ old('kantor') == 'Kantor 2' ? 'selected' : '' }}>Kantor 2</option>
                <option value="Kantor 3" {{ old('kantor') == 'Kantor 3' ? 'selected' : '' }}>Kantor 3</option>
                <option value="Kantor 4" {{ old('kantor') == 'Kantor 4' ? 'selected' : '' }}>Kantor 4</option>
            </select>
            <p class="text-[11px] text-gray-400 mt-1">Berlaku untuk tugas ke seluruh role. Kosongkan jika tidak ada lokasi khusus.</p>
        </div>
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5">
                Penerima Tugas <span class="text-red-500">*</span>
            </label>
            <div class="relative mb-2">
                <input type="text"
                       placeholder="Cari penerima tugas..."
                       oninput="filterRecipients(this.value, 'create-recipients-box')"
                       class="w-full pl-8 pr-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-primary-500 bg-gray-50 focus:bg-white transition">
                <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <div id="create-recipients-box"
                 class="space-y-2 max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-2 sm:p-3">
                @foreach($assignableUsers as $user)
                    <label class="recipient-item flex items-center gap-3 cursor-pointer hover:bg-gray-50 p-1.5 rounded-lg"
                           data-search="{{ strtolower($user->name . ' ' . $user->role_label) }}">
                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                               class="create-recipient-checkbox h-4 w-4 text-primary-600 border-gray-300 rounded">
                        <div>
                            <p class="text-xs sm:text-sm font-medium text-gray-700">{{ $user->name }}</p>
                            <p class="text-xs text-gray-400">
                                {{ $user->role_label }}
                            </p>
                        </div>
                    </label>
                @endforeach
                <div class="recipient-no-result hidden py-3 text-center text-xs text-gray-400">
                    Tidak ada penerima yang cocok.
                </div>
            </div>
            <p id="create-recipient-error"
               class="hidden mt-1.5 text-xs text-red-500 flex items-center gap-1">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                          d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                          clip-rule="evenodd"/>
                </svg>
                Pilih minimal satu penerima tugas.
            </p>
            @error('user_ids')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex justify-end gap-2 sm:gap-3 pt-2">
            <x-responsive-button variant="secondary" size="sm" type="button"
                    onclick="document.getElementById('modal-create').classList.add('hidden')">
                Batal
            </x-responsive-button>
            <x-responsive-button variant="primary" size="sm" type="submit">
                Kirim Tugas
            </x-responsive-button>
        </div>
    </form>
</x-responsive-modal>

{{-- ── MODAL EDIT ─────────────────────────────────────── --}}
<x-responsive-modal id="modal-edit">
    <div class="flex items-center justify-between px-3 sm:px-6 py-4 border-b border-gray-200 flex-shrink-0">
        <h3 class="text-base font-semibold text-gray-800">Edit Tugas</h3>
        <button onclick="document.getElementById('modal-edit').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <form id="form-edit" action="" method="POST"
          class="px-3 sm:px-6 py-5 space-y-4 overflow-y-auto flex-1">
        @csrf
        @method('PATCH')
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Judul Tugas</label>
            <input type="text" id="edit-title" name="title" required maxlength="200"
                   class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm
                          focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">
                Deskripsi / Instruksi <span class="text-gray-400 font-normal">(opsional)</span>
            </label>
            <textarea id="edit-description" name="description" rows="3"
                      class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm
                             focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"></textarea>
        </div>
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">
                Lokasi Kantor Penugasan <span class="text-gray-400 font-normal">(opsional)</span>
            </label>
            <select id="edit-kantor" name="kantor"
                    class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm
                           focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                <option value="">-- Tanpa Lokasi Kantor Khusus --</option>
                <option value="Kantor 1">Kantor 1</option>
                <option value="Kantor 2">Kantor 2</option>
                <option value="Kantor 3">Kantor 3</option>
                <option value="Kantor 4">Kantor 4</option>
            </select>
            <p class="text-[11px] text-gray-400 mt-1">Berlaku untuk tugas ke seluruh role. Kosongkan jika tidak ada lokasi khusus.</p>
        </div>
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5">
                Penerima Tugas <span class="text-red-500">*</span>
            </label>
            <div class="relative mb-2">
                <input type="text"
                       placeholder="Cari penerima tugas..."
                       oninput="filterRecipients(this.value, 'edit-recipients-box')"
                       class="w-full pl-8 pr-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-primary-500 bg-gray-50 focus:bg-white transition">
                <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <div id="edit-recipients-box" class="space-y-2 max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-2 sm:p-3">
                @foreach($assignableUsers as $user)
                    <label class="recipient-item flex items-center gap-3 cursor-pointer hover:bg-gray-50 p-1.5 rounded-lg"
                           data-search="{{ strtolower($user->name . ' ' . $user->role_label) }}">
                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                               class="edit-user-checkbox h-4 w-4 text-primary-600 border-gray-300 rounded">
                        <div>
                            <p class="text-xs sm:text-sm font-medium text-gray-700">{{ $user->name }}</p>
                            <p class="text-xs text-gray-400">
                                {{ $user->role_label }}
                            </p>
                        </div>
                    </label>
                @endforeach
                <div class="recipient-no-result hidden py-3 text-center text-xs text-gray-400">
                    Tidak ada penerima yang cocok.
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-2 sm:gap-3 pt-2">
            <x-responsive-button variant="secondary" size="sm" type="button"
                    onclick="document.getElementById('modal-edit').classList.add('hidden')">
                Batal
            </x-responsive-button>
            <x-responsive-button variant="primary" size="sm" type="submit">
                Simpan Perubahan
            </x-responsive-button>
        </div>
    </form>
</x-responsive-modal>

{{-- ── MODAL HAPUS ─────────────────────────────────────── --}}
<x-responsive-modal id="modal-delete" class="max-w-sm">
    <div class="px-3 sm:px-6 py-5 text-center">
        <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </div>
        <h3 class="text-base font-semibold text-gray-800 mb-1">Hapus Tugas</h3>
        <p class="text-xs sm:text-sm text-gray-500 mb-6">
            Apakah Anda yakin ingin menghapus tugas <strong id="delete-task-title"></strong>?
            Tindakan ini tidak dapat dibatalkan.
        </p>
        <form id="form-delete" action="" method="POST" class="flex justify-center gap-2 sm:gap-3">
            @csrf
            @method('DELETE')
            <x-responsive-button variant="secondary" size="sm" type="button"
                    onclick="document.getElementById('modal-delete').classList.add('hidden')">
                Batal
            </x-responsive-button>
            <x-responsive-button variant="danger" size="sm" type="submit">
                Ya, Hapus
            </x-responsive-button>
        </form>
    </div>
</x-responsive-modal>

<script>
    function openEditModal(id, title, description, currentUserIds, kantor) {
        document.getElementById('edit-title').value = title;
        document.getElementById('edit-description').value = description;
        const editKantorEl = document.getElementById('edit-kantor');
        if (editKantorEl) {
            editKantorEl.value = kantor || '';
        }
        document.getElementById('form-edit').action = `/admin/tasks/${id}`;
        document.querySelectorAll('.edit-user-checkbox').forEach(cb => {
            cb.checked = currentUserIds.includes(parseInt(cb.value));
        });
        document.getElementById('modal-edit').classList.remove('hidden');
    }

    function openDeleteModal(id, title) {
        document.getElementById('delete-task-title').textContent = title;
        document.getElementById('form-delete').action = `/admin/tasks/${id}`;
        document.getElementById('modal-delete').classList.remove('hidden');
    }

    function validateCreateRecipients() {
        const checked = document.querySelectorAll('.create-recipient-checkbox:checked').length;
        const errorEl = document.getElementById('create-recipient-error');
        const box     = document.getElementById('create-recipients-box');
        if (checked === 0) {
            errorEl.classList.remove('hidden');
            box.classList.add('border-red-400');
            box.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            return false;
        }
        errorEl.classList.add('hidden');
        box.classList.remove('border-red-400');
        return true;
    }

    // Buka modal create ulang jika server mengembalikan error user_ids
    @if($errors->has('user_ids'))
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('modal-create').classList.remove('hidden');
        });
    @endif
</script>

@endsection
