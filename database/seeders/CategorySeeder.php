<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Category;

// Seeder danh mục mẫu
class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $items = ['Bàn', 'Ghế', 'Tủ', 'Giường', 'Kệ'];
        foreach ($items as $name) {
            Category::create(['name' => $name, 'slug' => \Illuminate\Support\Str::slug($name), 'description' => "Danh mục $name"]);
        }
    }
}
