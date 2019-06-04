<?php

use Illuminate\Support\Facades\Route;
use Riclep\NovaMailchimpField\Http\Controllers\NovaMailchimpFieldController;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

Route::post('/subscription-status', NovaMailchimpFieldController::class.'@subscriptionStatus');
Route::post('/update-interests', NovaMailchimpFieldController::class.'@updateInterests');
Route::post('/subscribe', NovaMailchimpFieldController::class.'@subscribe');
Route::post('/unsubscribe', NovaMailchimpFieldController::class.'@unsubscribe');
Route::post('/status', NovaMailchimpFieldController::class.'@status');