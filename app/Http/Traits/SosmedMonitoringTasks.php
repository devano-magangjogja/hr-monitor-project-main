<?php

namespace App\Http\Traits;

use App\Models\SosmedAccount;
use App\Models\SosmedTask;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Data untuk tab monitoring tugas sosmed (Admin & Staff pakai bentuk yang sama).
 */
trait SosmedMonitoringTasks
{
    /**
     * Label status tugas: dipakai dropdown filter dan badge di tabel monitoring.
     */
    protected function taskStatusLabels(): array
    {
        return [
            'pending'        => 'Belum Dikerjakan',
            'done_by_staff'  => 'Menunggu Verifikasi PM/Asisten',
            'verified_by_pm' => 'Menunggu HR Staff',
            'approved_hr'    => 'Disetujui Final',
            'rejected'       => 'Ditolak',
            'no_task'        => 'Belum Ada Tugas',
        ];
    }

    /**
     * Status yang masih dianggap 'pekerjaan belum selesai'. Tugas final (approved_hr) tidak
     * carried over ke tanggal berikutnya.
     */
    protected function openTaskStatuses(): array
    {
        return ['pending', 'done_by_staff', 'verified_by_pm', 'rejected'];
    }

    /**
     * Baris monitoring "per tanggal $date": tugas yang memang terjadwal pada tanggal itu, DITAMBAH
     * tugas terakhir tiap akun sebelum tanggal itu yang statusnya masih menggantung.
     *
     * Tugas sosmed hanya dibuat sekali saat akun ditugaskan (bukan otomatis tiap hari), jadi kalau
     * monitoring hanya melihat task_date = $date, akun yang sejak seminggu lalu belum mengerjakan
     * apa pun hilang dari daftar dan kartu 'Belum Dikerjakan' menunjukkan 0.
     */
    protected function monitoringTasksQuery(string $date)
    {
        return SosmedTask::with(['account.staffUsers', 'account.pmUser', 'assignedUser', 'assignedBy', 'verifiedBy', 'hrVerifiedBy'])
            ->where(function ($q) use ($date) {
                $q->whereDate('task_date', $date)
                    ->orWhere(function ($q2) use ($date) {
                        $q2->whereDate('task_date', '<', $date)
                            ->whereIn('status', $this->openTaskStatuses())
                            // hanya tugas terbaru akun ini sampai $date, agar tugas lama yang sudah
                            // ditimpa tugas berikutnya tidak muncul dua kali
                            ->whereNotExists(function ($q3) use ($date) {
                                $q3->select(DB::raw(1))
                                    ->from('sosmed_tasks as lanjutan')
                                    ->whereColumn('lanjutan.sosmed_account_id', 'sosmed_tasks.sosmed_account_id')
                                    ->whereDate('lanjutan.task_date', '<=', $date)
                                    ->whereRaw('(date(lanjutan.task_date) > date(sosmed_tasks.task_date)
                                              or (date(lanjutan.task_date) = date(sosmed_tasks.task_date)
                                                  and lanjutan.id > sosmed_tasks.id))');
                            });
                    });
            })
            ->orderBy('task_date', 'desc');
    }

    /**
     * Akun dalam pemantauan yang punya pengelola tetapi sampai tanggal $date belum pernah punya
     * record tugas sama sekali (status 'no_task').
     */
    protected function accountsWithoutTaskQuery(string $date, string $search = '')
    {
        $query = SosmedAccount::inSosmed()
            ->with(['staffUsers', 'pmUser'])
            ->has('staffUsers')
            ->whereDoesntHave('sosmedTasks', fn ($q) => $q->whereDate('task_date', '<=', $date));

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $like = '%' . $search . '%';
                $q->where('name', 'like', $like)
                  ->orWhere('username', 'like', $like)
                  ->orWhere('platform', 'like', $like)
                  ->orWhere('brand', 'like', $like)
                  ->orWhereHas('staffUsers', fn ($u) => $u->where('name', 'like', $like));
            });
        }

        return $query->orderBy('platform')->orderBy('name');
    }

    /**
     * Baris 'Belum Ada Tugas' dibuat meniru bentuk record tugas supaya tabel monitoring tetap
     * satu bentuk (kolom, pagination, cetak PDF) walaupun sumbernya akun, bukan tugas.
     */
    protected function noTaskPaginator(Request $request, string $date, string $search, string $routeName, int $perPage = 15)
    {
        $rows = $this->accountsWithoutTaskQuery($date, $search)->get()->map(function (SosmedAccount $acc) use ($date) {
            return (object) [
                'id'                 => null,
                'title'              => 'Belum ada tugas tercatat',
                'description'        => null,
                'type'               => 'daily',
                'task_date'          => Carbon::parse($date),
                'account'            => $acc,
                'assignedUser'       => null,
                'assignedBy'         => null,
                'verifiedBy'         => null,
                'hrVerifiedBy'       => null,
                'status'             => 'no_task',
                'status_label'       => $this->taskStatusLabels()['no_task'],
                'status_badge_class' => 'bg-gray-100 text-gray-500 border border-gray-200',
                'rejection_note'     => null,
                'link_upload'        => [],
            ];
        });

        $page = max(1, $request->integer('page', 1));

        return new LengthAwarePaginator(
            $rows->forPage($page, $perPage),
            $rows->count(),
            $perPage,
            $page,
            ['path' => route($routeName), 'query' => $request->all()]
        );
    }
}
