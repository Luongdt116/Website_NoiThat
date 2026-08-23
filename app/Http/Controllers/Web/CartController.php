<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartService $service) {}

    // Thêm vào giỏ: kiểm tra tồn kho trước khi lưu
    public function add(Request $r)
    {
        $product = Product::find($r->product_id);

        if (! $product) {
            return back()->with('error', 'Sản phẩm không tồn tại.');
        }

        $qty = max(1, (int) ($r->quantity ?? 1));

        // Tổng trong giỏ (nếu đã có sẵn) + số lượng thêm không được vượt tồn kho
        $existing = Cart::where('user_id', auth()->id())->where('product_id', $product->id)->first();
        if (($existing?->quantity ?? 0) + $qty > $product->stock) {
            return back()->with('error', "Chỉ còn {$product->stock} sản phẩm '{$product->name}' trong kho.");
        }

        $this->service->add(auth()->id(), $product->id, $qty);

        // Món mới vào giỏ mặc định được tick (default DB); món đã có mà từng bỏ tick
        // thì tick lại khi người dùng chủ động thêm lần nữa
        if ($existing && ! $existing->selected) {
            $existing->update(['selected' => true]);
        }

        return redirect()->route('cart.index')->with('success', 'Đã thêm vào giỏ');
    }

    public function index()
    {
        return view('cart.index', ['items' => $this->service->items(auth()->id())]);
    }

    // Tick / bỏ tick chọn món để thanh toán (checkbox trên trang giỏ)
    public function select(Request $r, $id)
    {
        // Chỉ chủ giỏ được tick dòng của mình
        $item = Cart::where('id', $id)->where('user_id', auth()->id())->first();

        if (! $item) {
            return back()->with('error', 'Không tìm thấy sản phẩm trong giỏ.');
        }

        $data = $r->validate(['selected' => 'required|boolean']);
        $this->service->setSelected($item->id, (bool) $data['selected']);

        return back();
    }

    // Cập nhật số lượng 1 dòng giỏ — chỉ chủ giỏ mới được sửa
    public function update(Request $r, $id)
    {
        $item = Cart::where('id', $id)->where('user_id', auth()->id())->first();

        if (! $item) {
            return back()->with('error', 'Không tìm thấy sản phẩm trong giỏ.');
        }

        $data = $r->validate([
            'quantity' => 'required|integer|min:1|max:'.$item->product->stock,
        ]);
        $this->service->updateQty($item->id, $data['quantity']);

        return back()->with('success', 'Đã cập nhật số lượng.');
    }

    // Xóa 1 dòng giỏ — chỉ chủ giỏ mới được xóa
    public function remove($id)
    {
        $deleted = Cart::where('id', $id)->where('user_id', auth()->id())->delete();

        return back()->with($deleted ? 'success' : 'error', $deleted ? 'Đã xóa khỏi giỏ.' : 'Không tìm thấy sản phẩm trong giỏ.');
    }
}
