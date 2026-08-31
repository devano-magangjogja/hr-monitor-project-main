{{-- Separator --}}
<div class="my-2 border-t border-white/10"></div>

{{-- Dashboard --}}
<a href="{{ route('sosmed.dashboard') }}"
   class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all
          {{ request()->routeIs('sosmed.dashboard') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
    </svg>
    Dashboard
</a>

{{-- Divider --}}
<div class="pt-4 pb-2">
    <p class="px-4 text-[11px] font-semibold text-slate-500 uppercase tracking-widest">AKUN & KONTEN SOSMED</p>
</div>

{{-- Tugas Sosmed Utama --}}
<a href="{{ route('sosmed.sosmed.index') }}"
   class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all
          {{ request()->routeIs('sosmed.sosmed.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M8 12H8.01M12 12H12.01M16 12H16.01M21.0039 12C21.0039 16.9706 16.9745 21 12.0039 21C9.9675 21 3.00463 21 3.00463 21C3.00463 21 4.56382 17.2561 3.93982 16.0008C3.34076 14.7956 3.00391 13.4372 3.00391 12C3.00391 7.02944 7.03334 3 12.0039 3C16.9745 3 21.0039 7.02944 21.0039 12Z" />
    </svg>
    Tugas & Akun Sosmed
</a>

{{-- Divider --}}
<div class="pt-4 pb-2">
    <p class="px-4 text-[11px] font-semibold text-slate-500 uppercase tracking-widest">TASK</p>
</div>

{{-- Tugas Hari Ini (collapsible) --}}
@php $taskActive = request()->routeIs('sosmed.tasks.*'); @endphp
<div x-data="{ open: {{ $taskActive ? 'true' : 'false' }} }">
    <button @click="open = !open"
            class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all
                   {{ $taskActive ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
        </svg>
        <span class="flex-1 text-left">Tugas Hari Ini</span>
        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    <div x-show="open" x-transition class="mt-1 ml-4 pl-4 border-l border-white/10 space-y-1">
        <a href="{{ route('sosmed.tasks.all') }}"
           class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all
                  {{ request()->routeIs('sosmed.tasks.all') ? 'text-white bg-white/10' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
            </svg>
            Semua Tugas
        </a>
        <a href="{{ route('sosmed.tasks.daily') }}"
           class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all
                  {{ request()->routeIs('sosmed.tasks.daily') ? 'text-white bg-white/10' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Tugas Harian
        </a>
        <a href="{{ route('sosmed.tasks.assigned') }}"
           class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all
                  {{ request()->routeIs('sosmed.tasks.assigned') ? 'text-white bg-white/10' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Tugas dari Admin / PM
        </a>
        <a href="{{ route('sosmed.tasks.index') }}"
           class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all
                  {{ request()->routeIs('sosmed.tasks.index') ? 'text-white bg-white/10' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Tugas Mandiri
        </a>
    </div>
</div>

{{-- Riwayat Tugas --}}
<a href="{{ route('sosmed.tasks.history') }}"
   class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all
          {{ request()->routeIs('sosmed.tasks.history') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    Riwayat Tugas
</a>
