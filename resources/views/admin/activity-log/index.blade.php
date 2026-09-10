@extends('layouts.app')

@section('title', 'Log Aktivitas')
@section('page-title', 'Log Aktivitas')
@section('page-subtitle', 'Pantau semua aksi yang dilakukan oleh seluruh pengguna sistem')

@section('sidebar')
    @include('components.sidebar-admin')
@endsection

@section('content')

    {{-- ── Flash Notifications ─────────────────────────────────────────── --}}
    @if(session('success'))
        <div
            class="mb-3.5 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif
    @if(session('error'))
        <div
            class="mb-3.5 p-3 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    {{-- ── 1. Stat Cards Compact ──────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4 mb-4">
        <div class="bg-white rounded-xl border border-gray-200 p-3 shadow-sm flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] sm:text-xs text-gray-500 font-medium truncate">Aksi Hari Ini</p>
                <p class="text-base sm:text-xl font-bold text-gray-800 leading-tight">{{ number_format($statsToday) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-3 shadow-sm flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] sm:text-xs text-gray-500 font-medium truncate">Total Log</p>
                <p class="text-base sm:text-xl font-bold text-gray-800 leading-tight">{{ number_format($statsTotal) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-3 shadow-sm flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] sm:text-xs text-gray-500 font-medium truncate">Modul Tersibuk</p>
                <p class="text-xs sm:text-sm font-bold text-gray-800 truncate leading-tight" title="{{ $topModule }}">
                    {{ $topModule }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-3 shadow-sm flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] sm:text-xs text-gray-500 font-medium truncate">Pengguna Teraktif</p>
                <p class="text-xs sm:text-sm font-bold text-gray-800 truncate leading-tight" title="{{ $topUser }}">
                    {{ $topUser }}</p>
            </div>
        </div>
    </div>

    {{-- ── 2. Filter Form Terbuka Lengkap & Rapi ────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-4 p-3">
        <form method="GET" action="{{ route('admin.activity-log.index') }}" class="space-y-2.5">

            {{-- Search Bar --}}
            <div class="relative w-full">
                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari pengguna atau deskripsi..."
                    class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
            </div>

            {{-- Filter Grid 2-Kolom di Mobile, 4-Kolom di Desktop --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2">
                <div>
                    <select name="user_id" onchange="this.form.submit()"
                        class="w-full px-2.5 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Pengguna</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select name="role" onchange="this.form.submit()"
                        class="w-full px-2.5 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>{{ strtoupper($role) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select name="module" onchange="this.form.submit()"
                        class="w-full px-2.5 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Modul</option>
                        @foreach($modules as $mod)
                            <option value="{{ $mod }}" {{ request('module') == $mod ? 'selected' : '' }}>{{ $mod }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select name="action" onchange="this.form.submit()"
                        class="w-full px-2.5 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Tipe Aksi</option>
                        <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>Dibuat</option>
                        <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>Diperbarui</option>
                        <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>Dihapus</option>
                        <option value="completed" {{ request('action') == 'completed' ? 'selected' : '' }}>Diselesaikan
                        </option>
                        <option value="assigned" {{ request('action') == 'assigned' ? 'selected' : '' }}>Ditugaskan</option>
                        <option value="verified" {{ request('action') == 'verified' ? 'selected' : '' }}>Diverifikasi</option>
                    </select>
                </div>
            </div>

            {{-- Date Range & Tombol Terapkan Pas di Layar --}}
            <div class="flex flex-col sm:flex-row items-center gap-2 pt-1 border-t border-gray-100">
                <div class="flex items-center gap-1.5 w-full">
                    <input type="date" name="date_from" value="{{ request('date_from', today()->format('Y-m-d')) }}"
                        class="w-full min-w-0 px-2 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <span class="text-xs text-gray-400 font-medium shrink-0">s/d</span>
                    <input type="date" name="date_to" value="{{ request('date_to', today()->format('Y-m-d')) }}"
                        class="w-full min-w-0 px-2 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto shrink-0 justify-end">
                    @if(request()->hasAny(['search', 'user_id', 'role', 'module', 'action']))
                        <a href="{{ route('admin.activity-log.index') }}"
                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                            title="Reset Filter">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif

                    <button type="submit"
                        class="w-full sm:w-auto px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition shrink-0">
                        Terapkan
                    </button>
                </div>
            </div>

        </form>
    </div>

    {{-- ── 3. List Data Content ────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- Header Bar --}}
        <div class="px-4 sm:px-6 py-3.5 border-b border-gray-100 bg-gray-50/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex-1">
                <h2 class="text-sm font-bold text-gray-800">Daftar Aktivitas</h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    Menampilkan {{ $logs->firstItem() ?? 0 }}–{{ $logs->lastItem() ?? 0 }} dari {{ number_format($logs->total()) }} entri
                </p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <button type="button" onclick="openPurgeModal()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition active:scale-95"
                    title="Bersihkan riwayat log untuk menghemat penyimpanan">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Bersihkan Log</span>
                </button>
                <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 bg-green-50 text-green-700 rounded-full border border-green-200">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    <span>Live 30s</span>
                </span>
            </div>
        </div>

        {{-- Desktop Table (md+) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3">Pengguna</th>
                        <th class="px-3 py-3">Modul</th>
                        <th class="px-3 py-3">Aksi</th>
                        <th class="px-4 py-3">Deskripsi</th>
                        <th class="px-4 py-3">IP</th>
                        <th class="px-3 py-3 text-center w-12">Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                        @php
                            $moduleColors = [
                                'Tugas' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'Presensi' => 'bg-green-50 text-green-700 border-green-200',
                                'Pemagang' => 'bg-purple-50 text-purple-700 border-purple-200',
                                'Sosmed' => 'bg-pink-50 text-pink-700 border-pink-200',
                                'Pengguna' => 'bg-orange-50 text-orange-700 border-orange-200',
                                'Role' => 'bg-red-50 text-red-700 border-red-200',
                                'Log Aktivitas' => 'bg-rose-50 text-rose-700 border-rose-200',
                                'Pengaturan' => 'bg-slate-100 text-slate-700 border-slate-200',
                            ];
                            $moduleStyle = $moduleColors[$log->module] ?? 'bg-gray-100 text-gray-700 border-gray-200';

                            $actionColors = [
                                'created' => 'bg-emerald-50 text-emerald-700',
                                'updated' => 'bg-blue-50 text-blue-700',
                                'deleted' => 'bg-red-50 text-red-700',
                                'completed' => 'bg-indigo-50 text-indigo-700',
                            ];
                            $actionParts = explode('.', $log->action);
                            $actionKey = end($actionParts);
                            $actionStyle = $actionColors[$actionKey] ?? 'bg-gray-100 text-gray-700';
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <p class="font-medium text-gray-700">
                                    {{ $log->created_at->locale('id')->translatedFormat('d M Y') }}</p>
                                <p class="text-[10px] text-gray-400">{{ $log->created_at->format('H:i:s') }}
                                    ({{ $log->created_at->diffForHumans() }})</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-gray-800">{{ $log->user_name }}</p>
                                <span
                                    class="inline-block px-1.5 py-0.2 rounded text-[9px] font-medium bg-slate-100 text-slate-600 uppercase">{{ $log->user_role }}</span>
                            </td>
                            <td class="px-3 py-3">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $moduleStyle }}">{{ $log->module }}</span>
                            </td>
                            <td class="px-3 py-3">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $actionStyle }}">{{ ucfirst($actionKey) }}</span>
                            </td>
                            <td class="px-4 py-3 max-w-xs text-gray-700">{{ $log->description }}</td>
                            <td class="px-4 py-3 text-[10px] text-gray-400 font-mono">{{ $log->ip_address ?? '-' }}</td>
                            <td class="px-3 py-3 text-center whitespace-nowrap">
                                <form action="{{ route('admin.activity-log.destroy', $log->id) }}" method="POST"
                                    onsubmit="return confirm('Hapus riwayat aktivitas ini?')" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition"
                                        title="Hapus log">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">Belum ada log aktivitas yang sesuai
                                filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards (< md) --}} <div class="md:hidden divide-y divide-gray-100">
            @forelse($logs as $log)
                @php
                    $moduleColors = [
                        'Tugas' => 'bg-blue-50 text-blue-700 border-blue-200',
                        'Presensi' => 'bg-green-50 text-green-700 border-green-200',
                        'Pemagang' => 'bg-purple-50 text-purple-700 border-purple-200',
                        'Sosmed' => 'bg-pink-50 text-pink-700 border-pink-200',
                        'Pengguna' => 'bg-orange-50 text-orange-700 border-orange-200',
                        'Role' => 'bg-red-50 text-red-700 border-red-200',
                        'Log Aktivitas' => 'bg-rose-50 text-rose-700 border-rose-200',
                        'Pengaturan' => 'bg-slate-100 text-slate-700 border-slate-200',
                    ];
                    $moduleStyle = $moduleColors[$log->module] ?? 'bg-gray-100 text-gray-700 border-gray-200';

                    $actionColors = [
                        'created' => 'bg-emerald-50 text-emerald-700',
                        'updated' => 'bg-blue-50 text-blue-700',
                        'deleted' => 'bg-red-50 text-red-700',
                        'completed' => 'bg-indigo-50 text-indigo-700',
                    ];
                    $actionParts = explode('.', $log->action);
                    $actionKey = end($actionParts);
                    $actionStyle = $actionColors[$actionKey] ?? 'bg-gray-100 text-gray-700';
                @endphp
                <div class="p-3 space-y-2">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="font-bold text-gray-800 text-xs truncate">{{ $log->user_name }}</p>
                            <span
                                class="inline-block px-1.5 py-0.2 rounded text-[9px] font-semibold bg-gray-100 text-gray-500 uppercase">
                                {{ $log->user_role }}
                            </span>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $moduleStyle }}">
                                {{ $log->module }}
                            </span>
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $actionStyle }}">
                                {{ ucfirst($actionKey) }}
                            </span>
                        </div>
                    </div>

                    <div class="text-xs text-gray-700 leading-snug bg-gray-50/80 p-2.5 rounded-lg border border-gray-100">
                        {{ $log->description }}
                    </div>

                    <div class="flex items-center justify-between text-[10px] text-gray-400 pt-0.5">
                        <span>{{ $log->created_at->format('d M, H:i') }} • {{ $log->created_at->diffForHumans() }}</span>
                        <div class="flex items-center gap-2">
                            <span class="font-mono">{{ $log->ip_address ?? '-' }}</span>
                            <form action="{{ route('admin.activity-log.destroy', $log->id) }}" method="POST"
                                onsubmit="return confirm('Hapus riwayat aktivitas ini?')" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="p-1 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded transition"
                                    title="Hapus log">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-xs text-gray-400">
                    Belum ada log aktivitas yang sesuai filter.
                </div>
            @endforelse
    </div>

    @if($logs->hasPages())
        <div class="px-3 py-2 border-t border-gray-100 bg-gray-50/50">
            {{ $logs->links() }}
        </div>
    @endif

    </div>

    {{-- ── Modal: Bersihkan Log Aktivitas ────────────────────────────────── --}}
    <div id="modal-purge-logs" class="hidden fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closePurgeModal()"></div>
        <div
            class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm sm:max-w-md z-10 flex flex-col overflow-hidden">
            <div class="flex items-center justify-between px-4 sm:px-6 py-3 sm:py-4 border-b border-rose-100 bg-rose-50/70">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-gray-800">Bersihkan Log Aktivitas</h3>
                        <p class="text-[11px] text-gray-500">Hemat ruang penyimpanan database</p>
                    </div>
                </div>
                <button type="button" onclick="closePurgeModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.activity-log.purge') }}" class="p-4 pt-1 sm:pt-1 sm:p-6 space-y-4">
                @csrf
                @method('DELETE')

                <div
                    class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800 flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <p class="font-semibold">Perhatian Penting</p>
                        <p class="mt-0.5 text-amber-700">Data log yang dihapus tidak dapat dikembalikan. Saat ini tersimpan
                            <strong>{{ number_format($statsTotal) }}</strong> log aktivitas.</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Pilih Periode Pembersihan:</label>
                    <div class="space-y-2">
                        <label
                            class="flex items-center gap-2.5 p-2.5 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition">
                            <input type="radio" name="period" value="1_week" class="text-rose-600 focus:ring-rose-500">
                            <div class="text-xs">
                                <span class="font-medium text-gray-800">Lebih dari 1 Minggu Lalu</span>
                                <span class="text-gray-400 block text-[10px]">Hapus log yang berusia lebih dari 7
                                    hari</span>
                            </div>
                        </label>
                        <label
                            class="flex items-center gap-2.5 p-2.5 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition">
                            <input type="radio" name="period" value="1_month" checked
                                class="text-rose-600 focus:ring-rose-500">
                            <div class="text-xs">
                                <span class="font-medium text-gray-800">Lebih dari 1 Bulan Lalu (Disarankan)</span>
                                <span class="text-gray-400 block text-[10px]">Hapus log yang berusia lebih dari 30
                                    hari</span>
                            </div>
                        </label>
                        <label
                            class="flex items-center gap-2.5 p-2.5 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition">
                            <input type="radio" name="period" value="3_months" class="text-rose-600 focus:ring-rose-500">
                            <div class="text-xs">
                                <span class="font-medium text-gray-800">Lebih dari 3 Bulan Lalu</span>
                                <span class="text-gray-400 block text-[10px]">Hapus log yang berusia lebih dari 90
                                    hari</span>
                            </div>
                        </label>
                        <label
                            class="flex items-center gap-2.5 p-2.5 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition">
                            <input type="radio" name="period" value="6_months" class="text-rose-600 focus:ring-rose-500">
                            <div class="text-xs">
                                <span class="font-medium text-gray-800">Lebih dari 6 Bulan Lalu</span>
                                <span class="text-gray-400 block text-[10px]">Hapus log yang berusia lebih dari 180
                                    hari</span>
                            </div>
                        </label>
                        <label
                            class="flex items-center gap-2.5 p-2.5 rounded-lg border border-rose-200 bg-rose-50/40 hover:bg-rose-50 cursor-pointer transition">
                            <input type="radio" name="period" value="all" class="text-rose-600 focus:ring-rose-500">
                            <div class="text-xs">
                                <span class="font-semibold text-rose-700">Semua Log Aktivitas</span>
                                <span class="text-rose-500 block text-[10px]">Kosongkan seluruh riwayat log tanpa
                                    terkecuali</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" onclick="closePurgeModal()"
                        class="px-3.5 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-lg transition">
                        Batal
                    </button>
                    <button type="submit"
                        onclick="return confirm('Apakah Anda yakin ingin melanjutkan penghapusan log sesuai periode terpilih?')"
                        class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg shadow-sm transition active:scale-95">
                        Hapus Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function openPurgeModal() {
                document.getElementById('modal-purge-logs').classList.remove('hidden');
            }

            function closePurgeModal() {
                document.getElementById('modal-purge-logs').classList.add('hidden');
            }

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closePurgeModal();
                }
            });

            let refreshTimer;
            const REFRESH_MS = 30000;

            function scheduleRefresh() {
                clearTimeout(refreshTimer);
                refreshTimer = setTimeout(() => location.reload(), REFRESH_MS);
            }

            scheduleRefresh();

            document.querySelectorAll('select, input').forEach(el => {
                el.addEventListener('focus', () => clearTimeout(refreshTimer));
                el.addEventListener('blur', scheduleRefresh);
            });
        </script>
    @endpush

@endsection