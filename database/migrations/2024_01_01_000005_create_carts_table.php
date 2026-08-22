<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Bảng giỏ hàng lưu DB (mỗi dòng = 1 sản phẩm trong giỏ của 1 user)
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');   // chủ giỏ hàng
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // sản phẩm được thêm
            $table->integer('quantity')->default(1);                            // số lượng
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
