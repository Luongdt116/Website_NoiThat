<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    public function __construct(private ProductRepository $repo) {}

    public function list()
    {
        return $this->repo->paginate();
    }

    public function get(int $id)
    {
        return $this->repo->find($id);
    }

    public function create(array $data)
    {
        return $this->repo->create($data);
    }

    // Tìm kiếm & lọc (từ khóa, danh mục, chất liệu, khoảng giá)
    public function search($kw, $cat, $material, $min, $max): LengthAwarePaginator
    {
        return $this->repo->search($kw, $cat, $material, $min, $max);
    }

    // ===== Trang chủ theo block + trang khuyến mãi =====

    public function onSale(int $n = 12): LengthAwarePaginator
    {
        return $this->repo->onSale($n);
    }

    public function bestSellers(int $limit = 8)
    {
        return $this->repo->bestSellers($limit);
    }

    public function cheapest(int $limit = 8)
    {
        return $this->repo->cheapest($limit);
    }

    public function suggestions(int $limit = 8)
    {
        return $this->repo->suggestions($limit);
    }
}
