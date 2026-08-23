@extends('layouts.app')

@section('title', 'Quản lý sản phẩm')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="text-wood mb-0"><i class="bi bi-box-seam"></i> Quản lý sản phẩm</h2>
    <a href="{{ route('admin.products.create') }}" class="btn btn-wood">+ Thêm sản phẩm</a>
</div>

<table class="table bg-white shadow-sm align-middle">
    <thead class="table-light">
        <tr><th>Ảnh</th><th>Tên</th><th>Danh mục</th><th>Giá gốc</th><th>Giảm giá</th><th>Tồn kho</th><th>Chất liệu</th><th width="160">Thao tác</th></tr>
    </thead>
    <tbody>
    @forelse($products as $p)
        <tr>
            <td style="width:60px">
                @if($p->image)
                    <img src="{{ asset('storage/'.$p->image) }}" alt="" class="rounded" width="50" height="50" style="object-fit:cover">
                @else
                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:50px;height:50px">
                        <i class="bi bi-image text-secondary"></i>
                    </div>
                @endif
            </td>
            <td class="fw-bold">{{ $p->name }}</td>
            <td>{{ $p->category->name ?? '—' }}</td>
            <td>{{ number_format($p->price, 0, ',', '.') }} ₫</td>
            <td>
                @if($p->hasDiscount())
                    <span class="badge bg-danger">-{{ $p->discount_percent }}%</span>
                    <div class="small text-muted">còn {{ number_format($p->final_price, 0, ',', '.') }} ₫</div>
                @else
                    <span class="text-muted">—</span>
                @endif
            </td>
            <td>
                {{-- Tồn kho thấp (<5) tô đỏ để admin chú ý nhập thêm --}}
                <span class="badge {{ $p->stock > 5 ? 'bg-success' : ($p->stock > 0 ? 'bg-warning text-dark' : 'bg-danger') }}">
                    {{ $p->stock }}
                </span>
            </td>
            <td>{{ $p->material ?? '—' }}</td>
            <td>
                <a href="{{ route('admin.products.edit', $p->id) }}" class="btn btn-sm btn-outline-primary">Sửa</a>
                <form method="POST" action="{{ route('admin.products.destroy', $p->id) }}" class="d-inline" onsubmit="return confirm('Xóa sản phẩm này?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">Xóa</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="8" class="text-center text-muted py-4">Chưa có sản phẩm nào.</td></tr>
    @endforelse
    </tbody>
</table>
<div class="d-flex justify-content-center">{{ $products->links() }}</div>
@endsection
