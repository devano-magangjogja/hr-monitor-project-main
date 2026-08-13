<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class UserService
{
    public function __construct(
        protected UserRepository $userRepository
    ) {}

    public function getAllUsers(): Collection
    {
        return $this->userRepository->getAllExceptAdmin();
    }

    public function createUser(array $data): User
    {
        if ($this->userRepository->isEmailTaken($data['email'])) {
            throw ValidationException::withMessages([
                'email' => 'Email sudah digunakan oleh pengguna lain.',
            ]);
        }

        return $this->userRepository->create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'role'      => $data['role'],
            'is_active' => $data['is_active'] ?? 1,
        ]);
    }

    public function updateUser(User $user, array $data): bool
    {
        if ($this->userRepository->isEmailTaken($data['email'], $user->id)) {
            throw ValidationException::withMessages([
                'email' => 'Email sudah digunakan oleh pengguna lain.',
            ]);
        }

        return $this->userRepository->update($user, [
            'name'      => $data['name'],
            'email'     => $data['email'],
            'role'      => $data['role'],
            'is_active' => $data['is_active'] ?? $user->is_active,
        ]);
    }

    /**
     * Update data pengguna oleh Admin, termasuk foto profil.
     */
    public function updateUserWithPhoto(User $user, array $data, $imageFile = null): bool
    {
        if ($this->userRepository->isEmailTaken($data['email'], $user->id)) {
            throw ValidationException::withMessages([
                'email' => 'Email sudah digunakan oleh pengguna lain.',
            ]);
        }

        $payload = [
            'name'      => $data['name'],
            'email'     => $data['email'],
            'role'      => $data['role'],
            'is_active' => $data['is_active'] ?? $user->is_active,
        ];

        // Upload foto baru
        if ($imageFile) {
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }
            $payload['image'] = $imageFile->store('profile-photos', 'public');
        }

        // Hapus foto
        if (isset($data['remove_image']) && $data['remove_image'] === '1') {
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }
            $payload['image'] = null;
        }

        return $this->userRepository->update($user, $payload);
    }

    public function updatePassword(User $user, string $newPassword): bool
    {
        return $this->userRepository->updatePassword(
            $user,
            Hash::make($newPassword)
        );
    }

    public function deleteUser(User $user): bool
    {
        // Cegah hapus diri sendiri
        if ($user->id === Auth::id()) {
            throw ValidationException::withMessages([
                'user' => 'Tidak dapat menghapus akun sendiri.',
            ]);
        }

        return $this->userRepository->delete($user);
    }

    /**
     * Update profil milik user yang sedang login (semua role).
     * Menangani upload, replace, dan hapus foto profil.
     */
    public function updateProfile(User $user, array $data, $imageFile = null): bool
    {
        // Validasi email tidak dipakai user lain
        if ($this->userRepository->isEmailTaken($data['email'], $user->id)) {
            throw ValidationException::withMessages([
                'email' => 'Email sudah digunakan oleh pengguna lain.',
            ]);
        }

        $payload = [
            'name'  => $data['name'],
            'email' => $data['email'],
        ];

        // Proses upload foto baru
        if ($imageFile) {
            // Hapus foto lama jika ada
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }

            $path = $imageFile->store('profile-photos', 'public');
            $payload['image'] = $path;
        }

        // Hapus foto (jika user centang "hapus foto")
        if (isset($data['remove_image']) && $data['remove_image'] === '1') {
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }
            $payload['image'] = null;
        }

        return $this->userRepository->updateProfile($user, $payload);
    }

    /**
     * Ganti password milik user yang sedang login (semua role).
     */
    public function updateOwnPassword(User $user, array $data): bool
    {
        // Verifikasi password lama
        if (! Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Password saat ini tidak sesuai.',
            ]);
        }

        return $this->userRepository->updatePassword(
            $user,
            Hash::make($data['password'])
        );
    }
}