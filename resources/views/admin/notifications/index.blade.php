@extends('layouts.app')

@section('title', 'Kirim Notifikasi')
@section('page-title', 'Kirim Notifikasi')
@section('page-subtitle', 'Buat dan kirimkan notifikasi ke seluruh anggota tim')

@section('sidebar')
    @include('components.sidebar-admin')
@endsection

@section('content')

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- ── Form Kirim Notifikasi ─────────────────────── --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-sm font-semibold text-gray-800 mb-5">Buat Notifikasi Baru</h2>

                <form action="{{ route('admin.notifications.store') }}" method="POST" class="space-y-4" x-data="{
                          recipients: 'all',
                          users: {{ $users->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'role' => $u->role])->values()->toJson() }},
                          selected: []
                      }">
                    @csrf

                    {{-- Judul --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Judul Notifikasi</label>
                        <input type="text" name="title" value="{{ old('title') }}" required maxlength="100"
                            placeholder="Contoh: Pengumuman Rapat Tim" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm
                                      focus:outline-none focus:ring-2 focus:ring-primary-500
                                      @error('title') border-red-400 @enderror">
                        @error('title')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Pesan --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Isi Pesan</label>
                        <textarea name="message" required maxlength="500" rows="4"
                            placeholder="Tulis isi notifikasi di sini..." class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm resize-none
                                         focus:outline-none focus:ring-2 focus:ring-primary-500
                                         @error('message') border-red-400 @enderror">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Penerima --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Kirim Ke</label>
                        <select name="recipients" x-model="recipients" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm
                                       focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="all">Semua (Seluruh Anggota Tim)</option>
                            <option value="hr_staff">Semua HR Staff</option>
                            <option value="hr_assistant">Semua HR Assistant</option>
                            <option value="cs">Semua CS (Customer Service)</option>
                            <option value="ob">Semua OB (Office Boy)</option>
                            <option value="programmer">Semua Programmer</option>
                            <option value="dg">Semua DG (Design Graphics)</option>
                            <option value="vg">Semua VG (Videografer)</option>
                            <option value="pm">Semua PM (Project Manager)</option>
                            <option value="specific">Pilih Pengguna Tertentu</option>
                        </select>
                    </div>

                    {{-- Pilih pengguna spesifik --}}
                    <div x-show="recipients === 'specific'" x-transition class="space-y-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Pilih Pengguna</label>
                        <div class="border border-gray-300 rounded-lg divide-y divide-gray-100 max-h-52 overflow-y-auto">
                            <template x-for="user in users" :key="user.id">
                                <label class="flex items-center gap-3 px-3 py-2.5 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" name="user_ids[]" :value="user.id" x-model="selected" class="w-4 h-4 rounded border-gray-300 text-primary-600
                                                  focus:ring-primary-500">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-800 truncate" x-text="user.name"></p>
                                        <p class="text-xs text-gray-400" x-text="{
                                               hr_staff:     'HR Staff',
                                               hr_assistant: 'HR Assistant',
                                               cs:           'CS (Customer Service)',
                                               ob:           'OB (Office Boy)',
                                               programmer:   'Programmer',
                                               dg:           'DG (Design Graphics)',
                                               vg:           'VG (Videografer)',
                                               pm:           'PM (Project Manager)',
                                           }[user.role] ?? user.role"></p>
                                    </div>
                                </label>
                            </template>
                        </div>
                        @error('user_ids')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5
                                   bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium
                                   rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        Kirim Notifikasi
                    </button>
                </form>
            </div>
        </div>

        {{-- ── Riwayat Notifikasi Terkirim ──────────────── --}}
        <div class="lg:col-span-3">
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-sm font-semibold text-gray-800">Riwayat Notifikasi Terkirim</h2>
                    <p class="text-xs text-gray-400 mt-0.5">50 notifikasi terbaru</p>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse($sent as $item)
                        @php
                            $data = $item->data;
                            $recipient = $item->recipient;
                        @endphp
                        <div class="px-6 py-4 hover:bg-gray-50 transition">
                            <div class="flex items-start gap-3">
                                {{-- Icon --}}
                                <div
                                    class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-800 truncate">
                                                {{ $data['title'] ?? '-' }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">
                                                {{ $data['message'] ?? '' }}
                                            </p>
                                        </div>
                                        <div class="text-right flex-shrink-0">
                                            <span class="text-xs text-gray-400">
                                                {{ $item->created_at->locale('id')->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="text-xs text-gray-400">Ke:</span>
                                        @if($recipient)
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $recipient->role_badge_class }}">
                                                {{ $recipient->name }} ({{ $recipient->role_label }})
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Pengguna dihapus</span>
                                        @endif
                                        @if(!is_null($item->read_at))
                                            <span class="text-xs text-green-600">• Dibaca</span>
                                        @else
                                            <span class="text-xs text-orange-500">• Belum dibaca</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-16 text-center">
                            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                            <p class="text-sm text-gray-500">Belum ada notifikasi yang dikirim</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

@endsection