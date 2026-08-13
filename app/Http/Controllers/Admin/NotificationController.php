<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\CustomNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $users = User::whereIn('role', ['hr_staff', 'hr_assistant', 'cs', 'ob'])
            ->where('is_active', 1)
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        // Riwayat notifikasi custom yang sudah dikirim admin
        // Diambil dari tabel notifications, type = CustomNotification
        $sent = \App\Models\Notification::where('type', \App\Notifications\CustomNotification::class)
            ->latest()
            ->take(50)
            ->get()
            ->map(function ($n) {
                $n->recipient = User::find($n->notifiable_id);
                return $n;
            });

        return view('admin.notifications.index', compact('users', 'sent'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'      => ['required', 'string', 'max:100'],
            'message'    => ['required', 'string', 'max:500'],
            'recipients' => ['required', 'in:all,hr_staff,hr_assistant,cs,ob,specific'],
            'user_ids'   => ['required_if:recipients,specific', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $senderName = Auth::user()->name;

        // Tentukan penerima
        if ($validated['recipients'] === 'specific') {
            $users = User::whereIn('id', $validated['user_ids'])
                ->whereIn('role', ['hr_staff', 'hr_assistant', 'cs', 'ob'])
                ->where('is_active', 1)
                ->get();
        } else {
            $query = User::where('is_active', 1);

            if ($validated['recipients'] !== 'all') {
                $query->where('role', $validated['recipients']);
            } else {
                $query->whereIn('role', ['hr_staff', 'hr_assistant', 'cs', 'ob']);
            }

            $users = $query->get();
        }

        if ($users->isEmpty()) {
            return back()->with('error', 'Tidak ada penerima yang ditemukan.');
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

        return redirect()->route('admin.notifications.index')
            ->with('success', "Notifikasi berhasil dikirim ke {$count} pengguna.");
    }
}
