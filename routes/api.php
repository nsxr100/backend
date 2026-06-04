<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\MenuVariantController;
use App\Http\Controllers\OrderController;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;

Route::get('menu-version', function () {
    return response()->json([
        'version' => implode('|', [
            Category::count(),
            Category::max('updated_at'),
            MenuItem::count(),
            MenuItem::max('updated_at'),
        ]),
    ])->header('Cache-Control', 'no-store, no-cache, must-revalidate');
});

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

Route::get('menu-image/{path}', function (string $path) {
    $menuItem = MenuItem::where('image_url', $path)->first();

    if ($menuItem?->image_data_url && str_contains($menuItem->image_data_url, ',')) {
        [$meta, $data] = explode(',', $menuItem->image_data_url, 2);
        preg_match('/data:(.*);base64/', $meta, $match);

        return response(base64_decode($data), 200)
            ->header('Content-Type', $match[1] ?? 'image/jpeg')
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Cache-Control', 'public, max-age=31536000');
    }

    if (Storage::disk('public')->exists($path)) {
        return Storage::disk('public')->response($path)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Cache-Control', 'public, max-age=31536000');
    }

    abort(404);
})->where('path', '.*');

Route::get('menu-image-data/{menuItem}', function (MenuItem $menuItem) {
    abort_unless($menuItem->image_data_url && str_contains($menuItem->image_data_url, ','), 404);

    [$meta, $data] = explode(',', $menuItem->image_data_url, 2);
    preg_match('/data:(.*);base64/', $meta, $match);

    return response(base64_decode($data), 200)
        ->header('Content-Type', $match[1] ?? 'image/jpeg')
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Cache-Control', 'public, max-age=31536000');
});
