<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pemagang;
use App\Models\Presensi;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $baseQuery = Presensi::with(['pemagang', 'creator'])->where('tanggal', $tanggal);

        // Filter Kantor
        if ($request->filled('kantor')) {
            $baseQuery->where('kantor', $request->input('kantor'));
        }

        // Filter Pencarian (Nama, No HP, Kampus)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $baseQuery->whereHas('pemagang', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%")
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
        $statsQuery = Presensi::where('tanggal', $tanggal);
        if ($request->filled('kantor')) {
            $statsQuery->where('kantor', $request->input('kantor'));
        }

        $stats = [
            'total_pemagang' => Pemagang::count(),
            'total_presensi' => (clone $statsQuery)->count(),
            'datang_awal'    => (clone $statsQuery)->where('keterangan', 'Lebih Awal')->count(),
            'tepat_waktu'    => (clone $statsQuery)->where('keterangan', 'Tepat Waktu')->count(),
            'terlambat'      => (clone $statsQuery)->where('keterangan', 'Terlambat')->count(),
            'tidak_hadir'    => (clone $statsQuery)->where('keterangan', 'Tidak Hadir')->count(),
            'total_hadir'    => (clone $statsQuery)->whereIn('keterangan', ['Lebih Awal', 'Tepat Waktu', 'Terlambat'])->count(),
        ];

        // List semua pemagang untuk dropdown modal
        $pemagangs = Pemagang::orderBy('nama_lengkap', 'asc')->get();

        // List opsi divisi unik
        $divisiList = Pemagang::select('divisi')->distinct()->orderBy('divisi')->pluck('divisi');

        // Monitoring Asisten yang sedang bertugas di masing-masing kantor pada tanggal ini
        // Hanya tampilkan user dengan role hr_assistant / base_type assistant yang bertugas mengelola presensi
        $asistenKantors = Task::where('task_date', $tanggal)
            ->whereNotNull('kantor')
            ->with(['assignedUsers.roleModel'])
            ->get()
            ->flatMap(function ($task) {
                return $task->assignedUsers
                    ->filter(fn($u) => $u->isHrAssistant())
                    ->map(function ($u) use ($task) {
                        return [
                            'name'   => $u->name,
                            'kantor' => $task->kantor,
                        ];
                    });
            })->unique(fn($item) => $item['name'] . $item['kantor'])->values();

        $kantorList = ['Kantor 1', 'Kantor 2', 'Kantor 3', 'Kantor 4'];

        return view('admin.presensi.presensi', compact(
            'presensiHadir',
            'presensiTidakHadir',
            'stats',
            'pemagangs',
            'divisiList',
            'tanggal',
            'isToday',
            'formattedDate',
            'asistenKantors',
            'kantorList'
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
            'kantor'      => ['nullable', 'string', 'in:Kantor 1,Kantor 2,Kantor 3,Kantor 4'],
            'waktu_masuk' => ['required'],
            'keterangan'  => ['required', 'in:Lebih Awal,Tepat Waktu,Terlambat,Tidak Hadir'],
            'notes'       => ['nullable', 'string', 'max:500'],
        ]);

        // Tanggal otomatis dikunci pada hari ini
        $validated['tanggal']    = $today;
        $validated['kantor']     = $validated['kantor'] ?? 'Kantor 1';
        $validated['created_by'] = Auth::id();
        $validated['notes']      = $validated['notes'] ?? ($validated['keterangan'] === 'Tidak Hadir' ? 'Tidak hadir tanpa keterangan' : 'Presensi tercatat');

        Presensi::create($validated);

        return redirect()->route('admin.presensi.index', ['tanggal' => $today])
            ->with('success', 'Catatan presensi pemagang hari ini berhasil disimpan.');
    }

    /**
     * Perbarui data presensi
     */
    public function update(Request $request, Presensi $presensi)
    {
        $validated = $request->validate([
            'shift'       => ['required', 'in:Pagi,Middle,Siang'],
            'kantor'      => ['nullable', 'string', 'in:Kantor 1,Kantor 2,Kantor 3,Kantor 4'],
            'waktu_masuk' => ['required'],
            'keterangan'  => ['required', 'in:Lebih Awal,Tepat Waktu,Terlambat,Tidak Hadir'],
            'notes'       => ['nullable', 'string', 'max:500'],
        ]);

        $validated['notes'] = $validated['notes'] ?? '-';

        $presensi->update($validated);

        return redirect()->route('admin.presensi.index', ['tanggal' => $presensi->tanggal])
            ->with('success', 'Data presensi berhasil diperbarui.');
    }

    /**
     * Hapus catatan presensi
     */
    public function destroy(Presensi $presensi)
    {
        $tanggal = $presensi->tanggal;
        $presensi->delete();

        return redirect()->route('admin.presensi.index', ['tanggal' => $tanggal])
            ->with('success', 'Catatan presensi berhasil dihapus.');
    }

    /**
     * Tampilkan halaman laporan dan rekapitulasi presensi pemagang
     */
    public function laporan(Request $request)
    {
        $queryPemagang = Pemagang::with('presensis');

        if ($request->filled('kantor')) {
            $kantor = $request->input('kantor');
            $queryPemagang->whereHas('presensis', function ($q) use ($kantor) {
                $q->where('kantor', $kantor);
            });
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $queryPemagang->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%")
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
        $kantorFilter = $request->input('kantor');
        $rekapPemagang = $pemagangs->through(function ($p) use ($kantorFilter) {
            $presensiList = $p->presensis;
            if ($kantorFilter) {
                $presensiList = $presensiList->where('kantor', $kantorFilter);
            }
            $total = $presensiList->count();
            $awal = $presensiList->where('keterangan', 'Lebih Awal')->count();
            $tepat = $presensiList->where('keterangan', 'Tepat Waktu')->count();
            $terlambat = $presensiList->where('keterangan', 'Terlambat')->count();
            $tidakHadir = $presensiList->where('keterangan', 'Tidak Hadir')->count();
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
        $statsBase = Presensi::query();
        if ($request->filled('kantor')) {
            $statsBase->where('kantor', $request->input('kantor'));
        }

        $totalPresensi = (clone $statsBase)->count();
        $totalAwal = (clone $statsBase)->where('keterangan', 'Lebih Awal')->count();
        $totalTepat = (clone $statsBase)->where('keterangan', 'Tepat Waktu')->count();
        $totalTerlambat = (clone $statsBase)->where('keterangan', 'Terlambat')->count();
        $totalTidakHadir = (clone $statsBase)->where('keterangan', 'Tidak Hadir')->count();
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
        $logQuery = Presensi::with(['pemagang', 'creator']);
        if ($request->filled('kantor')) {
            $logQuery->where('kantor', $request->input('kantor'));
        }
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
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        $logs = $logQuery->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10, ['*'], 'page_logs')
            ->withQueryString()
            ->fragment('tabel-log-presensi');

        $divisiList = Pemagang::select('divisi')->distinct()->orderBy('divisi')->pluck('divisi');
        $kantorList = ['Kantor 1', 'Kantor 2', 'Kantor 3', 'Kantor 4'];

        return view('admin.presensi.laporan-presensi', compact('rekapPemagang', 'stats', 'logs', 'divisiList', 'kantorList'));
    }
}
