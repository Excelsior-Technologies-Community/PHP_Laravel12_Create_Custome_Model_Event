<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductEventNotificationController;


/*
|--------------------------------------------------------------------------
| Product Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('products.index');
});


Route::get('/products-dashboard', [
    ProductController::class,
    'dashboard'
])->name('products.dashboard');


Route::get('/products-export/csv', [
    ProductController::class,
    'export'
])->name('products.export');


Route::post('/products/bulk-action', [
    ProductController::class,
    'bulkAction'
])->name('products.bulk-action');


Route::resource('products', ProductController::class);


Route::post('/products/{product}/activate', [
    ProductController::class,
    'activate'
])->name('products.activate');


Route::post('/products/{product}/deactivate', [
    ProductController::class,
    'deactivate'
])->name('products.deactivate');


Route::post('/products/{product}/archive', [
    ProductController::class,
    'archive'
])->name('products.archive');


Route::get('/products/{product}/logs', [
    ProductController::class,
    'logs'
])->name('products.logs');


/*
|--------------------------------------------------------------------------
| Notification Routes
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Notification List
|--------------------------------------------------------------------------
*/

Route::get('/notifications', [
    ProductEventNotificationController::class,
    'index'
])->name('notifications.index');


/*
|--------------------------------------------------------------------------
| Latest Notifications
|--------------------------------------------------------------------------
*/

Route::get('/notifications/latest', [
    ProductEventNotificationController::class,
    'latest'
])->name('notifications.latest');


/*
|--------------------------------------------------------------------------
| Mark All Notifications As Read
|--------------------------------------------------------------------------
| IMPORTANT:
| Keep this before /notifications/{notification}
|--------------------------------------------------------------------------
*/

Route::post('/notifications/mark-all-read', [
    ProductEventNotificationController::class,
    'markAllAsRead'
])->name('notifications.mark-all-read');


/*
|--------------------------------------------------------------------------
| Clear All Notifications
|--------------------------------------------------------------------------
| IMPORTANT:
| Keep this before /notifications/{notification}
|--------------------------------------------------------------------------
*/

Route::delete('/notifications-clear', [
    ProductEventNotificationController::class,
    'clear'
])->name('notifications.clear');


/*
|--------------------------------------------------------------------------
| Mark Individual Notification As Read
|--------------------------------------------------------------------------
*/

Route::post('/notifications/{notification}/read', [
    ProductEventNotificationController::class,
    'markAsRead'
])->name('notifications.read');


/*
|--------------------------------------------------------------------------
| Delete Individual Notification
|--------------------------------------------------------------------------
*/

Route::delete('/notifications/{notification}', [
    ProductEventNotificationController::class,
    'destroy'
])->name('notifications.destroy');