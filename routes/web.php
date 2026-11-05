<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FypController;

// 1. Paparan Halaman Login
Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/', function () {
    return redirect()->route('login');
});

// 2. Proses Hantar Form Login & Logout
Route::post('/login', [FypController::class, 'login'])->name('login.post');
Route::post('/logout', [FypController::class, 'logout'])->name('logout');

// 3. Route Dilindungi Middleware Auth
Route::middleware(['auth'])->group(function () {
    // Worker / Staff
    Route::get('/worker/profile', [FypController::class, 'workerProfile'])->name('worker.dashboard');

    // HR Routes
    Route::get('/hr/dashboard', [FypController::class, 'hrDashboard'])->name('hr.dashboard');
    Route::get('/hr/kpi-management', [FypController::class, 'kpiManagement'])->name('hr.kpi');
    Route::get('/hr/report', [FypController::class, 'hrReport'])->name('hr.report');
    Route::get('/hr/emergency-leave', [FypController::class, 'emerLeave'])->name('hr.emerleave');
    Route::post('/hr/update-target', [FypController::class, 'updateTarget'])->name('hr.updateTarget');
    Route::post('/hr/update-formula', [FypController::class, 'updateFormula'])->name('hr.updateFormula');
    Route::post('/hr/update-thresholds', [FypController::class, 'updateThresholds'])->name('hr.updateThresholds');

    // Leader
    Route::get('/leader/dashboard', [FypController::class, 'leaderDashboard'])->name('leader.dashboard');
});