@extends('layouts.app')

@section('title', 'Quản lý danh mục')

@section('content')
<h2>Quản lý danh mục</h2>
<a href="{{ route('admin.categories.create') }}" class="btn btn-wood mb-3">+ Thêm danh mục</a>
<table class="table bg-white shadow-sm align-middle">
    <thead class="table-light">
        <tr><th>Tên</th><th>Slug</th><th>Số sản phẩm</th><th width="180">Thao tác</th></tr>
    </thead>
    <tbody>
    @forelse($categories as $c)
        <tr>
            <td>{{ $c->name }}</td>
            <td><code>{{ $c->slug }}</code></td>
            <td><span class="badge bg-secondary">{{ $c->products_count }}</span></td>
            <td>
                <a href="{{ route('admin.categories.edit', $c->id) }}" class="btn btn-sm btn-outline-primary">Sửa</a>
                <form method="POST" action="{{ route('admin.categories.destroy', $c->id) }}" class="d-inline" onsubmit="return confirm('Xóa danh mục này?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">Xóa</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="4" class="text-center text-muted">Chưa có danh mục nào.</td></tr>
    @endforelse
    </tbody>
</table>
@endsection
