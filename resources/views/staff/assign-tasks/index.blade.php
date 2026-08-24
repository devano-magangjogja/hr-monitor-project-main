@extends('layouts.app')

@section('title', 'Buat Tugas Assistant')
@section('page-title', 'Buat Tugas Assistant')
@section('page-subtitle', 'Distribusikan tugas kepada HR Assistant')

@section('sidebar')
    @include('components.sidebar-staff')
@endsection

@section('content')

{{-- Header + Tombol Tambah --}}
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">
        Tugas yang kamu buat hari ini:
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

{{-- Tabel --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[580px]">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-48">Judul</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Deskripsi</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-48">Penerima</th>
                <th class="text-right px-6 py-3.5 font-semibold text-gray-600 w-28">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($tasks as $task)
                @php
                    $hasCompleted = $task->assignments->where('is_completed', 1)->count() > 0;
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 w-48">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-medium text-gray-800" title="{{ $task->title }}">
                                {{ $task->title }}
                            </span>
                            @if($task->kantor)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    📍 {{ $task->kantor }}
                                </span>
                            @endif
                        </div>
                    </td>
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
                    <td class="px-6 py-4 w-48">
                        <div class="flex flex-wrap gap-1">
                            @foreach($task->assignedUsers as $assignee)
                                @php
                                    $assignment = $task->assignments->firstWhere('user_id', $assignee->id);
                                    $done = $assignment?->is_completed;
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $done ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    @if($done)
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                    {{ $assignee->name }}
                                </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-6 py-4 w-28">
                        <div class="flex items-center justify-end gap-2 whitespace-nowrap">

                            {{-- Tombol Detail — selalu tampil --}}
                            <button
                                onclick="openDetailModal(
                                    '{{ addslashes($task->title) }}',
                                    '{{ addslashes($task->description ?? '') }}',
                                    '{{ $task->task_date->translatedFormat('d M Y') }}',
                                    {{ json_encode($task->assignedUsers->map(function($u) use ($task) {
                                        $a = $task->assignments->firstWhere('user_id', $u->id);
                                        return [
                                            'name'         => $u->name,
                                            'status'       => $a?->is_completed ?? 'pending',
                                            'note'         => $a?->note ?? '',
                                            'completed_at' => $a?->completed_at?->translatedFormat('d M Y, H:i') ?? '',
                                        ];
                                    })->values()) }}
                                )"
                                class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                                title="Lihat Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>

                            @if(!$hasCompleted)
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
                            @else
                                <span class="text-xs text-gray-400 italic">Terkunci</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-400 text-sm">
                        Belum ada tugas yang kamu buat hari ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

{{-- Modal Tambah, Edit, Hapus — sama persis dengan Admin tapi action route berbeda --}}

{{-- ── MODAL TAMBAH ───────────────────────────────────── --}}
<div id="modal-create" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 flex flex-col max-h-[90vh]">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 flex-shrink-0">
            <h3 class="text-base font-semibold text-gray-800">Buat Tugas untuk Assistant</h3>
            <button onclick="document.getElementById('modal-create').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form action="{{ route('staff.assign.store') }}" method="POST"
              class="px-6 py-5 space-y-4 overflow-y-auto flex-1"
              onsubmit="return validateCreateRecipients()">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Tugas</label>
                <input type="text" name="title" value="{{ old('title') }}" required maxlength="200"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                              focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Deskripsi / Instruksi <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <textarea name="description" rows="3"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                                 focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none">{{ old('description') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Lokasi Kantor Penugasan <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <select name="kantor"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                    <option value="">-- Tanpa Lokasi Kantor Khusus --</option>
                    <option value="Kantor 1" {{ old('kantor') == 'Kantor 1' ? 'selected' : '' }}>Kantor 1</option>
                    <option value="Kantor 2" {{ old('kantor') == 'Kantor 2' ? 'selected' : '' }}>Kantor 2</option>
                    <option value="Kantor 3" {{ old('kantor') == 'Kantor 3' ? 'selected' : '' }}>Kantor 3</option>
                    <option value="Kantor 4" {{ old('kantor') == 'Kantor 4' ? 'selected' : '' }}>Kantor 4</option>
                </select>
                <p class="text-[11px] text-gray-400 mt-1">Jika dipilih, asisten akan terhubung ke kantor ini saat mencatat presensi hari ini.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Pilih HR Assistant <span class="text-red-500">*</span>
                </label>
                <div id="create-recipients-box"
                     class="space-y-2 max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-3">
                    @forelse($assignableUsers as $user)
                        <label class="flex items-center gap-3 cursor-pointer hover:bg-gray-50 p-1.5 rounded-lg">
                            <input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                                   class="create-recipient-checkbox h-4 w-4 text-primary-600 border-gray-300 rounded">
                            <p class="text-sm font-medium text-gray-700">{{ $user->name }}</p>
                        </label>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-2">
                            Tidak ada HR Assistant aktif.
                        </p>
                    @endforelse
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
            <div class="flex justify-end gap-3 pt-2">
                <button type="button"
                        onclick="document.getElementById('modal-create').classList.add('hidden')"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 font-medium">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition">
                    Kirim Tugas
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── MODAL EDIT ─────────────────────────────────────── --}}
<div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 flex flex-col max-h-[90vh]">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 flex-shrink-0">
            <h3 class="text-base font-semibold text-gray-800">Edit Tugas</h3>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="form-edit" action="" method="POST"
              class="px-6 py-5 space-y-4 overflow-y-auto flex-1">
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
                    Deskripsi / Instruksi <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <textarea id="edit-description" name="description" rows="3"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                                 focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Lokasi Kantor Penugasan <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <select id="edit-kantor" name="kantor"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                    <option value="">-- Tanpa Lokasi Kantor Khusus --</option>
                    <option value="Kantor 1">Kantor 1</option>
                    <option value="Kantor 2">Kantor 2</option>
                    <option value="Kantor 3">Kantor 3</option>
                    <option value="Kantor 4">Kantor 4</option>
                </select>
                <p class="text-[11px] text-gray-400 mt-1">Jika dipilih, asisten akan terhubung ke kantor ini saat mencatat presensi hari ini.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Pilih HR Assistant <span class="text-red-500">*</span>
                </label>
                <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-3">
                    @foreach($assignableUsers as $user)
                        <label class="flex items-center gap-3 cursor-pointer hover:bg-gray-50 p-1.5 rounded-lg">
                            <input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                                   class="edit-user-checkbox h-4 w-4 text-primary-600 border-gray-300 rounded">
                            <p class="text-sm font-medium text-gray-700">{{ $user->name }}</p>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button"
                        onclick="document.getElementById('modal-edit').classList.add('hidden')"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 font-medium">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── MODAL DETAIL TUGAS ──────────────────────────────── --}}
<div id="modal-detail" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 flex flex-col max-h-[85vh]">

        {{-- Header --}}
        <div class="flex items-start justify-between px-6 py-4 border-b border-gray-200 flex-shrink-0">
            <div class="min-w-0 pr-4">
                <h3 id="detail-title" class="text-base font-semibold text-gray-800 break-words"></h3>
                <p id="detail-date" class="text-xs text-gray-400 mt-0.5"></p>
            </div>
            <button onclick="document.getElementById('modal-detail').classList.add('hidden')"
                    class="flex-shrink-0 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Deskripsi --}}
        <div id="detail-desc-wrap" class="px-6 pt-4 hidden">
            <p class="text-xs font-medium text-gray-500 mb-1">Deskripsi / Instruksi</p>
            <p id="detail-desc" class="text-sm text-gray-700 whitespace-pre-line leading-relaxed"></p>
        </div>

        {{-- Daftar Penerima + Status --}}
        <div class="px-6 pt-4 pb-2 flex-shrink-0">
            <p class="text-xs font-medium text-gray-500 mb-2">Status per Penerima</p>
        </div>
        <div id="detail-assignees" class="px-6 pb-6 overflow-y-auto flex-1 space-y-2"></div>
    </div>
</div>

{{-- ── MODAL HAPUS ─────────────────────────────────────── --}}
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
                            class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                        Ya, Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── JavaScript ──────────────────────────────────────── --}}
<script>
    function openDetailModal(title, description, date, assignees) {
        document.getElementById('detail-title').textContent = title;
        document.getElementById('detail-date').textContent  = 'Tanggal: ' + date;

        const descWrap = document.getElementById('detail-desc-wrap');
        const descEl   = document.getElementById('detail-desc');
        if (description && description.trim() !== '') {
            descEl.textContent = description;
            descWrap.classList.remove('hidden');
        } else {
            descWrap.classList.add('hidden');
        }

        const statusLabel = { pending: 'Belum Selesai', completed: 'Selesai', not_done: 'Tidak Dikerjakan' };
        const statusClass  = {
            pending:  'bg-yellow-50 text-yellow-700 border-yellow-200',
            completed:'bg-green-50  text-green-700  border-green-200',
            not_done: 'bg-red-50    text-red-700    border-red-200',
        };
        const dotClass = {
            pending:  'bg-yellow-400',
            completed:'bg-green-500',
            not_done: 'bg-red-500',
        };

        const container = document.getElementById('detail-assignees');
        container.innerHTML = '';

        if (!assignees || assignees.length === 0) {
            container.innerHTML = '<p class="text-sm text-gray-400 text-center py-4">Tidak ada penerima.</p>';
        } else {
            assignees.forEach(function (a) {
                const s    = a.status || 'pending';
                const card = document.createElement('div');
                card.className = 'flex items-start gap-3 p-3 rounded-xl border ' + (statusClass[s] || statusClass.pending);
                card.innerHTML =
                    '<div class="w-2 h-2 rounded-full flex-shrink-0 mt-1.5 ' + (dotClass[s] || dotClass.pending) + '"></div>' +
                    '<div class="flex-1 min-w-0">' +
                        '<div class="flex items-center justify-between gap-2">' +
                            '<p class="text-sm font-semibold text-gray-800 truncate">' + escHtml(a.name) + '</p>' +
                            '<span class="text-xs font-medium whitespace-nowrap">' + (statusLabel[s] || s) + '</span>' +
                        '</div>' +
                        (a.completed_at
                            ? '<p class="text-xs text-gray-500 mt-0.5">Diselesaikan: ' + escHtml(a.completed_at) + '</p>'
                            : '') +
                        (a.note && a.note.trim() !== ''
                            ? '<p class="text-xs text-gray-500 mt-1 italic">"' + escHtml(a.note) + '"</p>'
                            : '') +
                    '</div>';
                container.appendChild(card);
            });
        }

        document.getElementById('modal-detail').classList.remove('hidden');
    }

    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function openEditModal(id, title, description, currentUserIds, kantor) {
        document.getElementById('edit-title').value = title;
        document.getElementById('edit-description').value = description;
        const editKantorEl = document.getElementById('edit-kantor');
        if (editKantorEl) {
            editKantorEl.value = kantor || '';
        }
        document.getElementById('form-edit').action = `/staff/assign-tasks/${id}`;

        document.querySelectorAll('.edit-user-checkbox').forEach(cb => {
            cb.checked = currentUserIds.includes(parseInt(cb.value));
        });

        document.getElementById('modal-edit').classList.remove('hidden');
    }

    function openDeleteModal(id, title) {
        document.getElementById('delete-task-title').textContent = title;
        document.getElementById('form-delete').action = `/staff/assign-tasks/${id}`;
        document.getElementById('modal-delete').classList.remove('hidden');
    }

    function validateCreateRecipients() {
        const checked = document.querySelectorAll('.create-recipient-checkbox:checked').length;
        const errorEl = document.getElementById('create-recipient-error');
        const box     = document.getElementById('create-recipients-box');
        if (checked === 0) {
            errorEl.classList.remove('hidden');
            box.classList.add('border-red-400');
            return false;
        }
        errorEl.classList.add('hidden');
        box.classList.remove('border-red-400');
        return true;
    }

    @if($errors->has('user_ids'))
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('modal-create').classList.remove('hidden');
        });
    @endif
</script>

@endsection