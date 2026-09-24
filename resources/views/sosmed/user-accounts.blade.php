@extends('layouts.app')
@section('title', 'Daftar Akun Dikelola — ' . $targetUser->name)
@section('page-title', 'Daftar Akun Sosmed Dikelola')
@section('page-subtitle', 'Seluruh akun sosmed tempat ' . $targetUser->name . ' berperan sebagai eksekutor, PM, asisten, atau staff pengawas')
@section('sidebar')
    @include($sidebar)
@endsection

@section('content')
    @php
        $roleChip = [
            'Eksekutor'      => 'bg-pink-50 text-pink-700 border-pink-200',
            'PM'             => 'bg-blue-50 text-blue-700 border-blue-200',
            'Asisten'        => 'bg-purple-50 text-purple-700 border-purple-200',
            'Staff Pengawas' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        ];
        $hasSearch = !empty($accountSearch);
    @endphp

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200 bg-gray-50/50 flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-full bg-primary-600 text-white flex items-center justify-center font-semibold shrink-0">
                    {{ strtoupper(substr($targetUser->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <h3 class="text-sm font-semibold text-gray-800 truncate">{{ $targetUser->name }}</h3>
                    <p class="text-xs text-gray-500 truncate">{{ $targetUser->email }} • {{ $rows->total() }} akun</p>
                </div>
            </div>
            <a href="{{ url()->previous() }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali
            </a>
        </div>

        <div class="p-4 sm:p-6">
            {{-- Search --}}
            <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2 mb-4">
                <div class="relative flex-1 max-w-xs">
                    <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="account_search" value="{{ $accountSearch ?? '' }}"
                           placeholder="Cari nama akun / platform / brand..."
                           class="w-full border border-gray-300 rounded-lg pl-8 pr-3 py-1.5 text-xs focus:ring-2 focus:ring-primary-500 focus:outline-none">
                </div>
                <button type="submit"
                        class="px-3 py-1.5 text-xs font-semibold text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition">Cari</button>
                @if($hasSearch)
                    <a href="{{ url()->current() }}" class="text-xs text-gray-500 hover:text-gray-700 underline">Reset</a>
                @endif
            </form>

            @if($rows->total() === 0)
                <div class="py-10 text-center text-sm text-gray-400">
                    @if($hasSearch)
                        Tidak ada akun yang cocok dengan pencarian "{{ $accountSearch }}".
                    @else
                        {{ $targetUser->name }} belum mengelola akun sosmed apa pun.
                    @endif
                </div>
            @else
                {{-- Desktop Table (md+) --}}
                <div class="hidden md:block overflow-x-auto rounded-lg border border-gray-100">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                <th class="px-2.5 py-2 text-center w-10">No</th>
                                <th class="px-2.5 py-2 text-left">Nama Akun</th>
                                <th class="px-2.5 py-2 text-left">Platform</th>
                                <th class="px-2.5 py-2 text-left">Peran</th>
                                <th class="px-2.5 py-2 text-left">Link Profil</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($rows as $i => $row)
                                @php $acc = $row['account']; @endphp
                                <tr class="hover:bg-gray-50/60 transition align-middle">
                                    <td class="px-2.5 py-2 text-center text-xs text-gray-400">{{ ($rows->firstItem() ?? 1) + $i }}</td>
                                    <td class="px-2.5 py-2 max-w-[200px]">
                                        <div class="flex items-center gap-1.5 min-w-0">
                                            <span class="font-semibold text-gray-800 truncate min-w-0" title="{{ $acc->name }}">{{ $acc->name }}</span>
                                            <x-brand-badge :acc="$acc" :logos="$brandLogos" />
                                        </div>
                                    </td>
                                    <td class="px-2.5 py-2 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium border whitespace-nowrap {{ $acc->platform_color }}">
                                            {{ $acc->platform }}
                                        </span>
                                    </td>
                                    <td class="px-2.5 py-2">
                                        <div class="flex items-center gap-1 flex-wrap">
                                            @foreach($row['roles'] as $r)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium border whitespace-nowrap {{ $roleChip[$r] ?? 'bg-gray-100 text-gray-700 border-gray-200' }}">{{ $r }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-2.5 py-2 text-xs max-w-[220px]">
                                        @if($acc->link)
                                            <a href="{{ $acc->link }}" target="_blank" rel="noopener noreferrer"
                                               class="text-primary-600 hover:underline truncate block" title="{{ $acc->link }}">{{ $acc->link }}</a>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards (< md) --}}
                <div class="md:hidden space-y-3">
                    @foreach($rows as $row)
                        @php $acc = $row['account']; @endphp
                        <div class="rounded-xl border border-gray-200 bg-white p-3 shadow-sm">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5 min-w-0">
                                        <p class="font-semibold text-gray-800 text-sm truncate min-w-0" title="{{ $acc->name }}">{{ $acc->name }}</p>
                                        <x-brand-badge :acc="$acc" :logos="$brandLogos" />
                                    </div>
                                    <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-md text-[10px] font-medium border whitespace-nowrap {{ $acc->platform_color }}">
                                        {{ $acc->platform }}
                                    </span>
                                </div>
                                @if($acc->link)
                                    <a href="{{ $acc->link }}" target="_blank" rel="noopener noreferrer"
                                       class="text-xs text-primary-600 hover:underline shrink-0">Buka</a>
                                @endif
                            </div>
                            <div class="flex items-center gap-1 flex-wrap mt-2 border-t border-gray-100 pt-2">
                                @foreach($row['roles'] as $r)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium border whitespace-nowrap {{ $roleChip[$r] ?? 'bg-gray-100 text-gray-700 border-gray-200' }}">{{ $r }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($rows->hasPages())
                    <div class="mt-4">
                        {{ $rows->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
