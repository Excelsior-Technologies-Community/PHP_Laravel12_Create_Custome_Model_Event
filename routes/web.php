<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductEventNotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('products.index');
});

/*
|--------------------------------------------------------------------------
| Product Dashboard
|--------------------------------------------------------------------------
*/

Route::get(
    '/products-dashboard',
    [ProductController::class, 'dashboard']
)->name('products.dashboard');

/*
|--------------------------------------------------------------------------
| Bulk Product Actions
|--------------------------------------------------------------------------
*/

Route::post(
    '/products/bulk-action',
    [ProductController::class, 'bulkAction']
)->name('products.bulk-action');

/*
|--------------------------------------------------------------------------
| Product CRUD
|--------------------------------------------------------------------------
*/

Route::resource('products', ProductController::class);

/*
|--------------------------------------------------------------------------
| Product Custom Events
|--------------------------------------------------------------------------
*/

Route::patch(
    '/products/{product}/activate',
    [ProductController::class, 'activate']
)->name('products.activate');

Route::patch(
    '/products/{product}/deactivate',
    [ProductController::class, 'deactivate']
)->name('products.deactivate');

Route::patch(
    '/products/{product}/archive',
    [ProductController::class, 'archive']
)->name('products.archive');

Route::get(
    '/products/{product}/logs',
    [ProductController::class, 'logs']
)->name('products.logs');

/*
|--------------------------------------------------------------------------
| Notification Center
|--------------------------------------------------------------------------
*/

Route::get(
    '/notifications',
    [ProductEventNotificationController::class, 'index']
)->name('notifications.index');

Route::get(
    '/notifications/latest',
    [ProductEventNotificationController::class, 'latest']
)->name('notifications.latest');

Route::patch(
    '/notifications/{notification}/read',
    [ProductEventNotificationController::class, 'markAsRead']
)->name('notifications.read');

Route::patch(
    '/notifications/mark-all-read',
    [ProductEventNotificationController::class, 'markAllAsRead']
)->name('notifications.mark-all-read');

Route::delete(
    '/notifications/{notification}',
    [ProductEventNotificationController::class, 'destroy']
)->name('notifications.destroy');

Route::delete(
    '/notifications-clear',
    [ProductEventNotificationController::class, 'clearAll']
)->name('notifications.clear-all');