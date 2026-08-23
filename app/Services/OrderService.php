<?php

namespace App\Services;

use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private OrderRepository $orders,
        private ProductRepository $products,
    ) {}

    // Đặt hàng nhiều sản phẩm từ giỏ. Dùng transaction để nhất quán tồn kho.
    public function createFromCart(array $items, int $userId, array $info): Order
    {
        return DB::transaction(function () use ($items, $userId, $info) {
            $total = 0;
            $lines = [];
            foreach ($items as $item) {
                $product = $this->products->find($item['product_id']);
                if (! $product || $product->stock < $item['quantity']) {
                    throw new Exception("Sản phẩm {$product->name} chỉ còn {$product->stock}");
                }
                $total += $product->price * $item['quantity'];
                $lines[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ];
                $this->products->decreaseStock($product->id, $item['quantity']);
            }

            return $this->orders->createWithItems([
                'user_id' => $userId, 'total' => $total,
                'status' => 'pending', 'payment_status' => 'cod_pending',
                'address' => $info['address'], 'phone' => $info['phone'],
            ], $lines);
        });
    }

    public function history(int $userId)
    {
        return $this->orders->ofUser($userId);
    }

    public function list()
    {
        return $this->orders->all();
    }

    // Chi tiết 1 đơn (kèm items + product) cho trang xem đơn
    public function find(int $id)
    {
        return $this->orders->find($id);
    }

    public function updateStatus(int $id, string $status)
    {
        $this->orders->updateStatus($id, $status);
    }
}
