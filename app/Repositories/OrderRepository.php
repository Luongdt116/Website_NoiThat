<?php
namespace App\Repositories;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

class OrderRepository
{
    // Tạo đơn + items trong 1 hàm (Service gọi trong transaction)
    public function createWithItems(array $orderData, array $items): Order
    {
        $order = Order::create($orderData);
        $order->items()->createMany($items);
        return $order;
    }
    public function ofUser(int $userId): Collection { return Order::with('items.product')->where('user_id', $userId)->latest()->get(); }
    public function find(int $id): ?Order { return Order::with('items.product')->find($id); }
    public function all() { return Order::with('user')->latest()->paginate(15); }
    public function updateStatus(int $id, string $status): void { Order::where('id', $id)->update(['status' => $status]); }
}
