@extends('layouts.app')

@section('title', 'Manajemen HR Assistant')
@section('page-title', 'Manajemen HR Assistant')
@section('page-subtitle', 'Kelola akun HR Assistant yang ada di bawah pengawasan Anda')

@section('sidebar')
    @include('components.sidebar-staff')
@endsection

@section('content')

{{-- Header + Tombol Tambah --}}
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">
        Total <span class="font-semibold text-gray-700">{{ $users->count() }}</span> HR Assistant
    </p>
    <button onclick="document.getElementById('modal-create').classList.remove('hidden')"
            class="flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700
                   text-white text-sm font-medium rounded-lg transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah HR Assistant
    </button>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[500px]">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Nama</th>
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Email</th>
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
                                <div class="w-8 h-8 rounded-full overflow-hidden flex-shrink-0">
                                    @if($user->image)
                                        <img src="{{ asset('storage/' . $user->image) }}"
                                             alt="{{ $user->name }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-purple-100 flex items-center justify-center">
                                            <span class="text-xs font-semibold text-purple-600">
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
                                    onclick="openPasswordModal({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                    class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded-lg transition"
                                    title="Ganti Password">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                    </svg>
                                </button>

                                {{-- Hapus --}}
                                <button
                                    onclick="openDeleteModal({{ $user->id }}, '{{ addslashes($user->name) }}')"
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
                        <td colspan="4" class="px-6 py-12 text-center text-gray-400 text-sm">
                            Belum ada HR Assistant. Tambahkan akun baru.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ── MODAL TAMBAH ───────────────────────────────────── --}}
<div id="modal-create"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-800">Tambah HR Assistant</h3>
            <button onclick="document.getElementById('modal-create').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form action="{{ route('staff.users.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required maxlength="100"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                              focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required maxlength="100"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                              focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div x-data="{ show: false }">
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-sm font-medium text-gray-700">Password <span class="text-red-500">*</span></label>
                    <span id="create-pwd-len-msg" class="text-[11px] text-gray-400 font-medium">Min. 8 karakter</span>
                </div>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" id="create-password" name="password" required minlength="8"
                           placeholder="Minimal 8 karakter"
                           class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-lg text-sm transition
                                  focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <button type="button" @click="show = !show"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition"
                            title="Tampilkan / Sembunyikan Password">
                        <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div x-data="{ show: false }">
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-sm font-medium text-gray-700">Konfirmasi Password <span class="text-red-500">*</span></label>
                    <span id="create-pwd-match-msg" class="text-[11px] font-semibold hidden"></span>
                </div>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" id="create-password-confirmation" name="password_confirmation" required
                           placeholder="Ulangi password di atas"
                           class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-lg text-sm transition
                                  focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <button type="button" @click="show = !show"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition"
                            title="Tampilkan / Sembunyikan Password">
                        <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button"
                        onclick="document.getElementById('modal-create').classList.add('hidden')"
                        class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── MODAL EDIT ─────────────────────────────────────── --}}
<div id="modal-edit"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md flex flex-col max-h-[90vh]">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 flex-shrink-0">
            <h3 class="text-base font-semibold text-gray-800">Edit HR Assistant</h3>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="form-edit" action="" method="POST" enctype="multipart/form-data"
              class="px-6 py-5 space-y-4 overflow-y-auto flex-1">
            @csrf
            @method('PATCH')

            {{-- Nama --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" id="edit-name" name="name" required maxlength="100"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                              focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="edit-email" name="email" required maxlength="100"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                              focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="edit-status" name="is_active"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
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
                }">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Foto Profil</label>
                <div class="flex items-start gap-3">
                    <div class="w-14 h-14 rounded-xl overflow-hidden border border-gray-200 bg-gray-50 flex-shrink-0">
                        <template x-if="hasPhoto && previewUrl">
                            <img :src="previewUrl" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!hasPhoto || !previewUrl">
                            <div id="edit-avatar-wrap"
                                 class="w-full h-full bg-purple-100 flex items-center justify-center">
                                <span id="edit-avatar-letter"
                                      class="text-lg font-bold text-purple-600"></span>
                            </div>
                        </template>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5
                                          text-xs font-medium text-primary-600 border border-primary-300
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
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium
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
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button"
                        onclick="document.getElementById('modal-edit').classList.add('hidden')"
                        class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── MODAL GANTI PASSWORD ────────────────────────────── --}}
<div id="modal-password"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
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
        <form id="form-password" action="" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            @method('PATCH')
            <div x-data="{ show: false }">
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-sm font-medium text-gray-700">Password Baru <span class="text-red-500">*</span></label>
                    <span id="reset-pwd-len-msg" class="text-[11px] text-gray-400 font-medium">Min. 8 karakter</span>
                </div>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" id="reset-password" name="password" required minlength="8"
                           placeholder="Minimal 8 karakter"
                           class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-lg text-sm transition
                                  focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <button type="button" @click="show = !show"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition"
                            title="Tampilkan / Sembunyikan Password">
                        <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div x-data="{ show: false }">
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru <span class="text-red-500">*</span></label>
                    <span id="reset-pwd-match-msg" class="text-[11px] font-semibold hidden"></span>
                </div>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" id="reset-password-confirmation" name="password_confirmation" required
                           placeholder="Ulangi password baru"
                           class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-lg text-sm transition
                                  focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <button type="button" @click="show = !show"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition"
                            title="Tampilkan / Sembunyikan Password">
                        <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button"
                        onclick="document.getElementById('modal-password').classList.add('hidden')"
                        class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg transition">
                    Ganti Password
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── MODAL HAPUS ─────────────────────────────────────── --}}
<div id="modal-delete"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm">
        <div class="px-6 py-5 text-center">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-800 mb-1">Hapus HR Assistant</h3>
            <p class="text-sm text-gray-500 mb-6">
                Yakin ingin menghapus akun
                <span id="delete-user-name" class="font-semibold text-gray-700"></span>?
                Tindakan ini tidak dapat dibatalkan.
            </p>
            <form id="form-delete" action="" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-center gap-3">
                    <button type="button"
                            onclick="document.getElementById('modal-delete').classList.add('hidden')"
                            class="px-5 py-2 text-sm text-gray-600 font-medium border border-gray-300 rounded-lg hover:bg-gray-50 transition">
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

@endsection

@push('scripts')
<script>
    function setupPasswordMatchValidation(pwdInputId, confirmInputId, matchMsgId, lenMsgId, formEl) {
        const pwdInput = document.getElementById(pwdInputId);
        const confirmInput = document.getElementById(confirmInputId);
        const matchMsg = document.getElementById(matchMsgId);
        const lenMsg = lenMsgId ? document.getElementById(lenMsgId) : null;

        if (!pwdInput || !confirmInput || !matchMsg) return;

        function validate() {
            const pwd = pwdInput.value;
            const confirm = confirmInput.value;

            // Indikator panjang password
            if (lenMsg) {
                if (pwd.length > 0 && pwd.length < 8) {
                    lenMsg.textContent = `${pwd.length}/8 karakter (Kurang ${8 - pwd.length})`;
                    lenMsg.className = 'text-[11px] text-amber-600 font-semibold';
                } else if (pwd.length >= 8) {
                    lenMsg.textContent = '✓ Panjang cukup';
                    lenMsg.className = 'text-[11px] text-emerald-600 font-semibold';
                } else {
                    lenMsg.textContent = 'Min. 8 karakter';
                    lenMsg.className = 'text-[11px] text-gray-400 font-medium';
                }
            }

            // Indikator pencocokan password
            if (confirm.length === 0) {
                matchMsg.classList.add('hidden');
                confirmInput.classList.remove('border-red-500', 'border-emerald-500', 'focus:ring-red-500', 'focus:ring-emerald-500');
                confirmInput.classList.add('border-gray-300');
                return true;
            }

            matchMsg.classList.remove('hidden');

            if (pwd === confirm) {
                matchMsg.innerHTML = '<span class="inline-flex items-center gap-1 text-emerald-600 font-semibold"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Password cocok</span>';
                confirmInput.classList.remove('border-gray-300', 'border-red-500', 'focus:ring-red-500');
                confirmInput.classList.add('border-emerald-500', 'focus:ring-emerald-500');
                return true;
            } else {
                matchMsg.innerHTML = '<span class="inline-flex items-center gap-1 text-red-600 font-semibold"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg> Password tidak cocok</span>';
                confirmInput.classList.remove('border-gray-300', 'border-emerald-500', 'focus:ring-emerald-500');
                confirmInput.classList.add('border-red-500', 'focus:ring-red-500');
                return false;
            }
        }

        pwdInput.addEventListener('input', validate);
        confirmInput.addEventListener('input', validate);

        if (formEl) {
            formEl.addEventListener('submit', function(e) {
                if (pwdInput.value !== confirmInput.value) {
                    e.preventDefault();
                    validate();
                    confirmInput.focus();
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const createForm = document.querySelector('#modal-create form');
        setupPasswordMatchValidation('create-password', 'create-password-confirmation', 'create-pwd-match-msg', 'create-pwd-len-msg', createForm);

        const resetForm = document.getElementById('form-password');
        setupPasswordMatchValidation('reset-password', 'reset-password-confirmation', 'reset-pwd-match-msg', 'reset-pwd-len-msg', resetForm);
    });

    function openEditModal(id, name, email, isActive, imageUrl) {
        document.getElementById('edit-name').value   = name;
        document.getElementById('edit-email').value  = email;
        document.getElementById('edit-status').value = isActive;
        document.getElementById('edit-avatar-letter').textContent = name.charAt(0).toUpperCase();
        document.getElementById('form-edit').action  = `/staff/users/${id}`;
        document.getElementById('modal-edit').classList.remove('hidden');
    }

    function openPasswordModal(id, name) {
        document.getElementById('password-user-name').textContent = name;
        document.getElementById('reset-password').value = '';
        document.getElementById('reset-password-confirmation').value = '';
        const matchMsg = document.getElementById('reset-pwd-match-msg');
        if (matchMsg) matchMsg.classList.add('hidden');
        const lenMsg = document.getElementById('reset-pwd-len-msg');
        if (lenMsg) {
            lenMsg.textContent = 'Min. 8 karakter';
            lenMsg.className = 'text-[11px] text-gray-400 font-medium';
        }
        const confirmInput = document.getElementById('reset-password-confirmation');
        if (confirmInput) {
            confirmInput.classList.remove('border-red-500', 'border-emerald-500', 'focus:ring-red-500', 'focus:ring-emerald-500');
            confirmInput.classList.add('border-gray-300');
        }
        document.getElementById('form-password').action = `/staff/users/${id}/password`;
        document.getElementById('modal-password').classList.remove('hidden');
    }

    function openDeleteModal(id, name) {
        document.getElementById('delete-user-name').textContent = name;
        document.getElementById('form-delete').action = `/staff/users/${id}`;
        document.getElementById('modal-delete').classList.remove('hidden');
    }
</script>
@endpush
