# 📋 HANDOFF — Ghi chú chuyển tiếp giữa các phiên làm việc

> File này dành cho **phiên Claude Code tiếp theo** (hoặc thành viên nhóm) đọc để nắm trạng thái dự án,
> các lỗi còn tồn đọng và việc cần làm. Cập nhật lần cuối: **2026-08-23**.

---

## 1. Bối cảnh dự án (đọc nhanh)

| Mục | Giá trị |
|---|---|
| Đề tài | Website bán đồ nội thất trực tuyến (đồ án tích hợp, nhóm 3 người) |
| Thư mục | `D:\WORK\NAM4\3_CDTH\Website_NoiThat` |
| Nhánh làm việc | `Duc_Luong` (còn có `Nhu_Kien`, `Phan_Kiet`, `main` của nhóm) |
| Repo | https://github.com/Luongdt116/Website_NoiThat |
| Tech stack | Laravel 11 (^11.31) · MySQL 8.0 · Blade + Bootstrap 5 (CDN, không cần npm) · Docker Compose · GitHub Actions |
| Kiến trúc | Route → Controller (Web/Api) → Service → Repository → Model Eloquent → MySQL |
| DB local | MySQL XAMPP (`D:\Download\APPcode\XAMPP_MT`), host `127.0.0.1`, user `root` / mật khẩu `cscorner`, database `furniture` |
| Tài khoản demo | admin@furniture.test / password — user@furniture.test / password (xem `resources/accounts.md`) |
| Chạy web | `php artisan serve` → http://localhost:8000 |

Quy ước sinh code/tài liệu nằm trong skill `furniture-web` (`C:\Users\tranl\.claude\skills\furniture-web\`).
Người dùng đã đồng ý: làm liên tục, không cần hỏi từng bước; mọi thứ commit + push thẳng lên nhánh `Duc_Luong`.

## 2. Đã hoàn thành — ĐỪNG LÀM LẠI

- [x] Khung Laravel 11 + vendor đầy đủ; docker-compose.yml (app/db/phpmyadmin) + Dockerfile + `.github/workflows/ci.yml`
- [x] Auth tự viết (`AuthController`): đăng ký/đăng nhập/đăng xuất, chặn user bị khóa; middleware alias `admin` (403)
- [x] CRUD danh mục + CRUD sản phẩm (upload ảnh storage/app/public/products, badge tồn kho thấp)
- [x] Giỏ hàng lưu DB (`carts`), đặt hàng COD qua `OrderService::createFromCart()` trong DB transaction (kiểm kho → tạo đơn + items → trừ kho, thiếu hàng rollback)
- [x] Admin: dashboard 4 stat-card, quản lý đơn (confirm/ship/complete/cancel — form POST đúng chuẩn), khóa/mở user
- [x] API RESTful: `/api/v1`… xem `routes/api.php` (products/categories Resource, ghi qua Sanctum)
- [x] Layout tông nâu gỗ `layouts.app`, component `product-card`, tìm kiếm/lọc 4 tiêu chí
- [x] Seeders: 2 users demo + 5 danh mục + 12 sản phẩm mẫu; `storage:link` đã tạo
- [x] Smoke test curl toàn luồng PASSED (login → giỏ → đặt → kho giảm đúng 10→9/25→23 → tổng 8.100.000₫ → admin duyệt đủ vòng đời → non-admin 403)
- [x] `php artisan test`: **8/8 xanh** (`tests/Feature/ExampleTest.php` + `tests/Feature/OrderFlowTest.php`)
- [x] Tài liệu `docs/`: Proposal + Báo cáo `.docx`, `SoDo-UML.md` (Use-case/Class/Sequence Mermaid)
- [x] README.md hướng dẫn chạy XAMPP/Docker; 6 commit đã push lên `origin/Duc_Luong` (mới nhất: `0fe6062`)

## 3. ⚠️ Lỗi/rủi ro còn tồn đọng (ƯU TIÊN XỬ LÝ)

1. **CI trên GitHub nhiều khả năng ĐỎ** — 3 nguyên nhân cộng dồn trong `.github/workflows/ci.yml` + `.env.example`:
   - `.env.example` có `DB_HOST=db`, `DB_USERNAME=furniture`, `DB_PASSWORD=secret` nhưng service CI khai báo nhãn `mysql`, user `root/root` → bước `php artisan migrate --force` chắc chắn fail.
   - Composer 2.10 chặn cài `laravel/framework` 11.x vì security advisories (Laravel 11 hết bảo trì bảo mật 3/2026) — máy này đã tắt bằng `composer config -g policy.advisories.block false` (chỉ hiệu lực máy local!) → cần thêm bước trong CI: `composer config policy.advisories.block false` trước `composer install`.
   - `pint --test` chưa từng chạy local → style code chưa chắc đạt, có thể fail bước cuối của CI.
   - **Gợi ý sửa:** sau `cp .env.example .env` trong ci.yml, append đè biến DB (`DB_HOST=127.0.0.1`, `DB_DATABASE=furniture`, `DB_USERNAME=root`, `DB_PASSWORD=root`); thêm dòng config advisory; chạy `./vendor/bin/pint` local fix style rồi commit trước khi kiểm tra tab Actions.
2. **View còn thô:** `resources/views/products/show.blade.php` vẫn là bản scaffold ban đầu (chưa nâng cấp theo tông card/giỏ hàng); `resources/views/admin/users/index.blade.php` bảng đơn giản (đã vá đúng POST toggle nhưng giao diện cơ bản).
3. **README.md chứa mật khẩu DB local thật (`cscorner`)** — nếu repo công khai thì nên thay bằng placeholder.
4. **Composer global trên máy này đã tắt advisory blocking** — ai clone về máy khác chạy `composer install` lần đầu sẽ gặp lỗi resolve giống lúc đầu; cân nhắc commit config vào project-level thay vì global.
5. **Server `php artisan serve` (port 8000)** đang chạy nền từ phiên cũ — máy tắt/mở lại thì tự mất, chạy lại lệnh là được.
6. **Tài liệu .docx sinh từ template chung** — chưa điền thông tin cá nhân (tên SV, MSSV, lớp, GVHD) nếu đề bài yêu cầu.

## 4. Việc tiếp theo (theo thứ tự ưu tiên)

- [ ] Sửa `ci.yml` + `.env.example` (mục 3.1) → chạy `./vendor/bin/pint` → commit + push → vào tab Actions của GitHub xác nhận CI xanh
- [ ] Nâng cấp view `products/show.blade.php` (ảnh lớn, giá nổi bật, nút thêm giỏ, thông tin chất liệu/tồn kho theo tông `var(--wood)` của layout)
- [ ] Tuỳ chọn: đẹp hơn view `admin/users/index.blade.php`
- [ ] Khi nhóm thống nhất: tạo PR `Duc_Luong` → `main` (hiện `main` vẫn chỉ có Initial commit)
- [ ] Điền thông tin cá nhân/nhóm vào Proposal + Báo cáo `.docx` trong `docs/`
- [ ] Quay video demo các luồng chính (yêu cầu đồ án): duyệt/lọc → đăng nhập → giỏ → đặt hàng → admin duyệt đơn

### Lệnh hay dùng

```bash
cd /d/WORK/NAM4/3_CDTH/Website_NoiThat
php artisan serve                      # chạy web :8000
php artisan test                       # 8 feature tests
php artisan migrate:fresh --seed      # reset dữ liệu demo
./vendor/bin/pint                     # auto-fix style code
# Sinh lại tài liệu (skill furniture-web):
python ~/.claude/skills/furniture-web/scripts/gen_proposal_docx.py --proposal --out docs/Proposal-WebNoiThat.docx
python ~/.claude/skills/furniture-web/scripts/gen_diagrams.py --type all --out docs/SoDo-UML.md
```

## 5. Cách tiếp tục ở phiên mới

Mở Claude Code tại thư mục dự án và nói: **"Đọc HANDOFF.md rồi xử lý các việc còn tồn đọng theo thứ tự."**
Phiên mới nên bắt đầu từ mục 3 (lỗi tồn đọng) — đặc biệt kiểm tra tab GitHub Actions trước khi sửa gì khác.
