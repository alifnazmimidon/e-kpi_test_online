<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FypController;
use App\Http\Controllers\ProfileController;

// 1. Paparan Halaman Login (Hanya untuk pengguna yang belum login)
Route::get('/login', function () {
    if (Auth::check()) {
        return match (strtoupper(Auth::user()->role ?? '')) {
            'HR'     => redirect()->route('hr.dashboard'),
            'LEADER' => redirect()->route('leader.dashboard'),
            default  => redirect()->route('worker.dashboard'),
        };
    }

    return view('login');
})->name('login');

Route::get('/', function () {
    return redirect()->route('login');
});

// 2. Proses Hantar Form Login & Logout
    Route::post('/login', [FypController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('login.post');
    Route::post('/logout', [FypController::class, 'logout'])->name('logout');

// 2B. Forgot & Reset Password (Sebelum Middleware Auth)
    Route::get('/forgot-password', [FypController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [FypController::class, 'verifyForgotPassword'])
    ->middleware('throttle:5,1')
    ->name('password.verify');
    Route::post('/forgot-password/resend', [FypController::class, 'resendForgotOtp'])
    ->middleware('throttle:3,1')
    ->name('password.resend');
    Route::get('/reset-password', [FypController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [FypController::class, 'updateResetPassword'])
    ->middleware('throttle:5,1')
    ->name('password.update');

// 3. Semua Halaman Sistem Dilindungi Middleware Auth (Belum login -> tendang ke /login)
Route::middleware(['auth'])->group(function () {

    // Tetapan profil peribadi untuk pengguna yang sudah login
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('/worker/profile', [FypController::class, 'workerProfile'])->name('worker.dashboard');
        Route::post('/worker/profile/photo', [FypController::class, 'updateProfilePhoto'])->name('worker.photo.update');

    // Emergency leave
        Route::post('/worker/emergency-leave', [App\Http\Controllers\FypController::class, 'submitWorkerEmergencyLeave'])
            ->name('worker.emergency-leave.store');

    // Notifikasi worker
        Route::post('/worker/notifications/read', [FypController::class, 'markWorkerNotificationsRead'])
            ->name('worker.notifications.read');

    // Khas untuk HR sahaja (Worker & Leader akan dapat 403 jika cuba masuk)
    Route::middleware(['role:HR'])->group(function () {
        Route::get('/hr/dashboard', [FypController::class, 'hrDashboard'])->name('hr.dashboard');
        Route::get('/hr/kpi-management', [FypController::class, 'kpiManagement'])->name('hr.kpi');
        Route::get('/hr/report', [FypController::class, 'hrReport'])->name('hr.report');
        Route::post('/hr/report/generate', [FypController::class, 'generateReport'])->name('hr.report.generate');
        Route::delete('/hr/report/{id}', [FypController::class, 'deleteReport'])->name('hr.report.delete');
        Route::get('/hr/emergency-leave', [FypController::class, 'emerLeave'])->name('hr.emerleave');
        Route::post('/hr/emergency-leave/override', [FypController::class, 'storeEmerLeave'])->name('hr.emerleave.store');
        Route::post('/hr/update-target', [FypController::class, 'updateTarget'])->name('hr.updateTarget');
        Route::post('/hr/update-formula', [FypController::class, 'updateFormula'])->name('hr.updateFormula');
        Route::post('/hr/update-thresholds', [FypController::class, 'updateThresholds'])->name('hr.updateThresholds');

        //Bahagian AI untuk pekerja

        Route::post('/hr/ai/refresh', [FypController::class, 'refreshAiInsights'])->name('hr.ai.refresh');
        Route::post('/hr/ai/reminder-draft', [FypController::class, 'generateAiReminderDraft'])->name('hr.ai.reminder');

        //Bahagian untuk Emergency Leave

        Route::get('/hr/emergency-leave', [FypController::class, 'emerLeave'])->name('hr.emerleave');
        Route::post('/hr/emergency-leave/override', [FypController::class, 'storeEmerLeave'])->name('hr.emerleave.store');
        Route::get('/hr/notifications', [FypController::class, 'getHrNotifications'])->name('hr.notifications');
        
    });

    // Khas untuk LEADER sahaja (Worker & HR akan dapat 403 jika cuba masuk)
    Route::middleware(['role:LEADER'])->group(function () {
        Route::get('/leader/dashboard', [FypController::class, 'leaderDashboard'])->name('leader.dashboard');
        Route::get('/leader/dashboard/data', [FypController::class, 'leaderDashboardData'])->name('leader.dashboard.data');
        Route::get('/leader/input-rating', [FypController::class, 'leaderInputRating'])->name('leader.kpi');
        Route::get('/leader/input-rating/data', [FypController::class, 'leaderInputRatingData'])->name('leader.kpi.data');
        Route::get('/leader/team-status', [FypController::class, 'leaderTeamStatus'])->name('leader.team.status');
        Route::get('/leader/team-status/data', [FypController::class, 'leaderTeamStatusData'])->name('leader.team.status.data');
        Route::get('/leader/reports', [FypController::class, 'leaderReports'])->name('leader.reports');
        Route::get('/leader/reports/data', [FypController::class, 'leaderReportsData'])->name('leader.reports.data');
        Route::post('/leader/reports/submit', [FypController::class, 'submitLeaderReportToHr'])->name('leader.reports.submit');
        Route::post('/leader/input-rating/save', [FypController::class, 'saveLeaderInputRating'])->name('leader.kpi.save');
        Route::post('/leader/rating', [FypController::class, 'saveLeaderRating'])->name('leader.rating.save');
        Route::post('/leader/reminder', [FypController::class, 'sendLeaderReminder'])->name('leader.reminder');
    });
});