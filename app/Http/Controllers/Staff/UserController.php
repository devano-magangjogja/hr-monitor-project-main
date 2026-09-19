<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogsActivity;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    use LogsActivity;

    public function __construct(
        protected UserService $userService
    ) {}

    /** Role yang boleh dikelola oleh Staff */
    protected array $allowedRoles = ['hr_assistant', 'pm', 'sosmed'];

    protected function roleLabel(string $role): string
    {
        return match ($role) {
            'pm'           => 'PM',
            'sosmed'       => 'Sosmed',
            default        => 'HR Assistant',
        };
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $role   = $request->query('role');

        $users = User::whereIn('role', $this->allowedRoles)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role && in_array($role, $this->allowedRoles, true), function ($query) use ($role) {
                $query->where('role', $role);
            })
            ->orderBy('name')
            ->get();

        return view('staff.users.index', compact('users', 'search', 'role'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:100'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', 'string', 'in:' . implode(',', $this->allowedRoles)],
        ]);

        $validated['is_active'] = 1;
        $label = $this->roleLabel($validated['role']);

        try {
            $user = $this->userService->createUser($validated);
            $this->logActivity('user.created', 'Pengguna', "Menambahkan akun {$label} '{$validated['name']}'", $user);
            return redirect()->route('staff.users.index')
                ->with('success', "Akun {$label} berhasil ditambahkan.");
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function update(Request $request, User $user)
    {
        // Pastikan hanya bisa edit role yang diizinkan
        abort_if(!in_array($user->role, $this->allowedRoles), 403);

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:100'],
            'email'        => ['required', 'email', 'max:100'],
            'is_active'    => ['nullable', 'boolean'],
            'image'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_image' => ['nullable', 'in:0,1'],
        ]);

        // Pertahankan role yang sudah ada (tidak diubah saat edit)
        $validated['role'] = $user->role;
        $label = $this->roleLabel($user->role);

        try {
            $this->userService->updateUserWithPhoto(
                $user,
                $validated,
                $request->hasFile('image') ? $request->file('image') : null
            );
            $this->logActivity('user.updated', 'Pengguna', "Memperbarui data {$label} '{$user->name}'", $user);
            return redirect()->route('staff.users.index')
                ->with('success', "Data {$label} berhasil diperbarui.");
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            Log::error("Gagal memperbarui data {$label}: " . $e->getMessage(), [
                'target_user_id' => $user->id,
                'exception' => $e,
            ]);

            return back()->with('error', "Terjadi kendala saat memperbarui data {$label}. Silakan coba beberapa saat lagi.")->withInput();
        }
    }

    public function updatePassword(Request $request, User $user)
    {
        abort_if(!in_array($user->role, $this->allowedRoles), 403);

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $label = $this->roleLabel($user->role);
        $this->userService->updatePassword($user, $request->password);
        $this->logActivity('user.updated', 'Pengguna', "Memperbarui password {$label} '{$user->name}'", $user);

        return redirect()->route('staff.users.index')
            ->with('success', "Password {$label} berhasil diperbarui.");
    }

    public function destroy(User $user)
    {
        abort_if(!in_array($user->role, $this->allowedRoles), 403);

        $label = $this->roleLabel($user->role);
        try {
            $name = $user->name;
            $this->userService->deleteUser($user);
            $this->logActivity('user.deleted', 'Pengguna', "Menghapus akun {$label} '{$name}'");
            return redirect()->route('staff.users.index')
                ->with('success', "Akun {$label} berhasil dihapus.");
        } catch (ValidationException $e) {
            return back()->with('error', $e->errors()['user'][0]);
        }
    }
}
