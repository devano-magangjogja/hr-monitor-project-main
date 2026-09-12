<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CustomNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $title,
        protected string $message,
        protected string $senderName,
        protected ?int $senderId = null,
        protected ?string $senderRole = null,
        protected string $audience = 'specific',
        protected string $audienceLabel = '',
        protected string $batchId = '',
        protected int $recipientCount = 1,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'             => 'custom',
            'title'            => $this->title,
            'message'          => $this->message,
            'sender_name'      => $this->senderName,
            'sender_id'        => $this->senderId,
            'sender_role'      => $this->senderRole,
            'audience'         => $this->audience,
            'audience_label'   => $this->audienceLabel,
            'batch_id'         => $this->batchId,
            'recipient_count'  => $this->recipientCount,
        ];
    }
}
