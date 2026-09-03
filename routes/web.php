<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::resource('products', ProductController::class);

Route::patch('products/{product}/activate',   [ProductController::class, 'activate'])->name('products.activate');
Route::patch('products/{product}/deactivate', [ProductController::class, 'deactivate'])->name('products.deactivate');
Route::patch('products/{product}/archive',    [ProductController::class, 'archive'])->name('products.archive');
Route::get('products/{product}/logs',         [ProductController::class, 'logs'])->name('products.logs');

Route::get('/', fn() => redirect()->route('products.index'));
