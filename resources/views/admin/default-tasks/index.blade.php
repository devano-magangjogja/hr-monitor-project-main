@extends('layouts.app')

@section('title', 'Default Task')
@section('page-title', 'Default Task')
@section('page-subtitle', 'Kelola tugas rutin harian per role')

@section('sidebar')
    @include('components.sidebar-admin')
@endsection

@section('content')

    {{-- Header + Tombol Tambah --}}
    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-gray-500">
            Total <span class="font-semibold text-gray-700">{{ $defaultTasks->count() }}</span> default task
        </p>
        <button onclick="document.getElementById('modal-create').classList.remove('hidden')" class="flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700
                       text-white text-sm font-medium rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Default Task
        </button>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[580px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-44">Judul</th>
                        <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Deskripsi</th>
                        <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-32">Target Role</th>
                        <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-24">Status</th>
                        <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-36">Dibuat Oleh</th>
                        <th class="text-right px-6 py-3.5 font-semibold text-gray-600 w-20">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($defaultTasks as $task)
                                    <tr class="hover:bg-gray-50 transition">

                                        {{-- Judul --}}
                                        <td class="px-6 py-4 w-44">
                                            <div class="truncate max-w-[160px] font-medium text-gray-800" title="{{ $task->title }}">
                                                {{ $task->title }}
                                            </div>
                                        </td>

                                        {{-- Deskripsi --}}
                                        <td class="px-6 py-4">
                                            <div class="truncate max-w-[280px] text-gray-500" title="{{ $task->description ?? '-' }}">
                                                @if($task->description)
                                                    {!! linkify(e($task->description)) !!}
                                                @else
                                                    -
                                                @endif
                                            </div>
                                        </td>

                                        {{-- Target Role --}}
                                        <td class="px-6 py-4 w-32">
                                            @php
                                                $targetRoleModel = $roles->firstWhere('name', $task->target_role);
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $targetRoleModel?->badge_class ?? 'bg-gray-100 text-gray-700' }}">
                                                {{ $targetRoleModel?->label ?? strtoupper($task->target_role) }}
                                            </span>
                                        </td>

                                        {{-- Status --}}
                                        <td class="px-6 py-4 w-24">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                                {{ $task->is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                                {{ $task->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>

                                        {{-- Dibuat Oleh --}}
                                        <td class="px-6 py-4 w-36">
                                            <div class="truncate max-w-[120px] text-gray-500" title="{{ $task->creator?->name ?? '-' }}">
                                                {{ $task->creator?->name ?? '-' }}
                                            </div>
                                        </td>

                                        {{-- Aksi — flex-shrink-0 agar tidak pernah hilang --}}
                                        <td class="px-6 py-4 w-20 flex-shrink-0">
                                            <div class="flex items-center justify-end gap-2 whitespace-nowrap">
                                                <button type="button" onclick="openEditModal(this)" data-id="{{ $task->id }}"
                                                    data-title="{{ $task->title }}" data-description="{{ $task->description ?? '' }}"
                                                    data-target-role="{{ $task->target_role }}"
                                                    data-is-active="{{ $task->is_active ? 1 : 0 }}"
                                                    class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                                                    title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                <button type="button" onclick="openDeleteModal(this)" data-id="{{ $task->id }}"
                                                    data-title="{{ $task->title }}"
                                                    class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                                    title="Hapus">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">
                                Belum ada default task. Tambahkan default task pertama.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── MODAL TAMBAH ───────────────────────────────────── --}}
    <x-responsive-modal id="modal-create">
        <div class="flex items-center justify-between px-3 sm:px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-800">Tambah Default Task</h3>
            <button onclick="document.getElementById('modal-create').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form action="{{ route('admin.default-tasks.store') }}" method="POST"
            class="px-3 sm:px-6 py-5 space-y-4 overflow-y-auto flex-1">
            @csrf
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Judul Tugas</label>
                <input type="text" name="title" value="{{ old('title') }}" required maxlength="150" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm
                              focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">
                    Deskripsi/Catatan <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <textarea name="description" rows="3"
                    class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm
                                 focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none">{{ old('description') }}</textarea>
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Target Role</label>
                <div class="relative mb-1.5">
                    <input type="text" id="search-create-target-role" placeholder="Cari role..."
                           oninput="filterSelectOptions(this.value, 'create-target-role')"
                           class="w-full pl-7 pr-2.5 py-1 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-primary-500 bg-gray-50 focus:bg-white transition">
                    <svg class="w-3 h-3 text-gray-400 absolute left-2 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <select name="target_role" id="create-target-role" required class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm
                               focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">-- Pilih Role --</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->name }}">{{ $r->label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="is_active" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm
                               focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>
            <div class="flex justify-end gap-2 sm:gap-3 pt-2">
                <x-responsive-button variant="secondary" size="sm" type="button"
                    onclick="document.getElementById('modal-create').classList.add('hidden')">
                    Batal
                </x-responsive-button>
                <x-responsive-button variant="primary" size="sm" type="submit">
                    Simpan
                </x-responsive-button>
            </div>
        </form>
    </x-responsive-modal>

    {{-- ── MODAL EDIT ─────────────────────────────────────── --}}
    <x-responsive-modal id="modal-edit">
        <div class="flex items-center justify-between px-3 sm:px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-800">Edit Default Task</h3>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="form-edit" action="" method="POST" class="px-3 sm:px-6 py-5 space-y-4 overflow-y-auto flex-1">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Judul Tugas</label>
                <input type="text" id="edit-title" name="title" required maxlength="150" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm
                              focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">
                    Deskripsi/Catatan <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <textarea id="edit-description" name="description" rows="3" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm
                                 focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"></textarea>
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Target Role</label>
                <div class="relative mb-1.5">
                    <input type="text" id="search-edit-target-role" placeholder="Cari role..."
                           oninput="filterSelectOptions(this.value, 'edit-target-role')"
                           class="w-full pl-7 pr-2.5 py-1 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-primary-500 bg-gray-50 focus:bg-white transition">
                    <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <select id="edit-target-role" name="target_role" required class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm
                               focus:outline-none focus:ring-2 focus:ring-primary-500">
                    @foreach($roles as $r)
                        <option value="{{ $r->name }}">{{ $r->label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="edit-is-active" name="is_active" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm
                               focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
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
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-800 mb-1">Hapus Default Task</h3>
            <p class="text-xs sm:text-sm text-gray-500 mb-6">
                Yakin ingin menghapus <span id="delete-task-title" class="font-semibold text-gray-700"></span>?
                Tindakan ini tidak dapat dibatalkan.
            </p>
            <form id="form-delete" action="" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-center gap-2 sm:gap-3">
                    <x-responsive-button variant="secondary" size="sm" type="button"
                        onclick="document.getElementById('modal-delete').classList.add('hidden')">
                        Batal
                    </x-responsive-button>
                    <x-responsive-button variant="danger" size="sm" type="submit">
                        Ya, Hapus
                    </x-responsive-button>
                </div>
            </form>
        </div>
    </x-responsive-modal>

    {{-- ── JavaScript ──────────────────────────────────────── --}}
    <script>
        function filterSelectOptions(query, selectId) {
            const select = document.getElementById(selectId);
            if (!select) return;
            const q = (query || '').toLowerCase().trim();
            const options = select.options;
            for (let i = 0; i < options.length; i++) {
                const opt = options[i];
                if (!opt.value) {
                    opt.hidden = false;
                    continue;
                }
                const text = opt.text.toLowerCase();
                opt.hidden = q ? !text.includes(q) : false;
            }
        }

        function openEditModal(button) {
            const searchInput = document.getElementById('search-edit-target-role');
            if (searchInput) {
                searchInput.value = '';
                filterSelectOptions('', 'edit-target-role');
            }
            const id = button.dataset.id;
            const title = button.dataset.title;
            const description = button.dataset.description;
            const targetRole = button.dataset.targetRole;
            const isActive = button.dataset.isActive;

            document.getElementById('edit-title').value = title;
            document.getElementById('edit-description').value = description;
            document.getElementById('edit-target-role').value = targetRole;
            document.getElementById('edit-is-active').value = isActive;
            document.getElementById('form-edit').action = `/admin/default-tasks/${id}`;
            document.getElementById('modal-edit').classList.remove('hidden');
        }

        function openDeleteModal(button) {
            const id = button.dataset.id;
            const title = button.dataset.title;

            document.getElementById('delete-task-title').textContent = title;
            document.getElementById('form-delete').action = `/admin/default-tasks/${id}`;
            document.getElementById('modal-delete').classList.remove('hidden');
        }
    </script>

@endsection