<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;

Route::get('/test-activate', [ProductController::class, 'index']);
