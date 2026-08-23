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
}
