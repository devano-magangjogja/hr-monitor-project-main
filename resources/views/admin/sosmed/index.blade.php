@extends('layouts.app')
@section('title', 'Manajemen Akun & Monitoring Sosmed')
@section('page-title', 'Manajemen Akun & Monitoring Sosmed')
@section('page-subtitle', 'Kelola seluruh akun sosmed, delegasi penugasan & audit trail approval')
@section('sidebar')
    @include('components.sidebar-admin')
@endsection

@section('content')
    @include('components.notification-popup')

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- STAT CARDS --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs font-medium text-gray-500 mb-1">Total Akun</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total_accounts'] }}</p>
            <p class="text-[11px] text-gray-400 mt-0.5">akun terdaftar</p>
        </div>
        <div
            class="bg-white rounded-xl border {{ $stats['unassigned_pm'] > 0 ? 'border-amber-300 bg-amber-50/20' : 'border-gray-200' }} p-4 shadow-sm">
            <p class="text-xs font-medium text-gray-500 mb-1">Belum Ada PM</p>
            <p class="text-2xl font-bold text-amber-600">{{ $stats['unassigned_pm'] }}</p>
            <p class="text-[11px] text-amber-600 mt-0.5">perlu assign PM</p>
        </div>
        <div
            class="bg-white rounded-xl border {{ $stats['unassigned_staff'] > 0 ? 'border-orange-300 bg-orange-50/20' : 'border-gray-200' }} p-4 shadow-sm">
            <p class="text-xs font-medium text-gray-500 mb-1">Belum Ada Staff</p>
            <p class="text-2xl font-bold text-orange-600">{{ $stats['unassigned_staff'] }}</p>
            <p class="text-[11px] text-orange-600 mt-0.5">perlu assign Sosmed</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs font-medium text-gray-500 mb-1">Total Tugas</p>
            <p class="text-2xl font-bold text-indigo-600">{{ $stats['total_tasks'] }}</p>
            <p class="text-[11px] text-gray-400 mt-0.5">harian & custom</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs font-medium text-gray-500 mb-1">Verif Level 1 (PM)</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['need_pm_verify'] }}</p>
            <p class="text-[11px] text-gray-400 mt-0.5">tugas menunggu PM</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs font-medium text-gray-500 mb-1">Verif Level 2 (HR)</p>
            <p class="text-2xl font-bold text-purple-600">{{ $stats['need_hr_verify'] }}</p>
            <p class="text-[11px] text-gray-400 mt-0.5">tugas menunggu HR</p>
        </div>

        <div class="col-span-2 sm:col-span-1 bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs font-medium text-gray-500 mb-1">Selesai Final</p>
            <p class="text-2xl font-bold text-emerald-600">{{ $stats['completed'] }}</p>
            <p class="text-[11px] text-emerald-600 mt-0.5">approved final</p>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- TABS & CONTENT --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        {{-- Navigation Tabs --}}
        <div class="flex border-b border-gray-200 overflow-x-auto scrollbar-none">
            <a href="{{ route('admin.sosmed.index', ['tab' => 'accounts']) }}"
                class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 transition
                                    {{ $tab === 'accounts' ? 'border-primary-600 text-primary-600 bg-primary-50/50' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Seluruh Akun Sosmed ({{ $accounts->count() }})
            </a>
            <a href="{{ route('admin.sosmed.index', ['tab' => 'tasks']) }}"
                class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 transition
                                    {{ $tab === 'tasks' ? 'border-primary-600 text-primary-600 bg-primary-50/50' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                Monitoring Seluruh Tugas ({{ $tasks->count() }})
            </a>
            <a href="{{ route('admin.sosmed.index', ['tab' => 'logs']) }}"
                class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 transition
                                    {{ $tab === 'logs' ? 'border-primary-600 text-primary-600 bg-primary-50/50' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                Audit Trail & Log Approval
            </a>
        </div>

        {{-- ── TAB 1: SELURUH AKUN SOSMED ─────────────────────────────── --}}
        @if($tab === 'accounts')
                <div class="p-4 sm:p-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">Master Data Akun Sosial Media</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Admin memiliki kewenangan penuh menambah akun dan menunjuk
                                penanggung jawab PM & Staff Sosmed</p>
                        </div>
                        <button onclick="openCreateAccountModal()"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs sm:text-sm font-semibold rounded-lg transition shadow-sm w-full sm:w-auto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Akun Baru
                        </button>
                    </div>

                    {{-- Desktop Table (md+) --}}
                    <div class="hidden md:block overflow-x-auto rounded-lg border border-gray-100">
                        <table class="w-full table-fixed text-sm">
                            <colgroup>
                                <col class="w-[24%]"> {{-- Nama Akun & Catatan --}}
                                <col class="w-32">     {{-- Platform --}}
                                <col class="w-40">     {{-- Link URL --}}
                                <col class="w-48">     {{-- Eksekutor --}}
                                <col class="w-44">     {{-- Supervisor PM --}}
                                <col class="w-28">     {{-- Aksi --}}
                            </colgroup>
                            <thead>
                                <tr
                                    class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    <th class="px-4 py-3 text-left">Nama Akun & Deskripsi</th>
                                    <th class="px-4 py-3 text-left">Platform</th>
                                    <th class="px-4 py-3 text-left">Link URL</th>
                                    <th class="px-4 py-3 text-left">Eksekutor (Dikelola Oleh)</th>
                                    <th class="px-4 py-3 text-left">Supervisor (Diawasi PM)</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($accounts as $acc)
                                    <tr class="hover:bg-gray-50/80 transition align-middle">
                                        <td class="px-4 py-3.5 min-w-0">
                                            <span class="font-semibold text-gray-800 block truncate"
                                                title="{{ $acc->name }}">{{ $acc->name }}</span>
                                            @if($acc->notes)
                                                <p class="text-xs text-gray-400 mt-0.5 truncate max-w-full block" title="{{ $acc->notes }}">
                                                    {{ $acc->notes }}
                                                </p>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <span
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-medium border whitespace-nowrap {{ $acc->platform_color }}">
                                                {{ $acc->platform_icon }} {{ $acc->platform }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3.5">
                                            @if($acc->link)
                                                <a href="{{ $acc->link }}" target="_blank"
                                                    class="inline-flex items-center gap-1 text-xs text-primary-600 hover:underline truncate max-w-full block"
                                                    title="{{ $acc->link }}">
                                                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                    </svg>
                                                    <span class="truncate">{{ parse_url($acc->link, PHP_URL_HOST) ?? $acc->link }}</span>
                                                </a>
                                            @else
                                                <span class="text-xs text-gray-300">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-xs min-w-0">
                                            @if($acc->staffUser)
                                                @php
                                                    $staffRoleTag = match($acc->staffUser->role) {
                                                        'pm' => 'PM Mandiri',
                                                        'sosmed' => 'Staff Sosmed',
                                                        'digital_marketing' => 'Digital Marketing',
                                                        default => $acc->staffUser->role_label ?? strtoupper($acc->staffUser->role)
                                                    };
                                                @endphp
                                                <div class="min-w-0">
                                                    <span class="font-semibold text-gray-800 block truncate"
                                                        title="{{ $acc->staffUser->name }}">
                                                        {{ $acc->staffUser->name }}
                                                    </span>
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold mt-1 {{ $acc->staffUser->role === 'pm' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-pink-50 text-pink-700 border border-pink-200' }}">
                                                        {{ $staffRoleTag }}
                                                    </span>
                                                </div>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200 whitespace-nowrap">
                                                    Belum Ditugaskan
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-xs min-w-0">
                                            @if($acc->staffUser && $acc->staffUser->role === 'pm')
                                                <span class="text-gray-400 italic text-[11px] block">— (Langsung ke HR)</span>
                                            @elseif($acc->pmUser)
                                                <div class="min-w-0">
                                                    <span class="font-semibold text-gray-800 block truncate"
                                                        title="{{ $acc->pmUser->name }}">
                                                        {{ $acc->pmUser->name }}
                                                    </span>
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold mt-1 bg-purple-50 text-purple-700 border border-purple-200">
                                                        Supervisor PM
                                                    </span>
                                                </div>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 whitespace-nowrap">
                                                    Belum Ada PM
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3.5 text-center">
                                            <div class="flex items-center justify-center gap-1">
                                                <button type="button"
                                                    onclick="openEditAccountModal({{ $acc->id }}, '{{ addslashes($acc->name) }}', '{{ addslashes($acc->platform) }}', '{{ addslashes($acc->link ?? '') }}', {{ $acc->pm_id ?? 'null' }}, {{ $acc->staff_id ?? 'null' }}, '{{ addslashes(str_replace(["\r", "\n"], [' ', ' '], $acc->notes ?? '')) }}')"
                                                    class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                                    title="Edit Akun">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                <button
                                                    onclick="openAssignModal({{ $acc->id }}, '{{ addslashes($acc->name) }}', {{ $acc->pm_id ?? 'null' }}, {{ $acc->staff_id ?? 'null' }})"
                                                    class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                                                    title="Atur Eksekutor & Supervisor">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                </button>
                                                <form method="POST" action="{{ route('admin.sosmed.accounts.destroy', $acc) }}"
                                                    onsubmit="return confirm('Hapus akun ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                                        title="Hapus">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400">Belum ada akun sosial media
                                            yang didaftarkan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile Cards (< md) --}} <div class="md:hidden space-y-3">
                        @forelse($accounts as $acc)
                            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                                <div class="flex items-start justify-between gap-2 mb-3">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-800 text-sm truncate" title="{{ $acc->name }}">
                                            {{ $acc->name }}
                                        </p>
                                        <span
                                            class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-md text-xs font-medium border {{ $acc->platform_color }}">
                                            {{ $acc->platform_icon }} {{ $acc->platform }}
                                        </span>
                                        @if($acc->notes)
                                            <p class="text-xs text-gray-400 mt-1 line-clamp-2" title="{{ $acc->notes }}">
                                                {{ $acc->notes }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <button type="button"
                                            onclick="openEditAccountModal({{ $acc->id }}, '{{ addslashes($acc->name) }}', '{{ addslashes($acc->platform) }}', '{{ addslashes($acc->link ?? '') }}', {{ $acc->pm_id ?? 'null' }}, {{ $acc->staff_id ?? 'null' }}, '{{ addslashes(str_replace(["\r", "\n"], [' ', ' '], $acc->notes ?? '')) }}')"
                                            class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                            title="Edit Akun">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button
                                            onclick="openAssignModal({{ $acc->id }}, '{{ addslashes($acc->name) }}', {{ $acc->pm_id ?? 'null' }}, {{ $acc->staff_id ?? 'null' }})"
                                            class="p-1.5 bg-primary-50 text-primary-600 rounded-lg text-xs font-medium transition"
                                            title="Atur Eksekutor & Supervisor">
                                            Atur
                                        </button>
                                        <form method="POST" action="{{ route('admin.sosmed.accounts.destroy', $acc) }}"
                                            onsubmit="return confirm('Hapus akun ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between gap-2 text-xs border-t border-gray-100 pt-2.5">
                                    <div class="min-w-0">
                                        <p class="text-gray-400 mb-0.5">Eksekutor</p>
                                        <p class="font-medium text-gray-800 truncate">
                                            @if($acc->staffUser)
                                                @php
                                                    $mRoleTag = match($acc->staffUser->role) {
                                                        'pm' => 'PM Mandiri',
                                                        'sosmed' => 'Staff Sosmed',
                                                        'digital_marketing' => 'Digital Marketing',
                                                        default => $acc->staffUser->role_label ?? strtoupper($acc->staffUser->role)
                                                    };
                                                @endphp
                                                {{ $acc->staffUser->name }} <span class="text-[10px] text-gray-500 font-normal">({{ $mRoleTag }})</span>
                                            @else
                                                Belum Ditugaskan
                                            @endif
                                        </p>
                                    </div>
                                    <div class="min-w-0 text-right">
                                        <p class="text-gray-400 mb-0.5">Supervisor PM</p>
                                        <p class="font-medium text-gray-800 truncate">
                                            {{ ($acc->staffUser && $acc->staffUser->role === 'pm') ? 'Langsung ke HR' : ($acc->pmUser?->name ?? 'Belum Ada PM') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-sm text-gray-400">Belum ada akun sosial media.</div>
                        @endforelse
                </div>
            </div>
        @endif

    {{-- ── TAB 2: MONITORING SELURUH TUGAS ────────────────────────── --}}
    @if($tab === 'tasks')
        <div class="p-4 sm:p-5">
            <div class="mb-4">
                <h3 class="text-sm font-semibold text-gray-800">Laporan & Status Pengerjaan Tugas Sosmed</h3>
                <p class="text-xs text-gray-500 mt-0.5">Monitoring nama user pelaksana, penanggung jawab PM, dan verifikator</p>
            </div>

            {{-- Desktop Table (md+) --}}
            <div class="hidden md:block overflow-x-auto rounded-lg border border-gray-100">
                <table class="w-full table-fixed text-sm">
                    <colgroup>
                        <col class="w-1/4"> {{-- Judul Tugas --}}
                        <col class="w-36"> {{-- Akun --}}
                        <col class="w-40"> {{-- PJ PM --}}
                        <col class="w-36"> {{-- Pelaksana --}}
                        <col class="w-40"> {{-- Verif PM --}}
                        <col class="w-40"> {{-- Final HR --}}
                        <col class="w-32"> {{-- Status --}}
                    </colgroup>
                    <thead>
                        <tr
                            class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            <th class="px-4 py-3 text-left">Judul Tugas</th>
                            <th class="px-4 py-3 text-left">Akun</th>
                            <th class="px-4 py-3 text-left">PJ PM</th>
                            <th class="px-4 py-3 text-left">Pelaksana</th>
                            <th class="px-4 py-3 text-left">Verif PM</th>
                            <th class="px-4 py-3 text-left">Final HR</th>
                            <th class="px-4 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($tasks as $t)
                            <tr class="hover:bg-gray-50/80 transition align-middle">
                                <td class="px-4 py-3.5 min-w-0">
                                    <p class="font-semibold text-gray-800 truncate" title="{{ $t->title }}">{{ $t->title }}</p>
                                    @if($t->description)
                                        <p class="text-xs text-gray-400 mt-0.5 truncate max-w-full" title="{{ $t->description }}">
                                            {{ $t->description }}
                                        </p>
                                    @endif
                                    <div class="flex items-center gap-2 mt-1">
                                        <span
                                            class="px-1.5 py-0.5 text-[10px] rounded font-medium {{ $t->type === 'daily' ? 'bg-gray-100 text-gray-600' : 'bg-purple-50 text-purple-700' }}">
                                            {{ $t->type === 'daily' ? 'Harian' : 'Custom' }}
                                        </span>
                                        @if($t->hasLinks())
                                            <button type="button"
                                                onclick="openLinksPopup({{ json_encode($t->link_upload) }}, '{{ addslashes($t->title) }}')"
                                                class="inline-flex items-center gap-0.5 text-xs text-primary-600 hover:underline">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                                </svg>
                                                {{ $t->link_count }} Bukti
                                            </button>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-xs">
                                    <p class="font-medium text-gray-800 truncate">{{ $t->account?->name ?? '—' }}</p>
                                    <p class="text-gray-400 mt-0.5">{{ $t->account?->platform }}</p>
                                </td>
                                <td class="px-4 py-3.5 text-xs whitespace-nowrap">
                                    @if($t->account?->pmUser)
                                        <span class="font-medium text-gray-800">{{ $t->account->pmUser->name }}</span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-xs whitespace-nowrap">
                                    @if($t->assignedUser)
                                        <span class="font-medium text-gray-800">{{ $t->assignedUser->name }}</span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-xs whitespace-nowrap">
                                    @if($t->verifiedBy)
                                        <span class="text-blue-700 font-semibold block">{{ $t->verifiedBy->name }}</span>
                                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $t->verified_at?->translatedFormat('d M, H:i') }}
                                        </p>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-xs whitespace-nowrap">
                                    @if($t->hrVerifiedBy)
                                        <span class="text-emerald-700 font-semibold block">{{ $t->hrVerifiedBy->name }}</span>
                                        <p class="text-[10px] text-gray-400 mt-0.5">
                                            {{ $t->hr_verified_at?->translatedFormat('d M, H:i') }}
                                        </p>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $t->status_badge_class }}">
                                        {{ $t->status_label }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-400">Belum ada aktivitas tugas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards (< md) --}} <div class="md:hidden space-y-3">
                @forelse($tasks as $t)
                    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-800 text-sm truncate">{{ $t->title }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $t->account?->name }} ({{ $t->account?->platform }})</p>
                            </div>
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium whitespace-nowrap {{ $t->status_badge_class }}">
                                {{ $t->status_label }}
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs border-t border-gray-100 pt-2.5 mt-2">
                            {{-- Kiri --}}
                            <div class="min-w-0">
                                <p class="text-gray-400 mb-0.5">PJ PM</p>
                                <p class="font-medium text-gray-800 truncate">{{ $t->account?->pmUser?->name ?? '—' }}</p>
                            </div>
                            {{-- Kanan (Pelaksana) --}}
                            <div class="min-w-0 text-right">
                                <p class="text-gray-400 mb-0.5">Pelaksana</p>
                                <p class="font-medium text-gray-800 truncate">{{ $t->assignedUser?->name ?? '—' }}</p>
                            </div>
                            {{-- Kiri --}}
                            <div class="min-w-0">
                                <p class="text-gray-400 mb-0.5">Verif PM</p>
                                <p class="font-medium text-blue-700 truncate">{{ $t->verifiedBy?->name ?? '—' }}</p>
                            </div>
                            {{-- Kanan (Final HR) --}}
                            <div class="min-w-0 text-right">
                                <p class="text-gray-400 mb-0.5">Final HR</p>
                                <p class="font-medium text-emerald-700 truncate">{{ $t->hrVerifiedBy?->name ?? '—' }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-sm text-gray-400">Belum ada aktivitas tugas.</div>
                @endforelse
        </div>
        </div>
    @endif

    {{-- ── TAB 3: AUDIT TRAIL LOG APPROVAL ─────────────────────────── --}}
    @if($tab === 'logs')
        <div class="p-4 sm:p-5">
            <div class="mb-4">
                <h3 class="text-sm font-semibold text-gray-800">Transparansi Audit Trail & Riwayat Approval</h3>
                <p class="text-xs text-gray-500 mt-0.5">Sistem merekam setiap aksi pengerjaan, approval PM, approval HR Staff,
                    hingga penolakan tugas</p>
            </div>

            {{-- Desktop Table (md+) --}}
            <div class="hidden md:block overflow-x-auto rounded-lg border border-gray-100">
                <table class="w-full text-sm">
                    <colgroup>
                        <col class="w-36"> {{-- Waktu --}}
                        <col class="w-36"> {{-- Pelaku --}}
                        <col class="w-32"> {{-- Role --}}
                        <col class="w-40"> {{-- Aksi --}}
                        <col class="w-1/4"> {{-- Tugas & Akun --}}
                        <col class="w-1/3"> {{-- Catatan --}}
                    </colgroup>
                    <thead>
                        <tr
                            class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            <th class="px-4 py-3 text-left">Waktu</th>
                            <th class="px-4 py-3 text-left">Pelaku</th>
                            <th class="px-4 py-3 text-left">Role</th>
                            <th class="px-4 py-3 text-left">Aksi</th>
                            <th class="px-4 py-3 text-left">Tugas & Akun</th>
                            <th class="px-4 py-3 text-left">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($logs as $log)
                            <tr class="hover:bg-gray-50/80 transition align-middle">
                                <td class="px-4 py-3.5 text-xs text-gray-500 whitespace-nowrap">
                                    {{ $log->created_at->translatedFormat('d M Y, H:i') }}
                                </td>
                                <td class="px-4 py-3.5 text-xs font-semibold text-gray-800 whitespace-nowrap"
                                    title="{{ $log->user_name ?? $log->user?->name }}">
                                    {{ $log->user_name ?? $log->user?->name ?? 'System' }}
                                </td>
                                <td class="px-4 py-3.5 text-xs text-gray-600 whitespace-nowrap">
                                    {{ $log->role_name ?? $log->user?->role_label ?? '—' }}
                                </td>
                                <td class="px-4 py-3.5 text-xs whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium {{ $log->action_badge_class }}">
                                        {{ $log->action_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-xs">
                                    <p class="font-medium text-gray-800 truncate" title="{{ $log->task?->title }}">
                                        {{ $log->task?->title ?? '—' }}
                                    </p>
                                    @if($log->task?->account)
                                        <p class="text-gray-400 mt-0.5">{{ $log->task->account->name }}
                                            ({{ $log->task->account->platform }})</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-xs text-gray-600 leading-relaxed">
                                    {{ $log->notes ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400">Belum ada riwayat audit log.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards (< md) --}} <div class="md:hidden space-y-3">
                @forelse($logs as $log)
                    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-800 text-xs">
                                    {{ $log->user_name ?? $log->user?->name ?? 'System' }} <span
                                        class="text-gray-400 font-normal">({{ $log->role_name ?? $log->user?->role_label ?? '—' }})</span>
                                </p>
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $log->created_at->translatedFormat('d M Y, H:i') }}
                                </p>
                            </div>
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium whitespace-nowrap {{ $log->action_badge_class }}">
                                {{ $log->action_label }}
                            </span>
                        </div>
                        <div class="border-t border-gray-100 pt-2 mt-2 text-xs">
                            <p class="font-medium text-gray-800 truncate">{{ $log->task?->title ?? '—' }}</p>
                            @if($log->notes)
                                <p class="text-gray-500 mt-1 italic text-[11px] bg-gray-50 p-2 rounded">"{{ $log->notes }}"</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-sm text-gray-400">Belum ada riwayat audit log.</div>
                @endforelse
        </div>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>
        </div>
    @endif
    </div>

    {{-- ── MODAL CREATE ACCOUNT (ADMIN ONLY) ───────────────────────── --}}
    <div id="modal-create-account" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            onclick="document.getElementById('modal-create-account').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-bold text-gray-800">Tambah Akun Sosial Media Baru</h3>
                <button onclick="document.getElementById('modal-create-account').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.sosmed.accounts.store') }}" class="p-6 pt-1 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Akun / Username <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: @seveninc_official"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Platform <span
                            class="text-red-500">*</span></label>
                    <select name="platform" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="">-- Pilih Platform --</option>
                        @foreach(['Instagram', 'TikTok', 'YouTube', 'Facebook', 'Twitter/X', 'LinkedIn', 'Threads', 'Website'] as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Link URL Akun</label>
                    <input type="url" name="link" placeholder="https://instagram.com/seveninc_official"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">1. Eksekutor Akun (Dikelola Oleh) <span
                            class="text-gray-400 font-normal">(Opsional)</span></label>
                    <select name="staff_id" id="create-acc-staff" onchange="syncSupervisorState(this, 'create-acc-pm', 'create-pm-hint')"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="" data-role="">-- Belum Ditugaskan --</option>
                        @foreach($executors as $ex)
                            @php
                                $exRoleLabel = match($ex->role) {
                                    'pm' => 'PM Mandiri',
                                    'sosmed' => 'Staff Sosmed',
                                    'digital_marketing' => 'Digital Marketing',
                                    default => $ex->role_label ?? strtoupper($ex->role)
                                };
                            @endphp
                            <option value="{{ $ex->id }}" data-role="{{ $ex->role }}">
                                {{ $ex->name }} ({{ $exRoleLabel }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-gray-400 mt-1">Bisa ditugaskan ke Staff Sosmed maupun PM langsung sebagai
                        eksekutor mandiri.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">2. Supervisor Approval (Diawasi Oleh PM)
                        <span class="text-gray-400 font-normal">(Opsional)</span></label>
                    <select name="pm_id" id="create-acc-pm"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="">-- Tanpa Supervisor / Langsung ke HR --</option>
                        @foreach($pms as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }} (PM)</option>
                        @endforeach
                    </select>
                    <p id="create-pm-hint" class="text-[11px] text-gray-400 mt-1">PM yang berwenang meninjau & approve bukti postingan Staff.
                        Jika eksekutor adalah PM mandiri, bagian ini otomatis dinonaktifkan.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan / Briefing</label>
                    <textarea name="notes" rows="2" placeholder="Catatan seputar akun..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-create-account').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700">Batal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-semibold">Simpan
                        Akun</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── MODAL EDIT ACCOUNT (ADMIN) ───────────────────────────────── --}}
    <div id="modal-edit-account" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            onclick="document.getElementById('modal-edit-account').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg z-10 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-white z-10">
                <div>
                    <h3 class="text-base font-bold text-gray-800">Edit Akun Sosial Media</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Perbarui informasi akun, URL, eksekutor, dan supervisor</p>
                </div>
                <button onclick="document.getElementById('modal-edit-account').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="form-edit-account" method="POST" action="" class="p-6 pt-2 space-y-4">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Akun / Username <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" id="edit-acc-name" required placeholder="Contoh: @seveninc_official"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Platform <span
                            class="text-red-500">*</span></label>
                    <select name="platform" id="edit-acc-platform" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="">-- Pilih Platform --</option>
                        @foreach(['Instagram', 'TikTok', 'YouTube', 'Facebook', 'Twitter/X', 'LinkedIn', 'Threads', 'Website'] as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Link URL Akun</label>
                    <input type="url" name="link" id="edit-acc-link" placeholder="https://instagram.com/seveninc_official"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">1. Eksekutor Akun (Dikelola Oleh)</label>
                    <select name="staff_id" id="edit-acc-staff" onchange="syncSupervisorState(this, 'edit-acc-pm', 'edit-pm-hint')"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="" data-role="">-- Belum Ditugaskan --</option>
                        @foreach($executors as $ex)
                            @php
                                $exRoleLabel = match($ex->role) {
                                    'pm' => 'PM Mandiri',
                                    'sosmed' => 'Staff Sosmed',
                                    'digital_marketing' => 'Digital Marketing',
                                    default => $ex->role_label ?? strtoupper($ex->role)
                                };
                            @endphp
                            <option value="{{ $ex->id }}" data-role="{{ $ex->role }}">
                                {{ $ex->name }} ({{ $exRoleLabel }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-gray-400 mt-1">Pelaksana harian akun (Staff Sosmed atau PM mandiri).</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">2. Supervisor Approval (Diawasi Oleh
                        PM)</label>
                    <select name="pm_id" id="edit-acc-pm"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="">-- Tanpa Supervisor / Langsung ke HR --</option>
                        @foreach($pms as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }} (PM)</option>
                        @endforeach
                    </select>
                    <p id="edit-pm-hint" class="text-[11px] text-gray-400 mt-1">PM yang meng-approve tugas staff. Jika eksekutor adalah PM
                        mandiri, bagian ini otomatis dinonaktifkan.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan / Briefing</label>
                    <textarea name="notes" id="edit-acc-notes" rows="3" placeholder="Catatan seputar akun..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-edit-account').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">Batal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── MODAL ASSIGN ACCOUNT (ADMIN) ─────────────────────────────── --}}
    <div id="modal-assign" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            onclick="document.getElementById('modal-assign').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <div>
                    <h3 class="text-base font-bold text-gray-800">Atur Penugasan Akun</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Tentukan eksekutor harian dan supervisor approver</p>
                </div>
                <button onclick="document.getElementById('modal-assign').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="form-assign" method="POST" action="" class="p-6 space-y-4">
                @csrf @method('PATCH')
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                    <p class="text-xs text-gray-500 mb-0.5">Nama Akun Sosial Media</p>
                    <p id="assign-acc-name" class="text-sm font-bold text-gray-800"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">1. Eksekutor (Dikelola Oleh)</label>
                    <select name="staff_id" id="assign-staff-sel" onchange="syncSupervisorState(this, 'assign-pm-sel', 'assign-pm-hint')"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="" data-role="">-- Belum Ditugaskan --</option>
                        @foreach($executors as $ex)
                            @php
                                $exRoleLabel = match($ex->role) {
                                    'pm' => 'PM Mandiri',
                                    'sosmed' => 'Staff Sosmed',
                                    'digital_marketing' => 'Digital Marketing',
                                    default => $ex->role_label ?? strtoupper($ex->role)
                                };
                            @endphp
                            <option value="{{ $ex->id }}" data-role="{{ $ex->role }}">
                                {{ $ex->name }} ({{ $exRoleLabel }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-gray-400 mt-1">User yang bertugas memposting & mengunggah bukti konten.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">2. Supervisor (Diawasi Oleh PM)</label>
                    <select name="pm_id" id="assign-pm-sel"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="">-- Tanpa Supervisor / Langsung ke HR --</option>
                        @foreach($pms as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }} (PM)</option>
                        @endforeach
                    </select>
                    <p id="assign-pm-hint" class="text-[11px] text-gray-400 mt-1">PM yang berwenang meninjau & approve bukti pengerjaan Staff.
                        Jika eksekutor adalah PM mandiri, bagian ini otomatis dinonaktifkan.
                    </p>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-assign').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">Batal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">Simpan
                        Penugasan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── MODAL: DETAIL LINKS POPUP ────────────────────────────────── --}}
    <div id="modal-links" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            onclick="document.getElementById('modal-links').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <div>
                    <h3 class="text-base font-bold text-gray-800">Detail Bukti Konten</h3>
                    <p id="links-popup-title" class="text-xs text-gray-500 mt-0.5"></p>
                </div>
                <button onclick="document.getElementById('modal-links').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="links-popup-body" class="p-6 space-y-2 max-h-80 overflow-y-auto"></div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function syncSupervisorState(staffSelect, pmSelectId, hintId) {
            if (!staffSelect) return;
            const pmSelect = document.getElementById(pmSelectId);
            const hint = document.getElementById(hintId);
            if (!pmSelect) return;

            const selectedOption = staffSelect.options[staffSelect.selectedIndex];
            const role = selectedOption ? selectedOption.getAttribute('data-role') : null;

            if (role === 'pm') {
                pmSelect.value = '';
                pmSelect.disabled = true;
                pmSelect.classList.add('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
                if (hint) {
                    hint.innerHTML = '<span class="text-indigo-600 font-semibold">🔒 PM Mandiri:</span> Akun dikelola langsung oleh PM. Hasil pengerjaan otomatis lolos Level 1 dan langsung diverifikasi HR Staff (supervisor otomatis dinonaktifkan).';
                }
            } else {
                pmSelect.disabled = false;
                pmSelect.classList.remove('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
                if (hint) {
                    hint.innerHTML = 'PM yang berwenang meninjau & approve bukti postingan Staff. Jika eksekutor adalah PM mandiri, bagian ini otomatis dinonaktifkan.';
                }
            }
        }

        function openCreateAccountModal() {
            const staffSel = document.getElementById('create-acc-staff');
            if (staffSel) {
                staffSel.value = '';
                syncSupervisorState(staffSel, 'create-acc-pm', 'create-pm-hint');
            }
            document.getElementById('modal-create-account').classList.remove('hidden');
        }

        function openEditAccountModal(accId, accName, platform, link, currentPmId, currentStaffId, notes) {
            document.getElementById('form-edit-account').action = `/admin/sosmed/accounts/${accId}`;
            document.getElementById('edit-acc-name').value = accName;
            document.getElementById('edit-acc-platform').value = platform;
            document.getElementById('edit-acc-link').value = link || '';
            document.getElementById('edit-acc-staff').value = currentStaffId ?? '';
            document.getElementById('edit-acc-pm').value = currentPmId ?? '';
            document.getElementById('edit-acc-notes').value = notes || '';
            syncSupervisorState(document.getElementById('edit-acc-staff'), 'edit-acc-pm', 'edit-pm-hint');
            document.getElementById('modal-edit-account').classList.remove('hidden');
        }

        function openAssignModal(accId, accName, currentPmId, currentStaffId) {
            document.getElementById('assign-acc-name').textContent = accName;
            document.getElementById('form-assign').action = `/admin/sosmed/accounts/${accId}/assign`;
            document.getElementById('assign-staff-sel').value = currentStaffId ?? '';
            document.getElementById('assign-pm-sel').value = currentPmId ?? '';
            syncSupervisorState(document.getElementById('assign-staff-sel'), 'assign-pm-sel', 'assign-pm-hint');
            document.getElementById('modal-assign').classList.remove('hidden');
        }

        // Enable any disabled select before submitting forms so payload isn't dropped
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                this.querySelectorAll('select:disabled').forEach(sel => {
                    sel.disabled = false;
                });
            });
        });

        function openLinksPopup(links, title) {
            document.getElementById('links-popup-title').textContent = title;
            const body = document.getElementById('links-popup-body');
            body.innerHTML = '';
            if (!links || links.length === 0) {
                body.innerHTML = '<p class="text-sm text-gray-400 text-center">Tidak ada link bukti.</p>';
            } else {
                links.forEach((url, i) => {
                    const item = document.createElement('a');
                    item.href = url;
                    item.target = '_blank';
                    item.rel = 'noopener noreferrer';
                    item.className = 'flex items-start gap-2.5 p-3 rounded-lg border border-gray-100 hover:border-primary-300 hover:bg-primary-50/50 transition group';
                    item.innerHTML = `
                                <span class="flex-shrink-0 w-5 h-5 rounded-full bg-primary-100 text-primary-700 text-[10px] font-bold flex items-center justify-center mt-0.5">${i + 1}</span>
                                <span class="text-xs text-primary-700 group-hover:underline break-all leading-relaxed">${url}</span>
                                <svg class="w-3.5 h-3.5 flex-shrink-0 text-gray-400 group-hover:text-primary-600 mt-0.5 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>`;
                    body.appendChild(item);
                });
            }
            document.getElementById('modal-links').classList.remove('hidden');
        }
    </script>
@endpush