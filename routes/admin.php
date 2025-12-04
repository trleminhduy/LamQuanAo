<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UsersController;
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

    Route::middleware(['permission:manage_users'])->group(function () {
        Route::get('/users', [UsersController::class, 'index'])->name('admin.users.index');
        Route::post('/user/upgrade', [UsersController::class, 'upgrade']);
        Route::post('/user/updateStatus', [UsersController::class, 'updateStatus']);
    });

    Route::middleware(['permission:manage_categories'])->group(function () {
        Route::get('/categories/add', [CategoryController::class, 'showFormAddCategory'])->name('admin.categories.add');
        Route::post('/categories/add', [CategoryController::class, 'addCategory'])->name('admin.categories.add');

        //Quản lý danh mục thêm xoas sửa
        Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
        Route::post('/categories/update', [CategoryController::class, 'updateCategory']);
        Route::post('/categories/delete', [CategoryController::class, 'deleteCategory']);
    });

    // Sản phẩm
    Route::middleware(['permission:manage_products'])->group(function () {
        Route::get('/product/add', [ProductController::class, 'showFormAddProduct'])->name('admin.product.add');
        Route::post('/product/add', [ProductController::class, 'addProduct'])->name('admin.product.add');

        //Quản lý danh mục thêm xoas sửa
        Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
        Route::post('/product/update', [ProductController::class, 'updateProduct']);
        Route::post('/product/delete', [ProductController::class, 'deleteProduct']);
    });
});
