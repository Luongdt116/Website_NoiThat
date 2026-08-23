<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

// Seeder sản phẩm: dữ liệu mẫu trải đều 5 danh mục
class ProductSeeder extends Seeder
{
    // Dữ liệu mẫu: [tên, giá, giảm giá %, tồn kho, chất liệu, slug danh mục]
    private array $items = [
        ['Bàn ăn gỗ sồi 6 chỗ', 4500000, 15, 10, 'Gỗ sồi', 'ban'],
        ['Bàn làm việc góc', 2100000, 0, 15, 'Gỗ MDF', 'ban'],
        ['Bàn trà phòng khách', 1250000, 20, 20, 'Gỗ thông', 'ban'],
        ['Ghế ergonomic lưng lưới', 1800000, 0, 25, 'Lưới + kim loại', 'ghe'],
        ['Ghế sofa vải nỉ 3 chỗ', 8900000, 10, 6, 'Vải nỉ', 'ghe'],
        ['Ghế ăn gỗ óc chó', 2350000, 25, 12, 'Gỗ óc chó', 'ghe'],
        ['Tủ quần áo 3 cánh', 6200000, 0, 8, 'Gỗ MDF', 'tu'],
        ['Tủ giày 4 tầng', 1490000, 30, 18, 'Nhựa dán', 'tu'],
        ['Giường ngủ gỗ tự nhiên', 9800000, 20, 5, 'Gỗ sồi', 'giuong'],
        ['Giường gấp thông minh', 3200000, 0, 9, 'Kim loại', 'giuong'],
        ['Kệ sách 5 tầng', 1650000, 15, 14, 'Gỗ công nghiệp', 'ke'],
        ['Kệ tivi phòng khách', 2750000, 0, 11, 'Gỗ MDF', 'ke'],
    ];

    public function run(): void
    {
        foreach ($this->items as [$name, $price, $discount, $stock, $material, $catSlug]) {
            // Tìm danh mục theo slug (CategorySeeder đã tạo trước)
            $category = Category::where('slug', $catSlug)->first();
            if (! $category) {
                continue; // bỏ qua nếu danh mục chưa tồn tại
            }
            Product::create([
                'name' => $name,
                'price' => $price,
                'discount_percent' => $discount,
                'stock' => $stock,
                'material' => $material,
                'category_id' => $category->id,
                'description' => 'Sản phẩm '.$name.' — chất liệu '.$material.', phù hợp cho không gian sống hiện đại.',
            ]);
        }
    }
}
