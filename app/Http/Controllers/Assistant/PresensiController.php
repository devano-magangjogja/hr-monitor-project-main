<?php

namespace App\Http\Controllers\Assistant;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogsActivity;
use Illuminate\Http\Request;
use App\Models\Pemagang;
use App\Models\Presensi;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PresensiController extends Controller
{
    use LogsActivity;
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

        // Flag: apakah asisten sudah memiliki penugasan kantor pada tanggal ini
        $hasKantor = !is_null($assignedKantor);

        $baseQuery = Presensi::with(['pemagang', 'creator'])
            ->where('tanggal', $tanggal)
            ->where('session', 'entry');

        if ($selectedKantor) {
            $baseQuery->where('kantor', $selectedKantor);
        }

        // Jika asisten BELUM punya penugasan kantor resmi:
        // hanya tampilkan presensi yang DIA SENDIRI catat.
        // Asisten yang sudah punya penugasan → bisa lihat semua presensi di kantornya.
        if (!$hasKantor) {
            $baseQuery->where('created_by', Auth::id());
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

        // 1. Tabel Hadir (Lebih Awal, Tepat Waktu, Terlambat)
        $allowedHadir = ['Lebih Awal', 'Tepat Waktu', 'Terlambat'];
        $filterKet = in_array($request->input('keterangan'), $allowedHadir)
            ? $request->input('keterangan') : null;

        $presensiHadir = (clone $baseQuery)
            ->whereIn('keterangan', $allowedHadir)
            ->when($filterKet, fn($q) => $q->where('keterangan', $filterKet))
            ->orderBy('waktu_masuk', 'asc')
            ->paginate(15, ['*'], 'page_hadir')
            ->withQueryString()
            ->fragment('tabel-hadir');

        // 2. Tabel Tidak Hadir - 5 data per halaman
        $presensiTidakHadir = (clone $baseQuery)
            ->where('keterangan', 'Tidak Hadir')
            ->orderBy('id', 'desc')
            ->paginate(15, ['*'], 'page_tidak_hadir')
            ->withQueryString()
            ->fragment('tabel-tidak-hadir');

        // Statistik Ringkasan untuk TANGGAL YANG DIPILIH
        // Jika belum ada penugasan kantor, semua stats = 0
        $statsQuery = Presensi::where('tanggal', $tanggal)->where('session', 'entry');
        if ($hasKantor) {
            $statsQuery->where('kantor', $selectedKantor);
        } else {
            $statsQuery->whereRaw('1 = 0');
        }

        $stats = [
            'total_pemagang' => Pemagang::count(),
            'total_presensi' => (clone $statsQuery)->count(),
            'datang_awal' => (clone $statsQuery)->where('keterangan', 'Lebih Awal')->count(),
            'tepat_waktu' => (clone $statsQuery)->where('keterangan', 'Tepat Waktu')->count(),
            'terlambat' => (clone $statsQuery)->where('keterangan', 'Terlambat')->count(),
            'tidak_hadir' => (clone $statsQuery)->where('keterangan', 'Tidak Hadir')->count(),
            'total_hadir' => (clone $statsQuery)->whereIn('keterangan', ['Lebih Awal', 'Tepat Waktu', 'Terlambat'])->count(),
        ];

        // List pemagang untuk dropdown modal — hanya yang BELUM tercatat presensinya hari ini (di kantor mana pun)
        $pemagangQuery = Pemagang::query();
        $pemagangQuery->whereDoesntHave('presensis', function ($q) use ($tanggal) {
            $q->where('tanggal', $tanggal);
        });
        $pemagangs = $pemagangQuery->orderBy('nama_lengkap', 'asc')->get();

        // List opsi divisi lengkap
        $divisiList = Pemagang::getAllDivisi();

        $kantorList = ['Kantor 1', 'Kantor 2', 'Kantor 3', 'Kantor 4', 'Kantor 5', 'Kantor 6', 'Kantor 7', 'Kantor 8', 'Kantor 9', 'Kantor 10'];

        $authId = Auth::id();

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
            'kantorList',
            'authId',
            'hasKantor'
        ));
    }

    /**
     * Tetapkan kantor tugas asisten secara mandiri
     */
    public function setKantor(Request $request)
    {
        $validated = $request->validate([
            'kantor' => ['required', 'string', 'in:Kantor 1,Kantor 2,Kantor 3,Kantor 4,Kantor 5,Kantor 6,Kantor 7,Kantor 8,Kantor 9,Kantor 10'],
        ]);

        $this->ensureAssistantKantorTask(Auth::id(), $validated['kantor']);
        $this->logActivity('presensi.assigned', 'Presensi', "Menetapkan lokasi bertugas ke {$validated['kantor']}");

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
            'shift' => ['required', 'in:Pagi,Middle,Siang'],
            'waktu_masuk' => ['required'],
            'keterangan' => ['required', 'in:Lebih Awal,Tepat Waktu,Terlambat,Tidak Hadir'],
            'notes' => ['nullable', 'string', 'max:500'],
            'kantor' => ['required', 'string', 'in:Kantor 1,Kantor 2,Kantor 3,Kantor 4,Kantor 5,Kantor 6,Kantor 7,Kantor 8,Kantor 9,Kantor 10'],
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
        $validated['tanggal'] = $today;
        $validated['session'] = 'entry';
        $validated['kantor'] = $kantorTujuan;
        $validated['created_by'] = Auth::id();
        $validated['notes'] = $validated['notes'] ?? ($validated['keterangan'] === 'Tidak Hadir' ? 'Tidak hadir tanpa keterangan' : 'Presensi tercatat');

        Presensi::create($validated);
        $pemagang = \App\Models\Pemagang::find($validated['pemagang_id']);
        $this->logActivity(
            'presensi.created',
            'Presensi',
            "Mencatat presensi '{$pemagang?->nama_lengkap}' ({$validated['keterangan']}) di {$validated['kantor']}"
        );

        return redirect()->route('assistant.presensi.index', ['tanggal' => $today])
            ->with('success', 'Catatan presensi pemagang hari ini berhasil disimpan.');
    }

    /**
     * Perbarui data presensi
     * Asisten hanya dapat mengubah shift, waktu masuk, keterangan, dan notes
     * Hanya admin/staff dapat mengubah kantor
     * Asisten hanya boleh mengubah catatan yang dia sendiri buat
     */
    public function update(Request $request, Presensi $presensi)
    {
        // Asisten hanya boleh mengubah catatan yang dia sendiri buat
        if ($presensi->created_by !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki izin untuk mengubah catatan presensi ini.');
        }

        $validated = $request->validate([
            'shift' => ['required', 'in:Pagi,Middle,Siang'],
            'waktu_masuk' => ['required'],
            'keterangan' => ['required', 'in:Lebih Awal,Tepat Waktu,Terlambat,Tidak Hadir'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['notes'] = $validated['notes'] ?? '-';

        $presensi->update($validated);
        $this->logActivity(
            'presensi.updated',
            'Presensi',
            "Memperbarui presensi '{$presensi->pemagang?->nama_lengkap}' tanggal {$presensi->tanggal}",
            $presensi
        );

        return redirect()->route('assistant.presensi.index', ['tanggal' => $presensi->tanggal])
            ->with('success', 'Data presensi berhasil diperbarui.');
    }

    /**
     * Hapus catatan presensi
     * Asisten hanya boleh menghapus catatan yang dia sendiri buat
     */
    public function destroy(Presensi $presensi)
    {
        // Asisten hanya boleh menghapus catatan yang dia sendiri buat
        if ($presensi->created_by !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki izin untuk menghapus catatan presensi ini.');
        }

        $tanggal = $presensi->tanggal;
        $namaPemagang = $presensi->pemagang?->nama_lengkap ?? 'Pemagang';
        $presensi->delete();
        $this->logActivity(
            'presensi.deleted',
            'Presensi',
            "Menghapus catatan presensi '{$namaPemagang}' tanggal {$tanggal}"
        );

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

        // ── Query Pemagang: ambil yang punya presensi di kantor + tanggal ini (semua session)
        $queryPemagang = Pemagang::whereHas('presensis', function ($q) use ($tanggal, $selectedKantor) {
            $q->where('tanggal', $tanggal)
                ->where('kantor', $selectedKantor);
        })->with([
                    'presensis' => function ($q) use ($tanggal, $selectedKantor) {
                        $q->where('tanggal', $tanggal)
                            ->where('kantor', $selectedKantor);
                        // load semua session (entry + break_return)
                    }
                ]);

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

        $pemagangs = $queryPemagang->orderBy('nama_lengkap', 'asc')
            ->paginate(15, ['*'], 'page_rekap')
            ->withQueryString()
            ->fragment('tabel-rekap-pemagang');

        // ── Hitung metrik per pemagang (semua session dihitung)
        $rekapPemagang = $pemagangs->through(function ($p) {
            $presensiList = $p->presensis;

            // Metrik utama dari SEMUA session → terlambat di istirahat juga menambah poin Telat
            $total = $presensiList->count();
            $awal = $presensiList->where('keterangan', 'Lebih Awal')->count();
            $tepat = $presensiList->where('keterangan', 'Tepat Waktu')->count();
            $terlambat = $presensiList->where('keterangan', 'Terlambat')->count();
            $tidakHadir = $presensiList->where('keterangan', 'Tidak Hadir')->count();
            $hadirDisiplin = $awal + $tepat;
            $rate = $total > 0 ? round(($hadirDisiplin / $total) * 100, 1) : 0;

            // Breakdown untuk modal detail
            $entryList = $presensiList->where('session', 'entry');
            $terlambatPagi = $entryList->where('keterangan', 'Terlambat')->count();
            $breakList = $presensiList->where('session', 'break_return');
            $terlambatIstirahat = $breakList->where('keterangan', 'Terlambat')->count();

            return (object) [
                'pemagang' => $p,
                'total' => $total,
                'datang_awal' => $awal,
                'tepat_waktu' => $tepat,
                'terlambat' => $terlambat,
                'tidak_hadir' => $tidakHadir,
                'rate' => $rate,
                'terlambat_pagi' => $terlambatPagi,
                'terlambat_istirahat' => $terlambatIstirahat,
            ];
        });

        // Global stats HANYA untuk hari itu dan kantor tersebut
        $statsBase = Presensi::where('tanggal', $tanggal)->where('kantor', $selectedKantor)->where('session', 'entry');
        $totalPresensi = (clone $statsBase)->count();
        $totalAwal = (clone $statsBase)->where('keterangan', 'Lebih Awal')->count();
        $totalTepat = (clone $statsBase)->where('keterangan', 'Tepat Waktu')->count();
        $totalTerlambat = (clone $statsBase)->where('keterangan', 'Terlambat')->count();
        $totalTidakHadir = (clone $statsBase)->where('keterangan', 'Tidak Hadir')->count();
        $avgDisiplinRate = $totalPresensi > 0 ? round((($totalAwal + $totalTepat) / $totalPresensi) * 100, 1) : 0;

        $stats = [
            'total_pemagang' => $totalPemagang,
            'total_presensi' => $totalPresensi,
            'datang_awal' => $totalAwal,
            'tepat_waktu' => $totalTepat,
            'terlambat' => $totalTerlambat,
            'tidak_hadir' => $totalTidakHadir,
            'avg_rate' => $avgDisiplinRate,
        ];

        // Tabel 2: Riwayat detail log – hanya yang dia sendiri catat + di kantor penugasan
        $logQuery = Presensi::with(['pemagang', 'creator'])
            ->where('tanggal', $tanggal)
            ->where('kantor', $selectedKantor)
            ->where('created_by', Auth::id());   // hanya milik dia sendiri
// tidak ada filter session → tampilkan entry + break_return

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
            ->paginate(15, ['*'], 'page_logs')
            ->withQueryString()
            ->fragment('tabel-log-presensi');

        // Tabel 3: Presensi Masuk untuk tanggal yang dipilih di kantor ini
        $tanggalMasuk = $request->input('tanggal_masuk', $tanggal);
        $presensiMasukQuery = Presensi::with(['pemagang'])
            ->where('session', 'entry')
            ->whereDate('tanggal', $tanggalMasuk)
            ->where('kantor', $selectedKantor);
        if ($request->filled('divisi')) {
            $divisi = $request->input('divisi');
            $presensiMasukQuery->whereHas('pemagang', fn($q) => $q->where('divisi', $divisi));
        }
        if ($request->filled('search')) {
            $search = $request->input('search');
            $presensiMasukQuery->whereHas('pemagang', fn($q) => $q->where('nama_lengkap', 'like', "%{$search}%")
                ->orWhere('no_hp', 'like', "%{$search}%"));
        }
        $presensiMasuk = $presensiMasukQuery
            ->orderBy('waktu_masuk', 'asc')
            ->paginate(15, ['*'], 'page_masuk')
            ->withQueryString()
            ->fragment('tabel-presensi-masuk');
        $totalPresensiMasuk = $presensiMasukQuery->count();

        // Tabel 4: Presensi Istirahat untuk tanggal yang dipilih di kantor ini
        $tanggalIstirahat = $request->input('tanggal_istirahat', $tanggal);
        $presensiIstirahatQuery = Presensi::with(['pemagang'])
            ->where('session', 'break_return')
            ->whereDate('tanggal', $tanggalIstirahat)
            ->where('kantor', $selectedKantor);
        if ($request->filled('divisi')) {
            $divisi = $request->input('divisi');
            $presensiIstirahatQuery->whereHas('pemagang', fn($q) => $q->where('divisi', $divisi));
        }
        if ($request->filled('search')) {
            $search = $request->input('search');
            $presensiIstirahatQuery->whereHas('pemagang', fn($q) => $q->where('nama_lengkap', 'like', "%{$search}%")
                ->orWhere('no_hp', 'like', "%{$search}%"));
        }
        $presensiIstirahat = $presensiIstirahatQuery
            ->orderBy('waktu_masuk', 'asc')
            ->paginate(15, ['*'], 'page_istirahat')
            ->withQueryString()
            ->fragment('tabel-presensi-istirahat');
        $totalPresensiIstirahat = $presensiIstirahatQuery->count();

        $divisiList = Pemagang::getAllDivisi();

        $kantorList = ['Kantor 1', 'Kantor 2', 'Kantor 3', 'Kantor 4', 'Kantor 5', 'Kantor 6', 'Kantor 7', 'Kantor 8', 'Kantor 9', 'Kantor 10'];

        return view('assistant.presensi.laporan-presensi', compact(
            'rekapPemagang',
            'stats',
            'logs',
            'divisiList',
            'assignedKantor',
            'selectedKantor',
            'tanggal',
            'formattedDate',
            'kantorList',
            'presensiMasuk',
            'totalPresensiMasuk',
            'tanggalMasuk',
            'presensiIstirahat',
            'totalPresensiIstirahat',
            'tanggalIstirahat'
        ));
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
                    'title' => "Presensi Pemagang - {$kantor}",
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
            'title' => "Presensi Pemagang - {$kantor}",
            'description' => "Penugasan presensi pemagang di {$kantor} (Ditentukan mandiri oleh {$userName})",
            'task_date' => $today,
            'type' => 'assigned',
            'kantor' => $kantor,
            'created_by' => $userId,
        ]);

        $task->assignments()->create([
            'user_id' => $userId,
            'is_completed' => 'pending',
        ]);

        return $task;
    }
}
