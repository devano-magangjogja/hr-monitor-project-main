@extends('layouts.app')

@section('title', 'Manajemen Akun')
@section('page-title', 'Manajemen Akun')
@section('page-subtitle', 'Kelola daftar akun sosial media dan kredensial akses untuk penugasan tim')

@section('sidebar')
    @include(auth()->user()->isAdmin() ? 'components.sidebar-admin' : 'components.sidebar-staff')
@endsection

@section('content')

@php($accountPrefix = auth()->user()->isAdmin() ? 'admin' : 'staff')

{{-- ── STATS CARDS ─────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    {{-- Total Akun → reset filter status --}}
    <a href="{{ route($accountPrefix . '.accounts.index', ['tab' => 'accounts']) }}"
        class="bg-white rounded-xl border p-4 shadow-sm flex items-center gap-4 transition hover:shadow-md
            {{ empty($status) && ($tab ?? 'accounts') === 'accounts' ? 'border-primary-400 ring-2 ring-primary-100' : 'border-gray-200 hover:border-primary-200' }}">
        <div class="w-12 h-12 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-xs font-medium text-gray-500">Total Akun</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
            <p class="text-[11px] text-gray-400">Klik untuk tampilkan semua</p>
        </div>
    </a>

    {{-- Belum Ditugaskan --}}
    <a href="{{ route($accountPrefix . '.accounts.index', ['tab' => 'accounts', 'status' => 'unassigned']) }}"
        class="bg-white rounded-xl border p-4 shadow-sm flex items-center gap-4 transition hover:shadow-md
            {{ ($status ?? '') === 'unassigned' ? 'border-emerald-400 ring-2 ring-emerald-100' : 'border-gray-200 hover:border-emerald-200' }}">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-xs font-medium text-gray-500">Belum Ditugaskan (Tersedia)</p>
            <p class="text-2xl font-bold text-emerald-600">{{ $stats['unassigned'] }}</p>
            <p class="text-[11px] text-gray-400">Klik untuk filter akun tersedia</p>
        </div>
    </a>

    {{-- Sudah Ditugaskan --}}
    <a href="{{ route($accountPrefix . '.accounts.index', ['tab' => 'accounts', 'status' => 'assigned']) }}"
        class="bg-white rounded-xl border p-4 shadow-sm flex items-center gap-4 transition hover:shadow-md
            {{ ($status ?? '') === 'assigned' ? 'border-blue-400 ring-2 ring-blue-100' : 'border-gray-200 hover:border-blue-200' }}">
        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-xs font-medium text-gray-500">Sudah Ditugaskan</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['assigned'] }}</p>
            <p class="text-[11px] text-gray-400">Klik untuk filter akun aktif</p>
        </div>
    </a>
</div>

{{-- ── TABS ────────────────────────────────────────────────────── --}}
<div class="flex items-center gap-2 border-b border-gray-200 mb-6">
    <a href="{{ route($accountPrefix . '.accounts.index', ['tab' => 'accounts']) }}"
        class="px-4 py-3 text-sm font-semibold border-b-2 {{ $tab === 'accounts' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
        Daftar Akun Terverifikasi
    </a>
    <a href="{{ route($accountPrefix . '.accounts.index', ['tab' => 'pending']) }}"
        class="px-4 py-3 text-sm font-semibold border-b-2 {{ $tab === 'pending' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
        Pengajuan Akun Baru
        @if($pendingAccounts->total() > 0)
            <span
                class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] bg-amber-100 text-amber-700">{{ $pendingAccounts->total() }}</span>
        @endif
    </a>
</div>

{{-- ═══════════════════════════════════════════════════════════════
TAB: PENDING ACCOUNTS
═══════════════════════════════════════════════════════════════ --}}
@if($tab === 'pending')
    <div class="bg-white rounded-xl border border-amber-200 shadow-sm overflow-hidden">
        <!-- Header Section -->
        <div class="px-4 sm:px-6 py-4 border-b border-amber-100 bg-amber-50/50">
            <h2 class="text-base font-bold text-gray-800">Pengajuan Akun Menunggu Verifikasi</h2>
            <p class="text-xs text-gray-500 mt-0.5">Lengkapi atribut keamanan sebelum menyetujui atau menolak pengajuan.</p>
        </div>

        {{-- ── SEARCH BAR PENDING ──────────────────────────────── --}}
        <div class="px-4 sm:px-6 py-3 border-b border-gray-100 bg-white">
            <form action="{{ route($accountPrefix . '.accounts.index') }}" method="GET"
                class="flex flex-col sm:flex-row sm:items-center gap-3">
                <input type="hidden" name="tab" value="pending">

                <div class="relative flex-1 min-w-[220px]">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                        placeholder="Cari nama akun, platform, email, atau pengaju..."
                        class="w-full h-10 pl-9 pr-3 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:bg-white focus:outline-none transition">
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <select name="platform" onchange="this.form.submit()"
                        class="h-10 w-full sm:w-40 px-3 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:bg-white focus:outline-none transition">
                        <option value="">Semua Platform</option>
                        @foreach($platformList as $p)
                            <option value="{{ $p }}" {{ ($platform ?? '') === $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>

                    <button type="submit"
                        class="h-10 px-4 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-lg transition shrink-0">
                        Cari
                    </button>

                    @if(($search ?? null) || ($platform ?? null))
                        <a href="{{ route($accountPrefix . '.accounts.index', ['tab' => 'pending']) }}"
                            class="flex items-center justify-center h-10 w-10 text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition shrink-0"
                            title="Reset Filter">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- TAMPILAN MOBILE (Card View) - Tampil di layar < md -->
        <div class="block md:hidden divide-y divide-gray-100">
            @forelse($pendingAccounts as $pending)
                <div class="p-4 space-y-3">
                    <!-- Info Pengaju & Platform -->
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span
                                class="inline-flex px-2 py-0.5 rounded-md border text-[11px] font-semibold {{ $pending->platform_color }} mb-1">
                                {{ $pending->platform }}
                            </span>
                            <h3 class="font-bold text-gray-900 text-sm leading-snug">{{ $pending->name }}</h3>
                        </div>
                        <div class="text-right">
                            <span
                                class="text-[11px] font-medium text-gray-500 block">{{ $pending->creator?->name ?? '-' }}</span>
                            <span class="text-[10px] text-gray-400 block">{{ $pending->creator?->role_label ?? '' }}</span>
                        </div>
                    </div>

                    <!-- Detail Email & Password -->
                    <div class="bg-gray-50 rounded-lg p-2.5 space-y-1.5 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Email:</span>
                            <span class="font-medium text-gray-700 truncate max-w-[200px]">{{ $pending->email ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center" x-data="{ show: false }">
                            <span class="text-gray-400">Password:</span>
                            @if($pending->password)
                                <div class="flex items-center gap-1.5">
                                    <span x-show="!show" class="font-mono text-gray-400 tracking-widest">••••••••</span>
                                    <span x-show="show" x-cloak
                                        class="font-mono font-semibold text-gray-800">{{ $pending->password }}</span>
                                    <button type="button" @click="show = !show" class="text-gray-400 hover:text-primary-600 p-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <button type="button" onclick="openRejectAccountModal({{ $pending->id }}, @js($pending->name))"
                            class="w-full py-2 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold transition">Reject</button>
                        <button type="button"
                            onclick="openApproveAccountModal({{ $pending->id }}, @js($pending->name), @js($pending->email_recovery), @js($pending->phone), {{ $pending->two_factor_enabled ? 'true' : 'false' }})"
                            class="w-full py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition">Approve</button>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-xs text-gray-400">Tidak ada pengajuan akun baru.</div>
            @endforelse
        </div>

        <!-- TAMPILAN DESKTOP (Table View) - Tampil di layar >= md -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead
                    class="bg-gray-50 border-b border-gray-200 text-[11px] font-bold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-5 py-3.5 w-3/12">Pengaju & Akun</th>
                        <th class="px-5 py-3.5 w-2/12">Platform</th>
                        <th class="px-5 py-3.5 w-3/12">Email</th>
                        <th class="px-5 py-3.5 w-2/12">Password</th>
                        <th class="px-5 py-3.5 w-2/12 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pendingAccounts as $pending)
                        <tr class="align-top hover:bg-amber-50/20 transition">
                            <!-- Pengaju -->
                            <td class="px-5 py-4">
                                <div class="font-bold text-gray-900 leading-snug">{{ $pending->name }}</div>
                                <div class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                    <span class="font-medium text-gray-700">{{ $pending->creator?->name ?? '-' }}</span>
                                </div>
                                <div class="text-[10px] text-gray-400">{{ $pending->creator?->role_label ?? '' }}</div>
                            </td>

                            <!-- Platform -->
                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex px-2.5 py-1 rounded-md border text-xs font-semibold {{ $pending->platform_color }}">
                                    {{ $pending->platform }}
                                </span>
                            </td>

                            <!-- Email -->
                            <td class="px-5 py-4 text-xs text-gray-700 font-medium">
                                {{ $pending->email ?: '-' }}
                            </td>

                            <!-- Password Toggle -->
                            <td class="px-5 py-4" x-data="{ show: false }">
                                @if($pending->password)
                                    <div class="flex items-center gap-2">
                                        <span x-show="!show" class="font-mono text-xs tracking-widest text-gray-400">••••••••</span>
                                        <span x-show="show" x-cloak
                                            class="font-mono text-xs font-semibold text-gray-800 bg-gray-100 px-1.5 py-0.5 rounded">{{ $pending->password }}</span>
                                        <button type="button" @click="show = !show"
                                            class="text-gray-400 hover:text-amber-600 transition p-0.5"
                                            title="Lihat/sembunyikan password">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>

                            <!-- Form Atribut Security & Action -->
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button"
                                        onclick="openRejectAccountModal({{ $pending->id }}, @js($pending->name))"
                                        class="px-3 py-1.5 rounded-md border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold transition">Reject</button>
                                    <button type="button"
                                        onclick="openApproveAccountModal({{ $pending->id }}, @js($pending->name), @js($pending->email_recovery), @js($pending->phone), {{ $pending->two_factor_enabled ? 'true' : 'false' }})"
                                        class="px-3 py-1.5 rounded-md bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition">Approve</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-sm text-gray-400">
                                Tidak ada pengajuan akun baru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($pendingAccounts->hasPages())
            <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $pendingAccounts->links() }}
            </div>
        @endif
    </div>

    {{-- ── MODAL APPROVE ACCOUNT ──────────────────────────────── --}}
    <div id="modal-approve-account" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeAccountDecisionModals()"></div>
        <div class="relative z-10 w-full max-w-md rounded-xl bg-white shadow-2xl p-6">
            <h3 class="text-base font-bold text-gray-800">Lengkapi Atribut Keamanan</h3>
            <p class="mt-1 text-xs text-gray-500">Approve akun <strong id="approve-account-name"></strong> setelah data
                keamanan diisi.</p>
            <form id="form-approve-account" method="POST" class="mt-5 space-y-4">
                @csrf @method('PATCH')
                <input type="hidden" name="verification_status" value="approved">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Email Recovery</label>
                    <input id="approve-recovery" type="email" name="email_recovery"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nomor HP / Telepon</label>
                    <input id="approve-phone" type="text" name="phone"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input id="approve-2fa" type="checkbox" name="two_factor_enabled" value="1"
                        class="rounded border-gray-300"> 2FA aktif
                </label>
                <div class="flex gap-2 pt-2">
                    <button type="button" onclick="closeAccountDecisionModals()"
                        class="flex-1 rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700">Batal</button>
                    <button
                        class="flex-1 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">Approve</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── MODAL REJECT ACCOUNT ───────────────────────────────── --}}
    <div id="modal-reject-account" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeAccountDecisionModals()"></div>
        <div class="relative z-10 w-full max-w-md rounded-xl bg-white shadow-2xl p-6">
            <h3 class="text-base font-bold text-gray-800">Tolak Pengajuan Akun</h3>
            <p class="mt-1 text-xs text-gray-500">Berikan catatan untuk pengaju agar penolakan dapat ditindaklanjuti.</p>
            <form id="form-reject-account" method="POST" class="mt-5 space-y-4">
                @csrf @method('PATCH')
                <input type="hidden" name="verification_status" value="rejected">
                <textarea name="rejection_note" required maxlength="1000" rows="4"
                    placeholder="Tuliskan alasan penolakan..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></textarea>
                <div class="flex gap-2">
                    <button type="button" onclick="closeAccountDecisionModals()"
                        class="flex-1 rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700">Batal</button>
                    <button class="flex-1 rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white">Reject</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const accountPrefix = @json($accountPrefix);

        function openApproveAccountModal(id, name, recovery, phone, twoFactor) {
            document.getElementById('form-approve-account').action = `/${accountPrefix}/accounts/${id}/verify`;
            document.getElementById('approve-account-name').textContent = name;
            document.getElementById('approve-recovery').value = recovery || '';
            document.getElementById('approve-phone').value = phone || '';
            document.getElementById('approve-2fa').checked = Boolean(twoFactor);
            document.getElementById('modal-approve-account').classList.remove('hidden');
        }

        function openRejectAccountModal(id) {
            document.getElementById('form-reject-account').action = `/${accountPrefix}/accounts/${id}/verify`;
            document.getElementById('modal-reject-account').classList.remove('hidden');
            document.querySelector('#form-reject-account textarea').focus();
        }

        function closeAccountDecisionModals() {
            document.getElementById('modal-approve-account').classList.add('hidden');
            document.getElementById('modal-reject-account').classList.add('hidden');
        }
    </script>
@else

    {{-- ═══════════════════════════════════════════════════════════
    TAB: DAFTAR AKUN TERVERIFIKASI
    ═══════════════════════════════════════════════════════════ --}}

    {{-- ── FILTER & ACTION BAR ──────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <form action="{{ route($accountPrefix . '.accounts.index') }}" method="GET"
                class="flex flex-col sm:flex-row sm:items-center gap-3 flex-1">
                {{-- Search --}}
                <div class="relative flex-1 min-w-[220px]">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                        placeholder="Cari nama akun, platform, atau email..."
                        class="w-full h-10 pl-9 pr-3 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:bg-white focus:outline-none transition">
                </div>

                {{-- Filter group --}}
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    <select name="platform" onchange="this.form.submit()"
                        class="h-10 w-full sm:w-36 px-3 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:bg-white focus:outline-none transition">
                        <option value="">Semua Platform</option>
                        @foreach($platformList as $p)
                            <option value="{{ $p }}" {{ ($platform ?? '') === $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>

                    <select name="brand" onchange="this.form.submit()"
                        class="h-10 w-full sm:w-36 px-3 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:bg-white focus:outline-none transition">
                        <option value="">Semua Brand</option>
                        @foreach($brands as $b)
                            <option value="{{ $b }}" {{ ($brand ?? '') === $b ? 'selected' : '' }}>{{ $b }}</option>
                        @endforeach
                    </select>

                    @if($search || $platform || $brand || $status)
                        <a href="{{ route($accountPrefix . '.accounts.index') }}"
                            class="flex items-center justify-center h-10 w-10 text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition shrink-0"
                            title="Reset Filter">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </div>
            </form>

            <button onclick="openCreateAccountModal()"
                class="inline-flex items-center justify-center gap-2 h-10 px-4 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg shadow-sm transition shrink-0 w-full lg:w-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Akun Baru
            </button>
        </div>
    </div>

    {{-- ── TABLE ACCOUNTS ──────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left table-fixed">
                <thead>
                    <tr
                        class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-4 py-3.5 w-[12%]">Platform</th>
                        <th class="px-4 py-3.5 w-[16%]">Nama Akun</th>
                        <th class="px-4 py-3.5 w-[10%]">Link</th>
                        <th class="px-4 py-3.5 w-[18%]">Email</th>
                        <th class="px-4 py-3.5 w-[14%]">Password</th>
                        <th class="px-4 py-3.5 w-[6%] text-center">2FA</th>
                        <th class="px-4 py-3.5 w-[16%]">Status</th>
                        <th class="px-4 py-3.5 w-[8%] text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($accounts as $acc)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    {{-- Platform --}}
                                    <td class="px-4 py-3.5">
                                        <span
                                            class="inline-flex max-w-full truncate items-center px-2 py-1 rounded-md text-xs font-medium border {{ $acc->platform_color }}"
                                            title="{{ $acc->platform }}">
                                            {{ $acc->platform }}
                                        </span>
                                    </td>

                                    {{-- Nama Akun (truncate jika panjang) --}}
                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="font-semibold text-gray-900 truncate" title="{{ $acc->name }}">
                                                {{ $acc->name }}
                                            </span>
                                            @if($acc->brand)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200"
                                                    title="Brand: {{ $acc->brand }}">
                                                    <svg class="w-3 h-3 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                                    </svg>
                                                    {{ $acc->brand }}
                                                </span>
                                            @endif
                                        </div>
                                        @if($acc->notes)
                                            <div class="text-xs text-gray-400 truncate mt-0.5" title="{{ $acc->notes }}">
                                                {{ $acc->notes }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Link --}}
                                    <td class="px-4 py-3.5">
                                        @if($acc->link)
                                            <a href="{{ $acc->link }}" target="_blank" rel="noopener noreferrer"
                                                class="inline-flex items-center gap-1 text-xs font-medium text-primary-600 hover:text-primary-800 hover:underline"
                                                title="{{ $acc->link }}">
                                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                                <span>Buka</span>
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400 italic">—</span>
                                        @endif
                                    </td>

                                    {{-- Email (truncate jika panjang) --}}
                                    <td class="px-4 py-3.5">
                                        @if($acc->email)
                                            <div class="flex items-center gap-1 min-w-0" x-data="{ copied: false }">
                                                <span
                                                    class="text-xs font-mono text-gray-700 bg-gray-50 border border-gray-200 px-2 py-1 rounded truncate min-w-0"
                                                    title="{{ $acc->email }}">
                                                    {{ $acc->email }}
                                                </span>
                                                <button type="button"
                                                    @click="navigator.clipboard.writeText(@js($acc->email)); copied = true; setTimeout(() => copied = false, 2000)"
                                                    class="p-1 text-gray-400 hover:text-gray-600 rounded transition shrink-0"
                                                    :title="copied ? 'Tersalin!' : 'Salin Email'">
                                                    <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                    </svg>
                                                    <svg x-show="copied" x-cloak class="w-3.5 h-3.5 text-emerald-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic">—</span>
                                        @endif
                                    </td>

                                    {{-- Password --}}
                                    <td class="px-4 py-3.5">
                                        @if($acc->password)
                                            <div class="flex items-center gap-1 min-w-0" x-data="{ show: false, copied: false }">
                                                <div
                                                    class="bg-gray-50 border border-gray-200 px-2 py-1 rounded min-w-0 flex-1 overflow-hidden">
                                                    <span x-show="!show"
                                                        class="font-mono text-xs text-gray-400 tracking-widest select-none">••••••••</span>
                                                    <span x-show="show" x-cloak
                                                        class="font-mono text-xs text-gray-900 font-semibold truncate block"
                                                        title="{{ $acc->password }}">{{ $acc->password }}</span>
                                                </div>
                                                <button type="button" @click="show = !show"
                                                    class="p-1 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded transition shrink-0"
                                                    :title="show ? 'Sembunyikan' : 'Lihat'">
                                                    <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    <svg x-show="show" x-cloak class="w-4 h-4 text-primary-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                                    </svg>
                                                </button>
                                                <button type="button"
                                                    @click="navigator.clipboard.writeText(@js($acc->password)); copied = true; setTimeout(() => copied = false, 2000)"
                                                    class="p-1 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded transition shrink-0"
                                                    :title="copied ? 'Tersalin!' : 'Salin'">
                                                    <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                    </svg>
                                                    <svg x-show="copied" x-cloak class="w-3.5 h-3.5 text-emerald-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic">—</span>
                                        @endif
                                    </td>

                                    {{-- 2FA --}}
                                    <td
                                        class="px-4 py-3.5 text-center text-xs font-semibold {{ $acc->two_factor_enabled ? 'text-emerald-600' : 'text-gray-400' }}">
                                        {{ $acc->two_factor_enabled ? 'Ya' : 'Tidak' }}
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-4 py-3.5">
                                        @if(($acc->verification_status ?? 'approved') === 'pending')
                                            <form method="POST" action="{{ route($accountPrefix . '.accounts.verify', $acc) }}"
                                                class="mb-1">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="verification_status" value="approved">
                                                <button
                                                    class="px-2 py-1 text-[11px] font-semibold rounded bg-emerald-600 text-white">Verifikasi</button>
                                            </form>
                                        @endif
                                        @if($acc->is_in_sosmed)
                                            @if($acc->staffUsers->count() > 0)
                                                <div class="inline-flex max-w-full items-center gap-1.5 px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200"
                                                    title="Dikelola: {{ $acc->staffUsers->pluck('name')->join(', ') }}">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 shrink-0"></span>
                                                    <span class="truncate">{{ $acc->staffUsers->pluck('name')->join(', ') }}</span>
                                                    @if($acc->staffUsers->count() > 1)
                                                        <span class="inline-flex items-center px-1 py-0.2 rounded-full text-[10px] font-bold bg-blue-200 text-blue-800">
                                                            {{ $acc->staffUsers->count() }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <div
                                                    class="inline-flex max-w-full items-center gap-1.5 px-2 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>
                                                    <span class="truncate">Belum ada pengelola</span>
                                                </div>
                                            @endif
                                        @else
                                            <div
                                                class="inline-flex max-w-full items-center gap-1.5 px-2 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                                <span class="truncate">Belum di Sosmed</span>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-4 py-3.5 text-center">
                                        <div class="flex items-center justify-center gap-0.5">
                                            <button type="button" onclick="openAccountDetail({{ json_encode([
                            'platform' => $acc->platform,
                            'name' => $acc->name,
                            'brand' => $acc->brand ?? '',
                            'link' => $acc->link ?? '',
                            'email' => $acc->email ?? '',
                            'password' => $acc->password ?? '',
                            'email_recovery' => $acc->email_recovery ?? '',
                            'phone' => $acc->phone ?? '',
                            'two_factor' => $acc->two_factor_enabled ? 'Ya' : 'Tidak',
                            'notes' => $acc->notes ?? '',
                            'status' => $acc->is_in_sosmed ? 'Dikelola' : 'Belum ditambahkan ke Sosmed',
                        ]) }})" class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                                                title="Lihat detail akun">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7z" />
                                                    <path stroke-linecap="round" stroke-width="2"
                                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                                                </svg>
                                            </button>
                                            <button type="button" onclick="openEditAccountModal({{ json_encode([
                            'id' => $acc->id,
                            'name' => $acc->name,
                            'brand' => $acc->brand ?? '',
                            'platform' => $acc->platform,
                            'link' => $acc->link ?? '',
                            'email' => $acc->email ?? '',
                            'email_recovery' => $acc->email_recovery ?? '',
                            'phone' => $acc->phone ?? '',
                            'two_factor_enabled' => (bool) $acc->two_factor_enabled,
                            'notes' => $acc->notes ?? '',
                        ]) }})" class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                                                title="Edit Akun">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button type="button"
                                                onclick="openDeleteAccountModal({{ $acc->id }}, '{{ addslashes($acc->name) }}', '{{ addslashes($acc->platform) }}')"
                                                class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                                title="Hapus Akun">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    <p class="font-medium text-gray-600">Belum ada data akun sosial media</p>
                                    <p class="text-xs text-gray-400 mt-1">Klik tombol "Tambah Akun Baru" di atas untuk
                                        menambahkan akun.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($accounts->hasPages())
            <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $accounts->links() }}
            </div>
        @endif
    </div>

    {{-- ── MODAL CREATE ACCOUNT ────────────────────────────── --}}
    <div id="modal-create-account" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            onclick="document.getElementById('modal-create-account').classList.add('hidden')"></div>
        <div
            class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg z-10 max-h-[90vh] flex flex-col overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <div>
                    <h3 class="text-base font-bold text-gray-800">Tambah Akun Baru</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Tambahkan akun media sosial dan informasi kredensialnya</p>
                </div>
                <button type="button" onclick="document.getElementById('modal-create-account').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route($accountPrefix . '.accounts.store') }}"
                class="p-6 pt-1 space-y-4 overflow-y-auto">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Platform <span
                            class="text-red-500">*</span></label>
                    <select name="platform" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="">-- Pilih Platform --</option>
                        @foreach($platformList as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="custom_platform" id="create-custom-platform"
                        placeholder="Masukkan nama platform"
                        class="hidden mt-2 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nama Akun <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: @republikweb_net"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Brand / Kategori Brand</label>
                    <input type="text" name="brand" list="brand-datalist" placeholder="Contoh: Republikweb"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    <p class="text-[11px] text-gray-400 mt-1">Label pengelompokan brand sebagai penanda akun.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Link Akun (URL Profil)</label>
                    <input type="text" name="link" placeholder="https://instagram.com/republikweb_net"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    <p class="text-[11px] text-gray-400 mt-1">Link ini dapat langsung diklik pada tabel untuk membuka profil
                        akun.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Email Akun</label>
                        <input type="email" name="email" placeholder="email@domain.com"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <p class="text-[10px] text-amber-600 mt-1">Hanya bisa dilihat oleh Admin.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Password Akun</label>
                        <input type="text" name="password" placeholder="Ketik password akun..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <p class="text-[10px] text-amber-600 mt-1">Tersimpan aman & terenkripsi.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Email Recovery</label>
                        <input type="email" name="email_recovery"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nomor HP / Telepon</label>
                        <input type="text" name="phone" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>
                <label class="inline-flex items-center gap-2 text-xs font-semibold text-gray-700">
                    <input type="checkbox" name="two_factor_enabled" value="1" class="rounded border-gray-300"> 2FA aktif
                </label>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Catatan / Keterangan</label>
                    <textarea name="notes" rows="2" placeholder="Catatan tambahan seputar akun..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none"></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-create-account').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">Batal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">Simpan
                        Akun</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── MODAL EDIT ACCOUNT ──────────────────────────────── --}}
    <div id="modal-edit-account" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            onclick="document.getElementById('modal-edit-account').classList.add('hidden')"></div>
        <div
            class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg z-10 max-h-[90vh] flex flex-col overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <div>
                    <h3 class="text-base font-bold text-gray-800">Edit Akun</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Perbarui informasi akun atau kredensial akses</p>
                </div>
                <button type="button" onclick="document.getElementById('modal-edit-account').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="form-edit-account" method="POST" action="" class="p-6 pt-1 space-y-4 overflow-y-auto">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Platform <span
                            class="text-red-500">*</span></label>
                    <select name="platform" id="edit-acc-platform" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="">-- Pilih Platform --</option>
                        @foreach($platformList as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="custom_platform" id="edit-custom-platform" placeholder="Masukkan nama platform"
                        class="hidden mt-2 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nama Akun <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" id="edit-acc-name" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Brand / Kategori Brand</label>
                    <input type="text" name="brand" id="edit-acc-brand" list="brand-datalist" placeholder="Contoh: Republikweb"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    <p class="text-[11px] text-gray-400 mt-1">Label pengelompokan brand sebagai penanda akun.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Link Akun (URL Profil)</label>
                    <input type="text" name="link" id="edit-acc-link" placeholder="https://instagram.com/nama-akun"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Email Akun</label>
                        <input type="email" name="email" id="edit-acc-email" placeholder="email@domain.com"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Ganti Password</label>
                        <input type="text" name="password" id="edit-acc-password" placeholder="Kosongkan jika tetap sama"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Email Recovery</label>
                        <input type="email" name="email_recovery" id="edit-acc-recovery"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nomor HP / Telepon</label>
                        <input type="text" name="phone" id="edit-acc-phone"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>
                <label class="inline-flex items-center gap-2 text-xs font-semibold text-gray-700">
                    <input type="checkbox" name="two_factor_enabled" id="edit-acc-2fa" value="1"
                        class="rounded border-gray-300"> 2FA aktif
                </label>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Catatan / Keterangan</label>
                    <textarea name="notes" id="edit-acc-notes" rows="2"
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

    {{-- ── MODAL DELETE ACCOUNT ────────────────────────────── --}}
    <div id="modal-delete-account" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            onclick="document.getElementById('modal-delete-account').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm z-10 p-6">
            <div class="text-center mb-5">
                <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 mx-auto flex items-center justify-center mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-800">Hapus Akun?</h3>
                <p class="text-xs text-gray-500 mt-1">
                    Apakah kamu yakin ingin menghapus akun <span id="del-acc-name"
                        class="font-semibold text-gray-800"></span>? Riwayat penugasan terkait akun ini juga akan terhapus.
                </p>
            </div>

            <form id="form-delete-account" method="POST" action="" class="flex gap-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="document.getElementById('modal-delete-account').classList.add('hidden')"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">Batal</button>
                <button type="submit"
                    class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">Ya,
                    Hapus</button>
            </form>
        </div>
    </div>

    {{-- ── MODAL ACCOUNT DETAIL ────────────────────────────── --}}
    <div id="modal-account-detail" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeAccountDetail()"></div>
        <div class="relative z-10 w-full max-w-lg rounded-xl bg-white shadow-2xl p-6">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                <div>
                    <h3 class="text-base font-bold text-gray-800">Detail Akun Sosmed</h3>
                    <p id="detail-account-name" class="text-xs text-gray-500"></p>
                </div>
                <button type="button" onclick="closeAccountDetail()" class="text-gray-400 text-xl">&times;</button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 py-4 text-sm">
                <div>
                    <p class="text-xs text-gray-400">Platform</p>
                    <p id="detail-platform" class="font-semibold text-gray-800"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Brand</p>
                    <p id="detail-brand" class="font-semibold text-indigo-600"></p>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-xs text-gray-400">Nama Akun</p>
                    <p id="detail-name" class="font-semibold text-gray-800"></p>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-xs text-gray-400">Link Akun</p>
                    <p id="detail-link" class="break-all text-primary-600"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Email</p>
                    <p id="detail-email" class="break-all text-gray-800"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Password</p>
                    <p id="detail-password" class="break-all font-mono text-gray-800"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Email Recovery</p>
                    <p id="detail-recovery" class="break-all text-gray-800"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Nomor HP</p>
                    <p id="detail-phone" class="text-gray-800"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">2FA</p>
                    <p id="detail-2fa" class="text-gray-800"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Status Penugasan</p>
                    <p id="detail-status" class="text-gray-800"></p>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-xs text-gray-400">Catatan</p>
                    <p id="detail-notes" class="whitespace-pre-wrap text-gray-800"></p>
                </div>
            </div>
        </div>
    </div>

    <datalist id="brand-datalist">
        @foreach($brands as $b)
            <option value="{{ $b }}">
        @endforeach
    </datalist>

    {{-- ── JAVASCRIPT MODAL HANDLERS ───────────────────────── --}}
    <script>
        const accountPrefix = @json($accountPrefix);

        function openApproveAccountModal(id, name, recovery, phone, twoFactor) {
            document.getElementById('form-approve-account').action = `/${accountPrefix}/accounts/${id}/verify`;
            document.getElementById('approve-account-name').textContent = name;
            document.getElementById('approve-recovery').value = recovery || '';
            document.getElementById('approve-phone').value = phone || '';
            document.getElementById('approve-2fa').checked = Boolean(twoFactor);
            document.getElementById('modal-approve-account').classList.remove('hidden');
        }

        function openRejectAccountModal(id, name) {
            document.getElementById('form-reject-account').action = `/${accountPrefix}/accounts/${id}/verify`;
            document.getElementById('modal-reject-account').classList.remove('hidden');
            document.querySelector('#form-reject-account textarea').focus();
        }

        function closeAccountDecisionModals() {
            document.getElementById('modal-approve-account').classList.add('hidden');
            document.getElementById('modal-reject-account').classList.add('hidden');
        }

        function openAccountDetail(data) {
            const fields = {
                platform: data.platform,
                brand: data.brand || '-',
                name: data.name,
                link: data.link || '-',
                email: data.email || '-',
                password: data.password || '-',
                recovery: data.email_recovery || '-',
                phone: data.phone || '-',
                '2fa': data.two_factor || 'Tidak',
                status: data.status || '-',
                notes: data.notes || '-',
            };
            Object.entries(fields).forEach(([key, value]) => {
                const element = document.getElementById(`detail-${key}`);
                if (element) element.textContent = value;
            });
            document.getElementById('modal-account-detail').classList.remove('hidden');
        }

        function closeAccountDetail() {
            document.getElementById('modal-account-detail').classList.add('hidden');
        }

        function openCreateAccountModal() {
            document.getElementById('modal-create-account').classList.remove('hidden');
        }

        document.querySelector('#modal-create-account select[name="platform"]').addEventListener('change', function () {
            toggleAdminCustomPlatform(this, 'create-custom-platform');
        });

        document.querySelector('#edit-acc-platform').addEventListener('change', function () {
            toggleAdminCustomPlatform(this, 'edit-custom-platform');
        });

        function toggleAdminCustomPlatform(select, inputId, value = '') {
            const input = document.getElementById(inputId);
            const isCustom = select.value === 'Lainnya';
            input.classList.toggle('hidden', !isCustom);
            input.required = isCustom;
            if (isCustom && value) input.value = value;
            if (!isCustom) input.value = '';
        }

        function openEditAccountModal(data) {
            const form = document.getElementById('form-edit-account');
            form.action = `/${accountPrefix}/accounts/${data.id}`;

            document.getElementById('edit-acc-name').value = data.name || '';
            document.getElementById('edit-acc-brand').value = data.brand || '';
            const standardPlatforms = Array.from(document.getElementById('edit-acc-platform').options).map(option => option.value);
            const isCustomPlatform = data.platform && !standardPlatforms.includes(data.platform);
            document.getElementById('edit-acc-platform').value = isCustomPlatform ? 'Lainnya' : (data.platform || '');
            toggleAdminCustomPlatform(document.getElementById('edit-acc-platform'), 'edit-custom-platform', isCustomPlatform ? data.platform : '');
            document.getElementById('edit-acc-link').value = data.link || '';
            document.getElementById('edit-acc-email').value = data.email || '';
            document.getElementById('edit-acc-recovery').value = data.email_recovery || '';
            document.getElementById('edit-acc-phone').value = data.phone || '';
            document.getElementById('edit-acc-2fa').checked = Boolean(data.two_factor_enabled);
            document.getElementById('edit-acc-password').value = '';
            document.getElementById('edit-acc-notes').value = data.notes || '';

            document.getElementById('modal-edit-account').classList.remove('hidden');
        }

        function openDeleteAccountModal(id, name, platform) {
            const form = document.getElementById('form-delete-account');
            form.action = `/${accountPrefix}/accounts/${id}`;
            document.getElementById('del-acc-name').textContent = `${name} (${platform})`;
            document.getElementById('modal-delete-account').classList.remove('hidden');
        }
    </script>

@endif

{{-- ═══════════════════════════════════════════════════════════════
TAB: AKUN YANG DITOLAK
═══════════════════════════════════════════════════════════════ --}}
@if($tab === 'rejected')
    <div class="bg-white rounded-xl border border-rose-200 shadow-sm overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-rose-100 bg-rose-50/50">
            <h2 class="text-base font-bold text-gray-800">Pengajuan Akun yang Ditolak</h2>
            <p class="text-xs text-gray-500 mt-0.5">Akun-akun ini telah ditolak dan tidak muncul di daftar manajemen maupun
                kelola sosmed.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs sm:text-sm">
                <thead>
                    <tr
                        class="bg-rose-50 border-b border-rose-100 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        <th class="px-4 py-3 text-left">Platform</th>
                        <th class="px-4 py-3 text-left">Nama / Email</th>
                        <th class="px-4 py-3 text-left">Diajukan Oleh</th>
                        <th class="px-4 py-3 text-left">Alasan Penolakan</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rejectedAccounts as $rej)
                        <tr class="hover:bg-rose-50/30 transition">
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex px-2 py-0.5 rounded-md border text-[11px] font-semibold {{ $rej->platform_color }}">
                                    {{ $rej->platform }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800">{{ $rej->name ?: '—' }}</p>
                                <p class="text-gray-400 text-[11px]">{{ $rej->email ?: '—' }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-700">{{ $rej->creator?->name ?? '—' }}</p>
                                <p class="text-gray-400 text-[11px]">{{ $rej->creator?->role_label ?? '' }}</p>
                            </td>
                            <td class="px-4 py-3 text-rose-600 text-xs">{{ $rej->rejection_note ?: '—' }}</td>
                            <td class="px-4 py-3 text-gray-400 whitespace-nowrap">
                                {{ $rej->updated_at->translatedFormat('d M Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-400">Tidak ada pengajuan yang
                                ditolak.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($rejectedAccounts->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $rejectedAccounts->links() }}
            </div>
        @endif
    </div>
@endif

@endsection