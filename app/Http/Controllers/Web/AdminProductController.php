<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// Admin quản lý sản phẩm: CRUD + upload ảnh + tồn kho
class AdminProductController extends Controller
{
    // Quy tắc validate dùng chung cho store/update
    private array $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'discount_percent' => 'nullable|integer|min:0|max:90',
        'stock' => 'required|integer|min:0',
        'material' => 'nullable|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ];

    // Danh sách sản phẩm trong admin (kèm phân trang)
    public function index()
    {
        return view('admin.products.index', [
            'products' => Product::with('category')->latest()->paginate(15),
        ]);
    }

    // Form thêm mới (cần danh sách danh mục cho dropdown)
    public function create()
    {
        return view('admin.products.form', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    // Lưu sản phẩm mới, có xử lý upload ảnh
    public function store(Request $request)
    {
        $data = $request->validate($this->rules);

        // Upload ảnh vào storage/app/public/products
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Đã thêm sản phẩm.');
    }

    // Form sửa — tái dùng form.blade.php với biến $product
    public function edit($id)
    {
        return view('admin.products.form', [
            'product' => Product::findOrFail($id),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    // Cập nhật, có thay ảnh nếu upload ảnh mới
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $data = $request->validate($this->rules);

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ trước khi lưu ảnh mới (tránh rác storage)
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Đã cập nhật sản phẩm.');
    }

    // Xóa sản phẩm (ảnh đính kèm cũng bị xóa)
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Đã xóa sản phẩm.');
    }
}
