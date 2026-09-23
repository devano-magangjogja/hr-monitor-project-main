<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogsActivity;
use App\Models\Brand;
use App\Models\SosmedAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    use LogsActivity;

    public function index(Request $request)
    {
        $tab = $request->query('tab', 'accounts');
        $search = $request->query('search');
        $platform = $request->query('platform');
        $brand = $request->query('brand');
        $status = $request->query('status'); // 'assigned', 'unassigned'

        $accountsQuery = SosmedAccount::with(['pmUser', 'staffUsers', 'assistantUser', 'creator'])
            ->where('verification_status', 'approved')   // hanya tampilkan yang sudah disetujui
            ->orderBy('platform')
            ->orderBy('name');

        if ($search) {
            $accountsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('username', 'like', '%' . $search . '%')
                    ->orWhere('brand', 'like', '%' . $search . '%')
                    ->orWhere('platform', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($platform) {
            $accountsQuery->where('platform', $platform);
        }

        if ($brand) {
            $accountsQuery->where('brand', $brand);
        }

        if ($status === 'assigned') {
            $accountsQuery->whereHas('staffUsers');
        } elseif ($status === 'unassigned') {
            $accountsQuery->whereDoesntHave('staffUsers');
        }

        $accounts = $accountsQuery->paginate(15)->appends($request->query());

        $pendingQuery = SosmedAccount::with('creator')
            ->where('verification_status', 'pending');

        if ($search) {
            $pendingQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('username', 'like', '%' . $search . '%')
                    ->orWhere('brand', 'like', '%' . $search . '%')
                    ->orWhere('platform', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhereHas('creator', function ($c) use ($search) {
                        $c->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($platform) {
            $pendingQuery->where('platform', $platform);
        }

        if ($brand) {
            $pendingQuery->where('brand', $brand);
        }

        $pendingAccounts = $pendingQuery
            ->latest()
            ->paginate(15, ['*'], 'pending_page')
            ->appends($request->only(['tab', 'search', 'platform', 'brand']));

        $stats = [
            'total' => SosmedAccount::where('verification_status', 'approved')->count(),
            'assigned' => SosmedAccount::where('verification_status', 'approved')->whereHas('staffUsers')->count(),
            'unassigned' => SosmedAccount::where('verification_status', 'approved')->whereDoesntHave('staffUsers')->count(),
        ];

        $platformList = ['Instagram', 'TikTok', 'YouTube', 'Facebook', 'Twitter/X', 'LinkedIn', 'Threads', 'Website', 'Lainnya'];

        $brands = Brand::orderBy('name')->pluck('name');

        $brandsList = null;
        if ($tab === 'brands') {
            $brandsQuery = Brand::query()
                ->with(['accounts' => function ($q) {
                    $q->where('verification_status', 'approved')
                        ->with('staffUsers')
                        ->orderBy('platform')
                        ->orderBy('name');
                }])
                ->orderBy('name');

            if ($search) {
                $brandsQuery->where('name', 'like', '%' . $search . '%');
            }

            $brandsList = $brandsQuery
                ->paginate(10, ['*'], 'brands_page')
                ->appends($request->only(['tab', 'search']));
        }

        return view('admin.accounts.index', compact(
            'accounts',
            'stats',
            'search',
            'platform',
            'brand',
            'brands',
            'brandsList',
            'status',
            'platformList',
            'pendingAccounts',
            'tab'
        ));
    }

    public function storeBrand(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:brands,name'],
            'logo' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $data = ['name' => $validated['name']];

        if ($request->hasFile('logo')) {
            $data['logo_path'] = store_image_as_webp($request->file('logo'), 'brand-logos');
        }

        $brand = Brand::create($data);

        $this->logActivity(
            'brand.created',
            'Manajemen Akun',
            "Menambahkan brand '{$brand->name}'",
            $brand
        );

        return redirect()->route($this->accountRoute('index'), ['tab' => 'brands'])
            ->with('success', "Brand '{$brand->name}' berhasil ditambahkan.");
    }

    public function updateBrand(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('brands', 'name')->ignore($brand->id)],
            'logo' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $oldName = $brand->name;
        $data = ['name' => $validated['name']];

        if ($request->hasFile('logo')) {
            if ($brand->logo_path) {
                Storage::disk('public')->delete($brand->logo_path);
            }
            $data['logo_path'] = store_image_as_webp($request->file('logo'), 'brand-logos');
        }

        $brand->update($data);

        // Jaga relasi berbasis nama: sinkronkan akun yang memakai nama brand lama
        if ($oldName !== $brand->name) {
            SosmedAccount::where('brand', $oldName)->update(['brand' => $brand->name]);
        }

        $this->logActivity(
            'brand.updated',
            'Manajemen Akun',
            "Memperbarui brand '{$oldName}'" . ($oldName !== $brand->name ? " menjadi '{$brand->name}'" : ''),
            $brand
        );

        return redirect()->route($this->accountRoute('index'), ['tab' => 'brands'])
            ->with('success', "Brand '{$brand->name}' berhasil diperbarui.");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'brand' => ['nullable', 'string', 'max:100'],
            'platform' => ['required', 'string', 'max:50'],
            'custom_platform' => ['required_if:platform,Lainnya', 'nullable', 'string', 'max:50'],
            'link' => ['nullable', 'string', 'max:500'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'email_recovery' => ['nullable', 'string', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'max:255'],
            'two_factor_enabled' => ['nullable', 'boolean'],
            'phone' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $data = [
            'name' => $validated['name'],
            'brand' => $validated['brand'] ?? null,
            'platform' => $validated['platform'] === 'Lainnya' ? $validated['custom_platform'] : $validated['platform'],
            'link' => $validated['link'] ?? null,
            'email' => $validated['email'] ?? null,
            'email_recovery' => $validated['email_recovery'] ?? null,
            'password' => !empty($validated['password']) ? $validated['password'] : null,
            'two_factor_enabled' => $request->boolean('two_factor_enabled'),
            'phone' => $validated['phone'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'is_in_sosmed' => false,
            'verification_status' => 'approved',
            'created_by' => Auth::id(),
        ];

        $account = SosmedAccount::create($data);

        if (!empty($data['brand'])) {
            Brand::firstOrCreate(['name' => $data['brand']]);
        }

        $this->logActivity(
            'account.created',
            'Manajemen Akun',
            "Menambahkan akun '{$account->name}' ({$account->platform})" . ($account->brand ? " [Brand: {$account->brand}]" : ''),
            $account
        );

        return redirect()->route($this->accountRoute('index'))
            ->with('success', "Akun '{$account->name}' ({$account->platform}) berhasil ditambahkan.");
    }

    public function update(Request $request, SosmedAccount $account)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'brand' => ['nullable', 'string', 'max:100'],
            'platform' => ['required', 'string', 'max:50'],
            'custom_platform' => ['required_if:platform,Lainnya', 'nullable', 'string', 'max:50'],
            'link' => ['nullable', 'string', 'max:500'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'email_recovery' => ['nullable', 'string', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'max:255'],
            'two_factor_enabled' => ['nullable', 'boolean'],
            'phone' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $data = [
            'name' => $validated['name'],
            'brand' => $validated['brand'] ?? null,
            'platform' => $validated['platform'] === 'Lainnya' ? $validated['custom_platform'] : $validated['platform'],
            'link' => $validated['link'] ?? null,
            'email' => $validated['email'] ?? null,
            'email_recovery' => $validated['email_recovery'] ?? null,
            'two_factor_enabled' => $request->boolean('two_factor_enabled'),
            'phone' => $validated['phone'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ];

        // Only update password if a new one is provided
        if (!empty($validated['password'])) {
            $data['password'] = $validated['password'];
        }

        $account->update($data);

        if (!empty($data['brand'])) {
            Brand::firstOrCreate(['name' => $data['brand']]);
        }

        $this->logActivity(
            'account.updated',
            'Manajemen Akun',
            "Memperbarui data akun '{$account->name}' ({$account->platform})",
            $account
        );

        return redirect()->route($this->accountRoute('index'))
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

        return redirect()->route($this->accountRoute('index'))
            ->with('success', "Akun '{$name}' ({$platform}) berhasil dihapus.");
    }

    public function verify(Request $request, SosmedAccount $account)
    {
        $validated = $request->validate([
            'verification_status' => ['required', 'in:approved,rejected'],
            'email_recovery' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'two_factor_enabled' => ['nullable', 'boolean'],
            'rejection_note' => ['required_if:verification_status,rejected', 'nullable', 'string', 'max:1000'],
        ]);
        $isRejected = $validated['verification_status'] === 'rejected';

        $updateData = [
            'verification_status' => $validated['verification_status'],
            'email_recovery' => $validated['email_recovery'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'two_factor_enabled' => $request->boolean('two_factor_enabled'),
            'rejection_note' => $isRejected ? $validated['rejection_note'] : null,
        ];

        // Jika ditolak: lepas semua penugasan dan keluarkan dari kelola sosmed
        if ($isRejected) {
            $updateData['staff_id'] = null;
            $updateData['pm_id'] = null;
            $updateData['assistant_id'] = null;
            $updateData['supervisor_staff_id'] = null;
            $updateData['is_in_sosmed'] = false;
        }

        $account->update($updateData);

        $message = $isRejected
            ? 'Pengajuan akun ditolak dan dikeluarkan dari daftar manajemen akun.'
            : 'Pengajuan akun berhasil disetujui.';

        return back()->with('success', $message);
    }

    public function ownIndex(Request $request)
    {
        $search = $request->query('search');
        $accounts = SosmedAccount::query()
            ->where('created_by', Auth::id())
            ->when($search, fn($query) => $query->where(function ($nested) use ($search) {
                $nested->where('name', 'like', "%{$search}%")
                    ->orWhere('platform', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('accounts.my', compact('accounts', 'search'));
    }

    public function ownSubmissions(Request $request)
    {
        $search = $request->query('search');
        $userId = Auth::id();
        $accounts = SosmedAccount::query()
            ->where('verification_status', 'approved')
            ->where('created_by', $userId)
            ->when($search, fn($query) => $query->where(function ($nested) use ($search) {
                $nested->where('name', 'like', "%{$search}%")
                    ->orWhere('platform', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('accounts.submissions', compact('accounts', 'search'));
    }

    public function ownStore(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'platform' => ['required', 'string', 'max:50'],
            'custom_platform' => ['required_if:platform,Lainnya', 'nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        SosmedAccount::create([
            'name' => $validated['name'],
            'platform' => $validated['platform'] === 'Lainnya'
                ? $validated['custom_platform']
                : $validated['platform'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'created_by' => Auth::id(),
            'verification_status' => 'pending',
            'is_in_sosmed' => false,
        ]);

        return redirect()->back()->with('success', 'Pengajuan akun berhasil dikirim untuk verifikasi admin.');
    }

    private function accountRoute(string $action): string
    {
        return (Auth::user()?->isAdmin() ? 'admin' : 'staff') . '.accounts.' . $action;
    }
}
