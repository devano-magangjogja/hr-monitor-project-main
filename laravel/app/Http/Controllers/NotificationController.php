<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()
            ->notifications()
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(string $id)
    {
        $notification = Auth::user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        return redirect()->back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    public function liveCheck()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['unread_count' => 0, 'notifications' => []]);
        }

        $unreadNotifications = $user->unreadNotifications()->latest()->take(5)->get();

        $data = $unreadNotifications->map(function ($n) {
            $notifData = $n->data;
            $isCustom  = ($notifData['type'] ?? null) === 'custom';
            $title     = $isCustom ? ($notifData['title'] ?? 'Pengumuman') : ($notifData['task_title'] ?? 'Tugas Baru');
            $body      = $notifData['message'] ?? '';
            $sender    = $notifData['sender_name'] ?? 'Admin / HR';

            return [
                'id'          => $n->id,
                'title'       => $title,
                'message'     => $body,
                'sender_name' => $sender,
                'time_ago'    => $n->created_at->locale('id')->diffForHumans(),
                'is_custom'   => $isCustom,
                'created_at'  => $n->created_at->toIso8601String(),
            ];
        });

        return response()->json([
            'unread_count'  => $user->unreadNotifications()->count(),
            'notifications' => $data,
        ]);
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}