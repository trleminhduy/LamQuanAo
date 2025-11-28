<?php

use App\Http\Controllers\Admin\AdminAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {

    //Custom middleware kiểm tra đăng nhập
    Route::middleware(['check.auth.admin'])->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
    });



    //đĂNG XUẤT
    Route::get('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');


    Route::middleware(['auth.custom'])->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.pages.dashboard');
        })->name('admin.dashboard');
    });
});
