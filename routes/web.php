<?php

use App\Http\Controllers\Auth\MagicLinkController;
use App\Http\Controllers\Auth\StaffLoginController;
use App\Http\Controllers\ShortUrlController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DirectChatController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// ── Root ───────────────────────────────────────────────────────────────────
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});
Route::prefix('api')->name('api.')->group(function () {
    Route::post('/chat/generate-link', [\App\Http\Controllers\Api\CustomerChatApiController::class, 'generateLink'])
        ->name('chat.generate-link');
});
// ── Guest auth routes ──────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [StaffLoginController::class, 'showForm'])->name('login');
    Route::post('/staff/login', [StaffLoginController::class, 'login'])->name('staff.login');
    Route::post('/auth/magic-link', [MagicLinkController::class, 'requestLink'])->name('auth.magic.request');
    Route::get('/auth/magic-link/verify', [MagicLinkController::class, 'verifyLink'])
        ->name('auth.magic.verify')->middleware('signed');
    Route::get('/auth/otp', [MagicLinkController::class, 'showOtpForm'])->name('auth.otp.form');
    Route::post('/auth/otp', [MagicLinkController::class, 'verifyOtp'])->name('auth.otp.verify');
});

Route::get('/direct-chat/history', [\App\Http\Controllers\DirectChatController::class, 'history'])
     ->name('direct-chat.history');
    
// ── Public customer chat ───────────────────────────────────────────────────
Route::get('/c/{agentId}', [DirectChatController::class, 'enter'])->name('direct-chat.enter');
Route::get('/c/{agentId}/chat', [DirectChatController::class, 'show'])->name('direct-chat.show');
Route::post('/direct-chat/send', [DirectChatController::class, 'send'])->name('direct-chat.send');
Route::get('/direct-chat/poll', [DirectChatController::class, 'poll'])->name('direct-chat.poll');
Route::get('/direct-chat/attachment/{attachment}', [DirectChatController::class, 'downloadAttachment'])
    ->name('direct-chat.attachment.download');
    
Route::get('/short/{code}', [ShortUrlController::class, 'redirect'])->name('short-url.redirect');

Route::get('/clear-cache', function () {

    Artisan::call('route:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');

    return 'Caches cleared safely!';
});
// ── Protected routes ────────────────────────────────────────────────────────
Route::middleware(['auth', 'inactivity.timeout'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [MagicLinkController::class, 'logout'])->name('auth.logout');

    // Team chat (all staff)
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('/{room}/messages', [ChatController::class, 'messages'])->name('messages');
        Route::post('/{room}/send', [ChatController::class, 'send'])->name('send');
        Route::get('/{room}/poll', [ChatController::class, 'poll'])->name('poll');
    });

    // Agent direct chat (admin + agent access)
    Route::middleware('role:admin,agent')->prefix('agent')->name('direct-chat.agent.')->group(function () {
        Route::get('/inbox', [DirectChatController::class, 'agentInbox'])->name('inbox');
        Route::get('/session/{session}', [DirectChatController::class, 'agentSession'])->name('session');
    });

    // Admin: full CRUD + link management
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {

        // Audit logs (admin + auditor)
        Route::get('/audit-logs', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-logs');

        // User CRUD
        Route::get('/users',                 [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users');
        Route::post('/users',                [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}',          [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
        Route::put('/users/{user}',          [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}',       [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');

        // User actions (chat links, magic links)
        Route::post('/users/chat-link',             [\App\Http\Controllers\Admin\UserController::class, 'generateChatLink'])->name('users.chat-link');
        Route::post('/users/{user}/send-magic-link',[\App\Http\Controllers\Admin\UserController::class, 'sendMagicLink'])->name('users.send-magic-link');
        Route::post('/users/{user}/send-chat-link', [\App\Http\Controllers\Admin\UserController::class, 'sendChatLink'])->name('users.send-chat-link');
        Route::get('/users/{user}/agent-sessions',  [\App\Http\Controllers\Admin\UserController::class, 'agentSessions'])->name('users.agent-sessions');
    });

    // Auditor: view-only audit logs
    Route::middleware('role:auditor')->prefix('auditor')->name('auditor.')->group(function () {
        Route::get('/audit-logs', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-logs');
    });
});