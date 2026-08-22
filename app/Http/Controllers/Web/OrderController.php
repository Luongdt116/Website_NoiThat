<?php
namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Models\Order; // dùng để kiểm tra quyền xem đơn của chính mình
use App\Services\OrderService;
use App\Services\CartService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private OrderService $orders, private CartService $cart) {}

    // Trang thanh toán: hiển thị tóm tắt giỏ + form địa chỉ/SĐT
    public function checkout()
    {
        $items = $this->cart->items(auth()->id());

        // Giỏ trống thì không cho vào trang thanh toán
        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống, hãy thêm sản phẩm trước.');
        }

        return view('orders.checkout', ['items' => $items]);
    }

    // Lưu đơn: bọc transaction trong OrderService, trừ tồn kho, xóa giỏ
    public function store(Request $r)
    {
        $items = $this->cart->items(auth()->id())
            ->map(fn($i) => ['product_id' => $i->product_id, 'quantity' => $i->quantity])
            ->toArray();

        try {
            $order = $this->orders->createFromCart($items, auth()->id(), $r->validate([
                'address' => 'required|string|max:500',
                'phone'   => 'required|string|max:20',
            ]));
            $this->cart->clear(auth()->id()); // xóa giỏ sau khi đặt thành công

            return redirect()->route('orders.show', $order->id)->with('success', 'Đặt hàng thành công! Chúng tôi sẽ liên hệ bạn sớm.');
        } catch (\Exception $e) {
            // Thường là hết hàng: transaction đã rollback, tồn kho nguyên vẹn
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    // Lịch sử đơn của user hiện tại
    public function history()
    {
        return view('orders.history', ['orders' => $this->orders->history(auth()->id())]);
    }

    // Chi tiết đơn — chỉ chủ đơn hoặc admin được xem
    public function show($id)
    {
        $order = $this->orders->find($id);

        if (!$order) {
            abort(404);
        }
        if ($order->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403, 'Bạn không có quyền xem đơn hàng này.');
        }

        return view('orders.show', ['order' => $order]);
    }
}
