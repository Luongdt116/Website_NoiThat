<?php
namespace App\Services;
use App\Repositories\CartRepository;

class CartService
{
    public function __construct(private CartRepository $repo) {}
    public function items(int $userId) { return $this->repo->items($userId); }
    public function add(int $userId, int $pid, int $qty) { return $this->repo->add($userId, $pid, $qty); }
    public function updateQty(int $id, int $qty) { return $this->repo->updateQty($id, $qty); }
    public function remove(int $id) { return $this->repo->remove($id); }
    public function clear(int $userId) { return $this->repo->clear($userId); }
}
