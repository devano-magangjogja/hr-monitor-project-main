@extends('layouts.app')

@section('title', 'Manajemen Role')
@section('page-title', 'Manajemen Role')
@section('page-subtitle', 'Kelola daftar role dan tentukan template hak akses tampilan')

@section('sidebar')
    @include('components.sidebar-admin')
@endsection

@section('content')

    {{-- Alert Notifikasi --}}
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
            <p class="font-semibold mb-1">Terjadi kesalahan input:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Header Action --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <p class="text-sm text-gray-500">
                Total terdaftar: <span class="font-bold text-gray-800">{{ $roles->count() }}</span> role
            </p>
        </div>
        <button onclick="document.getElementById('modal-create-role').classList.remove('hidden')"
            class="flex items-center justify-center gap-2 px-4 py-2.5 bg-primary-600 hover:bg-primary-700
                   text-white text-sm font-medium rounded-xl shadow-sm hover:shadow transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Role Baru
        </button>
    </div>

    {{-- Tabel Role --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4">Nama Role & Kode</th>
                        <th class="px-6 py-4">Tipe Peran / Template Tampilan</th>
                        <th class="px-6 py-4 text-center">Pengguna Aktif</th>
                        <th class="px-6 py-4 text-center">Tipe Sistem</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($roles as $role)
                        <tr class="hover:bg-gray-50/80 transition"
                            data-role="{{ json_encode([
                                'id' => $role->id,
                                'name' => $role->name,
                                'label' => $role->label,
                                'base_type' => $role->base_type,
                                'badge_class' => $role->badge_class,
                                'is_system' => $role->is_system,
                            ]) }}">
                            {{-- Nama Role & Badge --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2.5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $role->badge_class }}">
                                        {{ $role->label }}
                                    </span>
                                    <span class="text-xs text-gray-400 font-mono bg-gray-100 px-1.5 py-0.5 rounded">
                                        {{ $role->name }}
                                    </span>
                                </div>
                            </td>

                            {{-- Base Type / Template --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    @if($role->base_type === 'admin')
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-red-700 bg-red-50 border border-red-200 px-2 py-0.5 rounded-md">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                            </svg>
                                            Administrator
                                        </span>
                                    @elseif($role->base_type === 'staff')
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-blue-700 bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-md">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            Staff / Koordinator
                                        </span>
                                    @elseif($role->base_type === 'assistant')
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-purple-700 bg-purple-50 border border-purple-200 px-2 py-0.5 rounded-md">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            Asisten
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-teal-700 bg-teal-50 border border-teal-200 px-2 py-0.5 rounded-md">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            Anggota / Mandiri (seperti CS, VG)
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Jumlah Pengguna --}}
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center min-w-[28px] h-7 px-2 text-xs font-bold rounded-full bg-gray-100 text-gray-700">
                                    {{ $role->users_count }}
                                </span>
                            </td>

                            {{-- Tipe Sistem --}}
                            <td class="px-6 py-4 text-center">
                                @if($role->is_system)
                                    <span class="text-xs font-semibold text-gray-400 bg-gray-100 px-2 py-0.5 rounded">
                                        Bawaan Sistem
                                    </span>
                                @else
                                    <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                        Kustom
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button onclick="openEditRoleModal(JSON.parse(this.closest('tr').dataset.role))"
                                        class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                                        title="Edit Role">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    @if(!$role->is_system)
                                        <button onclick="openDeleteRoleModal({{ $role->id }}, '{{ addslashes($role->label) }}', {{ $role->users_count }})"
                                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                            title="Hapus Role">
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
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">
                                Belum ada role yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL TAMBAH ROLE --}}
    <div id="modal-create-role" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 overflow-y-auto p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg my-8">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Tambah Role Baru</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Tentukan nama role dan template peran antarmukanya</p>
                </div>
                <button onclick="document.getElementById('modal-create-role').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.roles.store') }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                {{-- Nama Role --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Role (Tampilan) <span class="text-red-500">*</span></label>
                    <input type="text" name="label" id="create-role-label" required placeholder="Contoh: Digital Marketing, Copywriter, Trainer"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
                        onkeyup="autoGenerateSlug(this.value)">
                </div>

                {{-- Kode Slug --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode / Identifier Role (Slug) <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="create-role-name" required placeholder="digital_marketing"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm font-mono text-gray-600 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <p class="text-[11px] text-gray-400 mt-1">Hanya huruf kecil, angka, dan garis bawah (_).</p>
                </div>

                {{-- Template / Tipe Peran --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Peran & Template Tampilan <span class="text-red-500">*</span></label>
                    <div class="space-y-2.5">
                        @foreach($baseTypeOptions as $key => $opt)
                            <label class="flex items-start gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-primary-50/30 hover:border-primary-300 transition">
                                <input type="radio" name="base_type" value="{{ $key }}" {{ $loop->first ? 'checked' : '' }} class="mt-1 text-primary-600 focus:ring-primary-500">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">{{ $opt['label'] }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $opt['description'] }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Warna Badge --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Warna Badge Tampilan</label>
                    <select name="badge_class" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                        @foreach($colorPresets as $badgeClass => $colorName)
                            <option value="{{ $badgeClass }}">{{ $colorName }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-gray-100">
                    <button type="button" onclick="document.getElementById('modal-create-role').classList.add('hidden')"
                        class="px-4 py-2.5 text-sm text-gray-600 hover:text-gray-800 font-medium">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl transition shadow-sm">
                        Simpan Role
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT ROLE --}}
    <div id="modal-edit-role" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 overflow-y-auto p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg my-8">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Edit Role</h3>
                    <p id="edit-role-subtitle" class="text-xs text-gray-400 mt-0.5"></p>
                </div>
                <button onclick="document.getElementById('modal-edit-role').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form id="form-edit-role" action="" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                @method('PATCH')
                
                {{-- Nama Role --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Role (Tampilan) <span class="text-red-500">*</span></label>
                    <input type="text" name="label" id="edit-role-label" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                {{-- Template / Tipe Peran (Hanya jika bukan sistem) --}}
                <div id="edit-base-type-wrapper">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Peran & Template Tampilan</label>
                    <div class="space-y-2.5">
                        @foreach($baseTypeOptions as $key => $opt)
                            <label class="flex items-start gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-primary-50/30 hover:border-primary-300 transition">
                                <input type="radio" name="base_type" id="edit-basetype-{{ $key }}" value="{{ $key }}" class="mt-1 text-primary-600 focus:ring-primary-500">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">{{ $opt['label'] }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $opt['description'] }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Warna Badge --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Warna Badge Tampilan</label>
                    <select name="badge_class" id="edit-role-badge" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                        @foreach($colorPresets as $badgeClass => $colorName)
                            <option value="{{ $badgeClass }}">{{ $colorName }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-gray-100">
                    <button type="button" onclick="document.getElementById('modal-edit-role').classList.add('hidden')"
                        class="px-4 py-2.5 text-sm text-gray-600 hover:text-gray-800 font-medium">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl transition shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL HAPUS ROLE --}}
    <div id="modal-delete-role" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4">
            <div class="px-6 py-6 text-center">
                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-800 mb-1">Hapus Role</h3>
                <p class="text-sm text-gray-500 mb-6">
                    Yakin ingin menghapus role <span id="delete-role-name" class="font-bold text-gray-700"></span>?
                </p>
                <form id="form-delete-role" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="flex justify-center gap-3">
                        <button type="button" onclick="document.getElementById('modal-delete-role').classList.add('hidden')"
                            class="px-5 py-2 text-sm text-gray-600 font-medium border border-gray-300 rounded-xl hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl transition">
                            Ya, Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function autoGenerateSlug(text) {
            const slug = text.toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .trim()
                .replace(/[\s_-]+/g, '_');
            document.getElementById('create-role-name').value = slug;
        }

        function openEditRoleModal(role) {
            document.getElementById('edit-role-label').value = role.label;
            document.getElementById('edit-role-subtitle').textContent = `Kode: ${role.name}`;
            document.getElementById('edit-role-badge').value = role.badge_class;

            const radio = document.getElementById(`edit-basetype-${role.base_type}`);
            if (radio) {
                radio.checked = true;
            }

            const baseTypeWrapper = document.getElementById('edit-base-type-wrapper');
            if (role.is_system) {
                baseTypeWrapper.classList.add('opacity-50', 'pointer-events-none');
            } else {
                baseTypeWrapper.classList.remove('opacity-50', 'pointer-events-none');
            }

            document.getElementById('form-edit-role').action = `/admin/roles/${role.id}`;
            document.getElementById('modal-edit-role').classList.remove('hidden');
        }

        function openDeleteRoleModal(id, label, usersCount) {
            if (usersCount > 0) {
                alert(`Role '${label}' tidak dapat dihapus karena masih digunakan oleh ${usersCount} pengguna.`);
                return;
            }
            document.getElementById('delete-role-name').textContent = label;
            document.getElementById('form-delete-role').action = `/admin/roles/${id}`;
            document.getElementById('modal-delete-role').classList.remove('hidden');
        }
    </script>

@endsection
