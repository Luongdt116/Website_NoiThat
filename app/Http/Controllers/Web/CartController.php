<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartService $service) {}

    public function add(Request $r)
    {
        $this->service->add(auth()->id(), $r->product_id, $r->quantity ?? 1);

        return redirect()->route('cart.index')->with('success', 'Đã thêm vào giỏ');
    }

    public function index()
    {
        return view('cart.index', ['items' => $this->service->items(auth()->id())]);
    }

    public function update(Request $r, $id)
    {
        $this->service->updateQty($id, $r->quantity);

        return back();
    }

    public function remove($id)
    {
        $this->service->remove($id);

        return back();
    }
}
