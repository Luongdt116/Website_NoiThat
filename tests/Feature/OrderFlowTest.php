<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Test luồng nghiệp vụ cốt lõi: giỏ hàng (tick chọn món) → đặt hàng → tồn kho giảm → admin duyệt
class OrderFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private User $admin;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        // Dữ liệu dùng chung cho các test
        $this->user = User::create([
            'name' => 'Khách test', 'email' => 'khach@test.vn',
            'password' => bcrypt('password'), 'is_admin' => false, 'is_active' => true,
        ]);
        $this->admin = User::create([
            'name' => 'Admin test', 'email' => 'admin@test.vn',
            'password' => bcrypt('password'), 'is_admin' => true, 'is_active' => true,
        ]);
        $cat = Category::create(['name' => 'Ghế', 'slug' => 'ghe']);
        $this->product = Product::create([
            'name' => 'Ghế test', 'price' => 500000, 'stock' => 10, 'category_id' => $cat->id,
        ]);
    }

    /**
     * Đăng nhập được với tài khoản demo.
     */
    public function test_user_can_login(): void
    {
        $response = $this->post('/dang-nhap', [
            'email' => 'khach@test.vn',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($this->user);
    }

    /**
     * Tài khoản bị khóa không đăng nhập được.
     */
    public function test_locked_user_cannot_login(): void
    {
        $this->user->update(['is_active' => false]);

        $response = $this->post('/dang-nhap', [
            'email' => 'khach@test.vn',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /**
     * Thêm sản phẩm vào giỏ rồi đặt hàng: tồn kho giảm đúng, đơn có tổng đúng.
     */
    public function test_place_order_decreases_stock(): void
    {
        $this->actingAs($this->user);

        // Thêm 2 chiếc ghế vào giỏ (POST form như trình duyệt)
        $this->post('/gio-hang/them', ['product_id' => $this->product->id, 'quantity' => 2]);

        // Đặt hàng COD với địa chỉ + SĐT
        $response = $this->post('/don-hang', [
            'address' => '123 Test Street',
            'phone' => '0900000000',
        ]);

        // Tồn kho giảm 10 -> 8; tổng tiền = 2 x 500.000
        $this->assertDatabaseHas('products', ['id' => $this->product->id, 'stock' => 8]);
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'total' => '1000000.00',
            'status' => 'pending',
            'payment_status' => 'cod_pending',
        ]);
        // Giỏ bị xóa sau khi đặt
        $this->assertDatabaseCount('carts', 0);
    }

    /**
     * Đặt quá số tồn kho phải bị từ chối và KHÔNG trừ kho (transaction rollback).
     */
    public function test_order_rejected_when_stock_insufficient(): void
    {
        $this->actingAs($this->user);

        $this->post('/gio-hang/them', ['product_id' => $this->product->id, 'quantity' => 999]);
        $response = $this->post('/don-hang', [
            'address' => '123 Test Street',
            'phone' => '0900000000',
        ]);

        // Không tạo đơn nào, tồn kho nguyên vẹn
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseHas('products', ['id' => $this->product->id, 'stock' => 10]);
    }

    /**
     * Admin xác nhận đơn: trạng thái chuyển confirmed.
     */
    public function test_admin_can_confirm_order(): void
    {
        $this->actingAs($this->user);
        $this->post('/gio-hang/them', ['product_id' => $this->product->id, 'quantity' => 1]);
        $this->post('/don-hang', ['address' => 'addr', 'phone' => '0900000000']);

        // Lấy id đơn vừa tạo (không giả định = 1 vì sequence không reset giữa các test)
        $orderId = Order::where('user_id', $this->user->id)->value('id');

        // Chuyển sang vai admin và xác nhận đơn
        $this->actingAs($this->admin)
            ->post("/admin/orders/{$orderId}/confirm");

        $this->assertDatabaseHas('orders', ['id' => $orderId, 'status' => 'confirmed']);
    }

    /**
     * User thường bị chặn 403 khi vào khu vực admin.
     */
    public function test_non_admin_blocked_from_admin_area(): void
    {
        $this->actingAs($this->user)
            ->get('/admin')
            ->assertForbidden();
    }

    /**
     * Thêm vào giỏ quá tồn kho phải bị chặn, giỏ không nhận số lượng vượt kho.
     */
    public function test_cannot_add_more_than_stock_to_cart(): void
    {
        $this->actingAs($this->user);

        // Thử thêm 999 chiếc khi kho chỉ có 10 → bị từ chối với flash error
        $response = $this->post('/gio-hang/them', ['product_id' => $this->product->id, 'quantity' => 999]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('carts', 0);
    }

    /**
     * Admin hủy đơn: tồn kho được hoàn lại đúng số lượng đã trừ.
     */
    public function test_cancelling_order_restores_stock(): void
    {
        $this->actingAs($this->user);
        $this->post('/gio-hang/them', ['product_id' => $this->product->id, 'quantity' => 3]);
        $this->post('/don-hang', ['address' => 'addr', 'phone' => '0900000000']);

        // Sau khi đặt: tồn kho 10 -> 7
        $this->assertDatabaseHas('products', ['id' => $this->product->id, 'stock' => 7]);

        $orderId = Order::where('user_id', $this->user->id)->value('id');
        $this->actingAs($this->admin)->post("/admin/orders/{$orderId}/cancel");

        // Hủy đơn: tồn kho hoàn lại 7 -> 10
        $this->assertDatabaseHas('orders', ['id' => $orderId, 'status' => 'cancelled']);
        $this->assertDatabaseHas('products', ['id' => $this->product->id, 'stock' => 10]);
    }

    /**
     * Người khác mua mất một phần hàng trước khi mình chốt đơn: đơn bị từ chối,
     * giỏ TỰ GIẢM về mức còn lại, tồn kho nguyên vẹn.
     */
    public function test_partially_sold_out_adjusts_cart_and_rejects_order(): void
    {
        $this->actingAs($this->user);
        $this->post('/gio-hang/them', ['product_id' => $this->product->id, 'quantity' => 5]);

        // Giả lập người B mua trước 8 chiếc: kho 10 -> 2 (giỏ A vẫn đang giữ 5)
        Product::whereKey($this->product->id)->decrement('stock', 8);

        $response = $this->post('/don-hang', ['address' => 'addr', 'phone' => '0900000000']);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('orders', 0);
        // Giỏ tự điều chỉnh về đúng mức kho còn lại, tồn kho không bị trừ thêm
        $this->assertDatabaseHas('carts', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);
        $this->assertDatabaseHas('products', ['id' => $this->product->id, 'stock' => 2]);
    }

    /**
     * Hết hẳn hàng (khách khác mua chiếc cuối): dòng giỏ bị xóa để user đặt lại được.
     */
    public function test_sold_out_removes_cart_line(): void
    {
        $this->actingAs($this->user);
        $this->post('/gio-hang/them', ['product_id' => $this->product->id, 'quantity' => 2]);

        Product::whereKey($this->product->id)->update(['stock' => 0]);

        $response = $this->post('/don-hang', ['address' => 'addr', 'phone' => '0900000000']);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('carts', 0);
        $this->assertDatabaseHas('products', ['id' => $this->product->id, 'stock' => 0]);
    }

    /**
     * Bỏ tick một món trong giỏ rồi đặt hàng: chỉ món ĐƯỢC TICK vào đơn,
     * món bỏ tick ở lại giỏ cho lần mua sau.
     */
    public function test_only_selected_items_go_into_order(): void
    {
        $this->actingAs($this->user);
        // Thêm 2 món khác nhau
        $other = Product::create([
            'name' => 'Bàn test', 'price' => 2000000, 'stock' => 5, 'category_id' => $this->product->category_id,
        ]);
        $this->post('/gio-hang/them', ['product_id' => $this->product->id, 'quantity' => 1]);
        $this->post('/gio-hang/them', ['product_id' => $other->id, 'quantity' => 2]);

        // Bỏ tick món ghế
        $chairLine = Cart::where('user_id', $this->user->id)->where('product_id', $this->product->id)->first();
        $this->post("/gio-hang/{$chairLine->id}/chon", ['selected' => 0]);

        // Đặt hàng
        $response = $this->post('/don-hang', ['address' => 'addr', 'phone' => '0900000000']);
        $response->assertSessionHas('success');

        // Đơn CHỈ chứa bàn (2 × 2.000.000), không chứa ghế
        $orderId = Order::where('user_id', $this->user->id)->value('id');
        $this->assertDatabaseHas('orders', ['id' => $orderId, 'total' => '4000000.00']);
        $this->assertDatabaseHas('order_items', ['order_id' => $orderId, 'product_id' => $other->id]);
        $this->assertDatabaseMissing('order_items', ['order_id' => $orderId, 'product_id' => $this->product->id]);

        // Ghế bị bỏ tick vẫn nằm lại giỏ; bàn đã đặt bị xóa khỏi giỏ
        $this->assertDatabaseHas('carts', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'selected' => false,
        ]);
        $this->assertDatabaseMissing('carts', ['product_id' => $other->id]);

        // Tồn kho: ghế nguyên vẹn 10, bàn giảm 5 -> 3
        $this->assertDatabaseHas('products', ['id' => $this->product->id, 'stock' => 10]);
        $this->assertDatabaseHas('products', ['id' => $other->id, 'stock' => 3]);
    }

    /**
     * Bỏ tick hết mọi món thì không đặt được hàng (chặn đơn rỗng).
     */
    public function test_cannot_order_when_nothing_selected(): void
    {
        $this->actingAs($this->user);
        $this->post('/gio-hang/them', ['product_id' => $this->product->id, 'quantity' => 1]);

        $line = Cart::where('user_id', $this->user->id)->first();
        $this->post("/gio-hang/{$line->id}/chon", ['selected' => 0]);

        // Trang thanh toán từ chối khi không có món được chọn
        $this->get('/thanh-toan')->assertRedirect(route('cart.index'));

        // POST trực tiếp cũng bị chặn (giỏ trống theo nghĩa "không có món được tick")
        $this->post('/don-hang', ['address' => 'addr', 'phone' => '0900000000'])
            ->assertSessionHas('error');
        $this->assertDatabaseCount('orders', 0);
    }

    /**
     * Hủy 2 lần không hoàn kho 2 lần (guard chống hoàn trùng).
     */
    public function test_cancelling_twice_does_not_double_restore_stock(): void
    {
        $this->actingAs($this->user);
        $this->post('/gio-hang/them', ['product_id' => $this->product->id, 'quantity' => 3]);
        $this->post('/don-hang', ['address' => 'addr', 'phone' => '0900000000']);

        $orderId = Order::where('user_id', $this->user->id)->value('id');

        // Hủy lần 1: hoàn kho về 10; hủy lần 2: kho phải giữ nguyên 10
        $this->actingAs($this->admin)->post("/admin/orders/{$orderId}/cancel");
        $this->actingAs($this->admin)->post("/admin/orders/{$orderId}/cancel");

        $this->assertDatabaseHas('products', ['id' => $this->product->id, 'stock' => 10]);
    }
}
