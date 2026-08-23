<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Repositories\CategoryRepository;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private ProductService $service, private CategoryRepository $cats) {}

    public function index(Request $r)
    {
        $products = $this->service->search($r->keyword, $r->category, $r->material, $r->min, $r->max);
        $categories = $this->cats->all();

        return view('products.index', compact('products', 'categories'));
    }

    public function show($id)
    {
        return view('products.show', ['product' => $this->service->get($id)]);
    }
}
