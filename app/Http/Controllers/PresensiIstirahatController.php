<?php

namespace App\Http\Controllers;

use App\Http\Traits\LogsActivity;
use App\Models\Pemagang;
use App\Models\Presensi;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresensiIstirahatController extends Controller
{
    use LogsActivity;

    public function index(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $tanggal = min($request->input('tanggal', $today), $today);
        $kantor = $request->input('kantor');
        if (str_starts_with($request->path(), 'assistant/')) {
            $kantor = Task::whereHas('assignments', fn ($query) => $query->where('user_id', Auth::id()))
                ->whereDate('task_date', $tanggal)
                ->whereNotNull('kantor')
                ->value('kantor') ?: '__no_assigned_office__';
        }
        $search = trim((string) $request->input('search'));
        $filter = in_array($request->input('filter'), ['kembali', 'terlambat'], true)
            ? $request->input('filter')
            : 'all';

        // ── Query daftar utama ──────────────────────────────────────────
        $entries = Presensi::with('pemagang')
            ->where('session', 'break_return')
            ->whereDate('tanggal', $tanggal)
            ->when($kantor, fn ($query) => $query->where('kantor', $kantor))
            ->when($search, function ($query) use ($search) {
                $query->whereHas('pemagang', function ($pemagang) use ($search) {
                    $pemagang->where('nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('no_hp', 'like', "%{$search}%")
                        ->orWhere('kampus', 'like', "%{$search}%");
                });
            })
            ->when($filter === 'kembali', fn ($q) => $q->where('keterangan', '!=', 'Terlambat'))
            ->when($filter === 'terlambat', fn ($q) => $q->where('keterangan', 'Terlambat'))
            ->orderBy('kantor')
            ->orderBy('pemagang_id')
            ->paginate(20)
            ->withQueryString();

        $entryTimes = Presensi::where('session', 'entry')
            ->whereDate('tanggal', $tanggal)
            ->whereIn('pemagang_id', $entries->getCollection()->pluck('pemagang_id'))
            ->when($kantor, fn ($query) => $query->where('kantor', $kantor))
            ->get()
            ->keyBy('pemagang_id');
        $entries->getCollection()->each(
            fn ($entry) => $entry->setRelation('entry', $entryTimes->get($entry->pemagang_id))
        );

        $pendingEntries = $this->entryQuery($tanggal, $kantor)
            ->with('pemagang')
            ->orderBy('kantor')
            ->orderBy('pemagang_id')
            ->get();

        $lateCandidates = collect();
        if ($search !== '') {
            $lateCandidates = $this->entryQuery($tanggal, $kantor)
                ->whereHas('pemagang', function ($query) use ($search) {
                    $query->where('nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('no_hp', 'like', "%{$search}%")
                        ->orWhere('kampus', 'like', "%{$search}%");
                })
                ->get();
        }

        $base = Presensi::where('session', 'entry')->whereDate('tanggal', $tanggal)
            ->when($kantor, fn ($query) => $query->where('kantor', $kantor));
        $stats = [
            'peserta' => (clone $base)->count(),
            'kembali' => Presensi::where('session', 'break_return')->whereDate('tanggal', $tanggal)->when($kantor, fn ($query) => $query->where('kantor', $kantor))->count(),
            'terlambat' => Presensi::where('session', 'break_return')->whereDate('tanggal', $tanggal)->where('keterangan', 'Terlambat')->when($kantor, fn ($query) => $query->where('kantor', $kantor))->count(),
            'belum_kembali' => max(0, (clone $base)->count() - Presensi::where('session', 'break_return')->whereDate('tanggal', $tanggal)->when($kantor, fn ($query) => $query->where('kantor', $kantor))->count()),
        ];

        $defaultBreakTime = Presensi::where('session', 'break_return')
            ->whereDate('tanggal', $tanggal)
            ->when($kantor, fn ($query) => $query->where('kantor', $kantor))
            ->latest('id')
            ->value('waktu_masuk');

        return view('presensi.istirahat', [
            'entries' => $entries,
            'pendingEntries' => $pendingEntries,
            'lateCandidates' => $lateCandidates,
            'defaultBreakTime' => $defaultBreakTime ? substr($defaultBreakTime, 0, 5) : null,
            'stats' => $stats,
            'tanggal' => $tanggal,
            'formattedDate' => Carbon::parse($tanggal)->locale('id')->translatedFormat('l, d F Y'),
            'isToday' => $tanggal === $today,
            'search' => $search,
            'filter' => $filter,          // ← pastikan ada
            'kantor' => $kantor,
            'kantorList' => ['Kantor 1', 'Kantor 2', 'Kantor 3', 'Kantor 4', 'Kantor 5', 'Kantor 6', 'Kantor 7', 'Kantor 8', 'Kantor 9', 'Kantor 10'],
            'prefix' => str_starts_with($request->path(), 'staff/') ? 'staff' : (str_starts_with($request->path(), 'assistant/') ? 'assistant' : 'admin'),
        ]);
    }

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
            'kantor' => ['nullable', 'string'],
            'waktu_kembali' => ['required', 'date_format:H:i'],
        ]);

        $kantor = $this->resolveKantor($request, $validated['kantor'] ?? null, $validated['tanggal']);
        $entries = $this->entryQuery($validated['tanggal'], $kantor)->get();
        $count = 0;
        foreach ($entries as $entry) {
            $this->saveBreakReturn($entry, $validated['waktu_kembali'], 'Tepat Waktu', 'Kembali dari istirahat secara massal.');
            $count++;
        }

        $this->logActivity('presensi.break_bulk', 'Presensi', "Mencatat {$count} pemagang kembali dari istirahat");

        return back()->with('success', "Presensi kembali dari istirahat berhasil dicatat untuk {$count} pemagang.");
    }

    public function storeLate(Request $request)
    {
        $validated = $request->validate([
            'entry_id' => ['required_without:break_id', 'nullable', 'integer', 'exists:presensi,id'],
            'break_id' => ['required_without:entry_id', 'nullable', 'integer', 'exists:presensi,id'],
            'waktu_kembali' => ['required', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        if (!empty($validated['break_id'])) {
            $break = Presensi::whereKey($validated['break_id'])
                ->where('session', 'break_return')
                ->firstOrFail();
            $kantor = $this->resolveKantor($request, $break->kantor, $break->tanggal);
            abort_unless($break->kantor === $kantor, 403);

            $break->update([
                'waktu_masuk' => $validated['waktu_kembali'],
                'keterangan' => 'Terlambat',
                'notes' => ($validated['notes'] ?? null) ?: 'Terlambat kembali dari istirahat.',
                'created_by' => Auth::id(),
            ]);

            $this->logActivity('presensi.break_late', 'Presensi', "Menandai '{$break->pemagang?->nama_lengkap}' terlambat kembali dari istirahat");

            return back()->with('success', 'Pemagang berhasil ditandai terlambat pada sesi presensi istirahat.');
        }

        $entry = Presensi::whereKey($validated['entry_id'])->where('session', 'entry')->firstOrFail();
        $kantor = $this->resolveKantor($request, $entry->kantor, $entry->tanggal);
        abort_unless($entry->kantor === $kantor, 403);
        $this->saveBreakReturn($entry, $validated['waktu_kembali'], 'Terlambat', ($validated['notes'] ?? null) ?: 'Terlambat kembali dari istirahat.');
        $this->logActivity('presensi.break_late', 'Presensi', "Menandai '{$entry->pemagang?->nama_lengkap}' terlambat kembali dari istirahat");

        return back()->with('success', 'Pemagang berhasil ditandai terlambat pada sesi presensi istirahat.');
    }

    public function correctLate(Request $request)
    {
        $validated = $request->validate([
            'break_id' => ['required', 'integer', 'exists:presensi,id'],
        ]);

        $break = Presensi::whereKey($validated['break_id'])
            ->where('session', 'break_return')
            ->firstOrFail();
        $kantor = $this->resolveKantor($request, $break->kantor, $break->tanggal);
        abort_unless($break->kantor === $kantor, 403);

        $break->update([
            'keterangan' => 'Tepat Waktu',
            'notes' => 'Status terlambat dibatalkan dan dikoreksi menjadi tepat waktu.',
            'created_by' => Auth::id(),
        ]);

        $this->logActivity('presensi.break_corrected', 'Presensi', "Membatalkan status terlambat '{$break->pemagang?->nama_lengkap}'");

        return back()->with('success', 'Status terlambat berhasil dibatalkan. Pemagang kembali menjadi tepat waktu.');
    }

    private function entryQuery(string $tanggal, ?string $kantor)
    {
        return Presensi::with('pemagang')
            ->where('session', 'entry')
            ->whereDate('tanggal', $tanggal)
            ->whereDoesntHave('pemagang.presensis', function ($query) use ($tanggal) {
                $query->where('session', 'break_return')->whereDate('tanggal', $tanggal);
            })
            ->when($kantor, fn ($query) => $query->where('kantor', $kantor));
    }

    private function resolveKantor(Request $request, ?string $kantor, string $tanggal): ?string
    {
        if (str_starts_with($request->path(), 'assistant/')) {
            return Task::whereHas('assignments', fn ($query) => $query->where('user_id', Auth::id()))
                ->whereDate('task_date', $tanggal)
                ->whereNotNull('kantor')
                ->value('kantor') ?: '__no_assigned_office__';
        }

        return $kantor;
    }

    private function saveBreakReturn(Presensi $entry, string $waktuKembali, string $keterangan, string $notes): void
    {
        $break = Presensi::firstOrNew([
            'pemagang_id' => $entry->pemagang_id,
            'tanggal' => $entry->tanggal,
            'kantor' => $entry->kantor,
            'session' => 'break_return',
        ]);

        $break->fill([
            'shift' => $entry->shift,
            'waktu_masuk' => $waktuKembali,
            'keterangan' => $keterangan,
            'notes' => $notes,
            'created_by' => Auth::id(),
        ])->save();
    }
}
