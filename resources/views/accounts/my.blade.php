@extends('layouts.app')

@section('title', 'Tambah Akun Sosmed')
@section('page-title', 'Tambah Akun Sosmed')
@section('page-subtitle', 'Daftar pengajuan akun media sosial Anda')

@section('sidebar')
    @if(auth()->user()->isAdmin())
        @include('components.sidebar-admin')
    @elseif(auth()->user()->isHrStaff())
        @include('components.sidebar-staff')
    @elseif(auth()->user()->isHrAssistant())
        @include('components.sidebar-assistant')
    @elseif(auth()->user()->role === 'pm')
        @include('components.sidebar-pm')
    @else
        @include('components.sidebar-sosmed')
    @endif
@endsection

@section('content')
    @include('components.notification-popup')

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-6">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <form method="GET" action="{{ url()->current() }}" class="flex-1 max-w-xl">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Cari akun</label>
                <div class="flex gap-2">
                    <input name="search" value="{{ $search ?? '' }}" placeholder="Nama akun, platform, atau email"
                        class="flex-1 h-10 px-3 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    <button class="h-10 px-4 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-semibold">Cari</button>
                </div>
            </form>
            <button type="button" onclick="document.getElementById('account-modal').classList.remove('hidden')"
                class="h-10 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold">+ Ajukan Akun</button>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[820px] text-sm">
                <thead class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-5 py-3 text-left">Platform</th>
                        <th class="px-5 py-3 text-left">Nama Akun</th>
                        <th class="px-5 py-3 text-left">Email</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-left">Catatan Penolakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($accounts as $account)
                        @php($status = $account->verification_status ?? 'approved')
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-4 font-medium text-gray-700">{{ $account->platform }}</td>
                            <td class="px-5 py-4 font-semibold text-gray-800">{{ $account->name }}</td>
                            <td class="px-5 py-4 text-gray-600">{{ $account->email ?: '-' }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $status === 'approved' ? 'bg-emerald-50 text-emerald-700' : ($status === 'pending' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700') }}">
                                    {{ $status === 'approved' ? 'Disetujui' : ($status === 'pending' ? 'Menunggu verifikasi' : 'Ditolak') }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-xs {{ $status === 'rejected' ? 'text-rose-600' : 'text-gray-400' }}">
                                {{ $account->rejection_note ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-12 text-center text-sm text-gray-400">Belum ada pengajuan akun.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($accounts->hasPages())
            <div class="p-4 border-t border-gray-100">{{ $accounts->links() }}</div>
        @endif
    </div>

    <div id="account-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="document.getElementById('account-modal').classList.add('hidden')"></div>
        <form method="POST" action="{{ route(request()->segment(1) . '.accounts.store') }}" class="relative z-10 w-full max-w-lg bg-white rounded-xl shadow-xl p-6 space-y-4">
            @csrf
            <div class="flex items-center justify-between"><h2 class="font-bold text-gray-800">Ajukan Akun Baru</h2><button type="button" onclick="document.getElementById('account-modal').classList.add('hidden')" class="text-gray-400 text-xl">&times;</button></div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <label class="text-xs font-semibold text-gray-700 sm:col-span-2">Platform *<select name="platform" id="own-platform" required onchange="toggleCustomPlatform(this, 'own-custom-platform')" class="mt-1 w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"><option value="">Pilih platform</option><option>Instagram</option><option>TikTok</option><option>YouTube</option><option>Facebook</option><option>Twitter/X</option><option>LinkedIn</option><option>Threads</option><option>Website</option><option value="Lainnya">Lainnya</option></select></label>
                <label id="own-custom-platform" class="hidden text-xs font-semibold text-gray-700 sm:col-span-2">Nama Platform Custom *<input name="custom_platform" maxlength="50" class="mt-1 w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"></label>
                <label class="text-xs font-semibold text-gray-700 sm:col-span-2">Email *<input name="email" type="email" required class="mt-1 w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"></label>
                <label class="text-xs font-semibold text-gray-700 sm:col-span-2">Password *<span class="relative block mt-1"><input id="own-password" name="password" type="password" required class="w-full border border-gray-200 rounded-lg pl-3 pr-10 py-2 text-sm"><button type="button" onclick="togglePassword('own-password', this)" class="absolute inset-y-0 right-0 px-3 text-gray-400" title="Lihat password"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"/></svg></button></span></label>
            </div>
            <button class="w-full py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-semibold">Kirim Pengajuan</button>
        </form>
    </div>
    <script>
        function toggleCustomPlatform(select, targetId) {
            const target = document.getElementById(targetId);
            const input = target.querySelector('input');
            const isCustom = select.value === 'Lainnya';
            target.classList.toggle('hidden', !isCustom);
            input.required = isCustom;
            if (!isCustom) input.value = '';
        }
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            input.type = input.type === 'password' ? 'text' : 'password';
            button.textContent = input.type === 'password' ? '◉' : '◉';
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (new URLSearchParams(window.location.search).get('create') === '1') {
                document.getElementById('account-modal')?.classList.remove('hidden');
            }
        });
    </script>
@endsection
