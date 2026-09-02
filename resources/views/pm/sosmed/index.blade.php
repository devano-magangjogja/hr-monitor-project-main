@extends('layouts.app')
@section('title', 'Manajemen Sosmed')
@section('page-title', 'Manajemen Sosmed')
@section('page-subtitle', 'Kelola akun tanggung jawab Anda & verifikasi hasil tim sosmed')
@section('sidebar')
    @include('components.sidebar-pm')
@endsection

@section('content')
    @include('components.notification-popup')

    {{-- ═══ STAT CARDS ══════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs font-medium text-gray-500 mb-1">Akun Tanggung Jawab</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total_accounts'] }}</p>
            <p class="text-[11px] text-gray-400 mt-0.5">di-assign HR ke Anda</p>
        </div>
        <div class="bg-white rounded-xl border {{ $stats['pending_today'] > 0 ? 'border-amber-300 bg-amber-50/20 ring-2 ring-amber-400/30' : 'border-gray-200' }} p-4 shadow-sm relative">
            <p class="text-xs font-medium text-gray-500 mb-1">Perlu Diurus Hari Ini</p>
            <p class="text-2xl font-bold text-amber-600">{{ $stats['pending_today'] }}</p>
            <p class="text-[11px] text-amber-600 mt-0.5">belum disubmit</p>
        </div>
        <div class="bg-white rounded-xl border {{ $stats['need_pm_verify'] > 0 ? 'border-blue-300 bg-blue-50/30 ring-2 ring-blue-400/30' : 'border-gray-200' }} p-4 shadow-sm relative">
            @if($stats['need_pm_verify'] > 0)
                <span class="absolute top-3 right-3 flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                </span>
            @endif
            <p class="text-xs font-medium text-gray-500 mb-1">Perlu Verif PM</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['need_pm_verify'] }}</p>
            <p class="text-[11px] text-blue-600 mt-0.5">dari tim sosmed</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs font-medium text-gray-500 mb-1">Menunggu HR Staff</p>
            <p class="text-2xl font-bold text-purple-600">{{ $stats['waiting_hr'] }}</p>
            <p class="text-[11px] text-gray-400 mt-0.5">lolos verif PM</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs font-medium text-gray-500 mb-1">Disetujui Final</p>
            <p class="text-2xl font-bold text-emerald-600">{{ $stats['approved_final'] }}</p>
            <p class="text-[11px] text-emerald-600 mt-0.5">selesai 100%</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs font-medium text-gray-500 mb-1">Ditolak / Revisi</p>
            <p class="text-2xl font-bold text-rose-600">{{ $stats['rejected'] }}</p>
            <p class="text-[11px] text-rose-600 mt-0.5">perlu perbaikan</p>
        </div>
    </div>

    {{-- ═══ TABS ════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex border-b border-gray-200 overflow-x-auto">

            {{-- Tab 1: Akun Tanggung Jawab --}}
            <a href="{{ route('pm.sosmed.index', ['tab' => 'accounts']) }}"
               class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 transition
                      {{ $tab === 'accounts' ? 'border-primary-600 text-primary-600 bg-primary-50/50' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Akun Saya ({{ $accounts->count() }})
            </a>

            {{-- Tab 2: Verifikasi --}}
            <a href="{{ route('pm.sosmed.index', ['tab' => 'approvals']) }}"
               class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 transition
                      {{ $tab === 'approvals' ? 'border-primary-600 text-primary-600 bg-primary-50/50' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Verifikasi Tim Sosmed
                @if($stats['need_pm_verify'] > 0)
                    <span class="ml-1 px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-blue-600 text-white">
                        {{ $stats['need_pm_verify'] }}
                    </span>
                @endif
            </a>
        </div>

        {{-- ──────────────────────────────────────────────────────────────
        TAB 1: AKUN TANGGUNG JAWAB + SUBMIT BUKTI PM
        ─────────────────────────────────────────────────────────────── --}}
        @if($tab === 'accounts')
            <div class="p-5">
                {{-- Header Section --}}
                <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Daftar Akun & Status Pengerjaan Hari Ini</h3>
                        <p class="mt-0.5 text-xs text-gray-500">
                            Klik <strong>Submit Bukti</strong> untuk akun yang Anda kerjakan sendiri,
                            atau klik <strong>Atur Staff</strong> untuk mendelegasikan ke tim Sosmed.
                            Mendelegasikan ke staff akan otomatis membuat tugas harian untuk mereka.
                        </p>
                    </div>
                    <div class="shrink-0 rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-medium text-indigo-700">
                        Hari Ini: <span class="font-bold">{{ now()->translatedFormat('d M Y') }}</span>
                    </div>
                </div>

                {{-- Table Section --}}
                <div class="overflow-x-auto rounded-lg border border-gray-100">
                    <table class="w-full table-fixed text-sm">
                        <colgroup>
                            <col class="w-10">   {{-- Ceklis --}}
                            <col class="w-44">   {{-- Nama Akun --}}
                            <col class="w-28">   {{-- Platform --}}
                            <col class="w-20">   {{-- Link --}}
                            <col class="w-32">   {{-- PJ Sosmed --}}
                            <col class="w-28">   {{-- Bukti --}}
                            <col class="w-36">   {{-- Status --}}
                            <col class="w-28">   {{-- Aksi --}}
                        </colgroup>
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                <th class="px-3 py-3 text-center">✓</th>
                                <th class="px-4 py-3 text-left">Nama Akun</th>
                                <th class="px-4 py-3 text-left">Platform</th>
                                <th class="px-4 py-3 text-left">Link</th>
                                <th class="px-4 py-3 text-left">PJ Sosmed</th>
                                <th class="px-4 py-3 text-left">Bukti Hari Ini</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($accounts as $acc)
                                @php
                                    $todayTask   = $todayTasks[$acc->id] ?? null;
                                    $status      = $todayTask?->status ?? 'pending';
                                    $isSubmitted = in_array($status, ['done_by_staff', 'verified_by_pm', 'approved_hr']);
                                    $isRejected  = $status === 'rejected';
                                    $pmCanSubmit = is_null($acc->staff_id);
                                @endphp
                                <tr class="align-middle transition hover:bg-gray-50/60">

                                    {{-- Ceklis Status --}}
                                    <td class="px-3 py-3.5 text-center">
                                        @if($isSubmitted)
                                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full border border-emerald-300 bg-emerald-100 text-xs text-emerald-600" title="Sudah diurus">✓</span>
                                        @elseif($pmCanSubmit)
                                            <button type="button"
                                                onclick="openSubmitModal({{ $acc->id }}, '{{ addslashes($acc->name) }}', {{ $todayTask ? 'true' : 'false' }}, '{{ addslashes($todayTask?->description ?? '') }}')"
                                                class="inline-flex h-6 w-6 items-center justify-center rounded-md border-2 transition {{ $isRejected ? 'border-rose-400 bg-rose-50 hover:bg-rose-100' : 'border-gray-300 bg-white hover:border-primary-500' }}"
                                                title="{{ $isRejected ? 'Klik untuk revisi' : 'Klik untuk submit bukti' }}">
                                            </button>
                                        @else
                                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-gray-100 text-xs text-gray-400" title="Dikerjakan oleh tim sosmed">→</span>
                                        @endif
                                    </td>

                                    {{-- Nama Akun --}}
                                    <td class="px-4 py-3.5">
                                        <p class="truncate font-semibold text-gray-800" title="{{ $acc->name }}">{{ $acc->name }}</p>
                                        @if($isRejected && $todayTask?->rejection_note)
                                            <p class="mt-1 rounded border border-rose-200 bg-rose-50 px-1.5 py-1 text-xs leading-snug text-rose-600">
                                                ↩ "{{ $todayTask->rejection_note }}"
                                            </p>
                                        @endif
                                    </td>

                                    {{-- Platform --}}
                                    <td class="px-4 py-3.5">
                                        <span class="inline-flex items-center gap-1 rounded-md border px-2 py-1 text-xs font-medium {{ $acc->platform_color }}">
                                            {{ $acc->platform_icon }} {{ $acc->platform }}
                                        </span>
                                    </td>

                                    {{-- Link Profil --}}
                                    <td class="px-4 py-3.5">
                                        @if($acc->link)
                                            <a href="{{ $acc->link }}" target="_blank"
                                                class="inline-flex items-center gap-1 text-xs text-primary-600 hover:underline">
                                                <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                </svg>
                                                Buka
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-300">—</span>
                                        @endif
                                    </td>

                                    {{-- PJ Sosmed --}}
                                    <td class="px-4 py-3.5">
                                        @if($acc->staffUser)
                                            <div class="flex items-center gap-1.5">
                                                <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-pink-100 text-[10px] font-bold text-pink-700">
                                                    {{ strtoupper(substr($acc->staffUser->name, 0, 1)) }}
                                                </div>
                                                <span class="truncate text-xs font-medium text-gray-800">{{ $acc->staffUser->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-xs italic text-gray-400">Belum ada</span>
                                        @endif
                                    </td>

                                    {{-- Bukti Hari Ini --}}
                                    <td class="px-4 py-3.5">
                                        @if($todayTask && $todayTask->hasLinks())
                                            <button type="button"
                                                onclick="openLinksPopup({{ json_encode($todayTask->link_upload) }}, '{{ addslashes($acc->name) }}')"
                                                class="inline-flex items-center gap-1 text-xs font-medium text-primary-600 hover:underline">
                                                <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                                </svg>
                                                {{ $todayTask->link_count }} link
                                            </button>
                                        @else
                                            <span class="text-xs text-gray-300">—</span>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-4 py-3.5">
                                        @if($isSubmitted)
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $todayTask->status_badge_class }}">
                                                {{ $todayTask->status_label }}
                                            </span>
                                        @elseif($isRejected)
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $todayTask->status_badge_class }}">
                                                {{ $todayTask->status_label }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Belum dikerjakan</span>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-4 py-3.5 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            @if($pmCanSubmit)
                                                <button type="button"
                                                    onclick="openSubmitModal({{ $acc->id }}, '{{ addslashes($acc->name) }}', {{ $todayTask ? 'true' : 'false' }}, '{{ addslashes($todayTask?->description ?? '') }}')"
                                                    class="rounded-lg {{ $isRejected ? 'bg-rose-500 hover:bg-rose-600' : 'bg-primary-600 hover:bg-primary-700' }} px-2.5 py-1 text-xs font-semibold text-white shadow-sm transition">
                                                    {{ $isRejected ? 'Revisi' : 'Submit' }}
                                                </button>
                                            @endif
                                            <button type="button"
                                                onclick="openAssignStaffModal({{ $acc->id }}, '{{ addslashes($acc->name) }}', {{ $acc->staff_id ?? 'null' }})"
                                                class="rounded-lg border border-gray-200 bg-white px-2.5 py-1 text-xs font-medium text-gray-600 shadow-sm transition hover:border-primary-300 hover:text-primary-600">
                                                Staff
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-12 text-center text-sm text-gray-400">
                                        Belum ada akun sosial media yang di-assign ke Anda oleh HR Staff.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- ──────────────────────────────────────────────────────────────
             TAB 2: VERIFIKASI TIM SOSMED (done_by_staff → verified_by_pm)
        ─────────────────────────────────────────────────────────────── --}}
        @if($tab === 'approvals')
        <div class="p-4 sm:p-5">
            <div class="mb-4">
                <h3 class="text-sm font-semibold text-gray-800">Verifikasi Hasil Tim Sosmed (Level 1)</h3>
                <p class="text-xs text-gray-500 mt-0.5">Periksa bukti dari tim sosmed. Setelah diverifikasi, tugas diteruskan ke HR Staff untuk approval final.</p>
            </div>

            <div class="space-y-3">
                @forelse($pendingVerification as $task)
                    <div class="p-4 bg-blue-50/60 border border-blue-200 rounded-xl hover:border-blue-300 transition">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-semibold text-gray-800 text-sm">{{ $task->title }}</span>
                                    <span class="px-2 py-0.5 rounded-md text-xs font-medium border {{ $task->account?->platform_color ?? 'bg-gray-100' }}">
                                        {{ $task->account?->platform_icon }} {{ $task->account?->name }}
                                    </span>
                                </div>
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1.5 text-xs text-gray-500">
                                    <span>Dikerjakan: <strong class="text-gray-800">{{ $task->assignedUser?->name ?? '-' }}</strong></span>
                                    <span class="hidden sm:inline">·</span>
                                    <span>{{ $task->task_date->translatedFormat('d M Y') }}</span>
                                    @if($task->hasLinks())
                                        <button type="button"
                                            onclick="openLinksPopup({{ json_encode($task->link_upload) }}, '{{ addslashes($task->title) }}')"
                                            class="inline-flex items-center gap-1 text-primary-600 font-medium hover:underline">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                            </svg>
                                            {{ $task->link_count }} Bukti
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-shrink-0 self-center">
                                <button onclick="openVerifyModal({{ $task->id }}, '{{ addslashes($task->title) }}')"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition shadow-sm whitespace-nowrap">
                                    Verifikasi
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center bg-gray-50 rounded-xl border border-dashed border-gray-200">
                        <p class="text-sm text-gray-500">Tidak ada tugas yang menunggu verifikasi PM saat ini.</p>
                    </div>
                @endforelse
            </div>

            {{-- Riwayat Approval --}}
            @if($approvalHistory->count() > 0)
                <div class="mt-6">
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Riwayat Approval</h4>

                    {{-- Desktop table (md+) --}}
                    <div class="hidden md:block overflow-x-auto rounded-lg border border-gray-100">
                        <table class="w-full text-sm table-fixed">
                            <colgroup>
                                <col class="w-2/5">
                                <col class="w-1/5">
                                <col class="w-24">
                                <col class="w-28">
                                <col class="w-36">
                            </colgroup>
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    <th class="px-4 py-3 text-left">Tugas</th>
                                    <th class="px-4 py-3 text-left">Dikerjakan</th>
                                    <th class="px-4 py-3 text-left">Bukti</th>
                                    <th class="px-4 py-3 text-left">Tanggal</th>
                                    <th class="px-4 py-3 text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($approvalHistory as $t)
                                    <tr class="hover:bg-gray-50/80 transition align-top">
                                        <td class="px-4 py-3">
                                            <p class="font-medium text-gray-800 truncate" title="{{ $t->title }}">{{ $t->title }}</p>
                                            <p class="text-xs text-gray-400">{{ $t->account?->name }} ({{ $t->account?->platform }})</p>
                                            @if($t->rejection_note)
                                                <p class="text-xs text-rose-600 mt-0.5 truncate">↩ {{ $t->rejection_note }}</p>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-700">{{ $t->assignedUser?->name ?? '—' }}</td>
                                        <td class="px-4 py-3">
                                            @if($t->hasLinks())
                                                <button type="button"
                                                    onclick="openLinksPopup({{ json_encode($t->link_upload) }}, '{{ addslashes($t->title) }}')"
                                                    class="inline-flex items-center gap-1 text-xs text-primary-600 hover:underline font-medium">
                                                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                                    </svg>
                                                    {{ $t->link_count }} link
                                                </button>
                                            @else
                                                <span class="text-xs text-gray-300">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-500">{{ $t->task_date->translatedFormat('d M Y') }}</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $t->status_badge_class }}">
                                                {{ $t->status_label }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile cards (< md) --}}
                    <div class="md:hidden space-y-3">
                        @foreach($approvalHistory as $t)
                        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-800 truncate text-sm">{{ $t->title }}</p>
                                    <p class="text-xs text-gray-400">{{ $t->account?->name }} ({{ $t->account?->platform }})</p>
                                </div>
                                <span class="flex-shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $t->status_badge_class }}">
                                    {{ $t->status_label }}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs mt-2">
                                <div>
                                    <p class="text-gray-400 mb-0.5">Dikerjakan</p>
                                    <p class="font-medium text-gray-700">{{ $t->assignedUser?->name ?? '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-400 mb-0.5">Tanggal</p>
                                    <p class="text-gray-700">{{ $t->task_date->translatedFormat('d M Y') }}</p>
                                </div>
                                @if($t->hasLinks())
                                <div class="col-span-2">
                                    <p class="text-gray-400 mb-0.5">Bukti</p>
                                    <button type="button"
                                        onclick="openLinksPopup({{ json_encode($t->link_upload) }}, '{{ addslashes($t->title) }}')"
                                        class="inline-flex items-center gap-1 text-primary-600 font-medium hover:underline">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                        </svg>
                                        {{ $t->link_count }} link bukti
                                    </button>
                                </div>
                                @endif
                                @if($t->rejection_note)
                                <div class="col-span-2">
                                    <p class="text-xs text-rose-600 bg-rose-50 px-2 py-1 rounded border border-rose-200">↩ {{ $t->rejection_note }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        @endif
    </div>

    {{-- ═══ MODAL: SUBMIT BUKTI (PM) ════════════════════════════════════ --}}
    <div id="modal-submit" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('modal-submit').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg z-10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-bold text-gray-800">Submit Bukti Konten</h3>
                <button onclick="document.getElementById('modal-submit').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="form-submit" method="POST" action="" class="p-6 pt-1 space-y-4">
                @csrf
                <div>
                    <p class="text-xs text-gray-500 mb-0.5">Akun Sosmed</p>
                    <p id="submit-account-name" class="text-sm font-semibold text-gray-800"></p>
                </div>

                {{-- Link Bukti (multi) --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Link Bukti Konten <span class="text-red-500">*</span>
                        <span class="font-normal text-gray-400 ml-1">(bisa lebih dari satu)</span>
                    </label>
                    <div id="links-container" class="space-y-2">
                        <div class="flex gap-2 link-row">
                            <input type="url" name="links[]" required placeholder="https://instagram.com/p/xxx"
                                class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                            <button type="button" onclick="removeLinkRow(this)" class="text-gray-300 hover:text-rose-500 px-1 transition hidden remove-btn">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                    <button type="button" onclick="addLinkRow()"
                        class="mt-2 inline-flex items-center gap-1 text-xs text-primary-600 hover:text-primary-700 font-medium">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Link Lain
                    </button>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan (Opsional)</label>
                    <textarea id="submit-description" name="description" rows="2" placeholder="Brief konten / keterangan tambahan..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none"></textarea>
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="document.getElementById('modal-submit').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700">Batal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-semibold">
                        Kirim Bukti
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══ MODAL: ASSIGN STAFF ══════════════════════════════════════════ --}}
    <div id="modal-assign-staff" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('modal-assign-staff').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm z-10">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
                <h3 class="text-base font-bold text-gray-800">Delegasikan ke Staff Sosmed</h3>
                <button onclick="document.getElementById('modal-assign-staff').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="form-assign-staff" method="POST" action="" class="p-5 pt-0.5 space-y-4">
                @csrf @method('PATCH')
                <div>
                    <p class="text-xs text-gray-500 mb-0.5">Akun</p>
                    <p id="staff-acc-name" class="text-sm font-semibold text-gray-800"></p>
                </div>
                <div class="bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 text-xs text-amber-700">
                    Setelah disimpan, tugas ini otomatis tersimpan untuk staff yang dipilih.
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Pilih Staff Sosmed <span class="text-red-500">*</span></label>
                    <select name="staff_id" id="staff-sel-input" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="">Tidak ada staff sosmed terpilih</option>
                        @foreach($sosmedStaff as $st)
                            <option value="{{ $st->id }}">{{ $st->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('modal-assign-staff').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-semibold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══ MODAL: VERIFIKASI PM ══════════════════════════════════════════ --}}
    <div id="modal-verify" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('modal-verify').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm z-10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-bold text-gray-800">Verifikasi Tugas (PM)</h3>
                <button onclick="document.getElementById('modal-verify').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
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
                    <textarea name="rejection_note" rows="3" placeholder="Catatan perbaikan untuk staff..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:outline-none"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" onclick="setPmAction('reject')"
                        class="flex-1 px-4 py-2 border border-red-300 text-red-600 text-sm font-semibold rounded-lg hover:bg-red-50">Tolak</button>
                    <button type="submit" onclick="setPmAction('verify')"
                        class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg">Verifikasi</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══ MODAL: DETAIL LINKS POPUP ════════════════════════════════════ --}}
    <div id="modal-links" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('modal-links').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <div>
                    <h3 class="text-base font-bold text-gray-800">Detail Bukti Konten</h3>
                    <p id="links-popup-title" class="text-xs text-gray-500 mt-0.5"></p>
                </div>
                <button onclick="document.getElementById('modal-links').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div id="links-popup-body" class="p-6 space-y-2 max-h-80 overflow-y-auto"></div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
// ── Submit Modal ──────────────────────────────────────────────────────────────
function openSubmitModal(accId, accName, hasExisting, existingDesc) {
    document.getElementById('submit-account-name').textContent = accName;
    document.getElementById('form-submit').action = `/pm/sosmed/accounts/${accId}/submit`;
    document.getElementById('submit-description').value = existingDesc || '';

    // Reset link rows ke 1
    const container = document.getElementById('links-container');
    container.innerHTML = `
        <div class="flex gap-2 link-row">
            <input type="url" name="links[]" required placeholder="https://instagram.com/p/xxx"
                class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
            <button type="button" onclick="removeLinkRow(this)" class="text-gray-300 hover:text-rose-500 px-1 transition hidden remove-btn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>`;

    document.getElementById('modal-submit').classList.remove('hidden');
}

function addLinkRow() {
    const container = document.getElementById('links-container');
    const row = document.createElement('div');
    row.className = 'flex gap-2 link-row';
    row.innerHTML = `
        <input type="url" name="links[]" placeholder="https://tiktok.com/@xxx/video/xxx"
            class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
        <button type="button" onclick="removeLinkRow(this)" class="text-gray-300 hover:text-rose-500 px-1 transition remove-btn">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>`;
    container.appendChild(row);
    updateRemoveButtons();
}

function removeLinkRow(btn) {
    const row = btn.closest('.link-row');
    row.remove();
    updateRemoveButtons();
}

function updateRemoveButtons() {
    const rows = document.querySelectorAll('#links-container .link-row');
    rows.forEach(r => {
        const btn = r.querySelector('.remove-btn');
        if (btn) btn.classList.toggle('hidden', rows.length === 1);
    });
}

// ── Assign Staff Modal ────────────────────────────────────────────────────────
function openAssignStaffModal(accId, accName, currentStaffId) {
    document.getElementById('staff-acc-name').textContent = accName;
    document.getElementById('form-assign-staff').action = `/pm/sosmed/accounts/${accId}/assign`;
    document.getElementById('staff-sel-input').value = currentStaffId ?? '';
    document.getElementById('modal-assign-staff').classList.remove('hidden');
}

// ── Verify Modal ──────────────────────────────────────────────────────────────
function openVerifyModal(taskId, title) {
    document.getElementById('verify-task-title').textContent = title;
    document.getElementById('form-verify').action = `/pm/sosmed/tasks/${taskId}/verify`;
    document.getElementById('rej-field').classList.add('hidden');
    document.getElementById('verify-action').value = 'verify';
    document.getElementById('modal-verify').classList.remove('hidden');
}

function setPmAction(action) {
    document.getElementById('verify-action').value = action;
    const rej = document.getElementById('rej-field');
    action === 'reject' ? rej.classList.remove('hidden') : rej.classList.add('hidden');
}

// ── Links Popup ───────────────────────────────────────────────────────────────
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
