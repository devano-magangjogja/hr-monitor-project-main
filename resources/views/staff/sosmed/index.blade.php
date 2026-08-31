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
{{-- STAT CARDS                                                      --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
        <p class="text-xs font-medium text-gray-500 mb-1">Total Akun</p>
        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_accounts'] }}</p>
        <p class="text-[11px] text-gray-400 mt-0.5">dibuat oleh Admin</p>
    </div>
    <div class="bg-white rounded-xl border {{ $stats['unassigned_pm'] > 0 ? 'border-amber-300 bg-amber-50/20' : 'border-gray-200' }} p-4 shadow-sm">
        <p class="text-xs font-medium text-gray-500 mb-1">Belum Terbagi ke PM</p>
        <p class="text-2xl font-bold text-amber-600">{{ $stats['unassigned_pm'] }}</p>
        <p class="text-[11px] text-amber-600 mt-0.5">siap didistribusikan</p>
    </div>
    <div class="bg-white rounded-xl border {{ $stats['need_hr_verify'] > 0 ? 'border-purple-300 bg-purple-50/30 ring-2 ring-purple-400/30' : 'border-gray-200' }} p-4 shadow-sm relative">
        @if($stats['need_hr_verify'] > 0)
            <span class="absolute top-3 right-3 flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-purple-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-purple-500"></span>
            </span>
        @endif
        <p class="text-xs font-medium text-gray-500 mb-1">Perlu Final Approval HR</p>
        <p class="text-2xl font-bold text-purple-600">{{ $stats['need_hr_verify'] }}</p>
        <p class="text-[11px] text-purple-600 mt-0.5">sudah lolos PM</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
        <p class="text-xs font-medium text-gray-500 mb-1">Total Tugas</p>
        <p class="text-2xl font-bold text-indigo-600">{{ $stats['total_tasks'] }}</p>
        <p class="text-[11px] text-gray-400 mt-0.5">seluruh divisi</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
        <p class="text-xs font-medium text-gray-500 mb-1">Selesai Approved</p>
        <p class="text-2xl font-bold text-emerald-600">{{ $stats['completed'] }}</p>
        <p class="text-[11px] text-emerald-600 mt-0.5">final selesai</p>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- TABS & CONTENT                                                  --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="flex border-b border-gray-200 overflow-x-auto">
        <a href="{{ route('staff.sosmed.index', ['tab' => 'accounts']) }}"
           class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 transition
                  {{ $tab === 'accounts' ? 'border-primary-600 text-primary-600 bg-primary-50/50' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            Distribusi Akun ke PM / Sosmed ({{ $accounts->count() }})
        </a>
        <a href="{{ route('staff.sosmed.index', ['tab' => 'approvals']) }}"
           class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 transition
                  {{ $tab === 'approvals' ? 'border-primary-600 text-primary-600 bg-primary-50/50' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
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
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            Laporan Tugas & Monitoring
        </a>
    </div>

    {{-- ── TAB 1: DISTRIBUSI AKUN ─────────────────────────────────── --}}
    @if($tab === 'accounts')
    <div class="p-5">
        <div class="mb-4">
            <h3 class="text-sm font-semibold text-gray-800">Daftar Akun & Delegasi Tanggung Jawab</h3>
            <p class="text-xs text-gray-500 mt-0.5">HR Staff membagikan akun sosial media kepada PM agar PM hanya dapat mengelola akun yang menjadi tanggung jawabnya.</p>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-100">
            <table class="w-full text-sm min-w-[700px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Nama Akun</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 w-32">Platform</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 w-44">Link Akun</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 w-48">Penanggung Jawab PM</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 w-48">Penanggung Jawab Sosmed</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600 w-28">Delegasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($accounts as $acc)
                    <tr class="hover:bg-gray-50/80 transition">
                        <td class="px-4 py-3.5">
                            <span class="font-semibold text-gray-800">{{ $acc->name }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-xs font-medium border {{ $acc->platform_color }}">
                                {{ $acc->platform_icon }} {{ $acc->platform }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            @if($acc->link)
                                <a href="{{ $acc->link }}" target="_blank" class="text-xs text-primary-600 hover:underline truncate max-w-[140px] block">
                                    {{ parse_url($acc->link, PHP_URL_HOST) ?? $acc->link }}
                                </a>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                        {{-- Nama User PM --}}
                        <td class="px-4 py-3.5">
                            @if($acc->pmUser)
                                <div class="flex items-center gap-1.5">
                                    <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-[10px]">
                                        {{ strtoupper(substr($acc->pmUser->name, 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-gray-800 text-xs">{{ $acc->pmUser->name }}</span>
                                </div>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                    Belum Di-assign
                                </span>
                            @endif
                        </td>
                        {{-- Nama User Sosmed --}}
                        <td class="px-4 py-3.5">
                            @if($acc->staffUser)
                                <div class="flex items-center gap-1.5">
                                    <div class="w-6 h-6 rounded-full bg-pink-100 text-pink-700 font-bold flex items-center justify-center text-[10px]">
                                        {{ strtoupper(substr($acc->staffUser->name, 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-gray-800 text-xs">{{ $acc->staffUser->name }}</span>
                                </div>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    Belum Di-assign
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-right">
                            <button onclick="openAssignModal({{ $acc->id }}, '{{ addslashes($acc->name) }}', {{ $acc->pm_id ?? 'null' }}, {{ $acc->staff_id ?? 'null' }})"
                                    class="px-3 py-1 bg-primary-50 hover:bg-primary-100 text-primary-700 text-xs font-semibold rounded-lg transition">
                                Atur PJ
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400">Belum ada akun sosial media. Admin perlu menambahkan akun terlebih dahulu.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ── TAB 2: APPROVAL LEVEL 2 (HR STAFF) ────────────────────── --}}
    @if($tab === 'approvals')
    <div class="p-5">
        <div class="mb-4">
            <h3 class="text-sm font-semibold text-gray-800">Tugas Siap Approval Final (Level 2)</h3>
            <p class="text-xs text-gray-500 mt-0.5">Tugas-tugas di bawah ini telah diverifikasi oleh PM dan menunggu persetujuan akhir dari HR Staff.</p>
        </div>

        <div class="space-y-3 mb-6">
            @forelse($needHrApproval as $task)
            <div class="flex items-center justify-between gap-4 p-4 bg-purple-50/60 border border-purple-200 rounded-xl hover:border-purple-300 transition">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-semibold text-gray-800 text-sm">{{ $task->title }}</span>
                        <span class="px-2 py-0.5 rounded-md text-xs font-medium border {{ $task->account?->platform_color ?? 'bg-gray-100' }}">
                            {{ $task->account?->platform_icon }} {{ $task->account?->name }} ({{ $task->account?->platform }})
                        </span>
                        <span class="px-2 py-0.5 rounded text-[11px] bg-indigo-100 text-indigo-700 font-medium">
                            Verif PM: {{ $task->verifiedBy?->name ?? 'PM' }}
                        </span>
                    </div>
                    <div class="flex items-center gap-3 mt-2 text-xs text-gray-500">
                        <span>Staff Pelaksana: <strong>{{ $task->assignedUser?->name ?? '-' }}</strong></span>
                        <span>·</span>
                        <span>Tanggal: {{ $task->task_date->translatedFormat('d M Y') }}</span>
                        @if($task->link_upload)
                            <span>·</span>
                            <a href="{{ $task->link_upload }}" target="_blank" class="text-primary-600 font-medium hover:underline inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                Cek Bukti Konten
                            </a>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <button onclick="openVerifyModal({{ $task->id }}, '{{ addslashes($task->title) }}')"
                            class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded-lg transition shadow-sm">
                        Review & Final Approve
                    </button>
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
    <div class="p-5">
        <div class="overflow-x-auto rounded-lg border border-gray-100">
            <table class="w-full text-sm min-w-[760px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Tugas</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 w-36">Akun Sosmed</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 w-36">PJ PM</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 w-40">Staff Pelaksana</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 w-36">Verifikasi PM</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 w-36">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($allTasks as $t)
                    <tr class="hover:bg-gray-50/80 transition">
                        <td class="px-4 py-3.5 font-medium text-gray-800">
                            {{ $t->title }}
                            <p class="text-xs text-gray-400">{{ $t->task_date->translatedFormat('d M Y') }}</p>
                        </td>
                        <td class="px-4 py-3.5 text-xs text-gray-700">
                            {{ $t->account?->name }} ({{ $t->account?->platform }})
                        </td>
                        {{-- PJ PM User Name --}}
                        <td class="px-4 py-3.5 text-xs">
                            @if($t->account?->pmUser)
                                <span class="font-semibold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100">
                                    {{ $t->account->pmUser->name }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        {{-- Staff Pelaksana User Name --}}
                        <td class="px-4 py-3.5 text-xs text-gray-700">
                            @if($t->assignedUser)
                                <span class="font-semibold text-gray-800 bg-gray-50 px-2 py-0.5 rounded border border-gray-200">
                                    {{ $t->assignedUser->name }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        {{-- Verifikasi PM User Name --}}
                        <td class="px-4 py-3.5 text-xs text-gray-700">
                            @if($t->verifiedBy)
                                <span class="text-blue-700 font-semibold">{{ $t->verifiedBy->name }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $t->status_badge_class }}">
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
    </div>
    @endif
</div>

{{-- ── MODAL ASSIGN ACCOUNT (HR STAFF) ─────────────────────────── --}}
<div id="modal-assign" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('modal-assign').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm z-10">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-bold text-gray-800">Delegasi Akun Sosmed</h3>
            <button onclick="document.getElementById('modal-assign').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="form-assign" method="POST" action="" class="p-6 space-y-4">
            @csrf @method('PATCH')
            <div>
                <p class="text-xs text-gray-500 mb-0.5">Nama Akun</p>
                <p id="assign-acc-name" class="text-sm font-semibold text-gray-800"></p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Pilih Penanggung Jawab PM (Nama User)</label>
                <select name="pm_id" id="assign-pm-sel" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    <option value="">-- Tanpa PM --</option>
                    @foreach($pms as $pm)
                        <option value="{{ $pm->id }}">{{ $pm->name }} (PM)</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Pilih Penanggung Jawab Sosmed (Nama User)</label>
                <select name="staff_id" id="assign-staff-sel" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    <option value="">-- Tanpa Staff --</option>
                    @foreach($staffs as $st)
                        <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->role_label }})</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('modal-assign').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700">Batal</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-semibold">Simpan Delegasi</button>
            </div>
        </form>
    </div>
</div>

{{-- ── MODAL VERIFIKASI FINAL (HR STAFF) ───────────────────────── --}}
<div id="modal-verify" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('modal-verify').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm z-10">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-bold text-gray-800">Final Approval HR Staff</h3>
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
                <textarea name="rejection_note" rows="3" placeholder="Catatan revisi..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:outline-none"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="setAction('reject')" class="flex-1 px-4 py-2 border border-red-300 text-red-600 text-sm font-semibold rounded-lg hover:bg-red-50">Tolak</button>
                <button type="button" onclick="setAction('verify')" class="flex-1 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Approve Final</button>
            </div>
            <button type="submit" id="btn-submit-verify" class="hidden w-full px-4 py-2 bg-purple-600 text-white text-sm font-bold rounded-lg"></button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openAssignModal(accId, accName, currentPmId, currentStaffId) {
    document.getElementById('assign-acc-name').textContent = accName;
    document.getElementById('form-assign').action = `/staff/sosmed/accounts/${accId}/assign`;
    document.getElementById('assign-pm-sel').value = currentPmId ?? '';
    document.getElementById('assign-staff-sel').value = currentStaffId ?? '';
    document.getElementById('modal-assign').classList.remove('hidden');
}

function openVerifyModal(taskId, title) {
    document.getElementById('verify-task-title').textContent = title;
    document.getElementById('form-verify').action = `/staff/sosmed/tasks/${taskId}/verify`;
    document.getElementById('rej-field').classList.add('hidden');
    document.getElementById('btn-submit-verify').classList.add('hidden');
    document.getElementById('modal-verify').classList.remove('hidden');
}

function setAction(action) {
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
        btn.textContent = 'Konfirmasi Approve Final';
        btn.className = 'w-full px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-lg';
    }
}
</script>
@endpush
