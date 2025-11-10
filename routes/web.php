<?php

use App\Http\Controllers\Clients\AccountController;
use App\Http\Controllers\Clients\AuthController;
use App\Http\Controllers\Clients\ForgotPasswordController;
use App\Http\Controllers\Clients\HomeController;
use App\Http\Controllers\Clients\ProductController;
use App\Http\Controllers\Clients\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::get('/',[HomeController::class,'index'])->name('home');

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
        
    });

//Trang chi tiết sản phẩm
Route::get('/product',[ProductController::class,'index'])->name('products.index');