@extends('layouts.app')

@section('title', 'Manajemen Akun')
@section('page-title', 'Manajemen Akun')
@section('page-subtitle', 'Kelola daftar akun sosial media dan kredensial akses untuk penugasan tim')

@section('sidebar')
    @include('components.sidebar-admin')
@endsection

@section('content')

    {{-- ── STATS CARDS ─────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">Total Akun</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                <p class="text-[11px] text-gray-400">Akun terdaftar di sistem</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">Belum Ditugaskan (Tersedia)</p>
                <p class="text-2xl font-bold text-emerald-600">{{ $stats['unassigned'] }}</p>
                <p class="text-[11px] text-gray-400">Bisa dipilih pada form penugasan</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">Sudah Ditugaskan</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['assigned'] }}</p>
                <p class="text-[11px] text-gray-400">Sedang aktif dikelola eksekutor</p>
            </div>
        </div>
    </div>

    {{-- ── FILTER & ACTION BAR ──────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <form action="{{ route('admin.accounts.index') }}" method="GET"
                class="flex flex-col sm:flex-row sm:items-center gap-3 flex-1">
                {{-- Search --}}
                <div class="relative flex-1 min-w-[220px]">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                        placeholder="Cari nama akun, platform, atau email..."
                        class="w-full h-10 pl-9 pr-3 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:bg-white focus:outline-none transition">
                </div>

                {{-- Filter group --}}
                <div class="flex items-center gap-2 shrink-0">
                    <select name="platform" onchange="this.form.submit()"
                        class="h-10 w-full sm:w-40 px-3 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:bg-white focus:outline-none transition">
                        <option value="">Semua Platform</option>
                        @foreach($platformList as $p)
                            <option value="{{ $p }}" {{ ($platform ?? '') === $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>

                    <select name="status" onchange="this.form.submit()"
                        class="h-10 w-full sm:w-44 px-3 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:bg-white focus:outline-none transition">
                        <option value="">Semua Status</option>
                        <option value="unassigned" {{ ($status ?? '') === 'unassigned' ? 'selected' : '' }}>Belum Ditugaskan</option>
                        <option value="assigned" {{ ($status ?? '') === 'assigned' ? 'selected' : '' }}>Sudah Ditugaskan</option>
                    </select>

                    @if($search || $platform || $status)
                        <a href="{{ route('admin.accounts.index') }}"
                            class="flex items-center justify-center h-10 w-10 text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition shrink-0"
                            title="Reset Filter">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </div>
            </form>

            <button onclick="openCreateAccountModal()"
                class="inline-flex items-center justify-center gap-2 h-10 px-4 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg shadow-sm transition shrink-0 w-full lg:w-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Akun Baru
            </button>
        </div>
    </div>

    {{-- ── TABLE ACCOUNTS ───────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse min-w-[850px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-5 py-3.5 w-36">Platform</th>
                        <th class="px-5 py-3.5">Nama Akun</th>
                        <th class="px-5 py-3.5">Link Akun</th>
                        <th class="px-5 py-3.5">Email</th>
                        <th class="px-5 py-3.5">Password</th>
                        <th class="px-5 py-3.5">Status Penugasan</th>
                        <th class="px-5 py-3.5 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($accounts as $acc)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            {{-- Platform --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium border {{ $acc->platform_color }}">
                                    {{ $acc->platform }}
                                </span>
                            </td>

                            {{-- Nama Akun --}}
                            <td class="px-5 py-4">
                                <div class="font-semibold text-gray-900">{{ $acc->name }}</div>
                                @if($acc->notes)
                                    <div class="text-xs text-gray-400 truncate max-w-xs mt-0.5" title="{{ $acc->notes }}">
                                        {{ $acc->notes }}
                                    </div>
                                @endif
                            </td>

                            {{-- Link Akun --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($acc->link)
                                    <a href="{{ $acc->link }}" target="_blank" rel="noopener noreferrer"
                                        class="inline-flex items-center gap-1.5 text-xs font-medium text-primary-600 hover:text-primary-800 hover:underline max-w-[200px] truncate"
                                        title="{{ $acc->link }}">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                        <span class="truncate">Buka Link</span>
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400 italic">Tidak ada link</span>
                                @endif
                            </td>

                            {{-- Email (Admin Only) --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($acc->email)
                                    <div class="flex items-center gap-1.5" x-data="{ copied: false }">
                                        <span class="text-xs font-mono text-gray-700 bg-gray-50 border border-gray-200 px-2 py-1 rounded select-all">
                                            {{ $acc->email }}
                                        </span>
                                        <button type="button"
                                            @click="navigator.clipboard.writeText('{{ $acc->email }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                            class="p-1 text-gray-400 hover:text-gray-600 rounded transition"
                                            :title="copied ? 'Tersalin!' : 'Salin Email'">
                                            <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                            <svg x-show="copied" x-cloak class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">-</span>
                                @endif
                            </td>

                            {{-- Password (Admin Only - Masked by default with eye toggle & copy) --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($acc->password)
                                    <div class="flex items-center gap-1.5" x-data="{ show: false, copied: false }">
                                        <div class="bg-gray-50 border border-gray-200 px-2.5 py-1 rounded min-w-[100px]">
                                            <span x-show="!show" class="font-mono text-xs text-gray-400 tracking-widest select-none">••••••••</span>
                                            <span x-show="show" x-cloak class="font-mono text-xs text-gray-900 font-semibold select-all">{{ $acc->password }}</span>
                                        </div>

                                        {{-- Toggle View Password --}}
                                        <button type="button" @click="show = !show"
                                            class="p-1 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded transition"
                                            :title="show ? 'Sembunyikan Password' : 'Lihat Password'">
                                            <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg x-show="show" x-cloak class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                            </svg>
                                        </button>

                                        {{-- Copy Password --}}
                                        <button type="button"
                                            @click="navigator.clipboard.writeText('{{ $acc->password }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                            class="p-1 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded transition"
                                            :title="copied ? 'Tersalin!' : 'Salin Password'">
                                            <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                            <svg x-show="copied" x-cloak class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">-</span>
                                @endif
                            </td>

                            {{-- Status Penugasan --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($acc->is_in_sosmed)
                                    @if($acc->staffUser)
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                            Dikelola: <span class="font-semibold">{{ $acc->staffUser->name }}</span>
                                        </div>
                                    @else
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Di Sosmed (Belum Ada Pengelola)
                                        </div>
                                    @endif
                                @else
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Belum Ditambahkan ke Sosmed
                                    </div>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-5 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <button type="button"
                                        onclick="openEditAccountModal({{ json_encode([
                                            'id' => $acc->id,
                                            'name' => $acc->name,
                                            'platform' => $acc->platform,
                                            'link' => $acc->link ?? '',
                                            'email' => $acc->email ?? '',
                                            'notes' => $acc->notes ?? '',
                                        ]) }})"
                                        class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                                        title="Edit Akun">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>

                                    <button type="button"
                                        onclick="openDeleteAccountModal({{ $acc->id }}, '{{ addslashes($acc->name) }}', '{{ addslashes($acc->platform) }}')"
                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                        title="Hapus Akun">
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
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    <p class="font-medium text-gray-600">Belum ada data akun sosial media</p>
                                    <p class="text-xs text-gray-400 mt-1">Klik tombol "Tambah Akun Baru" di atas untuk menambahkan akun.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── MODAL CREATE ACCOUNT ─────────────────────────────────────── --}}
    <div id="modal-create-account" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            onclick="document.getElementById('modal-create-account').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg z-10 max-h-[90vh] flex flex-col overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <div>
                    <h3 class="text-base font-bold text-gray-800">Tambah Akun Baru</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Tambahkan akun media sosial dan informasi kredensialnya</p>
                </div>
                <button type="button" onclick="document.getElementById('modal-create-account').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.accounts.store') }}" class="p-6 pt-1 space-y-4 overflow-y-auto">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Platform <span class="text-red-500">*</span></label>
                    <select name="platform" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="">-- Pilih Platform --</option>
                        @foreach($platformList as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nama Akun / Username <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: @republikweb_net"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Link Akun (URL Profil)</label>
                    <input type="url" name="link" placeholder="https://instagram.com/republikweb_net"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    <p class="text-[11px] text-gray-400 mt-1">Link ini dapat langsung diklik pada tabel untuk membuka profil akun.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Email Akun</label>
                        <input type="email" name="email" placeholder="email@domain.com"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <p class="text-[10px] text-amber-600 mt-1">Hanya bisa dilihat oleh Admin.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Password Akun</label>
                        <input type="text" name="password" placeholder="Ketik password akun..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <p class="text-[10px] text-amber-600 mt-1">Tersimpan aman & terenkripsi.</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Catatan / Keterangan</label>
                    <textarea name="notes" rows="2" placeholder="Catatan tambahan seputar akun..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none"></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-create-account').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">Batal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── MODAL EDIT ACCOUNT ───────────────────────────────────────── --}}
    <div id="modal-edit-account" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            onclick="document.getElementById('modal-edit-account').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg z-10 max-h-[90vh] flex flex-col overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <div>
                    <h3 class="text-base font-bold text-gray-800">Edit Akun</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Perbarui informasi akun atau kredensial akses</p>
                </div>
                <button type="button" onclick="document.getElementById('modal-edit-account').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="form-edit-account" method="POST" action="" class="p-6 space-y-4 overflow-y-auto">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Platform <span class="text-red-500">*</span></label>
                    <select name="platform" id="edit-acc-platform" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="">-- Pilih Platform --</option>
                        @foreach($platformList as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nama Akun / Username <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="edit-acc-name" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Link Akun (URL Profil)</label>
                    <input type="url" name="link" id="edit-acc-link" placeholder="https://instagram.com/username"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Email Akun</label>
                        <input type="email" name="email" id="edit-acc-email" placeholder="email@domain.com"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Ganti Password</label>
                        <input type="text" name="password" id="edit-acc-password" placeholder="Kosongkan jika tetap sama"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <p class="text-[10px] text-gray-400 mt-1">Kosongkan jika tidak ingin mengubah password.</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Catatan / Keterangan</label>
                    <textarea name="notes" id="edit-acc-notes" rows="2"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none"></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-edit-account').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">Batal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── MODAL DELETE ACCOUNT ─────────────────────────────────────── --}}
    <div id="modal-delete-account" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            onclick="document.getElementById('modal-delete-account').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm z-10 p-6">
            <div class="text-center mb-5">
                <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 mx-auto flex items-center justify-center mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-800">Hapus Akun?</h3>
                <p class="text-xs text-gray-500 mt-1">
                    Apakah kamu yakin ingin menghapus akun <span id="del-acc-name" class="font-semibold text-gray-800"></span>? Riwayat penugasan terkait akun ini juga akan terhapus.
                </p>
            </div>

            <form id="form-delete-account" method="POST" action="" class="flex gap-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="document.getElementById('modal-delete-account').classList.add('hidden')"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">Batal</button>
                <button type="submit"
                    class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">Ya, Hapus</button>
            </form>
        </div>
    </div>

    {{-- ── JAVASCRIPT MODAL HANDLERS ────────────────────────────────── --}}
    <script>
        function openCreateAccountModal() {
            document.getElementById('modal-create-account').classList.remove('hidden');
        }

        function openEditAccountModal(data) {
            const form = document.getElementById('form-edit-account');
            form.action = `/admin/accounts/${data.id}`;

            document.getElementById('edit-acc-name').value = data.name || '';
            document.getElementById('edit-acc-platform').value = data.platform || '';
            document.getElementById('edit-acc-link').value = data.link || '';
            document.getElementById('edit-acc-email').value = data.email || '';
            document.getElementById('edit-acc-password').value = '';
            document.getElementById('edit-acc-notes').value = data.notes || '';

            document.getElementById('modal-edit-account').classList.remove('hidden');
        }

        function openDeleteAccountModal(id, name, platform) {
            const form = document.getElementById('form-delete-account');
            form.action = `/admin/accounts/${id}`;
            document.getElementById('del-acc-name').textContent = `${name} (${platform})`;
            document.getElementById('modal-delete-account').classList.remove('hidden');
        }
    </script>

@endsection
