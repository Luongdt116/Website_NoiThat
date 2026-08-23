<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

// Seeder danh mục mẫu
class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $items = ['Bàn', 'Ghế', 'Tủ', 'Giường', 'Kệ'];
        foreach ($items as $name) {
            Category::create(['name' => $name, 'slug' => Str::slug($name), 'description' => "Danh mục $name"]);
        }
    }
}
