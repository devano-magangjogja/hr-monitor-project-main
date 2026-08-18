<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Pemagang;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    /**
     * Tampilkan halaman presensi pemagang hari ini (atau sesuai tanggal filter)
     */
    public function index(Request $request)
    {
        // Default tanggal adalah hari ini
        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $isToday = $tanggal === Carbon::today()->format('Y-m-d');
        $formattedDate = Carbon::parse($tanggal)->locale('id')->translatedFormat('l, d F Y');

        // Query dasar berdasarkan tanggal yang dipilih
        $baseQuery = Presensi::with('pemagang')->where('tanggal', $tanggal);

        // Filter Pencarian (Nama, NIM, Kampus)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $baseQuery->whereHas('pemagang', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%")
                  ->orWhere('kampus', 'like', "%{$search}%");
            });
        }

        // Filter Shift
        if ($request->filled('shift')) {
            $baseQuery->where('shift', $request->input('shift'));
        }

        // Filter Divisi
        if ($request->filled('divisi')) {
            $divisi = $request->input('divisi');
            $baseQuery->whereHas('pemagang', function ($q) use ($divisi) {
                $q->where('divisi', $divisi);
            });
        }

        // 1. Tabel Hadir (Lebih Awal, Tepat Waktu, Terlambat) - 5 data per halaman
        $presensiHadir = (clone $baseQuery)
            ->whereIn('keterangan', ['Lebih Awal', 'Tepat Waktu', 'Terlambat'])
            ->orderBy('waktu_masuk', 'asc')
            ->paginate(5, ['*'], 'page_hadir')
            ->withQueryString()
            ->fragment('tabel-hadir');

        // 2. Tabel Tidak Hadir - 5 data per halaman
        $presensiTidakHadir = (clone $baseQuery)
            ->where('keterangan', 'Tidak Hadir')
            ->orderBy('id', 'desc')
            ->paginate(5, ['*'], 'page_tidak_hadir')
            ->withQueryString()
            ->fragment('tabel-tidak-hadir');

        // Statistik Ringkasan untuk TANGGAL YANG DIPILIH
        $stats = [
            'total_pemagang' => Pemagang::count(),
            'total_presensi' => Presensi::where('tanggal', $tanggal)->count(),
            'datang_awal'    => Presensi::where('tanggal', $tanggal)->where('keterangan', 'Lebih Awal')->count(),
            'tepat_waktu'    => Presensi::where('tanggal', $tanggal)->where('keterangan', 'Tepat Waktu')->count(),
            'terlambat'      => Presensi::where('tanggal', $tanggal)->where('keterangan', 'Terlambat')->count(),
            'tidak_hadir'    => Presensi::where('tanggal', $tanggal)->where('keterangan', 'Tidak Hadir')->count(),
            'total_hadir'    => Presensi::where('tanggal', $tanggal)->whereIn('keterangan', ['Lebih Awal', 'Tepat Waktu', 'Terlambat'])->count(),
        ];

        // List semua pemagang untuk dropdown modal
        $pemagangs = Pemagang::orderBy('nama_lengkap', 'asc')->get();

        // List opsi divisi unik
        $divisiList = Pemagang::select('divisi')->distinct()->orderBy('divisi')->pluck('divisi');

        return view('staff.presensi.presensi', compact(
            'presensiHadir',
            'presensiTidakHadir',
            'stats',
            'pemagangs',
            'divisiList',
            'tanggal',
            'isToday',
            'formattedDate'
        ));
    }

    /**
     * Simpan catatan presensi baru (otomatis terkunci untuk hari ini)
     */
    public function store(Request $request)
    {
        $today = Carbon::today()->format('Y-m-d');

        $validated = $request->validate([
            'pemagang_id' => ['required', 'exists:pemagang,id'],
            'shift'       => ['required', 'in:Pagi,Middle,Siang'],
            'waktu_masuk' => ['required'],
            'keterangan'  => ['required', 'in:Lebih Awal,Tepat Waktu,Terlambat,Tidak Hadir'],
            'notes'       => ['nullable', 'string', 'max:500'],
        ]);

        // Tanggal otomatis dikunci pada hari ini
        $validated['tanggal'] = $today;
        $validated['notes'] = $validated['notes'] ?? ($validated['keterangan'] === 'Tidak Hadir' ? 'Tidak hadir tanpa keterangan' : 'Presensi tercatat');

        Presensi::create($validated);

        return redirect()->route('staff.presensi.index', ['tanggal' => $today])
            ->with('success', 'Catatan presensi pemagang hari ini berhasil disimpan.');
    }

    /**
     * Perbarui data presensi
     */
    public function update(Request $request, Presensi $presensi)
    {
        $validated = $request->validate([
            'shift'       => ['required', 'in:Pagi,Middle,Siang'],
            'waktu_masuk' => ['required'],
            'keterangan'  => ['required', 'in:Lebih Awal,Tepat Waktu,Terlambat,Tidak Hadir'],
            'notes'       => ['nullable', 'string', 'max:500'],
        ]);

        $validated['notes'] = $validated['notes'] ?? '-';

        $presensi->update($validated);

        return redirect()->route('staff.presensi.index', ['tanggal' => $presensi->tanggal])
            ->with('success', 'Data presensi berhasil diperbarui.');
    }

    /**
     * Hapus catatan presensi
     */
    public function destroy(Presensi $presensi)
    {
        $tanggal = $presensi->tanggal;
        $presensi->delete();

        return redirect()->route('staff.presensi.index', ['tanggal' => $tanggal])
            ->with('success', 'Catatan presensi berhasil dihapus.');
    }

    /**
     * Tampilkan halaman laporan dan rekapitulasi presensi pemagang
     */
    public function laporan(Request $request)
    {
        $queryPemagang = Pemagang::with('presensis');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $queryPemagang->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%")
                  ->orWhere('kampus', 'like', "%{$search}%");
            });
        }

        if ($request->filled('divisi')) {
            $queryPemagang->where('divisi', $request->input('divisi'));
        }

        // Tabel 1: Rekapitulasi per Pemagang (10 per halaman)
        $pemagangs = $queryPemagang->orderBy('nama_lengkap', 'asc')
            ->paginate(10, ['*'], 'page_rekap')
            ->withQueryString()
            ->fragment('tabel-rekap-pemagang');

        // Hitung metrik per pemagang
        $rekapPemagang = $pemagangs->through(function ($p) {
            $total = $p->presensis->count();
            $awal = $p->presensis->where('keterangan', 'Lebih Awal')->count();
            $tepat = $p->presensis->where('keterangan', 'Tepat Waktu')->count();
            $terlambat = $p->presensis->where('keterangan', 'Terlambat')->count();
            $tidakHadir = $p->presensis->where('keterangan', 'Tidak Hadir')->count();
            $hadirDisiplin = $awal + $tepat;
            $rate = $total > 0 ? round(($hadirDisiplin / $total) * 100, 1) : 0;

            return (object) [
                'pemagang'     => $p,
                'total'        => $total,
                'datang_awal'  => $awal,
                'tepat_waktu'  => $tepat,
                'terlambat'    => $terlambat,
                'tidak_hadir'  => $tidakHadir,
                'rate'         => $rate,
            ];
        });

        // Global stats
        $totalPresensi = Presensi::count();
        $totalAwal = Presensi::where('keterangan', 'Lebih Awal')->count();
        $totalTepat = Presensi::where('keterangan', 'Tepat Waktu')->count();
        $totalTerlambat = Presensi::where('keterangan', 'Terlambat')->count();
        $totalTidakHadir = Presensi::where('keterangan', 'Tidak Hadir')->count();
        $avgDisiplinRate = $totalPresensi > 0 ? round((($totalAwal + $totalTepat) / $totalPresensi) * 100, 1) : 0;

        $stats = [
            'total_pemagang'  => Pemagang::count(),
            'total_presensi'  => $totalPresensi,
            'datang_awal'     => $totalAwal,
            'tepat_waktu'     => $totalTepat,
            'terlambat'       => $totalTerlambat,
            'tidak_hadir'     => $totalTidakHadir,
            'avg_rate'        => $avgDisiplinRate,
        ];

        // Tabel 2: Riwayat detail log presensi (10 per halaman)
        $logQuery = Presensi::with('pemagang');
        if ($request->filled('shift')) {
            $logQuery->where('shift', $request->input('shift'));
        }
        if ($request->filled('keterangan')) {
            $logQuery->where('keterangan', $request->input('keterangan'));
        }
        if ($request->filled('divisi')) {
            $divisi = $request->input('divisi');
            $logQuery->whereHas('pemagang', function ($q) use ($divisi) {
                $q->where('divisi', $divisi);
            });
        }
        if ($request->filled('search')) {
            $search = $request->input('search');
            $logQuery->whereHas('pemagang', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $logs = $logQuery->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10, ['*'], 'page_logs')
            ->withQueryString()
            ->fragment('tabel-log-presensi');

        $divisiList = Pemagang::select('divisi')->distinct()->orderBy('divisi')->pluck('divisi');

        return view('staff.presensi.laporan-presensi', compact('rekapPemagang', 'stats', 'logs', 'divisiList'));
    }
}
