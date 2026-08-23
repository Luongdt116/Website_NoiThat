<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Test luồng nghiệp vụ cốt lõi: giỏ hàng → đặt hàng → tồn kho giảm → admin duyệt
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
}
