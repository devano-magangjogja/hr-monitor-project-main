<?php

use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$admin = User::where('role', 'admin')->firstOrFail();

$request = Illuminate\Http\Request::create('/admin/notifications', 'GET');
$app->instance('request', $request);
Illuminate\Support\Facades\View::share('errors', new Illuminate\Support\ViewErrorBag);

auth()->login($admin);

$users = User::where('role', '!=', 'admin')->where('is_active', 1)->orderBy('role')->orderBy('name')->get();
$sent = NotificationService::groupSentCustomNotifications(Notification::query());
$html = view('admin.notifications.index', compact('users', 'sent'))->render();
echo 'admin_ok '.strlen($html).' groups='.$sent->count().PHP_EOL;
echo 'has_dari='.(str_contains($html, 'Dari:') ? 'yes' : 'no').PHP_EOL;

$staff = User::where('role', 'hr_staff')->first();
if ($staff) {
    auth()->login($staff);
    $ausers = User::where('role', 'hr_assistant')->where('is_active', 1)->orderBy('name')->get();
    $aids = User::where('role', 'hr_assistant')->pluck('id');
    $ssent = NotificationService::groupSentCustomNotifications(
        Notification::query()
            ->where('notifiable_type', User::class)
            ->whereIn('notifiable_id', $aids)
    );
    $html2 = view('staff.notifications.index', ['users' => $ausers, 'sent' => $ssent])->render();
    echo 'staff_ok '.strlen($html2).' groups='.$ssent->count().PHP_EOL;
}
