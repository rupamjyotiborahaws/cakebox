<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\Frontend\FrontendController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [FrontendController::class, 'index'])->name('index');
Route::get('/products', [FrontendController::class, 'products'])->name('products');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::get('/user-login', [AuthController::class, 'userLogin'])->name('user-login');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('auth/{provider}', [SocialController::class, 'redirect']);
Route::get('auth/{provider}/callback', [SocialController::class, 'callback']);

Route::middleware(['session_time','ifloggedin'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/order', [OrderController::class, 'new_order'])->name('order');
    Route::post('place-order', [OrderController::class, 'place_order'])->name('place_order');
    Route::get('/past-orders', [OrderController::class, 'past_orders'])->name('past_orders');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});
