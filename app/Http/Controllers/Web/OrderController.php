<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\OutOfStockException;
use App\Http\Controllers\Controller;
use App\Models\Cart;
// dùng để kiểm tra quyền xem đơn của chính mình
use App\Services\CartService;
use App\Services\OrderService;
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
            ->map(fn ($i) => ['product_id' => $i->product_id, 'quantity' => $i->quantity])
            ->toArray();

        try {
            $order = $this->orders->createFromCart($items, auth()->id(), $r->validate([
                'address' => 'required|string|max:500',
                'phone' => 'required|string|max:20',
            ]));
            $this->cart->clear(auth()->id()); // xóa giỏ sau khi đặt thành công

            return redirect()->route('orders.show', $order->id)->with('success', 'Đặt hàng thành công! Chúng tôi sẽ liên hệ bạn sớm.');
        } catch (OutOfStockException $e) {
            // Hết hàng do khách khác mua mất trước: dọn dòng giỏ tương ứng để user không bị kẹt lặp lại lỗi
            $this->handleOutOfStock($e);

            return back()->withInput()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            // Lỗi nghiệp vụ khác: transaction đã rollback, tồn kho nguyên vẹn
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Xử lý giỏ khi sản phẩm vừa bị mua mất:
     * - Còn 0 (hoặc sản phẩm đã xóa): xóa luôn dòng giỏ.
     * - Còn ít hơn số trong giỏ: giảm số lượng giỏ về mức còn lại.
     */
    private function handleOutOfStock(OutOfStockException $e): void
    {
        $userId = auth()->id();
        $cartItem = Cart::where('user_id', $userId)->where('product_id', $e->productId)->first();

        if ($e->remainingStock <= 0) {
            optional($cartItem)->delete();

            return;
        }
        if ($cartItem && $cartItem->quantity > $e->remainingStock) {
            $this->cart->updateQty($cartItem->id, $e->remainingStock);
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

        if (! $order) {
            abort(404);
        }
        if ($order->user_id !== auth()->id() && ! auth()->user()->is_admin) {
            abort(403, 'Bạn không có quyền xem đơn hàng này.');
        }

        return view('orders.show', ['order' => $order]);
    }
}
