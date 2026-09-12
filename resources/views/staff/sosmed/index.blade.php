@extends('layouts.app')
@section('title', 'Manajemen Sosmed & Approval')
@section('page-title', 'Manajemen Sosmed & Approval')
@section('page-subtitle', 'Delegasi akun sosmed ke PM/Staff & final approval (Level 2)')
@section('sidebar')
    @include('components.sidebar-staff')
@endsection

@section('content')
    @include('components.notification-popup')

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- STAT CARDS --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 mb-6">
        {{-- Card 1: Total Akun --}}
        <x-responsive-card :padding="'p-3 sm:p-4'">
            <p class="text-xs font-medium text-gray-500">Total Akun</p>
            <p class="text-xl sm:text-2xl font-bold text-gray-800 mt-1">3</p>
            <p class="text-[10px] sm:text-xs text-gray-400 mt-0.5">dibuat oleh Admin</p>
        </x-responsive-card>

        {{-- Card 2: Belum Terbagi ke PM --}}
        <x-responsive-card :padding="'p-3 sm:p-4'">
            <p class="text-xs font-medium text-gray-500">Belum Terbagi ke PM</p>
            <p class="text-xl sm:text-2xl font-bold text-amber-600 mt-1">0</p>
            <p class="text-[10px] sm:text-xs text-amber-500 mt-0.5">siap didistribusikan</p>
        </x-responsive-card>

        {{-- Card 3: Perlu Final Approval HR --}}
        <x-responsive-card :padding="'p-3 sm:p-4'" class="border-purple-200 bg-purple-50/20">
            <p class="text-xs font-medium text-purple-700">Perlu Final Approval HR</p>
            <p class="text-xl sm:text-2xl font-bold text-purple-600 mt-1">1</p>
            <p class="text-[10px] sm:text-xs text-purple-500 mt-0.5">sudah lolos PM</p>
        </x-responsive-card>

        {{-- Card 4: Total Tugas --}}
        <x-responsive-card :padding="'p-3 sm:p-4'">
            <p class="text-xs font-medium text-gray-500">Total Tugas</p>
            <p class="text-xl sm:text-2xl font-bold text-indigo-600 mt-1">1</p>
            <p class="text-[10px] sm:text-xs text-gray-400 mt-0.5">seluruh divisi</p>
        </x-responsive-card>

        {{-- Card 5: Selesai Approved (Ditambahkan col-span-2 agar penuh di mobile) --}}
        <x-responsive-card :padding="'p-3 sm:p-4'" class="col-span-2 sm:col-span-1">
            <p class="text-xs font-medium text-gray-500">Selesai Approved</p>
            <p class="text-xl sm:text-2xl font-bold text-emerald-600 mt-1">0</p>
            <p class="text-[10px] sm:text-xs text-emerald-500 mt-0.5">final selesai</p>
        </x-responsive-card>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- TABS & CONTENT --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex border-b border-gray-200 overflow-x-auto">
            <a href="{{ route('staff.sosmed.index', ['tab' => 'accounts']) }}"
                class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 transition
                                      {{ $tab === 'accounts' ? 'border-primary-600 text-primary-600 bg-primary-50/50' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Distribusi Akun ke PM / Sosmed ({{ $accounts->count() }})
            </a>
            <a href="{{ route('staff.sosmed.index', ['tab' => 'approvals']) }}"
                class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 transition
                                      {{ $tab === 'approvals' ? 'border-primary-600 text-primary-600 bg-primary-50/50' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Approval Level 2 (Final)
                @if($stats['need_hr_verify'] > 0)
                    <span class="ml-1 px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-purple-600 text-white">
                        {{ $stats['need_hr_verify'] }}
                    </span>
                @endif
            </a>
            <a href="{{ route('staff.sosmed.index', ['tab' => 'tasks']) }}"
                class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 transition
                                      {{ $tab === 'tasks' ? 'border-primary-600 text-primary-600 bg-primary-50/50' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                Laporan Tugas & Monitoring
            </a>
            <a href="{{ route('staff.sosmed.index', ['tab' => 'my_accounts']) }}"
                class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 transition
                                      {{ $tab === 'my_accounts' ? 'border-primary-600 text-primary-600 bg-primary-50/50' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Tugas Sosmed Saya
                @if($stats['my_pending_today'] > 0)
                    <span class="ml-1 px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-amber-500 text-white">
                        {{ $stats['my_pending_today'] }}
                    </span>
                @endif
            </a>
        </div>

        {{-- ── TAB 1: DISTRIBUSI AKUN ─────────────────────────────────── --}}
        @if($tab === 'accounts')
                <div class="p-4 sm:p-5">
                    <div class="mb-4">
                        <h3 class="text-sm font-semibold text-gray-800">Daftar Akun & Delegasi Tanggung Jawab</h3>
                        <p class="text-xs text-gray-500 mt-0.5">HR Staff membagikan akun sosial media kepada PM agar PM hanya dapat
                            mengelola akun yang menjadi tanggung jawabnya.</p>
                    </div>

                    {{-- Desktop table (md+) --}}
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
                                <tr
                                    class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 tracking-wide">
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
                                        <td class="px-4 py-3.5">
                                            <span class="font-semibold text-gray-800 truncate block"
                                                title="{{ $acc->name }}">{{ $acc->name }}</span>
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
                                                    class="inline-flex items-center gap-1 text-xs text-primary-600 hover:underline">
                                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                    </svg>
                                                    Buka
                                                </a>
                                            @else
                                                <span class="text-xs text-gray-300">—</span>
                                            @endif
                                        </td>
                                        {{-- Eksekutor --}}
                                        <td class="px-4 py-3 text-xs min-w-0">
                                            @if($acc->staffUser)
                                                @php
                                                    $stRoleTag = match($acc->staffUser->role) {
                                                        'pm' => 'PM Mandiri',
                                                        'sosmed' => 'Staff Sosmed',
                                                        'digital_marketing' => 'Digital Marketing',
                                                        default => $acc->staffUser->role_label ?? strtoupper($acc->staffUser->role)
                                                    };
                                                @endphp
                                                <div class="flex items-start gap-2 min-w-0">
                                                    <div
                                                        class="w-6 h-6 rounded-full bg-pink-100 text-pink-700 font-bold flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5">
                                                        {{ strtoupper(substr($acc->staffUser->name, 0, 1)) }}
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <span class="font-semibold text-gray-800 block truncate"
                                                            title="{{ $acc->staffUser->name }}">
                                                            {{ $acc->staffUser->name }}
                                                        </span>
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold mt-0.5 {{ $acc->staffUser->role === 'pm' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-pink-50 text-pink-700 border border-pink-200' }}">
                                                            {{ $stRoleTag }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200 whitespace-nowrap">
                                                    Belum Ditugaskan
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Supervisor PM --}}
                                        <td class="px-4 py-3 text-xs min-w-0">
                                            @if($acc->staffUser && $acc->staffUser->role === 'pm')
                                                <span class="text-gray-400 italic text-[11px] block">— (Langsung ke HR)</span>
                                            @elseif($acc->pmUser)
                                                <div class="flex items-start gap-2 min-w-0">
                                                    <div
                                                        class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5">
                                                        {{ strtoupper(substr($acc->pmUser->name, 0, 1)) }}
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <span class="font-semibold text-gray-800 block truncate"
                                                            title="{{ $acc->pmUser->name }}">
                                                            {{ $acc->pmUser->name }}
                                                        </span>
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold mt-0.5 bg-purple-50 text-purple-700 border border-purple-200">
                                                            Supervisor PM
                                                        </span>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 whitespace-nowrap">
                                                    Belum Ada PM
                                                </span>
                                            @endif
                                        </td>
                                        {{-- Asisten Pengawas --}}
                                        <td class="px-4 py-3 text-xs min-w-0">
                                            @if($acc->assistantUser)
                                                <div class="flex items-start gap-2 min-w-0">
                                                    <div class="w-6 h-6 rounded-full bg-teal-100 text-teal-700 font-bold flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5">
                                                        {{ strtoupper(substr($acc->assistantUser->name, 0, 1)) }}
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <span class="font-semibold text-gray-800 block truncate" title="{{ $acc->assistantUser->name }}">
                                                            {{ $acc->assistantUser->name }}
                                                        </span>
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold mt-0.5 bg-teal-50 text-teal-700 border border-teal-200">
                                                            Asisten HR
                                                        </span>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-400 whitespace-nowrap">
                                                    Tanpa Asisten
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3.5 text-center">
                                            <button
                                                onclick="openAssignModal({{ $acc->id }}, '{{ addslashes($acc->name) }}', {{ $acc->pm_id ?? 'null' }}, {{ $acc->staff_id ?? 'null' }}, {{ $acc->assistant_id ?? 'null' }}, '{{ $acc->staffUser?->role ?? '' }}')"
                                                class="px-3 py-1 bg-primary-50 hover:bg-primary-100 text-primary-700 text-xs font-semibold rounded-lg transition whitespace-nowrap">
                                                Atur PJ
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-400">Belum ada akun sosial media.
                                            Admin perlu menambahkan akun terlebih dahulu.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile cards (< md) --}} <div class="md:hidden space-y-3">
                        @forelse($accounts as $acc)
                            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                                <div class="flex items-start justify-between gap-3 mb-3">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-800 truncate">{{ $acc->name }}</p>
                                        <span
                                            class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-md text-xs font-medium border {{ $acc->platform_color }}">
                                            {{ $acc->platform_icon }} {{ $acc->platform }}
                                        </span>
                                    </div>
                                    <button
                                        onclick="openAssignModal({{ $acc->id }}, '{{ addslashes($acc->name) }}', {{ $acc->pm_id ?? 'null' }}, {{ $acc->staff_id ?? 'null' }}, {{ $acc->assistant_id ?? 'null' }}, '{{ $acc->staffUser?->role ?? '' }}')"
                                        class="flex-shrink-0 px-3 py-1.5 bg-primary-50 hover:bg-primary-100 text-primary-700 text-xs font-semibold rounded-lg transition">
                                        Atur PJ
                                    </button>
                                </div>

                                <div class="space-y-2 text-xs border-t border-gray-100 pt-2.5">
                                    {{-- Link Akun --}}
                                    <div class="min-w-0">
                                        <p class="text-gray-400 mb-0.5">Link Akun</p>
                                        @if($acc->link)
                                            <a href="{{ $acc->link }}" target="_blank"
                                                class="text-primary-600 hover:underline truncate block">{{ parse_url($acc->link, PHP_URL_HOST) ?? $acc->link }}</a>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </div>

                                    {{-- Row Eksekutor (Kiri) & Supervisor PM (Kanan) --}}
                                    <div class="flex items-center justify-between gap-2 pt-1">
                                        <div class="min-w-0">
                                            <p class="text-gray-400 mb-0.5">Eksekutor</p>
                                            @if($acc->staffUser)
                                                @php
                                                    $mStRoleTag = match($acc->staffUser->role) {
                                                        'pm' => 'PM Mandiri',
                                                        'sosmed' => 'Staff Sosmed',
                                                        'digital_marketing' => 'Digital Marketing',
                                                        default => $acc->staffUser->role_label ?? strtoupper($acc->staffUser->role)
                                                    };
                                                @endphp
                                                <span class="font-medium text-gray-800 truncate block">
                                                    {{ $acc->staffUser->name }} <span class="text-[10px] text-gray-500 font-normal">({{ $mStRoleTag }})</span>
                                                </span>
                                            @else
                                                <span class="text-amber-600 font-medium">Belum Ditugaskan</span>
                                            @endif
                                        </div>
                                        <div class="min-w-0 text-right">
                                            <p class="text-gray-400 mb-0.5">Supervisor PM</p>
                                            <span class="font-medium text-gray-800 truncate block">
                                                {{ ($acc->staffUser && $acc->staffUser->role === 'pm') ? 'Langsung ke HR' : ($acc->pmUser?->name ?? 'Belum Ada PM') }}
                                            </span>
                                        </div>
                                    </div>
                                    {{-- Row Asisten Pengawas Mobile --}}
                                    <div class="pt-1 border-t border-gray-50 flex items-center justify-between text-xs">
                                        <span class="text-gray-400">Asisten Pengawas:</span>
                                        <span class="font-medium text-gray-800 truncate">
                                            {{ $acc->assistantUser?->name ?? 'Tanpa Asisten' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-sm text-gray-400">Belum ada akun sosial media.</div>
                        @endforelse
                </div>
            </div>
        @endif

    {{-- ── TAB 2: APPROVAL LEVEL 2 (HR STAFF) ────────────────────── --}}
    @if($tab === 'approvals')
        <div class="p-4 sm:p-5">
            <div class="mb-4">
                <h3 class="text-sm font-semibold text-gray-800">Tugas Siap Approval Final (Level 2)</h3>
                <p class="text-xs text-gray-500 mt-0.5">Tugas-tugas di bawah ini telah diverifikasi oleh PM dan menunggu
                    persetujuan akhir dari HR Staff.</p>
            </div>

            <div class="space-y-3 mb-6">
                @forelse($needHrApproval as $task)
                    <div class="p-4 bg-purple-50/60 border border-purple-200 rounded-xl hover:border-purple-300 transition">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-semibold text-gray-800 text-sm">{{ $task->title }}</span>
                                    <span
                                        class="px-2 py-0.5 rounded-md text-xs font-medium border {{ $task->account?->platform_color ?? 'bg-gray-100' }}">
                                        {{ $task->account?->platform_icon }} {{ $task->account?->name }}
                                        ({{ $task->account?->platform }})
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[11px] bg-indigo-100 text-indigo-700 font-medium">
                                        Verif PM: {{ $task->verifiedBy?->name ?? 'PM' }}
                                    </span>
                                </div>
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-2 text-xs text-gray-500">
                                    <span>Pelaksana: <strong
                                            class="text-gray-800">{{ $task->assignedUser?->name ?? '-' }}</strong></span>
                                    <span class="hidden sm:inline">·</span>
                                    <span>{{ $task->task_date->translatedFormat('d M Y') }}</span>
                                    @if($task->hasLinks())
                                        <button type="button"
                                            onclick="openLinksPopup({{ json_encode($task->link_upload) }}, '{{ addslashes($task->title) }}')"
                                            class="text-primary-600 font-medium hover:underline inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                            </svg>
                                            {{ $task->link_count }} Bukti
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-shrink-0 self-center">
                                <button onclick="openVerifyModal({{ $task->id }}, '{{ addslashes($task->title) }}')"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded-xl transition shadow-sm whitespace-nowrap">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Final Approve
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-16 text-center">
                        <div class="w-16 h-16 rounded-full bg-green-50 flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-700">Semua Tugas Sudah Selesai Diverifikasi</p>
                        <p class="text-xs text-gray-400 mt-1 max-w-xs">
                            Tidak ada tugas tim sosmed yang menunggu approval final HR Staff saat ini.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    {{-- ── TAB 3: MONITORING TUGAS ───────────────────────────────── --}}
    @if($tab === 'tasks')
        <div class="p-4 sm:p-5">

            {{-- Desktop table (md+) --}}
            <div class="hidden md:block overflow-x-auto rounded-lg border border-gray-100">
                <table class="w-full text-sm table-fixed">
                    <colgroup>
                        <col class="w-1/3">
                        <col class="w-36">
                        <col class="w-36">
                        <col class="w-32">
                        <col class="w-32">
                        <col class="w-36">
                    </colgroup>
                    <thead>
                        <tr
                            class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            <th class="px-4 py-3 text-left">Tugas</th>
                            <th class="px-4 py-3 text-left">Akun Sosmed</th>
                            <th class="px-4 py-3 text-left">Project Manager</th>
                            <th class="px-4 py-3 text-left">PJ Sosmed</th>
                            <th class="px-4 py-3 text-left">Verif PM</th>
                            <th class="px-4 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($allTasks as $t)
                            <tr class="hover:bg-gray-50/80 transition align-top">
                                <td class="px-4 py-3.5">
                                    <p class="font-medium text-gray-800 truncate" title="{{ $t->title }}">{{ $t->title }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $t->task_date->translatedFormat('d M Y') }}</p>
                                </td>
                                <td class="px-4 py-3.5 text-xs">
                                    <p class="font-medium text-gray-800 truncate">{{ $t->account?->name ?? '—' }}</p>
                                    <p class="text-gray-400">{{ $t->account?->platform }}</p>
                                </td>
                                <td class="px-4 py-3.5 text-xs">
                                    @if($t->account?->pmUser)
                                        <span
                                            class="font-semibold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100">{{ $t->account->pmUser->name }}</span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-xs">
                                    @if($t->assignedUser)
                                        <span
                                            class="font-semibold text-gray-800 bg-gray-50 px-2 py-0.5 rounded border border-gray-200">{{ $t->assignedUser->name }}</span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-xs">
                                    @if($t->verifiedBy)
                                        <span class="text-blue-700 font-semibold block truncate">{{ $t->verifiedBy->name }}</span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $t->status_badge_class }}">
                                        {{ $t->status_label }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400">Belum ada tugas tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile cards (< md) --}} <div class="md:hidden space-y-3">
                @forelse($allTasks as $t)
                    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-800 truncate text-sm">{{ $t->title }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $t->task_date->translatedFormat('d M Y') }}</p>
                            </div>
                            <span
                                class="flex-shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $t->status_badge_class }}">
                                {{ $t->status_label }}
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs mt-2 border-t border-gray-100 pt-2.5">
                            {{-- Baris 1: Kiri (Akun) --}}
                            <div class="min-w-0">
                                <p class="text-gray-400 mb-0.5">Akun</p>
                                <p class="font-medium text-gray-700 truncate">{{ $t->account?->name ?? '—' }}</p>
                                <p class="text-gray-400 truncate">{{ $t->account?->platform }}</p>
                            </div>

                            {{-- Baris 1: Kanan (Pelaksana) --}}
                            <div class="min-w-0 text-right">
                                <p class="text-gray-400 mb-0.5">Pelaksana</p>
                                <p class="font-medium text-gray-700 truncate">{{ $t->assignedUser?->name ?? '—' }}</p>
                            </div>

                            {{-- Baris 2: Kiri (Project Manager) --}}
                            <div class="min-w-0">
                                <p class="text-gray-400 mb-0.5">Project Manager</p>
                                <p class="font-medium text-indigo-700 truncate">{{ $t->account?->pmUser?->name ?? '—' }}</p>
                            </div>

                            {{-- Baris 2: Kanan (Verif PM) --}}
                            <div class="min-w-0 text-right">
                                <p class="text-gray-400 mb-0.5">Verif PM</p>
                                <p class="font-medium text-blue-700 truncate">{{ $t->verifiedBy?->name ?? '—' }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-sm text-gray-400">Belum ada tugas tercatat.</div>
                @endforelse
        </div>
        </div>
    @endif

    {{-- ── TAB 4: TUGAS SOSMED SAYA ─────────────────────────────── --}}
    @if($tab === 'my_accounts')
        <div class="p-4 sm:p-5">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Akun Sosmed yang Saya Kelola</h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Upload bukti pengerjaan konten harian untuk akun yang ditetapkan Admin kepada Anda.
                    </p>
                </div>
                <div class="text-xs bg-indigo-50 border border-indigo-200 text-indigo-700 px-3 py-1.5 rounded-lg font-medium shrink-0">
                    Hari Ini: <span class="font-bold">{{ now()->translatedFormat('d M Y') }}</span>
                </div>
            </div>

            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto rounded-lg border border-gray-100">
                <table class="w-full table-fixed text-sm">
                    <colgroup>
                        <col class="w-[28%]">
                        <col class="w-28">
                        <col class="w-36">
                        <col class="w-40">
                        <col class="w-36">
                    </colgroup>
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 tracking-wide">
                            <th class="px-4 py-3 text-left">Nama Akun</th>
                            <th class="px-4 py-3 text-left">Platform</th>
                            <th class="px-4 py-3 text-left">Bukti Konten</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($myAccounts as $acc)
                            @php
                                $myTask = $todayTasks[$acc->id] ?? null;
                                $myStatus = $myTask?->status ?? 'pending';
                                $isSubmitted = in_array($myStatus, ['done_by_staff', 'verified_by_pm', 'approved_hr']);
                                $isRejected  = $myStatus === 'rejected';
                            @endphp
                            <tr class="hover:bg-gray-50/80 transition align-middle">
                                <td class="px-4 py-3.5">
                                    <p class="font-semibold text-gray-800 truncate">{{ $acc->name }}</p>
                                    @if($acc->link)
                                        <a href="{{ $acc->link }}" target="_blank"
                                           class="inline-flex items-center gap-1 text-xs text-primary-600 hover:underline mt-0.5">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                            Buka Profil
                                        </a>
                                    @endif
                                    @if($isRejected && $myTask?->rejection_note)
                                        <p class="text-xs text-rose-600 mt-1 bg-rose-50 px-2 py-1 rounded border border-rose-200 leading-snug">
                                            ↩ "{{ $myTask->rejection_note }}"
                                        </p>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-medium border {{ $acc->platform_color }}">
                                        {{ $acc->platform_icon }} {{ $acc->platform }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-xs">
                                    @if($myTask && $myTask->hasLinks())
                                        <button type="button"
                                            onclick="openLinksPopup({{ json_encode($myTask->link_upload) }}, '{{ addslashes($acc->name) }}')"
                                            class="inline-flex items-center gap-1 text-xs text-primary-600 hover:underline font-medium">
                                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                            </svg>
                                            {{ $myTask->link_count }} link bukti
                                        </button>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    @if($myTask)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $myTask->status_badge_class }}">
                                            {{ $myTask->status_label }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Belum Dikerjakan</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    @if(!$isSubmitted)
                                        <button type="button"
                                            onclick="openMySubmitModal({{ $acc->id }}, '{{ addslashes($acc->name) }}', '{{ addslashes($myTask?->description ?? '') }}')"
                                            class="px-3 py-1 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-lg transition shadow-sm">
                                            {{ $isRejected ? 'Revisi' : 'Submit Bukti' }}
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Terkunci</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-400">
                                    Admin belum menetapkan akun sosmed kepada Anda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden space-y-3">
                @forelse($myAccounts as $acc)
                    @php
                        $myTask = $todayTasks[$acc->id] ?? null;
                        $myStatus = $myTask?->status ?? 'pending';
                        $isSubmitted = in_array($myStatus, ['done_by_staff', 'verified_by_pm', 'approved_hr']);
                        $isRejected  = $myStatus === 'rejected';
                    @endphp
                    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-gray-800 text-sm truncate">{{ $acc->name }}</p>
                                <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-md text-[10px] font-medium border whitespace-nowrap {{ $acc->platform_color }}">
                                    {{ $acc->platform_icon }} {{ $acc->platform }}
                                </span>
                                @if($isRejected && $myTask?->rejection_note)
                                    <p class="text-xs text-rose-600 mt-1 bg-rose-50 px-2 py-1 rounded border border-rose-200 leading-snug">
                                        ↩ "{{ $myTask->rejection_note }}"
                                    </p>
                                @endif
                            </div>
                            <div class="flex-shrink-0">
                                @if($isSubmitted)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $myTask->status_badge_class }}">
                                        {{ $myTask->status_label }}
                                    </span>
                                @else
                                    <button type="button"
                                        onclick="openMySubmitModal({{ $acc->id }}, '{{ addslashes($acc->name) }}', '{{ addslashes($myTask?->description ?? '') }}')"
                                        class="px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-lg transition">
                                        {{ $isRejected ? 'Revisi' : 'Submit Bukti' }}
                                    </button>
                                @endif
                            </div>
                        </div>
                        @if($myTask && $myTask->hasLinks())
                            <div class="border-t border-gray-100 pt-2 mt-1">
                                <button type="button"
                                    onclick="openLinksPopup({{ json_encode($myTask->link_upload) }}, '{{ addslashes($acc->name) }}')"
                                    class="inline-flex items-center gap-1 text-xs text-primary-600 hover:underline font-medium">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                    </svg>
                                    {{ $myTask->link_count }} link bukti
                                </button>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="py-8 text-center text-sm text-gray-400">Admin belum menetapkan akun sosmed kepada Anda.</div>
                @endforelse
            </div>
        </div>
    @endif
    </div>

    {{-- ── MODAL DELEGASI AKUN (HR STAFF) ─────────────────────────── --}}
    <div id="modal-assign" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            onclick="document.getElementById('modal-assign').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10 overflow-visible">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <div>
                    <h3 class="text-base font-bold text-gray-800">Atur Penugasan Akun Sosmed</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Tentukan eksekutor harian dan supervisor pengawas</p>
                </div>
                <button onclick="document.getElementById('modal-assign').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="form-assign" method="POST" action="" class="p-6 pt-1 space-y-4">
                @csrf @method('PATCH')
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                    <p class="text-xs text-gray-500 mb-0.5">Nama Akun Sosial Media</p>
                    <p id="assign-acc-name" class="text-sm font-bold text-gray-800"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">1. Eksekutor Akun (Dikelola Oleh)</label>
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
                    <p id="assign-staff-note" class="text-[11px] text-gray-400 mt-1">User yang bertugas membuat & mengunggah konten.</p>
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
                    <p id="assign-pm-hint" class="text-[11px] text-gray-400 mt-1">PM yang berwenang meninjau & approve tugas. Jika PM mengelola akun mandiri, bagian ini otomatis dinonaktifkan.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">3. Asisten Pengawas (Wewenang Verifikasi Asisten)</label>
                    <select name="assistant_id" id="assign-assistant-sel"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="">-- Tanpa Asisten / Belum Diberi Wewenang --</option>
                        @foreach($assistants as $ast)
                            <option value="{{ $ast->id }}">{{ $ast->name }} (Asisten)</option>
                        @endforeach
                    </select>
                    <p id="assign-ast-note" class="text-[11px] text-gray-400 mt-1">Jika dipilih, asisten ini berwenang melihat tugas dan memverifikasi Level-1 tugas akun ini.</p>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-assign').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">Batal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">Simpan Penugasan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── MODAL VERIFIKASI FINAL (HR STAFF - APPROVE / REJECT) ─────── --}}
    <div id="modal-verify" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
         x-data="{ action: 'verify' }"
         @open-verify.window="action = 'verify'">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeVerifyModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10 overflow-hidden">

            {{-- Modal Header with Dynamic Color --}}
            <div class="px-6 py-4 flex items-center justify-between transition-colors"
                 :class="action === 'verify' ? 'bg-purple-50 border-b border-purple-100' : 'bg-rose-50 border-b border-rose-100'">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors"
                         :class="action === 'verify' ? 'bg-purple-100 text-purple-600' : 'bg-rose-100 text-rose-600'">
                        <template x-if="action === 'verify'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </template>
                        <template x-if="action === 'reject'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </template>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-800"
                            x-text="action === 'verify' ? 'Final Approval Tugas (HR Staff)' : 'Tolak & Kembalikan Tugas'"></h3>
                        <p class="text-[11px] text-gray-400">Persetujuan akhir tingkat 2 untuk hasil konten tim sosmed</p>
                    </div>
                </div>
                <button type="button" onclick="closeVerifyModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="form-verify" method="POST" action="" class="p-6 pt-1 space-y-4">
                @csrf @method('PATCH')
                <input type="hidden" name="action" :value="action">

                {{-- Task Info Card --}}
                <div class="bg-gray-50 rounded-xl p-3.5 border border-gray-200/80">
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Tugas yang Ditinjau</p>
                    <p id="verify-task-title" class="text-sm font-bold text-gray-800 break-words"></p>
                </div>

                {{-- Action Toggle (Segmented control) --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-2">Keputusan Final HR</label>
                    <div class="grid grid-cols-2 gap-2 p-1 bg-gray-100 rounded-xl">
                        <button type="button"
                            @click="action = 'verify'"
                            :class="action === 'verify' ? 'bg-white text-purple-700 shadow-sm font-bold' : 'text-gray-500 font-medium hover:text-gray-700'"
                            class="py-2 text-xs rounded-lg transition-all flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Approve Final
                        </button>
                        <button type="button"
                            @click="action = 'reject'"
                            :class="action === 'reject' ? 'bg-white text-rose-700 shadow-sm font-bold' : 'text-gray-500 font-medium hover:text-gray-700'"
                            class="py-2 text-xs rounded-lg transition-all flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Tolak
                        </button>
                    </div>
                </div>

                {{-- Rejection Note (conditionally visible) --}}
                <div x-show="action === 'reject'" x-cloak class="space-y-1.5">
                    <label class="block text-xs font-semibold text-gray-700">
                        Catatan Revisi <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="rejection_note" rows="3"
                        placeholder="Jelaskan alasan penolakan atau instruksi perbaikan..."
                        class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-gray-800 placeholder-gray-400 focus:ring-2 focus:ring-rose-400 focus:border-rose-400 outline-none transition resize-none"></textarea>
                    <p class="text-[11px] text-gray-400">Catatan ini akan dikirimkan ke staff yang mengerjakan tugas.</p>
                </div>

                {{-- Approval Note (conditionally visible) --}}
                <div x-show="action === 'verify'" class="bg-purple-50 border border-purple-100 rounded-xl p-3 text-xs text-purple-800 leading-relaxed">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-purple-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p>Tugas akan <strong>disetujui secara final</strong> (Level 2). Status pengerjaan akan selesai 100% dan dicatat dalam riwayat kinerja divisi.</p>
                    </div>
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeVerifyModal()"
                        class="flex-1 px-4 py-2.5 rounded-xl border border-gray-300 text-sm text-gray-600 font-medium hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition shadow-sm"
                        :class="action === 'verify' ? 'bg-purple-600 hover:bg-purple-700' : 'bg-rose-600 hover:bg-rose-700'">
                        <span x-show="action === 'verify'">Approve Final</span>
                        <span x-show="action === 'reject'">Tolak Tugas</span>
                    </button>
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

    {{-- ── MODAL: SUBMIT BUKTI TUGAS SAYA (STAFF) ─────────────────── --}}
    <div id="modal-my-submit" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeMySubmitModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg z-10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-bold text-gray-800">Submit Bukti Konten</h3>
                <button onclick="closeMySubmitModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="form-my-submit" method="POST" action="" class="p-6 pt-4 space-y-4">
                @csrf
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                    <p class="text-xs text-gray-500 mb-0.5">Nama Akun Sosmed</p>
                    <p id="my-submit-account-name" class="text-sm font-semibold text-gray-800"></p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Link / URL Hasil Konten <span class="text-red-500">*</span>
                        <span class="font-normal text-gray-400 ml-1">(bisa lebih dari satu)</span>
                    </label>
                    <div id="my-links-container" class="space-y-2 max-h-[200px] overflow-y-auto pr-1">
                        <div class="flex gap-2 link-row">
                            <input type="url" name="links[]" required
                                placeholder="https://instagram.com/p/xxx"
                                class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                            <button type="button" onclick="removeMyLinkRow(this)"
                                class="text-gray-300 hover:text-rose-500 px-1 transition hidden remove-btn">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <button type="button" onclick="addMyLinkRow()"
                        class="mt-2 inline-flex items-center gap-1 text-xs text-primary-600 hover:text-primary-700 font-medium">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Link Lain
                    </button>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan Tambahan (Opsional)</label>
                    <textarea name="description" id="my-submit-description" rows="2"
                        placeholder="Brief konten / keterangan..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none"></textarea>
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeMySubmitModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700">Batal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-semibold">
                        Kirim Bukti
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
                'select[id="assign-staff-sel"], select[id="assign-pm-sel"], select[id="assign-assistant-sel"]'
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
                        button.className = 'w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-left flex justify-between items-center transition bg-gray-100 text-gray-400 cursor-not-allowed';
                    } else {
                        button.className = 'w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-left flex justify-between items-center transition bg-white text-gray-800 focus:ring-2 focus:ring-primary-500 focus:outline-none';
                    }
                });
                observer.observe(select, { attributes: true, attributeFilter: ['disabled'] });
            });
        });

        // Deprecated functions kept for compatibility
        function filterSelectOptions(query, selectId) {}
        function resetSearchFilter(inputId, selectId) {}

        function syncSupervisorState(staffSelect, pmSelectId, hintId) {
            if (!staffSelect) return;
            const pmSelect = document.getElementById(pmSelectId);
            const assistantSelect = document.getElementById('assign-assistant-sel');
            const hint = document.getElementById(hintId);
            if (!pmSelect) return;

            const selectedOption = staffSelect.options[staffSelect.selectedIndex];
            const role = selectedOption ? selectedOption.getAttribute('data-role') : null;

            if (role === 'pm') {
                pmSelect.value = '';
                pmSelect.disabled = true;
                pmSelect.classList.add('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
                
                // Also disable assistant selection
                if (assistantSelect) {
                    assistantSelect.value = '';
                    assistantSelect.disabled = true;
                    assistantSelect.classList.add('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
                }
                
                if (hint) {
                    hint.innerHTML = '<span class="text-indigo-600 font-semibold">🔒 PM Mandiri:</span> Akun dikelola langsung oleh PM. Hasil pengerjaan otomatis lolos Level 1 dan langsung diverifikasi HR Staff (supervisor dan asisten otomatis dinonaktifkan).';
                }
            } else {
                pmSelect.disabled = false;
                pmSelect.classList.remove('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
                
                // Enable assistant selection
                if (assistantSelect) {
                    assistantSelect.disabled = false;
                    assistantSelect.classList.remove('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
                }
                
                if (hint) {
                    hint.innerHTML = 'PM yang berwenang meninjau & approve tugas. Jika PM mengelola akun mandiri, bagian ini otomatis dinonaktifkan.';
                }
            }
        }

        // Helper: set disabled state on a custom-dropdown-wrapped select AND update its trigger button visually
        function setCustomSelectDisabled(select, disabled) {
            select.disabled = disabled;
            const wrapper = select.closest('.custom-select-wrapper');
            if (!wrapper) return;
            const btn = wrapper.querySelector('button[type="button"]');
            if (!btn) return;
            if (disabled) {
                btn.disabled = true;
                btn.className = 'w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-left flex justify-between items-center bg-gray-100 text-gray-400 cursor-not-allowed opacity-70 select-none';
                // Make the chevron also dimmed
                const svg = btn.querySelector('svg');
                if (svg) svg.classList.add('opacity-40');
            } else {
                btn.disabled = false;
                btn.className = 'w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-left flex justify-between items-center transition bg-white text-gray-800 focus:ring-2 focus:ring-primary-500 focus:outline-none';
                const svg = btn.querySelector('svg');
                if (svg) svg.classList.remove('opacity-40');
            }
        }

        function openAssignModal(accId, accName, currentPmId, currentStaffId, currentAssistantId, currentStaffRole) {
            document.getElementById('assign-acc-name').textContent = accName;
            document.getElementById('form-assign').action = `/staff/sosmed/accounts/${accId}/assign`;

            const staffSel = document.getElementById('assign-staff-sel');
            const pmSel    = document.getElementById('assign-pm-sel');
            const astSel   = document.getElementById('assign-assistant-sel');

            staffSel.value = currentStaffId ?? '';
            pmSel.value    = currentPmId    ?? '';
            astSel.value   = currentAssistantId ?? '';

            const adminAssigned = currentStaffRole === 'hr_staff';

            if (adminAssigned) {
                // Lock all three — Admin has already set everything
                setCustomSelectDisabled(staffSel, true);
                setCustomSelectDisabled(pmSel,    true);
                setCustomSelectDisabled(astSel,   true);
            } else {
                // Re-enable all, then let syncSupervisorState decide PM
                setCustomSelectDisabled(staffSel, false);
                setCustomSelectDisabled(astSel,   false);
                syncSupervisorState(staffSel, 'assign-pm-sel', 'assign-pm-hint');
            }

            // Update helper notes
            const staffNote = document.getElementById('assign-staff-note');
            const pmNote    = document.getElementById('assign-pm-hint');
            const astNote   = document.getElementById('assign-ast-note');

            if (staffNote) {
                staffNote.textContent = adminAssigned
                    ? '🔒 Eksekutor ditetapkan oleh Admin dan tidak dapat diubah.'
                    : 'User yang bertugas membuat & mengunggah konten.';
                staffNote.className = adminAssigned ? 'text-[11px] text-amber-600 mt-1' : 'text-[11px] text-gray-400 mt-1';
            }
            if (pmNote && adminAssigned) {
                pmNote.textContent  = '🔒 Supervisor PM sudah ditetapkan oleh Admin, tidak dapat diubah.';
                pmNote.className    = 'text-[11px] text-amber-600 mt-1';
            }
            if (astNote) {
                astNote.textContent = adminAssigned
                    ? '🔒 Asisten pengawas sudah ditetapkan oleh Admin, tidak dapat diubah.'
                    : 'Jika dipilih, asisten ini berwenang melihat tugas dan memverifikasi Level-1 tugas akun ini.';
                astNote.className = adminAssigned ? 'text-[11px] text-amber-600 mt-1' : 'text-[11px] text-gray-400 mt-1';
            }

            // Update custom dropdown labels
            staffSel.dispatchEvent(new Event('change', { bubbles: true }));
            pmSel.dispatchEvent(new Event('change', { bubbles: true }));
            astSel.dispatchEvent(new Event('change', { bubbles: true }));

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

        function openVerifyModal(taskId, title) {
            document.getElementById('verify-task-title').textContent = title;
            document.getElementById('form-verify').action = `/staff/sosmed/tasks/${taskId}/verify`;
            const ta = document.querySelector('#form-verify textarea[name="rejection_note"]');
            if (ta) ta.value = '';
            window.dispatchEvent(new CustomEvent('open-verify'));
            document.getElementById('modal-verify').classList.remove('hidden');
        }

        function closeVerifyModal() {
            document.getElementById('modal-verify').classList.add('hidden');
        }

        // ── My Submit Modal (Staff own tasks) ──────────────────────────────
        function openMySubmitModal(accountId, accountName, existingDesc) {
            document.getElementById('my-submit-account-name').textContent = accountName;
            document.getElementById('form-my-submit').action = `/staff/sosmed/accounts/${accountId}/submit`;
            document.getElementById('my-submit-description').value = existingDesc || '';

            // Reset links container to single empty input
            const container = document.getElementById('my-links-container');
            container.innerHTML = `
                <div class="flex gap-2 link-row">
                    <input type="url" name="links[]" required
                        placeholder="https://instagram.com/p/xxx"
                        class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    <button type="button" onclick="removeMyLinkRow(this)"
                        class="text-gray-300 hover:text-rose-500 px-1 transition hidden remove-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>`;

            document.getElementById('modal-my-submit').classList.remove('hidden');
        }

        function closeMySubmitModal() {
            document.getElementById('modal-my-submit').classList.add('hidden');
        }

        function addMyLinkRow() {
            const container = document.getElementById('my-links-container');
            const newRow = document.createElement('div');
            newRow.className = 'flex gap-2 link-row';
            newRow.innerHTML = `
                <input type="url" name="links[]" required
                    placeholder="https://instagram.com/p/xxx"
                    class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                <button type="button" onclick="removeMyLinkRow(this)"
                    class="text-gray-300 hover:text-rose-500 px-1 transition remove-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>`;
            container.appendChild(newRow);
            // Show all remove buttons when more than one row
            container.querySelectorAll('.remove-btn').forEach(btn => btn.classList.remove('hidden'));
        }

        function removeMyLinkRow(btn) {
            const container = document.getElementById('my-links-container');
            const rows = container.querySelectorAll('.link-row');
            if (rows.length <= 1) return;
            btn.closest('.link-row').remove();
            // Hide remove button on last remaining row
            if (container.querySelectorAll('.link-row').length === 1) {
                container.querySelector('.remove-btn')?.classList.add('hidden');
            }
        }

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