<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use NotificationChannels\WebPush\PushSubscription;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\PushSubscriptionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::prefix('v1')->group(function () {
    Route::post('send-otp', [UserController::class, 'send_otp']);
    Route::post('validate-otp', [UserController::class, 'validate_otp']);
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('login', [AuthController::class, 'login'])->name('login');
});

Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {

    // --------------------------- User APIs -------------------------------
    Route::get('get-my-orders', [UserController::class, 'get_my_orders']);
    Route::post('update-profile', [UserController::class, 'update_profile']);

    // --------------------------- Admin APIs -------------------------------
    Route::get('orders-for-admin-dashboard', [AdminController::class, 'get_orders_for_admin_dashboard']);
    Route::get('process-order', [AdminController::class, 'process_order']);
    Route::get('deliver-order', [AdminController::class, 'deliver_order']);
    Route::post('push/subscribe', [PushSubscriptionController::class, 'store']);
});
