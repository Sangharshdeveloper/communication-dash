<?php

use App\Http\Controllers\Api\CustomerChatApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Public endpoint for external systems to register a customer and obtain
| a chat URL. No auth — protect in production with a throttle + API key
| middleware (example shown).
|
| Rate-limit: 30 requests / minute / IP
*/

Route::middleware('throttle:30,1')->group(function () {
    Route::post('/customer/chat-link', [CustomerChatApiController::class, 'generateLink'])
         ->name('api.customer.chat-link');
    
    Route::get('/customer/chat-link', [CustomerChatApiController::class, 'generateLink'])
        ->name('api.customer.chat-link.get');

});