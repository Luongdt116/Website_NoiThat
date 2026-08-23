<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductRepository
{
    public function paginate(int $n = 12)
    {
        return Product::with('category')->latest()->paginate($n);
    }

    public function find(int $id): ?Product
    {
        return Product::find($id);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $p, array $data): bool
    {
        return $p->update($data);
    }

    public function decreaseStock(int $id, int $qty): void
    {
        Product::where('id', $id)->decrement('stock', $qty);
    }

    public function search(?string $kw, ?int $cat, ?string $material, ?float $min, ?float $max): LengthAwarePaginator
    {
        return Product::with('category')
            ->when($kw, fn ($q) => $q->where('name', 'like', "%$kw%"))
            ->when($cat, fn ($q) => $q->where('category_id', $cat))
            ->when($material, fn ($q) => $q->where('material', $material))
            ->when($min, fn ($q) => $q->where('price', '>=', $min))
            ->when($max, fn ($q) => $q->where('price', '<=', $max))
            ->paginate(12);
    }

    // ===== Trang chủ theo block + trang khuyến mãi =====

    // Sản phẩm đang giảm giá (sắp giảm nhiều trước), có phân trang cho trang khuyến mãi
    public function onSale(int $n = 12): LengthAwarePaginator
    {
        return Product::with('category')
            ->where('discount_percent', '>', 0)
            ->orderByDesc('discount_percent')
            ->orderBy('name')
            ->paginate($n);
    }

    // Sản phẩm bán chạy: tổng số lượng bán trong order_items, giảm dần
    public function bestSellers(int $limit = 8)
    {
        return Product::with('category')
            ->selectRaw('products.*, COALESCE(SUM(order_items.quantity), 0) AS sold_count')
            ->leftJoin('order_items', 'order_items.product_id', '=', 'products.id')
            ->groupBy('products.id')
            ->orderByDesc('sold_count')
            ->limit($limit)
            ->get();
    }

    // Sản phẩm giá rẻ: giá sau giảm thấp nhất (để block "Sản phẩm giá rẻ" không gợi ý nhầm sản phẩm đắt đang sale sâu)
    public function cheapest(int $limit = 8)
    {
        return Product::with('category')
            ->get()
            ->sortBy(fn ($p) => $p->final_price)
            ->take($limit)
            ->values();
    }

    // Gợi ý hôm nay: sản phẩm mới nhất xen kẽ ngẫu nhiên nhẹ (mỗi lần vào hơi khác nhau)
    public function suggestions(int $limit = 8)
    {
        return Product::with('category')->inRandomOrder()->limit($limit)->get();
    }
}
