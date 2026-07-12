<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnalyticsController;


Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Tasks
    Route::get('/tasks/trash', [TaskController::class, 'trash'])->name('tasks.trash');
    Route::post('/tasks/restore/{id}', [TaskController::class, 'restore'])->name('tasks.restore');
    Route::delete('/tasks/force-delete/{id}', [TaskController::class, 'forceDelete'])->name('tasks.forceDelete');
    Route::post('/tasks/bulk-delete', [TaskController::class, 'bulkDelete'])->name('tasks.bulkDelete');
    Route::patch('/tasks/{task}/toggle-active', [TaskController::class, 'toggleActive'])->name('tasks.toggleActive');
    Route::resource('tasks', TaskController::class)->except(['show']);

    // Daily tracking
    Route::post('/tasks/{task}/done', [DashboardController::class, 'markDone'])->name('tasks.markDone');
    Route::post('/tasks/{task}/skip', [DashboardController::class, 'markSkipped'])->name('tasks.markSkipped');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
});

require __DIR__ . '/auth.php';
