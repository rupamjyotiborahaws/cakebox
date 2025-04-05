<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\Frontend\FrontendController;
use App\Http\Controllers\Admin\AdminController;

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

Route::get('/test-push', function () {
    $user = \App\Models\User::find(12);
    $user->notify(new \App\Notifications\NewOrderNotification());
});

// -----------------------------------Common Routes--------------------------------------------
Route::get('/', [FrontendController::class, 'index'])->name('index');
Route::get('/products', [FrontendController::class, 'products'])->name('products');
Route::get('/user-login', [AuthController::class, 'userLogin'])->name('user-login');
Route::get('auth/{provider}', [SocialController::class, 'redirect']);
Route::get('auth/{provider}/callback', [SocialController::class, 'callback']);


// ------------------------------------User Routes----------------------------------------------
Route::middleware(['session_time','ifloggedin'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/order', [OrderController::class, 'new_order'])->name('order');
    Route::post('place-order', [OrderController::class, 'place_order'])->name('place_order');
    Route::get('/past-orders', [OrderController::class, 'past_orders'])->name('past_orders');
    Route::get('/user-profile', [AuthController::class, 'profile'])->name('profile');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});


// -----------------------------------Admin Routes----------------------------------------------
Route::prefix('admin')->middleware(['session_time','ifloggedin'])->group(function() {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin_dashboard');
    Route::get('/order-details/{status_id}', [AdminController::class, 'order_details_by_status'])->name('admin_order_status');
});
