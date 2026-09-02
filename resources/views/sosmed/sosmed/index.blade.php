@extends('layouts.app')
@section('title', 'Tugas & Akun Sosmed Saya')
@section('page-title', 'Tugas & Akun Sosmed Saya')
@section('page-subtitle', 'Kelola akun yang kamu pegang dan selesaikan tugas harian untuk hari ini')
@section('sidebar')
    @include('components.sidebar-sosmed')
@endsection

@section('content')
    @include('components.notification-popup')

    {{-- ═══ STAT CARDS ═══════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs font-medium text-gray-500 mb-1">Akun Tanggung Jawab</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total_accounts'] }}</p>
            <p class="text-[11px] text-gray-400 mt-0.5">akun yang dipegang</p>
        </div>
        <div class="bg-white rounded-xl border {{ $stats['pending_tasks'] > 0 ? 'border-amber-300 bg-amber-50/20 ring-2 ring-amber-400/30' : 'border-gray-200' }} p-4 shadow-sm relative">
            <p class="text-xs font-medium text-gray-500 mb-1">Perlu Diurus Hari Ini</p>
            <p class="text-2xl font-bold text-amber-600">{{ $stats['pending_tasks'] }}</p>
            <p class="text-[11px] text-amber-600 mt-0.5">belum disubmit</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs font-medium text-gray-500 mb-1">Menunggu Verif PM</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['waiting_pm'] }}</p>
            <p class="text-[11px] text-gray-400 mt-0.5">sudah disubmit hari ini</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs font-medium text-gray-500 mb-1">Menunggu Final HR</p>
            <p class="text-2xl font-bold text-purple-600">{{ $stats['waiting_hr'] }}</p>
            <p class="text-[11px] text-gray-400 mt-0.5">lolos verifikasi PM</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs font-medium text-gray-500 mb-1">Disetujui Final</p>
            <p class="text-2xl font-bold text-emerald-600">{{ $stats['approved_final'] }}</p>
            <p class="text-[11px] text-emerald-600 mt-0.5">selesai tuntas hari ini</p>
        </div>
    </div>

    {{-- ═══ TABEL AKUN ════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50 flex justify-between items-center flex-wrap gap-2">
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Daftar Akun & Status Tugas Harian</h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    Klik kotak atau tombol <strong>Submit Bukti</strong> untuk mengirim link hasil konten hari ini ke PM.
                    Kamu bisa mengirim lebih dari satu link bukti.
                </p>
            </div>
            <div class="text-xs bg-indigo-50 border border-indigo-200 text-indigo-700 px-3 py-1.5 rounded-lg font-medium shrink-0">
                Hari Ini: <span class="font-bold">{{ now()->translatedFormat('d M Y') }}</span>
            </div>
        </div>

        <div class="p-4 sm:p-6">
            <div class="overflow-x-auto rounded-lg border border-gray-100">
                <table class="w-full text-sm min-w-[820px] table-fixed">
                    <colgroup>
                        <col class="w-14">
                        <col class="w-44">
                        <col class="w-32">
                        <col class="w-28">
                        <col class="w-36">
                        <col class="w-44">
                        <col class="w-32">
                    </colgroup>
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            <th class="px-3 py-3 text-center">✓</th>
                            <th class="px-4 py-3 text-left">Nama Akun</th>
                            <th class="px-4 py-3 text-left">Platform</th>
                            <th class="px-4 py-3 text-left">Link Profil</th>
                            <th class="px-4 py-3 text-left">Project Manager</th>
                            <th class="px-4 py-3 text-left">Bukti Konten</th>
                            <th class="px-4 py-3 text-center">Status / Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($accounts as $acc)
                            @php
                                $todayTask  = $todayTasks[$acc->id] ?? null;
                                $status     = $todayTask?->status ?? 'pending';
                                $isSubmitted = in_array($status, ['done_by_staff', 'verified_by_pm', 'approved_hr']);
                                $isRejected  = $status === 'rejected';
                            @endphp
                            <tr class="hover:bg-gray-50/60 transition align-top">

                                {{-- Ceklis --}}
                                <td class="px-3 py-3.5 text-center">
                                    @if($isSubmitted)
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 border border-emerald-300 text-emerald-600 text-xs" title="Sudah diurus">✓</span>
                                    @else
                                        <button type="button"
                                            onclick="openSubmitModal({{ $acc->id }}, '{{ addslashes($acc->name) }}', '{{ addslashes($todayTask?->description ?? '') }}')"
                                            class="inline-flex items-center justify-center w-6 h-6 rounded-md border-2 {{ $isRejected ? 'border-rose-400 bg-rose-50 hover:bg-rose-100' : 'border-gray-300 bg-white hover:border-primary-500' }} transition"
                                            title="{{ $isRejected ? 'Klik untuk revisi' : 'Klik untuk submit bukti' }}">
                                        </button>
                                    @endif
                                </td>

                                {{-- Nama Akun --}}
                                <td class="px-4 py-3.5">
                                    <p class="font-semibold text-gray-800 truncate" title="{{ $acc->name }}">{{ $acc->name }}</p>
                                    @if($isRejected && $todayTask?->rejection_note)
                                        <p class="text-xs text-rose-600 mt-1 bg-rose-50 px-1.5 py-1 rounded border border-rose-200 leading-snug">
                                            ↩ "{{ $todayTask->rejection_note }}"
                                        </p>
                                    @endif
                                </td>

                                {{-- Platform --}}
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-medium border {{ $acc->platform_color }}">
                                        {{ $acc->platform_icon }} {{ $acc->platform }}
                                    </span>
                                </td>

                                {{-- Link Profil --}}
                                <td class="px-4 py-3.5">
                                    @if($acc->link)
                                        <a href="{{ $acc->link }}" target="_blank"
                                            class="inline-flex items-center gap-1 text-xs text-primary-600 hover:underline">
                                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                            Buka Profil
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-300">—</span>
                                    @endif
                                </td>

                                {{-- PM --}}
                                <td class="px-4 py-3.5">
                                    @if($acc->pmUser)
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-100 max-w-full truncate" title="{{ $acc->pmUser->name }}">
                                            {{ $acc->pmUser->name }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-300">—</span>
                                    @endif
                                </td>

                                {{-- Bukti Konten --}}
                                <td class="px-4 py-3.5">
                                    @if($todayTask && $todayTask->hasLinks())
                                        <button type="button"
                                            onclick="openLinksPopup({{ json_encode($todayTask->link_upload) }}, '{{ addslashes($acc->name) }}')"
                                            class="inline-flex items-center gap-1 text-xs text-primary-600 hover:underline font-medium">
                                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                            </svg>
                                            {{ $todayTask->link_count }} link bukti
                                        </button>
                                        @if($todayTask->description)
                                            <p class="text-[10px] text-gray-400 mt-0.5 truncate" title="{{ $todayTask->description }}">{{ $todayTask->description }}</p>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-300">—</span>
                                    @endif
                                </td>

                                {{-- Status / Aksi --}}
                                <td class="px-4 py-3.5 text-center">
                                    @if($isSubmitted)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold leading-snug {{ $todayTask->status_badge_class }}">
                                            {{ $todayTask->status_label }}
                                        </span>
                                    @else
                                        <button type="button"
                                            onclick="openSubmitModal({{ $acc->id }}, '{{ addslashes($acc->name) }}', '{{ addslashes($todayTask?->description ?? '') }}')"
                                            class="px-3 py-1 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-lg transition shadow-sm">
                                            {{ $isRejected ? 'Revisi' : 'Submit Bukti' }}
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-sm text-gray-400">
                                    Belum ada akun sosial media yang di-assign kepada Anda oleh PM / HR Staff.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ═══ MODAL: SUBMIT BUKTI (MULTI-LINK) ═══════════════════════════════ --}}
    <div id="modal-submit" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('modal-submit').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg z-10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-bold text-gray-800">Tandai Selesai & Kirim Bukti</h3>
                <button onclick="document.getElementById('modal-submit').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="form-submit" method="POST" action="" class="p-6 pt-1 space-y-4">
                @csrf
                <div>
                    <p class="text-xs text-gray-500 mb-0.5">Nama Akun Sosmed</p>
                    <p id="submit-account-name" class="text-sm font-semibold text-gray-800"></p>
                </div>

                {{-- Multi-link input --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Link / URL Hasil Konten <span class="text-red-500">*</span>
                        <span class="font-normal text-gray-400 ml-1">(bisa lebih dari satu)</span>
                    </label>
                    <div id="links-container" class="space-y-2">
                        <div class="flex gap-2 link-row">
                            <input type="url" name="links[]" required
                                placeholder="https://instagram.com/p/xxx atau https://tiktok.com/@xxx/video/xxx"
                                class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                            <button type="button" onclick="removeLinkRow(this)"
                                class="text-gray-300 hover:text-rose-500 px-1 transition hidden remove-btn">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                    <button type="button" onclick="addLinkRow()"
                        class="mt-2 inline-flex items-center gap-1 text-xs text-primary-600 hover:text-primary-700 font-medium">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Link Lain
                    </button>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan Tambahan (Opsional)</label>
                    <textarea name="description" id="submit-description" rows="2"
                        placeholder="Brief konten / keterangan..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none"></textarea>
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="document.getElementById('modal-submit').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700">Batal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-semibold">
                        Tandai Selesai
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══ MODAL: DETAIL LINKS POPUP ════════════════════════════════════ --}}
    <div id="modal-links" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('modal-links').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <div>
                    <h3 class="text-base font-bold text-gray-800">Detail Bukti Konten</h3>
                    <p id="links-popup-title" class="text-xs text-gray-500 mt-0.5"></p>
                </div>
                <button onclick="document.getElementById('modal-links').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div id="links-popup-body" class="p-6 space-y-2 max-h-80 overflow-y-auto"></div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
// ── Submit Modal ──────────────────────────────────────────────────────────────
function openSubmitModal(accountId, accountName, existingDesc) {
    document.getElementById('submit-account-name').textContent = accountName;
    document.getElementById('submit-description').value = existingDesc || '';
    document.getElementById('form-submit').action = `/sosmed/sosmed/accounts/${accountId}/submit`;

    // Reset link rows ke 1 kosong
    const container = document.getElementById('links-container');
    container.innerHTML = `
        <div class="flex gap-2 link-row">
            <input type="url" name="links[]" required
                placeholder="https://instagram.com/p/xxx atau https://tiktok.com/@xxx/video/xxx"
                class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
            <button type="button" onclick="removeLinkRow(this)"
                class="text-gray-300 hover:text-rose-500 px-1 transition hidden remove-btn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>`;

    document.getElementById('modal-submit').classList.remove('hidden');
}

function addLinkRow() {
    const container = document.getElementById('links-container');
    const row = document.createElement('div');
    row.className = 'flex gap-2 link-row';
    row.innerHTML = `
        <input type="url" name="links[]"
            placeholder="https://tiktok.com/@xxx/video/xxx"
            class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
        <button type="button" onclick="removeLinkRow(this)"
            class="text-gray-300 hover:text-rose-500 px-1 transition remove-btn">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>`;
    container.appendChild(row);
    updateRemoveButtons();
}

function removeLinkRow(btn) {
    btn.closest('.link-row').remove();
    updateRemoveButtons();
}

function updateRemoveButtons() {
    const rows = document.querySelectorAll('#links-container .link-row');
    rows.forEach(r => {
        const btn = r.querySelector('.remove-btn');
        if (btn) btn.classList.toggle('hidden', rows.length === 1);
    });
}

// ── Links Popup ───────────────────────────────────────────────────────────────
function openLinksPopup(links, title) {
    document.getElementById('links-popup-title').textContent = title;
    const body = document.getElementById('links-popup-body');
    body.innerHTML = '';

    if (!links || links.length === 0) {
        body.innerHTML = '<p class="text-sm text-gray-400 text-center">Tidak ada link bukti.</p>';
    } else {
        links.forEach((url, i) => {
            const item = document.createElement('a');
            item.href = url;
            item.target = '_blank';
            item.rel = 'noopener noreferrer';
            item.className = 'flex items-start gap-2.5 p-3 rounded-lg border border-gray-100 hover:border-primary-300 hover:bg-primary-50/50 transition group';
            item.innerHTML = `
                <span class="flex-shrink-0 w-5 h-5 rounded-full bg-primary-100 text-primary-700 text-[10px] font-bold flex items-center justify-center mt-0.5">${i + 1}</span>
                <span class="text-xs text-primary-700 group-hover:underline break-all leading-relaxed">${url}</span>
                <svg class="w-3.5 h-3.5 flex-shrink-0 text-gray-400 group-hover:text-primary-600 mt-0.5 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>`;
            body.appendChild(item);
        });
    }

    document.getElementById('modal-links').classList.remove('hidden');
}
</script>
@endpush
