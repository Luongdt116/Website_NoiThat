<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private ProductRepository $repo) {}
    public function index() { return ProductResource::collection($this->repo->paginate()); }
    public function store(Request $r) {
        $d = $r->validate([
            'name' => 'required|string|max:255', 'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0', 'category_id' => 'required|exists:categories,id',
            'material' => 'nullable|string', 'image' => 'nullable|string',
        ]);
        return new ProductResource($this->repo->create($d));
    }
    public function show($id) { return new ProductResource($this->repo->find($id)); }
}
