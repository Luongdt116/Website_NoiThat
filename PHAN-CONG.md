# 📌 PHÂN CÔNG — Kế hoạch commit thật của từng thành viên

> **Mục đích:** lịch sử commit phản ánh đúng người làm phần đó, và mỗi thành viên **hiểu rõ phần mình** để trả lời câu hỏi khi bảo vệ đồ án.
> **Nguyên tắc:** commit bằng tài khoản GitHub **của chính mình**, commit nhỏ + đều tay (mỗi ngày làm việc đều có push), **không dồn sát hạn**.

## 0. Bối cảnh & bản đối chiếu

- Nhánh `Duc_Luong` hiện là **bản hoàn chỉnh chạy tốt** (CI xanh, 20 tests pass) — dùng làm **BẢN ĐỐI CHIẾU**. Không merge nó vào `main` vội, không đẩy nó sang nhánh của ai cả.
- `Nhu_Kien`, `Phan_Kiet` đang trống (Initial commit `a5c8668`) — mỗi bạn **xây phần việc của mình trên nhánh đó**, commit thật từng bước.
- Cách làm việc khuyến nghị: mở bản đối chiếu (`Duc_Luong`) bên cạnh để xem cách giải quyết từng phần, nhưng **tự gõ lại và tùy biến** code của mình (đặt tên biến/comment theo cách mình hiểu). Vừa học vừa có sản phẩm, và khi bị hỏi "tại sao làm vậy" sẽ trả lời được.

## 1. Thứ tự tiếp sức giữa 3 nhánh (tránh xung đột merge)

Backend phải có trước vì giao diện cần route/controller để render; logic nghiệp vụ cần models của backend. Vì vậy làm **tuần tự 4 giai đoạn**:

```
GĐ1: Kiên dựng lõi backend trên Nhu_Kien
      ↓ (Lương merge Nhu_Kien vào Duc_Luong)
GĐ2: Lương thêm logic nghiệp vụ (giỏ/đơn/admin) trên Duc_Luong
      ↓ (Kiệt checkout Phan_Kiet TỪ Duc_Luong lúc này)
GĐ3: Kiệt dựng giao diện trên Phan_Kiet
      ↓ (Lương merge Phan_Kiet vào Duc_Luong)
GĐ4: Lương tổng hợp: test + CI + tài liệu → PR Duc_Luong → main
```

> ⚠️ Kiệt **không checkout Phan_Kiet ngay từ đầu** — phải đợi đến GĐ3 (sau khi Lương merge GĐ2) rồi mới chuyển sang làm, để nhánh có sẵn backend + logic mà giao diện cần.

## 2. Chi tiết việc từng người

### 🧩 Giai đoạn 1 — Nguyễn Như Kiên · Backend lõi (nhánh `Nhu_Kien`, ~12–18 commit)

| # | Việc | File/thư mục | Gợi ý chia commit |
|---|---|---|---|
| 1 | Khởi tạo project Laravel + `.env.example` + `docker-compose.yml` | gốc repo | 1–2 commit |
| 2 | Migrations: users (+roles), categories, products, orders, order_items, carts (+selected) | `database/migrations/` | mỗi bảng 1 commit |
| 3 | Models + quan hệ Eloquent | `app/Models/` | 1–2 commit |
| 4 | Seeders: 2 user demo, 5 danh mục, 12 sản phẩm | `database/seeders/`, `resources/accounts.md` | 1–2 commit |
| 5 | Auth: đăng ký/đăng nhập/đăng xuất + chặn user bị khóa | `AuthController.php`, `routes/auth.php` | 2–3 commit |
| 6 | Middleware `admin` (403 cho non-admin) | bootstrap/app.php | 1 commit |
| 7 | Repository + Service khung | `app/Repositories/`, `app/Services/` | 1–2 commit |
| 8 | API `/api/v1` products + categories | `routes/api.php`, Api controllers | 1–2 commit |

### 🧭 Giai đoạn 2 — Trần Đức Lương · Logic nghiệp vụ (nhánh `Duc_Luong`, sau khi merge GĐ1)

| # | Việc | File |
|---|---|---|
| 1 | Giỏ hàng: thêm/sửa/xóa/tick chọn + validate tồn kho + quyền sở hữu | `CartController`, `CartService`, `CartRepository` |
| 2 | Đặt hàng COD: transaction + `lockForUpdate` chống âm kho | `OrderService::createFromCart`, `OutOfStockException` |
| 3 | Hủy đơn hoàn kho (guard chống hoàn 2 lần) | `OrderService::updateStatus` |
| 4 | Vòng đời đơn admin: confirm → ship → complete → cancel | `AdminOrderController` |
| 5 | Quản lý user: khóa/mở khóa | `AdminUserController` |
| 6 | Feature tests luồng đặt hàng | `tests/Feature/OrderFlowTest.php` |
| 7 | CI GitHub Actions + phpunit SQLite :memory: + pint | `.github/workflows/ci.yml`, `phpunit.xml` |

### 🎨 Giai đoạn 3 — Phan Thế Kiệt · Giao diện (nhánh `Phan_Kiet`, sau khi Lương merge GĐ2)

| # | Việc | File |
|---|---|---|
| 1 | Layout chung: navbar, footer bám đáy, tông gỗ (CSS vars --wood/--cream) | `layouts/app.blade.php` |
| 2 | Trang chủ + danh sách SP + card sản phẩm + tìm kiếm/lọc | `products/index`, `product-card`, `ProductController@index` view |
| 3 | Chi tiết sản phẩm (ảnh lớn, giá nổi bật, badge tồn kho thấp) | `products/show` |
| 4 | Giỏ hàng: checkbox chọn món + "chọn tất cả" + badge vượt kho | `cart/index` |
| 5 | Thanh toán + lịch sử đơn + chi tiết đơn | `orders/checkout`, `orders/history`, `orders/show` |
| 6 | Trang tài khoản (2 card thông tin + đổi mật khẩu) | `profile/show` |
| 7 | Views admin: dashboard stat-card, categories, products, orders, users | `resources/views/admin/**` |

### 🧭 Giai đoạn 4 — Trần Đức Lương · Tổng hợp

Merge `Phan_Kiet` vào `Duc_Luong` → xử lý conflict (nếu có) → chạy lại toàn bộ tests + CI xanh → cập nhật README/HANDOFF/docs → quay video demo → tạo PR `Duc_Luong` → `main`.

## 3. Quy ước commit chung

- **Message tiếng Việt, ngắn, nói rõ việc:** `Tạo migration bảng products`, `Thêm form đăng nhập`, `Sửa footer bám đáy trang giỏ`…
- **1 commit = 1 việc** — đừng commit 30 file một lần với message "update".
- **Không commit `.env`** (có mật khẩu thật) — chỉ `.env.example`. Kiểm tra `git status` trước khi `git add .`.
- **Push ít nhất mỗi ngày có làm** — lịch sử trải đều mới đúng thực chất công việc.
- Gặp lỗi/lạ: hỏi trong nhóm Zalo, đừng tự force-push lên nhánh chung.

## 4. Cheat sheet Git (ai chưa quen)

```bash
# Lần đầu tiên (mỗi máy chỉ làm 1 lần)
git clone https://github.com/Luongdt116/Website_NoiThat.git
cd Website_NoiThat
git config user.name "Tên đầy đủ của bạn"
git config user.email "email-cua-ban@github.com"   # email trùng tài khoản GitHub của bạn

# Mỗi ngày làm việc
git checkout Nhu_Kien            # hoặc Phan_Kiet — nhánh của mình
git pull origin Nhu_Kien         # lấy mới nhất trước khi làm
# ... code, tạo/sửa file ...
git status                       # SOI TRƯỚC KHI ADD: không có .env hay file lạ
git add <file>                   # hoặc git add . nếu chắc chắn
git commit -m "Mô tả ngắn việc vừa làm"
git push origin Nhu_Kien         # đẩy lên nhánh của mình
```

## 5. Tiến độ

- [ ] **GĐ1 — Kiên:** lõi backend trên `Nhu_Kien`
- [ ] **GĐ2 — Lương:** merge `Nhu_Kien` + logic nghiệp vụ
- [ ] **GĐ3 — Kiệt:** giao diện trên `Phan_Kiet`
- [ ] **GĐ4 — Lương:** merge tổng + test/CI + PR `main`
- [ ] Video demo (người quay: Lương)
- [ ] Chốt slide thuyết trình (ai phụ trách: Lương tổng hợp)
