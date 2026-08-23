<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Thêm cột selected vào carts: tick chọn món nào sẽ đặt trong lần thanh toán tới
// (mô phỏng giỏ kiểu Shopee/Tiki — món không tick ở lại giỏ, không vào đơn)
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->boolean('selected')->default(true)->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn('selected');
        });
    }
};
