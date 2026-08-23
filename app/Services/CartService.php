<?php

namespace App\Services;

use App\Repositories\CartRepository;

class CartService
{
    public function __construct(private CartRepository $repo) {}

    public function items(int $userId)
    {
        return $this->repo->items($userId);
    }

    // Chỉ những món được tick — dùng khi đặt hàng
    public function selectedItems(int $userId)
    {
        return $this->repo->selectedItems($userId);
    }

    // Tick/bỏ tick 1 dòng giỏ
    public function setSelected(int $id, bool $selected): void
    {
        $this->repo->setSelected($id, $selected);
    }

    public function add(int $userId, int $pid, int $qty)
    {
        return $this->repo->add($userId, $pid, $qty);
    }

    public function updateQty(int $id, int $qty)
    {
        return $this->repo->updateQty($id, $qty);
    }

    public function remove(int $id)
    {
        return $this->repo->remove($id);
    }

    public function clear(int $userId)
    {
        return $this->repo->clear($userId);
    }
}
