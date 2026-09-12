<?php

namespace App\Http\Controllers\Staff;

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
        $users = User::where('role', 'hr_assistant')
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        $assistantIds = User::where('role', 'hr_assistant')->pluck('id');

        $sent = NotificationService::groupSentCustomNotifications(
            Notification::query()
                ->where('notifiable_type', User::class)
                ->whereIn('notifiable_id', $assistantIds)
        );

        return view('staff.notifications.index', compact('users', 'sent'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'      => ['required', 'string', 'max:100'],
            'message'    => ['required', 'string', 'max:500'],
            'recipients' => ['required', 'in:all,specific'],
            'user_ids'   => ['required_if:recipients,specific', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $sender = Auth::user();

        if ($validated['recipients'] === 'specific') {
            $users = User::whereIn('id', $validated['user_ids'])
                ->where('role', 'hr_assistant')
                ->where('is_active', 1)
                ->get();
        } else {
            $users = User::where('role', 'hr_assistant')
                ->where('is_active', 1)
                ->get();
        }

        if ($users->isEmpty()) {
            return back()->with('error', 'Tidak ada HR Assistant yang ditemukan.');
        }

        $notification = new CustomNotification(
            $validated['title'],
            $validated['message'],
            $sender->name,
            $sender->id,
            $sender->role_label,
            $validated['recipients'],
            NotificationService::audienceLabel($validated['recipients'], 'staff'),
            (string) Str::uuid(),
            $users->count()
        );

        foreach ($users as $user) {
            $user->notify($notification);
        }

        $count = $users->count();

        return redirect()->route('staff.notifications.index')
            ->with('success', "Notifikasi berhasil dikirim ke {$count} HR Assistant.");
    }
}
