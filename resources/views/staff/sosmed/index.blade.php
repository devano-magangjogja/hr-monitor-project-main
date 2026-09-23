@extends('layouts.app')
@section('title', 'Manajemen Sosmed & Approval')
@section('page-title', 'Manajemen Sosmed & Approval')
@section('page-subtitle', 'Delegasi akun sosmed ke PM/Staff & final approval (Level 2)')
@section('sidebar')
    @include('components.sidebar-staff')
@endsection

@section('content')
    @php
        $executorsJson = json_encode($executors->map(fn($e) => [
            'id' => $e->id,
            'name' => $e->name,
            'role' => $e->role,
            'role_label' => match($e->role) {
                'pm' => 'PM Mandiri',
                'sosmed' => 'Staff Sosmed',
                'digital_marketing' => 'Digital Marketing',
                default => $e->role_label ?? strtoupper($e->role)
            }
        ]));
    @endphp
    @include('components.notification-popup')

    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const m = document.getElementById('modal-my-submit');
                if (m) m.classList.remove('hidden');
            });
        </script>
    @endif

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
                Distribusi Akun ke PM / Sosmed ({{ $accounts->total() }})
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
                    <div class="mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">Daftar Akun & Delegasi Tanggung Jawab</h3>
                            <p class="text-xs text-gray-500 mt-0.5">HR Staff membagikan akun sosial media kepada PM agar PM hanya
                                dapat
                                mengelola akun yang menjadi tanggung jawabnya.</p>
                        </div>
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 shrink-0">
                            <form action="{{ route('staff.sosmed.index') }}" method="GET" class="flex items-center gap-1.5 flex-wrap sm:flex-nowrap">
                                <input type="hidden" name="tab" value="accounts">
                                <select name="search_type"
                                    class="h-9 px-2.5 text-xs bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 text-gray-700 transition cursor-pointer"
                                    onchange="if(this.form.account_search.value) this.form.submit()">
                                    <option value="all" {{ ($searchType ?? 'all') === 'all' ? 'selected' : '' }}>Semua Kriteria</option>
                                    <option value="manager" {{ ($searchType ?? 'all') === 'manager' ? 'selected' : '' }}>Pengelola Akun</option>
                                    <option value="pm" {{ ($searchType ?? 'all') === 'pm' ? 'selected' : '' }}>Supervisor PM</option>
                                    <option value="assistant" {{ ($searchType ?? 'all') === 'assistant' ? 'selected' : '' }}>Asisten Pengawas</option>
                                    <option value="staff" {{ ($searchType ?? 'all') === 'staff' ? 'selected' : '' }}>Staff Pengawas</option>
                                    <option value="account" {{ ($searchType ?? 'all') === 'account' ? 'selected' : '' }}>Nama Akun</option>
                                </select>
                                <div class="relative flex items-center flex-1 sm:flex-initial">
                                    <input type="text" name="account_search" value="{{ $accountSearch ?? '' }}"
                                        placeholder="Cari akun, pengelola, PM, asisten, staff..."
                                        class="h-9 pl-3 pr-8 text-xs bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 text-gray-700 transition w-full sm:w-52 lg:w-60">
                                    <button type="submit" class="absolute right-2 text-gray-400 hover:text-primary-600 transition" title="Cari">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </button>
                                </div>
                                @if($accountSearch || ($searchType ?? 'all') !== 'all')
                                    <a href="{{ route('staff.sosmed.index', ['tab' => 'accounts']) }}"
                                        class="inline-flex items-center justify-center h-9 px-2.5 text-xs font-medium text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition shrink-0"
                                        title="Reset pencarian & filter">
                                        Reset
                                    </a>
                                @endif
                            </form>
                            <button onclick="openAssignTaskModal()"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs sm:text-sm font-semibold rounded-lg transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Beri Tugas
                            </button>
                        </div>
                    </div>

                    {{-- Desktop table (md+) --}}
                    <div class="hidden md:block overflow-x-auto rounded-lg border border-gray-100">
                        <table class="w-full table-fixed text-sm">
                            <colgroup>
                                <col class="w-[24%]"> {{-- Nama Akun & Catatan --}}
                                <col class="w-28"> {{-- Platform --}}
                                <col class="w-32"> {{-- Link URL --}}
                                <col class="w-36"> {{-- Eksekutor --}}
                                <col class="w-36"> {{-- Supervisor PM --}}
                                <col class="w-36"> {{-- Asisten Pengawas --}}
                                <col class="w-36"> {{-- Staff Pengawas --}}
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
                                    <th class="px-4 py-3 text-left">Staff Pengawas</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($accounts as $acc)
                                    @php
                                        $managers = $acc->staffUsers;
                                        $hasManagers = $managers->count() > 0;
                                        if ($accountSearch && $searchType === 'manager') {
                                            $filteredManagers = $managers->filter(
                                                fn($u) => str_contains(strtolower($u->name), strtolower(trim($accountSearch)))
                                            );
                                            $rows = $filteredManagers->count() > 0 ? $filteredManagers : collect([null]);
                                        } elseif ($accountSearch && $searchType === 'all') {
                                            $accNameMatch = str_contains(strtolower($acc->name), strtolower(trim($accountSearch)))
                                                || str_contains(strtolower($acc->username ?? ''), strtolower(trim($accountSearch)));
                                            if (!$accNameMatch) {
                                                $filteredManagers = $managers->filter(
                                                    fn($u) => str_contains(strtolower($u->name), strtolower(trim($accountSearch)))
                                                );
                                                $rows = $filteredManagers->count() > 0 ? $filteredManagers : ($hasManagers ? $managers : collect([null]));
                                            } else {
                                                $rows = $hasManagers ? $managers : collect([null]);
                                            }
                                        } else {
                                            $rows = $hasManagers ? $managers : collect([null]);
                                        }
                                        $adminLocked = $acc->staffUsers->contains(fn($u) => $u->role === 'hr_staff');
                                    @endphp

                                    @foreach($rows as $stUser)
                                        @php
                                            $stRoleTag = $stUser ? match ($stUser->role) {
                                                'pm' => 'PM Mandiri',
                                                'sosmed' => 'Staff Sosmed',
                                                'digital_marketing' => 'Digital Marketing',
                                                'hr_assistant' => 'HR Assistant',
                                                'hr_staff' => 'HR Staff',
                                                default => $stUser->role_label ?? strtoupper($stUser->role)
                                            } : null;

                                            $isDirectHr = $stUser && in_array($stUser->role, ['pm', 'hr_assistant', 'hr_staff']);
                                        @endphp

                                        <tr class="hover:bg-gray-50/80 transition align-middle">
                                            {{-- Nama Akun --}}
                                            <td class="px-4 py-3.5 min-w-[180px]">
                                                <span class="font-semibold text-gray-800 block break-words" title="{{ $acc->name }}">
                                                    {{ $acc->name }}
                                                </span>
                                            </td>

                                            {{-- Platform --}}
                                            <td class="px-4 py-3.5">
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-medium border whitespace-nowrap {{ $acc->platform_color }}">
                                                    {{ $acc->platform_icon }} {{ $acc->platform }}
                                                </span>
                                            </td>

                                            {{-- URL --}}
                                            <td class="px-4 py-3.5">
                                                @if($acc->link)
                                                    <a href="{{ $acc->link }}" target="_blank"
                                                    class="inline-flex items-center gap-1 text-xs text-primary-600 hover:underline">
                                                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                        </svg>
                                                        Buka
                                                    </a>
                                                @else
                                                    <span class="text-xs text-gray-300">—</span>
                                                @endif
                                            </td>

                                            {{-- Dikelola (1 orang per baris) --}}
                                            <td class="px-4 py-3 text-xs min-w-0">
                                                @if($stUser)
                                                    <div class="min-w-0">
                                                        <span class="font-semibold text-gray-800 block truncate" title="{{ $stUser->name }}">
                                                            {{ $stUser->name }}
                                                        </span>
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold mt-0.5
                                                            {{ $stUser->role === 'pm' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-pink-50 text-pink-700 border border-pink-200' }}">
                                                            {{ $stRoleTag }}
                                                        </span>
                                                    </div>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border bg-gray-100 text-gray-600 whitespace-nowrap">
                                                        Belum Ditugaskan
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- PM (berdasarkan role di baris ini) --}}
                                            <td class="px-4 py-3 text-xs min-w-0">
                                                @if($isDirectHr)
                                                    <span class="text-gray-400 text-[11px] block">Langsung ke HR</span>
                                                @elseif($acc->pmUser)
                                                    <div class="min-w-0">
                                                        <span class="font-semibold text-gray-800 block truncate" title="{{ $acc->pmUser->name }}">
                                                            {{ $acc->pmUser->name }}
                                                        </span>
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold mt-0.5 bg-purple-50 text-purple-700 border border-purple-200">
                                                            Supervisor PM
                                                        </span>
                                                    </div>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 whitespace-nowrap">
                                                        Belum Ada PM
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- Asisten Pengawas --}}
                                            <td class="px-4 py-3 text-xs min-w-0">
                                                @if($isDirectHr)
                                                    <span class="text-gray-400 text-[11px] block">Langsung ke HR</span>
                                                @elseif($acc->assistantUser)
                                                    <div class="min-w-0">
                                                        <span class="font-semibold text-gray-800 block truncate" title="{{ $acc->assistantUser->name }}">
                                                            {{ $acc->assistantUser->name }}
                                                        </span>
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold mt-0.5 bg-teal-50 text-teal-700 border border-teal-200">
                                                            Asisten HR
                                                        </span>
                                                    </div>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 whitespace-nowrap">
                                                        Tanpa Asisten
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- Staff Pengawas --}}
                                            <td class="px-4 py-3 text-xs min-w-0">
                                                @if($acc->supervisorStaff)
                                                    <div class="min-w-0">
                                                        <span class="font-semibold text-gray-800 block truncate" title="{{ $acc->supervisorStaff->name }}">
                                                            {{ $acc->supervisorStaff->name }}
                                                        </span>
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold mt-0.5 bg-blue-50 text-blue-700 border border-blue-200">
                                                            Staff Pengawas
                                                        </span>
                                                    </div>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500 whitespace-nowrap">
                                                        Tanpa Pengawas
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- Aksi --}}
                                            <td class="px-4 py-3.5 text-center">
                                                <div class="flex items-center justify-center gap-1">
                                                    {{-- Edit --}}
                                                    @if($adminLocked)
                                                        <button type="button" disabled
                                                            class="p-1.5 text-gray-300 bg-gray-50 rounded-lg cursor-not-allowed"
                                                            title="Pengelolaan ditetapkan oleh Admin, tidak dapat diubah">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                            </svg>
                                                        </button>
                                                    @else
                                                        <button type="button"
                                                            onclick="openAssignModal({
                                                                id: {{ $acc->id }},
                                                                name: '{{ addslashes($acc->name) }}',
                                                                platform: '{{ addslashes($acc->platform) }}',
                                                                link: '{{ addslashes($acc->link ?? '') }}',
                                                                pm_id: {{ $acc->pm_id ?? 'null' }},
                                                                assistant_id: {{ $acc->assistant_id ?? 'null' }},
                                                                supervisor_staff_id: {{ $acc->supervisor_staff_id ?? 'null' }},
                                                                current_staff_id: {{ $stUser ? $stUser->id : 'null' }},
                                                                notes: '{{ addslashes(str_replace(["\r", "\n"], [' ', ' '], $acc->notes ?? '')) }}',
                                                                staff_users: {{ json_encode($acc->staffUsers->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'role' => $u->role, 'role_label' => match($u->role) { 'pm' => 'PM Mandiri', 'sosmed' => 'Staff Sosmed', 'digital_marketing' => 'Digital Marketing', default => $u->role_label ?? strtoupper($u->role) }])) }},
                                                                managers_count: {{ $acc->staffUsers->count() }},
                                                                assigned_user_ids: {{ json_encode($acc->staffUsers->pluck('id')) }}
                                                            })"
                                                            class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                                            title="Atur Penugasan">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                            </svg>
                                                        </button>
                                                    @endif

                                                    {{-- Sampah = lepas HANYA pengelola di baris ini (kecuali hr_staff) --}}
                                                    @if($stUser && $stUser->role !== 'hr_staff')
                                                        <form method="POST" action="{{ route('staff.sosmed.accounts.unassign', $acc) }}"
                                                            onsubmit="return confirm('Lepas akses {{ addslashes($stUser->name) }} dari akun {{ addslashes($acc->name) }}?')"
                                                            class="inline">
                                                            @csrf
                                                            <input type="hidden" name="user_id" value="{{ $stUser->id }}">
                                                            <button type="submit"
                                                                    class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                                                    title="Lepas {{ $stUser->name }} dari akun ini">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-400">
                                            @if($accountSearch)
                                                Tidak ditemukan akun yang cocok dengan pencarian "<strong>{{ $accountSearch }}</strong>".
                                                <div class="mt-2">
                                                    <a href="{{ route('staff.sosmed.index', ['tab' => 'accounts']) }}" class="text-xs text-primary-600 hover:underline">Reset pencarian</a>
                                                </div>
                                            @else
                                                Belum ada akun sosial media. Admin perlu menambahkan akun terlebih dahulu.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- Pagination Links --}}
                        @if($accounts->hasPages())
                            <div class="mt-4 px-4 py-3 border-t border-gray-200">
                                {{ $accounts->links() }}
                            </div>
                        @endif
                    </div>

                    {{-- Mobile cards (< md) --}}
                <div class="md:hidden space-y-3">
                    @forelse($accounts as $acc)
                        @php
                            $managers = $acc->staffUsers;
                            $hasManagers = $managers->count() > 0;
                            if ($accountSearch && $searchType === 'manager') {
                                $filteredManagers = $managers->filter(
                                    fn($u) => str_contains(strtolower($u->name), strtolower(trim($accountSearch)))
                                );
                                $rows = $filteredManagers->count() > 0 ? $filteredManagers : collect([null]);
                            } elseif ($accountSearch && $searchType === 'all') {
                                $accNameMatch = str_contains(strtolower($acc->name), strtolower(trim($accountSearch)))
                                    || str_contains(strtolower($acc->username ?? ''), strtolower(trim($accountSearch)));
                                if (!$accNameMatch) {
                                    $filteredManagers = $managers->filter(
                                        fn($u) => str_contains(strtolower($u->name), strtolower(trim($accountSearch)))
                                    );
                                    $rows = $filteredManagers->count() > 0 ? $filteredManagers : ($hasManagers ? $managers : collect([null]));
                                } else {
                                    $rows = $hasManagers ? $managers : collect([null]);
                                }
                            } else {
                                $rows = $hasManagers ? $managers : collect([null]);
                            }
                            $adminLocked = $acc->staffUsers->contains(fn($u) => $u->role === 'hr_staff');
                        @endphp

                        @foreach($rows as $stUser)
                            @php
                                $stRoleTag = $stUser ? match ($stUser->role) {
                                    'pm' => 'PM Mandiri',
                                    'sosmed' => 'Staff Sosmed',
                                    'digital_marketing' => 'Digital Marketing',
                                    'hr_assistant' => 'HR Assistant',
                                    'hr_staff' => 'HR Staff',
                                    default => $stUser->role_label ?? strtoupper($stUser->role)
                                } : null;

                                $isDirectHr = $stUser && in_array($stUser->role, ['pm', 'hr_assistant', 'hr_staff']);
                            @endphp

                            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                                <div class="flex items-start justify-between gap-3 mb-3">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-800 truncate">{{ $acc->name }}</p>
                                        <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-md text-xs font-medium border {{ $acc->platform_color }}">
                                            {{ $acc->platform_icon }} {{ $acc->platform }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-1 shrink-0">
                                        {{-- Edit --}}
                                        @if($adminLocked)
                                            <button type="button" disabled
                                                class="p-1.5 text-gray-300 bg-gray-50 rounded-lg cursor-not-allowed"
                                                title="Pengelolaan ditetapkan oleh Admin, tidak dapat diubah">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                </svg>
                                            </button>
                                        @else
                                            <button type="button"
                                                onclick="openAssignModal({
                                                    id: {{ $acc->id }},
                                                    name: '{{ addslashes($acc->name) }}',
                                                    platform: '{{ addslashes($acc->platform) }}',
                                                    link: '{{ addslashes($acc->link ?? '') }}',
                                                    pm_id: {{ $acc->pm_id ?? 'null' }},
                                                    assistant_id: {{ $acc->assistant_id ?? 'null' }},
                                                    supervisor_staff_id: {{ $acc->supervisor_staff_id ?? 'null' }},
                                                    current_staff_id: {{ $stUser ? $stUser->id : 'null' }},
                                                    notes: '{{ addslashes(str_replace(["\r", "\n"], [' ', ' '], $acc->notes ?? '')) }}',
                                                    staff_users: {{ json_encode($acc->staffUsers->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'role' => $u->role, 'role_label' => match($u->role) { 'pm' => 'PM Mandiri', 'sosmed' => 'Staff Sosmed', 'digital_marketing' => 'Digital Marketing', default => $u->role_label ?? strtoupper($u->role) }])) }},
                                                    managers_count: {{ $acc->staffUsers->count() }},
                                                    assigned_user_ids: {{ json_encode($acc->staffUsers->pluck('id')) }}
                                                })"
                                                class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                                title="Atur Penugasan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                        @endif

                                        {{-- Sampah = lepas HANYA pengelola di card ini --}}
                                        @if($stUser && $stUser->role !== 'hr_staff')
                                            <form method="POST" action="{{ route('staff.sosmed.accounts.unassign', $acc) }}"
                                                onsubmit="return confirm('Lepas akses {{ addslashes($stUser->name) }} dari akun {{ addslashes($acc->name) }}?')"
                                                class="inline">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $stUser->id }}">
                                                <button type="submit"
                                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                                        title="Lepas {{ $stUser->name }} dari akun ini">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                <div class="space-y-2 text-xs border-t border-gray-100 pt-2.5">
                                    {{-- Link --}}
                                    <div class="min-w-0">
                                        <p class="text-gray-400 mb-0.5">Link Akun</p>
                                        @if($acc->link)
                                            <a href="{{ $acc->link }}" target="_blank" rel="noopener noreferrer"
                                            class="inline-flex items-center gap-1 text-xs text-primary-600 hover:text-primary-800 hover:underline truncate">
                                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                </svg>
                                                <span class="truncate">Buka Link</span>
                                            </a>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </div>

                                    {{-- Eksekutor & PM --}}
                                    <div class="flex items-start justify-between gap-2 pt-1">
                                        <div class="min-w-0">
                                            <p class="text-gray-400 mb-0.5">Eksekutor</p>
                                            @if($stUser)
                                                <span class="font-medium text-gray-800 truncate block text-xs">
                                                    {{ $stUser->name }}
                                                    <span class="text-[10px] text-gray-500 font-normal">({{ $stRoleTag }})</span>
                                                </span>
                                            @else
                                                <span class="text-amber-600 font-medium">Belum Ditugaskan</span>
                                            @endif
                                        </div>
                                        <div class="min-w-0 text-right">
                                            <p class="text-gray-400 mb-0.5">Supervisor PM</p>
                                            <span class="font-medium text-gray-800 truncate block text-xs">
                                                @if($isDirectHr)
                                                    Langsung ke HR
                                                @else
                                                    {{ $acc->pmUser?->name ?? 'Belum Ada PM' }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Asisten --}}
                                    <div class="pt-1 border-t border-gray-50 flex items-center justify-between text-xs">
                                        <span class="text-gray-400">Asisten Pengawas:</span>
                                        <span class="font-medium text-gray-800 truncate text-xs">
                                            @if($isDirectHr)
                                                Langsung ke HR
                                            @else
                                                {{ $acc->assistantUser?->name ?? 'Tanpa Asisten' }}
                                            @endif
                                        </span>
                                    </div>

                                    {{-- Staff Pengawas --}}
                                    <div class="pt-1 border-t border-gray-50 flex items-center justify-between text-xs">
                                        <span class="text-gray-400">Staff Pengawas:</span>
                                        <span class="font-medium text-gray-800 truncate text-xs">
                                            {{ $acc->supervisorStaff?->name ?? 'Tanpa Pengawas' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @empty
                        <div class="py-8 text-center text-sm text-gray-400">
                            @if($accountSearch)
                                Tidak ditemukan akun yang cocok dengan pencarian "<strong>{{ $accountSearch }}</strong>".
                                <div class="mt-2">
                                    <a href="{{ route('staff.sosmed.index', ['tab' => 'accounts']) }}" class="text-xs text-primary-600 hover:underline">Reset pencarian</a>
                                </div>
                            @else
                                Belum ada akun sosial media.
                            @endif
                        </div>
                    @endforelse

                    <div class="mt-4">
                        {{ $accounts->links() }}
                    </div>
                </div>
            </div>
        @endif

    {{-- ── TAB 2: APPROVAL LEVEL 2 (HR STAFF) ────────────────────── --}}
    @if($tab === 'approvals')
        <div class="p-4 sm:p-5">
            <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Tugas Siap Approval Final (Level 2) & Pengawasan</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Tugas-tugas di bawah ini telah diverifikasi oleh PM atau memerlukan
                        verifikasi langsung oleh Anda sebagai Staff Pengawas.</p>
                </div>
                <form method="GET" action="{{ route('staff.sosmed.index') }}" class="flex items-center gap-2 w-full sm:w-auto">
                    <input type="hidden" name="tab" value="approvals">
                    <input type="text" name="verify_search" value="{{ request()->query('verify_search', '') }}"
                        placeholder="Cari judul tugas, akun, atau petugas..."
                        class="w-full sm:w-64 h-9 px-3 text-xs bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 text-gray-700 transition">
                    <button type="submit"
                        class="h-9 px-3 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-lg transition shrink-0">Cari</button>
                    @if(request()->query('verify_search'))
                        <a href="{{ route('staff.sosmed.index', ['tab' => 'approvals']) }}"
                            class="text-xs text-primary-600 hover:underline whitespace-nowrap">Reset</a>
                    @endif
                </form>
            </div>

            <div class="space-y-3 mb-6">
                @forelse($needHrApproval as $task)
                    <div
                        class="p-4 {{ $task->status === 'done_by_staff' ? 'bg-blue-50/60 border-blue-200 hover:border-blue-300' : 'bg-purple-50/60 border-purple-200 hover:border-purple-300' }} border rounded-xl transition">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-semibold text-gray-800 text-sm">{{ $task->title }}</span>
                                    <span
                                        class="px-2 py-0.5 rounded-md text-xs font-medium border {{ $task->account?->platform_color ?? 'bg-gray-100' }}">
                                        {{ $task->account?->platform_icon }} {{ $task->account?->name }}
                                        ({{ $task->account?->platform }})
                                    </span>
                                    @if($task->status === 'done_by_staff')
                                        <span
                                            class="px-2 py-0.5 rounded text-[11px] bg-blue-100 text-blue-700 font-medium inline-flex items-center gap-1">
                                            <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                            Verifikasi Langsung (Staff Pengawas)
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[11px] bg-indigo-100 text-indigo-700 font-medium">
                                            Verif PM: {{ $task->verifiedBy?->name ?? 'PM' }}
                                        </span>
                                    @endif
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
                                    class="inline-flex items-center gap-1.5 px-4 py-2 {{ $task->status === 'done_by_staff' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-purple-600 hover:bg-purple-700' }} text-white text-xs font-semibold rounded-xl transition shadow-sm whitespace-nowrap">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $task->status === 'done_by_staff' ? 'Verifikasi & Approve' : 'Final Approve' }}
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
                        <p class="text-sm font-semibold text-gray-700">Semua Tugas Sudah Selesai Diverifikasi</p>
                        <p class="text-xs text-gray-400 mt-1 max-w-xs">
                            Tidak ada tugas tim sosmed yang menunggu approval final HR Staff saat ini.
                        </p>
                    </div>
                @endforelse

                {{-- Pagination Links --}}
                @if($needHrApproval->count() > 0)
                    <div class="mt-6 flex justify-center">
                        {{ $needHrApproval->links() }}
                    </div>
                @endif
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

                {{-- Pagination Links --}}
                @if($allTasks->hasPages())
                    <div class="mt-4 px-4 py-3 border-t border-gray-200">
                        {{ $allTasks->links() }}
                    </div>
                @endif
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
                <div
                    class="text-xs bg-indigo-50 border border-indigo-200 text-indigo-700 px-3 py-1.5 rounded-lg font-medium shrink-0">
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
                                $isRejected = $myStatus === 'rejected';
                            @endphp
                            <tr class="hover:bg-gray-50/80 transition align-middle">
                                <td class="px-4 py-3.5">
                                    <p class="font-semibold text-gray-800 truncate">{{ $acc->name }}</p>
                                    @if($acc->link)
                                        <a href="{{ $acc->link }}" target="_blank"
                                            class="inline-flex items-center gap-1 text-xs text-primary-600 hover:underline mt-0.5">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                            Buka Profil
                                        </a>
                                    @endif
                                    @if($isRejected && $myTask?->rejection_note)
                                        <p
                                            class="text-xs text-rose-600 mt-1 bg-rose-50 px-2 py-1 rounded border border-rose-200 leading-snug">
                                            ↩ "{{ $myTask->rejection_note }}"
                                        </p>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-medium border {{ $acc->platform_color }}">
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
                                                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                            </svg>
                                            {{ $myTask->link_count }} link bukti
                                        </button>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    @if($myTask)
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $myTask->status_badge_class }}">
                                            {{ $myTask->status_label }}
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Belum
                                            Dikerjakan</span>
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
                                        <span class="text-xs text-gray-400 italic">-</span>
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
                        $isRejected = $myStatus === 'rejected';
                    @endphp
                    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-gray-800 text-sm truncate">{{ $acc->name }}</p>
                                <span
                                    class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-md text-[10px] font-medium border whitespace-nowrap {{ $acc->platform_color }}">
                                    {{ $acc->platform_icon }} {{ $acc->platform }}
                                </span>
                                @if($isRejected && $myTask?->rejection_note)
                                    <p
                                        class="text-xs text-rose-600 mt-1 bg-rose-50 px-2 py-1 rounded border border-rose-200 leading-snug">
                                        ↩ "{{ $myTask->rejection_note }}"
                                    </p>
                                @endif
                            </div>
                            <div class="flex-shrink-0">
                                @if($isSubmitted)
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $myTask->status_badge_class }}">
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
                                            d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                    </svg>
                                    {{ $myTask->link_count }} link bukti
                                </button>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="py-8 text-center text-sm text-gray-400">Admin belum menetapkan akun sosmed kepada Anda.</div>
                @endforelse

                {{-- Pagination Links --}}
                @if($accounts->hasPages())
                    <div class="mt-4">
                        {{ $accounts->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif
    </div>

    {{-- ── MODAL BERI TUGAS PENGELOLAAN SOSMED (HR STAFF) ─────────── --}}
    <div id="modal-assign-task" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            onclick="document.getElementById('modal-assign-task').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg z-10 max-h-[90vh] flex flex-col overflow-visible"
            x-data="assignTaskDropdown({{ json_encode($availableAccounts) }}, {{ $executorsJson }})"
            @reset-assign-task.window="resetState()">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <div>
                    <h3 class="text-base font-bold text-gray-800">Beri Tugas Pengelolaan Sosmed</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Tugaskan akun kepada eksekutor (dapat dikelola lebih dari satu user)</p>
                </div>
                <button type="button" onclick="document.getElementById('modal-assign-task').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('staff.sosmed.assign') }}" class="p-6 pt-1 space-y-4 overflow-y-auto">
                @csrf

                {{-- Dropdown Searchable: Pilih Akun yang Tersedia --}}
                <div class="relative">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Pilih Akun yang Dikelola <span class="text-red-500">*</span>
                    </label>
                    <input type="hidden" name="sosmed_account_id" :value="selectedId" required>

                    {{-- Dropdown Trigger --}}
                    <button type="button" @click="open = !open"
                        class="w-full flex items-center justify-between gap-2 border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white hover:bg-gray-50 focus:ring-2 focus:ring-primary-500 focus:outline-none transition min-w-0">
                        <div x-show="selectedId" class="flex items-center gap-2 truncate min-w-0 text-left">
                            <span class="font-medium text-gray-800 truncate" :title="selectedLabel" x-text="selectedLabel"></span>
                            <template x-if="selectedManagersCount > 0">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700 border border-indigo-200 shrink-0"
                                    :title="`${selectedManagersCount} user sedang mengelola`"
                                    x-text="selectedManagersCount"></span>
                            </template>
                        </div>
                        <span x-show="!selectedId" class="text-gray-400">-- Pilih Akun Tersedia --</span>
                        <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-150"
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
                                    class="w-full text-left px-3 py-2 text-xs hover:bg-primary-50 hover:text-primary-700 transition flex items-center justify-between gap-2 min-w-0"
                                    :class="selectedId == acc.id ? 'bg-primary-50/70 font-semibold text-primary-700' : 'text-gray-700'"
                                    :title="`${acc.name} (${acc.platform})`">
                                    <div class="flex items-center gap-1.5 min-w-0">
                                        <span class="truncate min-w-0" x-text="`${acc.name} (${acc.platform})`"></span>
                                        <template x-if="acc.managers_count > 0">
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 shrink-0"
                                                :title="`${acc.managers_count} user sedang mengelola`"
                                                x-text="acc.managers_count"></span>
                                        </template>
                                    </div>
                                    <span x-show="selectedId == acc.id" class="text-primary-600 shrink-0">
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
                                    sistem.</span>
                                <span x-show="accounts.length > 0">Tidak ada akun yang cocok dengan pencarian.</span>
                            </div>
                        </div>
                    </div>
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
                        Eksekutor Akun (Dikelola Oleh) <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>
                    <select name="staff_id" id="assign-task-staff"
                        x-model="selectedStaffId"
                        :disabled="!selectedId"
                        @change="syncSupervisorState($el, 'assign-task-pm', 'assign-task-pm-hint', 'assign-task-ast')"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none
                            disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed">
                        <option value="" data-role="">-- Belum Ditugaskan / Pilih Nanti --</option>
                        <template x-for="ex in availableExecutors" :key="ex.id">
                            <option :value="ex.id" :data-role="ex.role" x-text="`${ex.name} (${ex.role_label})`"></option>
                        </template>
                    </select>
                    <p x-show="!selectedId" class="text-[11px] text-gray-400 mt-1 italic">
                        Pilih akun terlebih dahulu untuk mengaktifkan delegasi.
                    </p>
                    <p x-show="selectedAccount && availableExecutors.length === 0" class="text-[11px] text-amber-600 mt-1 italic">
                        Semua user yang berwenang sudah mengelola akun ini.
                    </p>
                </div>

                {{-- Supervisor PM --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Supervisor PM <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>
                    <select name="pm_id" id="assign-task-pm"
                        :disabled="!selectedId"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none
                            disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed">
                        <option value="">-- Tanpa Supervisor / Langsung ke HR --</option>
                        @foreach($pms as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }} (PM)</option>
                        @endforeach
                    </select>
                    <p id="assign-task-pm-hint" class="text-[11px] text-gray-400 mt-1">
                        PM yang berwenang meninjau & approve tugas.
                    </p>
                </div>

                {{-- Asisten Pengawas --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Asisten Pengawas <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>
                    <select name="assistant_id" id="assign-task-ast"
                        :disabled="!selectedId"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none
                            disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed">
                        <option value="">-- Tanpa Asisten --</option>
                        @foreach($assistants as $ast)
                            <option value="{{ $ast->id }}">{{ $ast->name }} (Asisten)</option>
                        @endforeach
                    </select>
                </div>

                {{-- Staff Pengawas --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Staff Pengawas <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>
                    <select name="supervisor_staff_id" id="assign-task-supervisor"
                        :disabled="!selectedId"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none
                            disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed">
                        <option value="">-- Tanpa Pengawas / PM Standard --</option>
                        @foreach($supervisors as $sup)
                            <option value="{{ $sup->id }}">{{ $sup->name }} (Staff)</option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-gray-400 mt-1">Staff HR yang berwenang langsung memverifikasi tugas sosmed akun ini.</p>
                </div>

                {{-- Catatan / Arahan --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Catatan / Arahan Penugasan</label>
                    <textarea name="notes" rows="2" placeholder="Catatan atau instruksi pengelolaan akun..."
                        :disabled="!selectedId"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none
                            disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed"></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-assign-task').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">Batal</button>
                    <button type="submit"
                        :disabled="!selectedId"
                        class="flex-1 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-semibold shadow-sm transition
                            disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-primary-600">Tambahkan
                        ke Sosmed</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── MODAL DELEGASI AKUN (HR STAFF) ─────────────────────────── --}}
    <div id="modal-assign" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
        x-data="editAccountDropdown({{ json_encode($availableAccounts) }}, {{ $executorsJson }})"
        @open-edit-account.window="initAccount($event.detail)">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeAssignModal()"></div>
        <div
            class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg z-10 max-h-[90vh] flex flex-col overflow-visible">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <div>
                    <h3 class="text-base font-bold text-gray-800">Atur Penugasan Akun Sosmed</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Perbarui akun yang dikelola, eksekutor, supervisor, dan arahan
                    </p>
                </div>
                <button type="button" onclick="closeAssignModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="form-assign" method="POST" action="" class="p-6 pt-1 space-y-4 overflow-y-auto">
                @csrf
                @method('PATCH')
                <input type="hidden" name="old_staff_id" id="assign-old-staff-id" :value="oldStaffId">

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
                    <div class="relative">
                        <input type="text" readonly :value="selectedLink || '-'"
                            class="w-full bg-gray-100/80 border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-600 cursor-not-allowed select-all focus:outline-none">
                        <template x-if="selectedLink && selectedLink !== '-'">
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
                        Eksekutor Akun (Dikelola Oleh)
                    </label>

                    {{-- Pengelola lain di akun ini jika multi-manager --}}
                    <template x-if="otherManagers && otherManagers.length > 0">
                        <div class="mb-2.5 p-2.5 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="text-[11px] text-gray-500 font-medium mb-1">Pengelola lain di akun ini:</p>
                            <div class="flex flex-wrap gap-1">
                                <template x-for="u in otherManagers" :key="u.id">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] bg-white text-gray-700 border border-gray-200">
                                        <span x-text="u.name"></span>
                                        <span class="text-[10px] text-gray-400 font-semibold" x-text="`(${u.role_label})`"></span>
                                    </span>
                                </template>
                            </div>
                        </div>
                    </template>

                    <select name="staff_id" id="assign-staff-sel"
                        x-model="selectedStaffId"
                        onchange="syncSupervisorState(this, 'assign-pm-sel', 'assign-pm-hint', 'assign-assistant-sel')"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="" data-role="">-- Belum Ditugaskan / Kosongkan --</option>
                        <template x-for="ex in availableExecutors" :key="ex.id">
                            <option :value="ex.id" :data-role="ex.role" x-text="`${ex.name} (${ex.role_label})`"></option>
                        </template>
                    </select>
                    <p id="assign-staff-note" class="text-[11px] text-gray-400 mt-1">Mengubah eksekutor penanggung jawab pada baris ini.</p>
                </div>

                {{-- Supervisor PM --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Supervisor PM <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>
                    <select name="pm_id" id="assign-pm-sel"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="">-- Tanpa Supervisor / Langsung ke HR --</option>
                        @foreach($pms as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }} (PM)</option>
                        @endforeach
                    </select>
                    <p id="assign-pm-hint" class="text-[11px] text-gray-400 mt-1">PM yang berwenang meninjau & approve
                        tugas.</p>
                </div>

                {{-- Asisten Pengawas --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Asisten Pengawas <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>
                    <select name="assistant_id" id="assign-assistant-sel"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="">-- Tanpa Asisten / Belum Diberi Wewenang --</option>
                        @foreach($assistants as $ast)
                            <option value="{{ $ast->id }}">{{ $ast->name }} (Asisten)</option>
                        @endforeach
                    </select>
                    <p id="assign-ast-note" class="text-[11px] text-gray-400 mt-1">Asisten HR yang berwenang meninjau &
                        approve tugas akun ini sebagai backup PM.</p>
                </div>

                {{-- Staff Pengawas --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Staff Pengawas <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>
                    <select name="supervisor_staff_id" id="assign-supervisor-sel"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="">-- Tanpa Pengawas / PM Standard --</option>
                        @foreach($supervisors as $sup)
                            <option value="{{ $sup->id }}">{{ $sup->name }} (Staff)</option>
                        @endforeach
                    </select>
                    <p id="assign-sup-note" class="text-[11px] text-gray-400 mt-1">Staff HR yang berwenang langsung
                        memverifikasi tugas tanpa menunggu PM/Asisten.</p>
                </div>

                {{-- Catatan / Arahan Penugasan --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Catatan / Arahan Penugasan</label>
                    <textarea name="notes" id="assign-notes-input" rows="2"
                        placeholder="Catatan atau instruksi pengelolaan akun..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none"></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeAssignModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">Batal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">Simpan
                        Penugasan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── MODAL VERIFIKASI FINAL (HR STAFF - APPROVE / REJECT) ─────── --}}
    <div id="modal-verify" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
        x-data="{ action: 'verify' }" @open-verify.window="action = 'verify'">
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
                            x-text="action === 'verify' ? 'Final Approval Tugas (HR Staff)' : 'Tolak & Kembalikan Tugas'">
                        </h3>
                        <p class="text-[11px] text-gray-400">Persetujuan akhir tingkat 2 untuk hasil konten tim sosmed</p>
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

                {{-- Task Info Card --}}
                <div class="bg-gray-50 rounded-xl p-3.5 border border-gray-200/80">
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Tugas yang Ditinjau</p>
                    <p id="verify-task-title" class="text-sm font-bold text-gray-800 break-words"></p>
                </div>

                {{-- Action Toggle (Segmented control) --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-2">Keputusan Final HR</label>
                    <div class="grid grid-cols-2 gap-2 p-1 bg-gray-100 rounded-xl">
                        <button type="button" @click="action = 'verify'"
                            :class="action === 'verify' ? 'bg-white text-purple-700 shadow-sm font-bold' : 'text-gray-500 font-medium hover:text-gray-700'"
                            class="py-2 text-xs rounded-lg transition-all flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Approve Final
                        </button>
                        <button type="button" @click="action = 'reject'"
                            :class="action === 'reject' ? 'bg-white text-rose-700 shadow-sm font-bold' : 'text-gray-500 font-medium hover:text-gray-700'"
                            class="py-2 text-xs rounded-lg transition-all flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
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
                <div x-show="action === 'verify'"
                    class="bg-purple-50 border border-purple-100 rounded-xl p-3 text-xs text-purple-800 leading-relaxed">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-purple-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p>Tugas akan <strong>disetujui secara final</strong> (Level 2). Status pengerjaan akan selesai 100%
                            dan dicatat dalam riwayat kinerja divisi.</p>
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
            <form id="form-my-submit" method="POST" action="" onsubmit="handleMyFormSubmit(event)"
                class="p-6 pt-1 space-y-4">
                @csrf
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                    <p class="text-xs text-gray-500 mb-0.5">Nama Akun Sosmed</p>
                    <p id="my-submit-account-name" class="text-sm font-semibold text-gray-800"></p>
                </div>

                {{-- Error flash (tampil jika ada error dari server) --}}
                @if($errors->any())
                    <div class="bg-rose-50 border border-rose-200 rounded-lg px-4 py-3">
                        <ul class="text-xs text-rose-600 space-y-1 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Bukti Konten <span class="text-red-500">*</span>
                        <span class="font-normal text-gray-400 ml-1">(bisa lebih dari satu)</span>
                    </label>
                    <div id="my-links-container" class="space-y-2 max-h-[200px] overflow-y-auto pr-1">
                        <div class="flex gap-2 link-row">
                            <input type="text" name="links[]" required placeholder="Tulis link atau keterangan bukti..."
                                class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                            <button type="button" onclick="removeMyLinkRow(this)"
                                class="text-gray-300 hover:text-rose-500 px-1 transition hidden remove-btn">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-1">Isi link atau keterangan hasil konten yang dikerjakan.</p>
                    <button type="button" onclick="addMyLinkRow()"
                        class="mt-1.5 inline-flex items-center gap-1 text-xs text-primary-600 hover:text-primary-700 font-medium">
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
                        Lanjut
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── MODAL: KONFIRMASI SUBMIT (STAFF) ────────────────────────── --}}
    <div id="modal-my-confirm" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeMyConfirm()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm z-10 p-6 text-center">
            <div
                class="w-12 h-12 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center mx-auto mb-3.5">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-base font-bold text-gray-800 mb-1">Kirim Bukti Konten?</h3>
            <p class="text-xs text-gray-500 mb-5 leading-relaxed">
                Pastikan semua link bukti sudah benar sebelum dikirim. Bukti yang sudah dikirim akan masuk ke antrian
                verifikasi Admin.
            </p>
            <div class="flex gap-3">
                <button type="button" onclick="closeMyConfirm()"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Periksa Lagi
                </button>
                <button type="button" onclick="submitMyConfirmed()"
                    class="flex-1 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                    Ya, Kirim
                </button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Auto-upgrade select elements to have an integrated search dropdown
            const selectsToUpgrade = document.querySelectorAll(
                'select[id="assign-staff-sel"], select[id="assign-pm-sel"], select[id="assign-assistant-sel"], select[id="assign-supervisor-sel"], ' +
                'select[id="assign-task-staff"], select[id="assign-task-pm"], select[id="assign-task-ast"], select[id="assign-task-supervisor"]'
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
                    button.disabled = select.disabled;
                    if (select.disabled) {
                        button.className = 'w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-left flex justify-between items-center bg-gray-100 text-gray-400 cursor-not-allowed opacity-70 select-none';
                        const svg = button.querySelector('svg');
                        if (svg) svg.classList.add('opacity-40');
                    } else {
                        button.className = 'w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-left flex justify-between items-center transition bg-white text-gray-800 focus:ring-2 focus:ring-primary-500 focus:outline-none';
                        const svg = button.querySelector('svg');
                        if (svg) svg.classList.remove('opacity-40');
                    }
                });
                observer.observe(select, { attributes: true, attributeFilter: ['disabled'] });

                if (select.disabled) {
                    button.disabled = true;
                    button.className = 'w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-left flex justify-between items-center bg-gray-100 text-gray-400 cursor-not-allowed opacity-70 select-none';
                    const svg = button.querySelector('svg');
                    if (svg) svg.classList.add('opacity-40');
                }
            });
        });

        // Deprecated functions kept for compatibility
        function filterSelectOptions(query, selectId) { }
        function resetSearchFilter(inputId, selectId) { }

        function syncSupervisorState(staffSelect, pmSelectId, hintId, astSelectId) {
            if (!staffSelect) return;

            const pmSelect = document.getElementById(pmSelectId);
            const assistantSelect = astSelectId
                ? document.getElementById(astSelectId)
                : document.getElementById('assign-assistant-sel');
            const hint = document.getElementById(hintId);
            if (!pmSelect) return;

            // Jika berada dalam form yang belum memilih akun, tetap disable supervisor
            const accountInput = staffSelect.form ? staffSelect.form.querySelector('input[name="sosmed_account_id"]') : null;
            if (accountInput && !accountInput.value) {
                pmSelect.value = '';
                pmSelect.disabled = true;
                pmSelect.classList.add('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
                if (typeof setCustomSelectDisabled === 'function') {
                    setCustomSelectDisabled(pmSelect, true);
                }
                if (assistantSelect) {
                    assistantSelect.value = '';
                    assistantSelect.disabled = true;
                    assistantSelect.classList.add('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
                    if (typeof setCustomSelectDisabled === 'function') {
                        setCustomSelectDisabled(assistantSelect, true);
                    }
                }
                if (hint) {
                    hint.innerHTML = 'Pilih akun terlebih dahulu untuk mengatur supervisor.';
                }
                return;
            }

            const selectedOption = staffSelect.options[staffSelect.selectedIndex];
            const role = selectedOption ? selectedOption.getAttribute('data-role') : null;

            // Role yang langsung ke HR → PM & Asisten dinonaktifkan
            const isDirectToHr = ['pm', 'hr_assistant', 'hr_staff'].includes(role);

            if (isDirectToHr) {
                pmSelect.value = '';
                pmSelect.disabled = true;
                pmSelect.classList.add('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
                // Sync custom dropdown button (jika ada)
                if (typeof setCustomSelectDisabled === 'function') {
                    setCustomSelectDisabled(pmSelect, true);
                }

                if (assistantSelect) {
                    assistantSelect.value = '';
                    assistantSelect.disabled = true;
                    assistantSelect.classList.add('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
                    if (typeof setCustomSelectDisabled === 'function') {
                        setCustomSelectDisabled(assistantSelect, true);
                    }
                }

                if (hint) {
                    if (role === 'pm') {
                        hint.innerHTML = '<span class="text-indigo-600 font-semibold">🔒 PM Mandiri:</span> Akun dikelola langsung oleh PM. Hasil pengerjaan otomatis lolos Level 1 dan langsung diverifikasi HR Staff (supervisor dan asisten otomatis dinonaktifkan).';
                    } else if (role === 'hr_assistant') {
                        hint.innerHTML = '<span class="text-teal-600 font-semibold">🔒 HR Asisten:</span> Eksekutor adalah Asisten HR. Supervisor PM dan Asisten Pengawas tidak diperlukan (langsung ke HR).';
                    } else {
                        hint.innerHTML = '<span class="text-amber-600 font-semibold">🔒 HR Staff:</span> Eksekutor adalah Staff HR. Supervisor PM dan Asisten Pengawas dinonaktifkan.';
                    }
                }
            } else {
                pmSelect.disabled = false;
                pmSelect.classList.remove('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
                if (typeof setCustomSelectDisabled === 'function') {
                    setCustomSelectDisabled(pmSelect, false);
                }

                if (assistantSelect) {
                    assistantSelect.disabled = false;
                    assistantSelect.classList.remove('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
                    if (typeof setCustomSelectDisabled === 'function') {
                        setCustomSelectDisabled(assistantSelect, false);
                    }
                }

                if (hint) {
                    hint.innerHTML = 'PM yang berwenang meninjau & approve tugas.';
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

        function openAssignModal(accId, accName, currentPmId, currentStaffId, currentAssistantId, currentStaffRole, platform, link, notes, currentSupervisorStaffId) {
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
                    current_staff_id: currentStaffId,
                    notes: notes,
                    assistant_id: currentAssistantId,
                    staff_role: currentStaffRole,
                    supervisor_staff_id: currentSupervisorStaffId
                };
            }

            document.getElementById('form-assign').action = `/staff/sosmed/accounts/${accData.id}/assign`;

            window.dispatchEvent(new CustomEvent('open-edit-account', { detail: accData }));

            const staffSel = document.getElementById('assign-staff-sel');
            const pmSel = document.getElementById('assign-pm-sel');
            const astSel = document.getElementById('assign-assistant-sel');
            const supSel = document.getElementById('assign-supervisor-sel');
            const notesEl = document.getElementById('assign-notes-input');

            if (staffSel) staffSel.value = accData.current_staff_id ? String(accData.current_staff_id) : '';
            if (pmSel) pmSel.value = accData.pm_id ?? '';
            if (astSel) astSel.value = accData.assistant_id ?? '';
            if (supSel) supSel.value = accData.supervisor_staff_id ?? '';
            if (notesEl) notesEl.value = accData.notes ?? '';

            const adminAssigned = (accData.staff_users && accData.staff_users.some(u => u.role === 'hr_staff')) || accData.staff_role === 'hr_staff';

            if (adminAssigned) {
                // Lock all — Admin has already set everything
                setCustomSelectDisabled(staffSel, true);
                setCustomSelectDisabled(pmSel, true);
                setCustomSelectDisabled(astSel, true);
                setCustomSelectDisabled(supSel, true);
            } else {
                // Re-enable all, then let syncSupervisorState decide PM
                setCustomSelectDisabled(staffSel, false);
                setCustomSelectDisabled(astSel, false);
                setCustomSelectDisabled(supSel, false);
                syncSupervisorState(staffSel, 'assign-pm-sel', 'assign-pm-hint', 'assign-assistant-sel');
            }

            // Update helper notes
            const staffNote = document.getElementById('assign-staff-note');
            const pmNote = document.getElementById('assign-pm-hint');
            const astNote = document.getElementById('assign-ast-note');
            const supNote = document.getElementById('assign-sup-note');

            if (staffNote) {
                staffNote.textContent = adminAssigned
                    ? '🔒 Eksekutor ditetapkan oleh Admin dan tidak dapat diubah.'
                    : 'Mengubah eksekutor penanggung jawab pada baris ini.';
                staffNote.className = adminAssigned ? 'text-[11px] text-amber-600 mt-1' : 'text-[11px] text-gray-400 mt-1';
            }
            if (pmNote && adminAssigned) {
                pmNote.textContent = '🔒 Supervisor PM sudah ditetapkan oleh Admin, tidak dapat diubah.';
                pmNote.className = 'text-[11px] text-amber-600 mt-1';
            }
            if (astNote) {
                astNote.textContent = adminAssigned
                    ? '🔒 Asisten pengawas sudah ditetapkan oleh Admin, tidak dapat diubah.'
                    : 'Jika dipilih, asisten ini berwenang melihat tugas dan memverifikasi Level-1 tugas akun ini.';
                astNote.className = adminAssigned ? 'text-[11px] text-amber-600 mt-1' : 'text-[11px] text-gray-400 mt-1';
            }
            if (supNote) {
                supNote.textContent = adminAssigned
                    ? '🔒 Staff pengawas sudah ditetapkan oleh Admin, tidak dapat diubah.'
                    : 'Staff HR yang berwenang langsung memverifikasi tugas tanpa menunggu PM/Asisten.';
                supNote.className = adminAssigned ? 'text-[11px] text-amber-600 mt-1' : 'text-[11px] text-gray-400 mt-1';
            }

            // Update custom dropdown labels after Alpine renders options
            setTimeout(() => {
                if (staffSel) {
                    staffSel.value = accData.current_staff_id ? String(accData.current_staff_id) : '';
                    if (!adminAssigned) {
                        syncSupervisorState(staffSel, 'assign-pm-sel', 'assign-pm-hint', 'assign-assistant-sel');
                    }
                    staffSel.dispatchEvent(new Event('change', { bubbles: true }));
                }
                if (pmSel) pmSel.dispatchEvent(new Event('change', { bubbles: true }));
                if (astSel) astSel.dispatchEvent(new Event('change', { bubbles: true }));
                if (supSel) supSel.dispatchEvent(new Event('change', { bubbles: true }));
            }, 50);

            document.getElementById('modal-assign').classList.remove('hidden');
        }

        function closeAssignModal() {
            document.getElementById('modal-assign').classList.add('hidden');
        }

        // Enable any disabled select before submitting forms so payload isn't dropped
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function () {
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

        // ── My Submit Modal (Staff own tasks) ──────────────────────────────────
        function openMySubmitModal(accountId, accountName, existingDesc) {
            document.getElementById('my-submit-account-name').textContent = accountName;
            document.getElementById('form-my-submit').action = `/staff/sosmed/accounts/${accountId}/submit`;
            document.getElementById('my-submit-description').value = existingDesc || '';

            // Reset links container to single empty input
            const container = document.getElementById('my-links-container');
            container.innerHTML = `
                                                    <div class="flex gap-2 link-row">
                                                        <input type="text" name="links[]" required
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

        function handleMyFormSubmit(event) {
            event.preventDefault();

            // Validate: at least one non-empty link
            const links = document.querySelectorAll('#my-links-container input[name="links[]"]');
            let hasLink = false;
            links.forEach(input => { if (input.value.trim()) hasLink = true; });
            if (!hasLink) {
                alert('Harap isi minimal satu link bukti.');
                return;
            }

            // Show confirmation modal
            document.getElementById('modal-my-confirm').classList.remove('hidden');
        }

        function closeMyConfirm() {
            document.getElementById('modal-my-confirm').classList.add('hidden');
        }

        function submitMyConfirmed() {
            closeMyConfirm();
            // Add https:// prefix if missing, to prevent URL validation errors
            document.querySelectorAll('#my-links-container input[name="links[]"]').forEach(input => {
                const val = input.value.trim();
                if (val && !val.startsWith('http://') && !val.startsWith('https://')) {
                    input.value = 'https://' + val;
                }
            });
            document.getElementById('form-my-submit').submit();
        }

        function openAssignTaskModal() {
            window.dispatchEvent(new CustomEvent('reset-assign-task'));
            const staffSel = document.getElementById('assign-task-staff');
            if (staffSel) {
                staffSel.value = '';
                if (typeof setCustomSelectDisabled === 'function') setCustomSelectDisabled(staffSel, true);
                else staffSel.disabled = true;
            }
            const pmSel = document.getElementById('assign-task-pm');
            if (pmSel) {
                pmSel.value = '';
                if (typeof setCustomSelectDisabled === 'function') setCustomSelectDisabled(pmSel, true);
                else pmSel.disabled = true;
            }
            const astSel = document.getElementById('assign-task-ast');
            if (astSel) {
                astSel.value = '';
                if (typeof setCustomSelectDisabled === 'function') setCustomSelectDisabled(astSel, true);
                else astSel.disabled = true;
            }
            const supSel = document.getElementById('assign-task-supervisor');
            if (supSel) {
                supSel.value = '';
                if (typeof setCustomSelectDisabled === 'function') setCustomSelectDisabled(supSel, true);
                else supSel.disabled = true;
            }
            const hint = document.getElementById('assign-task-pm-hint');
            if (hint) hint.textContent = 'PM yang berwenang meninjau & approve tugas.';

            document.getElementById('modal-assign-task').classList.remove('hidden');
        }

        function assignTaskDropdown(accountsList, executorsList) {
            return {
                accounts: accountsList || [],
                executors: executorsList || [],
                selectedId: '',
                selectedName: '',
                selectedPlatform: '',
                selectedLink: '',
                selectedAccount: null,
                selectedStaffId: '',
                search: '',
                open: false,
                resetState() {
                    this.selectedId = '';
                    this.selectedName = '';
                    this.selectedPlatform = '';
                    this.selectedLink = '';
                    this.selectedAccount = null;
                    this.selectedStaffId = '';
                    this.search = '';
                    this.open = false;
                },
                get selectedLabel() {
                    if (!this.selectedId) return '';
                    return this.selectedName + ' (' + this.selectedPlatform + ')';
                },
                get selectedManagersCount() {
                    return this.selectedAccount ? (this.selectedAccount.managers_count || 0) : 0;
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
                get availableExecutors() {
                    if (!this.selectedAccount || !this.selectedAccount.assigned_user_ids) {
                        return this.executors;
                    }
                    const assigned = this.selectedAccount.assigned_user_ids.map(Number);
                    return this.executors.filter(u => !assigned.includes(Number(u.id)));
                },
                selectAccount(acc) {
                    this.selectedId = acc.id;
                    this.selectedName = acc.name;
                    this.selectedPlatform = acc.platform;
                    this.selectedLink = acc.link || '';
                    this.selectedAccount = acc;
                    this.open = false;

                    // If currently selected staff is already assigned to this account, reset it
                    if (this.selectedStaffId && acc.assigned_user_ids && acc.assigned_user_ids.map(Number).includes(Number(this.selectedStaffId))) {
                        this.selectedStaffId = '';
                    }

                    this.$nextTick(() => {
                        const staffSel = document.getElementById('assign-task-staff');
                        const supSel = document.getElementById('assign-task-supervisor');
                        if (staffSel && typeof setCustomSelectDisabled === 'function') {
                            setCustomSelectDisabled(staffSel, false);
                        }
                        if (supSel && typeof setCustomSelectDisabled === 'function') {
                            setCustomSelectDisabled(supSel, false);
                        }
                        syncSupervisorState(staffSel, 'assign-task-pm', 'assign-task-pm-hint', 'assign-task-ast');
                    });
                }
            };
        }

        function editAccountDropdown(availableList, executorsList) {
            return {
                availableAccounts: availableList || [],
                executors: executorsList || [],
                accountsList: [],
                selectedId: '',
                selectedName: '',
                selectedPlatform: '',
                selectedLink: '',
                currentStaffUsers: [],
                selectedStaffId: '',
                oldStaffId: '',
                search: '',
                open: false,
                get selectedLabel() {
                    if (!this.selectedId) return '';
                    return this.selectedName + ' (' + this.selectedPlatform + ')';
                },
                get otherManagers() {
                    if (!this.currentStaffUsers) return [];
                    return this.currentStaffUsers.filter(u => Number(u.id) !== Number(this.oldStaffId));
                },
                get availableExecutors() {
                    const otherIds = this.otherManagers.map(u => Number(u.id));
                    return this.executors.filter(u => !otherIds.includes(Number(u.id)));
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
                    this.currentStaffUsers = accData.staff_users || [];
                    this.oldStaffId = accData.current_staff_id ? String(accData.current_staff_id) : '';
                    this.selectedStaffId = this.oldStaffId;
                    this.search = '';
                    this.open = false;

                    const list = [{
                        id: accData.id,
                        name: accData.name,
                        platform: accData.platform,
                        link: accData.link || '',
                        managers_count: accData.managers_count || 0,
                        assigned_user_ids: accData.assigned_user_ids || []
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

        function addMyLinkRow() {
            const container = document.getElementById('my-links-container');
            const newRow = document.createElement('div');
            newRow.className = 'flex gap-2 link-row';
            newRow.innerHTML = `
                                                                                    <input type="text" name="links[]" required
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