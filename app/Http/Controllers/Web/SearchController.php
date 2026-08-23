<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Repositories\CategoryRepository;
use App\Services\ProductSearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(private ProductSearchService $search, private CategoryRepository $cats) {}

    public function __invoke(Request $r)
    {
        $products = $this->search->run($r->keyword, $r->category, $r->material, $r->min, $r->max);

        return view('products.index', ['products' => $products, 'categories' => $this->cats->all()]);
    }
}
