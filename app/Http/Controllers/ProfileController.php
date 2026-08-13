<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    /**
     * Tampilkan halaman profil user yang sedang login.
     * Dipakai oleh semua role (admin, hr_staff, hr_assistant).
     */
    public function show()
    {
        return view('profile.show', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Update data profil (nama, email, foto).
     */
    public function update(Request $request)
    {
        $request->validate([
            'name'         => ['required', 'string', 'max:100'],
            'email'        => ['required', 'email', 'max:100'],
            'image'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_image' => ['nullable', 'in:0,1'],
        ]);

        try {
            $this->userService->updateProfile(
                Auth::user(),
                $request->only('name', 'email', 'remove_image'),
                $request->hasFile('image') ? $request->file('image') : null
            );

            return redirect()->route('profile.show')
                ->with('success', 'Profil berhasil diperbarui.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Ganti password user yang sedang login.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'  => ['required', 'string'],
            'password'          => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            $this->userService->updateOwnPassword(Auth::user(), $request->only(
                'current_password',
                'password',
            ));

            return redirect()->route('profile.show')
                ->with('success', 'Password berhasil diperbarui.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }
}
