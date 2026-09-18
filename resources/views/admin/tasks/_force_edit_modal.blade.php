{{-- ── MODAL FORCE EDIT (Admin) ─────────────────────── --}}
<div id="modal-force-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-800">Edit Tugas</h3>
            <button onclick="document.getElementById('modal-force-edit').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="form-force-edit" action="" method="POST" class="px-6 pt-4 pb-5 space-y-4">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Tugas</label>
                <input type="text" id="force-edit-title" name="title" required maxlength="200"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                              focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Deskripsi / Instruksi <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <textarea id="force-edit-description" name="description" rows="3"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                                 focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Lokasi Kantor <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <select id="force-edit-kantor" name="kantor"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                    <option value="">-- Tanpa Lokasi Kantor Khusus --</option>
                    <option value="Kantor 1">Kantor 1</option>
                    <option value="Kantor 2">Kantor 2</option>
                    <option value="Kantor 3">Kantor 3</option>
                    <option value="Kantor 4">Kantor 4</option>
                    <option value="Kantor 5">Kantor 5</option>
                    <option value="Kantor 6">Kantor 6</option>
                    <option value="Kantor 7">Kantor 7</option>
                    <option value="Kantor 8">Kantor 8</option>
                    <option value="Kantor 9">Kantor 9</option>
                    <option value="Kantor 10">Kantor 10</option>
                </select>
            </div>
            <div class="flex justify-end gap-3 pt-1">
                <button type="button"
                        onclick="document.getElementById('modal-force-edit').classList.add('hidden')"
                        class="px-5 py-2 text-sm text-gray-600 font-medium border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white
                               text-sm font-medium rounded-lg transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openForceEditModal(id, title, description, kantor) {
        document.getElementById('force-edit-title').value = title;
        document.getElementById('force-edit-description').value = description || '';
        const kantorEl = document.getElementById('force-edit-kantor');
        if (kantorEl) kantorEl.value = kantor || '';
        document.getElementById('form-force-edit').action = `/admin/tasks/${id}/force-update`;
        document.getElementById('modal-force-edit').classList.remove('hidden');
    }
</script>
