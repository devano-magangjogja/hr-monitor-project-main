@extends('layouts.app')
@section('title', 'Manajemen Sosmed')
@section('page-title', 'Manajemen Sosmed')
@section('page-subtitle', 'Kelola akun tanggung jawab Anda, buat tugas & verifikasi hasil tim')
@section('sidebar')
    @include('components.sidebar-pm')
@endsection

@section('content')
    @include('components.notification-popup')

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- STAT CARDS --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs font-medium text-gray-500 mb-1">Akun Tanggung Jawab</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total_accounts'] }}</p>
            <p class="text-[11px] text-gray-400 mt-0.5">di-assign HR ke Anda</p>
        </div>
        <div
            class="bg-white rounded-xl border {{ $stats['unassigned_staff'] > 0 ? 'border-amber-300 bg-amber-50/20' : 'border-gray-200' }} p-4 shadow-sm">
            <p class="text-xs font-medium text-gray-500 mb-1">Belum Ada Staff</p>
            <p class="text-2xl font-bold text-amber-600">{{ $stats['unassigned_staff'] }}</p>
            <p class="text-[11px] text-amber-600 mt-0.5">perlu assign Sosmed</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs font-medium text-gray-500 mb-1">Total Tugas</p>
            <p class="text-2xl font-bold text-indigo-600">{{ $stats['total_tasks'] }}</p>
            <p class="text-[11px] text-gray-400 mt-0.5">tugas tim saya</p>
        </div>
        <div
            class="bg-white rounded-xl border {{ $stats['need_pm_verify'] > 0 ? 'border-blue-300 bg-blue-50/30 ring-2 ring-blue-400/30' : 'border-gray-200' }} p-4 shadow-sm relative">
            @if($stats['need_pm_verify'] > 0)
                <span class="absolute top-3 right-3 flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                </span>
            @endif
            <p class="text-xs font-medium text-gray-500 mb-1">Perlu Verif PM</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['need_pm_verify'] }}</p>
            <p class="text-[11px] text-blue-600 mt-0.5">menunggu approval</p>
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

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- TABS & CONTENT --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex border-b border-gray-200 overflow-x-auto">
            <a href="{{ route('pm.sosmed.index', ['tab' => 'tasks']) }}"
                class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 transition
                                                                                  {{ $tab === 'tasks' ? 'border-primary-600 text-primary-600 bg-primary-50/50' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                Tugas Tim Sosmed ({{ $sosmedTasks->count() }})
            </a>
            <a href="{{ route('pm.sosmed.index', ['tab' => 'accounts']) }}"
                class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 transition
                                                                                  {{ $tab === 'accounts' ? 'border-primary-600 text-primary-600 bg-primary-50/50' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Akun Tanggung Jawab Saya ({{ $accounts->count() }})
            </a>
            <a href="{{ route('pm.sosmed.index', ['tab' => 'approvals']) }}"
                class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 transition
                                                                                  {{ $tab === 'approvals' ? 'border-primary-600 text-primary-600 bg-primary-50/50' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Verifikasi & Approval PM
                @if($stats['need_pm_verify'] > 0)
                    <span class="ml-1 px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-blue-600 text-white">
                        {{ $stats['need_pm_verify'] }}
                    </span>
                @endif
            </a>
        </div>

        {{-- ── TAB 1: TUGAS TIM SOSMED ───────────────────────────────── --}}
        @if($tab === 'tasks')
            <div class="p-5">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Daftar Tugas Akun Sosmed</h3>
                        <p class="text-xs text-gray-500 mt-0.5">PM dapat memberikan tugas harian atau tugas custom ke staff
                            sosmed</p>
                    </div>
                    @if($accounts->count() > 0)
                        <button onclick="document.getElementById('modal-create-task').classList.remove('hidden')"
                            class="flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Buat Tugas Sosmed
                        </button>
                    @endif
                </div>

                @if($accounts->isEmpty())
                    <div class="p-8 text-center bg-gray-50 rounded-xl border border-dashed border-gray-200">
                        <p class="text-sm text-gray-500">Anda belum memiliki akun sosial media yang di-assign oleh HR Staff.</p>
                    </div>
                @else
                    <div class="overflow-x-auto rounded-lg border border-gray-100">
                        <table class="w-full text-sm min-w-[700px]">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Tugas</th>
                                    <th class="text-left px-4 py-3 font-semibold text-gray-600 w-36">Akun Terkait</th>
                                    <th class="text-left px-4 py-3 font-semibold text-gray-600 w-40">Staff Pelaksana (Nama User)
                                    </th>
                                    <th class="text-left px-4 py-3 font-semibold text-gray-600 w-32">Tanggal</th>
                                    <th class="text-left px-4 py-3 font-semibold text-gray-600 w-36">Bukti Konten</th>
                                    <th class="text-left px-4 py-3 font-semibold text-gray-600 w-36">Status</th>
                                    <th class="text-right px-4 py-3 font-semibold text-gray-600 w-24">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($sosmedTasks as $task)
                                    <tr class="hover:bg-gray-50/80 transition">
                                        <td class="px-4 py-3.5">
                                            <span class="font-semibold text-gray-800">{{ $task->title }}</span>
                                            <span
                                                class="ml-1 px-1.5 py-0.5 text-[10px] rounded font-medium {{ $task->type === 'daily' ? 'bg-gray-100 text-gray-600' : 'bg-purple-50 text-purple-700' }}">
                                                {{ $task->type === 'daily' ? 'Harian' : 'Custom' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3.5 text-xs text-gray-700">
                                            <span class="font-medium text-gray-800">{{ $task->account?->name }}</span>
                                            <p class="text-gray-400">{{ $task->account?->platform }}</p>
                                        </td>
                                        {{-- Staff Pelaksana User Name --}}
                                        <td class="px-4 py-3.5 text-xs text-gray-700">
                                            @if($task->assignedUser)
                                                <div class="flex items-center gap-1.5">
                                                    <div
                                                        class="w-6 h-6 rounded-full bg-pink-100 text-pink-700 font-bold flex items-center justify-center text-[10px]">
                                                        {{ strtoupper(substr($task->assignedUser->name, 0, 1)) }}
                                                    </div>
                                                    <span class="font-semibold text-gray-800">{{ $task->assignedUser->name }}</span>
                                                </div>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3.5 text-xs text-gray-500">
                                            {{ $task->task_date->translatedFormat('d M Y') }}
                                        </td>
                                        <td class="px-4 py-3.5">
                                            @if($task->link_upload)
                                                <a href="{{ $task->link_upload }}" target="_blank"
                                                    class="inline-flex items-center gap-1 text-xs text-primary-600 hover:underline">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                    </svg>
                                                    Buka Link
                                                </a>
                                            @else
                                                <span class="text-xs text-gray-300 italic">Belum ada</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $task->status_badge_class }}">
                                                {{ $task->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3.5 text-right">
                                            <div class="flex items-center justify-end gap-1">
                                                @if($task->status === 'done_by_staff')
                                                    <button
                                                        onclick="openVerifyModal({{ $task->id }}, '{{ addslashes($task->title) }}', '{{ addslashes($task->link_upload ?? '') }}')"
                                                        class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition"
                                                        title="Verifikasi">
                                                        Verif
                                                    </button>
                                                @endif
                                                @if($task->status === 'pending')
                                                    <form method="POST" action="{{ route('pm.sosmed.tasks.destroy', $task) }}"
                                                        onsubmit="return confirm('Hapus tugas ini?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="p-1 text-gray-400 hover:text-rose-600">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-400">Belum ada tugas dibuat untuk
                                            akun-akun Anda.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif

        {{-- ── TAB 2: AKUN SAYA ───────────────────────────────────────── --}}
        @if($tab === 'accounts')
            <div class="p-5">
                <div class="mb-4">
                    <h3 class="text-sm font-semibold text-gray-800">Akun Sosial Media Tanggung Jawab Anda</h3>
                    <p class="text-xs text-gray-500 mt-0.5">PM hanya dapat melihat dan menugaskan akun yang telah diberikan oleh
                        HR Staff.</p>
                </div>

                <div class="overflow-x-auto rounded-lg border border-gray-100">
                    <table class="w-full text-sm min-w-[700px]">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="text-left px-4 py-3 font-semibold text-gray-600">Nama Akun</th>
                                <th class="text-left px-4 py-3 font-semibold text-gray-600 w-50">Platform</th>
                                <th class="text-left px-4 py-3 font-semibold text-gray-600 w-50">Link URL</th>
                                <th class="text-left px-4 py-3 font-semibold text-gray-600 w-50">Penanggung Jawab Sosmed</th>
                                <th class="text-right px-4 py-3 font-semibold text-gray-600 w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($accounts as $acc)
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="px-4 py-3.5">
                                        <span class="font-semibold text-gray-800">{{ $acc->name }}</span>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-xs font-medium border {{ $acc->platform_color }}">
                                            {{ $acc->platform_icon }} {{ $acc->platform }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        @if($acc->link)
                                            <a href="{{ $acc->link }}" target="_blank" class="text-xs text-primary-600 hover:underline">
                                                {{ parse_url($acc->link, PHP_URL_HOST) ?? $acc->link }}
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                    {{-- Nama User Sosmed --}}
                                    <td class="px-4 py-3.5">
                                        @if($acc->staffUser)
                                            <div class="flex items-center gap-1.5">
                                                <div
                                                    class="w-6 h-6 rounded-full bg-pink-100 text-pink-700 font-bold flex items-center justify-center text-[10px]">
                                                    {{ strtoupper(substr($acc->staffUser->name, 0, 1)) }}
                                                </div>
                                                <span class="font-semibold text-gray-800 text-xs">{{ $acc->staffUser->name }}</span>
                                            </div>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                                Belum Di-assign
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-right">
                                        <button
                                            onclick="openAssignStaffModal({{ $acc->id }}, '{{ addslashes($acc->name) }}', {{ $acc->staff_id ?? 'null' }})"
                                            class="px-3 py-1 bg-primary-50 hover:bg-primary-100 text-primary-700 text-xs font-semibold rounded-lg transition">
                                            Atur Staff
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-400">Belum ada akun sosial media
                                        yang didelegasikan ke Anda oleh HR Staff.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- ── TAB 3: VERIFIKASI APPROVAL PM ──────────────────────────── --}}
        @if($tab === 'approvals')
            <div class="p-5">
                <div class="mb-4">
                    <h3 class="text-sm font-semibold text-gray-800">Menunggu Verifikasi PM (Level 1)</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Periksa bukti link konten dari staff sosmed. Setelah diverifikasi,
                        tugas akan diteruskan ke HR Staff untuk approval final.</p>
                </div>

                <div class="space-y-3 mb-6">
                    @forelse($pendingVerification as $task)
                        <div
                            class="flex items-center justify-between gap-4 p-4 bg-blue-50/60 border border-blue-200 rounded-xl hover:border-blue-300 transition">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-semibold text-gray-800 text-sm">{{ $task->title }}</span>
                                    <span
                                        class="px-2 py-0.5 rounded-md text-xs font-medium border {{ $task->account?->platform_color ?? 'bg-gray-100' }}">
                                        {{ $task->account?->platform_icon }} {{ $task->account?->name }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-3 mt-2 text-xs text-gray-500">
                                    <span>Staff Pelaksana: <strong
                                            class="text-gray-800">{{ $task->assignedUser?->name ?? '-' }}</strong></span>
                                    <span>·</span>
                                    <span>Tanggal: {{ $task->task_date->translatedFormat('d M Y') }}</span>
                                    @if($task->link_upload)
                                        <span>·</span>
                                        <a href="{{ $task->link_upload }}" target="_blank"
                                            class="text-primary-600 font-medium hover:underline inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                            Cek Link Hasil Konten
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <button
                                    onclick="openVerifyModal({{ $task->id }}, '{{ addslashes($task->title) }}', '{{ addslashes($task->link_upload ?? '') }}')"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition shadow-sm">
                                    Verifikasi (PM)
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center bg-gray-50 rounded-xl border border-dashed border-gray-200">
                            <p class="text-sm text-gray-500">Tidak ada tugas yang menunggu verifikasi PM saat ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif
    </div>

    {{-- ── MODAL BUAT TUGAS SOSMED (PM) ─────────────────────────────── --}}
    <div id="modal-create-task" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            onclick="document.getElementById('modal-create-task').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-bold text-gray-800">Buat Tugas Akun Sosmed</h3>
                <button onclick="document.getElementById('modal-create-task').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('pm.sosmed.tasks.store') }}" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Pilih Akun Sosmed <span
                            class="text-red-500">*</span></label>
                    <select name="sosmed_account_id" id="task-acc-sel" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="">-- Pilih Akun Anda --</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" data-staff="{{ $acc->staff_id }}">
                                {{ $acc->platform_icon }} {{ $acc->name }} ({{ $acc->platform }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Ditugaskan ke Staff Sosmed (Nama User)
                        <span class="text-red-500">*</span></label>
                    <select name="assigned_to" id="task-staff-sel" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="">-- Pilih Staff --</option>
                        @foreach($sosmedStaff as $st)
                            <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->role_label }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tipe Tugas <span
                            class="text-red-500">*</span></label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="radio" name="type" value="daily" checked class="text-primary-600">
                            Tugas Harian (Recurring)
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="radio" name="type" value="custom" class="text-primary-600">
                            Custom Task (Insidental)
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Judul Tugas <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="title" required placeholder="Contoh: Upload konten feed harian"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Deskripsi / Instruksi</label>
                    <textarea name="description" rows="2" placeholder="Detail instruksi..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Tugas <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="task_date" required value="{{ date('Y-m-d') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Deadline (Opsional)</label>
                        <input type="datetime-local" name="deadline"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-create-task').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700">Batal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-semibold">Kirim
                        Tugas</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── MODAL ASSIGN STAFF KE AKUN (PM) ─────────────────────────── --}}
    <div id="modal-assign-staff" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            onclick="document.getElementById('modal-assign-staff').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm z-10">
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-200">
                <h3 class="text-base font-bold text-gray-800">Atur Staff Sosmed</h3>
                <button onclick="document.getElementById('modal-assign-staff').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="form-assign-staff" method="POST" action="" class="px-5 pb-4 pt-0.5 space-y-3">
                @csrf @method('PATCH')

                <div>
                    <p class="text-xs text-gray-500 mb-0.5">Nama Akun</p>
                    <p id="staff-acc-name" class="text-sm font-semibold text-gray-800"></p>
                </div>

                <div class="pt-3">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Pilih Staff Sosmed <span class="text-red-500">*</span>
                    </label>

                    <select name="staff_id" id="staff-sel-input" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="">Pilih</option>

                        @foreach($sosmedStaff as $st)
                            <option value="{{ $st->id }}">
                                {{ $st->name }} ({{ $st->role_label }})
                            </option>
                        @endforeach
                    </select>

                    <p id="staff-error" class="hidden mt-1.5 text-xs text-red-600">
                        Silakan pilih staff terlebih dahulu.
                    </p>
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="document.getElementById('modal-assign-staff').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700">
                        Batal
                    </button>

                    <button type="submit" id="btn-save-staff" disabled
                        class="flex-1 px-4 py-2 bg-gray-300 text-gray-500 rounded-lg text-sm font-semibold cursor-not-allowed">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── MODAL VERIFIKASI PM (LEVEL 1) ────────────────────────────── --}}
    <div id="modal-verify" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            onclick="document.getElementById('modal-verify').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm z-10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-bold text-gray-800">Verifikasi Tugas (PM)</h3>
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
                <div id="verify-link-container" class="mt-2 text-xs"></div>
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
                    <button type="button" onclick="setPmAction('reject')"
                        class="flex-1 px-4 py-2 border border-red-300 text-red-600 text-sm font-semibold rounded-lg hover:bg-red-50">Tolak</button>
                    <button type="button" onclick="setPmAction('verify')"
                        class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg">Verifikasi
                        (Lolos)</button>
                </div>
                <button type="submit" id="btn-submit-verify"
                    class="hidden w-full px-4 py-2 text-white text-sm font-bold rounded-lg"></button>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function openAssignStaffModal(accId, accName, currentStaffId) {
            const staffSelect = document.getElementById('staff-sel-input');
            const saveButton = document.getElementById('btn-save-staff');
            const errorMessage = document.getElementById('staff-error');

            document.getElementById('staff-acc-name').textContent = accName;
            document.getElementById('form-assign-staff').action =
                `/pm/sosmed/accounts/${accId}/assign`;

            staffSelect.value = currentStaffId ?? '';

            errorMessage.classList.add('hidden');
            updateStaffSaveButton();

            document.getElementById('modal-assign-staff').classList.remove('hidden');
        }

        function updateStaffSaveButton() {
            const staffSelect = document.getElementById('staff-sel-input');
            const saveButton = document.getElementById('btn-save-staff');
            const errorMessage = document.getElementById('staff-error');

            if (staffSelect.value) {
                saveButton.disabled = false;

                saveButton.className =
                    'flex-1 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-semibold transition';

                errorMessage.classList.add('hidden');
            } else {
                saveButton.disabled = true;

                saveButton.className =
                    'flex-1 px-4 py-2 bg-gray-300 text-gray-500 rounded-lg text-sm font-semibold cursor-not-allowed';

                errorMessage.classList.remove('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const staffSelect = document.getElementById('staff-sel-input');
            const form = document.getElementById('form-assign-staff');

            if (staffSelect) {
                staffSelect.addEventListener('change', function () {
                    updateStaffSaveButton();
                });
            }

            if (form) {
                form.addEventListener('submit', function (e) {
                    if (!staffSelect.value) {
                        e.preventDefault();

                        document.getElementById('staff-error').classList.remove('hidden');

                        staffSelect.focus();
                        return;
                    }
                });
            }
        });

        function openVerifyModal(taskId, title, link) {
            document.getElementById('verify-task-title').textContent = title;
            const linkDiv = document.getElementById('verify-link-container');
            if (link) {
                linkDiv.innerHTML = `<a href="${link}" target="_blank" class="text-primary-600 hover:underline flex items-center gap-1 font-medium"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg> Cek Bukti Link Konten</a>`;
            } else {
                linkDiv.innerHTML = `<span class="text-gray-400 italic">Belum ada link</span>`;
            }
            document.getElementById('form-verify').action = `/pm/sosmed/tasks/${taskId}/verify`;
            document.getElementById('rej-field').classList.add('hidden');
            document.getElementById('btn-submit-verify').classList.add('hidden');
            document.getElementById('modal-verify').classList.remove('hidden');
        }

        function setPmAction(action) {
            document.getElementById('verify-action').value = action;
            const rej = document.getElementById('rej-field');
            const btn = document.getElementById('btn-submit-verify');
            btn.classList.remove('hidden');
            if (action === 'reject') {
                rej.classList.remove('hidden');
                btn.textContent = 'Konfirmasi Tolak Tugas';
                btn.className = 'w-full px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold rounded-lg';
            } else {
                rej.classList.add('hidden');
                btn.textContent = 'Konfirmasi Loloskan ke HR Staff';
                btn.className = 'w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const accSel = document.getElementById('task-acc-sel');
            const stSel = document.getElementById('task-staff-sel');
            if (accSel && stSel) {
                accSel.addEventListener('change', function () {
                    const opt = this.options[this.selectedIndex];
                    const staffId = opt.dataset.staff;
                    if (staffId && staffId !== 'null') {
                        stSel.value = staffId;
                    }
                });
            }
        });
    </script>
@endpush