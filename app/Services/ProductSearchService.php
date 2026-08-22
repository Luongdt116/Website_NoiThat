<?php
namespace App\Services;
use App\Repositories\ProductRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

// Service tìm kiếm & lọc (từ khóa, danh mục, chất liệu, khoảng giá)
class ProductSearchService
{
    public function __construct(private ProductRepository $repo) {}
    public function run($kw, $cat, $material, $min, $max): LengthAwarePaginator
    {
        return $this->repo->search($kw, $cat, $material, $min, $max);
    }
}
