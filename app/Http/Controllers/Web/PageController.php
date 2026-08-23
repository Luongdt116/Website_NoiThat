<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

// Trang tĩnh: các trang chính sách của cửa hàng (đổi trả, giao hàng, bảo mật)
class PageController extends Controller
{
    // Danh sách slug hợp lệ -> tiêu đề tương ứng
    private array $pages = [
        'doi-tra' => 'Chính sách đổi trả',
        'giao-hang' => 'Chính sách giao hàng',
        'bao-mat' => 'Chính sách bảo mật',
    ];

    public function show(string $slug)
    {
        // Slug lạ -> 404 thay vì render trang rỗng
        if (! isset($this->pages[$slug])) {
            abort(404);
        }

        return view('pages.show', [
            'slug' => $slug,
            'title' => $this->pages[$slug],
        ]);
    }
}
