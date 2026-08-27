<?php

namespace App\Http\Controllers\Staff;

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
    ) {}

    public function index()
    {
        $users = User::where('role', 'hr_assistant')
            ->orderBy('name')
            ->get();

        return view('staff.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:100'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Role dikunci ke hr_assistant, tidak boleh diubah
        $validated['role']      = 'hr_assistant';
        $validated['is_active'] = 1;

        try {
            $this->userService->createUser($validated);
            return redirect()->route('staff.users.index')
                ->with('success', 'Akun HR Assistant berhasil ditambahkan.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function update(Request $request, User $user)
    {
        // Pastikan hanya bisa edit hr_assistant
        abort_if($user->role !== 'hr_assistant', 403);

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:100'],
            'email'        => ['required', 'email', 'max:100'],
            'is_active'    => ['nullable', 'boolean'],
            'image'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_image' => ['nullable', 'in:0,1'],
        ]);

        // Paksa role tetap hr_assistant
        $validated['role'] = 'hr_assistant';

        try {
            $this->userService->updateUserWithPhoto(
                $user,
                $validated,
                $request->hasFile('image') ? $request->file('image') : null
            );
            return redirect()->route('staff.users.index')
                ->with('success', 'Data HR Assistant berhasil diperbarui.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            Log::error('Gagal memperbarui data HR Assistant: ' . $e->getMessage(), [
                'target_user_id' => $user->id,
                'exception' => $e,
            ]);

            return back()->with('error', 'Terjadi kendala saat memperbarui data HR Assistant. Silakan coba beberapa saat lagi.')->withInput();
        }
    }

    public function updatePassword(Request $request, User $user)
    {
        abort_if($user->role !== 'hr_assistant', 403);

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $this->userService->updatePassword($user, $request->password);

        return redirect()->route('staff.users.index')
            ->with('success', 'Password HR Assistant berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        abort_if($user->role !== 'hr_assistant', 403);

        try {
            $this->userService->deleteUser($user);
            return redirect()->route('staff.users.index')
                ->with('success', 'Akun HR Assistant berhasil dihapus.');
        } catch (ValidationException $e) {
            return back()->with('error', $e->errors()['user'][0]);
        }
    }
}
