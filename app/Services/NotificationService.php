<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Notifications\CustomNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class NotificationService
{
    public static function audienceLabel(string $recipients, string $context = 'admin'): string
    {
        if ($context === 'staff' && $recipients === 'all') {
            return 'Semua HR Assistant';
        }

        if ($recipients === 'all') {
            return 'Semua Anggota Tim';
        }

        if ($recipients === 'specific') {
            return 'Pengguna tertentu';
        }

        $role = \App\Models\Role::where('name', $recipients)->first();
        if ($role) {
            return 'Semua ' . $role->label;
        }

        return match ($recipients) {
            'hr_staff'     => 'Semua HR Staff',
            'hr_assistant' => 'Semua HR Assistant',
            'cs'           => 'Semua CS (Customer Service)',
            'ob'           => 'Semua OB (Office Boy)',
            'programmer'   => 'Semua Programmer',
            'dg'           => 'Semua DG (Design Graphics)',
            'vg'           => 'Semua VG (Videografer)',
            'pm'           => 'Semua PM (Project Manager)',
            default        => 'Pengguna tertentu',
        };
    }

    /**
     * Riwayat kirim: satu baris per pengiriman (batch), bukan per penerima.
     */
    public static function groupSentCustomNotifications(Builder $query, int $limit = 50): Collection
    {
        $notifications = $query
            ->where('type', CustomNotification::class)
            ->latest()
            ->take(500)
            ->get();

        $users = User::whereIn('id', $notifications->pluck('notifiable_id')->unique())
            ->get()
            ->keyBy('id');

        return $notifications
            ->groupBy(function (Notification $n) {
                $data = $n->data ?? [];

                if (!empty($data['batch_id'])) {
                    return $data['batch_id'];
                }

                // Fallback grouping for legacy notifications sent in the same batch
                $timeKey = $n->created_at ? $n->created_at->format('Y-m-d H:i') : '';
                return ($data['title'] ?? '') . '|' . ($data['message'] ?? '') . '|' . ($data['sender_name'] ?? '') . '|' . $timeKey;
            })
            ->map(function (Collection $items) use ($users) {
                $first = $items->first();
                $data  = $first->data ?? [];
                $audience = $data['audience'] ?? null;

                $recipients = $items
                    ->map(fn (Notification $n) => $users->get($n->notifiable_id))
                    ->filter()
                    ->unique('id')
                    ->values();

                // Check if bulk or specific
                if ($audience !== null) {
                    $isBulk = $audience !== 'specific';
                    $audienceLabel = $data['audience_label'] ?? self::audienceLabel($audience);
                } else {
                    // For legacy items without audience field
                    if ($items->count() > 1) {
                        $isBulk = true;
                        $distinctRoles = $recipients->pluck('role')->unique();
                        if ($distinctRoles->count() === 1 && $distinctRoles->first() === 'hr_assistant') {
                            $audienceLabel = 'Semua HR Assistant';
                        } elseif ($distinctRoles->count() === 1) {
                            $firstRec = $recipients->first();
                            $audienceLabel = 'Semua ' . ($firstRec ? $firstRec->role_label : 'Anggota');
                        } else {
                            $audienceLabel = 'Semua Anggota Tim';
                        }
                    } else {
                        $isBulk = false;
                        $audienceLabel = 'Pengguna tertentu';
                    }
                }

                $first->is_bulk = $isBulk;
                $first->audience_label = $audienceLabel;
                $first->sender_name = $data['sender_name'] ?? 'Admin / Staff';
                $first->sender_role = $data['sender_role'] ?? null;
                $first->recipient_count = (int) ($data['recipient_count'] ?? $items->count());
                $first->read_count = $items->whereNotNull('read_at')->count();
                $first->group_size = $items->count();
                $first->recipients = $recipients;
                $first->recipient = $recipients->first();

                return $first;
            })
            ->values()
            ->take($limit);
    }
}
