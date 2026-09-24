<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $appName ?? 'Republikweb.net') — {{ $appName ?? 'Republikweb.net' }}</title>
    
    {{-- Favicon/Tab Icon --}}
    @php
        $faviconUrl = (!empty($appLogo) && \Illuminate\Support\Facades\Storage::disk('public')->exists($appLogo))
            ? asset('storage/' . $appLogo)
            : asset('images/logo_square.jpg');
    @endphp
    <link rel="icon" type="image/jpeg" href="{{ $faviconUrl }}">
    <link rel="shortcut icon" type="image/jpeg" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
    
    {{-- Print Styles --}}
    <style>
        @media print {
            body {
                background: white !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            main {
                padding: 20px !important;
            }
            table {
                font-size: 11px !important;
            }
            /* Prevent page breaks inside tables/sections */
            table tbody tr {
                page-break-inside: avoid;
            }
            .page-break {
                page-break-after: always;
            }
        }

        /* Sembunyikan elemen Alpine sebelum Alpine siap (cegah flash submenu/flyout) */
        [x-cloak] { display: none !important; }

        /* ── Sidebar mini (collapse jadi rail ikon, desktop only) ── */
        @media (min-width: 1024px) {
            aside.app-sidebar {
                transition-property: transform, width;
            }
            aside.app-sidebar.sidebar-mini {
                width: 76px;
                overflow: visible;
                z-index: 45;
            }
            aside.app-sidebar.sidebar-mini > div,
            aside.app-sidebar.sidebar-mini > nav {
                width: 76px;
            }
            aside.app-sidebar.sidebar-mini,
            aside.app-sidebar.sidebar-mini * {
                font-size: 0;
            }
            aside.app-sidebar.sidebar-mini > div:first-child {
                justify-content: center;
                gap: 0;
                padding-left: 0;
                padding-right: 0;
            }
            aside.app-sidebar.sidebar-mini > div:first-child > div {
                gap: 0;
            }
            aside.app-sidebar.sidebar-mini > div:first-child span {
                display: none;
            }
            aside.app-sidebar.sidebar-mini nav {
                padding-left: 0;
                padding-right: 0;
                overflow-x: hidden;
                scrollbar-width: none;
            }
            aside.app-sidebar.sidebar-mini nav::-webkit-scrollbar {
                display: none;
            }
            aside.app-sidebar.sidebar-mini nav a,
            aside.app-sidebar.sidebar-mini nav button {
                width: 48px;
                margin-left: auto;
                margin-right: auto;
                justify-content: center;
                gap: 0;
                padding: 10px 0;
            }
            aside.app-sidebar.sidebar-mini nav a > svg ~ svg,
            aside.app-sidebar.sidebar-mini nav button > svg ~ svg {
                display: none;
            }
            aside.app-sidebar.sidebar-mini nav :is(a, button) > span {
                display: none;
            }
            /* Submenu jadi flyout melayang di samping rail saat mini */
            aside.app-sidebar.sidebar-mini nav div[x-show] {
                position: fixed;
                left: 84px;
                top: var(--flyout-top, 90px);
                width: 220px;
                margin: 0;
                padding: 8px;
                border-left: 0;
                background: #222B3D;
                border: 1px solid rgba(255, 255, 255, 0.12);
                border-radius: 12px;
                box-shadow: 0 12px 32px rgba(0, 0, 0, 0.4);
                z-index: 60;
                font-size: 0.875rem;
                line-height: 1.25rem;
                max-height: calc(100vh - var(--flyout-top, 90px) - 24px);
                overflow-y: auto;
            }
            aside.app-sidebar.sidebar-mini nav div[x-show] a {
                width: auto;
                margin: 0;
                padding: 8px 12px;
                justify-content: flex-start;
                gap: 8px;
                font-size: 0.875rem;
                line-height: 1.25rem;
            }
            aside.app-sidebar.sidebar-mini nav div[x-show] a > span {
                display: inline;
            }
            aside.app-sidebar.sidebar-mini nav div[x-show] * {
                font-size: 0.875rem;
                line-height: 1.25rem;
            }
            aside.app-sidebar.sidebar-mini nav > div {
                padding-top: 10px;
                padding-bottom: 10px;
            }
            aside.app-sidebar.sidebar-mini > div:last-child > div {
                justify-content: center;
                gap: 0;
            }
            aside.app-sidebar.sidebar-mini > div:last-child .flex-1 {
                display: none;
            }
            aside.app-sidebar.sidebar-mini > div:last-child form {
                display: none;
            }
        }
    </style>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F1F5F9] font-sans antialiased h-full">

<div class="flex h-screen overflow-hidden"
     x-data="{
         sidebarOpen: false,
         sidebarCollapsed: localStorage.getItem('sidebar-collapsed') === '1',
         toggleSidebarCollapsed() {
             this.sidebarCollapsed = !this.sidebarCollapsed;
             localStorage.setItem('sidebar-collapsed', this.sidebarCollapsed ? '1' : '0');
             this.$nextTick(() => this.positionFlyout());
         },
         positionFlyout() {
             if (!this.sidebarCollapsed) return;
             const asideEl = this.$el.querySelector('aside.app-sidebar');
             if (!asideEl) return;
             const open = Array.from(asideEl.querySelectorAll('nav div[x-show]'))
                 .find(d => getComputedStyle(d).display !== 'none');
             if (!open) return;
             const btn = open.parentElement.querySelector('button');
             if (btn) asideEl.style.setProperty('--flyout-top',
                 (btn.getBoundingClientRect().top - asideEl.getBoundingClientRect().top) + 'px');
         }
     }"
     x-init="$nextTick(() => positionFlyout()); window.addEventListener('alpine:initialized', () => $nextTick(() => positionFlyout())); window.addEventListener('pageshow', () => $nextTick(() => positionFlyout()))">

    {{-- ── OVERLAY mobile (tap to close) ──────────────── --}}
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 z-30 bg-black/50 lg:hidden"
         style="display:none"></div>

    {{-- ── SIDEBAR ──────────────────────────────────────── --}}
    <aside class="app-sidebar fixed inset-y-0 left-0 z-40 w-[240px] sm:w-[260px] bg-[#1C2434] flex flex-col
                  transform transition-transform duration-200 ease-in-out
                  -translate-x-full lg:translate-x-0 lg:static lg:inset-auto lg:z-auto print:hidden"
           :class="(sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0')
                   + (sidebarCollapsed ? ' sidebar-mini' : '')">

        {{-- Logo --}}
        <div class="relative flex items-center justify-between px-3 sm:px-6 py-3 sm:py-4 border-b border-white/10 flex-shrink-0 gap-2 h-16 sm:h-[72px] w-[240px] sm:w-[260px]">
            <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                @php
                    $sidebarLogoUrl = (!empty($appLogo) && \Illuminate\Support\Facades\Storage::disk('public')->exists($appLogo))
                        ? asset('storage/' . $appLogo)
                        : asset('images/logo_square.jpg');
                @endphp
                <img src="{{ $sidebarLogoUrl }}" alt="{{ $appName }}" onerror="this.style.display='none'"
                     class="w-9 h-9 sm:w-10 sm:h-10 object-contain flex-shrink-0 rounded-md">
                <span class="text-white font-bold text-lg sm:text-xl tracking-tight truncate">
                    {{ $appName }}
                </span>
            </div>
            {{-- Tutup sidebar di mobile --}}
            <button @click="sidebarOpen = false"
                    class="lg:hidden text-slate-400 hover:text-white p-1.5 rounded-lg flex-shrink-0 hover:bg-white/10 transition min-w-[36px] min-h-[36px] flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            {{-- Toggle buka/tutup sidebar (desktop only, nempel di tepi kanan) --}}
            <button @click="toggleSidebarCollapsed()" type="button"
                    class="hidden lg:flex absolute top-1/2 -translate-y-1/2 z-50 w-6 h-6 rounded-full
                           bg-white border border-gray-300 text-gray-500 hover:text-gray-800
                           shadow-sm items-center justify-center transition"
                    style="right: -12px;"
                    :title="sidebarCollapsed ? 'Buka sidebar' : 'Tutup sidebar'">
                <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="sidebarCollapsed ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 sm:px-4 py-3 sm:py-5 space-y-1 overflow-y-auto w-[240px] sm:w-[260px]"
             @click="if (sidebarCollapsed) {
                 const asideEl = $el.closest('aside');
                 const closeFlyout = (d) => {
                     const w = d.closest('div[x-data]');
                     const st = w && w._x_dataStack && w._x_dataStack[0];
                     if (st && 'open' in st) { st.open = false; } else { d.style.display = 'none'; }
                 };
                 const btn = $event.target.closest('button');
                 const link = $event.target.closest('a');
                 const wrap = btn ? btn.closest('div[x-data]') : null;
                 const own = wrap ? wrap.querySelector('div[x-show]') : null;
                 asideEl.querySelectorAll('nav div[x-show]').forEach(d => {
                     if (link) closeFlyout(d);
                     else if (d !== own && getComputedStyle(d).display !== 'none') closeFlyout(d);
                 });
                 if (btn) asideEl.style.setProperty('--flyout-top',
                     (btn.getBoundingClientRect().top - asideEl.getBoundingClientRect().top) + 'px');
             }">
            @yield('sidebar')
        </nav>

        {{-- User Info + Logout --}}
        <div class="border-t border-white/10 p-3 sm:p-4 flex-shrink-0 w-[240px] sm:w-[260px]">
            <div class="flex items-center gap-2 sm:gap-3">
                <a href="{{ route('profile.show') }}"
                   class="w-8 sm:w-9 h-8 sm:h-9 rounded-full overflow-hidden flex-shrink-0 ring-2 ring-transparent
                          hover:ring-primary-400 transition"
                   title="Lihat Profil">
                    @if(Auth::user()->image)
                        <img src="{{ asset('storage/' . Auth::user()->image) }}"
                             alt="{{ Auth::user()->name }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-primary-600 flex items-center justify-center">
                            <span class="text-xs sm:text-sm font-semibold text-white">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                </a>
                <div class="flex-1 min-w-0">
                    <a href="{{ route('profile.show') }}"
                       class="block text-xs sm:text-sm font-semibold text-white truncate hover:text-primary-300 transition">
                        {{ Auth::user()->name }}
                    </a>
                    <p class="text-[11px] sm:text-xs text-slate-400 truncate">
                        {{ Auth::user()->role === 'admin' ? 'Admin' : Auth::user()->role_label }}
                    </p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Logout"
                            class="text-slate-400 hover:text-red-400 transition p-1.5 rounded-lg
                                   hover:bg-white/5 min-w-[36px] min-h-[36px] flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 sm:w-5 h-4 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ── MAIN AREA ─────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col overflow-hidden min-w-0">

        {{-- Print Header (Logo & Company Name - Visible Only When Printing) --}}
        <div class="hidden print:block print:mb-6 print:pb-4 print:border-b-2 print:border-gray-400">
            <div class="flex items-center gap-4 print:gap-4 print:mb-3">
                <img src="{{ $sidebarLogoUrl ?? asset('images/logo_square.jpg') }}" alt="{{ $appName }}" 
                     class="print:w-12 print:h-12 object-contain rounded-md">
                <div class="print:flex-1">
                    <h1 class="print:text-xl print:font-bold print:text-gray-800 print:m-0">{{ strtoupper($appName) }}</h1>
                    <p class="print:text-xs print:text-gray-600 print:m-0 print:mt-1">Laporan Sistem Monitoring HR & Tugas</p>
                </div>
            </div>
        </div>

        {{-- Topbar --}}
        <header class="bg-white border-b border-gray-200 px-3 sm:px-4 md:px-6 py-3 sm:py-4 flex items-center
                       justify-between flex-shrink-0 shadow-sm gap-2 sm:gap-3 print:hidden h-16 sm:h-[72px]">

            <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                {{-- Hamburger (mobile only) --}}
                <button @click="sidebarOpen = true"
                        class="lg:hidden flex-shrink-0 p-1.5 text-gray-500 hover:text-gray-700
                               hover:bg-gray-100 rounded-lg transition min-w-[40px] min-h-[40px]
                               flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div class="min-w-0">
                    <h1 class="text-sm sm:text-base md:text-lg font-semibold text-gray-800 truncate">
                        @yield('page-title', 'Dashboard')
                    </h1>
                    <p class="text-xs text-gray-500 truncate hidden sm:block">
                        @yield('page-subtitle', '')
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-1.5 sm:gap-2 md:gap-3 flex-shrink-0">
                {{-- Notifikasi --}}
                <a href="{{ route('notifications.index') }}"
                   class="relative p-1.5 text-gray-500 hover:text-primary-600 hover:bg-primary-50
                          rounded-lg transition min-w-[40px] min-h-[40px] flex items-center justify-center">
                    <svg id="nav-notif-bell-icon" class="w-4 sm:w-5 h-4 sm:h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @php
                        try { $unread = Auth::user()->unreadNotifications->count(); }
                        catch (\Exception $e) { $unread = 0; }
                    @endphp
                    <span id="nav-notif-badge" class="{{ $unread > 0 ? '' : 'hidden' }} absolute top-0 right-0 w-4 h-4 bg-red-500 text-white
                                text-[10px] rounded-full flex items-center justify-center font-medium transition-all">
                        {{ $unread > 9 ? '9+' : ($unread > 0 ? $unread : '') }}
                    </span>
                </a>

                {{-- Tanggal (desktop only) --}}
                <div class="hidden md:block text-xs text-gray-500 bg-gray-50 px-2 sm:px-3 py-1.5 sm:py-2
                            rounded-lg border border-gray-200 leading-tight text-center whitespace-nowrap">
                    {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('D, d M Y') }}
                </div>

                {{-- Divider (desktop only) --}}
                <div class="hidden md:block w-px h-5 sm:h-6 bg-gray-200"></div>

                {{-- Avatar → Profil --}}
                <a href="{{ route('profile.show') }}"
                   title="Profil Saya"
                   class="flex items-center gap-1.5 sm:gap-2 hover:opacity-80 transition">
                    <div class="w-7 sm:w-8 h-7 sm:h-8 rounded-full overflow-hidden ring-2 ring-gray-200 flex-shrink-0">
                        @if(Auth::user()->image)
                            <img src="{{ asset('storage/' . Auth::user()->image) }}"
                                 alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-primary-600 flex items-center justify-center">
                                <span class="text-xs font-bold text-white">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="hidden sm:block leading-tight min-w-0">
                        <p class="text-xs sm:text-sm font-semibold text-gray-700 truncate max-w-[100px] md:max-w-[120px]">
                            {{ Auth::user()->name }}
                        </p>
                        <p class="text-xs text-gray-400 truncate">
                            {{ Auth::user()->role_label }}
                        </p>
                    </div>
                </a>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto p-3 sm:p-4 md:p-6 pb-24 sm:pb-28 print:p-0 print:m-0 print:overflow-visible">
            @yield('content')
        </main>
    </div>

    @include('components.task-detail-modal')
</div>

{{-- Notification Popup (Tengah Layar & Real-time Live) — di luar semua container overflow --}}
@include('components.notification-popup')

{{-- Floating Chat Button --}}
@include('components.floating-chat')

{{-- Toast — di luar semua container overflow agar position:fixed tidak ter-clip --}}
@include('components.toast')

@stack('scripts')
</body>
</html>
