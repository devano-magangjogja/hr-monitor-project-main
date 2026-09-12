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
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3 mb-6">
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
        <div
            class="bg-white rounded-xl border {{ $stats['need_admin_verify'] > 0 ? 'border-purple-400 bg-purple-50/30 ring-2 ring-purple-400/30' : 'border-gray-200' }} p-4 shadow-sm">
            <p class="text-xs font-medium text-purple-700 mb-1">Verif Tugas Staff</p>
            <p class="text-2xl font-bold text-purple-600">{{ $stats['need_admin_verify'] }}</p>
            <p class="text-[11px] text-purple-600 mt-0.5">menunggu verif Admin</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs font-medium text-gray-500 mb-1">Verif Level 2 (HR)</p>
            <p class="text-2xl font-bold text-purple-600">{{ $stats['need_hr_verify'] }}</p>
            <p class="text-[11px] text-gray-400 mt-0.5">tugas menunggu HR</p>
        </div>

        <div class="col-span-1 bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
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
            <a href="{{ route('admin.sosmed.index', ['tab' => 'staff_approvals']) }}"
                class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 transition
                                                                                                                            {{ $tab === 'staff_approvals' ? 'border-purple-600 text-purple-600 bg-purple-50/50' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Verifikasi Tugas Staff
                @if($stats['need_admin_verify'] > 0)
                    <span class="ml-1 px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-purple-600 text-white">
                        {{ $stats['need_admin_verify'] }}
                    </span>
                @endif
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
                            <h3 class="text-sm font-semibold text-gray-800">Distribusi & Penugasan Akun Sosial Media</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Pemberian tugas pengelolaan akun sosial media kepada eksekutor
                                (Staff Sosmed / PM)</p>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <form action="{{ route('admin.sosmed.index') }}" method="GET" class="flex items-center gap-2">
                                <input type="hidden" name="tab" value="accounts">
                                <input type="text" name="account_search" value="{{ $accountSearch ?? '' }}"
                                    placeholder="Cari nama akun..."
                                    class="h-9 px-3 text-xs bg-white border border-gray-300 rounded-lg shadow-sm
                                                                                                                                           focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500
                                                                                                                                           text-gray-700 transition w-40 sm:w-auto">
                            </form>
                            <a href="{{ route('admin.accounts.index') }}"
                                class="inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs sm:text-sm font-medium rounded-lg transition border border-gray-300">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Manajemen Akun
                            </a>
                            <button onclick="openAssignTaskModal()"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs sm:text-sm font-semibold rounded-lg transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Beri Tugas
                            </button>
                        </div>
                    </div>

                    {{-- Desktop Table (md+) --}}
                    <div class="hidden md:block overflow-x-auto rounded-lg border border-gray-100">
                        <table class="w-full table-fixed text-sm">
                            <colgroup>
                                <col class="w-[20%]"> {{-- Nama Akun & Catatan --}}
                                <col class="w-28"> {{-- Platform --}}
                                <col class="w-36"> {{-- Link URL --}}
                                <col class="w-40"> {{-- Eksekutor --}}
                                <col class="w-40"> {{-- Supervisor PM --}}
                                <col class="w-40"> {{-- Asisten Pengawas --}}
                                <col class="w-28"> {{-- Aksi --}}
                            </colgroup>
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 tracking-wide">
                                    <th class="px-4 py-3 text-left">Nama Akun</th>
                                    <th class="px-4 py-3 text-left">Platform</th>
                                    <th class="px-4 py-3 text-left">URL</th>
                                    <th class="px-4 py-3 text-left">Dikelola</th>
                                    <th class="px-4 py-3 text-left">PM</th>
                                    <th class="px-4 py-3 text-left">Asisten Pengawas</th>
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
                                                    <span class="truncate">Buka</span>
                                                </a>
                                            @else
                                                <span class="text-xs text-gray-300">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-xs min-w-0">
                                            @if($acc->staffUser)
                                                @php
                                                    $staffRoleTag = match ($acc->staffUser->role) {
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
                                                    <span
                                                        class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold mt-1 {{ $acc->staffUser->role === 'pm' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-pink-50 text-pink-700 border border-pink-200' }}">
                                                        {{ $staffRoleTag }}
                                                    </span>
                                                </div>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 whitespace-nowrap">
                                                    Belum Ditugaskan
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-xs min-w-0">
                                            @if($acc->staffUser && $acc->staffUser->role === 'pm')
                                                <span class="text-gray-400 text-[11px] block">Langsung ke HR</span>
                                            @elseif($acc->pmUser)
                                                <div class="min-w-0">
                                                    <span class="font-semibold text-gray-800 block truncate"
                                                        title="{{ $acc->pmUser->name }}">
                                                        {{ $acc->pmUser->name }}
                                                    </span>
                                                    <span
                                                        class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold mt-1 bg-purple-50 text-purple-700 border border-purple-200">
                                                        Supervisor PM
                                                    </span>
                                                </div>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 whitespace-nowrap">
                                                    Belum Ada PM
                                                </span>
                                            @endif
                                        </td>
                                        {{-- Asisten Pengawas --}}
                                        <td class="px-4 py-3 text-xs min-w-0">
                                            @if($acc->assistantUser)
                                                <div class="flex items-start gap-2 min-w-0">
                                                    <div
                                                        class="w-6 h-6 rounded-full bg-teal-100 text-teal-700 font-bold flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5">
                                                        {{ strtoupper(substr($acc->assistantUser->name, 0, 1)) }}
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <span class="font-semibold text-gray-800 block truncate"
                                                            title="{{ $acc->assistantUser->name }}">
                                                            {{ $acc->assistantUser->name }}
                                                        </span>
                                                        <span
                                                            class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold mt-0.5 bg-teal-50 text-teal-700 border border-teal-200">
                                                            Asisten HR
                                                        </span>
                                                    </div>
                                                </div>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 whitespace-nowrap">
                                                    Tanpa Asisten
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3.5 text-center">
                                            <div class="flex items-center justify-center gap-1">
                                                <button type="button"
                                                    onclick="openEditAccountModal({{ $acc->id }}, '{{ addslashes($acc->name) }}', '{{ addslashes($acc->platform) }}', '{{ addslashes($acc->link ?? '') }}', {{ $acc->pm_id ?? 'null' }}, {{ $acc->staff_id ?? 'null' }}, '{{ addslashes(str_replace(["\r", "\n"], [' ', ' '], $acc->notes ?? '')) }}', {{ $acc->assistant_id ?? 'null' }})"
                                                    class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                                    title="Atur Penugasan">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                @if($acc->staff_id)
                                                    <form method="POST" action="{{ route('admin.sosmed.accounts.unassign', $acc) }}"
                                                        onsubmit="return confirm('Lepas penugasan untuk akun {{ addslashes($acc->name) }}? Akun tetap berada di daftar kelola sosmed dengan status belum ditugaskan.')"
                                                        class="inline">
                                                        @csrf
                                                        <button type="submit"
                                                            class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition"
                                                            title="Lepas Penugasan (Jadikan Belum Ditugaskan)">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                                <form method="POST" action="{{ route('admin.sosmed.accounts.destroy', $acc) }}"
                                                    onsubmit="return confirm('Hapus akun {{ addslashes($acc->name) }} dari daftar kelola sosmed? Data kredensial tetap aman di Manajemen Akun.')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                                        title="Hapus dari Kelola Sosmed">
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
                                        <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-400">Belum ada akun sosial media
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
                                            onclick="openEditAccountModal({{ $acc->id }}, '{{ addslashes($acc->name) }}', '{{ addslashes($acc->platform) }}', '{{ addslashes($acc->link ?? '') }}', {{ $acc->pm_id ?? 'null' }}, {{ $acc->staff_id ?? 'null' }}, '{{ addslashes(str_replace(["\r", "\n"], [' ', ' '], $acc->notes ?? '')) }}', {{ $acc->assistant_id ?? 'null' }})"
                                            class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                            title="Atur Penugasan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        @if($acc->staff_id)
                                            <form method="POST" action="{{ route('admin.sosmed.accounts.unassign', $acc) }}"
                                                onsubmit="return confirm('Lepas penugasan untuk akun {{ addslashes($acc->name) }}? Akun tetap berada di daftar kelola sosmed dengan status belum ditugaskan.')"
                                                class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition"
                                                    title="Lepas Penugasan (Jadikan Belum Ditugaskan)">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('admin.sosmed.accounts.destroy', $acc) }}"
                                            onsubmit="return confirm('Hapus akun {{ addslashes($acc->name) }} dari daftar kelola sosmed? Data kredensial tetap aman di Manajemen Akun.')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                                title="Hapus dari Kelola Sosmed">
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
                                                    $mRoleTag = match ($acc->staffUser->role) {
                                                        'pm' => 'PM Mandiri',
                                                        'sosmed' => 'Staff Sosmed',
                                                        'digital_marketing' => 'Digital Marketing',
                                                        default => $acc->staffUser->role_label ?? strtoupper($acc->staffUser->role)
                                                    };
                                                @endphp
                                                {{ $acc->staffUser->name }} <span
                                                    class="text-[10px] text-gray-500 font-normal">({{ $mRoleTag }})</span>
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

    {{-- ── TAB: VERIFIKASI TUGAS STAFF (ADMIN LANGSUNG) ────────────── --}}
    @if($tab === 'staff_approvals')
        <div class="p-4 sm:p-5">
            <div class="mb-4">
                <h3 class="text-sm font-semibold text-gray-800">Verifikasi Tugas Sosmed Staff</h3>
                <p class="text-xs text-gray-500 mt-0.5">Tugas sosial media yang dikerjakan oleh HR Staff diverifikasi langsung
                    oleh Administrator.</p>
            </div>

            <div class="space-y-3 mb-6">
                @forelse($staffPendingTasks as $task)
                    <div class="p-4 bg-purple-50/50 border border-purple-200 rounded-xl hover:border-purple-300 transition">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-semibold text-gray-800 text-sm">{{ $task->title }}</span>
                                    <span class="px-2 py-0.5 rounded-md text-xs font-medium border bg-white text-gray-700">
                                        {{ $task->account?->name }} ({{ $task->account?->platform }})
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[11px] bg-purple-100 text-purple-700 font-semibold">
                                        Role: HR Staff
                                    </span>
                                </div>
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-2 text-xs text-gray-500">
                                    <span>Dikerjakan oleh: <strong
                                            class="text-gray-800">{{ $task->assignedUser?->name ?? '-' }}</strong></span>
                                    <span class="hidden sm:inline">·</span>
                                    <span>Tanggal: {{ $task->task_date->translatedFormat('d M Y') }}</span>
                                    @if($task->hasLinks())
                                        <button type="button"
                                            onclick="openLinksPopup({{ json_encode($task->link_upload) }}, '{{ addslashes($task->title) }}')"
                                            class="text-primary-600 font-medium hover:underline inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                            </svg>
                                            {{ $task->link_count }} Bukti Link
                                        </button>
                                    @endif
                                </div>
                                @if($task->description)
                                    <p class="text-xs text-gray-600 mt-2 bg-white/80 p-2.5 rounded-lg border border-purple-100">
                                        {{ $task->description }}
                                    </p>
                                @endif
                            </div>
                            <div class="flex-shrink-0 self-center">
                                <button onclick="openVerifyModal({{ $task->id }}, '{{ addslashes($task->title) }}')"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded-xl transition shadow-sm whitespace-nowrap">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Verifikasi Admin
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-16 text-center">
                        <div class="w-16 h-16 rounded-full bg-green-50 flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-700">Semua Tugas Staff Sudah Diverifikasi</p>
                        <p class="text-xs text-gray-400 mt-1 max-w-xs">
                            Tidak ada tugas sosmed Staff yang menunggu verifikasi Admin saat ini.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    {{-- ── TAB 2: MONITORING SELURUH TUGAS ────────────────────────── --}}
    @if($tab === 'tasks')
        <div class="p-4 sm:p-5">
            {{-- Top Bar Filter & Action Buttons --}}
            <div class="mb-4 flex flex-col sm:flex-row items-stretch sm:items-center sm:justify-end gap-2.5 sm:gap-3">

                {{-- Filter Tanggal --}}
                <form action="{{ route('admin.sosmed.index') }}" method="GET" class="w-full sm:w-auto">
                    <input type="hidden" name="tab" value="tasks">
                    <input type="date" name="task_date" value="{{ $taskDateFilter }}"
                        class="w-full sm:w-auto h-9 px-3 text-xs bg-white border border-gray-300 rounded-lg shadow-sm
                                                                                                                                                                           focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500
                                                                                                                                                                           text-gray-700 transition"
                        onchange="this.form.submit()">
                </form>

                {{-- Action Buttons (Grid di Mobile, Inline di Tablet/Desktop) --}}
                <div class="grid grid-cols-2 sm:flex sm:items-center gap-2">
                    {{-- Cetak PDF --}}
                    <button onclick="window.print()"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 h-9 px-3 sm:px-3.5 bg-primary-600 hover:bg-primary-700 active:bg-primary-800
                                                                                                                                                                           text-white text-xs font-medium rounded-lg transition shadow-sm">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Cetak PDF</span>
                    </button>

                    {{-- Hapus Data --}}
                    <div class="relative w-full sm:w-auto" id="purgeDropdown">
                        <button type="button" onclick="togglePurgeDropdown()"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 h-9 px-3 sm:px-3.5 bg-white border border-red-200 text-red-600
                                                                                                                                                                               hover:bg-red-50 hover:border-red-300 active:bg-red-100
                                                                                                                                                                               text-xs font-medium rounded-lg transition shadow-sm">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>Hapus Data</span>
                            <svg id="purgeChevron" class="w-3.5 h-3.5 text-red-400 transition-transform duration-200 shrink-0"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        {{-- Dropdown --}}
                        <div id="purgeMenu"
                            class="absolute right-0 mt-2 w-56 bg-white border border-gray-100 rounded-xl shadow-lg
                                                                                                                                                                                                opacity-0 invisible translate-y-1
                                                                                                                                                                                                transition-all duration-150 z-20 overflow-hidden">
                            <div class="px-3.5 py-2.5 bg-gray-50 border-b border-gray-100">
                                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Hapus data tugas</p>
                            </div>
                            <div class="p-1.5 space-y-0.5">
                                <form action="{{ route('admin.sosmed.tasks.purge') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" name="range" value="weekly"
                                        class="w-full text-left px-3 py-2 text-xs text-red-600 hover:bg-red-50 rounded-lg transition"
                                        onclick="return confirm('Hapus data sebelum minggu ini?')">
                                        Lebih lama dari 1 Minggu
                                    </button>
                                    <button type="submit" name="range" value="monthly"
                                        class="w-full text-left px-3 py-2 text-xs text-red-600 hover:bg-red-50 rounded-lg transition"
                                        onclick="return confirm('Hapus data sebelum bulan ini?')">
                                        Lebih lama dari 1 Bulan
                                    </button>
                                    <button type="submit" name="range" value="yearly"
                                        class="w-full text-left px-3 py-2 text-xs text-red-600 hover:bg-red-50 rounded-lg transition"
                                        onclick="return confirm('Hapus data sebelum tahun ini?')">
                                        Lebih lama dari 1 Tahun
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
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
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 tracking-wide">
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
            {{-- Top Bar Filter & Action Buttons --}}
            <div class="mb-5 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 sm:gap-4">

                {{-- Form Filter --}}
                <form action="{{ route('admin.sosmed.index') }}" method="GET"
                    class="flex flex-col sm:flex-row sm:flex-wrap items-stretch sm:items-center gap-2">
                    <input type="hidden" name="tab" value="logs">

                    {{-- Search Assignee --}}
                    <div class="relative w-full sm:w-auto">
                        <input type="text" name="log_search" value="{{ $logSearch ?? '' }}" placeholder="Cari nama assignee..."
                            class="w-full sm:w-44 h-9 px-3 text-xs bg-white border border-gray-300 rounded-lg shadow-sm
                                                                                                                                                               focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 text-gray-700 transition">
                    </div>

                    {{-- Filter Aksi --}}
                    <select name="log_action"
                        class="w-full sm:w-auto h-9 pl-3 pr-8 text-xs bg-white border border-gray-300 rounded-lg shadow-sm
                                                                                                                                                                                 focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 text-gray-700 transition"
                        onchange="this.form.submit()">
                        <option value="">Semua Aksi</option>
                        <option value="submitted" {{ request('log_action') === 'submitted' ? 'selected' : '' }}>Selesai Dikerjakan
                        </option>
                        <option value="approved_pm" {{ request('log_action') === 'approved_pm' ? 'selected' : '' }}>Diverifikasi
                            PM</option>
                        <option value="approved_hr" {{ request('log_action') === 'approved_hr' ? 'selected' : '' }}>Disetujui HR
                            Staff</option>
                        <option value="rejected" {{ request('log_action') === 'rejected' ? 'selected' : '' }}>Ditolak / Revisi
                        </option>
                    </select>

                    {{-- Filter Rentang Waktu --}}
                    <select name="log_range"
                        class="w-full sm:w-auto h-9 pl-3 pr-8 text-xs bg-white border border-gray-300 rounded-lg shadow-sm
                                                                                                                                                                                focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 text-gray-700 transition"
                        onchange="this.form.submit()">
                        <option value="">Semua Waktu</option>
                        <option value="weekly" {{ request('log_range') === 'weekly' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="monthly" {{ request('log_range') === 'monthly' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="yearly" {{ request('log_range') === 'yearly' ? 'selected' : '' }}>Tahun Ini</option>
                    </select>

                    {{-- Filter Tanggal --}}
                    <input type="date" name="log_date" value="{{ $logDateFilter ?? '' }}"
                        class="w-full sm:w-auto h-9 px-3 text-xs bg-white border border-gray-300 rounded-lg shadow-sm
                                                                                                                                                           focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 text-gray-700 transition"
                        onchange="this.form.submit()">

                    {{-- Submit & Reset (Grouped di Mobile) --}}
                    <div class="grid grid-cols-2 sm:flex items-center gap-2 mt-1 sm:mt-0">
                        <button type="submit"
                            class="h-9 px-3.5 bg-primary-600 hover:bg-primary-700 active:bg-primary-800 text-white text-xs font-medium rounded-lg transition shadow-sm text-center">
                            Terapkan
                        </button>
                        <a href="{{ route('admin.sosmed.index', ['tab' => 'logs']) }}"
                            class="h-9 px-3.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition text-center inline-flex items-center justify-center">
                            Reset
                        </a>
                    </div>
                </form>

                {{-- Action Buttons (Aksi Ekspor & Hapus) --}}
                <div class="grid grid-cols-2 lg:flex lg:items-center gap-2 pt-2 lg:pt-0 border-t border-gray-100 lg:border-t-0">
                    {{-- Cetak PDF --}}
                    <button onclick="window.print()"
                        class="w-full lg:w-auto inline-flex items-center justify-center gap-2 h-9 px-3.5 bg-primary-600 hover:bg-primary-700 active:bg-primary-800 text-white text-xs font-medium rounded-lg transition shadow-sm">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Cetak PDF</span>
                    </button>

                    {{-- Hapus Data Dropdown --}}
                    <div class="relative w-full lg:w-auto" id="logsPurgeDropdown">
                        <button type="button" onclick="toggleLogsPurgeDropdown()"
                            class="w-full lg:w-auto inline-flex items-center justify-center gap-2 h-9 px-3.5 bg-white border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 active:bg-red-100 text-xs font-medium rounded-lg transition shadow-sm">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>Hapus Data</span>
                            <svg id="logsPurgeChevron"
                                class="w-3.5 h-3.5 text-red-400 transition-transform duration-200 shrink-0" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        {{-- Dropdown Menu --}}
                        <div id="logsPurgeMenu"
                            class="absolute right-0 mt-2 w-56 bg-white border border-gray-100 rounded-xl shadow-lg opacity-0 invisible translate-y-1 transition-all duration-150 z-20 overflow-hidden">
                            <div class="px-3.5 py-2.5 bg-gray-50 border-b border-gray-100">
                                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Hapus data log</p>
                            </div>
                            <div class="p-1.5 space-y-0.5">
                                <form action="{{ route('admin.sosmed.logs.purge') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" name="range" value="weekly"
                                        class="w-full text-left px-3 py-2 text-xs text-red-600 hover:bg-red-50 rounded-lg transition"
                                        onclick="return confirm('Hapus data sebelum minggu ini?')">
                                        Lebih lama dari 1 Minggu
                                    </button>
                                    <button type="submit" name="range" value="monthly"
                                        class="w-full text-left px-3 py-2 text-xs text-red-600 hover:bg-red-50 rounded-lg transition"
                                        onclick="return confirm('Hapus data sebelum bulan ini?')">
                                        Lebih lama dari 1 Bulan
                                    </button>
                                    <button type="submit" name="range" value="yearly"
                                        class="w-full text-left px-3 py-2 text-xs text-red-600 hover:bg-red-50 rounded-lg transition"
                                        onclick="return confirm('Hapus data sebelum tahun ini?')">
                                        Lebih lama dari 1 Tahun
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
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
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 tracking-wide">
                            <th class="px-4 py-3 text-left">Waktu</th>
                            <th class="px-4 py-3 text-left">Assign</th>
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
                                    {{ $log->user_name ?? $log->user?->name ?? 'System' }}
                                    <span
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

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $logs->links() }}
        </div>
        </div>
    @endif

    {{-- ── MODAL BERI TUGAS PENGELOLAAN SOSMED (ADMIN) ─────────────── --}}
    <div id="modal-assign-task" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            onclick="document.getElementById('modal-assign-task').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg z-10 max-h-[90vh] flex flex-col overflow-visible"
            x-data="assignTaskDropdown({{ json_encode($availableAccounts) }})">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <div>
                    <h3 class="text-base font-bold text-gray-800">Beri Tugas Pengelolaan Sosmed</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Tugaskan akun yang belum dikelola kepada eksekutor</p>
                </div>
                <button type="button" onclick="document.getElementById('modal-assign-task').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.sosmed.assign') }}" class="p-6 pt-1 space-y-4 overflow-y-auto">
                @csrf

                {{-- Dropdown Searchable: Pilih Akun yang Tersedia --}}
                <div class="relative">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Pilih Akun yang Dikelola <span class="text-red-500">*</span>
                    </label>
                    <input type="hidden" name="sosmed_account_id" :value="selectedId" required>

                    {{-- Dropdown Trigger --}}
                    <button type="button" @click="open = !open"
                        class="w-full flex items-center justify-between border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white hover:bg-gray-50 focus:ring-2 focus:ring-primary-500 focus:outline-none transition">
                        <span x-show="selectedId" class="font-medium text-gray-800" x-text="selectedLabel"></span>
                        <span x-show="!selectedId" class="text-gray-400">-- Pilih Akun Tersedia --</span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-150"
                            :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    {{-- Dropdown Menu (Searchable) --}}
                    <div x-show="open" @click.away="open = false" x-cloak
                        class="absolute z-50 left-0 right-0 mt-1 bg-white border border-gray-200 rounded-xl shadow-xl max-h-60 overflow-hidden flex flex-col">
                        {{-- Search Input --}}
                        <div class="p-2 border-b border-gray-100 bg-gray-50/80 sticky top-0">
                            <div class="relative">
                                <span
                                    class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none text-gray-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </span>
                                <input type="text" x-model="search" placeholder="Cari nama akun atau platform..."
                                    class="w-full pl-8 pr-3 py-1.5 text-xs bg-white border border-gray-200 rounded-lg focus:ring-1 focus:ring-primary-500 focus:outline-none">
                            </div>
                        </div>

                        {{-- Options List --}}
                        <div class="overflow-y-auto max-h-48 divide-y divide-gray-50">
                            <template x-for="acc in filteredAccounts" :key="acc.id">
                                <button type="button" @click="selectAccount(acc)"
                                    class="w-full text-left px-3 py-2 text-xs hover:bg-primary-50 hover:text-primary-700 transition flex items-center justify-between"
                                    :class="selectedId == acc.id ? 'bg-primary-50/70 font-semibold text-primary-700' : 'text-gray-700'">
                                    {{-- Hanya nama akun dan platform --}}
                                    <span x-text="`${acc.name} (${acc.platform})`"></span>
                                    <span x-show="selectedId == acc.id" class="text-primary-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    </span>
                                </button>
                            </template>

                            <div x-show="filteredAccounts.length === 0"
                                class="p-4 text-center text-xs text-gray-400 italic">
                                <span x-show="accounts.length === 0">Semua akun sudah dimasukkan ke daftar kelola sosmed
                                    atau belum ada akun di
                                    Manajemen Akun.</span>
                                <span x-show="accounts.length > 0">Tidak ada akun yang cocok dengan pencarian.</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Link Akun (Readonly & Auto-filled) --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Link Akun <span class="text-xs text-gray-400 font-normal"></span>
                    </label>
                    <div class="relative">
                        <input type="text" readonly :value="selectedLink || '-'"
                            class="w-full bg-gray-100/80 border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-600 cursor-not-allowed select-all focus:outline-none">
                        <template x-if="selectedLink">
                            <a :href="selectedLink" target="_blank" rel="noopener noreferrer"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 text-primary-600 hover:text-primary-800"
                                title="Buka Link di Tab Baru">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        </template>
                    </div>
                </div>

                {{-- Eksekutor Akun --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Eksekutor Akun (Dikelola Oleh) <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>
                    <select name="staff_id" id="assign-task-staff"
                        onchange="syncSupervisorState(this, 'assign-task-pm', 'assign-task-pm-hint', 'assign-task-ast')"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="" data-role="">-- Belum Ditugaskan / Pilih Nanti --</option>
                        @foreach($executors as $ex)
                            @php
                                $exRoleLabel = match ($ex->role) {
                                    'pm' => 'PM Mandiri',
                                    'sosmed' => 'Staff Sosmed',
                                    'digital_marketing' => 'Digital Marketing',
                                    default => $ex->role_label ?? strtoupper($ex->role)
                                };
                            @endphp
                            <option value="{{ $ex->id }}" data-role="{{ $ex->role }}">{{ $ex->name }} ({{ $exRoleLabel }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Supervisor PM --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Supervisor PM <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>
                    <select name="pm_id" id="assign-task-pm"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="">-- Tanpa Supervisor / Langsung ke Admin --</option>
                        @foreach($pms as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }} (PM)</option>
                        @endforeach
                    </select>
                    <p id="assign-task-pm-hint" class="text-[11px] text-gray-400 mt-1">PM yang berwenang meninjau & approve
                        bukti postingan Staff.</p>
                </div>

                {{-- Asisten Pengawas --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Asisten Pengawas <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>
                    <select name="assistant_id" id="assign-task-ast"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="">-- Tanpa Asisten --</option>
                        @foreach($assistants as $ast)
                            <option value="{{ $ast->id }}">{{ $ast->name }} (Asisten)</option>
                        @endforeach
                    </select>
                </div>

                {{-- Catatan / Arahan --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Catatan / Arahan Penugasan</label>
                    <textarea name="notes" rows="2" placeholder="Catatan atau instruksi pengelolaan akun..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none"></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-assign-task').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">Batal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">Tambahkan
                        ke Sosmed</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── MODAL CREATE ACCOUNT (ADMIN ONLY) ───────────────────────── --}}
    <div id="modal-create-account" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            onclick="document.getElementById('modal-create-account').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10 max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-bold text-gray-800">Tambah Akun Sosial Media Baru</h3>
                <button onclick="document.getElementById('modal-create-account').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.sosmed.accounts.store') }}"
                class="p-6 pt-1 space-y-4 overflow-y-auto">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Akun / Username <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: @republikweb_net"
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
                    <input type="url" name="link" placeholder="https://instagram.com/republikweb_net"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Dikelola Oleh <span
                            class="text-gray-400 font-normal">(Opsional)</span></label>
                    <select name="staff_id" id="create-acc-staff"
                        onchange="syncSupervisorState(this, 'create-acc-pm', 'create-pm-hint')"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="" data-role="">-- Belum Ditugaskan --</option>
                        @foreach($executors as $ex)
                            @php
                                $exRoleLabel = match ($ex->role) {
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
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Diawasi Oleh PM
                        <span class="text-gray-400 font-normal"> (Opsional)</span></label>
                    <select name="pm_id" id="create-acc-pm"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="">-- Tanpa Supervisor --</option>
                        @foreach($pms as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }} (PM)</option>
                        @endforeach
                    </select>
                    <p id="create-pm-hint" class="text-[11px] text-gray-400 mt-1">PM yang berwenang meninjau & approve bukti
                        postingan Staff.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Wewenang Verifikasi Asisten </label>
                    <select name="assistant_id" id="create-acc-ast"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="">-- Tanpa Asisten --</option>
                        @foreach($assistants as $ast)
                            <option value="{{ $ast->id }}">{{ $ast->name }} (Asisten)</option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-gray-400 mt-1">Asisten HR yang berwenang approve tugas sebagai backup PM.</p>
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

    {{-- ── MODAL EDIT DELEGASI AKUN (ADMIN) ─────────────────────────── --}}
    <div id="modal-edit-account" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
        x-data="editAccountDropdown({{ json_encode($availableAccounts) }})"
        @open-edit-account.window="initAccount($event.detail)">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEditAccountModal()"></div>
        <div
            class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg z-10 max-h-[90vh] flex flex-col overflow-visible">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <div>
                    <h3 class="text-base font-bold text-gray-800">Atur Pengelola & Penugasan Akun</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Perbarui akun yang dikelola, eksekutor, supervisor, dan arahan
                    </p>
                </div>
                <button type="button" onclick="closeEditAccountModal()"
                    class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="form-edit-account" method="POST" action="" class="p-6 pt-1 space-y-4 overflow-y-auto">
                @csrf
                @method('PATCH')

                {{-- Akun yang Dikelola (Readonly / Disabled) --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Akun yang Dikelola <span class="text-xs text-gray-400 font-normal">(Tidak dapat diubah)</span>
                    </label>
                    <input type="hidden" name="sosmed_account_id" :value="selectedId">
                    <input type="text" readonly :value="selectedLabel"
                        class="w-full bg-gray-100/80 border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-600 cursor-not-allowed select-all focus:outline-none">
                </div>

                {{-- Link Akun (Readonly & Auto-filled) --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Link Akun <span class="text-xs text-gray-400 font-normal">(Otomatis terisi & tidak dapat
                            diubah)</span>
                    </label>
                    <div
                        class="relative flex items-center w-full bg-gray-100/80 border border-gray-300 rounded-lg focus-within:ring-2 focus-within:ring-primary-500">
                        <input type="text" readonly :value="selectedLink || '-'"
                            class="w-full bg-transparent border-none px-3 py-2 text-sm text-gray-600 cursor-not-allowed select-all focus:outline-none focus:ring-0">
                        <template x-if="selectedLink && selectedLink !== '-'">
                            <a :href="selectedLink" target="_blank" rel="noopener noreferrer"
                                class="shrink-0 mr-2.5 p-1 text-primary-600 hover:text-primary-800"
                                title="Buka Link di Tab Baru">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        </template>
                    </div>
                </div>

                {{-- Eksekutor Akun --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Eksekutor Akun (Dikelola Oleh) <span class="text-red-500">*</span>
                    </label>
                    <select name="staff_id" id="edit-acc-staff" required
                        onchange="syncSupervisorState(this, 'edit-acc-pm', 'edit-pm-hint', 'edit-acc-ast')"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="" data-role="">-- Belum Ditugaskan --</option>
                        @foreach($executors as $ex)
                            @php
                                $exRoleLabel = match ($ex->role) {
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
                    <p id="edit-staff-note" class="text-[11px] text-gray-400 mt-1">Pelaksana harian akun (Staff Sosmed atau
                        PM mandiri).</p>
                </div>

                {{-- Supervisor PM --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Supervisor PM <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>
                    <select name="pm_id" id="edit-acc-pm"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="">-- Tanpa Supervisor / Langsung ke Admin --</option>
                        @foreach($pms as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }} (PM)</option>
                        @endforeach
                    </select>
                    <p id="edit-pm-hint" class="text-[11px] text-gray-400 mt-1">PM yang berwenang meninjau & approve bukti
                        postingan Staff.</p>
                </div>

                {{-- Asisten Pengawas --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Asisten Pengawas <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>
                    <select name="assistant_id" id="edit-acc-ast"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="">-- Tanpa Asisten / Belum Diberi Wewenang --</option>
                        @foreach($assistants as $ast)
                            <option value="{{ $ast->id }}">{{ $ast->name }} (Asisten)</option>
                        @endforeach
                    </select>
                    <p id="edit-ast-note" class="text-[11px] text-gray-400 mt-1">Asisten HR yang berwenang meninjau &
                        approve tugas akun ini sebagai backup PM.</p>
                </div>

                {{-- Catatan / Arahan Penugasan --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Catatan / Arahan Penugasan</label>
                    <textarea name="notes" id="edit-acc-notes" rows="2" placeholder="Catatan seputar akun..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none"></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeEditAccountModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">Batal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">Simpan
                        Perubahan</button>
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

    {{-- ── MODAL VERIFIKASI TUGAS STAFF (ADMIN LANGSUNG) ────────────── --}}
    <div id="modal-verify" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
        x-data="{ action: 'verify' }" @open-verify.window="action = 'verify'">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeVerifyModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10 overflow-hidden">
            <div class="px-6 py-4 flex items-center justify-between transition-colors"
                :class="action === 'verify' ? 'bg-purple-50 border-b border-purple-100' : 'bg-rose-50 border-b border-rose-100'">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors"
                        :class="action === 'verify' ? 'bg-purple-100 text-purple-600' : 'bg-rose-100 text-rose-600'">
                        <template x-if="action === 'verify'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </template>
                        <template x-if="action === 'reject'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </template>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-800"
                            x-text="action === 'verify' ? 'Verifikasi Tugas Staff (Admin)' : 'Tolak & Kembalikan Tugas Staff'">
                        </h3>
                        <p class="text-[11px] text-gray-400">Verifikasi langsung hasil pengerjaan sosmed oleh HR Staff</p>
                    </div>
                </div>
                <button type="button" onclick="closeVerifyModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="form-verify" method="POST" action="" class="p-6 pt-1 space-y-4">
                @csrf @method('PATCH')
                <input type="hidden" name="action" :value="action">

                <div class="bg-gray-50 rounded-xl p-3.5 border border-gray-200/80">
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Tugas yang Ditinjau</p>
                    <p id="verify-task-title" class="text-sm font-bold text-gray-800 break-words"></p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-2">Keputusan Administrator</label>
                    <div class="grid grid-cols-2 gap-2 p-1 bg-gray-100 rounded-xl">
                        <button type="button" @click="action = 'verify'"
                            :class="action === 'verify' ? 'bg-white text-purple-700 shadow-sm font-bold' : 'text-gray-500 font-medium hover:text-gray-700'"
                            class="py-2 text-xs rounded-lg transition-all flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Setujui
                        </button>
                        <button type="button" @click="action = 'reject'"
                            :class="action === 'reject' ? 'bg-white text-rose-700 shadow-sm font-bold' : 'text-gray-500 font-medium hover:text-gray-700'"
                            class="py-2 text-xs rounded-lg transition-all flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Tolak (Revisi)
                        </button>
                    </div>
                </div>

                <div x-show="action === 'reject'" x-transition>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Catatan Revisi <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="rejection_note" rows="3" placeholder="Tuliskan instruksi perbaikan untuk HR Staff..."
                        class="w-full text-xs sm:text-sm border border-rose-200 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-rose-400 bg-rose-50/20"></textarea>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <button type="button" onclick="closeVerifyModal()"
                        class="flex-1 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit"
                        :class="action === 'verify' ? 'bg-purple-600 hover:bg-purple-700 text-white' : 'bg-rose-600 hover:bg-rose-700 text-white'"
                        class="flex-1 py-2 text-xs font-semibold rounded-xl transition shadow-sm">
                        <span x-text="action === 'verify' ? 'Konfirmasi Setujui' : 'Kirim Penolakan'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Auto-upgrade select elements to have an integrated search dropdown
            const selectsToUpgrade = document.querySelectorAll(
                'select[id="create-acc-staff"], select[id="create-acc-pm"], select[id="create-acc-ast"], ' +
                'select[id="edit-acc-staff"], select[id="edit-acc-pm"], select[id="edit-acc-ast"], ' +
                'select[id="assign-task-staff"], select[id="assign-task-pm"], select[id="assign-task-ast"]'
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
                button.className = 'w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-left flex justify-between items-center transition ' +
                    (select.disabled ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-800 focus:ring-2 focus:ring-primary-500 focus:outline-none');

                const label = document.createElement('span');
                label.className = 'truncate block';
                label.textContent = select.options[select.selectedIndex]?.text || '';

                button.innerHTML = `<svg class="w-4 h-4 text-gray-500 shrink-0 ml-2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>`;
                button.prepend(label);

                const dropdown = document.createElement('div');
                dropdown.className = 'absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl flex flex-col hidden';

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
                        button.className = 'w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-left flex justify-between items-center transition bg-gray-100 text-gray-400 cursor-not-allowed';
                    } else {
                        button.className = 'w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-left flex justify-between items-center transition bg-white text-gray-800 focus:ring-2 focus:ring-primary-500 focus:outline-none';
                    }
                });
                observer.observe(select, { attributes: true, attributeFilter: ['disabled'] });
            });
        });

        function toggleLogsPurgeDropdown() {
            const menu = document.getElementById('logsPurgeMenu');
            const chevron = document.getElementById('logsPurgeChevron');
            const isOpen = !menu.classList.contains('invisible');

            if (isOpen) {
                closeLogsPurgeDropdown();
            } else {
                menu.classList.remove('opacity-0', 'invisible', 'translate-y-1');
                chevron.classList.add('rotate-180');
            }
        }

        function closeLogsPurgeDropdown() {
            const menu = document.getElementById('logsPurgeMenu');
            const chevron = document.getElementById('logsPurgeChevron');
            menu.classList.add('opacity-0', 'invisible', 'translate-y-1');
            chevron.classList.remove('rotate-180');
        }

        document.addEventListener('click', function (event) {
            const dropdown = document.getElementById('logsPurgeDropdown');
            if (dropdown && !dropdown.contains(event.target)) {
                closeLogsPurgeDropdown();
            }
        });

        function togglePurgeDropdown() {
            const menu = document.getElementById('purgeMenu');
            const chevron = document.getElementById('purgeChevron');
            const isOpen = !menu.classList.contains('invisible');

            if (isOpen) {
                closePurgeDropdown();
            } else {
                menu.classList.remove('opacity-0', 'invisible', 'translate-y-1');
                chevron.classList.add('rotate-180');
            }
        }

        function closePurgeDropdown() {
            const menu = document.getElementById('purgeMenu');
            const chevron = document.getElementById('purgeChevron');
            menu.classList.add('opacity-0', 'invisible', 'translate-y-1');
            chevron.classList.remove('rotate-180');
        }

        // Tutup dropdown kalau klik di luar area dropdown
        document.addEventListener('click', function (event) {
            const dropdown = document.getElementById('purgeDropdown');
            if (dropdown && !dropdown.contains(event.target)) {
                closePurgeDropdown();
            }
        });

        // The old functions are now deprecated but kept empty to avoid breaking legacy onclick handlers
        function filterSelectOptions(query, selectId) { }
        function resetSearchFilter(inputId, selectId) { }

        function syncSupervisorState(staffSelect, pmSelectId, hintId, astSelectId) {
            if (!staffSelect) return;
            const pmSelect = document.getElementById(pmSelectId);
            const astSelect = astSelectId ? document.getElementById(astSelectId) : document.getElementById(staffSelect.id.replace('staff', 'ast'));
            const hint = document.getElementById(hintId);
            if (!pmSelect) return;

            const selectedOption = staffSelect.options[staffSelect.selectedIndex];
            const role = selectedOption ? selectedOption.getAttribute('data-role') : null;

            if (role === 'pm') {
                // Ketika PM langsung, disable PM dan Asisten dropdown
                pmSelect.value = '';
                pmSelect.disabled = true;
                pmSelect.classList.add('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');

                if (astSelect) {
                    astSelect.value = '';
                    astSelect.disabled = true;
                    astSelect.classList.add('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
                }

                if (hint) {
                    hint.innerHTML = '<span class="text-indigo-700 font-semibold">🔒 PM Mandiri:</span> Akun dikelola langsung oleh PM. Hasil pengerjaan diverifikasi oleh HR Staff.';
                }
            } else if (role === 'hr_staff') {
                // Ketika HR Staff langsung, tugas diverifikasi langsung oleh Admin
                pmSelect.value = '';
                pmSelect.disabled = true;
                pmSelect.classList.add('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');

                if (astSelect) {
                    astSelect.value = '';
                    astSelect.disabled = true;
                    astSelect.classList.add('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
                }

                if (hint) {
                    hint.innerHTML = '<span class="text-purple-700 font-semibold">🔒 HR Staff:</span> Akun dikelola langsung oleh HR Staff. Tugas diverifikasi langsung oleh Admin.';
                }
            } else if (role === 'hr_assistant') {
                // Ketika HR Assistant langsung, tugas diverifikasi oleh HR Staff
                pmSelect.value = '';
                pmSelect.disabled = true;
                pmSelect.classList.add('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');

                if (astSelect) {
                    astSelect.value = '';
                    astSelect.disabled = true;
                    astSelect.classList.add('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
                }

                if (hint) {
                    hint.innerHTML = '<span class="text-blue-700 font-semibold">🔒 HR Assistant:</span> Akun dikelola langsung oleh Asisten. Tugas diverifikasi oleh HR Staff.';
                }
            } else {
                // Ketika Staff Sosmed, enable PM dan Asisten dropdown
                pmSelect.disabled = false;
                pmSelect.classList.remove('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');

                if (astSelect) {
                    astSelect.disabled = false;
                    astSelect.classList.remove('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
                }

                if (hint) {
                    hint.innerHTML = 'PM yang berwenang meninjau & approve bukti postingan Staff.';
                }
            }
        }

        function openCreateAccountModal() {
            const staffSel = document.getElementById('create-acc-staff');
            if (staffSel) {
                staffSel.value = '';
                syncSupervisorState(staffSel, 'create-acc-pm', 'create-pm-hint', 'create-acc-ast');
                staffSel.dispatchEvent(new Event('change', { bubbles: true }));
            }
            const pmSel = document.getElementById('create-acc-pm');
            if (pmSel) {
                pmSel.value = '';
                pmSel.dispatchEvent(new Event('change', { bubbles: true }));
            }
            const astSel = document.getElementById('create-acc-ast');
            if (astSel) {
                astSel.value = '';
                astSel.dispatchEvent(new Event('change', { bubbles: true }));
            }
            document.getElementById('modal-create-account').classList.remove('hidden');
        }

        function openEditAccountModal(accId, accName, platform, link, currentPmId, currentStaffId, notes, currentAssistantId) {
            let accData = {};
            if (typeof accId === 'object' && accId !== null) {
                accData = accId;
            } else {
                accData = {
                    id: accId,
                    name: accName,
                    platform: platform,
                    link: link,
                    pm_id: currentPmId,
                    staff_id: currentStaffId,
                    notes: notes,
                    assistant_id: currentAssistantId
                };
            }

            document.getElementById('form-edit-account').action = `/admin/sosmed/accounts/${accData.id}/assign`;

            window.dispatchEvent(new CustomEvent('open-edit-account', { detail: accData }));

            const staffSel = document.getElementById('edit-acc-staff');
            if (staffSel) staffSel.value = accData.staff_id ?? '';

            const pmSel = document.getElementById('edit-acc-pm');
            if (pmSel) pmSel.value = accData.pm_id ?? '';

            const astSel = document.getElementById('edit-acc-ast');
            if (astSel) astSel.value = accData.assistant_id ?? '';

            const notesEl = document.getElementById('edit-acc-notes');
            if (notesEl) notesEl.value = accData.notes ?? '';

            syncSupervisorState(staffSel, 'edit-acc-pm', 'edit-pm-hint', 'edit-acc-ast');

            // Dispatch change event to update custom dropdown labels
            if (staffSel) staffSel.dispatchEvent(new Event('change', { bubbles: true }));
            if (pmSel) pmSel.dispatchEvent(new Event('change', { bubbles: true }));
            if (astSel) astSel.dispatchEvent(new Event('change', { bubbles: true }));

            document.getElementById('modal-edit-account').classList.remove('hidden');
        }

        function closeEditAccountModal() {
            document.getElementById('modal-edit-account').classList.add('hidden');
        }

        // Enable any disabled select before submitting forms so payload isn't dropped
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function () {
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

        function openVerifyModal(taskId, title) {
            document.getElementById('verify-task-title').textContent = title;
            document.getElementById('form-verify').action = `/admin/sosmed/tasks/${taskId}/verify`;
            const ta = document.querySelector('#form-verify textarea[name="rejection_note"]');
            if (ta) ta.value = '';
            window.dispatchEvent(new CustomEvent('open-verify'));
            document.getElementById('modal-verify').classList.remove('hidden');
        }

        function closeVerifyModal() {
            document.getElementById('modal-verify').classList.add('hidden');
        }

        function openAssignTaskModal() {
            document.getElementById('modal-assign-task').classList.remove('hidden');
        }

        function assignTaskDropdown(accountsList) {
            return {
                accounts: accountsList || [],
                selectedId: '',
                selectedName: '',
                selectedPlatform: '',
                selectedLink: '',
                search: '',
                open: false,
                get selectedLabel() {
                    if (!this.selectedId) return '';
                    return this.selectedName + ' (' + this.selectedPlatform + ')';
                },
                get filteredAccounts() {
                    if (!this.search || !this.search.trim()) {
                        return this.accounts;
                    }
                    const q = this.search.toLowerCase();
                    return this.accounts.filter(acc => {
                        const target = (acc.name + ' ' + acc.platform).toLowerCase();
                        return target.includes(q);
                    });
                },
                selectAccount(acc) {
                    this.selectedId = acc.id;
                    this.selectedName = acc.name;
                    this.selectedPlatform = acc.platform;
                    this.selectedLink = acc.link || '';
                    this.open = false;
                }
            };
        }

        function editAccountDropdown(availableList) {
            return {
                availableAccounts: availableList || [],
                accountsList: [],
                selectedId: '',
                selectedName: '',
                selectedPlatform: '',
                selectedLink: '',
                search: '',
                open: false,
                get selectedLabel() {
                    if (!this.selectedId) return '';
                    return this.selectedName + ' (' + this.selectedPlatform + ')';
                },
                get filteredAccounts() {
                    if (!this.search || !this.search.trim()) {
                        return this.accountsList;
                    }
                    const q = this.search.toLowerCase();
                    return this.accountsList.filter(acc => {
                        const target = (acc.name + ' ' + acc.platform).toLowerCase();
                        return target.includes(q);
                    });
                },
                selectAccount(acc) {
                    this.selectedId = acc.id;
                    this.selectedName = acc.name;
                    this.selectedPlatform = acc.platform;
                    this.selectedLink = acc.link || '';
                    this.open = false;
                },
                initAccount(accData) {
                    this.selectedId = accData.id;
                    this.selectedName = accData.name;
                    this.selectedPlatform = accData.platform;
                    this.selectedLink = accData.link || '';
                    this.search = '';
                    this.open = false;

                    const list = [{
                        id: accData.id,
                        name: accData.name,
                        platform: accData.platform,
                        link: accData.link || ''
                    }];
                    this.availableAccounts.forEach(a => {
                        if (String(a.id) !== String(accData.id)) {
                            list.push(a);
                        }
                    });
                    this.accountsList = list;
                }
            };
        }
    </script>
@endpush