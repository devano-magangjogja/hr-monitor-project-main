@extends('layouts.app')
@section('title', 'Approval Tugas Sosmed')
@section('page-title', 'Approval Tugas Sosmed')
@section('page-subtitle', 'Verifikasi tugas tim Sosmed sebagai pengganti PM')
@section('sidebar')
    @include('components.sidebar-assistant')
@endsection

@section('content')
    @include('components.notification-popup')

    {{-- ═══ INFO BANNER ═══════════════════════════════════════════════════════ --}}
    <div class="mb-5 flex items-start gap-3 bg-orange-50 border border-orange-200 rounded-xl px-4 py-3">
        <div class="flex-shrink-0 mt-0.5">
            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="text-xs text-orange-800 leading-relaxed">
            <p class="font-semibold mb-0.5">Fungsi Backup Approval</p>
            <p>Halaman ini digunakan saat PM sedang tidak tersedia. Setelah Anda menyetujui tugas, status akan diteruskan ke <strong>HR Staff</strong> untuk persetujuan final.</p>
        </div>
    </div>

    {{-- ═══ STAT CARDS ════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="bg-white rounded-xl border {{ $stats['pending'] > 0 ? 'border-orange-300 bg-orange-50/30 ring-2 ring-orange-400/30' : 'border-gray-200' }} p-4 shadow-sm relative">
            @if($stats['pending'] > 0)
                <span class="absolute top-3 right-3 flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
                </span>
            @endif
            <p class="text-xs font-medium text-gray-500 mb-1">Perlu Diverifikasi</p>
            <p class="text-2xl font-bold text-orange-600">{{ $stats['pending'] }}</p>
            <p class="text-[11px] text-orange-500 mt-0.5">menunggu approval</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs font-medium text-gray-500 mb-1">Menunggu HR Staff</p>
            <p class="text-2xl font-bold text-indigo-600">{{ $stats['approved'] }}</p>
            <p class="text-[11px] text-gray-400 mt-0.5">lolos verif level 1</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs font-medium text-gray-500 mb-1">Disetujui Final</p>
            <p class="text-2xl font-bold text-emerald-600">{{ $stats['final_ok'] }}</p>
            <p class="text-[11px] text-emerald-500 mt-0.5">selesai 100%</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs font-medium text-gray-500 mb-1">Ditolak</p>
            <p class="text-2xl font-bold text-rose-600">{{ $stats['rejected'] }}</p>
            <p class="text-[11px] text-gray-400 mt-0.5">perlu revisi</p>
        </div>
    </div>

    {{-- ═══ TABS ════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex border-b border-gray-200 overflow-x-auto">

            {{-- Tab 1: Perlu Diverifikasi --}}
            <a href="{{ route('assistant.sosmed.index', ['tab' => 'pending']) }}"
               class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 transition
                      {{ $tab === 'pending' ? 'border-orange-500 text-orange-600 bg-orange-50/50' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Perlu Diverifikasi
                @if($stats['pending'] > 0)
                    <span class="ml-1 px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-orange-500 text-white">
                        {{ $stats['pending'] }}
                    </span>
                @endif
            </a>

            {{-- Tab 2: Riwayat --}}
            <a href="{{ route('assistant.sosmed.index', ['tab' => 'history']) }}"
               class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 transition
                      {{ $tab === 'history' ? 'border-orange-500 text-orange-600 bg-orange-50/50' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Riwayat Approval Saya
            </a>
        </div>

        {{-- ─── TAB 1: PERLU DIVERIFIKASI ──────────────────────────────────────── --}}
        @if($tab === 'pending')
            <div class="p-5">
                <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Tugas Sosmed Menunggu Verifikasi Level-1</h3>
                        <p class="mt-0.5 text-xs text-gray-500">
                            Tugas ini sudah diselesaikan oleh tim Sosmed dan menunggu persetujuan dari PM.
                            Anda bisa menyetujuinya sebagai <strong>pengganti PM</strong>.
                        </p>
                    </div>
                    <div class="shrink-0 rounded-lg border border-orange-200 bg-orange-50 px-3 py-1.5 text-xs font-medium text-orange-700">
                        Hari Ini: <span class="font-bold">{{ now()->translatedFormat('d M Y') }}</span>
                    </div>
                </div>

                @if($pendingVerification->isEmpty())
                    <div class="flex flex-col items-center justify-center py-16 text-center">
                        <div class="w-16 h-16 rounded-full bg-green-50 flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-700">Tidak Ada Tugas yang Perlu Diverifikasi</p>
                        <p class="text-xs text-gray-400 mt-1 max-w-xs">
                            Semua tugas Sosmed sudah diverifikasi atau belum ada yang diselesaikan tim.
                        </p>
                    </div>
                @else
                    {{-- Desktop Table (md+) --}}
                    <div class="hidden md:block overflow-x-auto rounded-lg border border-gray-100">
                        <table class="w-full table-fixed text-sm">
                            <colgroup>
                                <col class="w-[28%]"> {{-- Akun --}}
                                <col class="w-28">    {{-- Platform --}}
                                <col class="w-[20%]"> {{-- Dikerjakan Oleh --}}
                                <col class="w-32">    {{-- Bukti --}}
                                <col class="w-28">    {{-- Tanggal --}}
                                <col class="w-36">    {{-- Aksi --}}
                            </colgroup>
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    <th class="px-4 py-3 text-left">Akun Sosmed</th>
                                    <th class="px-4 py-3 text-left">Platform</th>
                                    <th class="px-4 py-3 text-left">Dikerjakan Oleh</th>
                                    <th class="px-4 py-3 text-left">Bukti Konten</th>
                                    <th class="px-4 py-3 text-left">Tanggal</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($pendingVerification as $task)
                                    <tr class="align-middle hover:bg-orange-50/30 transition">
                                        {{-- Akun --}}
                                        <td class="px-4 py-3.5 min-w-0">
                                            <p class="font-semibold text-gray-800 truncate">{{ $task->account?->name ?? '—' }}</p>
                                            @if($task->title)
                                                <p class="text-xs text-gray-400 truncate">{{ $task->title }}</p>
                                            @endif
                                        </td>
                                        {{-- Platform --}}
                                        <td class="px-4 py-3.5">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium border {{ $task->account?->platform_color ?? 'bg-gray-50 text-gray-600 border-gray-200' }}">
                                                {{ $task->account?->platform ?? '—' }}
                                            </span>
                                        </td>
                                        {{-- Dikerjakan Oleh --}}
                                        <td class="px-4 py-3.5 text-xs text-gray-700">
                                            <p class="font-medium">{{ $task->assignedUser?->name ?? '—' }}</p>
                                            <p class="text-gray-400">{{ $task->assignedUser?->role_label ?? '' }}</p>
                                        </td>
                                        {{-- Bukti --}}
                                        <td class="px-4 py-3.5">
                                            @if($task->hasLinks())
                                                <button type="button"
                                                    onclick="openLinksPopup({{ json_encode($task->link_upload) }}, '{{ addslashes($task->account?->name ?? '') }}')"
                                                    class="inline-flex items-center gap-1 text-xs text-primary-600 font-medium hover:underline">
                                                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                                    </svg>
                                                    {{ $task->link_count }} link
                                                </button>
                                            @else
                                                <span class="text-xs text-gray-300">—</span>
                                            @endif
                                        </td>
                                        {{-- Tanggal --}}
                                        <td class="px-4 py-3.5 text-xs text-gray-500">
                                            {{ $task->task_date?->translatedFormat('d M Y') ?? '—' }}
                                        </td>
                                        {{-- Aksi --}}
                                        <td class="px-4 py-3.5 text-center">
                                            <button type="button"
                                                onclick="openVerifyModal({{ $task->id }}, '{{ addslashes($task->account?->name ?? $task->title) }}')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold rounded-lg shadow-sm transition whitespace-nowrap">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Verifikasi
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile Cards --}}
                    <div class="md:hidden space-y-3">
                        @foreach($pendingVerification as $task)
                            <div class="rounded-xl border border-orange-100 bg-orange-50/30 p-4 shadow-sm">
                                <div class="flex items-start justify-between gap-2 mb-3">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-800 text-sm truncate">{{ $task->account?->name ?? '—' }}</p>
                                        <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-md text-xs font-medium border {{ $task->account?->platform_color ?? 'bg-gray-50 text-gray-600 border-gray-200' }}">
                                            {{ $task->account?->platform ?? '—' }}
                                        </span>
                                    </div>
                                    <span class="flex-shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                        Menunggu Verif
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-xs mb-3">
                                    <div>
                                        <p class="text-gray-400 mb-0.5">Dikerjakan</p>
                                        <p class="font-medium text-gray-700">{{ $task->assignedUser?->name ?? '—' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-400 mb-0.5">Tanggal</p>
                                        <p class="text-gray-700">{{ $task->task_date?->translatedFormat('d M Y') ?? '—' }}</p>
                                    </div>
                                    @if($task->hasLinks())
                                        <div class="col-span-2">
                                            <p class="text-gray-400 mb-0.5">Bukti Konten</p>
                                            <button type="button"
                                                onclick="openLinksPopup({{ json_encode($task->link_upload) }}, '{{ addslashes($task->account?->name ?? '') }}')"
                                                class="inline-flex items-center gap-1 text-primary-600 font-medium hover:underline">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                                </svg>
                                                {{ $task->link_count }} link bukti
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <button type="button"
                                    onclick="openVerifyModal({{ $task->id }}, '{{ addslashes($task->account?->name ?? $task->title) }}')"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Verifikasi Tugas Ini
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        {{-- ─── TAB 2: RIWAYAT APPROVAL SAYA ───────────────────────────────────── --}}
        @if($tab === 'history')
            <div class="p-5">
                <div class="mb-4">
                    <h3 class="text-sm font-semibold text-gray-800">Riwayat Approval yang Pernah Anda Lakukan</h3>
                    <p class="mt-0.5 text-xs text-gray-500">50 approval terbaru yang dilakukan oleh HR Assistant.</p>
                </div>

                @if($approvalHistory->isEmpty())
                    <div class="flex flex-col items-center justify-center py-16 text-center">
                        <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-700">Belum Ada Riwayat</p>
                        <p class="text-xs text-gray-400 mt-1">Riwayat approval oleh HR Assistant akan muncul di sini.</p>
                    </div>
                @else
                    {{-- Desktop Table --}}
                    <div class="hidden md:block overflow-x-auto rounded-lg border border-gray-100">
                        <table class="w-full text-sm table-fixed">
                            <colgroup>
                                <col class="w-[28%]"> {{-- Akun --}}
                                <col class="w-[20%]"> {{-- Diapprove Oleh --}}
                                <col class="w-28">    {{-- Aksi --}}
                                <col class="w-[30%]"> {{-- Catatan --}}
                                <col class="w-28">    {{-- Waktu --}}
                            </colgroup>
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    <th class="px-4 py-3 text-left">Akun Sosmed</th>
                                    <th class="px-4 py-3 text-left">Diapprove Oleh</th>
                                    <th class="px-4 py-3 text-left">Hasil</th>
                                    <th class="px-4 py-3 text-left">Catatan</th>
                                    <th class="px-4 py-3 text-left">Waktu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($approvalHistory as $log)
                                    <tr class="hover:bg-gray-50/80 transition align-top">
                                        <td class="px-4 py-3">
                                            <p class="font-semibold text-gray-800 truncate">{{ $log->task?->account?->name ?? '—' }}</p>
                                            <p class="text-xs text-gray-400">{{ $log->task?->account?->platform ?? '' }}</p>
                                        </td>
                                        <td class="px-4 py-3 text-xs">
                                            <p class="font-medium text-gray-700">{{ $log->user_name }}</p>
                                            <p class="text-gray-400">{{ $log->role_name }}</p>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border {{ $log->action_badge_class }}">
                                                {{ $log->action_label }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-500">
                                            <p class="line-clamp-2">{{ $log->notes ?? '—' }}</p>
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-400">
                                            {{ $log->created_at->translatedFormat('d M Y H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile Cards --}}
                    <div class="md:hidden space-y-3">
                        @foreach($approvalHistory as $log)
                            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-800 truncate text-sm">{{ $log->task?->account?->name ?? '—' }}</p>
                                        <p class="text-xs text-gray-400">{{ $log->task?->account?->platform ?? '' }}</p>
                                    </div>
                                    <span class="flex-shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border {{ $log->action_badge_class }}">
                                        {{ $log->action_label }}
                                    </span>
                                </div>
                                <div class="text-xs text-gray-500 space-y-1">
                                    <p><span class="text-gray-400">Oleh:</span> {{ $log->user_name }}</p>
                                    <p><span class="text-gray-400">Waktu:</span> {{ $log->created_at->translatedFormat('d M Y H:i') }}</p>
                                    @if($log->notes)
                                        <p class="text-gray-600 bg-gray-50 rounded px-2 py-1 mt-1">{{ $log->notes }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>

    {{-- ═══ MODAL VERIFIKASI (APPROVE / REJECT) ══════════════════════════════ --}}
    <div id="modal-verify" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
         x-data="{ action: 'verify' }"
         @open-verify.window="action = 'verify'">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeVerifyModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10 overflow-hidden">

            {{-- Modal Header with Dynamic Color --}}
            <div class="px-6 py-4 flex items-center justify-between transition-colors"
                 :class="action === 'verify' ? 'bg-orange-50 border-b border-orange-100' : 'bg-rose-50 border-b border-rose-100'">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors"
                         :class="action === 'verify' ? 'bg-orange-100 text-orange-600' : 'bg-rose-100 text-rose-600'">
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
                            x-text="action === 'verify' ? 'Verifikasi & Setujui Tugas (Asisten)' : 'Tolak & Kembalikan Tugas'"></h3>
                        <p class="text-[11px] text-gray-400">Verifikasi tugas tim sosmed sebagai backup PM</p>
                    </div>
                </div>
                <button type="button" onclick="closeVerifyModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="form-verify" method="POST" action="" class="p-6 space-y-4">
                @csrf @method('PATCH')
                <input type="hidden" name="action" :value="action">

                {{-- Task Info Card --}}
                <div class="bg-gray-50 rounded-xl p-3.5 border border-gray-200/80">
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Tugas yang Ditinjau</p>
                    <p id="verify-task-name" class="text-sm font-bold text-gray-800 break-words"></p>
                </div>

                {{-- Action Toggle (Segmented control) --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-2">Keputusan Verifikasi</label>
                    <div class="grid grid-cols-2 gap-2 p-1 bg-gray-100 rounded-xl">
                        <button type="button"
                            @click="action = 'verify'"
                            :class="action === 'verify' ? 'bg-white text-orange-700 shadow-sm font-bold' : 'text-gray-500 font-medium hover:text-gray-700'"
                            class="py-2 text-xs rounded-lg transition-all flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Setujui
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
                <div x-show="action === 'verify'" class="bg-orange-50 border border-orange-100 rounded-xl p-3 text-xs text-orange-800 leading-relaxed">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-orange-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p>Tugas akan diteruskan ke <strong>HR Staff</strong> untuk persetujuan final. Log akan mencatat bahwa verifikasi dilakukan oleh HR Assistant sebagai pengganti PM.</p>
                    </div>
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeVerifyModal()"
                        class="flex-1 px-4 py-2.5 rounded-xl border border-gray-300 text-sm text-gray-600 font-medium hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition shadow-sm"
                        :class="action === 'verify' ? 'bg-orange-500 hover:bg-orange-600' : 'bg-rose-500 hover:bg-rose-600'">
                        <span x-show="action === 'verify'">Setujui Tugas</span>
                        <span x-show="action === 'reject'">Tolak Tugas</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══ POPUP LIHAT LINKS ════════════════════════════════════════════════ --}}
    <div id="popup-links" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeLinksPopup()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm z-10 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Bukti Konten</h3>
                    <p id="popup-links-account" class="text-xs text-gray-400 mt-0.5"></p>
                </div>
                <button onclick="closeLinksPopup()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="popup-links-list" class="p-5 space-y-2 max-h-72 overflow-y-auto"></div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    // ── Verify Modal ──────────────────────────────────────────────────────
    function openVerifyModal(taskId, taskName) {
        document.getElementById('verify-task-name').textContent = taskName;
        document.getElementById('form-verify').action = `/assistant/sosmed/tasks/${taskId}/verify`;
        const ta = document.querySelector('#form-verify textarea[name="rejection_note"]');
        if (ta) ta.value = '';
        window.dispatchEvent(new CustomEvent('open-verify'));
        document.getElementById('modal-verify').classList.remove('hidden');
    }
    function closeVerifyModal() {
        document.getElementById('modal-verify').classList.add('hidden');
    }

    // ── Links Popup ───────────────────────────────────────────────────────
    function openLinksPopup(links, accountName) {
        document.getElementById('popup-links-account').textContent = accountName;
        const list = document.getElementById('popup-links-list');
        list.innerHTML = '';
        (links || []).forEach((url, i) => {
            list.innerHTML += `
                <a href="${url}" target="_blank" rel="noopener"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-100 hover:border-primary-300 hover:bg-primary-50 transition group">
                    <svg class="w-3.5 h-3.5 text-gray-400 group-hover:text-primary-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    <span class="text-xs text-primary-700 font-medium truncate group-hover:underline">Link ${i+1}: ${url}</span>
                </a>`;
        });
        document.getElementById('popup-links').classList.remove('hidden');
    }
    function closeLinksPopup() {
        document.getElementById('popup-links').classList.add('hidden');
    }
</script>
@endpush
