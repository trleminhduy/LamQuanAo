<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeliveryController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\RefundController;
use App\Http\Controllers\Admin\SupplierController;
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
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        //Notification
        Route::get('/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');


    });

    Route::middleware(['permission:manage_users'])->group(function () {
        Route::get('/users', [UsersController::class, 'index'])->name('admin.users.index');
        Route::post('/user/upgrade', [UsersController::class, 'upgrade']);
        Route::post('/user/updateStatus', [UsersController::class, 'updateStatus']);
        Route::post('/users/{user}/set-delivery', [UsersController::class, 'setDeliveryRole'])->name('users.setDeliveryRole');
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

    //Nhà cung cấp
    Route::middleware(['permission:manage_suppliers'])->group(function () {
        Route::get('/supplier/add', [SupplierController::class, 'showFormAddSupplier'])->name('admin.supplier.add');
        Route::post('/supplier/add', [SupplierController::class, 'addSupplier'])->name('admin.supplier.add');

        //Quản lý  thêm xoas sửa
        Route::get('/suppliers', [SupplierController::class, 'index'])->name('admin.suppliers.index');
        Route::get('/supplier/edit/{id}', [SupplierController::class, 'edit'])->name('admin.supplier.edit');
        Route::post('/supplier/update', [SupplierController::class, 'updateSupplier'])->name('admin.supplier.update');
        Route::post('/supplier/delete', [SupplierController::class, 'deleteSupplier'])->name('admin.supplier.delete');
    });

    // Quản lý biến thể sản phẩm
    Route::middleware(['permission:manage_variants'])->group(function () {
        Route::get('/variants', [ProductVariantController::class, 'listAll'])->name('admin.variants.all');
        Route::get('/products/{id}/variants', [ProductVariantController::class, 'index'])->name('admin.variants.index');
        Route::post('/products/{id}/variants/add', [ProductVariantController::class, 'addVariant'])->name('admin.variants.add');
        Route::post('/variants/update', [ProductVariantController::class, 'updateVariant']);
        Route::post('/variants/delete', [ProductVariantController::class, 'deleteVariant']);
    });


    // Quản lý đơn hàng
    Route::middleware(['permission:manage_orders'])->group(function () {
        Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders.index');
        Route::post('/orders/confirm', [OrderController::class, 'confirmOrder']);

        Route::get('/orders-detail/{id}', [OrderController::class, 'showOrderDetail'])->name('admin.orders-detail');
        Route::post('/orders-detail/send-invoice', [OrderController::class, 'sendMailInvoice']);
        Route::post('/orders-detail/cancel-order', [OrderController::class, 'cancelOrder']);

        //ghn
        Route::post('/orders/{id}/send-to-ghn', [OrderController::class, 'sendToGHN'])->name('admin.orders.sendToGHN');
    });

    // Quản lý giao hàng
    Route::middleware(['permission:manage_deliveries'])->prefix('deliveries')->group(function () {
        Route::get('/dashboard', [DeliveryController::class, 'dashboard'])->name('admin.deliveries.dashboard');
        Route::get('/my-orders', [DeliveryController::class, 'myOrders'])->name('admin.deliveries.myOrders');
        Route::get('/my-orders/{order}', [DeliveryController::class, 'showOrder'])->name('admin.deliveries.showOrder');
        Route::post('/my-orders/{order}/start', [DeliveryController::class, 'startDelivery'])->name('admin.deliveries.start');
        Route::post('/my-orders/{order}/complete', [DeliveryController::class, 'completeDelivery'])->name('admin.deliveries.complete');
    });

    // Quản lý liên hệ
    Route::middleware(['permission:manage_contacts'])->group(function () {
        Route::get('/contacts', [ContactController::class, 'index'])->name('admin.contacts.index');
        Route::post('/contacts/reply', [ContactController::class, 'replyContact']);
    });

    //Quản lý giao hàng
    Route::prefix('deliveries')->name('admin.deliveries.')->group(function () {
        Route::get('/', [DeliveryController::class, 'index'])->name('index');
        Route::get('/assign/{order}', [DeliveryController::class, 'assignForm'])->name('assignForm');
        Route::post('/assign/{order}', [DeliveryController::class, 'assign'])->name('assign');
    });


    //Quản lý khuyến mãi

    Route::resource('coupons', CouponController::class);


    // Quản lý hoàn trả
    Route::get('/refunds', [RefundController::class, 'index'])->name('admin.refunds.index');
    Route::post('/refunds/approve', [RefundController::class, 'approve'])->name('admin.refunds.approve');
    Route::post('/refunds/reject', [RefundController::class, 'reject'])->name('admin.refunds.reject');
});
