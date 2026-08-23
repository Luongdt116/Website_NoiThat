<?php

namespace App\Services;

use App\Exceptions\OutOfStockException;
use App\Models\Order;
use App\Models\Product;
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
        // Chặn đặt hàng với giỏ rỗng (tránh tạo đơn 0₫ vô nghĩa)
        if ($items === []) {
            throw new Exception('Giỏ hàng trống, hãy thêm sản phẩm trước khi đặt.');
        }

        return DB::transaction(function () use ($items, $userId, $info) {
            $total = 0;
            $lines = [];
            foreach ($items as $item) {
                // lockForUpdate: chặn 2 đơn đặt song song cùng đọc stock cũ rồi cùng trừ (âm kho).
                // Request sau phải đợi khóa nhả mới đọc được stock MỚI NHẤT.
                $product = Product::whereKey($item['product_id'])->lockForUpdate()->first();
                // Phân biệt 2 loại lỗi để message thân thiện với người dùng
                if (! $product) {
                    throw new OutOfStockException('Một sản phẩm trong giỏ không còn tồn tại, hãy xóa nó khỏi giỏ.', $item['product_id'], 0);
                }
                if ($product->stock < $item['quantity']) {
                    throw new OutOfStockException(
                        "Rất tiếc, '{$product->name}' vừa bị khách khác mua mất một phần (chỉ còn {$product->stock}).",
                        $product->id,
                        (int) $product->stock
                    );
                }
                // Đơn hàng chốt theo GIÁ SAU GIẢM tại thời điểm đặt (final_price accessor)
                $total += $product->final_price * $item['quantity'];
                $lines[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->final_price,
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
        DB::transaction(function () use ($id, $status) {
            // Khóa dòng đơn để tránh 2 admin hủy cùng lúc → hoàn kho 2 lần
            $order = Order::with('items')->lockForUpdate()->findOrFail($id);
            $oldStatus = $order->status;

            // Hủy đơn: hoàn lại tồn kho đã trừ khi đặt (chỉ hoàn 1 lần — nếu đơn đã hủy thì bỏ qua)
            if ($status === 'cancelled' && $oldStatus !== 'cancelled') {
                foreach ($order->items as $item) {
                    Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                }
            }

            $this->orders->updateStatus($id, $status);
        });
    }
}
