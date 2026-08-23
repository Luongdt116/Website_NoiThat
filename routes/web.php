<?php

use App\Http\Controllers\Web\AdminDashboardController;
use App\Http\Controllers\Web\AdminOrderController;
use App\Http\Controllers\Web\AdminProductController;
use App\Http\Controllers\Web\AdminUserController;
use App\Http\Controllers\Web\CartController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\PageController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Website bán nội thất
|--------------------------------------------------------------------------
| Khách vãng lai: xem sản phẩm. User: giỏ hàng + đặt hàng.
| Admin: quản trị danh mục/sản phẩm/đơn/user/dashboard (prefix /admin).
*/

// Trang chủ: các block Gợi ý / Bán chạy / Giá rẻ (HomeController)
Route::get('/', [HomeController::class, 'index'])->name('home');

// ===== Sản phẩm (khách + user) =====
Route::get('/san-pham', [ProductController::class, 'index'])->name('products.index');
Route::get('/san-pham/{id}', [ProductController::class, 'show'])->name('products.show');

// ===== Khuyến mãi: sản phẩm đang giảm giá =====
Route::get('/khuyen-mai', [HomeController::class, 'sale'])->name('home.sale');

// ===== Trang tĩnh: chính sách cửa hàng =====
Route::get('/chinh-sach/{slug}', [PageController::class, 'show'])->name('pages.show');

// ===== Giỏ hàng (cần đăng nhập) =====
Route::middleware('auth')->group(function () {
    Route::get('/gio-hang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/gio-hang/them', [CartController::class, 'add'])->name('cart.add');
    Route::post('/gio-hang/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::post('/gio-hang/{id}/chon', [CartController::class, 'select'])->name('cart.select');
    Route::get('/gio-hang/xoa/{id}', [CartController::class, 'remove'])->name('cart.remove');

    // ===== Đặt hàng (COD mô phỏng) =====
    Route::get('/thanh-toan', [OrderController::class, 'checkout'])->name('orders.checkout');
    Route::post('/don-hang', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/don-hang-cua-toi', [OrderController::class, 'history'])->name('orders.history');
    Route::get('/don-hang/{id}', [OrderController::class, 'show'])->name('orders.show');

    // ===== Tài khoản của tôi: cập nhật thông tin + đổi mật khẩu =====
    Route::get('/tai-khoan', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/tai-khoan', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/tai-khoan/mat-khau', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// ===== Khu vực quản trị (/admin): yêu cầu đăng nhập + is_admin =====
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Quản lý danh mục (CRUD đầy đủ)
    Route::resource('categories', CategoryController::class)->names('admin.categories');

    // Quản lý sản phẩm: CRUD + upload ảnh + tồn kho
    Route::resource('products', AdminProductController::class)->names('admin.products');

    // Quản lý đơn hàng: xem, xác nhận, giao, hoàn tất, hủy
    Route::get('orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::get('orders/{id}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
    Route::post('orders/{id}/confirm', [AdminOrderController::class, 'confirm'])->name('admin.orders.confirm');
    Route::post('orders/{id}/ship', [AdminOrderController::class, 'ship'])->name('admin.orders.ship');
    Route::post('orders/{id}/complete', [AdminOrderController::class, 'complete'])->name('admin.orders.complete');
    Route::post('orders/{id}/cancel', [AdminOrderController::class, 'cancel'])->name('admin.orders.cancel');

    // Quản lý người dùng: khóa / mở khóa
    Route::get('users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::post('users/{id}/toggle', [AdminUserController::class, 'toggle'])->name('admin.users.toggle');
});

require __DIR__.'/auth.php';
