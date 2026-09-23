{{-- Modal Detail Tugas --}}
<div id="modal-detail" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 flex flex-col max-h-[85vh]">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-primary-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-800">Detail Tugas</h3>
            </div>
            <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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
                <p id="detail-description" class="text-sm text-gray-600 whitespace-pre-wrap text-justify"></p>
            </div>

            {{-- Lokasi kantor (hanya tampil jika diisi) --}}
            <div id="detail-kantor-wrapper" class="hidden">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Lokasi Kantor</p>
                <p id="detail-kantor" class="text-sm text-gray-600"></p>
            </div>

            {{-- Row: Tipe (kiri) + Tanggal (pojok kanan) --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Tipe</p>
                    <div id="detail-type"></div>
                </div>
                <div class="text-right">
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Tanggal</p>
                    <p id="detail-date" class="text-sm text-gray-600"></p>
                </div>
            </div>

            {{-- Row: Sumber (kiri) + Status (pojok kanan) --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Sumber</p>
                    <p id="detail-source" class="text-sm text-gray-600"></p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Status</p>
                    <div id="detail-status" class="flex justify-end"></div>
                </div>
            </div>

            {{-- Ketentuan Foto Bukti --}}
            <div id="detail-proof-wrapper" class="hidden">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Ketentuan Foto Bukti</p>
                <div id="detail-proof"></div>
            </div>

            {{-- Penerima --}}
            <div id="detail-assignees-wrapper" class="hidden">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Penerima</p>
                    <span id="assignee-count-badge" class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full"></span>
                </div>

                {{-- Search bar --}}
                <div id="assignee-search-wrapper" class="mb-2">
                    <div class="relative">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" id="assignee-search" placeholder="Cari nama penerima..."
                            class="w-full pl-9 pr-8 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-200 focus:border-primary-300"
                            oninput="filterAssignees(this.value)">
                        <button type="button" id="assignee-search-clear" onclick="clearAssigneeSearch()"
                            class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition"
                            title="Hapus pencarian">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- List penerima (scrollable) --}}
                <div id="detail-assignees" class="space-y-2 max-h-48 overflow-y-auto pr-1"></div>
            </div>

            {{-- Catatan Penyelesaian / Deskripsi Sosmed --}}
            <div id="detail-note-wrapper" class="hidden">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Catatan Penyelesaian</p>
                <div class="bg-gray-50 rounded-lg px-4 py-3">
                    <p id="detail-note" class="text-sm text-gray-600 whitespace-pre-wrap"></p>
                </div>
            </div>

            {{-- Foto Bukti Penyelesaian (jika ada) --}}
            <div id="detail-attachment-wrapper" class="hidden">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1.5">Foto Bukti Penyelesaian</p>
                <div class="bg-gray-50 rounded-xl p-3 border border-gray-100 flex flex-col items-start gap-2">
                    <a id="detail-attachment-link" href="#" target="_blank" rel="noopener noreferrer"
                        class="block group relative overflow-hidden rounded-lg border border-gray-200 bg-white max-w-full">
                        <img id="detail-attachment-img" src="" alt="Bukti Foto"
                            class="max-h-56 max-w-full object-contain rounded-lg transition group-hover:opacity-90">
                        <div
                            class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 flex items-center justify-center transition text-white text-xs font-medium gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            <span>Lihat Gambar Penuh</span>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Link Bukti Konten Sosmed (jika ada) --}}
            <div id="detail-links-wrapper" class="hidden">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1.5">Bukti Konten (Link Upload)
                </p>
                <div id="detail-links" class="space-y-1.5 bg-gray-50 rounded-lg p-3"></div>
            </div>

            {{-- Catatan Penolakan / Revisi (jika ditolak) --}}
            <div id="detail-rejection-wrapper" class="hidden">
                <p class="text-xs font-medium text-rose-500 uppercase tracking-wider mb-1">Alasan Penolakan / Catatan
                    Revisi</p>
                <div class="bg-rose-50 border border-rose-200 rounded-lg px-4 py-3">
                    <p id="detail-rejection-note" class="text-sm text-rose-700 whitespace-pre-wrap"></p>
                </div>
            </div>

        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-gray-200 flex-shrink-0">
            <button onclick="closeDetailModal()"
                class="w-full px-4 py-2.5 border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- Modal Detail Penerima (popup kedua) --}}
<div id="modal-assignee-detail" class="hidden fixed inset-0 z-[60] flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 flex flex-col max-h-[80vh]">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 flex-shrink-0">
            <h3 class="text-base font-semibold text-gray-800">Detail Penerima</h3>
            <button onclick="closeAssigneeDetailModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="overflow-y-auto flex-1 px-5 py-4 space-y-4">
            {{-- Nama & Role --}}
            <div class="flex items-center gap-3">
                <div id="assignee-detail-avatar"
                    class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center">
                    <span id="assignee-detail-initial" class="text-sm font-semibold text-primary-600"></span>
                </div>
                <div>
                    <p id="assignee-detail-name" class="text-sm font-semibold text-gray-800"></p>
                    <p id="assignee-detail-role" class="text-xs text-gray-400"></p>
                </div>
            </div>

            {{-- Status --}}
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Status</p>
                <div id="assignee-detail-status"></div>
            </div>

            {{-- Catatan --}}
            <div id="assignee-detail-note-wrapper" class="hidden">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Catatan</p>
                <div class="bg-gray-50 rounded-lg px-4 py-3">
                    <p id="assignee-detail-note" class="text-sm text-gray-600 whitespace-pre-wrap"></p>
                </div>
            </div>

            {{-- Foto Bukti --}}
            <div id="assignee-detail-attachment-wrapper" class="hidden">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1.5">Foto Bukti</p>
                <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                    <a id="assignee-detail-attachment-link" href="#" target="_blank" rel="noopener noreferrer"
                        class="block group relative overflow-hidden rounded-lg border border-gray-200 bg-white max-w-full">
                        <img id="assignee-detail-attachment-img" src="" alt="Bukti Foto"
                            class="max-h-52 max-w-full object-contain rounded-lg transition group-hover:opacity-90">
                        <div
                            class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 flex items-center justify-center transition text-white text-xs font-medium gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            <span>Lihat Gambar Penuh</span>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Jika tidak ada catatan & foto --}}
            <div id="assignee-detail-empty" class="hidden text-center py-6">
                <p class="text-sm text-gray-400">Tidak ada catatan atau foto bukti.</p>
            </div>
        </div>

        <div class="px-5 py-4 border-t border-gray-200 flex-shrink-0">
            <button onclick="closeAssigneeDetailModal()"
                class="w-full px-4 py-2.5 border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    // Simpan data assignees sementara untuk search & detail
    let currentAssignees = [];

    function openDetailModal(data) {
        // Judul
        document.getElementById('detail-title').textContent = data.title;

        // Deskripsi
        document.getElementById('detail-description').textContent = data.description || 'Tidak ada deskripsi.';

        // Lokasi kantor
        const kantorWrapper = document.getElementById('detail-kantor-wrapper');
        const kantorEl = document.getElementById('detail-kantor');
        if (data.kantor) {
            kantorWrapper.classList.remove('hidden');
            kantorEl.textContent = data.kantor;
        } else {
            kantorWrapper.classList.add('hidden');
        }

        // Tipe
        const typeEl = document.getElementById('detail-type');
        const typeMap = {
            'default': {
                label: 'Rutin',
                cls: 'bg-purple-50 text-purple-700'
            },
            'assigned': {
                label: 'Ditugaskan',
                cls: 'bg-blue-50 text-blue-700'
            },
            'self': {
                label: 'Mandiri',
                cls: 'bg-gray-100 text-gray-600'
            },
            'sosmed': {
                label: 'Sosmed',
                cls: 'bg-pink-50 text-pink-700'
            },
        };
        const type = typeMap[data.type] || {
            label: data.type,
            cls: 'bg-gray-100 text-gray-600'
        };
        typeEl.innerHTML =
            `<span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium ${type.cls}">${type.label}</span>`;

        // Tanggal
        document.getElementById('detail-date').textContent = data.date;

        // Sumber
        document.getElementById('detail-source').textContent = data.source || '-';

        // Status
        const statusEl = document.getElementById('detail-status');
        const statusMap = {
            'completed': {
                label: 'Selesai',
                cls: 'bg-green-50 text-green-700'
            },
            'approved_hr': {
                label: 'Disetujui HR',
                cls: 'bg-emerald-50 text-emerald-700'
            },
            'done_by_staff': {
                label: data.status_label || 'Menunggu Verif PM',
                cls: data.status === 'done_by_staff' && data.no_pm_no_ast ? 'bg-orange-50 text-orange-700' :
                    'bg-amber-50 text-amber-700'
            },
            'verified_by_pm': {
                label: 'Menunggu Final HR',
                cls: 'bg-blue-50 text-blue-700'
            },
            'rejected': {
                label: 'Ditolak (Revisi)',
                cls: 'bg-rose-50 text-rose-700'
            },
            'not_done': {
                label: 'Tidak Dikerjakan',
                cls: 'bg-red-50 text-red-700'
            },
            'pending': {
                label: 'Belum Selesai',
                cls: 'bg-yellow-50 text-yellow-700'
            },
        };
        const status = statusMap[data.status] || {
            label: data.status_label || data.status,
            cls: 'bg-gray-100 text-gray-600'
        };
        statusEl.innerHTML =
            `<span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium ${status.cls}">${status.label}</span>`;

        // Penerima
        const assigneesWrapper = document.getElementById('detail-assignees-wrapper');
        const countBadge = document.getElementById('assignee-count-badge');
        const searchInput = document.getElementById('assignee-search');
        const clearBtn = document.getElementById('assignee-search-clear');

        currentAssignees = (data.assignees || []).map((a, idx) => ({
            ...a,
            _index: idx
        }));

        if (currentAssignees.length > 0) {
            assigneesWrapper.classList.remove('hidden');
            if (countBadge) {
                countBadge.textContent = `${currentAssignees.length} orang`;
            }
            if (searchInput) {
                searchInput.value = '';
            }
            if (clearBtn) {
                clearBtn.classList.add('hidden');
            }
            renderAssignees(currentAssignees);
        } else {
            assigneesWrapper.classList.add('hidden');
        }

        // Catatan
        const noteWrapper = document.getElementById('detail-note-wrapper');
        const noteEl = document.getElementById('detail-note');
        if (data.note) {
            noteWrapper.classList.remove('hidden');
            noteEl.textContent = data.note;
        } else {
            noteWrapper.classList.add('hidden');
        }

        // Bukti Link (Sosmed)
        const linksWrapper = document.getElementById('detail-links-wrapper');
        const linksEl = document.getElementById('detail-links');
        if (data.links && Array.isArray(data.links) && data.links.length > 0) {
            linksWrapper.classList.remove('hidden');
            linksEl.innerHTML = data.links.map((link, idx) => `
                <div class="flex items-center gap-2 text-xs">
                    <span class="text-gray-400 font-mono">#${idx + 1}</span>
                    <a href="${link}" target="_blank" rel="noopener noreferrer"
                       class="text-primary-600 hover:text-primary-700 hover:underline flex items-center gap-1 truncate max-w-full font-medium">
                        <span class="truncate">${link}</span>
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>
            `).join('');
        } else {
            linksWrapper.classList.add('hidden');
        }

        // Catatan Penolakan
        const rejWrapper = document.getElementById('detail-rejection-wrapper');
        const rejEl = document.getElementById('detail-rejection-note');
        if (data.rejection_note) {
            rejWrapper.classList.remove('hidden');
            rejEl.textContent = data.rejection_note;
        } else {
            rejWrapper.classList.add('hidden');
        }

        // Ketentuan Foto Bukti
        const proofWrapper = document.getElementById('detail-proof-wrapper');
        const proofEl = document.getElementById('detail-proof');
        if (proofWrapper && proofEl) {
            if (data.proof_requirement) {
                proofWrapper.classList.remove('hidden');
                const proofMap = {
                    'required': {
                        label: 'Wajib Foto Bukti',
                        cls: 'bg-rose-50 text-rose-700 border-rose-200'
                    },
                    'optional': {
                        label: 'Foto Opsional',
                        cls: 'bg-blue-50 text-blue-700 border-blue-200'
                    },
                    'none': {
                        label: 'Tanpa Foto Bukti',
                        cls: 'bg-gray-100 text-gray-600 border-gray-200'
                    },
                };
                const p = proofMap[data.proof_requirement] || {
                    label: data.proof_requirement,
                    cls: 'bg-gray-100 text-gray-600 border-gray-200'
                };
                proofEl.innerHTML =
                    `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border ${p.cls}">${p.label}</span>`;
            } else {
                proofWrapper.classList.add('hidden');
            }
        }

        // Foto Bukti Penyelesaian (level tugas)
        const attachWrapper = document.getElementById('detail-attachment-wrapper');
        const attachLink = document.getElementById('detail-attachment-link');
        const attachImg = document.getElementById('detail-attachment-img');
        if (attachWrapper && attachLink && attachImg) {
            if (data.attachment) {
                attachWrapper.classList.remove('hidden');
                attachLink.href = data.attachment;
                attachImg.src = data.attachment;
            } else {
                attachWrapper.classList.add('hidden');
                attachImg.src = '';
            }
        }

        document.getElementById('modal-detail').classList.remove('hidden');
    }

    function filterAssignees(keyword) {
        const q = (keyword || '').trim().toLowerCase();
        const clearBtn = document.getElementById('assignee-search-clear');
        if (clearBtn) {
            if (q) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }
        }

        if (!q) {
            renderAssignees(currentAssignees);
            return;
        }

        const filtered = currentAssignees.filter(a => {
            const nameMatch = a.name && a.name.toLowerCase().includes(q);
            const roleMatch = a.role && a.role.toLowerCase().includes(q);
            return nameMatch || roleMatch;
        });

        renderAssignees(filtered, q);
    }

    function clearAssigneeSearch() {
        const searchInput = document.getElementById('assignee-search');
        if (searchInput) {
            searchInput.value = '';
            searchInput.focus();
        }
        filterAssignees('');
    }

    function renderAssignees(list, query = '') {
        const assigneesEl = document.getElementById('detail-assignees');
        if (!list || list.length === 0) {
            if (query) {
                assigneesEl.innerHTML = `
                    <div class="py-6 text-center text-xs text-gray-400">
                        Tidak ada penerima dengan nama "<span class="font-medium text-gray-600">${escapeHtml(query)}</span>"
                    </div>`;
            } else {
                assigneesEl.innerHTML = `
                    <div class="py-6 text-center text-xs text-gray-400">
                        Tidak ada penerima.
                    </div>`;
            }
            return;
        }

        assigneesEl.innerHTML = list.map(a => {
            const statusCls = a.status === 'completed' ? 'bg-green-50 text-green-700' :
                (a.status === 'not_done' ? 'bg-red-50 text-red-700' : 'bg-gray-100 text-gray-600');
            const statusLabel = a.status === 'completed' ? 'Selesai' :
                (a.status === 'not_done' ? 'Tidak Dikerjakan' : 'Belum Selesai');

            return `
                <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0 hover:bg-gray-50/60 px-1 rounded transition">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-7 h-7 rounded-full bg-primary-100 flex items-center justify-center shrink-0">
                            <span class="text-xs font-semibold text-primary-600">${(a.name || '?').charAt(0).toUpperCase()}</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">${escapeHtml(a.name || '')}</p>
                            <p class="text-xs text-gray-400 truncate">${escapeHtml(a.role || '')}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium ${statusCls}">${statusLabel}</span>
                        <button type="button" onclick="openAssigneeDetailByIndex(${a._index})"
                            class="text-xs font-medium text-primary-600 hover:text-primary-700 hover:underline px-1.5 py-1 rounded transition">
                            Detail
                        </button>
                    </div>
                </div>`;
        }).join('');
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function openAssigneeDetailByIndex(index) {
        const a = currentAssignees[index];
        if (!a) return;

        // Avatar & Nama
        document.getElementById('assignee-detail-initial').textContent = (a.name || '?').charAt(0).toUpperCase();
        document.getElementById('assignee-detail-name').textContent = a.name || '-';
        document.getElementById('assignee-detail-role').textContent = a.role || '';

        // Status
        const statusCls = a.status === 'completed' ? 'bg-green-50 text-green-700' :
            (a.status === 'not_done' ? 'bg-red-50 text-red-700' : 'bg-gray-100 text-gray-600');
        const statusLabel = a.status === 'completed' ? 'Selesai' :
            (a.status === 'not_done' ? 'Tidak Dikerjakan' : 'Belum Selesai');
        document.getElementById('assignee-detail-status').innerHTML =
            `<span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium ${statusCls}">${statusLabel}</span>`;

        // Catatan
        const noteWrapper = document.getElementById('assignee-detail-note-wrapper');
        const noteEl = document.getElementById('assignee-detail-note');
        if (a.note && String(a.note).trim() !== '') {
            noteWrapper.classList.remove('hidden');
            noteEl.textContent = a.note;
        } else {
            noteWrapper.classList.add('hidden');
        }

        // Foto Bukti
        const attachWrapper = document.getElementById('assignee-detail-attachment-wrapper');
        const attachLink = document.getElementById('assignee-detail-attachment-link');
        const attachImg = document.getElementById('assignee-detail-attachment-img');
        if (a.attachment) {
            attachWrapper.classList.remove('hidden');
            attachLink.href = a.attachment;
            attachImg.src = a.attachment;
        } else {
            attachWrapper.classList.add('hidden');
            attachImg.src = '';
        }

        // Empty state
        const emptyEl = document.getElementById('assignee-detail-empty');
        if ((!a.note || String(a.note).trim() === '') && !a.attachment) {
            emptyEl.classList.remove('hidden');
        } else {
            emptyEl.classList.add('hidden');
        }

        document.getElementById('modal-assignee-detail').classList.remove('hidden');
    }

    function openAssigneeDetailByName(name) {
        const idx = currentAssignees.findIndex(x => x.name === name);
        if (idx !== -1) {
            openAssigneeDetailByIndex(idx);
        }
    }

    function closeDetailModal() {
        document.getElementById('modal-detail').classList.add('hidden');
    }

    function closeAssigneeDetailModal() {
        document.getElementById('modal-assignee-detail').classList.add('hidden');
    }
</script>
