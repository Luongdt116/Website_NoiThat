@extends('layouts.app')

@section('title', isset($category) ? 'Sửa danh mục' : 'Thêm danh mục')

@section('content')
<h2>{{ isset($category) ? 'Sửa danh mục' : 'Thêm danh mục' }}</h2>
<form method="POST"
      action="{{ isset($category) ? route('admin.categories.update', $category->id) : route('admin.categories.store') }}"
      class="card p-4" style="max-width:560px">
    @csrf
    @if(isset($category))
        @method('PUT')
    @endif
    <div class="mb-3">
        <label class="form-label">Tên danh mục <span class="text-danger">*</span></label>
        <input name="name" value="{{ old('name', $category->name ?? '') }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Slug (để trống sẽ tự sinh)</label>
        <input name="slug" value="{{ old('slug', $category->slug ?? '') }}" class="form-control" placeholder="vd: ban-ghe">
    </div>
    <div class="mb-3">
        <label class="form-label">Mô tả</label>
        <textarea name="description" rows="3" class="form-control">{{ old('description', $category->description ?? '') }}</textarea>
    </div>
    <button class="btn btn-wood">💾 Lưu</button>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary ms-2">Hủy</a>
</form>
@endsection
