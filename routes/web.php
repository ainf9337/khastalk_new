<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;

// ── Public ───────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));

// ── Auth (Breeze handles login/logout/register/profile) ──────
require __DIR__.'/auth.php';

Route::get('/logout', function () {
    Auth::guard('web')->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout.get');

// ── Redirect /dashboard to role-specific dashboard ───────────
Route::middleware('auth')->get('/dashboard', function () {
    $role = str_replace('_', '-', auth()->user()->role);

    return match($role) {
        'teacher'          => redirect()->route('teacher.dashboard'),
        'parent'           => redirect()->route('parent.dashboard'),
        'admin'            => redirect()->route('admin.dashboard'),
        'senior-assistant' => redirect()->route('senior.dashboard'),
        default            => redirect()->route('login'),
    };
})->name('dashboard');

// ── Teacher routes ────────────────────────────────────────────
Route::middleware(['auth', 'role:teacher'])
     ->prefix('teacher')
     ->name('teacher.')
     ->group(function () {

    Route::get('/dashboard',
        [\App\Http\Controllers\Teacher\DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/students',
        [\App\Http\Controllers\Teacher\StudentController::class, 'index'])
        ->name('students');

    Route::get('/students/{student}',
        [\App\Http\Controllers\Teacher\StudentController::class, 'show'])
        ->name('students.show');

    Route::get('/behaviour-log',
        [\App\Http\Controllers\Teacher\BehaviourLogController::class, 'create'])
        ->name('behaviour-log.create');

    Route::post('/behaviour-log',
        [\App\Http\Controllers\Teacher\BehaviourLogController::class, 'store'])
        ->name('behaviour-log.store');

    Route::get('/emergency',
        [\App\Http\Controllers\Teacher\EmergencyController::class, 'index'])
        ->name('emergency');

    Route::post('/emergency',
        [\App\Http\Controllers\Teacher\EmergencyController::class, 'store'])
        ->name('emergency.store');

    Route::get('/messages',
        [\App\Http\Controllers\Teacher\MessageController::class, 'index'])
        ->name('messages');

    Route::post('/messages',
        [\App\Http\Controllers\Teacher\MessageController::class, 'store'])
        ->name('messages.store');

    Route::get('/rpi',
        [\App\Http\Controllers\Teacher\RpiController::class, 'index'])
        ->name('rpi');

    Route::get('/rpi/create',
        [\App\Http\Controllers\Teacher\RpiController::class, 'create'])
        ->name('rpi.create');

    Route::post('/rpi',
        [\App\Http\Controllers\Teacher\RpiController::class, 'store'])
        ->name('rpi.store');

    Route::get('/rpi/{rpi}',
        [\App\Http\Controllers\Teacher\RpiController::class, 'show'])
        ->name('rpi.show');

    Route::post('/rpi/{rpi}/goals',
        [\App\Http\Controllers\Teacher\RpiController::class, 'addGoal'])
        ->name('rpi.goals.store');

    Route::patch('/rpi/{rpi}/submit',
        [\App\Http\Controllers\Teacher\RpiController::class, 'submit'])
        ->name('rpi.submit');

    Route::patch('/rpi/goals/{goal}',
        [\App\Http\Controllers\Teacher\RpiController::class, 'updateGoal'])
        ->name('rpi.goals.update');

    Route::get('/reports',
        [\App\Http\Controllers\Teacher\ReportController::class, 'index'])
        ->name('reports');
});

// ── Parent routes ─────────────────────────────────────────────
Route::middleware(['auth', 'role:parent'])
     ->prefix('parent')
     ->name('parent.')
     ->group(function () {

    Route::get('/dashboard',
        [\App\Http\Controllers\Parent\DashboardController::class, 'index'])
        ->name('dashboard');

    Route::post('/emergency/{alert}/confirm',
        [\App\Http\Controllers\Parent\DashboardController::class, 'confirmAlert'])
        ->name('emergency.confirm');

    Route::get('/child/{student}',
        [\App\Http\Controllers\Parent\ChildProfileController::class, 'show'])
        ->name('child.show');

    Route::get('/behaviour-history',
        [\App\Http\Controllers\Parent\BehaviourHistoryController::class, 'index'])
        ->name('behaviour-history');

    Route::get('/rpi-progress',
        [\App\Http\Controllers\Parent\RpiProgressController::class, 'index'])
        ->name('rpi-progress');

    Route::get('/messages',
        [\App\Http\Controllers\Parent\MessageController::class, 'index'])
        ->name('messages');

    Route::post('/messages',
        [\App\Http\Controllers\Parent\MessageController::class, 'store'])
        ->name('messages.store');
});

// ── Admin routes ──────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])
     ->prefix('admin')
     ->name('admin.')
     ->group(function () {

    Route::get('/dashboard',
        [\App\Http\Controllers\Admin\DashboardController::class, 'index'])
        ->name('dashboard');

    Route::resource('users',
        \App\Http\Controllers\Admin\UserController::class)
        ->except(['show']);

    Route::get('/users/{user}',
        [\App\Http\Controllers\Admin\UserController::class, 'show'])
        ->name('users.show');

    Route::post('/users/{user}/reset-password',
        [\App\Http\Controllers\Admin\UserController::class, 'resetPassword'])
        ->name('users.reset-password');

    Route::resource('students',
        \App\Http\Controllers\Admin\StudentController::class)
        ->except(['show', 'edit', 'update']);

    Route::resource('classes',
        \App\Http\Controllers\Admin\ClassController::class)
        ->except(['show', 'edit', 'update']);

    Route::get('/activity-log',
        [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])
        ->name('activity-log');
});

// ── Senior Assistant routes ───────────────────────────────────
Route::middleware(['auth', 'role:senior-assistant'])
     ->prefix('senior-assistant')
     ->name('senior.')
     ->group(function () {

    Route::get('/dashboard',
        [\App\Http\Controllers\Senior\DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/rpi-approval',
        [\App\Http\Controllers\Senior\RpiApprovalController::class, 'index'])
        ->name('rpi-approval');

    Route::get('/rpi-approval/{rpi}',
        [\App\Http\Controllers\Senior\RpiApprovalController::class, 'show'])
        ->name('rpi-approval.show');

    Route::patch('/rpi-approval/{rpi}',
        [\App\Http\Controllers\Senior\RpiApprovalController::class, 'update'])
        ->name('rpi-approval.update');
});
