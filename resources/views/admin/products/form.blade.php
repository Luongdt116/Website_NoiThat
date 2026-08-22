@extends('layouts.app')

@section('title', isset($product) ? 'Sửa sản phẩm' : 'Thêm sản phẩm')

@section('content')
<h2>{{ isset($product) ? 'Sửa sản phẩm' : 'Thêm sản phẩm' }}</h2>
<form method="POST"
      action="{{ isset($product) ? route('admin.products.update', $product->id) : route('admin.products.store') }}"
      enctype="multipart/form-data" class="card p-4">
    @csrf
    @if(isset($product))
        @method('PUT')
    @endif
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
            <input name="name" value="{{ old('name', $product->name ?? '') }}" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Danh mục <span class="text-danger">*</span></label>
            <select name="category_id" class="form-select" required>
                <option value="">-- Chọn danh mục --</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" @selected(old('category_id', $product->category_id ?? '') == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Giá (₫) <span class="text-danger">*</span></label>
            <input name="price" type="number" step="1000" min="0" value="{{ old('price', $product->price ?? '') }}" class="form-control" required>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Tồn kho <span class="text-danger">*</span></label>
            <input name="stock" type="number" min="0" value="{{ old('stock', $product->stock ?? 0) }}" class="form-control" required>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Chất liệu</label>
            <input name="material" value="{{ old('material', $product->material ?? '') }}" class="form-control" placeholder="VD: Gỗ sồi, vải nỉ...">
        </div>
        <div class="col-12 mb-3">
            <label class="form-label">Mô tả</label>
            <textarea name="description" rows="4" class="form-control">{{ old('description', $product->description ?? '') }}</textarea>
        </div>
        <div class="col-12 mb-3">
            <label class="form-label">Ảnh sản phẩm (jpg/png/webp, tối đa 2MB)</label>
            <input name="image" type="file" class="form-control" accept="image/*">
            @if(!empty($product->image))
                <img src="{{ asset('storage/'.$product->image) }}" alt="Ảnh hiện tại" class="mt-2 rounded" width="120">
            @endif
        </div>
    </div>
    <button class="btn btn-wood">💾 Lưu sản phẩm</button>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary ms-2">Hủy</a>
</form>
@endsection
