<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Delivery\DeliveryOrderController;

Route::prefix('delivery')->middleware(['auth.custom', 'permission:manage_deliveries'])->name('delivery.')->group(function () {
    Route::get('/dashboard', [DeliveryOrderController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [DeliveryOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [DeliveryOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/start', [DeliveryOrderController::class, 'start'])->name('orders.start');
    Route::post('/orders/{order}/complete', [DeliveryOrderController::class, 'complete'])->name('orders.complete');
});
