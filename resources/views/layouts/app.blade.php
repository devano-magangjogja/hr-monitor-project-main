<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HR-DWMS') — HR-DWMS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F1F5F9] font-sans antialiased h-full">

<div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

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
    <aside class="fixed inset-y-0 left-0 z-40 w-[240px] sm:w-[260px] bg-[#1C2434] flex flex-col
                  transform transition-transform duration-200 ease-in-out
                  -translate-x-full lg:translate-x-0 lg:static lg:inset-auto lg:z-auto"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        {{-- Logo --}}
        <div class="flex items-center justify-between px-3 sm:px-6 py-3 sm:py-5 border-b border-white/10 flex-shrink-0 gap-2">
            <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                @php
                    $useStorageLogo = !empty($appLogo) && \Illuminate\Support\Facades\Storage::disk('public')->exists($appLogo);
                @endphp
                @if($useStorageLogo)
                    <img src="{{ asset('storage/' . $appLogo) }}" alt="{{ $appName }}"
                         class="w-8 sm:w-9 h-8 sm:h-9 object-contain flex-shrink-0">
                @else
                    <img src="{{ asset('images/seveninc_logo.png') }}" alt="{{ $appName }}"
                         class="w-8 sm:w-9 h-8 sm:h-9 object-contain flex-shrink-0">
                @endif
                <span class="text-white font-bold text-lg sm:text-2xl tracking-tight truncate">
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
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 sm:px-4 py-3 sm:py-5 space-y-1 overflow-y-auto">
            @yield('sidebar')
        </nav>

        {{-- User Info + Logout --}}
        <div class="border-t border-white/10 p-3 sm:p-4 flex-shrink-0">
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
                        {{ match(Auth::user()->role) {
                            'admin'        => 'Administrator',
                            'hr_staff'     => 'HR Staff',
                            'hr_assistant' => 'HR Assistant',
                            'cs'           => 'Customer Service',
                            'ob'           => 'Office Boy',
                            default        => strtoupper(Auth::user()->role),
                        } }}
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

        {{-- Topbar --}}
        <header class="bg-white border-b border-gray-200 px-3 sm:px-4 md:px-6 py-2.5 sm:py-3 md:py-3.5 flex items-center
                       justify-between flex-shrink-0 shadow-sm gap-2 sm:gap-3">

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
                    <svg class="w-4 sm:w-5 h-4 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @php
                        try { $unread = Auth::user()->unreadNotifications->count(); }
                        catch (\Exception $e) { $unread = 0; }
                    @endphp
                    @if($unread > 0)
                        <span class="absolute top-0 right-0 w-4 h-4 bg-red-500 text-white
                                    text-[10px] rounded-full flex items-center justify-center font-medium">
                            {{ $unread > 9 ? '9+' : $unread }}
                        </span>
                    @endif
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
                            {{ match(Auth::user()->role) {
                                'admin'        => 'Administrator',
                                'hr_staff'     => 'HR Staff',
                                'hr_assistant' => 'HR Assistant',
                                default        => Auth::user()->role,
                            } }}
                        </p>
                    </div>
                </a>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto p-3 sm:p-4 md:p-6">
            @yield('content')
        </main>
    </div>

    @include('components.task-detail-modal')
</div>

{{-- Notification Popup — di luar semua container overflow --}}
@include('components.notification-popup')

{{-- Floating Chat Button --}}
@include('components.floating-chat')

{{-- Toast — di luar semua container overflow agar position:fixed tidak ter-clip --}}
@include('components.toast')

@stack('scripts')
</body>
</html>
