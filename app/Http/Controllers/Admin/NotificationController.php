<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Notifications\CustomNotification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    public function index()
    {
        $users = User::where('role', '!=', 'admin')
            ->where('is_active', 1)
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        $roles = \App\Models\Role::where('name', '!=', 'admin')->orderBy('id')->get();

        $sent = NotificationService::groupSentCustomNotifications(
            Notification::query()
        );

        return view('admin.notifications.index', compact('users', 'roles', 'sent'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'      => ['required', 'string', 'max:100'],
            'message'    => ['required', 'string', 'max:500'],
            'recipients' => ['required', 'string'],
            'user_ids'   => ['required_if:recipients,specific', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $sender = Auth::user();

        if ($validated['recipients'] === 'specific') {
            $users = User::whereIn('id', $validated['user_ids'])
                ->where('role', '!=', 'admin')
                ->where('is_active', 1)
                ->get();
        } else {
            $query = User::where('is_active', 1);

            if ($validated['recipients'] !== 'all') {
                $query->where('role', $validated['recipients']);
            } else {
                $query->where('role', '!=', 'admin');
            }

            $users = $query->get();
        }

        if ($users->isEmpty()) {
            return back()->with('error', 'Tidak ada penerima yang ditemukan.');
        }

        $notification = new CustomNotification(
            $validated['title'],
            $validated['message'],
            $sender->name,
            $sender->id,
            $sender->role_label,
            $validated['recipients'],
            NotificationService::audienceLabel($validated['recipients']),
            (string) Str::uuid(),
            $users->count()
        );

        foreach ($users as $user) {
            $user->notify($notification);
        }

        $count = $users->count();

        return redirect()->route('admin.notifications.index')
            ->with('success', "Notifikasi berhasil dikirim ke {$count} pengguna.");
    }
}
