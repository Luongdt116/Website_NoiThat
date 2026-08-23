<?php

namespace App\Exceptions;

use Exception;

/**
 * Ném khi đặt hàng mà sản phẩm hết/không đủ hàng (hoặc đã bị xóa).
 * Mang kèm product_id + số lượng còn lại để controller xử lý giỏ hàng
 * (xóa dòng hết hàng) và hiện message thân thiện.
 */
class OutOfStockException extends Exception
{
    public function __construct(
        string $message,
        public readonly int $productId,
        public readonly int $remainingStock,
    ) {}
}
