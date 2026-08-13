<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function showForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($request->boolean('remember')) {
            $credentials['remember'] = true;
        }

        try {
            $this->authService->login($credentials);
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput($request->only('email'));
        }

        return redirect($this->authService->redirectByRole());
    }

    public function logout(Request $request)
    {
        $this->authService->logout();
        return redirect()->route('login');
    }
}