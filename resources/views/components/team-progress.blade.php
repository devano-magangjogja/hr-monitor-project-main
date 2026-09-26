@props([
    'rows',
    'unfinished',
    'actionRoute',
    'reminderRoute',
    'detailRoute',
    'search' => '',
    'status' => '',
    'emptyText' => 'Belum ada data pengguna.',
])

<x-responsive-card :padding="'p-0'">
    {{-- ── Header: judul + filter ────────────────────────────── --}}
    <div class="px-3 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="min-w-0">
                <h2 class="text-xs sm:text-sm font-semibold text-gray-700">Progres Tim Hari Ini</h2>
                <p class="text-[11px] text-gray-400 mt-0.5">
                    Belum &amp; sudah dihitung dari tugas yang diberikan hari ini (rutin, presensi, mandiri, dan sosmed).
                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch gap-2.5 sm:gap-3">
                {{-- Pencarian nama --}}
                <form action="{{ $actionRoute }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                    @if($status !== '')
                        <input type="hidden" name="progress_status" value="{{ $status }}">
                    @endif
                    <div class="relative flex-1 sm:flex-initial sm:w-64">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" name="progress_search" value="{{ $search }}"
                            placeholder="Cari nama atau role..."
                            class="w-full h-9 pl-9 pr-3 text-xs bg-white border border-gray-300 rounded-lg shadow-sm
                                focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 text-gray-700 transition">
                    </div>
                    <button type="submit"
                        class="shrink-0 inline-flex items-center justify-center h-9 px-3.5 bg-primary-600 hover:bg-primary-700
                            text-white text-xs font-medium rounded-lg transition shadow-sm">
                        Cari
                    </button>
                    @if($search !== '')
                        <a href="{{ $actionRoute }}"
                            class="shrink-0 inline-flex items-center justify-center h-9 px-3 text-xs font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                            Reset
                        </a>
                    @endif
                </form>

                {{-- Filter status --}}
                <form action="{{ $actionRoute }}" method="GET" class="w-full sm:w-auto">
                    @if($search !== '')
                        <input type="hidden" name="progress_search" value="{{ $search }}">
                    @endif
                    <select name="progress_status" onchange="this.form.submit()"
                        class="w-full sm:w-auto h-9 px-3 text-xs bg-white border border-gray-300 rounded-lg shadow-sm
                            focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 text-gray-700 transition">
                        <option value="">Semua kondisi</option>
                        <option value="belum" @selected($status === 'belum')>Belum beres</option>
                        <option value="sudah" @selected($status === 'sudah')>Sudah beres</option>
                    </select>
                </form>

                {{-- Aksi massal: pengingat untuk yang belum selesai --}}
                @if($unfinished->isNotEmpty())
                    <form action="{{ $reminderRoute }}" method="POST" class="w-full sm:w-auto">
                        @csrf
                        @foreach($unfinished as $u)
                            <input type="hidden" name="user_ids[]" value="{{ $u->id }}">
                        @endforeach
                        <button type="submit"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 h-9 px-3.5 bg-red-50 hover:bg-red-100
                                text-red-700 text-xs font-semibold rounded-lg transition border border-red-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            Kirim Pengingat ({{ $unfinished->count() }})
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Tabel ─────────────────────────────────────────────── --}}
    <x-responsive-table-wrapper>
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="text-center px-3 sm:px-6 py-2.5 sm:py-3.5 font-semibold text-gray-600 text-xs sm:text-sm w-[28%] max-w-xs">
                    Nama
                </th>
                <th class="text-center px-3 sm:px-6 py-2.5 sm:py-3.5 font-semibold text-gray-600 text-xs sm:text-sm w-28 sm:w-36 whitespace-nowrap hidden sm:table-cell">
                    Role
                </th>
                <th class="text-center px-3 sm:px-6 py-2.5 sm:py-3.5 font-semibold text-gray-600 text-xs sm:text-sm w-20 sm:w-28 whitespace-nowrap">
                    Belum
                </th>
                <th class="text-center px-3 sm:px-6 py-2.5 sm:py-3.5 font-semibold text-gray-600 text-xs sm:text-sm w-20 sm:w-28 whitespace-nowrap">
                    Sudah
                </th>
                <th class="text-center px-3 sm:px-6 py-2.5 sm:py-3.5 font-semibold text-gray-600 text-xs sm:text-sm w-20 sm:w-28 whitespace-nowrap">
                    Total
                </th>
                <th class="text-center px-3 sm:px-6 py-2.5 sm:py-3.5 font-semibold text-gray-600 text-xs sm:text-sm">
                    Progress Hari Ini
                </th>
                <th class="text-center px-3 sm:px-6 py-2.5 sm:py-3.5 font-semibold text-gray-600 text-xs sm:text-sm w-20 sm:w-24">
                    Aksi
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($rows as $user)
                @php
                    $pct = $user->persen_hari_ini;
                    $barColor = $pct === 100
                        ? 'bg-green-500'
                        : ($pct >= 50 ? 'bg-primary-500' : 'bg-yellow-500');
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    {{-- Nama --}}
                    <td class="px-3 sm:px-6 py-3 sm:py-4">
                        <div class="flex items-center justify-center gap-2 sm:gap-3 min-w-0">
                            <span class="text-xs sm:text-sm font-medium text-gray-800 truncate max-w-[10rem]">{{ $user->name }}</span>
                        </div>
                    </td>
                    {{-- Role --}}
                    <td class="px-3 sm:px-6 py-3 sm:py-4 text-center hidden sm:table-cell">
                        <span class="inline-flex whitespace-nowrap px-1.5 sm:px-2.5 py-0.5 sm:py-1 rounded-full text-xs font-medium {{ $user->role_badge_class }}">
                            {{ $user->role_label }}
                        </span>
                    </td>
                    {{-- Belum --}}
                    <td class="px-3 sm:px-6 py-3 sm:py-4 text-center">
                        @if($user->belum_hari_ini > 0)
                            <span class="inline-flex items-center justify-center px-1.5 sm:px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700">
                                {{ $user->belum_hari_ini }}
                            </span>
                        @else
                            <span class="text-xs sm:text-sm text-gray-400">0</span>
                        @endif
                    </td>
                    {{-- Sudah --}}
                    <td class="px-3 sm:px-6 py-3 sm:py-4 text-center">
                        <span class="text-xs sm:text-sm font-semibold text-green-600">{{ $user->sudah_hari_ini }}</span>
                    </td>
                    {{-- Total all-time --}}
                    <td class="px-3 sm:px-6 py-3 sm:py-4 text-center">
                        <span class="text-xs sm:text-sm text-gray-600">{{ $user->total_all_time }}</span>
                    </td>
                    {{-- Progress --}}
                    <td class="px-3 sm:px-6 py-3 sm:py-4">
                        <div class="flex items-center justify-center gap-2 sm:gap-3">
                            <div class="flex-1 h-1.5 sm:h-2 bg-gray-100 rounded-full overflow-hidden min-w-[50px] max-w-[150px]">
                                <div class="{{ $barColor }} h-full rounded-full transition-all duration-300"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                            <span class="text-xs font-medium text-gray-600 w-8 text-right">{{ $pct }}%</span>
                        </div>
                        <p class="text-[11px] text-gray-400 mt-1 text-center">
                            {{ $user->sudah_hari_ini }}/{{ $user->tugas_hari_ini }} tugas hari ini
                        </p>
                    </td>
                    {{-- Aksi --}}
                    <td class="px-3 sm:px-6 py-3 sm:py-4">
                        <div class="flex items-center justify-center gap-1">
                            @if($user->belum_hari_ini > 0)
                                <form action="{{ $reminderRoute }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="user_ids[]" value="{{ $user->id }}">
                                    <button type="submit"
                                        class="p-1.5 inline-flex text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                        title="Kirim pengingat tugas ke {{ $user->name }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route($detailRoute, $user->id) }}"
                               class="p-1.5 inline-flex text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                               title="Lihat Detail Tugas">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-3 sm:px-6 py-8 sm:py-12 text-center text-gray-400 text-xs sm:text-sm">
                        {{ $emptyText }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-responsive-table-wrapper>

    {{-- ── Pagination ────────────────────────────────────────── --}}
    @if($rows->hasPages())
        <div class="px-3 sm:px-6 py-3 border-t border-gray-100 bg-gray-50/50">
            {{ $rows->links() }}
        </div>
    @endif
</x-responsive-card>
