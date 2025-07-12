<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\Frontend\FrontendController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\PaymentController;

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
Route::get('/products', [FrontendController::class, 'products'])->name('products');

Route::middleware(['session_time','checkloggedin'])->group(function() {
    Route::get('/', [FrontendController::class, 'index'])->name('index');
    Route::get('/user-login', [AuthController::class, 'userLogin'])->name('user-login');
    Route::get('auth/{provider}', [SocialController::class, 'redirect']);
    Route::get('auth/{provider}/callback', [SocialController::class, 'callback']);
});


// ------------------------------------User Routes----------------------------------------------
Route::middleware(['session_time','ifloggedin'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/order', [OrderController::class, 'new_order'])->name('order');
    Route::post('place-order', [OrderController::class, 'place_order'])->name('place_order');
    Route::get('/your-orders', [OrderController::class, 'past_orders'])->name('your_orders');
    Route::get('/user-profile', [AuthController::class, 'profile'])->name('profile');
    Route::get('/logout-user', [AuthController::class, 'logout'])->name('logout_user');
    Route::post('/create-payment-order', [PaymentController::class, 'createOrder']);
    Route::post('/payment-success', [PaymentController::class, 'paymentSuccess']);
    Route::get('/check-status', [PaymentController::class, 'checkOrderStatus']);
    Route::post('/razorpay/paymentwebhook', [PaymentController::class, 'webhookHandler']);
});


// -----------------------------------Admin Routes----------------------------------------------
Route::prefix('admin')->middleware(['session_time','ifadminloggedin'])->group(function() {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin_dashboard');
    Route::get('/order-details/{status_id}', [AdminController::class, 'order_details_by_status'])->name('admin_order_status');
    Route::get('/logout-admin', [AuthController::class, 'logout'])->name('logout_admin');
});
