<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

// Seeder chính: tạo tài khoản demo + gọi các seeder dữ liệu mẫu
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ===== Tài khoản admin (đăng nhập khu vực /admin) =====
        User::create([
            'name' => 'Quản trị viên',
            'email' => 'admin@furniture.test',
            'password' => bcrypt('password'),
            'is_admin' => true,
            'is_active' => true,
        ]);

        // ===== Tài khoản khách hàng mẫu =====
        User::create([
            'name' => 'Nguyễn Văn A',
            'email' => 'user@furniture.test',
            'password' => bcrypt('password'),
            'is_admin' => false,
            'is_active' => true,
        ]);

        // Dữ liệu mẫu: danh mục -> sản phẩm (theo thứ tự để có khóa ngoại)
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
        ]);
    }
}
