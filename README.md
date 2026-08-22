# 🛋️ Website Bán Đồ Nội Thất Trực Tuyến

Đồ án tích hợp — Xây dựng website bán đồ nội thất trực tuyến (Furniture Store Online).

**Tech stack:** Laravel 11 · MySQL 8.0 · Blade + Bootstrap 5 · Docker Compose · GitHub Actions CI

## ✨ Chức năng chính

| Nhóm | Chức năng |
|---|---|
| Khách hàng | Duyệt sản phẩm, tìm kiếm & lọc (từ khóa, danh mục, chất liệu, khoảng giá), giỏ hàng, đặt hàng COD, lịch sử đơn |
| Tài khoản | Đăng ký, đăng nhập, khóa/mở tài khoản (admin) |
| Quản trị | CRUD danh mục & sản phẩm (upload ảnh + tồn kho), quản lý đơn hàng (xác nhận → giao → hoàn tất / hủy), quản lý người dùng, dashboard thống kê |
| API | RESTful API cho sản phẩm & danh mục (đọc công khai, ghi qua Sanctum token) |

## 🏗️ Kiến trúc 3-layer

```
Route → Controller (Web/Api) → Service (nghiệp vụ) → Repository (truy vấn) → Model (Eloquent) → MySQL
```

- **Đặt hàng** chạy trong DB transaction: kiểm tra tồn kho → tạo Order + OrderItems → trừ kho; thiếu hàng thì rollback toàn bộ.
- Thanh toán là **COD mô phỏng** (`payment_status = cod_pending`), không tích hợp cổng thật.

## 🚀 Cài đặt & chạy

### Cách 1: Máy local (XAMPP)

```bash
# 1. Cấu hình database trong .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=furniture
DB_USERNAME=root
DB_PASSWORD=cscorner        # mật khẩu MySQL của bạn

# 2. Cài dependency và khởi tạo
composer install
cp .env.example .env        # nếu chưa có .env
php artisan key:generate
php artisan migrate --seed  # tạo bảng + dữ liệu demo
php artisan storage:link    # link thư mục ảnh upload
php artisan serve           # -> http://localhost:8000
```

> Lưu ý: cần tạo database `furniture` trước (phpMyAdmin hoặc lệnh SQL `CREATE DATABASE furniture CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`)

### Cách 2: Docker Compose

```bash
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
# Web: http://localhost:8000 — phpMyAdmin: http://localhost:8080
```

## 👤 Tài khoản demo

| Vai trò | Email | Mật khẩu |
|---|---|---|
| Quản trị viên | admin@furniture.test | password |
| Khách hàng | user@furniture.test | password |

Chi tiết xem [resources/accounts.md](resources/accounts.md).

## 🧪 Kiểm thử

```bash
php artisan test            # 8 feature tests: auth, giỏ hàng, đặt hàng, phân quyền
./vendor/bin/pint --test    # kiểm tra style code
```

## 📚 Tài liệu đồ án

Trong thư mục [`docs/`](docs/):
- `Proposal-WebNoiThat.docx` — Proposal đề tài
- `BaoCao-WebNoiThat.docx` — Báo cáo tổng hợp
- `SoDo-UML.md` — Sơ đồ Use-case, Class/ERD, Sequence (Mermaid)

## 🗂️ Cấu trúc chính

```
app/
├── Http/Controllers/{Web,Api}/   # Controller trả Blade / JSON
├── Http/Middleware/              # EnsureUserIsAdmin
├── Models/                       # Eloquent: User, Category, Product, Cart, Order...
├── Repositories/                 # Truy vấn DB
├── Services/                     # Nghiệp vụ (OrderService có transaction)
database/
├── migrations/                   # Schema các bảng
└── seeders/                      # Dữ liệu demo
docs/                             # Proposal, báo cáo, sơ đồ UML
resources/views/                  # Blade views (khách hàng + admin)
routes/                           # web.php, api.php, auth.php
```
