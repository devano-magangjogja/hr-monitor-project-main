<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Notifications\CustomNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        // Hanya HR Assistant aktif
        $users = User::where('role', 'hr_assistant')
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        // Riwayat notifikasi custom yang dikirim ke hr_assistant
        $sent = Notification::where('type', \App\Notifications\CustomNotification::class)
            ->latest()
            ->take(50)
            ->get()
            ->filter(function ($n) {
                $recipient = User::find($n->notifiable_id);
                return $recipient && $recipient->role === 'hr_assistant';
            })
            ->map(function ($n) {
                $n->recipient = User::find($n->notifiable_id);
                return $n;
            })
            ->values();

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

        $senderName = Auth::user()->name;

        if ($validated['recipients'] === 'specific') {
            // Pastikan hanya bisa kirim ke hr_assistant
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
            $senderName
        );

        foreach ($users as $user) {
            $user->notify($notification);
        }

        $count = $users->count();

        return redirect()->route('staff.notifications.index')
            ->with('success', "Notifikasi berhasil dikirim ke {$count} HR Assistant.");
    }
}
