<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogsActivity;
use App\Models\SosmedAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    use LogsActivity;

    public function index(Request $request)
    {
        $search = $request->query('search');
        $platform = $request->query('platform');
        $status = $request->query('status'); // 'assigned', 'unassigned'

        $accountsQuery = SosmedAccount::with(['pmUser', 'staffUser', 'assistantUser', 'creator'])
            ->orderBy('platform')
            ->orderBy('name');

        if ($search) {
            $accountsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('platform', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($platform) {
            $accountsQuery->where('platform', $platform);
        }

        if ($status === 'assigned') {
            $accountsQuery->whereNotNull('staff_id');
        } elseif ($status === 'unassigned') {
            $accountsQuery->whereNull('staff_id');
        }

        $accounts = $accountsQuery->get();

        $allAccounts = SosmedAccount::all();
        $stats = [
            'total'      => $allAccounts->count(),
            'assigned'   => $allAccounts->whereNotNull('staff_id')->count(),
            'unassigned' => $allAccounts->whereNull('staff_id')->count(),
        ];

        $platformList = ['Instagram', 'TikTok', 'YouTube', 'Facebook', 'Twitter/X', 'LinkedIn', 'Threads', 'Website'];

        return view('admin.accounts.index', compact(
            'accounts',
            'stats',
            'search',
            'platform',
            'status',
            'platformList'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:200'],
            'platform' => ['required', 'string', 'max:50'],
            'link'     => ['nullable', 'url', 'max:500'],
            'email'    => ['nullable', 'string', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'max:255'],
            'notes'    => ['nullable', 'string', 'max:1000'],
        ]);

        $data = [
            'name'       => $validated['name'],
            'platform'   => $validated['platform'],
            'link'       => $validated['link'] ?? null,
            'email'      => $validated['email'] ?? null,
            'password'   => !empty($validated['password']) ? $validated['password'] : null,
            'notes'      => $validated['notes'] ?? null,
            'created_by' => Auth::id(),
        ];

        $account = SosmedAccount::create($data);

        $this->logActivity(
            'account.created',
            'Manajemen Akun',
            "Menambahkan akun '{$account->name}' ({$account->platform})",
            $account
        );

        return redirect()->route('admin.accounts.index')
            ->with('success', "Akun '{$account->name}' ({$account->platform}) berhasil ditambahkan.");
    }

    public function update(Request $request, SosmedAccount $account)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:200'],
            'platform' => ['required', 'string', 'max:50'],
            'link'     => ['nullable', 'url', 'max:500'],
            'email'    => ['nullable', 'string', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'max:255'],
            'notes'    => ['nullable', 'string', 'max:1000'],
        ]);

        $data = [
            'name'     => $validated['name'],
            'platform' => $validated['platform'],
            'link'     => $validated['link'] ?? null,
            'email'    => $validated['email'] ?? null,
            'notes'    => $validated['notes'] ?? null,
        ];

        // Only update password if a new one is provided
        if (!empty($validated['password'])) {
            $data['password'] = $validated['password'];
        }

        $account->update($data);

        $this->logActivity(
            'account.updated',
            'Manajemen Akun',
            "Memperbarui data akun '{$account->name}' ({$account->platform})",
            $account
        );

        return redirect()->route('admin.accounts.index')
            ->with('success', "Akun '{$account->name}' berhasil diperbarui.");
    }

    public function destroy(SosmedAccount $account)
    {
        $name = $account->name;
        $platform = $account->platform;
        $account->delete();

        $this->logActivity(
            'account.deleted',
            'Manajemen Akun',
            "Menghapus akun '{$name}' ({$platform})"
        );

        return redirect()->route('admin.accounts.index')
            ->with('success', "Akun '{$name}' ({$platform}) berhasil dihapus.");
    }
}
