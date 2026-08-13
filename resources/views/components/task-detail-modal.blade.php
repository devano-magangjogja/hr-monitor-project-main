{{-- Modal Detail Tugas --}}
<div id="modal-detail" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 flex flex-col max-h-[85vh]">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-primary-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-800">Detail Tugas</h3>
            </div>
            <button onclick="closeDetailModal()"
                    class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Content --}}
        <div class="overflow-y-auto flex-1 px-6 py-5 space-y-4">

            {{-- Judul --}}
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Judul</p>
                <p id="detail-title" class="text-sm font-semibold text-gray-800"></p>
            </div>

            {{-- Deskripsi --}}
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Deskripsi</p>
                <p id="detail-description" class="text-sm text-gray-600 whitespace-pre-wrap"></p>
            </div>

            {{-- Row: Tipe + Tanggal --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Tipe</p>
                    <div id="detail-type"></div>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Tanggal</p>
                    <p id="detail-date" class="text-sm text-gray-600"></p>
                </div>
            </div>

            {{-- Row: Sumber + Status --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Sumber</p>
                    <p id="detail-source" class="text-sm text-gray-600"></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Status</p>
                    <div id="detail-status"></div>
                </div>
            </div>

            {{-- Penerima (hanya tampil jika ada) --}}
            <div id="detail-assignees-wrapper" class="hidden">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Penerima</p>
                <div id="detail-assignees" class="space-y-2"></div>
            </div>

            {{-- Catatan Penyelesaian --}}
            <div id="detail-note-wrapper" class="hidden">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Catatan Penyelesaian</p>
                <div class="bg-gray-50 rounded-lg px-4 py-3">
                    <p id="detail-note" class="text-sm text-gray-600 whitespace-pre-wrap"></p>
                </div>
            </div>

        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-gray-200 flex-shrink-0">
            <button onclick="closeDetailModal()"
                    class="w-full px-4 py-2.5 border border-gray-300 text-gray-600 text-sm
                           font-medium rounded-lg hover:bg-gray-50 transition">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    function openDetailModal(data) {
        // Judul
        document.getElementById('detail-title').textContent = data.title;

        // Deskripsi
        document.getElementById('detail-description').textContent = data.description || 'Tidak ada deskripsi.';

        // Tipe
        const typeEl = document.getElementById('detail-type');
        const typeMap = {
            'default':  { label: 'Rutin', cls: 'bg-purple-50 text-purple-700' },
            'assigned': { label: 'Ditugaskan', cls: 'bg-blue-50 text-blue-700' },
            'self':     { label: 'Mandiri', cls: 'bg-gray-100 text-gray-600' },
        };
        const type = typeMap[data.type] || { label: data.type, cls: 'bg-gray-100 text-gray-600' };
        typeEl.innerHTML = `<span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium ${type.cls}">${type.label}</span>`;

        // Tanggal
        document.getElementById('detail-date').textContent = data.date;

        // Sumber
        document.getElementById('detail-source').textContent = data.source || '-';

        // Status
        const statusEl = document.getElementById('detail-status');
        const statusMap = {
            'completed': { label: 'Selesai', cls: 'bg-green-50 text-green-700' },
            'not_done':  { label: 'Tidak Dikerjakan', cls: 'bg-red-50 text-red-700' },
            'pending':   { label: 'Belum Selesai', cls: 'bg-yellow-50 text-yellow-700' },
        };
        const status = statusMap[data.status] || { label: '-', cls: 'bg-gray-100 text-gray-600' };
        statusEl.innerHTML = `<span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium ${status.cls}">${status.label}</span>`;

        // Penerima
        const assigneesWrapper = document.getElementById('detail-assignees-wrapper');
        const assigneesEl     = document.getElementById('detail-assignees');
        if (data.assignees && data.assignees.length > 0) {
            assigneesWrapper.classList.remove('hidden');
            assigneesEl.innerHTML = data.assignees.map(a => {
                const statusCls = a.status === 'completed'
                    ? 'bg-green-50 text-green-700'
                    : (a.status === 'not_done' ? 'bg-red-50 text-red-700' : 'bg-gray-100 text-gray-600');
                const statusLabel = a.status === 'completed'
                    ? 'Selesai'
                    : (a.status === 'not_done' ? 'Tidak Dikerjakan' : 'Belum Selesai');
                return `
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-primary-100 flex items-center justify-center">
                                <span class="text-xs font-semibold text-primary-600">
                                    ${a.name.charAt(0).toUpperCase()}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">${a.name}</p>
                                <p class="text-xs text-gray-400">${a.role}</p>
                            </div>
                        </div>
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium ${statusCls}">
                            ${statusLabel}
                        </span>
                    </div>`;
            }).join('');
        } else {
            assigneesWrapper.classList.add('hidden');
        }

        // Catatan
        const noteWrapper = document.getElementById('detail-note-wrapper');
        const noteEl      = document.getElementById('detail-note');
        if (data.note) {
            noteWrapper.classList.remove('hidden');
            noteEl.textContent = data.note;
        } else {
            noteWrapper.classList.add('hidden');
        }

        document.getElementById('modal-detail').classList.remove('hidden');
    }

    function closeDetailModal() {
        document.getElementById('modal-detail').classList.add('hidden');
    }
</script>