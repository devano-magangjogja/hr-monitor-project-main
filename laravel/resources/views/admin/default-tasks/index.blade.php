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
            <table class="w-full text-xs sm:text-sm min-w-[580px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-3 sm:px-6 py-2.5 sm:py-3.5 font-semibold text-gray-600 w-36 sm:w-44 whitespace-nowrap">Judul</th>
                        <th class="text-left px-3 sm:px-6 py-2.5 sm:py-3.5 font-semibold text-gray-600">Deskripsi</th>
                        <th class="text-left px-3 sm:px-6 py-2.5 sm:py-3.5 font-semibold text-gray-600 w-32 whitespace-nowrap">Target Role</th>
                        <th class="text-left px-3 sm:px-6 py-2.5 sm:py-3.5 font-semibold text-gray-600 w-24 whitespace-nowrap">Status</th>
                        <th class="text-left px-3 sm:px-6 py-2.5 sm:py-3.5 font-semibold text-gray-600 w-28 sm:w-36 whitespace-nowrap">Dibuat Oleh</th>
                        <th class="text-right px-3 sm:px-6 py-2.5 sm:py-3.5 font-semibold text-gray-600 w-20 whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($defaultTasks as $task)
                        <tr class="hover:bg-gray-50 transition">

                            {{-- Judul --}}
                            <td class="px-3 sm:px-6 py-3 sm:py-4">
                                <div class="truncate max-w-[120px] sm:max-w-[160px] font-medium text-gray-800" title="{{ $task->title }}">
                                    {{ $task->title }}
                                </div>
                            </td>

                            {{-- Deskripsi --}}
                            <td class="px-3 sm:px-6 py-3 sm:py-4">
                                <div class="truncate max-w-[180px] sm:max-w-[280px] text-gray-500" title="{{ $task->description ?? '-' }}">
                                    @if($task->description)
                                        {!! linkify(e($task->description)) !!}
                                    @else
                                        -
                                    @endif
                                </div>
                            </td>

                            {{-- Target Role --}}
                            <td class="px-3 sm:px-6 py-3 sm:py-4">
                                @php
                                    $targetRoleModel = $roles->firstWhere('name', $task->target_role);
                                @endphp
                                <span class="inline-flex items-center whitespace-nowrap px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-semibold {{ $targetRoleModel?->badge_class ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $targetRoleModel?->label ?? strtoupper($task->target_role) }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="px-3 sm:px-6 py-3 sm:py-4">
                                <span class="inline-flex items-center whitespace-nowrap px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-medium
                                    {{ $task->is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                    {{ $task->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>

                            {{-- Dibuat Oleh --}}
                            <td class="px-3 sm:px-6 py-3 sm:py-4">
                                <div class="truncate max-w-[90px] sm:max-w-[120px] text-gray-500" title="{{ $task->creator?->name ?? '-' }}">
                                    {{ $task->creator?->name ?? '-' }}
                                </div>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-3 sm:px-6 py-3 sm:py-4">
                                <div class="flex items-center justify-end gap-1.5 sm:gap-2 whitespace-nowrap">
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
                            <td colspan="6" class="px-3 sm:px-6 py-8 sm:py-12 text-center text-gray-400 text-xs sm:text-sm">
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
            class="px-3 sm:px-6 py-5 pt-1 space-y-4 overflow-y-auto flex-1">
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
        document.addEventListener('DOMContentLoaded', () => {
            // Auto-upgrade select elements to have an integrated search dropdown
            const selectsToUpgrade = document.querySelectorAll(
                'select[id="create-target-role"], select[id="edit-target-role"]'
            );
            
            selectsToUpgrade.forEach(select => {
                if (select.dataset.customDropdownInit) return;
                select.dataset.customDropdownInit = "true";

                // Remove the old separate search input block if it exists right before the select
                const prev = select.previousElementSibling;
                if (prev && prev.classList.contains('relative') && prev.querySelector('input')) {
                    prev.remove();
                }

                const wrapper = document.createElement('div');
                wrapper.className = 'relative custom-select-wrapper';
                wrapper.style.zIndex = '10';
                
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm text-left flex justify-between items-center transition ' + 
                                   (select.disabled ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-800 focus:ring-2 focus:ring-primary-500 focus:outline-none');
                
                const label = document.createElement('span');
                label.className = 'truncate block';
                label.textContent = select.options[select.selectedIndex]?.text || '';
                
                button.innerHTML = `<svg class="w-4 h-4 text-gray-500 shrink-0 ml-2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>`;
                button.prepend(label);
                
                const dropdown = document.createElement('div');
                dropdown.className = 'absolute mt-1 z-50 w-full bg-white border border-gray-200 rounded-lg shadow-xl flex flex-col hidden';
                
                const searchBox = document.createElement('div');
                searchBox.className = 'p-2 border-b border-gray-100 sticky top-0 bg-white rounded-t-lg';
                searchBox.innerHTML = `
                    <div class="relative">
                        <input type="text" placeholder="Cari..." class="w-full pl-8 pr-2 py-1.5 text-xs border border-gray-200 rounded-md focus:outline-none focus:ring-1 focus:ring-primary-500 bg-gray-50 transition">
                        <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                `;
                const searchInput = searchBox.querySelector('input');
                
                const list = document.createElement('ul');
                list.className = 'max-h-48 overflow-y-auto p-1';
                
                const renderOptions = (filter = '') => {
                    list.innerHTML = '';
                    let hasMatch = false;
                    Array.from(select.options).forEach(opt => {
                        const text = opt.text;
                        if (filter && !text.toLowerCase().includes(filter.toLowerCase())) return;
                        hasMatch = true;
                        
                        const li = document.createElement('li');
                        li.className = 'px-3 py-1.5 text-sm cursor-pointer rounded-md mb-0.5 transition-colors ' + 
                                       (select.value === opt.value ? 'bg-primary-50 text-primary-700 font-medium' : 'hover:bg-gray-100 text-gray-700');
                        li.textContent = text;
                        li.addEventListener('click', (e) => {
                            e.stopPropagation();
                            select.value = opt.value;
                            label.textContent = text;
                            dropdown.classList.add('hidden');
                            select.dispatchEvent(new Event('change', { bubbles: true }));
                        });
                        list.appendChild(li);
                    });
                    if (!hasMatch) {
                        list.innerHTML = '<li class="px-3 py-2 text-xs text-gray-400 text-center pointer-events-none">Tidak ditemukan</li>';
                    }
                };
                
                searchInput.addEventListener('input', (e) => renderOptions(e.target.value));
                
                dropdown.appendChild(searchBox);
                dropdown.appendChild(list);
                
                wrapper.appendChild(button);
                wrapper.appendChild(dropdown);
                
                select.parentNode.insertBefore(wrapper, select);
                wrapper.appendChild(select);
                select.style.display = 'none';
                
                button.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (select.disabled) return;
                    
                    const isHidden = dropdown.classList.contains('hidden');
                    document.querySelectorAll('.custom-select-wrapper .absolute').forEach(d => d.classList.add('hidden'));
                    document.querySelectorAll('.custom-select-wrapper').forEach(w => {
                        w.style.zIndex = '10';
                        if (w.parentElement) w.parentElement.style.zIndex = '';
                        if (w.parentElement) w.parentElement.style.position = '';
                    });
                    
                    if (isHidden) {
                        wrapper.style.zIndex = '9999';
                        if (wrapper.parentElement) {
                            wrapper.parentElement.style.position = 'relative';
                            wrapper.parentElement.style.zIndex = '9999';
                        }
                        dropdown.classList.remove('hidden');
                        searchInput.value = '';
                        renderOptions();
                        setTimeout(() => searchInput.focus(), 50);
                    }
                });
                
                document.addEventListener('click', (e) => {
                    if (!wrapper.contains(e.target)) {
                        dropdown.classList.add('hidden');
                        wrapper.style.zIndex = '10';
                        if (wrapper.parentElement) {
                            wrapper.parentElement.style.zIndex = '';
                            wrapper.parentElement.style.position = '';
                        }
                    }
                });
                
                select.addEventListener('change', () => {
                    label.textContent = select.options[select.selectedIndex]?.text || '';
                });
                
                const observer = new MutationObserver(() => {
                    if (select.disabled) {
                        button.className = 'w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm text-left flex justify-between items-center transition bg-gray-100 text-gray-400 cursor-not-allowed';
                    } else {
                        button.className = 'w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm text-left flex justify-between items-center transition bg-white text-gray-800 focus:ring-2 focus:ring-primary-500 focus:outline-none';
                    }
                });
                observer.observe(select, { attributes: true, attributeFilter: ['disabled'] });
            });
        });

        // Backward compatibility stubs
        function filterSelectOptions(query, selectId) {}

        function openEditModal(button) {
            const id = button.dataset.id;
            const title = button.dataset.title;
            const description = button.dataset.description;
            const targetRole = button.dataset.targetRole;
            const isActive = button.dataset.isActive;

            document.getElementById('edit-title').value = title;
            document.getElementById('edit-description').value = description;
            
            const targetRoleSelect = document.getElementById('edit-target-role');
            targetRoleSelect.value = targetRole;
            // Update custom select label
            targetRoleSelect.dispatchEvent(new Event('change', { bubbles: true }));
            
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
