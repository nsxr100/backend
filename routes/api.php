<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\MenuVariantController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

// Category routes
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::get('{category}', [CategoryController::class, 'show']);
    Route::put('{category}', [CategoryController::class, 'update']);
    Route::delete('{category}', [CategoryController::class, 'destroy']);
});

// Menu Item routes
Route::prefix('menu-items')->group(function () {
    Route::get('/', [MenuItemController::class, 'index']);
    Route::post('/', [MenuItemController::class, 'store']);
    Route::get('search', [MenuItemController::class, 'search']);
    Route::get('category/{categoryId}', [MenuItemController::class, 'byCategory']);
    Route::get('{menuItem}', [MenuItemController::class, 'show']);
    Route::put('{menuItem}', [MenuItemController::class, 'update']);
    Route::delete('{menuItem}', [MenuItemController::class, 'destroy']);
});

// Menu Variant routes
Route::prefix('menu-items/{menuItemId}/variants')->group(function () {
    Route::get('/', [MenuVariantController::class, 'index']);
    Route::post('/', [MenuVariantController::class, 'store']);
    Route::get('{menuVariant}', [MenuVariantController::class, 'show']);
    Route::put('{menuVariant}', [MenuVariantController::class, 'update']);
    Route::delete('{menuVariant}', [MenuVariantController::class, 'destroy']);
});

// Order routes
Route::prefix('orders')->group(function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::post('/', [OrderController::class, 'store']);
    Route::get('{order}', [OrderController::class, 'show']);
    Route::post('{order}/pay', [OrderController::class, 'pay']);
    Route::patch('{order}/status', [OrderController::class, 'updateStatus']);
});
