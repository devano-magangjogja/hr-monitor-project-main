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
        <button onclick="document.getElementById('modal-create').classList.remove('hidden')"
            class="flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700
                       text-white text-sm font-medium rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Default Task
        </button>
    </div>

    {{-- Tabel --}}
    {{-- Desktop Table (md+) --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hidden md:block">
        <table class="w-full text-sm table-fixed">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-[20%]">Judul</th>
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-[26%]">Deskripsi</th>
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-[13%]">Target Role</th>
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-[16%]">User Terpilih</th>
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-[11%]">Foto Bukti</th>
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-[8%]">Status</th>
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-[10%]">Dibuat Oleh</th>
                    <th class="text-right px-6 py-3.5 font-semibold text-gray-600 w-[6%]">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($defaultTasks as $task)
                    <tr class="hover:bg-gray-50 transition">

                        {{-- Judul --}}
                        <td class="px-6 py-4">
                            <div class="truncate font-medium text-gray-800" title="{{ $task->title }}">
                                {{ $task->title }}
                            </div>
                        </td>

                        {{-- Deskripsi --}}
                        <td class="px-6 py-4">
                            <div class="truncate text-gray-500" title="{{ $task->description ?? '-' }}">
                                @if ($task->description)
                                    {!! linkify(e($task->description)) !!}
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </div>
                        </td>

                        {{-- Target Role --}}
                        <td class="px-6 py-4">
                            @php $targetRoleModel = $roles->firstWhere('name', $task->target_role); @endphp
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $targetRoleModel?->badge_class ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $targetRoleModel?->label ?? strtoupper($task->target_role) }}
                            </span>
                        </td>

                        {{-- User Terpilih --}}
                        <td class="px-6 py-4">
                            @if (empty($task->assigned_user_ids))
                                <span class="text-sm font-medium text-gray-600">Semua Terpilih</span>
                            @else
                                @php
                                    $selectedUserNames = collect($task->assigned_user_ids)
                                        ->map(fn($userId) => $usersById->get($userId)?->name)
                                        ->filter()
                                        ->join(', ');
                                @endphp
                                <div class="truncate text-gray-600" title="{{ $selectedUserNames }}">
                                    {{ $selectedUserNames ?: 'User tidak ditemukan' }}
                                </div>
                            @endif
                        </td>

                        {{-- Foto Bukti --}}
                        <td class="px-6 py-4">
                            @if ($task->proof_requirement === 'required')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Wajib
                                </span>
                            @elseif ($task->proof_requirement === 'optional')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Opsional
                                </span>
                            @else
                                <span class="text-xs text-gray-400 font-medium">Tanpa Foto</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4">
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                {{ $task->is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                {{ $task->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>

                        {{-- Dibuat Oleh --}}
                        <td class="px-6 py-4">
                            <div class="truncate text-gray-500" title="{{ $task->creator?->name ?? '-' }}">
                                {{ $task->creator?->name ?? '-' }}
                            </div>
                        </td>

                        {{-- Aksi --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-1.5">
                                {{-- Tombol Detail --}}
                                <button type="button" onclick="openDefaultTaskDetailModal(this)" data-title="{{ e($task->title) }}"
                                    data-description="{{ e($task->description ?? '') }}"
                                    data-role="{{ e($roles->firstWhere('name', $task->target_role)?->label ?? strtoupper($task->target_role)) }}"
                                    data-status="{{ $task->is_active ? 'Aktif' : 'Nonaktif' }}"
                                    data-proof-requirement="{{ $task->proof_requirement ?? 'none' }}"
                                    data-creator="{{ e($task->creator?->name ?? '-') }}"
                                    data-users="{{ empty($task->assigned_user_ids) ? 'Semua Terpilih' : e(collect($task->assigned_user_ids)->map(fn($id) => $usersById->get($id)?->name)->filter()->join(', ') ?: 'User tidak ditemukan') }}"
                                    class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                                    title="Lihat detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>

                                {{-- Tombol Edit --}}
                                <button type="button" onclick="openEditModal(this)" data-id="{{ $task->id }}"
                                    data-title="{{ $task->title }}" data-description="{{ $task->description ?? '' }}"
                                    data-target-role="{{ $task->target_role }}"
                                    data-is-active="{{ $task->is_active ? 1 : 0 }}"
                                    data-proof-requirement="{{ $task->proof_requirement ?? 'none' }}"
                                    data-assigned-user-ids='@json($task->assigned_user_ids ?? [])'
                                    class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                                    title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>

                                {{-- Tombol Hapus --}}
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
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400 text-sm">
                            Belum ada default task. Tambahkan default task pertama.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Cards (< md) --}}
    <div class="md:hidden space-y-3">
        @forelse($defaultTasks as $task)
            @php $targetRoleModel = $roles->firstWhere('name', $task->target_role); @endphp
            <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                {{-- Header: judul + aksi --}}
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold text-gray-800 text-sm leading-snug break-words">{{ $task->title }}</p>
                        @if ($task->description)
                            <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ strip_tags($task->description) }}</p>
                        @else
                            <p class="text-xs text-gray-300 mt-1 italic">Tidak ada deskripsi</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        {{-- Tombol Detail --}}
                        <button type="button" onclick="openDefaultTaskDetailModal(this)" data-title="{{ e($task->title) }}"
                            data-description="{{ e($task->description ?? '') }}"
                            data-role="{{ e($roles->firstWhere('name', $task->target_role)?->label ?? strtoupper($task->target_role)) }}"
                            data-status="{{ $task->is_active ? 'Aktif' : 'Nonaktif' }}"
                            data-proof-requirement="{{ $task->proof_requirement ?? 'none' }}"
                            data-creator="{{ e($task->creator?->name ?? '-') }}"
                            data-users="{{ empty($task->assigned_user_ids) ? 'Semua Terpilih' : e(collect($task->assigned_user_ids)->map(fn($id) => $usersById->get($id)?->name)->filter()->join(', ') ?: 'User tidak ditemukan') }}"
                            class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                            title="Lihat detail">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>

                        {{-- Tombol Edit --}}
                        <button type="button" onclick="openEditModal(this)" data-id="{{ $task->id }}"
                            data-title="{{ $task->title }}" data-description="{{ $task->description ?? '' }}"
                            data-target-role="{{ $task->target_role }}" data-is-active="{{ $task->is_active ? 1 : 0 }}"
                            data-proof-requirement="{{ $task->proof_requirement ?? 'none' }}"
                            data-assigned-user-ids='@json($task->assigned_user_ids ?? [])'
                            class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                            title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>

                        {{-- Tombol Hapus --}}
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
                </div>

                {{-- Badges: role + status + foto --}}
                <div class="flex flex-wrap items-center gap-1.5 mb-3">
                    <span class="text-[10px] text-gray-500">
                        Role: <span
                            class="font-semibold text-gray-700">{{ $targetRoleModel?->label ?? strtoupper($task->target_role) }}</span>
                    </span>
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium
                    {{ $task->is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                        {{ $task->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                    @if ($task->proof_requirement === 'required')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                            Wajib Foto
                        </span>
                    @elseif ($task->proof_requirement === 'optional')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-50 text-blue-700 border border-blue-200">
                            Foto Opsional
                        </span>
                    @endif
                    @if (empty($task->assigned_user_ids))
                        <span class="text-[10px] text-gray-500">User: <span class="font-semibold text-gray-700">Semua
                                Terpilih</span></span>
                    @else
                        @php
                            $selectedUserNames = collect($task->assigned_user_ids)
                                ->map(fn($userId) => $usersById->get($userId)?->name)
                                ->filter()
                                ->join(', ');
                        @endphp
                        <span class="w-full text-[10px] text-gray-500 break-words">
                            User: <span
                                class="font-semibold text-gray-700">{{ $selectedUserNames ?: 'User tidak ditemukan' }}</span>
                        </span>
                    @endif
                </div>

                {{-- Footer: dibuat oleh --}}
                <div class="pt-2 border-t border-gray-100 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="text-[11px] text-gray-400">{{ $task->creator?->name ?? '-' }}</span>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-200 px-6 py-12 text-center">
                <p class="text-sm text-gray-400">Belum ada default task. Tambahkan default task pertama.</p>
            </div>
        @endforelse
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
                <input type="text" name="title" value="{{ old('title') }}" required maxlength="150"
                    class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm
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
                <select name="target_role" id="create-target-role" required
                    class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm
                   focus:outline-none focus:ring-2 focus:ring-primary-500"
                    onchange="filterUsersByRole('create')">
                    <option value="">-- Pilih Role --</option>
                    @foreach ($roles as $r)
                        <option value="{{ $r->name }}">{{ $r->label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Pilihan User --}}
            <div id="create-user-section" class="hidden">
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">
                    Diberikan kepada
                </label>

                <div class="space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="assign_type" value="all" checked
                            class="text-primary-600 focus:ring-primary-500"
                            onchange="toggleUserSelect('create', this.value)">
                        <span class="text-sm text-gray-700">Semua anggota role ini</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="assign_type" value="specific"
                            class="text-primary-600 focus:ring-primary-500"
                            onchange="toggleUserSelect('create', this.value)">
                        <span class="text-sm text-gray-700">User tertentu saja</span>
                    </label>
                </div>

                <div id="create-user-select-wrapper" class="mt-3 hidden">
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="px-3 py-2 bg-gray-50 border-b border-gray-200">
                            <input type="text" id="create-user-search" placeholder="Cari nama user..."
                                class="w-full px-3 py-1.5 text-sm border border-gray-200 rounded-md focus:outline-none focus:ring-1 focus:ring-primary-500 bg-white"
                                oninput="filterUserCheckboxes('create', this.value)">
                        </div>
                        <div id="create-user-list" class="max-h-48 overflow-y-auto p-2 space-y-1">
                            {{-- diisi via JS --}}
                        </div>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-1.5">Centang user yang ingin diberi tugas ini.</p>
                </div>
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="is_active"
                    class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm
                               focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5">Ketentuan Lampiran Foto Bukti</label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <label class="flex items-center gap-2 p-2.5 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50/40">
                        <input type="radio" name="proof_requirement" value="none" checked
                            class="text-primary-600 focus:ring-primary-500">
                        <span class="text-xs sm:text-sm text-gray-700">Tanpa Foto</span>
                    </label>
                    <label class="flex items-center gap-2 p-2.5 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50/40">
                        <input type="radio" name="proof_requirement" value="optional"
                            class="text-primary-600 focus:ring-primary-500">
                        <span class="text-xs sm:text-sm text-gray-700">Foto Opsional</span>
                    </label>
                    <label class="flex items-center gap-2 p-2.5 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition has-[:checked]:border-rose-400 has-[:checked]:bg-rose-50/40">
                        <input type="radio" name="proof_requirement" value="required"
                            class="text-rose-600 focus:ring-rose-500">
                        <span class="text-xs sm:text-sm font-medium text-rose-700">Wajib Foto Bukti</span>
                    </label>
                </div>
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
        <form id="form-edit" action="" method="POST"
            class="px-3 sm:px-6 py-5 space-y-4 pt-1 overflow-y-auto flex-1">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Judul Tugas</label>
                <input type="text" id="edit-title" name="title" required maxlength="150"
                    class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm
                              focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">
                    Deskripsi/Catatan <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <textarea id="edit-description" name="description" rows="3"
                    class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm
                                 focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"></textarea>
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Target Role</label>
                <select id="edit-target-role" name="target_role" required
                    class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm
                   focus:outline-none focus:ring-2 focus:ring-primary-500"
                    onchange="filterUsersByRole('edit')">
                    @foreach ($roles as $r)
                        <option value="{{ $r->name }}">{{ $r->label }}</option>
                    @endforeach
                </select>
            </div>

            <div id="edit-user-section">
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Diberikan kepada</label>

                <div class="space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="assign_type" value="all" id="edit-assign-all"
                            class="text-primary-600 focus:ring-primary-500"
                            onchange="toggleUserSelect('edit', this.value)">
                        <span class="text-sm text-gray-700">Semua anggota role ini</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="assign_type" value="specific" id="edit-assign-specific"
                            class="text-primary-600 focus:ring-primary-500"
                            onchange="toggleUserSelect('edit', this.value)">
                        <span class="text-sm text-gray-700">User tertentu saja</span>
                    </label>
                </div>

                <div id="edit-user-select-wrapper" class="mt-3 hidden">
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="px-3 py-2 bg-gray-50 border-b border-gray-200">
                            <input type="text" id="edit-user-search" placeholder="Cari nama user..."
                                class="w-full px-3 py-1.5 text-sm border border-gray-200 rounded-md focus:outline-none focus:ring-1 focus:ring-primary-500 bg-white"
                                oninput="filterUserCheckboxes('edit', this.value)">
                        </div>
                        <div id="edit-user-list" class="max-h-48 overflow-y-auto p-2 space-y-1">
                            {{-- diisi via JS --}}
                        </div>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-1.5">Centang user yang ingin diberi tugas ini.</p>
                </div>
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="edit-is-active" name="is_active"
                    class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm
                               focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5">Ketentuan Lampiran Foto Bukti</label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <label class="flex items-center gap-2 p-2.5 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50/40">
                        <input type="radio" name="proof_requirement" value="none" id="edit-proof-none"
                            class="text-primary-600 focus:ring-primary-500">
                        <span class="text-xs sm:text-sm text-gray-700">Tanpa Foto</span>
                    </label>
                    <label class="flex items-center gap-2 p-2.5 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50/40">
                        <input type="radio" name="proof_requirement" value="optional" id="edit-proof-optional"
                            class="text-primary-600 focus:ring-primary-500">
                        <span class="text-xs sm:text-sm text-gray-700">Foto Opsional</span>
                    </label>
                    <label class="flex items-center gap-2 p-2.5 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition has-[:checked]:border-rose-400 has-[:checked]:bg-rose-50/40">
                        <input type="radio" name="proof_requirement" value="required" id="edit-proof-required"
                            class="text-rose-600 focus:ring-rose-500">
                        <span class="text-xs sm:text-sm font-medium text-rose-700">Wajib Foto Bukti</span>
                    </label>
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

    {{-- ── MODAL DETAIL ──────────────────────────────────── --}}
    <x-responsive-modal id="modal-default-task-detail" class="max-w-lg">
        <div class="flex items-center justify-between px-5 sm:px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-800">Detail Default Task</h3>
            <button type="button" onclick="document.getElementById('modal-default-task-detail').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="px-5 sm:px-6 py-5 space-y-4">
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Judul</p>
                <p id="detail-default-title" class="text-base font-semibold text-gray-800 break-words"></p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Target Role</p>
                    <p id="detail-default-role" class="text-sm text-gray-700"></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Status</p>
                    <p id="detail-default-status" class="text-sm text-gray-700"></p>
                </div>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Ketentuan Foto Bukti</p>
                <div id="detail-default-proof"></div>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">User Terpilih</p>
                <p id="detail-default-users" class="text-sm text-gray-700 break-words"></p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Deskripsi</p>
                <p id="detail-default-description" class="text-sm text-gray-600 whitespace-pre-line break-words"></p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Dibuat Oleh</p>
                <p id="detail-default-creator" class="text-sm text-gray-700"></p>
            </div>
        </div>
    </x-responsive-modal>

    {{-- ── JavaScript ──────────────────────────────────────── --}}
    <script>
        function openDefaultTaskDetailModal(btn) {
            const title = btn.getAttribute('data-title') || '-';
            const description = btn.getAttribute('data-description') || 'Tidak ada deskripsi';
            const role = btn.getAttribute('data-role') || '-';
            const status = btn.getAttribute('data-status') || '-';
            const proof = btn.getAttribute('data-proof-requirement') || 'none';
            const users = btn.getAttribute('data-users') || '-';
            const creator = btn.getAttribute('data-creator') || '-';

            document.getElementById('detail-default-title').textContent = title;
            document.getElementById('detail-default-description').textContent = description;
            document.getElementById('detail-default-role').textContent = role;
            document.getElementById('detail-default-status').textContent = status;

            const proofEl = document.getElementById('detail-default-proof');
            if (proof === 'required') {
                proofEl.innerHTML = '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">Wajib Foto Bukti</span>';
            } else if (proof === 'optional') {
                proofEl.innerHTML = '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">Foto Opsional</span>';
            } else {
                proofEl.innerHTML = '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">Tanpa Foto Bukti</span>';
            }

            document.getElementById('detail-default-users').textContent = users;
            document.getElementById('detail-default-creator').textContent = creator;

            document.getElementById('modal-default-task-detail').classList.remove('hidden');
        }

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
                button.className =
                    'w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm text-left flex justify-between items-center transition ' +
                    (select.disabled ? 'bg-gray-100 text-gray-400 cursor-not-allowed' :
                        'bg-white text-gray-800 focus:ring-2 focus:ring-primary-500 focus:outline-none');

                const label = document.createElement('span');
                label.className = 'truncate block';
                label.textContent = select.options[select.selectedIndex]?.text || '';

                button.innerHTML =
                    `<svg class="w-4 h-4 text-gray-500 shrink-0 ml-2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>`;
                button.prepend(label);

                const dropdown = document.createElement('div');
                dropdown.className =
                    'absolute mt-1 z-50 w-full bg-white border border-gray-200 rounded-lg shadow-xl flex flex-col hidden';

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
                        if (filter && !text.toLowerCase().includes(filter.toLowerCase()))
                            return;
                        hasMatch = true;

                        const li = document.createElement('li');
                        li.className =
                            'px-3 py-1.5 text-sm cursor-pointer rounded-md mb-0.5 transition-colors ' +
                            (select.value === opt.value ?
                                'bg-primary-50 text-primary-700 font-medium' :
                                'hover:bg-gray-100 text-gray-700');
                        li.textContent = text;
                        li.addEventListener('click', (e) => {
                            e.stopPropagation();
                            select.value = opt.value;
                            label.textContent = text;
                            dropdown.classList.add('hidden');
                            select.dispatchEvent(new Event('change', {
                                bubbles: true
                            }));
                        });
                        list.appendChild(li);
                    });
                    if (!hasMatch) {
                        list.innerHTML =
                            '<li class="px-3 py-2 text-xs text-gray-400 text-center pointer-events-none">Tidak ditemukan</li>';
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
                    document.querySelectorAll('.custom-select-wrapper .absolute').forEach(d => d
                        .classList.add('hidden'));
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
                        button.className =
                            'w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm text-left flex justify-between items-center transition bg-gray-100 text-gray-400 cursor-not-allowed';
                    } else {
                        button.className =
                            'w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm text-left flex justify-between items-center transition bg-white text-gray-800 focus:ring-2 focus:ring-primary-500 focus:outline-none';
                    }
                });
                observer.observe(select, {
                    attributes: true,
                    attributeFilter: ['disabled']
                });
            });
        });

        // Data user per role dari controller
        const usersByRole = @json(
            $usersByRole->mapWithKeys(function ($users, $role) {
                return [$role => $users->map(fn($u) => ['id' => $u->id, 'name' => $u->name])];
            }));

        function filterUsersByRole(prefix) {
            const roleSelect = document.getElementById(prefix + '-target-role');
            const role = roleSelect.value;
            const userSection = document.getElementById(prefix + '-user-section');
            const userList = document.getElementById(prefix + '-user-list');

            if (!role) {
                userSection.classList.add('hidden');
                return;
            }

            userSection.classList.remove('hidden');

            // Render checkbox list
            userList.innerHTML = '';
            const users = usersByRole[role] || [];

            if (users.length === 0) {
                userList.innerHTML = '<p class="text-xs text-gray-400 text-center py-3">Tidak ada user di role ini</p>';
                return;
            }

            users.forEach(u => {
                const label = document.createElement('label');
                label.className =
                    'flex items-center gap-2.5 px-2 py-2 rounded-md hover:bg-gray-50 cursor-pointer transition user-checkbox-item';
                label.dataset.name = u.name.toLowerCase();

                label.innerHTML = `
            <input type="checkbox" name="assigned_user_ids[]" value="${u.id}"
                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
            <span class="text-sm text-gray-700">${u.name}</span>
        `;
                userList.appendChild(label);
            });
        }

        function toggleUserSelect(prefix, type) {
            const wrapper = document.getElementById(prefix + '-user-select-wrapper');
            if (type === 'specific') {
                wrapper.classList.remove('hidden');
            } else {
                wrapper.classList.add('hidden');
                // Uncheck semua
                const checkboxes = document.querySelectorAll(`#${prefix}-user-list input[type="checkbox"]`);
                checkboxes.forEach(cb => cb.checked = false);
            }
        }

        function filterUserCheckboxes(prefix, query) {
            const items = document.querySelectorAll(`#${prefix}-user-list .user-checkbox-item`);
            const q = query.toLowerCase().trim();

            items.forEach(item => {
                const name = item.dataset.name || '';
                item.style.display = name.includes(q) ? '' : 'none';
            });
        }

        function openEditModal(button) {
            const id = button.dataset.id;
            const title = button.dataset.title;
            const description = button.dataset.description;
            const targetRole = button.dataset.targetRole;
            const isActive = button.dataset.isActive;
            const assignedUserIds = JSON.parse(button.dataset.assignedUserIds || '[]');

            document.getElementById('edit-title').value = title;
            document.getElementById('edit-description').value = description;

            const targetRoleSelect = document.getElementById('edit-target-role');
            targetRoleSelect.value = targetRole;
            targetRoleSelect.dispatchEvent(new Event('change', {
                bubbles: true
            }));

            // Render list user sesuai role
            filterUsersByRole('edit');

            // Set radio + centang user yang sudah dipilih
            if (assignedUserIds.length > 0) {
                document.getElementById('edit-assign-specific').checked = true;
                toggleUserSelect('edit', 'specific');

                // Centang checkbox yang sesuai
                setTimeout(() => {
                    const checkboxes = document.querySelectorAll('#edit-user-list input[type="checkbox"]');
                    checkboxes.forEach(cb => {
                        cb.checked = assignedUserIds.includes(parseInt(cb.value));
                    });
                }, 50);
            } else {
                document.getElementById('edit-assign-all').checked = true;
                toggleUserSelect('edit', 'all');
            }

            document.getElementById('edit-is-active').value = isActive;

            const proofRequirement = button.dataset.proofRequirement || 'none';
            const proofRadio = document.querySelector(`input[name="proof_requirement"][value="${proofRequirement}"][id^="edit-proof"]`);
            if (proofRadio) proofRadio.checked = true;

            document.getElementById('form-edit').action = `/admin/default-tasks/${id}`;
            document.getElementById('modal-edit').classList.remove('hidden');
        }

        // Backward compatibility stubs
        function filterSelectOptions(query, selectId) {}

        function openDeleteModal(button) {
            const id = button.dataset.id;
            const title = button.dataset.title;

            document.getElementById('delete-task-title').textContent = title;
            document.getElementById('form-delete').action = `/admin/default-tasks/${id}`;
            document.getElementById('modal-delete').classList.remove('hidden');
        }
    </script>

@endsection
