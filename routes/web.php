<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\RehearsalController;
use App\Http\Controllers\ChallengeAccessController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\ChallengeGameController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminChallengeController;
use App\Http\Controllers\Admin\AdminTypingTextController;
use App\Http\Controllers\Admin\AdminGeofenceController;
use App\Http\Controllers\Admin\AdminPlayerController;
use App\Http\Controllers\Admin\AdminAttemptController;
use App\Http\Controllers\Admin\AdminLeaderboardController;
use App\Http\Controllers\Admin\AdminSettingController;
use Illuminate\Support\Facades\Route;

// Frontend routes
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/rehearsal', [RehearsalController::class, 'index'])->name('rehearsal.index');
Route::post('/rehearsal/submit', [RehearsalController::class, 'submit'])->name('rehearsal.submit');

Route::prefix('challenge')->name('challenge.')->group(function () {
    Route::get('/access', [ChallengeAccessController::class, 'access'])->name('access');
    Route::post('/check-location', [ChallengeAccessController::class, 'checkLocation'])->name('check-location');
    Route::post('/request-otp', [ChallengeAccessController::class, 'requestOtp'])->name('request-otp');
    Route::get('/otp', [ChallengeAccessController::class, 'otpForm'])->name('otp');
    Route::post('/verify-otp', [OtpController::class, 'verify'])->name('verify-otp');
    Route::get('/play', [ChallengeGameController::class, 'play'])->name('play');
    Route::post('/start', [ChallengeGameController::class, 'start'])->name('start');
    Route::post('/submit', [ChallengeGameController::class, 'submit'])->name('submit');
    Route::get('/result/{attempt}', [ChallengeGameController::class, 'result'])->name('result');
});

Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');
Route::get('/leaderboard/data', [LeaderboardController::class, 'data'])->name('leaderboard.data');

// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware(\App\Http\Middleware\AdminAuth::class)->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('/challenges', AdminChallengeController::class)
            ->names('challenges')
            ->parameters(['challenges' => 'challenge']);

        Route::resource('/typing-texts', AdminTypingTextController::class)
            ->names('typing-texts')
            ->parameters(['typing-texts' => 'typingText']);

        Route::resource('/geofence', AdminGeofenceController::class)
            ->names('geofence')
            ->parameters(['geofence' => 'geofence']);

        Route::prefix('players')->name('players.')->group(function () {
            Route::get('/', [AdminPlayerController::class, 'index'])->name('index');
            // Bulk routes must be declared before /{player} to avoid wildcard conflict
            Route::post('/bulk-destroy', [AdminPlayerController::class, 'bulkDestroy'])->name('bulk-destroy');
            Route::post('/bulk-block', [AdminPlayerController::class, 'bulkBlock'])->name('bulk-block');
            Route::post('/bulk-unblock', [AdminPlayerController::class, 'bulkUnblock'])->name('bulk-unblock');
            Route::get('/{player}', [AdminPlayerController::class, 'show'])->name('show');
            Route::post('/{player}/block', [AdminPlayerController::class, 'block'])->name('block');
            Route::post('/{player}/unblock', [AdminPlayerController::class, 'unblock'])->name('unblock');
            Route::delete('/{player}', [AdminPlayerController::class, 'destroy'])->name('destroy');
        });

        Route::get('/attempts', [AdminAttemptController::class, 'index'])->name('attempts.index');
        Route::get('/leaderboard', [AdminLeaderboardController::class, 'index'])->name('leaderboard.index');

        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
    });
});
