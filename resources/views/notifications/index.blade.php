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
        @endphp
        <div class="bg-white rounded-xl border {{ $isUnread ? 'border-primary-200 bg-primary-50/30' : 'border-gray-200' }}
                    p-4 flex items-start gap-4 transition">

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
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        @if($isCustom)
                            <p class="text-sm font-semibold text-gray-800">
                                {{ $data['title'] ?? 'Pengumuman' }}
                            </p>
                            <p class="text-sm text-gray-600 mt-0.5">
                                {{ $data['message'] ?? '' }}
                            </p>
                            <div class="flex items-center gap-3 mt-2">
                                <span class="text-xs text-gray-400">
                                    {{ $notification->created_at->locale('id')->diffForHumans() }}
                                </span>
                                @if(!empty($data['sender_name']))
                                    <span class="text-xs text-gray-400">•</span>
                                    <span class="text-xs text-gray-400">Dari: {{ $data['sender_name'] }}</span>
                                @endif
                            </div>
                        @else
                            <p class="text-sm font-semibold text-gray-800">
                                {{ $data['task_title'] ?? 'Tugas Baru' }}
                            </p>
                            <p class="text-sm text-gray-600 mt-0.5">
                                {{ $data['message'] ?? '' }}
                            </p>
                            <div class="flex items-center gap-3 mt-2">
                                <span class="text-xs text-gray-400">
                                    {{ $notification->created_at->locale('id')->diffForHumans() }}
                                </span>
                                @if(isset($data['task_date']))
                                    <span class="text-xs text-gray-400">•</span>
                                    <span class="text-xs text-gray-400">
                                        Tanggal tugas:
                                        {{ \Carbon\Carbon::parse($data['task_date'])->locale('id')->translatedFormat('d M Y') }}
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Badge + Aksi --}}
                    <div class="flex items-center gap-2 flex-shrink-0">
                        @if($isUnread)
                            <span class="w-2 h-2 rounded-full bg-primary-600 flex-shrink-0"></span>
                            <form action="{{ route('notifications.read', $notification->id) }}"
                                  method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="text-xs text-primary-600 hover:text-primary-700
                                               font-medium whitespace-nowrap">
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

{{-- Pagination --}}
@if($notifications->hasPages())
    <div class="mt-6 bg-white rounded-xl border border-gray-200 px-6 py-3.5">
        {{ $notifications->links() }}
    </div>
@endif

@endsection