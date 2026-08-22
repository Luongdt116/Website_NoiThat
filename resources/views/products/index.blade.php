@extends('layouts.app')

@section('title', 'Sản phẩm nội thất')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="text-wood mb-0"><i class="bi bi-collection"></i> Sản phẩm nội thất</h2>
    <span class="text-muted">{{ $products->total() }} sản phẩm</span>
</div>

{{-- ===== Form tìm kiếm & lọc: từ khóa, danh mục, chất liệu, khoảng giá ===== --}}
<form method="GET" class="card p-3 mb-4">
    <div class="row g-2 align-items-end">
        <div class="col-12 col-md-3">
            <label class="form-label small text-muted">Từ khóa</label>
            <input name="keyword" value="{{ request('keyword') }}" class="form-control" placeholder="VD: bàn gỗ...">
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label small text-muted">Danh mục</label>
            <select name="category" class="form-select">
                <option value="">Tất cả</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label small text-muted">Chất liệu</label>
            <input name="material" value="{{ request('material') }}" class="form-control" placeholder="Gỗ, vải...">
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label small text-muted">Giá từ</label>
            <input name="min" type="number" min="0" value="{{ request('min') }}" class="form-control" placeholder="0">
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label small text-muted">Đến</label>
            <input name="max" type="number" min="0" value="{{ request('max') }}" class="form-control" placeholder="10.000.000">
        </div>
        <div class="col-12 col-md-1 d-grid">
            <button class="btn btn-wood"><i class="bi bi-funnel"></i></button>
        </div>
    </div>
    {{-- Giữ tham số phân trang khi lọc --}}
    @foreach(['keyword','category','material','min','max'] as $k)
        @if(request($k))<input type="hidden" name="{{ $k }}" value="{{ request($k) }}">@endif
    @endforeach
</form>

{{-- ===== Lưới sản phẩm ===== --}}
@if($products->isEmpty())
    <div class="text-center text-muted py-5">
        <i class="bi bi-search" style="font-size:3rem"></i>
        <p class="mt-3">Không tìm thấy sản phẩm phù hợp.</p>
    </div>
@else
    <div class="row">
        @foreach($products as $product)
            <x-product-card :product="$product" />
        @endforeach
    </div>
    <div class="d-flex justify-content-center mt-2">{{ $products->withQueryString()->links() }}</div>
@endif
@endsection
