<?php

use App\Http\Controllers\Web\AuthController;
use Illuminate\Support\Facades\Route;

// ===== Đăng ký / Đăng nhập / Đăng xuất (Breeze-style, tự viết theo Bootstrap) =====
Route::middleware('guest')->group(function () {
    Route::get('dang-nhap', [AuthController::class, 'showLogin'])->name('login');
    Route::post('dang-nhap', [AuthController::class, 'login']);
    Route::get('dang-ky', [AuthController::class, 'showRegister'])->name('register');
    Route::post('dang-ky', [AuthController::class, 'register']);
});

Route::post('dang-xuat', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
