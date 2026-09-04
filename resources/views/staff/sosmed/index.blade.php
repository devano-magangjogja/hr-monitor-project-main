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
                                <col class="w-1/4"> {{-- nama akun --}}
                                <col class="w-32"> {{-- platform --}}
                                <col class="w-1/4"> {{-- link --}}
                                <col class="w-1/5"> {{-- Eksekutor --}}
                                <col class="w-1/5"> {{-- Supervisor PM --}}
                                <col class="w-24"> {{-- aksi --}}
                            </colgroup>
                            <thead>
                                <tr
                                    class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    <th class="px-4 py-3 text-left">Nama Akun</th>
                                    <th class="px-4 py-3 text-left">Platform</th>
                                    <th class="px-4 py-3 text-left">Link Akun</th>
                                    <th class="px-4 py-3 text-left">Eksekutor</th>
                                    <th class="px-4 py-3 text-left">Supervisor PM</th>
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
                                        <td class="px-4 py-3.5 text-center">
                                            <button
                                                onclick="openAssignModal({{ $acc->id }}, '{{ addslashes($acc->name) }}', {{ $acc->pm_id ?? 'null' }}, {{ $acc->staff_id ?? 'null' }})"
                                                class="px-3 py-1 bg-primary-50 hover:bg-primary-100 text-primary-700 text-xs font-semibold rounded-lg transition whitespace-nowrap">
                                                Atur PJ
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400">Belum ada akun sosial media.
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
                                        onclick="openAssignModal({{ $acc->id }}, '{{ addslashes($acc->name) }}', {{ $acc->pm_id ?? 'null' }}, {{ $acc->staff_id ?? 'null' }})"
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
                                    class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded-lg transition shadow-sm whitespace-nowrap">
                                    Final Approve
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center bg-gray-50 rounded-xl border border-dashed border-gray-200">
                        <p class="text-sm text-gray-500">Tidak ada tugas yang menunggu verifikasi HR Staff saat ini.</p>
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
    </div>

    {{-- ── MODAL DELEGASI AKUN (HR STAFF) ─────────────────────────── --}}
    <div id="modal-assign" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            onclick="document.getElementById('modal-assign').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10">
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
            <form id="form-assign" method="POST" action="" class="p-6 pt-2 space-y-4">
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
                    <p class="text-[11px] text-gray-400 mt-1">User yang bertugas membuat & mengunggah konten.</p>
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
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-assign').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">Batal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">Simpan Penugasan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── MODAL VERIFIKASI FINAL (HR STAFF) ───────────────────────── --}}
    <div id="modal-verify" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            onclick="document.getElementById('modal-verify').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm z-10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-bold text-gray-800">Final Approval HR Staff</h3>
                <button onclick="document.getElementById('modal-verify').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="px-6 pt-4">
                <p class="text-xs text-gray-500 mb-0.5">Tugas</p>
                <p id="verify-task-title" class="text-sm font-semibold text-gray-800"></p>
            </div>
            <form id="form-verify" method="POST" action="" class="p-6 pt-3 space-y-4">
                @csrf @method('PATCH')
                <input type="hidden" name="action" id="verify-action" value="verify">
                <div id="rej-field" class="hidden">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Alasan Penolakan</label>
                    <textarea name="rejection_note" rows="3" placeholder="Catatan revisi..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:outline-none"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" onclick="setAction('reject')"
                        class="flex-1 px-4 py-2 border border-red-300 text-red-600 text-sm font-semibold rounded-lg hover:bg-red-50">Tolak</button>
                    <button type="submit" onclick="setAction('verify')"
                        class="flex-1 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Approve
                        Final</button>
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
                    hint.innerHTML = 'PM yang berwenang meninjau & approve tugas. Jika PM mengelola akun mandiri, bagian ini otomatis dinonaktifkan.';
                }
            }
        }

        function openAssignModal(accId, accName, currentPmId, currentStaffId) {
            document.getElementById('assign-acc-name').textContent = accName;
            document.getElementById('form-assign').action = `/staff/sosmed/accounts/${accId}/assign`;
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

        function openVerifyModal(taskId, title) {
            document.getElementById('verify-task-title').textContent = title;
            document.getElementById('form-verify').action = `/staff/sosmed/tasks/${taskId}/verify`;
            document.getElementById('rej-field').classList.add('hidden');
            document.getElementById('modal-verify').classList.remove('hidden');
        }

        function setAction(action) {
            document.getElementById('verify-action').value = action;
            const rej = document.getElementById('rej-field');
            if (action === 'reject') {
                rej.classList.remove('hidden');
            } else {
                rej.classList.add('hidden');
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