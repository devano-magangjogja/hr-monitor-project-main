{{-- Dashboard --}}
<a href="{{ route('assistant.dashboard') }}"
    class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all
          {{ request()->routeIs('assistant.dashboard') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
    </svg>
    Dashboard
</a>

{{-- Tugas Hari Ini (collapsible) --}}
@php
    $taskActive = request()->routeIs('assistant.tasks.*');
@endphp
<div x-data="{ open: {{ $taskActive ? 'true' : 'false' }} }">
    <button @click="open = !open" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all
                   {{ $taskActive ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
        </svg>
        <span class="flex-1 text-left">Tugas Hari Ini</span>
        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div x-show="open" x-transition class="mt-1 ml-4 pl-4 border-l border-white/10 space-y-1">

        {{-- Semua Tugas --}}
        <a href="{{ route('assistant.tasks.all') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all
                  {{ request()->routeIs('assistant.tasks.all') ? 'text-white bg-white/10' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 10h16M4 14h16M4 18h16" />
            </svg>
            Semua Tugas
        </a>

        {{-- Tugas Rutin --}}
        <a href="{{ route('assistant.tasks.routine') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all
                  {{ request()->routeIs('assistant.tasks.routine') ? 'text-white bg-white/10' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Tugas Rutin
        </a>

        {{-- Tugas dari Admin & Staff --}}
        <a href="{{ route('assistant.tasks.assigned') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all
                  {{ request()->routeIs('assistant.tasks.assigned') ? 'text-white bg-white/10' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Tugas dari Atasan
        </a>

        {{-- Tugas Mandiri --}}
        <a href="{{ route('assistant.tasks.index') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all
                  {{ request()->routeIs('assistant.tasks.index') ? 'text-white bg-white/10' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Tugas Mandiri
        </a>
    </div>
</div>

{{-- Riwayat Tugas --}}
<a href="{{ route('assistant.tasks.history') }}"
    class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all
          {{ request()->routeIs('assistant.tasks.history') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    Riwayat Tugas
</a>

{{-- Divider: Verifikasi Sosmed --}}
<div class="pt-4 pb-2">
    <p class="px-4 text-[11px] font-semibold text-slate-500 uppercase tracking-widest">Verifikasi Sosmed</p>
</div>

{{-- Approval Tugas Sosmed --}}
@php
    $sosmedPending = \App\Models\SosmedTask::where('status', 'done_by_staff')->count();
@endphp
<a href="{{ route('assistant.sosmed.index') }}"
    class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all
          {{ request()->routeIs('assistant.sosmed.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
    </svg>
    <span class="flex-1 text-left">Approval Sosmed</span>
    @if($sosmedPending > 0)
        <span class="px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-orange-500 text-white">
            {{ $sosmedPending > 9 ? '9+' : $sosmedPending }}
        </span>
    @endif
</a>

{{-- Divider --}}
<div class="pt-4 pb-2">
    <p class="px-4 text-[11px] font-semibold text-slate-500 uppercase tracking-widest">Presensi Pemagang</p>
</div>

{{-- Presensi --}}
<a href="{{ route('assistant.presensi.index') }}"
    class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all
          {{ request()->routeIs('assistant.presensi.index') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
    </svg>
    Presensi
</a>

{{-- Laporan Presensi --}}
<a href="{{ route('assistant.presensi.laporan') }}"
    class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all
          {{ request()->routeIs('assistant.presensi.laporan') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
    </svg>
    Laporan Presensi
</a>