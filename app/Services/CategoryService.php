<?php

namespace App\Services;

use App\Repositories\CategoryRepository;

// Service danh mục: nghiệp vụ đơn giản bọc qua repository
class CategoryService
{
    public function __construct(private CategoryRepository $repo) {}

    public function list()
    {
        return $this->repo->all();
    }

    public function create(array $data)
    {
        return $this->repo->create($data);
    }

    public function update($id, array $data)
    {
        $c = $this->repo->find($id);

        return $this->repo->update($c, $data);
    }

    public function delete($id)
    {
        $c = $this->repo->find($id);

        return $this->repo->delete($c);
    }
}
