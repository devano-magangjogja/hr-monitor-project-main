<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {
    }

    public function index()
    {
        $users = $this->userService->getAllUsers();
        $roles = \App\Models\Role::where('name', '!=', 'admin')->orderBy('id')->get();
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'exists:roles,name'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        try {
            $this->userService->createUser($validated);
            return redirect()->route('admin.users.index')
                ->with('success', 'Pengguna berhasil ditambahkan.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100'],
            'role' => ['required', 'exists:roles,name'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_image' => ['nullable', 'in:0,1'],
        ]);

        try {
            $this->userService->updateUserWithPhoto(
                $user,
                $validated,
                $request->hasFile('image') ? $request->file('image') : null
            );
            return redirect()->route('admin.users.index')
                ->with('success', 'Data pengguna berhasil diperbarui.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            Log::error('Gagal memperbarui data pengguna oleh Admin: ' . $e->getMessage(), [
                'target_user_id' => $user->id,
                'exception' => $e,
            ]);

            return back()->with('error', 'Terjadi kendala saat memperbarui data pengguna. Silakan coba beberapa saat lagi.')->withInput();
        }
    }

    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $this->userService->updatePassword($user, $request->password);

        return redirect()->route('admin.users.index')
            ->with('success', 'Password pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        try {
            $this->userService->deleteUser($user);
            return redirect()->route('admin.users.index')
                ->with('success', 'Pengguna berhasil dihapus.');
        } catch (ValidationException $e) {
            return back()->with('error', $e->errors()['user'][0]);
        }
    }
}