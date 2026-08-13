@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page-title', 'Manajemen Pengguna')
@section('page-subtitle', 'Kelola akun HR Staff dan HR Assistant')

@section('sidebar')
    @include('components.sidebar-admin')
@endsection

@section('content')

{{-- Header + Tombol Tambah --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <p class="text-sm text-gray-500">
            Total <span class="font-semibold text-gray-700">{{ $users->count() }}</span> pengguna
        </p>
    </div>
    <button onclick="document.getElementById('modal-create').classList.remove('hidden')"
            class="flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700
                   text-white text-sm font-medium rounded-lg transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Pengguna
    </button>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[600px]">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Nama</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Email</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Role</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Status</th>
                <th class="text-right px-6 py-3.5 font-semibold text-gray-600">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition">
                    {{-- Nama --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            {{-- Avatar: foto atau inisial --}}
                            <div class="w-8 h-8 rounded-full overflow-hidden flex-shrink-0">
                                @if($user->image)
                                    <img src="{{ asset('storage/' . $user->image) }}"
                                         alt="{{ $user->name }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-primary-100 flex items-center justify-center">
                                        <span class="text-xs font-semibold text-primary-600">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <span class="font-medium text-gray-800">{{ $user->name }}</span>
                        </div>
                    </td>

                    {{-- Email --}}
                    <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>

                    {{-- Role --}}
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                            {{ match($user->role) {
                                'hr_staff'     => 'bg-blue-50 text-blue-700',
                                'hr_assistant' => 'bg-purple-50 text-purple-700',
                                'cs'           => 'bg-teal-50 text-teal-700',
                                'ob'           => 'bg-orange-50 text-orange-700',
                                default        => 'bg-gray-100 text-gray-600',
                            } }}">
                            {{ match($user->role) {
                                'hr_staff'     => 'HR Staff',
                                'hr_assistant' => 'HR Assistant',
                                'cs'           => 'CS',
                                'ob'           => 'OB',
                                default        => strtoupper($user->role),
                            } }}
                        </span>
                    </td>

                    {{-- Status --}}
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                            {{ $user->is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>

                    {{-- Aksi --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">

                            {{-- Edit --}}
                            <button
                                onclick="openEditModal(
                                    {{ $user->id }},
                                    '{{ addslashes($user->name) }}',
                                    '{{ $user->email }}',
                                    '{{ $user->role }}',
                                    {{ $user->is_active ? 1 : 0 }},
                                    '{{ $user->image ? asset('storage/' . $user->image) : '' }}'
                                )"
                                class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                                title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>

                            {{-- Ganti Password --}}
                            <button
                                onclick="openPasswordModal({{ $user->id }}, '{{ $user->name }}')"
                                class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded-lg transition"
                                title="Ganti Password">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                            </button>

                            {{-- Hapus --}}
                            <button
                                onclick="openDeleteModal({{ $user->id }}, '{{ $user->name }}')"
                                class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">
                        Belum ada pengguna. Tambahkan pengguna baru.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>{{-- ── MODAL TAMBAH ───────────────────────────────────── --}}
<x-responsive-modal id="modal-create">
    <div class="flex items-center justify-between px-3 sm:px-6 py-4 border-b border-gray-200">
        <h3 class="text-base font-semibold text-gray-800">Tambah Pengguna</h3>
        <button onclick="document.getElementById('modal-create').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <form action="{{ route('admin.users.store') }}" method="POST" class="px-3 sm:px-6 py-5 space-y-4">
        @csrf
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" name="password" required minlength="8"
                   class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" required
                   class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Role</label>
            <select name="role" required
                    class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="">-- Pilih Role --</option>
                <option value="hr_staff">HR Staff</option>
                <option value="hr_assistant">HR Assistant</option>
                <option value="cs">CS (Customer Service)</option>
                <option value="ob">OB (Office Boy)</option>
            </select>
        </div>
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="is_active"
                    class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
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
<x-responsive-modal id="modal-edit" class="flex flex-col max-h-[90vh]">
    <div class="flex items-center justify-between px-3 sm:px-6 py-4 border-b border-gray-200 flex-shrink-0">
        <h3 class="text-base font-semibold text-gray-800">Edit Pengguna</h3>
        <button onclick="document.getElementById('modal-edit').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <form id="form-edit" action="" method="POST" enctype="multipart/form-data"
          class="px-3 sm:px-6 py-5 space-y-4 overflow-y-auto flex-1">
        @csrf
        @method('PATCH')

        {{-- Nama --}}
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" id="edit-name" name="name" required maxlength="100"
                   class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm
                          focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>

        {{-- Email --}}
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" id="edit-email" name="email" required maxlength="100"
                   class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm
                          focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>

        {{-- Role --}}
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Role</label>
            <select id="edit-role" name="role" required
                    class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm
                           focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="hr_staff">HR Staff</option>
                <option value="hr_assistant">HR Assistant</option>
                <option value="cs">CS (Customer Service)</option>
                <option value="ob">OB (Office Boy)</option>
            </select>
        </div>

        {{-- Status --}}
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Status</label>
            <select id="edit-status" name="is_active"
                    class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm
                           focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="1">Aktif</option>
                <option value="0">Nonaktif</option>
            </select>
        </div>

        {{-- Foto Profil --}}
        <div x-data="{
                hasPhoto: false,
                removePhoto: false,
                previewUrl: '',
                init() {
                    // Inisialisasi dari JS saat modal dibuka
                },
                handleFile(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    this.previewUrl = URL.createObjectURL(file);
                    this.hasPhoto = true;
                    this.removePhoto = false;
                },
                triggerRemove() {
                    this.removePhoto = true;
                    this.hasPhoto = false;
                    this.previewUrl = '';
                    this.$refs.fileInput.value = '';
                }
            }" id="edit-photo-area">
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5">Foto Profil</label>
            <div class="flex items-start gap-3">
                {{-- Thumbnail --}}
                <div class="w-14 h-14 rounded-xl overflow-hidden border border-gray-200
                            bg-gray-50 flex-shrink-0">
                    <template x-if="hasPhoto && previewUrl">
                        <img :src="previewUrl" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!hasPhoto || !previewUrl">
                        <div id="edit-avatar-initials"
                             class="w-full h-full bg-primary-100 flex items-center justify-center">
                            <span id="edit-avatar-letter"
                                  class="text-lg font-bold text-primary-600"></span>
                        </div>
                    </template>
                </div>

                <div class="flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <label class="cursor-pointer inline-flex items-center gap-1.5 px-2 sm:px-3 py-1 sm:py-1.5
                                      text-xs sm:text-xs font-medium text-primary-600 border border-primary-300
                                      hover:bg-primary-50 rounded-lg transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Pilih Foto
                            <input type="file" name="image" x-ref="fileInput"
                                   accept="image/jpg,image/jpeg,image/png,image/webp"
                                   @change="handleFile($event)" class="hidden">
                        </label>
                        <button type="button" x-show="hasPhoto" @click="triggerRemove()"
                                class="inline-flex items-center gap-1.5 px-2 sm:px-3 py-1 sm:py-1.5 text-xs font-medium
                                       text-red-600 border border-red-200 hover:bg-red-50 rounded-lg transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus Foto
                        </button>
                    </div>
                    <p class="mt-1.5 text-xs text-gray-400">JPG, PNG, WebP. Maks 2 MB.</p>
                    <input type="hidden" name="remove_image" :value="removePhoto ? '1' : '0'">
                </div>
            </div>
            @error('image')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
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

{{-- ── MODAL GANTI PASSWORD ────────────────────────────── --}}
<x-responsive-modal id="modal-password">
    <div class="flex items-center justify-between px-3 sm:px-6 py-4 border-b border-gray-200">
        <div>
            <h3 class="text-base font-semibold text-gray-800">Ganti Password</h3>
            <p id="password-user-name" class="text-xs text-gray-500 mt-0.5"></p>
        </div>
        <button onclick="document.getElementById('modal-password').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <form id="form-password" action="" method="POST" class="px-3 sm:px-6 py-5 space-y-4">
        @csrf
        @method('PATCH')
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Password Baru</label>
            <input type="password" name="password" required minlength="8"
                   class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" required
                   class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        <div class="flex justify-end gap-2 sm:gap-3 pt-2">
            <x-responsive-button variant="secondary" size="sm" type="button"
                    onclick="document.getElementById('modal-password').classList.add('hidden')">
                Batal
            </x-responsive-button>
            <x-responsive-button variant="primary" size="sm" type="submit">
                Ganti Password
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
        <h3 class="text-base font-semibold text-gray-800 mb-1">Hapus Pengguna</h3>
        <p class="text-xs sm:text-sm text-gray-500 mb-6">
            Yakin ingin menghapus <span id="delete-user-name" class="font-semibold text-gray-700"></span>?
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

{{-- ── JavaScript Modal ────────────────────────────────── --}}
<script>
    function openEditModal(id, name, email, role, isActive) {
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-email').value = email;
        document.getElementById('edit-role').value = role;
        document.getElementById('edit-status').value = isActive;
        document.getElementById('form-edit').action = `/admin/users/${id}`;
        document.getElementById('modal-edit').classList.remove('hidden');
    }

    function openPasswordModal(id, name) {
        document.getElementById('password-user-name').textContent = name;
        document.getElementById('form-password').action = `/admin/users/${id}/password`;
        document.getElementById('modal-password').classList.remove('hidden');
    }

    function openDeleteModal(id, name) {
        document.getElementById('delete-user-name').textContent = name;
        document.getElementById('form-delete').action = `/admin/users/${id}`;
        document.getElementById('modal-delete').classList.remove('hidden');
    }
</script>

@endsection