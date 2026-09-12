<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskAssigned extends Notification
{
    use Queueable;

    public function __construct(
        protected Task $task,
        protected string $assignerName
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'task_id'       => $this->task->id,
            'task_title'    => $this->task->title,
            'task_date'     => $this->task->task_date->format('Y-m-d'),
            'assigner_name' => $this->assignerName,
            'message'       => 'Anda mendapat tugas baru dari ' . $this->assignerName,
        ];
    }
}