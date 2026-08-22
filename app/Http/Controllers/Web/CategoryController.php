<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

// Controller Web: admin quản lý danh mục (CRUD đầy đủ)
class CategoryController extends Controller
{
    // Danh sách danh mục
    public function index()
    {
        return view('admin.categories.index', [
            'categories' => Category::withCount('products')->orderBy('name')->get(),
        ]);
    }

    // Form thêm mới
    public function create()
    {
        return view('admin.categories.form');
    }

    // Lưu danh mục mới (slug tự sinh từ tên nếu để trống)
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:categories,slug',
            'description' => 'nullable|string',
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        Category::create($data);
        return redirect()->route('admin.categories.index')->with('success', 'Đã thêm danh mục.');
    }

    // Form sửa
    public function edit($id)
    {
        return view('admin.categories.form', ['category' => Category::findOrFail($id)]);
    }

    // Cập nhật danh mục
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:categories,slug,'.$category->id,
            'description' => 'nullable|string',
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        $category->update($data);
        return redirect()->route('admin.categories.index')->with('success', 'Đã cập nhật danh mục.');
    }

    // Xóa danh mục
    public function destroy($id)
    {
        Category::findOrFail($id)->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Đã xóa danh mục.');
    }
}
