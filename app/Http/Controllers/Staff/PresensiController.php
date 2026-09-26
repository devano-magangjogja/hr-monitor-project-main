<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogsActivity;
use App\Models\Pemagang;
use App\Models\Presensi;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresensiController extends Controller
{
    use LogsActivity;
    /**
     * Tampilkan halaman presensi pemagang hari ini (atau sesuai tanggal filter)
     */
    public function index(Request $request)
    {
        // Default tanggal adalah hari ini, tidak boleh melebihi hari ini
        $todayDate = Carbon::today()->format('Y-m-d');
        $tanggal = $request->input('tanggal', $todayDate);
        if ($tanggal > $todayDate) {
            $tanggal = $todayDate;
        }
        $isToday = $tanggal === $todayDate;
        $formattedDate = Carbon::parse($tanggal)->locale('id')->translatedFormat('l, d F Y');

        // Query dasar berdasarkan tanggal yang dipilih
        $baseQuery = Presensi::with(['pemagang', 'creator'])
            ->where('tanggal', $tanggal)
            ->where('session', 'entry');

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

        // 2. Tabel Tidak Hadir - 15 data per halaman
        $presensiTidakHadir = (clone $baseQuery)
            ->where('keterangan', 'Tidak Hadir')
            ->orderBy('id', 'desc')
            ->paginate(15, ['*'], 'page_tidak_hadir')
            ->withQueryString()
            ->fragment('tabel-tidak-hadir');

        // Tab aktif: hadir | belum | tidak_hadir
        $tab = in_array($request->input('tab'), ['hadir', 'belum', 'tidak_hadir'], true)
            ? $request->input('tab') : 'hadir';

        // 3. Tab Belum Dipresensi - pemagang aktif tanpa catatan presensi pada tanggal (ops: kantor) ini
        $belumKantor = $request->input('kantor');
        $pemagangBelum = Pemagang::whereDoesntHave('presensis', function ($q) use ($tanggal, $belumKantor) {
            $q->where('tanggal', $tanggal)
                ->where('session', 'entry');
            if ($belumKantor) {
                $q->where('kantor', $belumKantor);
            }
        })
            ->when($request->filled('divisi'), fn($q) => $q->where('divisi', $request->input('divisi')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->input('search');
                $q->where(function ($w) use ($s) {
                    $w->where('nama_lengkap', 'like', "%{$s}%")
                        ->orWhere('no_hp', 'like', "%{$s}%")
                        ->orWhere('kampus', 'like', "%{$s}%");
                });
            })
            ->orderBy('nama_lengkap', 'asc')
            ->paginate(15, ['*'], 'page_belum')
            ->withQueryString()
            ->fragment('tabel-belum');

        // Statistik Ringkasan untuk TANGGAL YANG DIPILIH
        $statsQuery = Presensi::where('tanggal', $tanggal)->where('session', 'entry');
        if ($request->filled('kantor')) {
            $statsQuery->where('kantor', $request->input('kantor'));
        }

        $stats = [
            'total_pemagang' => Pemagang::count(),
            'total_presensi' => (clone $statsQuery)->count(),
            'datang_awal' => (clone $statsQuery)->where('keterangan', 'Lebih Awal')->count(),
            'tepat_waktu' => (clone $statsQuery)->where('keterangan', 'Tepat Waktu')->count(),
            'terlambat' => (clone $statsQuery)->where('keterangan', 'Terlambat')->count(),
            'tidak_hadir' => (clone $statsQuery)->where('keterangan', 'Tidak Hadir')->count(),
            'total_hadir' => (clone $statsQuery)->whereIn('keterangan', ['Lebih Awal', 'Tepat Waktu', 'Terlambat'])->count(),
            'belum_presensi' => Pemagang::whereDoesntHave('presensis', fn($q) => $q->where('tanggal', $tanggal)->where('session', 'entry'))->count(),
        ];

        // List pemagang untuk dropdown modal — hanya yang BELUM tercatat presensinya pada tanggal & kantor ini
        $filterKantor = $request->input('kantor');
        $pemagangs = Pemagang::whereDoesntHave('presensis', function ($q) use ($tanggal, $filterKantor) {
            $q->where('tanggal', $tanggal)
                ->where('session', 'entry');
            if ($filterKantor) {
                $q->where('kantor', $filterKantor);
            }
        })->orderBy('nama_lengkap', 'asc')->get();

        // List opsi divisi lengkap
        $divisiList = Pemagang::getAllDivisi();

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
                            'name' => $u->name,
                            'kantor' => $task->kantor,
                        ];
                    });
            })->unique(fn($item) => $item['name'] . $item['kantor'])->values();

        $kantorList = ['Kantor 1', 'Kantor 2', 'Kantor 3', 'Kantor 4', 'Kantor 5', 'Kantor 6', 'Kantor 7', 'Kantor 8', 'Kantor 9', 'Kantor 10'];

        return view('staff.presensi.presensi', compact(
            'presensiHadir',
            'presensiTidakHadir',
            'pemagangBelum',
            'tab',
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
            'shift' => ['required', 'in:Pagi,Middle,Siang'],
            'kantor' => ['nullable', 'string', 'in:Kantor 1,Kantor 2,Kantor 3,Kantor 4,Kantor 5,Kantor 6,Kantor 7,Kantor 8,Kantor 9,Kantor 10'],
            'waktu_masuk' => ['required'],
            'keterangan' => ['required', 'in:Lebih Awal,Tepat Waktu,Terlambat,Tidak Hadir'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Tanggal otomatis dikunci pada hari ini
        $validated['tanggal'] = $today;
        $validated['session'] = 'entry';
        $validated['kantor'] = $validated['kantor'] ?? 'Kantor 1';
        $validated['created_by'] = Auth::id();
        $validated['notes'] = $validated['notes'] ?? ($validated['keterangan'] === 'Tidak Hadir' ? 'Tidak hadir tanpa keterangan' : 'Presensi tercatat');

        $presensi = Presensi::create($validated);
        $pemagang = \App\Models\Pemagang::find($validated['pemagang_id']);
        $this->logActivity(
            'presensi.created',
            'Presensi',
            "Mencatat presensi '{$pemagang?->nama_lengkap}' ({$validated['keterangan']}) di {$validated['kantor']}",
            $presensi
        );

        return redirect()->route('staff.presensi.index', ['tanggal' => $today])
            ->with('success', 'Catatan presensi pemagang hari ini berhasil disimpan.');
    }

    /**
     * Perbarui data presensi
     */
    public function update(Request $request, Presensi $presensi)
    {
        $validated = $request->validate([
            'shift' => ['required', 'in:Pagi,Middle,Siang'],
            'kantor' => ['nullable', 'string', 'in:Kantor 1,Kantor 2,Kantor 3,Kantor 4,Kantor 5,Kantor 6,Kantor 7,Kantor 8,Kantor 9,Kantor 10'],
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

        return redirect()->route('staff.presensi.index', ['tanggal' => $presensi->tanggal])
            ->with('success', 'Data presensi berhasil diperbarui.');
    }

    /**
     * Hapus catatan presensi
     */
    public function destroy(Presensi $presensi)
    {
        $tanggal = $presensi->tanggal;
        $namaPemagang = $presensi->pemagang?->nama_lengkap ?? 'Pemagang';
        // Hapus juga catatan istirahat yang diturunkan dari entry ini agar tidak yatim
        if ($presensi->session === 'entry') {
            Presensi::where('pemagang_id', $presensi->pemagang_id)
                ->where('tanggal', $tanggal)
                ->where('session', 'break_return')
                ->delete();
        }
        $presensi->delete();
        $this->logActivity(
            'presensi.deleted',
            'Presensi',
            "Menghapus catatan presensi '{$namaPemagang}' tanggal {$tanggal}"
        );

        return redirect()->route('staff.presensi.index', ['tanggal' => $tanggal])
            ->with('success', 'Catatan presensi berhasil dihapus.');
    }

    /**
     * Tampilkan halaman laporan dan rekapitulasi presensi pemagang
     */
    public function laporan(Request $request)
    {
        $queryPemagang = Pemagang::with(['presensis']);

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

        $pemagangs = $queryPemagang->orderBy('nama_lengkap', 'asc')
            ->paginate(15, ['*'], 'page_rekap')
            ->withQueryString()
            ->fragment('tabel-rekap-pemagang');

        // Hitung metrik per pemagang (semua session dihitung)
        $kantorFilter = $request->input('kantor');
        $rekapPemagang = $pemagangs->through(function ($p) use ($kantorFilter) {
            $presensiList = $p->presensis;
            if ($kantorFilter) {
                $presensiList = $presensiList->where('kantor', $kantorFilter);
            }

            // Metrik utama dari SEMUA session → terlambat di istirahat juga menambah poin Telat
            $total = $presensiList->count();
            $awal = $presensiList->where('keterangan', 'Lebih Awal')->count();
            $tepat = $presensiList->where('keterangan', 'Tepat Waktu')->count();
            $terlambat = $presensiList->where('keterangan', 'Terlambat')->count();
            $tidakHadir = $presensiList->where('keterangan', 'Tidak Hadir')->count();
            $hadirDisiplin = $awal + $tepat;
            $rate = $total > 0 ? round(($hadirDisiplin / $total) * 100, 1) : 0;

            // Breakdown untuk modal
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

        // Global stats
        $statsBase = Presensi::where('session', 'entry');
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
            'total_pemagang' => Pemagang::count(),
            'total_presensi' => $totalPresensi,
            'datang_awal' => $totalAwal,
            'tepat_waktu' => $totalTepat,
            'terlambat' => $totalTerlambat,
            'tidak_hadir' => $totalTidakHadir,
            'avg_rate' => $avgDisiplinRate,
        ];

        // Tabel 2: Riwayat detail log presensi (semua session)
        $logQuery = Presensi::with(['pemagang', 'creator']); // hapus where('session', 'entry')

        if ($request->filled('kantor')) {
            $logQuery->where('kantor', $request->input('kantor'));
        }
        if ($request->filled('tanggal')) {
            $logQuery->whereDate('tanggal', $request->input('tanggal'));
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
            ->paginate(15, ['*'], 'page_logs')
            ->withQueryString()
            ->fragment('tabel-log-presensi');

        // Tabel 3: Presensi Masuk hari ini (session = 'entry')
        $tanggalMasuk = $request->input('tanggal_masuk', Carbon::today()->format('Y-m-d'));
        $presensiMasukQuery = Presensi::with(['pemagang'])
            ->where('session', 'entry')
            ->whereDate('tanggal', $tanggalMasuk);
        if ($request->filled('kantor')) {
            $presensiMasukQuery->where('kantor', $request->input('kantor'));
        }
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

        // Tabel 4: Presensi Istirahat hari ini (session = 'break_return')
        $tanggalIstirahat = $request->input('tanggal_istirahat', Carbon::today()->format('Y-m-d'));
        $presensiIstirahatQuery = Presensi::with(['pemagang'])
            ->where('session', 'break_return')
            ->whereDate('tanggal', $tanggalIstirahat);
        if ($request->filled('kantor')) {
            $presensiIstirahatQuery->where('kantor', $request->input('kantor'));
        }
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

        return view('staff.presensi.laporan-presensi', compact(
            'rekapPemagang',
            'stats',
            'logs',
            'divisiList',
            'kantorList',
            'presensiMasuk',
            'totalPresensiMasuk',
            'tanggalMasuk',
            'presensiIstirahat',
            'totalPresensiIstirahat',
            'tanggalIstirahat'
        ));
    }
}

