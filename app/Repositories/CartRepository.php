<?php

namespace App\Repositories;

use App\Models\Cart;

class CartRepository
{
    public function items(int $userId)
    {
        return Cart::with('product')->where('user_id', $userId)->get();
    }

    public function add(int $userId, int $productId, int $qty)
    {
        $item = Cart::firstOrNew(['user_id' => $userId, 'product_id' => $productId]);
        $item->quantity = ($item->exists ? $item->quantity : 0) + $qty;
        $item->save();

        return $item;
    }

    public function updateQty(int $id, int $qty)
    {
        return Cart::where('id', $id)->update(['quantity' => $qty]);
    }

    public function remove(int $id)
    {
        return Cart::where('id', $id)->delete();
    }

    public function clear(int $userId)
    {
        return Cart::where('user_id', $userId)->delete();
    }
}
