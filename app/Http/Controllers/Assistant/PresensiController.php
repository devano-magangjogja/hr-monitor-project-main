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
        $todayDate = Carbon::today()->format('Y-m-d');
        $tanggal = $request->input('tanggal', $todayDate);
        if ($tanggal > $todayDate) {
            $tanggal = $todayDate;
        }
        $isToday = $tanggal === $todayDate;
        $formattedDate = Carbon::parse($tanggal)->locale('id')->translatedFormat('l, d F Y');

        // Cari penugasan resmi asisten pada tanggal yang dipilih:
        // HANYA dari tugas yang memiliki penempatan kantor pada tanggal ini
        $assignedKantor = Task::whereHas('assignments', fn($q) => $q->where('user_id', Auth::id()))
            ->where('task_date', $tanggal)
            ->whereNotNull('kantor')
            ->value('kantor');

        // Kantor yang sedang dilihat / difilter pada halaman
        $selectedKantor = $request->input('kantor') ?: $assignedKantor;

        $baseQuery = Presensi::with(['pemagang', 'creator'])
            ->where('tanggal', $tanggal);

        if ($selectedKantor) {
            $baseQuery->where('kantor', $selectedKantor);
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
        if ($selectedKantor) {
            $statsQuery->where('kantor', $selectedKantor);
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
        if ($selectedKantor) {
            $pemagangQuery->whereDoesntHave('presensis', function ($q) use ($tanggal, $selectedKantor) {
                $q->where('tanggal', $tanggal)
                  ->where('kantor', '!=', $selectedKantor);
            });
        }
        $pemagangs = $pemagangQuery->orderBy('nama_lengkap', 'asc')->get();

        // List opsi divisi lengkap
        $divisiList = Pemagang::getAllDivisi();

        $kantorList = ['Kantor 1', 'Kantor 2', 'Kantor 3', 'Kantor 4'];

        return view('assistant.presensi.presensi', compact(
            'presensiHadir',
            'presensiTidakHadir',
            'stats',
            'pemagangs',
            'divisiList',
            'tanggal',
            'isToday',
            'formattedDate',
            'assignedKantor',
            'selectedKantor',
            'kantorList'
        ));
    }

    /**
     * Tetapkan kantor tugas asisten secara mandiri
     */
    public function setKantor(Request $request)
    {
        $validated = $request->validate([
            'kantor' => ['required', 'string', 'in:Kantor 1,Kantor 2,Kantor 3,Kantor 4'],
        ]);

        $this->ensureAssistantKantorTask(Auth::id(), $validated['kantor']);

        return redirect()->route('assistant.presensi.index', ['kantor' => $validated['kantor']])
            ->with('success', 'Lokasi bertugas berhasil ditetapkan ke ' . $validated['kantor'] . ' dan otomatis tercatat di tugas Anda.');
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
            'kantor'      => ['required', 'string', 'in:Kantor 1,Kantor 2,Kantor 3,Kantor 4'],
        ]);

        $kantorTujuan = $validated['kantor'];

        // Otomatis catat / sinkronkan tugas penempatan kantor asisten hari ini
        $this->ensureAssistantKantorTask(Auth::id(), $kantorTujuan);

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

        // Cari kantor penugasan resmi asisten pada tanggal yang dipilih
        $assignedKantor = Task::whereHas('assignments', fn($q) => $q->where('user_id', Auth::id()))
            ->where('task_date', $tanggal)
            ->whereNotNull('kantor')
            ->value('kantor');

        // Kantor aktif untuk laporan
        $selectedKantor = $request->input('kantor') ?: ($assignedKantor ?: 'Kantor 1');

        // Tabel 1: Rekapitulasi Pemagang HANYA untuk pemagang yang presensi di kantor tersebut pada hari itu
        $queryPemagang = Pemagang::whereHas('presensis', function ($q) use ($tanggal, $selectedKantor) {
            $q->where('tanggal', $tanggal)
              ->where('kantor', $selectedKantor);
        })->with(['presensis' => function ($q) use ($tanggal, $selectedKantor) {
            $q->where('tanggal', $tanggal)
              ->where('kantor', $selectedKantor);
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
        $statsBase = Presensi::where('tanggal', $tanggal)->where('kantor', $selectedKantor);
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
            ->where('kantor', $selectedKantor);

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

        $divisiList = Pemagang::getAllDivisi();

        $kantorList = ['Kantor 1', 'Kantor 2', 'Kantor 3', 'Kantor 4'];

        return view('assistant.presensi.laporan-presensi', compact('rekapPemagang', 'stats', 'logs', 'divisiList', 'assignedKantor', 'selectedKantor', 'tanggal', 'formattedDate', 'kantorList'));
    }

    /**
     * Memastikan tugas penempatan kantor asisten hari ini tercatat / tersinkronkan.
     */
    protected function ensureAssistantKantorTask(int $userId, string $kantor): Task
    {
        $today = Carbon::today()->toDateString();

        // Cari tugas hari ini yang ditugaskan ke asisten ini
        $existingTask = Task::whereHas('assignments', fn($q) => $q->where('user_id', $userId))
            ->whereDate('task_date', $today)
            ->first();

        if ($existingTask) {
            // Jika tugas ini hanya untuk asisten ini sendiri, perbarui kantor dan judulnya
            if ($existingTask->assignments()->count() === 1) {
                $existingTask->update([
                    'kantor' => $kantor,
                    'title'  => "Presensi Pemagang - {$kantor}",
                ]);
                return $existingTask;
            }

            // Jika tugas bersama banyak asisten, lepas user ini dari tugas bersama agar asisten lain tidak terpengaruh
            $existingTask->assignments()->where('user_id', $userId)->delete();
        }

        // Buat tugas penugasan kantor khusus untuk asisten ini
        $user = \App\Models\User::find($userId);
        $userName = $user ? $user->name : 'HR Assistant';

        $task = Task::create([
            'title'       => "Presensi Pemagang - {$kantor}",
            'description' => "Penugasan presensi pemagang di {$kantor} (Ditentukan mandiri oleh {$userName})",
            'task_date'   => $today,
            'type'        => 'assigned',
            'kantor'      => $kantor,
            'created_by'  => $userId,
        ]);

        $task->assignments()->create([
            'user_id'      => $userId,
            'is_completed' => 'pending',
        ]);

        return $task;
    }
}