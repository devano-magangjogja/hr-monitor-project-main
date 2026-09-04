@props([
    'tasks',
    'role' => 'staff',
])

@php
    $total     = $tasks->count();
    $completed = 0;
    $pending   = 0;
    $notDone   = 0;
    foreach ($tasks as $task) {
        $s = $task->assignments->first()?->is_completed ?? 'pending';
        if (in_array($s, ['completed', 'approved_hr'])) {
            $completed++;
        } elseif ($s === 'not_done') {
            $notDone++;
        } else {
            $pending++;
        }
    }
@endphp

{{-- Stat ringkas --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 px-4 py-3 flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-primary-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-400">Total Tugas</p>
            <p class="text-lg font-bold text-gray-800">{{ $total }}</p>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 px-4 py-3 flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-400">Selesai</p>
            <p class="text-lg font-bold text-emerald-600">{{ $completed }}</p>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 px-4 py-3 flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-yellow-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-400">Pending / Berjalan</p>
            <p class="text-lg font-bold text-yellow-600">{{ $pending }}</p>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 px-4 py-3 flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-400">Tidak Dikerjakan</p>
            <p class="text-lg font-bold text-red-600">{{ $notDone }}</p>
        </div>
    </div>
</div>

{{-- Tabel Semua Tugas --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[640px]">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <th class="text-left px-6 py-3.5 w-48">Judul</th>
                    <th class="text-left px-6 py-3.5 w-28">Kantor</th>
                    <th class="text-left px-6 py-3.5">Deskripsi</th>
                    <th class="text-left px-6 py-3.5 w-28">Sumber</th>
                    <th class="text-left px-6 py-3.5 w-36">Status</th>
                    <th class="text-right px-6 py-3.5 w-28">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($tasks as $task)
                    @php
                        $assignment = $task->assignments->first();
                        $status     = $assignment?->is_completed ?? 'pending';
                        $isSosmed   = !empty($task->is_sosmed);
                        $isPastUnapproved = !empty($task->is_past_unapproved);
                    @endphp
                    <tr class="hover:bg-gray-50 transition {{ $isPastUnapproved ? 'bg-amber-50/20' : '' }}"
                        data-task="{{ json_encode([
                            'title'          => $task->title,
                            'kantor'         => $task->kantor,
                            'description'    => $task->description ?? '',
                            'type'           => $task->type,
                            'date'           => $task->task_date->translatedFormat('d M Y'),
                            'source'         => $isSosmed ? 'Sosmed' : ($task->type === 'self' ? 'Mandiri' : ($task->type === 'default' ? 'Rutin' : ($task->creator?->name ?? 'Admin'))),
                            'status'         => $status,
                            'note'           => $assignment?->note ?? '',
                            'links'          => $task->links ?? [],
                            'rejection_note' => $task->rejection_note ?? null,
                            'assignees'      => [],
                        ]) }}">

                        {{-- Judul --}}
                        <td class="px-6 py-4 w-52">
                            <div class="font-medium text-gray-800 truncate max-w-[210px]" title="{{ $task->title }}">
                                {{ $task->title }}
                            </div>
                            <div class="flex items-center gap-1.5 flex-wrap mt-1">
                                @if(!empty($task->platform))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium border {{ $task->platform_color ?? 'bg-gray-100 text-gray-700 border-gray-200' }}">
                                        {{ $task->platform }}
                                    </span>
                                @endif
                                @if($isPastUnapproved)
                                    <span class="inline-flex items-center gap-1 text-[10px] text-amber-700 bg-amber-100/80 px-2 py-0.5 rounded font-normal">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $task->task_date->translatedFormat('d M') }} (Belum Final)
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- Kantor --}}
                        <td class="px-6 py-4 w-28">
                            @if($task->kantor)
                                <span class="text-xs text-gray-700 bg-gray-100 px-2 py-1 rounded font-medium">
                                    {{ $task->kantor }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400 pl-1">-</span>
                            @endif
                        </td>

                        {{-- Deskripsi --}}
                        <td class="px-6 py-4">
                            <div class="truncate max-w-[240px] text-gray-500 text-xs" title="{{ $task->description ?? '-' }}">
                                @if($task->description)
                                    {!! linkify(e($task->description)) !!}
                                @else
                                    -
                                @endif
                            </div>
                        </td>

                        {{-- Sumber --}}
                        <td class="px-6 py-4 w-28">
                            @if($isSosmed)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-pink-50 text-pink-700 border border-pink-200">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069z"/>
                                    </svg>
                                    Sosmed
                                </span>
                            @elseif($task->type === 'self')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    Mandiri
                                </span>
                            @elseif($task->type === 'default')
                                @if(str_contains(strtolower($task->title), 'presensi'))
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-teal-50 text-teal-700 border border-teal-200">
                                        Presensi
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-200">
                                        Rutin
                                    </span>
                                @endif
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200"
                                      title="{{ $task->creator?->name ?? 'Admin' }}">
                                    {{ Str::limit($task->creator?->name ?? 'Admin', 10) }}
                                </span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4 w-36">
                            <x-task-status-badge :status="$status" :completedAt="$assignment?->completed_at" />
                        </td>

                        {{-- Aksi --}}
                        <td class="px-6 py-4 w-28">
                            <div class="flex items-center justify-end gap-1.5 whitespace-nowrap">
                                {{-- Tombol Detail --}}
                                <button onclick="openDetailModal(JSON.parse(this.closest('tr').dataset.task))"
                                        class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                                        title="Lihat Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>

                                {{-- Aksi Sosmed --}}
                                @if($isSosmed)
                                    @if(in_array($status, ['pending', 'rejected']))
                                        <a href="{{ $task->action_url ?? '#' }}"
                                           class="inline-flex items-center gap-1 px-2.5 py-1 bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium rounded-lg transition shadow-sm"
                                           title="Submit Bukti Konten">
                                            <span>Submit</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                            </svg>
                                        </a>
                                    @else
                                        <a href="{{ $task->action_url ?? '#' }}"
                                           class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition"
                                           title="Lihat Progres Sosmed">
                                            <span>Lihat</span>
                                        </a>
                                    @endif

                                {{-- Aksi Regular / Default Task --}}
                                @else
                                    @if(str_contains(strtolower($task->title), 'presensi'))
                                        @if(in_array($role, ['staff', 'assistant']))
                                            <a href="{{ route($role . '.presensi.index') }}"
                                               class="p-1.5 text-teal-600 hover:bg-teal-50 rounded-lg transition"
                                               title="Buka Halaman Presensi">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                                </svg>
                                            </a>
                                        @endif
                                    @endif

                                    @if($status === 'pending')
                                        <button onclick="openCompleteModal({{ $task->id }}, '{{ addslashes($task->title) }}')"
                                                class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition"
                                                title="Tandai Selesai">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">
                            Tidak ada tugas hari ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Checklist Selesai --}}
<div id="modal-complete" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <div>
                <h3 class="text-base font-semibold text-gray-800">Tandai Selesai</h3>
                <p id="complete-task-title" class="text-xs text-gray-500 mt-0.5 truncate max-w-xs"></p>
            </div>
            <button onclick="document.getElementById('modal-complete').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="form-complete" action="" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Catatan Penyelesaian
                    <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <textarea name="note" rows="4"
                          placeholder="Tuliskan laporan singkat atau catatan penyelesaian tugas ini..."
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                                 focus:outline-none focus:ring-2 focus:ring-green-500 resize-none"></textarea>
                <p class="mt-1 text-xs text-gray-400">Setelah ditandai selesai, tugas tidak dapat diubah kembali.</p>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button"
                        onclick="document.getElementById('modal-complete').classList.add('hidden')"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 font-medium">
                    Batal
                </button>
                <button type="submit"
                        class="flex items-center gap-2 px-5 py-2 bg-green-600 hover:bg-green-700
                               text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Selesai
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCompleteModal(id, title) {
        document.getElementById('complete-task-title').textContent = title;
        document.getElementById('form-complete').action = `/{{ $role }}/tasks/all/${id}/complete`;
        document.getElementById('modal-complete').classList.remove('hidden');
    }
</script>
