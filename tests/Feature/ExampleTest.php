<?php

namespace Tests\Feature;

// RefreshDatabase: reset DB trong memory/file sqlite cho từng test (nhanh, cô lập)
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Trang chủ trả về 200 và hiển thị sản phẩm đã seed.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // Tạo dữ liệu mẫu tối thiểu: 1 danh mục + 1 sản phẩm
        Category::create(['name' => 'Bàn', 'slug' => 'ban']);
        Product::create([
            'name' => 'Bàn test', 'price' => 100000, 'stock' => 5, 'category_id' => 1,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Bàn test');
    }
}
