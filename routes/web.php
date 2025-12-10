<?php

use App\Http\Controllers\Clients\CheckoutController;
use App\Http\Controllers\Clients\AccountController;
use App\Http\Controllers\Clients\AuthController;
use App\Http\Controllers\Clients\CartController;
use App\Http\Controllers\Clients\ContactsController;
use App\Http\Controllers\Clients\ForgotPasswordController;
use App\Http\Controllers\Clients\HomeController;
use App\Http\Controllers\Clients\OrderController;
use App\Http\Controllers\Clients\ProductController;
use App\Http\Controllers\Clients\ResetPasswordController;
use App\Http\Controllers\Clients\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/about', function () {
    return view('clients.pages.about');
})->name('about');

Route::get('/service', function () {
    return view('clients.pages.service');
})->name('service');

Route::get('/team', function () {
    return view('clients.pages.team');
})->name('team');

Route::get('/faq', function () {
    return view('clients.pages.faq');
})->name('faq');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('post-register');

//Routue kích hoạt tài khoản
Route::get('/activate/{token}', [AuthController::class, 'activate'])->name('activate');

Route::get('/login', [AuthController::class, 'showloginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('post-login');




//Forgot password
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');


//Reset it
//reset phải có token mới biết được user nào cần reset
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword'])->name('password.update');


//Custom middleware
Route::middleware(['auth.custom'])->group(function () {
    //middleware check người dùng nếu chưa đăng nhập thì sẽ không đi luồng đăng xuất thành công 
    //vì nó không hợp lý thay vào đó sẽ bắt người dùng đăng nhập mới thực hiện chức năng
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    //update account,.....
    Route::prefix('account')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('account');
        Route::post('/account/update', [AccountController::class, 'update'])->name('account.update');
        Route::post('/change-password', [AccountController::class, 'changePassword'])->name('account.password-change');

        Route::post('/addresses', [AccountController::class, 'addAddress'])->name('account.addresses.add');

        //Xoá địa chỉ, update địa chỉ mặc định
        Route::put('/addresses/{id}', [AccountController::class, 'updatePrimaryAddress'])->name('account.addresses.update');
        Route::delete('/addresses/{id}', [AccountController::class, 'deleteAddress'])->name('account.addresses.delete');
    });

    // Giỏ hàng cart phải đăng nhập
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('cart.index');

        Route::put('/update/{id}', [CartController::class, 'update'])->name('cart.update');
    });

    //Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::get('/checkout/get-address', [CheckoutController::class, 'getAddress'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'placeOrder'])->name('checkout.placeOrder');
    Route::post('/checkout/paypal', [CheckoutController::class, 'placeOrderPayPal'])->name('checkout.placeOrderPayPal');
    Route::post('/checkout/momo', [CheckoutController::class, 'momo_payment'])->name('checkout.momo_payment');
    Route::post('/checkout/vnpay', [CheckoutController::class, 'vnpay_payment'])->name('checkout.vnpay_payment');

    //Xem chi tiết đơn hàng
    Route::get('/order/{id}', [OrderController::class, 'showOrder'])->name('order.show');
    //Huỷ đơn hàng
    Route::post('/order/{id}/cancel', [OrderController::class, 'cancel'])->name('order.cancel');
});

// Routes momo
Route::get('/checkout/momo/return', [CheckoutController::class, 'momoReturn'])->name('checkout.momo.return');
Route::post('/checkout/momo/notify', [CheckoutController::class, 'momoNotify'])->name('checkout.momo.notify');

// Routes VNPay
Route::get('/checkout/vnpay/return', [CheckoutController::class, 'vnpayReturn'])->name('checkout.vnpay.return');

// Giỏ hàng - ko cần đăng nhập (lưu vào session nếu chưa login)
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart/mini', [CartController::class, 'miniCart'])->name('cart.mini');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

//Trang sản phẩm
Route::get('/product', [ProductController::class, 'index'])->name('products.index');

//Route bộ lọc sản phẩm theo giá trị (mặc địh, mới nhất, giá tiền tăn.....)
Route::get('/product/filter', [ProductController::class, 'filter'])->name('products.filter');

//ROute chi tiết sản phẩm
Route::get('/product/{slug}', [ProductController::class, 'detail'])->name('products.detail');

//ROute search sản phẩm
Route::get('/search', [SearchController::class, 'index'])->name('search');

//ROute trang liên hệ
Route::get('/contact', [ContactsController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactsController::class, 'sendContact'])->name('contact');


//REquire admin
require __DIR__ . '/admin.php';
