<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Belum login
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Akun nonaktif — paksa logout
        if (! $user->is_active) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Akun Anda telah dinonaktifkan.']);
        }

        // Role & Base Type check
        $allowedRoles = explode(',', $role);
        
        $hasAccess = in_array($user->role, $allowedRoles, true)
            || in_array($user->base_type, $allowedRoles, true)
            || (in_array('admin', $allowedRoles, true) && $user->isAdmin())
            || (in_array('hr_staff', $allowedRoles, true) && $user->isHrStaff())
            || (in_array('hr_assistant', $allowedRoles, true) && $user->isHrAssistant())
            || (in_array('member', $allowedRoles, true) && $user->isMember());

        if (! $hasAccess) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}