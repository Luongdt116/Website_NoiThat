<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;

// Controller API: trả JSON danh mục (chuẩn REST)
class CategoryController extends Controller
{
    public function __construct(private CategoryRepository $repo) {}

    public function index()
    {
        return CategoryResource::collection($this->repo->all());
    }

    public function store(Request $r)
    {
        $d = $r->validate(['name' => 'required', 'slug' => 'required|unique:categories']);

        return new CategoryResource($this->repo->create($d));
    }
}
