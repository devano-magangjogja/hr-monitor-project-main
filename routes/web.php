<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DefaultTaskController;
use App\Http\Controllers\Admin\TaskController as AdminTaskController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboard;
use App\Http\Controllers\Staff\TaskController as StaffTaskController;
use App\Http\Controllers\Staff\UserController as StaffUserController;
use App\Http\Controllers\Staff\NotificationController as StaffNotificationController;
use App\Http\Controllers\Cs\DashboardController as CsDashboard;
use App\Http\Controllers\Cs\TaskController as CsTaskController;
use App\Http\Controllers\Ob\DashboardController as ObDashboard;
use App\Http\Controllers\Ob\TaskController as ObTaskController;
use App\Http\Controllers\Assistant\DashboardController as AssistantDashboard;
use App\Http\Controllers\Assistant\TaskController as AssistantTaskController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;

// ── Auth (All Roles) ────────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', [LoginController::class, 'showForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ── ADMIN ────────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::get('/team-progress/{user}', [AdminDashboard::class, 'teamProgressDetail'])->name('dashboard.team-progress-detail');
    Route::resource('users', UserController::class)
        ->only(['index', 'store', 'update', 'destroy']);
    Route::patch('/users/{user}/password', [UserController::class, 'updatePassword'])->name('users.password');
    Route::resource('default-tasks', DefaultTaskController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    // Task 
    Route::resource('tasks', AdminTaskController::class)
        ->only(['index', 'store', 'update', 'destroy']);
    Route::get('/tasks/staff', [AdminTaskController::class, 'staffTasks'])->name('tasks.staff');
    Route::get('/tasks/assistant', [AdminTaskController::class, 'assistantTasks'])->name('tasks.assistant');
    Route::get('/tasks/cs', [AdminTaskController::class, 'csTasks'])->name('tasks.cs');
    Route::get('/tasks/ob', [AdminTaskController::class, 'obTasks'])->name('tasks.ob');
    Route::get('/tasks/programmer', [AdminTaskController::class, 'programmerTasks'])->name('tasks.programmer');
    Route::get('/tasks/dg', [AdminTaskController::class, 'dgTasks'])->name('tasks.dg');
    Route::get('/tasks/vg', [AdminTaskController::class, 'vgTasks'])->name('tasks.vg');
    Route::get('/tasks/pm', [AdminTaskController::class, 'pmTasks'])->name('tasks.pm');
    Route::delete('/tasks/{task}/force', [AdminTaskController::class, 'forceDestroy'])->name('tasks.force-destroy');

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/productivity', [ReportController::class, 'productivity'])->name('productivity');
        Route::get('/productivity/{user}', [ReportController::class, 'productivityDetail'])->name('productivity.detail');
        Route::get('/history', [ReportController::class, 'history'])->name('history');
        Route::get('/ranking', [ReportController::class, 'ranking'])->name('ranking');
        // Admin kelola assignment bawahan
        Route::patch('/assignments/{assignmentId}/complete', [ReportController::class, 'completeAssignment'])->name('assignments.complete');
        Route::delete('/assignments/{assignmentId}', [ReportController::class, 'destroyAssignment'])->name('assignments.destroy');
    });

    // Notifikasi Custom
    Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications', [AdminNotificationController::class, 'store'])->name('notifications.store');

    // Pengaturan
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/app-info', [AdminSettingController::class, 'updateAppInfo'])->name('settings.app-info');
    Route::post('/settings/wa-groups', [AdminSettingController::class, 'storeWaGroup'])->name('settings.wa-groups.store');
    Route::patch('/settings/wa-groups/{waGroup}', [AdminSettingController::class, 'updateWaGroup'])->name('settings.wa-groups.update');
    Route::delete('/settings/wa-groups/{waGroup}', [AdminSettingController::class, 'destroyWaGroup'])->name('settings.wa-groups.destroy');
});

// ── HR Staff ────────────────────────────────────────────────────────────────
Route::prefix('staff')->name('staff.')->middleware(['auth', 'role:hr_staff'])->group(function () {
    Route::get('/dashboard', [StaffDashboard::class, 'index'])->name('dashboard');
    Route::resource('tasks', StaffTaskController::class) ->only(['index', 'store', 'update', 'destroy']);
    Route::patch('/tasks/{task}/complete', [StaffTaskController::class, 'complete'])->name('tasks.complete');
    Route::get('/tasks/daily', [StaffTaskController::class, 'dailyIndex'])->name('tasks.daily');
    Route::patch('/tasks/daily/{task}/complete', [StaffTaskController::class, 'dailyComplete'])->name('tasks.daily.complete');
    Route::get('/tasks/assigned', [StaffTaskController::class, 'assignedIndex'])->name('tasks.assigned');
    Route::patch('/tasks/assigned/{task}/complete', [StaffTaskController::class, 'assignedComplete'])->name('tasks.assigned.complete');
    Route::get('/tasks/all', [StaffTaskController::class, 'allIndex'])->name('tasks.all');
    Route::patch('/tasks/all/{task}/complete', [StaffTaskController::class, 'allComplete'])->name('tasks.all.complete');
    Route::get('/history', [StaffTaskController::class, 'history'])->name('tasks.history');
    Route::get('/assign-tasks', [StaffTaskController::class, 'assignIndex'])->name('assign.index');
    Route::post('/assign-tasks', [StaffTaskController::class, 'assignStore'])->name('assign.store');
    Route::patch('/assign-tasks/{task}', [StaffTaskController::class, 'assignUpdate'])->name('assign.update');
    Route::delete('/assign-tasks/{task}', [StaffTaskController::class, 'assignDestroy'])->name('assign.destroy');
    Route::get('/assistant-progress', [StaffTaskController::class, 'assistantProgress'])->name('assistant-progress');
    Route::get('/assistant-progress/{user}', [StaffTaskController::class, 'assistantProgressDetail'])->name('assistant-progress.detail');

    // Manajemen HR Assistant
    Route::resource('users', StaffUserController::class)
        ->only(['index', 'store', 'update', 'destroy']);
    Route::patch('/users/{user}/password', [StaffUserController::class, 'updatePassword'])->name('users.password');

    // Notifikasi Custom
    Route::get('/notifications', [StaffNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications', [StaffNotificationController::class, 'store'])->name('notifications.store');
});

// ── CS (Customer Service) ────────────────────────────────────────────────────
Route::prefix('cs')->name('cs.')->middleware(['auth', 'role:cs'])->group(function () {
    Route::get('/dashboard', [CsDashboard::class, 'index'])->name('dashboard');
    Route::resource('tasks', CsTaskController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::patch('/tasks/{task}/complete', [CsTaskController::class, 'complete'])->name('tasks.complete');
    Route::get('/tasks/daily', [CsTaskController::class, 'dailyIndex'])->name('tasks.daily');
    Route::patch('/tasks/daily/{task}/complete', [CsTaskController::class, 'dailyComplete'])->name('tasks.daily.complete');
    Route::get('/tasks/assigned', [CsTaskController::class, 'assignedIndex'])->name('tasks.assigned');
    Route::patch('/tasks/assigned/{task}/complete', [CsTaskController::class, 'assignedComplete'])->name('tasks.assigned.complete');
    Route::get('/tasks/all', [CsTaskController::class, 'allIndex'])->name('tasks.all');
    Route::patch('/tasks/all/{task}/complete', [CsTaskController::class, 'allComplete'])->name('tasks.all.complete');
    Route::get('/history', [CsTaskController::class, 'history'])->name('tasks.history');
});

// ── OB (Office Boy) ──────────────────────────────────────────────────────────
Route::prefix('ob')->name('ob.')->middleware(['auth', 'role:ob'])->group(function () {
    Route::get('/dashboard', [ObDashboard::class, 'index'])->name('dashboard');
    Route::resource('tasks', ObTaskController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::patch('/tasks/{task}/complete', [ObTaskController::class, 'complete'])->name('tasks.complete');
    Route::get('/tasks/daily', [ObTaskController::class, 'dailyIndex'])->name('tasks.daily');
    Route::patch('/tasks/daily/{task}/complete', [ObTaskController::class, 'dailyComplete'])->name('tasks.daily.complete');
    Route::get('/tasks/assigned', [ObTaskController::class, 'assignedIndex'])->name('tasks.assigned');
    Route::patch('/tasks/assigned/{task}/complete', [ObTaskController::class, 'assignedComplete'])->name('tasks.assigned.complete');
    Route::get('/tasks/all', [ObTaskController::class, 'allIndex'])->name('tasks.all');
    Route::patch('/tasks/all/{task}/complete', [ObTaskController::class, 'allComplete'])->name('tasks.all.complete');
    Route::get('/history', [ObTaskController::class, 'history'])->name('tasks.history');
});

// ── HR Assistant ─────────────────────────────────────────────────────────────
Route::prefix('assistant')->name('assistant.')->middleware(['auth', 'role:hr_assistant'])->group(function () {
    Route::get('/dashboard', [AssistantDashboard::class, 'index'])->name('dashboard');
    Route::get('/tasks/routine', [AssistantTaskController::class, 'routineIndex'])->name('tasks.routine');
    Route::patch('/tasks/routine/{task}/complete', [AssistantTaskController::class, 'routineComplete'])->name('tasks.routine.complete');
    Route::get('/tasks/assigned', [AssistantTaskController::class, 'assignedIndex'])->name('tasks.assigned');
    Route::patch('/tasks/assigned/{task}/complete', [AssistantTaskController::class, 'assignedComplete'])->name('tasks.assigned.complete');
    Route::get('/tasks/all', [AssistantTaskController::class, 'allIndex'])->name('tasks.all');
    Route::patch('/tasks/all/{task}/complete', [AssistantTaskController::class, 'allComplete'])->name('tasks.all.complete');
    Route::resource('tasks', AssistantTaskController::class) ->only(['index', 'store']);
    Route::patch('/tasks/{task}', [AssistantTaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [AssistantTaskController::class, 'destroy'])->name('tasks.destroy');
    Route::patch('/tasks/{task}/complete', [AssistantTaskController::class, 'complete'])->name('tasks.complete');
    Route::get('/history', [AssistantTaskController::class, 'history'])->name('tasks.history');
});

// ── Notifications (All Roles) ────────────────────────────────────────────────
Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::patch('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
    Route::patch('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read.all');
});

// ── Profile (All Roles) ──────────────────────────────────────────────────────
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('show');
    Route::patch('/update', [ProfileController::class, 'update'])->name('update');
    Route::patch('/password', [ProfileController::class, 'updatePassword'])->name('password');
});