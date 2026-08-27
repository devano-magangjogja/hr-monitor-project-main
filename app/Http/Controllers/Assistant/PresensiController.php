<?php

namespace App\Http\Controllers\Assistant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pemagang;
use App\Models\Presensi;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $isToday = $tanggal === Carbon::today()->format('Y-m-d');
        $formattedDate = Carbon::parse($tanggal)->locale('id')->translatedFormat('l, d F Y');

        // Cari tugas asisten pada tanggal ini yang memiliki lokasi kantor
        $assignedKantor = Task::whereHas('assignments', function ($q) {
            $q->where('user_id', Auth::id());
        })->where('task_date', $tanggal)
          ->whereNotNull('kantor')
          ->value('kantor');

        // Jika belum ada penugasan kantor, jangan tampilkan data kantor acak
        $baseQuery = Presensi::with(['pemagang', 'creator'])->where('tanggal', $tanggal);

        if ($assignedKantor) {
            $baseQuery->where('kantor', $assignedKantor);
        } else {
            // Asisten belum punya tugas kantor pada tanggal ini
            $baseQuery->whereRaw('1 = 0');
        }

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
        if ($assignedKantor) {
            $statsQuery->where('kantor', $assignedKantor);
        } else {
            $statsQuery->whereRaw('1 = 0');
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

        // List pemagang untuk dropdown modal
        $pemagangQuery = Pemagang::query();
        if ($assignedKantor) {
            $pemagangQuery->whereDoesntHave('presensis', function ($q) use ($tanggal, $assignedKantor) {
                $q->where('tanggal', $tanggal)
                  ->where('kantor', '!=', $assignedKantor);
            });
        }
        $pemagangs = $pemagangQuery->orderBy('nama_lengkap', 'asc')->get();

        // List opsi divisi unik
        $divisiList = Pemagang::select('divisi')->distinct()->orderBy('divisi')->pluck('divisi');

        return view('assistant.presensi.presensi', compact(
            'presensiHadir',
            'presensiTidakHadir',
            'stats',
            'pemagangs',
            'divisiList',
            'tanggal',
            'isToday',
            'formattedDate',
            'assignedKantor'
        ));
    }

    /**
     * Simpan catatan presensi baru (otomatis terkunci untuk hari ini)
     */
    public function store(Request $request)
    {
        $today = Carbon::today()->format('Y-m-d');

        // Cari tugas asisten hari ini untuk mendapatkan lokasi kantor
        $assignedKantor = Task::whereHas('assignments', function ($q) {
            $q->where('user_id', Auth::id());
        })->where('task_date', $today)
          ->whereNotNull('kantor')
          ->value('kantor');

        if (!$assignedKantor) {
            return back()->with('error', 'Anda belum dapat melakukan presensi karena Admin atau Staff belum menentukan tugas penugasan kantor Anda untuk hari ini. Silakan hubungi Admin atau Staff.')->withInput();
        }

        $validated = $request->validate([
            'pemagang_id' => ['required', 'exists:pemagang,id'],
            'shift'       => ['required', 'in:Pagi,Middle,Siang'],
            'waktu_masuk' => ['required'],
            'keterangan'  => ['required', 'in:Lebih Awal,Tepat Waktu,Terlambat,Tidak Hadir'],
            'notes'       => ['nullable', 'string', 'max:500'],
        ]);

        $kantorTujuan = $assignedKantor;

        // Validasi: Cegah pencatatan jika pemagang sudah tercatat di kantor lain hari ini
        $alreadyOtherOffice = Presensi::where('pemagang_id', $validated['pemagang_id'])
            ->where('tanggal', $today)
            ->where('kantor', '!=', $kantorTujuan)
            ->first();

        if ($alreadyOtherOffice) {
            return back()->with('error', "Pemagang ini sudah tercatat presensinya di {$alreadyOtherOffice->kantor} hari ini.")->withInput();
        }

        // Tanggal otomatis dikunci pada hari ini
        $validated['tanggal']    = $today;
        $validated['kantor']     = $kantorTujuan;
        $validated['created_by'] = Auth::id();
        $validated['notes']      = $validated['notes'] ?? ($validated['keterangan'] === 'Tidak Hadir' ? 'Tidak hadir tanpa keterangan' : 'Presensi tercatat');

        Presensi::create($validated);

        return redirect()->route('assistant.presensi.index', ['tanggal' => $today])
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

        return redirect()->route('assistant.presensi.index', ['tanggal' => $presensi->tanggal])
            ->with('success', 'Data presensi berhasil diperbarui.');
    }

    /**
     * Hapus catatan presensi
     */
    public function destroy(Presensi $presensi)
    {
        $tanggal = $presensi->tanggal;
        $presensi->delete();

        return redirect()->route('assistant.presensi.index', ['tanggal' => $tanggal])
            ->with('success', 'Catatan presensi berhasil dihapus.');
    }

    /**
     * Tampilkan halaman laporan dan rekapitulasi presensi pemagang (khusus kantor penugasan asisten pada hari tersebut)
     */
    public function laporan(Request $request)
    {
        $today = Carbon::today()->format('Y-m-d');
        $tanggal = $request->input('tanggal', $today);
        $formattedDate = Carbon::parse($tanggal)->locale('id')->translatedFormat('l, d F Y');

        // Cari tugas asisten pada hari/tanggal ini yang memiliki lokasi kantor
        $assignedKantor = Task::whereHas('assignments', function ($q) {
            $q->where('user_id', Auth::id());
        })->where('task_date', $tanggal)
          ->whereNotNull('kantor')
          ->value('kantor');

        if (!$assignedKantor) {
            $assignedKantor = Presensi::where('created_by', Auth::id())
                ->where('tanggal', $tanggal)
                ->whereNotNull('kantor')
                ->value('kantor');
        }

        // Jika belum ada kantor penugasan
        if (!$assignedKantor) {
            $rekapPemagang = collect();
            $stats = [
                'total_pemagang' => 0,
                'avg_rate'       => 0,
                'datang_awal'    => 0,
                'tepat_waktu'    => 0,
                'terlambat'      => 0,
                'tidak_hadir'    => 0,
                'total_hadir'    => 0,
            ];
            $logs = collect();
            $divisiList = collect();

            return view('assistant.presensi.laporan-presensi', compact('rekapPemagang', 'stats', 'logs', 'divisiList', 'assignedKantor', 'tanggal', 'formattedDate'));
        }

        // Tabel 1: Rekapitulasi Pemagang HANYA untuk pemagang yang presensi di kantor tersebut pada hari itu
        $queryPemagang = Pemagang::whereHas('presensis', function ($q) use ($tanggal, $assignedKantor) {
            $q->where('tanggal', $tanggal)
              ->where('kantor', $assignedKantor);
        })->with(['presensis' => function ($q) use ($tanggal, $assignedKantor) {
            $q->where('tanggal', $tanggal)
              ->where('kantor', $assignedKantor);
        }]);

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

        $totalPemagang = (clone $queryPemagang)->count();

        // Tabel 1: Rekapitulasi per Pemagang (10 per halaman)
        $pemagangs = $queryPemagang->orderBy('nama_lengkap', 'asc')
            ->paginate(10, ['*'], 'page_rekap')
            ->withQueryString()
            ->fragment('tabel-rekap-pemagang');

        // Hitung metrik per pemagang (hanya untuk hari dan kantor ini)
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

        // Global stats HANYA untuk hari itu dan kantor tersebut
        $statsBase = Presensi::where('tanggal', $tanggal)->where('kantor', $assignedKantor);
        $totalPresensi = (clone $statsBase)->count();
        $totalAwal = (clone $statsBase)->where('keterangan', 'Lebih Awal')->count();
        $totalTepat = (clone $statsBase)->where('keterangan', 'Tepat Waktu')->count();
        $totalTerlambat = (clone $statsBase)->where('keterangan', 'Terlambat')->count();
        $totalTidakHadir = (clone $statsBase)->where('keterangan', 'Tidak Hadir')->count();
        $avgDisiplinRate = $totalPresensi > 0 ? round((($totalAwal + $totalTepat) / $totalPresensi) * 100, 1) : 0;

        $stats = [
            'total_pemagang'  => $totalPemagang,
            'total_presensi'  => $totalPresensi,
            'datang_awal'     => $totalAwal,
            'tepat_waktu'     => $totalTepat,
            'terlambat'       => $totalTerlambat,
            'tidak_hadir'     => $totalTidakHadir,
            'avg_rate'        => $avgDisiplinRate,
        ];

        // Tabel 2: Riwayat detail log presensi HANYA untuk hari itu dan kantor tersebut
        $logQuery = Presensi::with(['pemagang', 'creator'])
            ->where('tanggal', $tanggal)
            ->where('kantor', $assignedKantor);

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

        $logs = $logQuery->orderBy('id', 'desc')
            ->paginate(10, ['*'], 'page_logs')
            ->withQueryString()
            ->fragment('tabel-log-presensi');

        $divisiList = Pemagang::whereHas('presensis', function ($q) use ($tanggal, $assignedKantor) {
            $q->where('tanggal', $tanggal)
              ->where('kantor', $assignedKantor);
        })->select('divisi')->distinct()->orderBy('divisi')->pluck('divisi');

        return view('assistant.presensi.laporan-presensi', compact('rekapPemagang', 'stats', 'logs', 'divisiList', 'assignedKantor', 'tanggal', 'formattedDate'));
    }
}