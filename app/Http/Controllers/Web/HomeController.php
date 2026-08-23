<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ProductService;

// Trang chủ: sản phẩm chia theo block (Gợi ý / Bán chạy / Giá rẻ) + trang khuyến mãi
class HomeController extends Controller
{
    public function __construct(private ProductService $products) {}

    // Trang chủ — mỗi block tối đa 8 sản phẩm
    public function index()
    {
        return view('home', [
            'suggestions' => $this->products->suggestions(),
            'bestSellers' => $this->products->bestSellers(),
            'cheapest' => $this->products->cheapest(),
            'onSaleCount' => $this->products->onSale(1)->total(), // đếm nhanh số SP đang sale để hiện badge link
        ]);
    }

    // Trang khuyến mãi — chỉ sản phẩm đang giảm giá, có phân trang
    public function sale()
    {
        return view('products.sale', [
            'products' => $this->products->onSale(),
        ]);
    }
}
