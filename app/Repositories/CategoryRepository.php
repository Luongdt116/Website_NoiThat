<?php
namespace App\Repositories;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

// Repository: truy xuất danh mục qua Eloquent (tách khỏi nghiệp vụ)
class CategoryRepository
{
    public function all(): Collection { return Category::orderBy('name')->get(); }
    public function find(int $id): ?Category { return Category::find($id); }
    public function create(array $data): Category { return Category::create($data); }
    public function update(Category $c, array $data): bool { return $c->update($data); }
    public function delete(Category $c): bool { return $c->delete(); }
}
