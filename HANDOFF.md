# 📋 HANDOFF — Ghi chú chuyển tiếp giữa các phiên làm việc

> File này dành cho **phiên Claude Code tiếp theo** (hoặc thành viên nhóm) đọc để nắm trạng thái dự án,
> các lỗi còn tồn đọng và việc cần làm. Cập nhật lần cuối: **2026-08-23** (phiên 3: polish + điền thông tin nhóm vào Proposal/Báo cáo ✅).

---

## 1. Bối cảnh dự án (đọc nhanh)

| Mục | Giá trị |
|---|---|
| Đề tài | Website bán đồ nội thất trực tuyến (đồ án tích hợp, nhóm 3 người) |
| Thư mục | `D:\WORK\NAM4\3_CDTH\Website_NoiThat` |
| Nhánh làm việc | `Duc_Luong` (đầy đủ code, CI xanh). `main` / `Nhu_Kien` / `Phan_Kiet` trên GitHub **vẫn dừng ở Initial commit** — chưa có code |
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
- [x] README.md hướng dẫn chạy XAMPP/Docker; commit đã push lên `origin/Duc_Luong`
- [x] **(phiên 2) CI trên GitHub ĐÃ XANH** — run `32622139909` cho commit `8a9e5c2`, đủ 13/13 step success. Các fix đã commit:
  - `770b591`: ci.yml trỏ DB về service mysql (sed đè HOST/USER/PASS), thêm `composer config policy.advisories.block false`, chạy pint auto-fix toàn repo (~42 file style).
  - `4ac6db9`: phpunit.xml chuyển test sang **SQLite :memory:** (force=true) + `DB_FOREIGN_KEYS` — cô lập môi trường, không phụ thuộc MySQL local/CI. Test nhanh hơn hẳn (~0.6s).
  - `f61f58f`: sửa cú pháp setup-php extensions (chuỗi CSV `pdo_mysql, sqlite3, pdo_sqlite`).
  - `8a9e5c2`: **thêm `APP_KEY=` placeholder vào .env.example** — nguyên nhân thật của CI fail trước đó: thiếu dòng này khiến `php artisan key:generate` báo "Unable to set application key" và mọi test chết với MissingAppKeyException.
- [x] **(phiên 2)** Nâng cấp view `products/show.blade.php` (ảnh lớn, giá nổi bật, badge tồn kho thấp, nút thêm giỏ theo tông wood) — commit `75ecd8e`
- [x] **(phiên 2)** Nâng cấp view `admin/users/index.blade.php` (card + badge trạng thái + icon toggle) — commit `75ecd8e`
- [x] **(phiên 2)** README.md đã thay mật khẩu DB thật bằng placeholder — commit `75ecd8e`
- [x] **(phiên 3)** Polish tổng thể (commit `43956e1`): fix nghiệp vụ giỏ hàng (validate số lượng theo tồn kho + check quyền sở hữu cart), OrderService chặn giỏ rỗng + hoàn kho khi hủy đơn (có guard chống hoàn 2 lần), viết lại views admin (orders/show, users/index, categories), xóa welcome.blade.php, test tăng lên **11 tests / 23 assertions xanh**, CI vẫn xanh
- [x] **(phiên 3)** Điền thông tin nhóm + phân công vào Proposal/Báo cáo `.docx` từ file gốc "Website ban do noi that.docx" — commit `1df173e`, `c8c73f2`. Chi tiết:
  - Proposal: 3 thành viên + vai trò (Lương – Nhóm trưởng·Backend, Kiên – Backend, Kiệt – Frontend), GVHD Phạm Hữu Tùng
  - Điền đủ **26/26 dòng "Phân công công việc"** trong 5 bảng kế hoạch 10 tuần; sửa chữ sót template khách sạn ("Room, Booking" → "Products, Orders, Cart")
  - Báo cáo: thêm khối thông tin nhóm sau tiêu đề
  - Tái tạo được qua `python scripts/fill_proposal.py` (dict `ASSIGNMENTS` trong script là nguồn chuẩn)

## 3. ⚠️ Lỗi/rủi ro còn tồn đọng

1. ~~CI đỏ~~ ✅ ĐÃ SỬA XONG (xem mục 2). Nếu CI lại đỏ trong tương lai: lấy log bằng git credential (`git credential-manager get` với protocol/host github.com) rồi gọi API `/actions/jobs/{job_id}/logs` — annotations công khai chỉ nói "exit code 2" không đủ.
2. **Composer global trên máy này vẫn tắt advisory blocking** — ai clone về máy khác chạy `composer install` lần đầu sẽ gặp lỗi resolve; CI đã tự tắt advisory nên không ảnh hưởng GitHub Actions.
3. **Server `php artisan serve` (port 8000)** cần chạy lại thủ công nếu máy vừa mở.
4. ~~Tài liệu .docx sinh từ template chung — chưa điền thông tin cá nhân~~ ✅ ĐÃ XONG (phiên 3): Proposal + Báo cáo đã điền 3 thành viên + GVHD + phân công 10 tuần qua `scripts/fill_proposal.py`.

## 4. Việc tiếp theo (theo thứ tự ưu tiên)

### 🔴 Ưu tiên 1 — Đưa code sang nhánh `Nhu_Kien` và `Phan_Kiet` (yêu cầu của Lương, chưa làm)

User muốn 2 thành viên kia có code để tự làm commit trên nhánh của mình. Trạng thái đã kiểm tra:
`origin/Nhu_Kien` và `origin/Phan_Kiet` **đã tồn tại trên GitHub nhưng dừng ở Initial commit** (`a5c8668`, giống `main`).
Cần hỏi lại user trước khi đẩy vì có 2 cách làm, user chưa chốt:

- **Cách A (đề xuất)**: đẩy full code `Duc_Luong` lên cả 2 nhánh (`git push origin Duc_Luong:Nhu_Kien` + `Duc_Luong:Phan_Kiet`). Sau đó Kiên/Kiệt checkout nhánh mình, cấu hình git account cá nhân và tự commit phần việc (Kiên: Auth/API/Seeder/test backend; Kiệt: views/UI/Blade). Lịch sử commit trung thực, merge về sau dễ.
- **Cách B**: chia sẵn commit theo vai trò trên từng nhánh, gắn tên tác giả tương ứng — cần email/username GitHub của Kiên và Kiệt.

LƯU Ý: đẩy code sang nhánh người khác = thay đổi repo chung; nếu user nói "làm đi" thì cứ đẩy thẳng (fast-forward từ Initial commit nên an toàn, không ghi đè gì).

### 🟡 Ưu tiên 2 — PR `Duc_Luong` → `main` (khi nhóm thống nhất)

CI đang xanh (11 tests pass) nên PR sẽ pass checks. Có thể tạo bằng:
`gh pr create --base main --head Duc_Luong --title "Đồ án Website bán đồ nội thất" --body "..."`

### 🟢 Ưu tiên 3 — Video demo + chuẩn bị nộp

- [ ] Quay video demo các luồng chính (yêu cầu đồ án): duyệt/lọc → đăng nhập → giỏ → đặt hàng → admin duyệt đơn. Tài khoản demo: admin@furniture.test / user@furniture.test (password) — xem `resources/accounts.md`
- [ ] Checklist nộp: Proposal `.docx` ✅ · Báo cáo `.docx` ✅ · sơ đồ UML ✅ · video ⏳ · deploy (nếu yêu cầu) ⏳

### 🟣 Ưu tiên 4 — Cải tiến ghi nhận từ Lương ✅ ĐÃ XONG HẾT (phiên 3, commit `37713ea`)

- [x] **4a. Footer bám đáy**: body `d-flex flex-column min-vh-100` + main `flex-grow-1` + footer `mt-auto` — trang ngắn (Giỏ hàng/Đơn của tôi) footer không còn lơ lửng
- [x] **4b. Xung đột tồn kho**: SELECT sản phẩm trong transaction có `lockForUpdate()` (chặn âm kho khi 2 đơn song song); exception riêng `OutOfStockException`; controller tự dọn giỏ khi hết hàng (hết hẳn → xóa dòng, còn ít → giảm số lượng về mức kho); view giỏ có badge đỏ vượt kho + disable nút đặt; +2 test cạnh tranh kho
- [x] **4c. Trang Tài khoản** `/tai-khoan`: ProfileController (đổi tên/email unique trừ chính mình, đổi mật khẩu cần mật khẩu hiện tại), view 2 card tông wood, link trong dropdown navbar, +5 test ProfileTest
- Tests sau cả 3 mục: **18 passed / 45 assertions**, pint passed
- Chưa làm (tùy chọn, khỏi scope nếu nặng): prefill địa chỉ/SĐT mặc định từ profile vào form đặt hàng (cần migration thêm cột users)


### Lệnh hay dùng

```bash
cd /d/WORK/NAM4/3_CDTH/Website_NoiThat
php artisan serve                      # chạy web :8000
php artisan test                       # 11 tests / 23 assertions (SQLite :memory:)
php artisan migrate:fresh --seed      # reset dữ liệu demo
./vendor/bin/pint                     # auto-fix style code
# Sinh lại tài liệu:
python scripts/fill_proposal.py        # điền lại Proposal/Báo cáo từ file gốc (sửa ASSIGNMENTS trong script nếu đổi phân công)
python ~/.claude/skills/furniture-web/scripts/gen_proposal_docx.py --proposal --out docs/Proposal-WebNoiThat.docx
python ~/.claude/skills/furniture-web/scripts/gen_diagrams.py --type all --out docs/SoDo-UML.md
# Đẩy code sang nhánh thành viên (Ưu tiên 1, nếu user chốt cách A):
git push origin Duc_Luong:Nhu_Kien
git push origin Duc_Luong:Phan_Kiet
```

## 5. Cách tiếp tục ở phiên mới

Mở Claude Code tại thư mục dự án và nói: **"Đọc HANDOFF.md rồi xử lý các việc còn tồn đọng theo thứ tự."**
Phiên mới nên bắt đầu từ **mục 4 — Ưu tiên 1** (đưa code sang nhánh `Nhu_Kien` + `Phan_Kiet`, hỏi user chọn cách A/B trước khi đẩy). Kiểm tra tab GitHub Actions trước khi sửa gì khác.
