<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Auth\AuthController;

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
    Route::get('get-my-orders', [UserController::class, 'get_my_orders']);
    Route::post('update-profile', [UserController::class, 'update_profile']);
});