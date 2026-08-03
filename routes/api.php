<?php

use App\Http\Controllers\KashierWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::match(['POST', 'GET'], '/webhooks/kashier', [KashierWebhookController::class, 'handle'])
    ->name('api.webhooks.kashier');
`