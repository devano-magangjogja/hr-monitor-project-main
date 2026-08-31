@extends('layouts.app')
@section('title', 'Tugas & Akun Sosmed Saya')
@section('page-title', 'Tugas & Akun Sosmed Saya')
@section('page-subtitle', 'Kelola akun yang kamu pegang, selesaikan tugas harian/custom & unggah bukti konten')
@section('sidebar')
    @include('components.sidebar-sosmed')
@endsection

@section('content')
@include('components.notification-popup')

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- STAT CARDS                                                      --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
        <p class="text-xs font-medium text-gray-500 mb-1">Akun Tanggung Jawab</p>
        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_accounts'] }}</p>
        <p class="text-[11px] text-gray-400 mt-0.5">akun yang dipegang</p>
    </div>
    <div class="bg-white rounded-xl border {{ $stats['pending_tasks'] > 0 ? 'border-amber-300 bg-amber-50/20 ring-2 ring-amber-400/30' : 'border-gray-200' }} p-4 shadow-sm relative">
        <p class="text-xs font-medium text-gray-500 mb-1">Perlu Dikerjakan</p>
        <p class="text-2xl font-bold text-amber-600">{{ $stats['pending_tasks'] }}</p>
        <p class="text-[11px] text-amber-600 mt-0.5">belum disubmit</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
        <p class="text-xs font-medium text-gray-500 mb-1">Menunggu Verif PM</p>
        <p class="text-2xl font-bold text-blue-600">{{ $stats['waiting_pm'] }}</p>
        <p class="text-[11px] text-gray-400 mt-0.5">sudah disubmit</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
        <p class="text-xs font-medium text-gray-500 mb-1">Menunggu Final HR</p>
        <p class="text-2xl font-bold text-purple-600">{{ $stats['waiting_hr'] }}</p>
        <p class="text-[11px] text-gray-400 mt-0.5">lolos verifikasi PM</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
        <p class="text-xs font-medium text-gray-500 mb-1">Disetujui Final</p>
        <p class="text-2xl font-bold text-emerald-600">{{ $stats['approved_final'] }}</p>
        <p class="text-[11px] text-emerald-600 mt-0.5">selesai tuntas</p>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- TABS & CONTENT                                                  --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="flex border-b border-gray-200 overflow-x-auto">
        <a href="{{ route('sosmed.sosmed.index', ['tab' => 'tasks']) }}"
           class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 transition
                  {{ $tab === 'tasks' ? 'border-primary-600 text-primary-600 bg-primary-50/50' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            Daftar Tugas Saya ({{ $myTasks->count() }})
        </a>
        <a href="{{ route('sosmed.sosmed.index', ['tab' => 'accounts']) }}"
           class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 transition
                  {{ $tab === 'accounts' ? 'border-primary-600 text-primary-600 bg-primary-50/50' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            Akun yang Saya Pegang ({{ $accounts->count() }})
        </a>
    </div>

    {{-- ── TAB 1: TUGAS SOSMED SAYA ───────────────────────────────── --}}
    @if($tab === 'tasks')
    <div class="p-5">
        <div class="mb-4">
            <h3 class="text-sm font-semibold text-gray-800">Tugas Harian & Custom Penugasan Sosmed</h3>
            <p class="text-xs text-gray-500 mt-0.5">Selesaikan tugas konten dan unggah link bukti pengerjaan agar diverifikasi oleh PM penanggung jawab.</p>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-100">
            <table class="w-full text-sm min-w-[700px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Tugas</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 w-36">Akun Sosmed</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 w-36">PM Penanggung Jawab</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 w-32">Tanggal</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 w-44">Bukti / Link Konten</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 w-36">Status</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600 w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($myTasks as $t)
                    <tr class="hover:bg-gray-50/80 transition">
                        <td class="px-4 py-3.5">
                            <span class="font-semibold text-gray-800">{{ $t->title }}</span>
                            <span class="ml-1 px-1.5 py-0.5 text-[10px] rounded font-medium {{ $t->type === 'daily' ? 'bg-gray-100 text-gray-600' : 'bg-purple-50 text-purple-700' }}">
                                {{ $t->type === 'daily' ? 'Harian' : 'Custom' }}
                            </span>
                            @if($t->description)
                                <p class="text-xs text-gray-500 mt-0.5">{{ $t->description }}</p>
                            @endif
                            @if($t->rejection_note && $t->status === 'rejected')
                                <p class="text-xs text-rose-600 mt-1 font-medium bg-rose-50 p-1.5 rounded border border-rose-200">
                                    Catatan Revisi: "{{ $t->rejection_note }}"
                                </p>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-xs text-gray-700">
                            <span class="font-medium text-gray-800">{{ $t->account?->name }}</span>
                            <p class="text-gray-400">{{ $t->account?->platform }}</p>
                        </td>
                        <td class="px-4 py-3.5 text-xs text-gray-700">
                            <span class="font-medium text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-100">
                                {{ $t->account?->pmUser?->name ?? 'PM' }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-xs text-gray-500">
                            {{ $t->task_date->translatedFormat('d M Y') }}
                        </td>
                        <td class="px-4 py-3.5">
                            @if($t->link_upload)
                                <a href="{{ $t->link_upload }}" target="_blank" class="inline-flex items-center gap-1 text-xs text-primary-600 hover:underline">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    Lihat Link
                                </a>
                            @else
                                <span class="text-xs text-gray-300 italic">Belum submit</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $t->status_badge_class }}">
                                {{ $t->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-right">
                            @if(in_array($t->status, ['pending', 'rejected']))
                                <button onclick="openSubmitModal({{ $t->id }}, '{{ addslashes($t->title) }}', '{{ addslashes($t->link_upload ?? '') }}')"
                                        class="px-3 py-1 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-lg transition shadow-sm">
                                    {{ $t->status === 'rejected' ? 'Revisi & Kirim' : 'Selesaikan' }}
                                </button>
                            @else
                                <span class="text-xs text-gray-400 italic">Terkunci</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-400">Belum ada tugas sosmed yang ditugaskan kepada Anda.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ── TAB 2: AKUN SAYA ───────────────────────────────────────── --}}
    @if($tab === 'accounts')
    <div class="p-5">
        <div class="overflow-x-auto rounded-lg border border-gray-100">
            <table class="w-full text-sm min-w-[600px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Nama Akun</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 w-36">Platform</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 w-48">Link Profil</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 w-44">PM Penanggung Jawab</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($accounts as $acc)
                    <tr class="hover:bg-gray-50/80 transition">
                        <td class="px-4 py-3.5 font-semibold text-gray-800">
                            {{ $acc->name }}
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-xs font-medium border {{ $acc->platform_color }}">
                                {{ $acc->platform_icon }} {{ $acc->platform }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            @if($acc->link)
                                <a href="{{ $acc->link }}" target="_blank" class="text-xs text-primary-600 hover:underline">
                                    Buka Akun →
                                </a>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-xs text-gray-700">
                            @if($acc->pmUser)
                                <span class="font-medium text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-100">
                                    {{ $acc->pmUser->name }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-400">Belum ada akun sosial media yang di-assign kepada Anda oleh PM / HR Staff.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

{{-- ── MODAL SUBMIT TUGAS (STAFF SOSMED) ────────────────────────── --}}
<div id="modal-submit" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('modal-submit').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-bold text-gray-800">Submit Bukti Pengerjaan Konten</h3>
            <button onclick="document.getElementById('modal-submit').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="form-submit" method="POST" action="" class="p-6 space-y-4">
            @csrf @method('PATCH')
            <div>
                <p class="text-xs text-gray-500 mb-0.5">Tugas</p>
                <p id="submit-task-title" class="text-sm font-semibold text-gray-800"></p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Link / URL Hasil Konten <span class="text-red-500">*</span></label>
                <input type="url" name="link_upload" id="submit-link-upload" required placeholder="https://instagram.com/p/xxx atau https://tiktok.com/@xxx/video/xxx"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan Tambahan (Opsional)</label>
                <textarea name="description" rows="2" placeholder="Catatan singkat..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-submit').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700">Batal</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-semibold">Kirim ke PM</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openSubmitModal(taskId, title, currentLink) {
    document.getElementById('submit-task-title').textContent = title;
    document.getElementById('submit-link-upload').value = currentLink || '';
    document.getElementById('form-submit').action = `/sosmed/sosmed/tasks/${taskId}/submit`;
    document.getElementById('modal-submit').classList.remove('hidden');
}
</script>
@endpush
