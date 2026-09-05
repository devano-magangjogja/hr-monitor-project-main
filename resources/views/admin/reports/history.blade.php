@extends('layouts.app')

@section('title', 'Riwayat Tugas')
@section('page-title', 'Riwayat Tugas')
@section('page-subtitle', 'Rekap historis tugas seluruh anggota tim')

@section('sidebar')
    @include('components.sidebar-admin')
@endsection

@section('content')

{{-- Filter --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5 mb-6 shadow-sm">
    <form method="GET" action="{{ route('admin.reports.history') }}"
          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">

        {{-- Cari Tugas --}}
        <div class="lg:col-span-4">
            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Cari Tugas</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" name="search" value="{{ $search ?? '' }}"
                       placeholder="Cari judul / deskripsi..."
                       class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition">
            </div>
        </div>

        {{-- Filter Pengguna (Searchable Dropdown) --}}
        @php
            $selectedUser = $users->firstWhere('id', $userId);
        @endphp
        <div class="lg:col-span-3 relative" id="user-filter-container">
            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Pengguna</label>
            <input type="hidden" name="user_id" id="filter-user-id" value="{{ $userId ?? '' }}">
            
            <div id="user-filter-trigger" onclick="toggleUserDropdown()"
                 class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs sm:text-sm text-gray-700 flex items-center justify-between cursor-pointer hover:bg-white hover:border-primary-500 transition">
                <span id="user-filter-text" class="truncate font-medium {{ $selectedUser ? 'text-gray-800' : 'text-gray-500' }}">
                    {{ $selectedUser ? $selectedUser->name . ' (' . $selectedUser->role_label . ')' : 'Semua Pengguna' }}
                </span>
                <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>

            {{-- Popover list --}}
            <div id="user-filter-menu"
                 class="hidden absolute z-30 left-0 right-0 mt-1 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden">
                <div class="p-2 border-b border-gray-100 bg-gray-50">
                    <div class="relative">
                        <input type="text" id="user-search-input" oninput="filterUserOptions(this.value)"
                               placeholder="Cari nama atau role..."
                               class="w-full pl-8 pr-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
                <div id="user-options-list" class="max-h-48 overflow-y-auto divide-y divide-gray-50">
                    <div onclick="selectUser('', 'Semua Pengguna')"
                         data-search="semua pengguna all"
                         class="user-filter-option px-3 py-2 hover:bg-primary-50 cursor-pointer text-xs font-semibold text-gray-600 hover:text-primary-700">
                        Semua Pengguna
                    </div>
                    @foreach($users as $user)
                        <div onclick="selectUser('{{ $user->id }}', '{{ addslashes($user->name) }} ({{ addslashes($user->role_label) }})')"
                             data-search="{{ strtolower($user->name . ' ' . $user->role_label) }}"
                             class="user-filter-option px-3 py-2 hover:bg-primary-50 cursor-pointer flex items-center justify-between text-xs text-gray-700 hover:text-primary-700">
                            <span class="font-medium">{{ $user->name }}</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-gray-100 text-gray-500 font-normal">{{ $user->role_label }}</span>
                        </div>
                    @endforeach
                    <div id="user-filter-no-result" class="hidden py-3 text-center text-xs text-gray-400">
                        Tidak ada pengguna yang cocok
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Tanggal --}}
        <div class="lg:col-span-3">
            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Tanggal</label>
            <input type="date" name="date" value="{{ $date ?? '' }}"
                   class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs sm:text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition">
        </div>

        {{-- Tombol Aksi --}}
        <div class="sm:col-span-2 lg:col-span-2 flex items-center gap-2">
            <button type="submit"
                    class="flex-1 flex items-center justify-center gap-1.5 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs sm:text-sm font-semibold rounded-lg transition shadow-sm hover:shadow">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <span>Cari</span>
            </button>

            @if($date || $userId || $search)
                <a href="{{ route('admin.reports.history') }}"
                   class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs sm:text-sm font-semibold rounded-lg transition text-center flex-shrink-0"
                   title="Reset Filter">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-xs sm:text-sm min-w-full">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="text-left px-3 sm:px-6 py-2.5 sm:py-3.5 font-semibold text-gray-600 w-20 sm:w-28">Tanggal</th>
                <th class="text-left px-3 sm:px-6 py-2.5 sm:py-3.5 font-semibold text-gray-600 w-28 sm:w-44">Judul</th>
                <th class="text-left px-3 sm:px-6 py-2.5 sm:py-3.5 font-semibold text-gray-600 w-24 sm:w-36">Penerima</th>
                <th class="text-left px-3 sm:px-6 py-2.5 sm:py-3.5 font-semibold text-gray-600 w-20 sm:w-28">Sumber</th>
                <th class="text-left px-3 sm:px-6 py-2.5 sm:py-3.5 font-semibold text-gray-600 hidden sm:table-cell">Catatan</th>
                <th class="text-left px-3 sm:px-6 py-2.5 sm:py-3.5 font-semibold text-gray-600 w-20 sm:w-28">Status</th>
                <th class="text-right px-3 sm:px-6 py-2.5 sm:py-3.5 font-semibold text-gray-600 w-16 sm:w-28">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($tasks as $task)
                @foreach($task->assignedUsers as $assignee)
                    @php
                        $assignment = $task->assignments->firstWhere('user_id', $assignee->id);
                        $status     = $assignment?->is_completed ?? 'pending';
                    @endphp
                    <tr class="hover:bg-gray-50 transition">

                        {{-- Tanggal --}}
                        <td class="px-3 sm:px-6 py-3 sm:py-4 text-gray-500 text-xs">
                            {{ $task->task_date->translatedFormat('d M Y') }}
                        </td>

                        {{-- Judul --}}
                        <td class="px-3 sm:px-6 py-3 sm:py-4 w-28 sm:w-44">
                            <div class="truncate max-w-[80px] sm:max-w-[160px] font-medium text-gray-800 text-xs sm:text-sm"
                                 title="{{ $task->title }}">
                                {{ $task->title }}
                            </div>
                        </td>

                        {{-- Penerima --}}
                        <td class="px-3 sm:px-6 py-3 sm:py-4 w-24 sm:w-36">
                            <div class="truncate max-w-[70px] sm:max-w-[120px] text-gray-700 text-xs font-medium"
                                 title="{{ $assignee->name }}">
                                {{ $assignee->name }}
                            </div>
                            <p class="text-xs text-gray-400 hidden sm:block">
                                {{ $assignee->role_label }}
                            </p>
                        </td>

                        {{-- Sumber --}}
                        <td class="px-3 sm:px-6 py-3 sm:py-4 w-20 sm:w-28">
                            @if($task->type === 'self')
                                <span class="inline-flex items-center px-1.5 sm:px-2.5 py-0.5 sm:py-1 rounded-full
                                             text-xs font-medium bg-gray-100 text-gray-600">
                                    Mandiri
                                </span>
                            @elseif($task->type === 'assigned')
                                <span class="inline-flex items-center px-1.5 sm:px-2.5 py-0.5 sm:py-1 rounded-full
                                             text-xs font-medium bg-blue-50 text-blue-700">
                                    {{ $task->creator?->name ?? 'Admin' }}
                                </span>
                            @elseif($task->type === 'default')
                                <span class="inline-flex items-center px-1.5 sm:px-2.5 py-0.5 sm:py-1 rounded-full
                                             text-xs font-medium bg-purple-50 text-purple-700">
                                    Rutin
                                </span>
                            @endif
                        </td>

                        {{-- Catatan --}}
                        <td class="px-3 sm:px-6 py-3 sm:py-4 hidden sm:table-cell">
                            <div class="truncate max-w-[100px] sm:max-w-[180px] text-gray-500 text-xs"
                                 title="{{ $assignment?->note ?? '-' }}">
                                {{ $assignment?->note ?? '-' }}
                            </div>
                        </td>

                        {{-- Status --}}
                        <td class="px-3 sm:px-6 py-3 sm:py-4 w-20 sm:w-28">
                            <x-task-status-badge :status="$status" :completedAt="$assignment?->completed_at" />
                        </td>

                        {{-- Aksi --}}
                        <td class="px-3 sm:px-6 py-3 sm:py-4 w-16 sm:w-28">
                            <div class="flex items-center justify-end gap-0.5 sm:gap-1.5">

                                {{-- Tombol Detail --}}
                                <button
                                    onclick="openDetailModal({
                                        title: '{{ addslashes($task->title) }}',
                                        description: '{{ addslashes($task->description ?? '') }}',
                                        type: '{{ $task->type }}',
                                        date: '{{ $task->task_date->translatedFormat('d M Y') }}',
                                        source: '{{ addslashes($task->creator?->name ?? 'Sistem') }}',
                                        status: '{{ $status }}',
                                        note: '{{ addslashes($assignment?->note ?? '') }}',
                                        assignees: {{ json_encode($task->assignedUsers->map(fn($u) => [
                                            'name'   => $u->name,
                                            'role'   => $u->role_label,
                                            'status' => $task->assignments->firstWhere('user_id', $u->id)?->is_completed ?? 'pending',
                                        ])) }}
                                    })"
                                    class="p-1 sm:p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                                    title="Lihat Detail">
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>

                                @if($assignment)
                                    {{-- Tombol Tandai Selesai (hanya jika belum selesai) --}}
                                    @if($status !== 'completed')
                                        <button
                                            onclick="openCompleteModal({{ $assignment->id }}, '{{ addslashes($task->title) }}', '{{ addslashes($assignee->name) }}')"
                                            class="p-1 sm:p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition"
                                            title="Tandai Selesai">
                                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    @endif

                                    {{-- Tombol Hapus Riwayat --}}
                                    <button
                                        onclick="openDeleteModal({{ $assignment->id }}, '{{ addslashes($task->title) }}', '{{ addslashes($assignee->name) }}')"
                                        class="p-1 sm:p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                        title="Hapus Riwayat">
                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="7" class="px-3 sm:px-6 py-12 text-center text-gray-400 text-sm">
                        {{ $date || $userId || $search ? 'Tidak ada data sesuai filter.' : 'Belum ada riwayat tugas.' }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ── MODAL TANDAI SELESAI ─────────────────────────── --}}
<div id="modal-complete" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4">
        <div class="px-6 py-5 text-center">
            <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-800 mb-1">Tandai Selesai</h3>
            <p class="text-sm text-gray-500 mb-1">
                Tandai tugas <span id="complete-task-title" class="font-semibold text-gray-700"></span>
            </p>
            <p class="text-sm text-gray-500 mb-6">
                milik <span id="complete-assignee-name" class="font-semibold text-gray-700"></span> sebagai selesai?
            </p>
            <form id="form-complete" action="" method="POST">
                @csrf
                @method('PATCH')
                <div class="flex justify-center gap-3">
                    <button type="button"
                            onclick="document.getElementById('modal-complete').classList.add('hidden')"
                            class="px-5 py-2 text-sm text-gray-600 font-medium border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white
                                   text-sm font-medium rounded-lg transition">
                        Ya, Selesaikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── MODAL HAPUS RIWAYAT ──────────────────────────── --}}
<div id="modal-delete" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4">
        <div class="px-6 py-5 text-center">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-800 mb-1">Hapus Riwayat</h3>
            <p class="text-sm text-gray-500 mb-1">
                Hapus riwayat tugas <span id="delete-task-title" class="font-semibold text-gray-700"></span>
            </p>
            <p class="text-sm text-gray-500 mb-2">
                milik <span id="delete-assignee-name" class="font-semibold text-gray-700"></span>?
            </p>
            <p class="text-xs text-red-500 mb-6">Tindakan ini tidak dapat dibatalkan.</p>
            <form id="form-delete" action="" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-center gap-3">
                    <button type="button"
                            onclick="document.getElementById('modal-delete').classList.add('hidden')"
                            class="px-5 py-2 text-sm text-gray-600 font-medium border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white
                                   text-sm font-medium rounded-lg transition">
                        Ya, Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openCompleteModal(assignmentId, taskTitle, assigneeName) {
        document.getElementById('complete-task-title').textContent = taskTitle;
        document.getElementById('complete-assignee-name').textContent = assigneeName;
        document.getElementById('form-complete').action =
            `/admin/reports/assignments/${assignmentId}/complete`;
        document.getElementById('modal-complete').classList.remove('hidden');
    }

    function openDeleteModal(assignmentId, taskTitle, assigneeName) {
        document.getElementById('delete-task-title').textContent = taskTitle;
        document.getElementById('delete-assignee-name').textContent = assigneeName;
        document.getElementById('form-delete').action =
            `/admin/reports/assignments/${assignmentId}`;
        document.getElementById('modal-delete').classList.remove('hidden');
    }

    function toggleUserDropdown() {
        const menu = document.getElementById('user-filter-menu');
        if (!menu) return;
        const isHidden = menu.classList.contains('hidden');
        if (isHidden) {
            menu.classList.remove('hidden');
            const searchInput = document.getElementById('user-search-input');
            if (searchInput) {
                searchInput.value = '';
                filterUserOptions('');
                setTimeout(() => searchInput.focus(), 50);
            }
        } else {
            menu.classList.add('hidden');
        }
    }

    function selectUser(id, label) {
        document.getElementById('filter-user-id').value = id;
        const textEl = document.getElementById('user-filter-text');
        textEl.textContent = label;
        if (id) {
            textEl.classList.remove('text-gray-500');
            textEl.classList.add('text-gray-800');
        } else {
            textEl.classList.remove('text-gray-800');
            textEl.classList.add('text-gray-500');
        }
        document.getElementById('user-filter-menu').classList.add('hidden');
    }

    function filterUserOptions(keyword) {
        const query = (keyword || '').toLowerCase().trim();
        const options = document.querySelectorAll('.user-filter-option');
        let visibleCount = 0;
        options.forEach(opt => {
            const text = opt.getAttribute('data-search') || opt.textContent.toLowerCase();
            if (!query || text.includes(query)) {
                opt.classList.remove('hidden');
                visibleCount++;
            } else {
                opt.classList.add('hidden');
            }
        });
        const noResult = document.getElementById('user-filter-no-result');
        if (noResult) {
            noResult.classList.toggle('hidden', visibleCount > 0);
        }
    }

    document.addEventListener('click', function(e) {
        const container = document.getElementById('user-filter-container');
        const menu = document.getElementById('user-filter-menu');
        if (container && menu && !container.contains(e.target)) {
            menu.classList.add('hidden');
        }
    });
</script>

@endsection
