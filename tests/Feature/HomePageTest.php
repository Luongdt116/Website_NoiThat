<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Test các block trang chủ (Gợi ý/Bán chạy/Giá rẻ), trang khuyến mãi và trang chính sách
class HomePageTest extends TestCase
{
    use RefreshDatabase;

    private Category $cat;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cat = Category::create(['name' => 'Bàn', 'slug' => 'ban']);
    }

    private function makeProduct(string $name, int $price, array $extra = []): Product
    {
        return Product::create([
            'name' => $name, 'price' => $price, 'stock' => 10,
            'category_id' => $this->cat->id, ...$extra,
        ]);
    }

    /**
     * Trang chủ hiển thị đủ 3 block: Gợi ý / Bán chạy / Giá rẻ.
     */
    public function test_home_shows_three_blocks(): void
    {
        $this->makeProduct('Bàn A', 1000000);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Gợi ý hôm nay');
        $response->assertSee('Sản phẩm bán chạy');
        $response->assertSee('Sản phẩm giá rẻ');
        $response->assertSee('Bàn A'); // SP xuất hiện trong block
    }

    /**
     * Block "Sản phẩm bán chạy" xếp sản phẩm theo tổng số lượng đã bán giảm dần.
     */
    public function test_best_seller_block_ranks_by_sold_quantity(): void
    {
        $hot = $this->makeProduct('Bàn HOT', 2000000);
        $cold = $this->makeProduct('Bàn lạnh', 9000000);

        // Khách mua qua đơn hàng thật (order_items) để có số liệu bán
        $buyer = User::create([
            'name' => 'Khách mua', 'email' => 'mua@test.vn',
            'password' => bcrypt('password'), 'is_active' => true,
        ]);
        // Bán 5 chiếc Bàn HOT, 1 chiếc Bàn lạnh
        $order = Order::create([
            'user_id' => $buyer->id, 'total' => 19000000, 'status' => 'completed',
            'payment_status' => 'cod_paid', 'address' => 'x', 'phone' => '090',
        ]);
        OrderItem::create([
            'order_id' => $order->id, 'product_id' => $hot->id, 'quantity' => 5, 'price' => 2000000,
        ]);
        OrderItem::create([
            'order_id' => $order->id, 'product_id' => $cold->id, 'quantity' => 1, 'price' => 9000000,
        ]);

        $html = $this->get(route('home'))->getContent();
        // Chỉ xét riêng block "bán chạy" (mỗi SP xuất hiện ở cả 3 block — block Gợi ý lại ngẫu nhiên)
        $bestSection = $this->sectionAfter($html, 'Sản phẩm bán chạy');
        // Bàn HOT phải xuất hiện TRƯỚC Bàn lạnh trong block bán chạy
        $this->assertStrpos($bestSection, 'Bàn HOT', 'Bàn lạnh');
    }

    /**
     * Block "Sản phẩm giá rẻ" đưa sản phẩm giá sau thấp nhất lên đầu.
     */
    public function test_cheapest_block_orders_by_final_price(): void
    {
        $this->makeProduct('Bàn đắt sale sâu', 10000000, ['discount_percent' => 50]); // final 5.000.000
        $this->makeProduct('Bàn rẻ thường', 1200000); // final 1.200.000 — phải đứng đầu

        $html = $this->get(route('home'))->getContent();
        // Chỉ xét riêng block "giá rẻ" để không bị block Gợi ý (ngẫu nhiên) làm nhiễu
        $cheapSection = $this->sectionAfter($html, 'Sản phẩm giá rẻ');
        $this->assertStrpos($cheapSection, 'Bàn rẻ thường', 'Bàn đắt sale sâu');
    }

    /**
     * Trang chính sách render đúng nội dung theo slug; slug lạ trả 404.
     */
    public function test_policy_pages_render_and_unknown_slug_is_404(): void
    {
        foreach (['doi-tra', 'giao-hang', 'bao-mat'] as $slug) {
            $this->get(route('pages.show', $slug))->assertOk();
        }

        $this->get(route('pages.show', 'slug-tuyet-voi'))->assertNotFound();
    }

    /**
     * Footer có khối giới thiệu + link 3 trang chính sách trên mọi trang.
     */
    public function test_footer_has_intro_and_policy_links(): void
    {
        $html = $this->get(route('home'))->getContent();

        $this->assertStringContainsString('Cửa hàng nội thất trực tuyến', $html);
        foreach (['doi-tra', 'giao-hang', 'bao-mat'] as $slug) {
            $this->assertStringContainsString('/chinh-sach/'.$slug, $html);
        }
    }

    // Helper: cắt phần HTML bắt đầu từ tiêu đề section (để test đúng thứ tự trong 1 block)
    private function sectionAfter(string $html, string $heading): string
    {
        $pos = strpos($html, $heading);
        $this->assertNotFalse($pos, "Không tìm thấy tiêu đề '$heading'.");

        return substr($html, $pos);
    }

    // Helper: assert chuỗi $first xuất hiện TRƯỚC $second trong HTML
    private function assertStrpos(string $haystack, string $first, string $second): void
    {
        $this->assertNotFalse(strpos($haystack, $first), "Không tìm thấy '$first' trong HTML.");
        $this->assertNotFalse(strpos($haystack, $second), "Không tìm thấy '$second' trong HTML.");
        $this->assertTrue(
            strpos($haystack, $first) < strpos($haystack, $second),
            "'$first' phải xuất hiện trước '$second'."
        );
    }
}
