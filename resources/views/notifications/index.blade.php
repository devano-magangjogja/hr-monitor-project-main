@extends('layouts.app')

@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')
@section('page-subtitle', 'Pemberitahuan tugas masuk')

@section('sidebar')
    @if(Auth::user()->isAdmin())
        @include('components.sidebar-admin')
    @elseif(Auth::user()->isHrStaff())
        @include('components.sidebar-staff')
    @elseif(Auth::user()->isHrAssistant())
        @include('components.sidebar-assistant')
    @elseif(Auth::user()->isCs())
        @include('components.sidebar-cs')
    @elseif(Auth::user()->isOb())
        @include('components.sidebar-ob')
    @elseif(Auth::user()->isProgrammer())
        @include('components.sidebar-programmer')
    @elseif(Auth::user()->isDg())
        @include('components.sidebar-dg')
    @elseif(Auth::user()->isVg())
        @include('components.sidebar-vg')
    @elseif(Auth::user()->isPm())
        @include('components.sidebar-pm')
    @else
        @include('components.sidebar-member')
    @endif
@endsection

@section('content')

{{-- Header + Tombol Mark All --}}
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">
        Total
        <span class="font-semibold text-gray-700">{{ $notifications->total() }}</span>
        notifikasi,
        <span class="font-semibold text-red-600">
            {{ Auth::user()->unreadNotifications->count() }}
        </span>
        belum dibaca
    </p>
    @if(Auth::user()->unreadNotifications->count() > 0)
        <form action="{{ route('notifications.read.all') }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit"
                    class="flex items-center gap-2 px-4 py-2 border border-gray-300
                           hover:bg-gray-50 text-gray-600 text-sm font-medium rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 13l4 4L19 7"/>
                </svg>
                Tandai Semua Dibaca
            </button>
        </form>
    @endif
</div>

{{-- Daftar Notifikasi --}}
<div class="space-y-3">
    @forelse($notifications as $notification)
        @php
            $isUnread = is_null($notification->read_at);
            $data     = $notification->data;
            $isCustom = ($data['type'] ?? null) === 'custom';
            $title    = $isCustom ? ($data['title'] ?? 'Pengumuman') : ($data['task_title'] ?? 'Tugas Baru');
            $message  = $data['message'] ?? '';
            $sender   = $data['sender_name'] ?? null;
            $taskDate = $data['task_date'] ?? null;
        @endphp
        <div class="bg-white rounded-xl border {{ $isUnread ? 'border-primary-200 bg-primary-50/30' : 'border-gray-200' }}
                    p-4 flex items-start gap-4 transition overflow-hidden">

            {{-- Icon --}}
            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                        {{ $isUnread ? 'bg-primary-100' : 'bg-gray-100' }}">
                @if($isCustom)
                    <svg class="w-5 h-5 {{ $isUnread ? 'text-primary-600' : 'text-gray-400' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                @else
                    <svg class="w-5 h-5 {{ $isUnread ? 'text-primary-600' : 'text-gray-400' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                @endif
            </div>

            {{-- Konten --}}
            <div class="flex-1 min-w-0 overflow-hidden">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0 flex-1 overflow-hidden">
                        <p class="text-sm font-semibold text-gray-800 truncate">
                            {{ $title }}
                        </p>
                        <p class="text-sm text-gray-600 mt-0.5 line-clamp-2 break-all">
                            {{ $message }}
                        </p>

                        <div class="flex items-center gap-3 mt-2 flex-wrap">
                            <span class="text-xs text-gray-400 whitespace-nowrap">
                                {{ $notification->created_at->locale('id')->diffForHumans() }}
                            </span>

                            @if($isCustom && !empty($sender))
                                <span class="text-xs text-gray-400">•</span>
                                <span class="text-xs text-gray-400 truncate">Dari: {{ $sender }}</span>
                            @endif

                            @if(!$isCustom && $taskDate)
                                <span class="text-xs text-gray-400">•</span>
                                <span class="text-xs text-gray-400 whitespace-nowrap">
                                    Tanggal tugas:
                                    {{ \Carbon\Carbon::parse($taskDate)->locale('id')->translatedFormat('d M Y') }}
                                </span>
                            @endif
                        </div>

                        {{-- Tombol Lihat Detail --}}
                        <button type="button"
                                onclick="openNotifDetail(
                                    @js($title),
                                    @js($message),
                                    @js($notification->created_at->locale('id')->diffForHumans()),
                                    @js($isCustom ? ($sender ? 'Dari: '.$sender : null) : ($taskDate ? 'Tanggal tugas: '.\Carbon\Carbon::parse($taskDate)->locale('id')->translatedFormat('d M Y') : null))
                                )"
                                class="mt-2 text-xs text-primary-600 hover:text-primary-700 font-medium">
                            Lihat selengkapnya
                        </button>
                    </div>

                    {{-- Badge + Aksi --}}
                    <div class="flex items-center gap-2 flex-shrink-0">
                        @if($isUnread)
                            <span class="w-2 h-2 rounded-full bg-primary-600 flex-shrink-0"></span>
                            <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="text-xs text-primary-600 hover:text-primary-700 font-medium whitespace-nowrap">
                                    Tandai Dibaca
                                </button>
                            </form>
                        @else
                            <span class="text-xs text-gray-400 italic">Dibaca</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-xl border border-gray-200 px-6 py-16 text-center">
            <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-600">Belum ada notifikasi</p>
            <p class="text-xs text-gray-400 mt-1">Notifikasi akan muncul saat kamu mendapat tugas baru</p>
        </div>
    @endforelse
</div>

{{-- Modal Detail Notifikasi --}}
<div id="notif-detail-modal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-auto overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-sm font-bold text-gray-800">Detail Notifikasi</h3>
            <button type="button" onclick="closeNotifDetail()"
                    class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="px-5 py-5 space-y-4">
            <div>
                <p class="text-xs text-gray-400 mb-1">Judul</p>
                <p id="notif-detail-title" class="text-sm font-semibold text-gray-800 break-words"></p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Pesan</p>
                <p id="notif-detail-message" class="text-sm text-gray-700 break-words whitespace-pre-wrap leading-relaxed"></p>
            </div>
            <div class="flex items-center gap-3 text-xs text-gray-400 pt-2 border-t border-gray-100">
                <span id="notif-detail-time"></span>
                <span id="notif-detail-meta" class="hidden"></span>
            </div>
        </div>

        <div class="px-5 py-3.5 border-t border-gray-100 bg-gray-50/50 flex justify-end">
            <button type="button" onclick="closeNotifDetail()"
                    class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- Pagination --}}
@if($notifications->hasPages())
    <div class="mt-6 bg-white rounded-xl border border-gray-200 px-6 py-3.5">
        {{ $notifications->links() }}
    </div>
@endif

<script>
    function openNotifDetail(title, message, time, meta) {
        document.getElementById('notif-detail-title').textContent = title || '-';
        document.getElementById('notif-detail-message').textContent = message || '-';
        document.getElementById('notif-detail-time').textContent = time || '';

        var metaEl = document.getElementById('notif-detail-meta');
        if (meta) {
            metaEl.textContent = '• ' + meta;
            metaEl.classList.remove('hidden');
        } else {
            metaEl.classList.add('hidden');
        }

        document.getElementById('notif-detail-modal').classList.remove('hidden');
    }

    function closeNotifDetail() {
        document.getElementById('notif-detail-modal').classList.add('hidden');
    }

    // Tutup modal jika klik area gelap
    document.getElementById('notif-detail-modal')?.addEventListener('click', function (e) {
        if (e.target === this) closeNotifDetail();
    });
</script>

@endsection

