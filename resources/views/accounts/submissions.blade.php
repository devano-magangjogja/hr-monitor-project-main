@extends('layouts.app')

@section('title', 'Pengajuan Akun Saya')
@section('page-title', 'Pengajuan Akun Saya')
@section('page-subtitle', 'Daftar akun media sosial Anda yang telah disetujui')

@section('sidebar')
    @if(auth()->user()->isHrAssistant())
        @include('components.sidebar-assistant')
    @elseif(auth()->user()->role === 'pm')
        @include('components.sidebar-pm')
    @else
        @include('components.sidebar-sosmed')
    @endif
@endsection

@section('content')
    @include('components.notification-popup')

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-gray-200 flex flex-col sm:flex-row sm:items-end justify-between gap-3">
            <form method="GET" action="{{ url()->current() }}" class="flex-1 max-w-xl">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Cari akun</label>
                <div class="flex gap-2">
                    <input name="search" value="{{ $search ?? '' }}" placeholder="Nama akun, platform, atau email"
                        class="flex-1 h-10 px-3 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    <button
                        class="h-10 px-4 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-semibold">Cari</button>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm table-fixed">
                <thead class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-5 py-3 text-left w-[18%]">Platform</th>
                        <th class="px-5 py-3 text-left w-[27%]">Nama Akun</th>
                        <th class="px-5 py-3 text-left w-[25%]">Link Akun</th>
                        <th class="px-5 py-3 text-left w-[30%]">Email</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($accounts as $account)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-4">
                                <span class="font-medium text-gray-700 truncate block" title="{{ $account->platform }}">
                                    {{ $account->platform }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-semibold text-gray-800 truncate block" title="{{ $account->name }}">
                                    {{ $account->name }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                @if($account->link)
                                    <a href="{{ $account->link }}" target="_blank" rel="noopener noreferrer"
                                        class="text-primary-600 hover:underline inline-flex items-center gap-1 max-w-full"
                                        title="{{ $account->link }}">
                                        <span class="truncate">Buka Link</span>
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-gray-600 truncate block" title="{{ $account->email ?: '-' }}">
                                    {{ $account->email ?: '—' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center text-sm text-gray-400">
                                Belum ada akun media sosial yang disetujui.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($accounts->hasPages())
            <div class="p-4 border-t border-gray-100">{{ $accounts->links() }}</div>
        @endif
    </div>
@endsection