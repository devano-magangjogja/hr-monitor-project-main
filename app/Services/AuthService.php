<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        protected UserRepository $userRepository
    ) {}

    public function login(array $credentials): void
    {
        $user = $this->userRepository->findActiveByEmail($credentials['email']);

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'Email tidak terdaftar atau akun tidak aktif.',
            ]);
        }

        $remember = $credentials['remember'] ?? false;
        unset($credentials['remember']);

        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }

        request()->session()->regenerate();
    }

    public function logout(): void
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    public function redirectByRole(): string
    {
        return match(Auth::user()->role) {
            'admin'        => route('admin.dashboard'),
            'hr_staff'     => route('staff.dashboard'),
            'hr_assistant' => route('assistant.dashboard'),
            'cs'           => route('cs.dashboard'),
            'ob'           => route('ob.dashboard'),
            'programmer'   => route('programmer.dashboard'),
            'dg'           => route('dg.dashboard'),
            'vg'           => route('vg.dashboard'),
            'pm'           => route('pm.dashboard'),
            default        => route('login'),
        };
    }
}