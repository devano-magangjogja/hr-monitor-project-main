{{-- Tabel daftar akun sosmed. Dipakai di TAB "Daftar Akun" dan di accordion TAB "Brand".
     Props: $rows (collection|paginator). $accountPrefix diwarisi dari view induk.
     Desktop (md+): tabel penuh. Mobile: kartu per akun agar rapi. --}}
<div class="hidden md:block">
<table class="w-full text-sm text-left table-fixed">
    <thead>
        <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
            <th class="px-4 py-3.5 w-[10%]">Platform</th>
            <th class="px-4 py-3.5 w-[15%]">Nama Akun</th>
            <th class="px-4 py-3.5 w-[11%]">Brand</th>
            <th class="px-4 py-3.5 w-[7%]">Link</th>
            <th class="px-4 py-3.5 w-[13%]">Email</th>
            <th class="px-4 py-3.5 w-[13%]">Password</th>
            <th class="px-4 py-3.5 w-[5%] text-center">2FA</th>
            <th class="px-2 py-3.5 w-[8%] text-center" title="Jumlah user yang memegang akun ini">Pemegang</th>
            <th class="px-2 py-3.5 w-[6%] text-center" title="Hijau: belum ada di Sosmed · Merah: sudah ada di Sosmed">Sosmed</th>
            <th class="px-4 py-3.5 w-[8%] text-center">Aksi</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-100 bg-white">
        @forelse($rows as $acc)
            @php($pemegangLabels = array_map(fn($h) => $h['name'] . ' (' . implode(', ', $h['roles']) . ')', $acc->holders()))
            <tr class="hover:bg-gray-50/80 transition-colors">
                {{-- Platform --}}
                <td class="px-4 py-3.5">
                    <span
                        class="inline-flex max-w-full truncate items-center px-2 py-1 rounded-md text-xs font-medium border {{ $acc->platform_color }}"
                        title="{{ $acc->platform }}">
                        {{ $acc->platform }}
                    </span>
                </td>

                {{-- Nama Akun --}}
                <td class="px-4 py-3.5">
                    <span class="font-semibold text-gray-900 truncate block" title="{{ $acc->name }}">
                        {{ $acc->name }}
                    </span>
                    @if($acc->notes)
                        <div class="text-xs text-gray-400 truncate mt-0.5" title="{{ $acc->notes }}">
                            {{ $acc->notes }}
                        </div>
                    @endif
                </td>

                {{-- Brand --}}
                <td class="px-4 py-3.5">
                    @if($acc->brand)
                        <span
                            class="inline-flex max-w-full items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200 truncate"
                            title="Brand: {{ $acc->brand }}">
                            <svg class="w-3 h-3 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            <span class="truncate">{{ $acc->brand }}</span>
                        </span>
                    @else
                        <span class="text-xs text-gray-400 italic">—</span>
                    @endif
                </td>

                {{-- Link --}}
                <td class="px-4 py-3.5">
                    @if($acc->link)
                        <a href="{{ $acc->link }}" target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center gap-1 text-xs font-medium text-primary-600 hover:text-primary-800 hover:underline"
                            title="{{ $acc->link }}">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            <span>Buka</span>
                        </a>
                    @else
                        <span class="text-xs text-gray-400 italic">—</span>
                    @endif
                </td>

                {{-- Email --}}
                <td class="px-4 py-3.5">
                    @if($acc->email)
                        <div class="flex items-center gap-1 min-w-0" x-data="{ copied: false }">
                            <span
                                class="text-xs font-mono text-gray-700 bg-gray-50 border border-gray-200 px-2 py-1 rounded truncate min-w-0"
                                title="{{ $acc->email }}">
                                {{ $acc->email }}
                            </span>
                            <button type="button"
                                @click="navigator.clipboard.writeText(@js($acc->email)); copied = true; setTimeout(() => copied = false, 2000)"
                                class="p-1 text-gray-400 hover:text-gray-600 rounded transition shrink-0"
                                :title="copied ? 'Tersalin!' : 'Salin Email'">
                                <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <svg x-show="copied" x-cloak class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                        </div>
                    @else
                        <span class="text-xs text-gray-400 italic">—</span>
                    @endif
                </td>

                {{-- Password --}}
                <td class="px-4 py-3.5">
                    @if($acc->password)
                        <div class="flex items-center gap-1 min-w-0" x-data="{ show: false, copied: false }">
                            <div class="bg-gray-50 border border-gray-200 px-2 py-1 rounded min-w-0 flex-1 overflow-hidden">
                                <span x-show="!show" class="font-mono text-xs text-gray-400 tracking-widest select-none">••••••••</span>
                                <span x-show="show" x-cloak class="font-mono text-xs text-gray-900 font-semibold truncate block"
                                    title="{{ $acc->password }}">{{ $acc->password }}</span>
                            </div>
                            <button type="button" @click="show = !show"
                                class="p-1 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded transition shrink-0"
                                :title="show ? 'Sembunyikan' : 'Lihat'">
                                <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="show" x-cloak class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                            <button type="button"
                                @click="navigator.clipboard.writeText(@js($acc->password)); copied = true; setTimeout(() => copied = false, 2000)"
                                class="p-1 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded transition shrink-0"
                                :title="copied ? 'Tersalin!' : 'Salin'">
                                <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <svg x-show="copied" x-cloak class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                        </div>
                    @else
                        <span class="text-xs text-gray-400 italic">—</span>
                    @endif
                </td>

                {{-- 2FA --}}
                <td class="px-4 py-3.5 text-center text-xs font-semibold {{ $acc->two_factor_enabled ? 'text-emerald-600' : 'text-gray-400' }}">
                    {{ $acc->two_factor_enabled ? 'Ya' : 'Tidak' }}
                </td>

                {{-- Jumlah Pemegang --}}
                <td class="px-4 py-3.5 text-center">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ count($pemegangLabels) > 0 ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-gray-100 text-gray-400' }}"
                        title="{{ $pemegangLabels ? implode(', ', $pemegangLabels) : 'Belum ada pemegang akun' }}">
                        {{ count($pemegangLabels) }}
                    </span>
                </td>

                {{-- Di Sosmed (indikator warna) --}}
                <td class="px-4 py-3.5 text-center">
                    <span class="inline-block w-3 h-3 rounded-full {{ $acc->is_in_sosmed ? 'bg-red-500' : 'bg-emerald-500' }}"
                        title="{{ $acc->is_in_sosmed ? 'Sudah ada di Sosmed' : 'Belum ada di Sosmed' }}"></span>
                </td>

                {{-- Aksi --}}
                <td class="px-4 py-3.5 text-center">
                    <div class="flex items-center justify-center gap-0.5">
                        <button type="button" onclick="openAccountDetail({{ json_encode([
                            'platform' => $acc->platform,
                            'name' => $acc->name,
                            'brand' => $acc->brand ?? '',
                            'link' => $acc->link ?? '',
                            'email' => $acc->email ?? '',
                            'password' => $acc->password ?? '',
                            'email_recovery' => $acc->email_recovery ?? '',
                            'phone' => $acc->phone ?? '',
                            'two_factor' => $acc->two_factor_enabled ? 'Ya' : 'Tidak',
                            'notes' => $acc->notes ?? '',
                            'status' => $acc->is_in_sosmed ? 'Sudah ada di Sosmed' : 'Belum ada di Sosmed',
                            'pemegang' => $pemegangLabels,
                        ]) }})" class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                            title="Lihat detail akun">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7z" />
                                <path stroke-linecap="round" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                            </svg>
                        </button>
                        <button type="button" onclick="openEditAccountModal({{ json_encode([
                            'id' => $acc->id,
                            'name' => $acc->name,
                            'brand' => $acc->brand ?? '',
                            'platform' => $acc->platform,
                            'link' => $acc->link ?? '',
                            'email' => $acc->email ?? '',
                            'password' => $acc->password ?? '',
                            'email_recovery' => $acc->email_recovery ?? '',
                            'phone' => $acc->phone ?? '',
                            'two_factor_enabled' => (bool) $acc->two_factor_enabled,
                            'notes' => $acc->notes ?? '',
                        ]) }})" class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                            title="Edit Akun">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <button type="button"
                            onclick="openDeleteAccountModal({{ $acc->id }}, '{{ addslashes($acc->name) }}', '{{ addslashes($acc->platform) }}')"
                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                            title="Hapus Akun">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="px-6 py-12 text-center text-gray-400">
                    <div class="flex flex-col items-center justify-center">
                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <p class="font-medium text-gray-600">Belum ada data akun sosial media</p>
                        <p class="text-xs text-gray-400 mt-1">Klik tombol "Tambah Akun Baru" di atas untuk menambahkan akun.</p>
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
</div>

{{-- Mobile (kartu per akun) --}}
<div class="md:hidden divide-y divide-gray-100 bg-white">
    @forelse($rows as $acc)
        @php($pemegangLabels = array_map(fn($h) => $h['name'] . ' (' . implode(', ', $h['roles']) . ')', $acc->holders()))
        <div class="p-3.5">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-gray-900 text-sm break-words">{{ $acc->name }}</p>
                    <div class="flex items-center gap-1.5 flex-wrap mt-1">
                        <span
                            class="inline-flex max-w-full truncate items-center px-2 py-0.5 rounded-md text-[11px] font-medium border {{ $acc->platform_color }}">
                            {{ $acc->platform }}
                        </span>
                        @if($acc->brand)
                            <span
                                class="inline-flex max-w-[9rem] items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                <svg class="w-2.5 h-2.5 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                <span class="truncate">{{ $acc->brand }}</span>
                            </span>
                        @endif
                        <span class="inline-block w-2.5 h-2.5 rounded-full shrink-0 {{ $acc->is_in_sosmed ? 'bg-red-500' : 'bg-emerald-500' }}"
                            title="{{ $acc->is_in_sosmed ? 'Sudah ada di Sosmed' : 'Belum ada di Sosmed' }}"></span>
                    </div>
                    @if($acc->notes)
                        <p class="text-xs text-gray-400 mt-1 break-words">{{ $acc->notes }}</p>
                    @endif
                </div>
                {{-- Aksi --}}
                <div class="flex items-center gap-0.5 shrink-0">
                    <button type="button" onclick="openAccountDetail({{ json_encode([
                        'platform' => $acc->platform,
                        'name' => $acc->name,
                        'brand' => $acc->brand ?? '',
                        'link' => $acc->link ?? '',
                        'email' => $acc->email ?? '',
                        'password' => $acc->password ?? '',
                        'email_recovery' => $acc->email_recovery ?? '',
                        'phone' => $acc->phone ?? '',
                        'two_factor' => $acc->two_factor_enabled ? 'Ya' : 'Tidak',
                        'notes' => $acc->notes ?? '',
                        'status' => $acc->is_in_sosmed ? 'Sudah ada di Sosmed' : 'Belum ada di Sosmed',
                        'pemegang' => $pemegangLabels,
                    ]) }})" class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                        title="Lihat detail akun">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7z" />
                            <path stroke-linecap="round" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                        </svg>
                    </button>
                    <button type="button" onclick="openEditAccountModal({{ json_encode([
                        'id' => $acc->id,
                        'name' => $acc->name,
                        'brand' => $acc->brand ?? '',
                        'platform' => $acc->platform,
                        'link' => $acc->link ?? '',
                        'email' => $acc->email ?? '',
                        'password' => $acc->password ?? '',
                        'email_recovery' => $acc->email_recovery ?? '',
                        'phone' => $acc->phone ?? '',
                        'two_factor_enabled' => (bool) $acc->two_factor_enabled,
                        'notes' => $acc->notes ?? '',
                    ]) }})" class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                        title="Edit Akun">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                    <button type="button"
                        onclick="openDeleteAccountModal({{ $acc->id }}, '{{ addslashes($acc->name) }}', '{{ addslashes($acc->platform) }}')"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                        title="Hapus Akun">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="mt-2.5 space-y-1.5">
                @if($acc->email)
                    <div class="flex items-center gap-1.5 min-w-0" x-data="{ copied: false }">
                        <span class="text-[11px] text-gray-400 w-16 shrink-0">Email</span>
                        <span
                            class="text-xs font-mono text-gray-700 bg-gray-50 border border-gray-200 px-2 py-1 rounded truncate min-w-0 flex-1">
                            {{ $acc->email }}
                        </span>
                        <button type="button"
                            @click="navigator.clipboard.writeText(@js($acc->email)); copied = true; setTimeout(() => copied = false, 2000)"
                            class="p-1.5 text-gray-400 hover:text-gray-600 rounded transition shrink-0"
                            :title="copied ? 'Tersalin!' : 'Salin Email'">
                            <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <svg x-show="copied" x-cloak class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </div>
                @endif
                @if($acc->password)
                    <div class="flex items-center gap-1.5 min-w-0" x-data="{ show: false, copied: false }">
                        <span class="text-[11px] text-gray-400 w-16 shrink-0">Password</span>
                        <div class="relative bg-gray-50 border border-gray-200 rounded min-w-0 flex-1 overflow-hidden">
                            <span x-show="!show" class="block px-2 py-1 pr-8 font-mono text-xs text-gray-400 tracking-widest select-none">••••••••</span>
                            <span x-show="show" x-cloak class="block px-2 py-1 pr-8 font-mono text-xs text-gray-900 font-semibold truncate">{{ $acc->password }}</span>
                            <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-0 flex items-center px-1.5 text-gray-400 hover:text-gray-700 transition"
                                :title="show ? 'Sembunyikan' : 'Lihat'">
                                <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="show" x-cloak class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        <button type="button"
                            @click="navigator.clipboard.writeText(@js($acc->password)); copied = true; setTimeout(() => copied = false, 2000)"
                            class="p-1.5 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded transition shrink-0"
                            :title="copied ? 'Tersalin!' : 'Salin'">
                            <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <svg x-show="copied" x-cloak class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </div>
                @endif
                <div class="flex items-center gap-3 text-xs pt-0.5 flex-wrap">
                    <span class="text-gray-400">2FA:
                        <span class="font-semibold {{ $acc->two_factor_enabled ? 'text-emerald-600' : 'text-gray-500' }}">
                            {{ $acc->two_factor_enabled ? 'Ya' : 'Tidak' }}
                        </span>
                    </span>
                    <span class="text-gray-400" title="{{ $pemegangLabels ? implode(', ', $pemegangLabels) : 'Belum ada pemegang akun' }}">Pemegang:
                        <span class="font-semibold text-gray-700">{{ count($pemegangLabels) }}</span>
                    </span>
                    @if($acc->link)
                        <a href="{{ $acc->link }}" target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center gap-1 font-medium text-primary-600 hover:text-primary-800 hover:underline">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            Buka Link
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="px-4 py-10 text-center">
            <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <p class="text-sm font-medium text-gray-600">Belum ada data akun sosial media</p>
            <p class="text-xs text-gray-400 mt-1">Klik tombol "Tambah Akun Baru" di atas untuk menambahkan akun.</p>
        </div>
    @endforelse
</div>
