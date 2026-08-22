<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes — RESTful cho đồ án (test bằng Postman)
|--------------------------------------------------------------------------
| Đọc công khai; thao tác ghi yêu cầu Sanctum token (auth:sanctum).
*/

Route::apiResource('categories', CategoryController::class);

// Sản phẩm: xem công khai, thêm/sửa/xóa cần token Sanctum
Route::apiResource('products', ProductController::class)->only(['index', 'show']);
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('products', ProductController::class)->only(['store', 'update', 'destroy']);
});
