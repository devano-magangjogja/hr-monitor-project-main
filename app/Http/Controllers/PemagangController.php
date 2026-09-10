<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogsActivity;
use App\Models\Pemagang;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PemagangController extends Controller
{
    use LogsActivity;
    /**
     * Tampilkan daftar seluruh pemagang dengan filter & pencarian (Admin & Staff)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $divisi = $request->input('divisi');
        $kampus = $request->input('kampus');

        $query = Pemagang::withCount('presensis');

        // Filter Pencarian Nama atau Nomor HP
        if ($request->filled('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        // Filter Divisi
        if ($request->filled('divisi')) {
            $query->where('divisi', $divisi);
        }

        // Filter Asal Kampus / Sekolah
        if ($request->filled('kampus')) {
            $query->where('kampus', $kampus);
        }

        $pemagangs = $query->orderBy('nama_lengkap', 'asc')
            ->paginate(5)
            ->withQueryString();

        // Opsi Divisi & Kampus untuk filter
        $divisiList = Pemagang::getAllDivisi();
        $kampusList = Pemagang::select('kampus')
            ->distinct()
            ->whereNotNull('kampus')
            ->orderBy('kampus')
            ->pluck('kampus')
            ->toArray();

        // Statistik ringkas
        $today = Carbon::today()->toDateString();
        $stats = [
            'total_pemagang' => Pemagang::count(),
            'total_kampus'   => count($kampusList),
            'total_divisi'   => count($divisiList),
            'hadir_hari_ini' => Presensi::where('tanggal', $today)
                ->whereIn('keterangan', ['Lebih Awal', 'Tepat Waktu', 'Terlambat'])
                ->distinct('pemagang_id')
                ->count('pemagang_id'),
        ];

        return view('pemagang.index', compact(
            'pemagangs',
            'divisiList',
            'kampusList',
            'stats',
            'search',
            'divisi',
            'kampus'
        ));
    }

    /**
     * Simpan data pemagang baru (Admin & Staff)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'no_hp'        => ['required', 'string', 'max:15', 'unique:pemagang,no_hp'],
            'kampus'       => ['required', 'string', 'max:255'],
            'divisi'       => ['required', 'string', 'max:100'],
        ], [
            'nama_lengkap.required' => 'Nama lengkap pemagang wajib diisi.',
            'no_hp.required'        => 'Nomor WhatsApp / HP wajib diisi.',
            'no_hp.unique'          => 'Nomor WhatsApp / HP sudah terdaftar untuk pemagang lain.',
            'kampus.required'       => 'Asal kampus / sekolah wajib diisi.',
            'divisi.required'       => 'Divisi magang wajib dipilih atau diisi.',
        ]);

        $pemagang = Pemagang::create($validated);
        $this->logActivity('pemagang.created', 'Pemagang',
            "Menambahkan pemagang '{$validated['nama_lengkap']}' dari {$validated['kampus']} divisi {$validated['divisi']}",
            $pemagang
        );

        return redirect()->back()->with('success', "Pemagang {$validated['nama_lengkap']} berhasil ditambahkan.");
    }

    /**
     * Perbarui data pemagang (Admin & Staff)
     */
    public function update(Request $request, Pemagang $pemagang)
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'no_hp'        => ['required', 'string', 'max:15', Rule::unique('pemagang', 'no_hp')->ignore($pemagang->id)],
            'kampus'       => ['required', 'string', 'max:255'],
            'divisi'       => ['required', 'string', 'max:100'],
        ], [
            'nama_lengkap.required' => 'Nama lengkap pemagang wajib diisi.',
            'no_hp.required'        => 'Nomor WhatsApp / HP wajib diisi.',
            'no_hp.unique'          => 'Nomor WhatsApp / HP sudah terdaftar untuk pemagang lain.',
            'kampus.required'       => 'Asal kampus / sekolah wajib diisi.',
            'divisi.required'       => 'Divisi magang wajib dipilih atau diisi.',
        ]);

        $pemagang->update($validated);
        $this->logActivity('pemagang.updated', 'Pemagang',
            "Memperbarui data pemagang '{$pemagang->nama_lengkap}'",
            $pemagang
        );

        return redirect()->back()->with('success', "Data pemagang {$pemagang->nama_lengkap} berhasil diperbarui.");
    }

    /**
     * Hapus data pemagang beserta riwayat presensinya (Admin & Staff)
     */
    public function destroy(Pemagang $pemagang)
    {
        $nama = $pemagang->nama_lengkap;
        $pemagang->delete();
        $this->logActivity('pemagang.deleted', 'Pemagang',
            "Menghapus data pemagang '{$nama}' beserta seluruh riwayat presensi"
        );

        return redirect()->back()->with('success', "Data pemagang {$nama} beserta seluruh riwayat presensinya berhasil dihapus.");
    }

    /**
     * Hapus banyak pemagang sekaligus (Bulk Delete) — Admin & Staff
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'exists:pemagang,id'],
        ], [
            'ids.required'  => 'Tidak ada pemagang yang dipilih.',
            'ids.min'       => 'Pilih setidaknya 1 pemagang untuk dihapus.',
        ]);

        $ids   = $validated['ids'];
        $count = Pemagang::whereIn('id', $ids)->count();
        Pemagang::whereIn('id', $ids)->delete();

        $this->logActivity('pemagang.deleted', 'Pemagang',
            "Menghapus massal {$count} data pemagang sekaligus (Bulk Delete)"
        );

        return redirect()->back()->with('success', "Berhasil menghapus {$count} data pemagang beserta seluruh riwayat presensinya.");
    }
}
