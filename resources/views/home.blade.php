@extends('layouts.app')

@section('title', 'Trang chủ — Nội thất cho mọi ngôi nhà')

@section('content')
{{-- ===== Hero ngắn dẫn vào các block ===== --}}
<div class="p-5 mb-4 rounded-3 hero-banner">
    <div class="container-fluid py-3">
        <h1 class="display-6 fw-bold">Nội thất gỗ ấm cho mọi ngôi nhà</h1>
        <p class="col-md-8 fs-5 mb-4">Từ bàn ghế đến tủ kệ — chất liệu thật, giá thật, giao hàng COD toàn quốc.
            Hôm nay có <span class="badge bg-danger">{{ $onSaleCount }} sản phẩm đang giảm giá</span>.</p>
        <a href="{{ route('home.sale') }}" class="btn btn-light btn-lg me-2">
            <i class="bi bi-percent"></i> Xem khuyến mãi
        </a>
        <a href="{{ route('products.index') }}" class="btn btn-outline-light btn-lg">Duyệt tất cả sản phẩm</a>
    </div>
</div>

{{-- ===== Block 1: Gợi ý hôm nay (ngẫu nhiên nhẹ, mỗi lần vào khác nhau) ===== --}}
<section class="mb-5">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0"><i class="bi bi-stars text-warning"></i> Gợi ý hôm nay</h4>
        <a href="{{ route('products.index') }}" class="text-decoration-none small">Xem tất cả →</a>
    </div>
    <div class="row">
        @foreach($suggestions as $product)
            @include('components.product-card', ['product' => $product])
        @endforeach
    </div>
</section>

{{-- ===== Block 2: Sản phẩm bán chạy ===== --}}
<section class="mb-5">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0"><i class="bi bi-fire text-danger"></i> Sản phẩm bán chạy</h4>
        <span class="text-muted small">Xếp theo số lượng đã bán</span>
    </div>
    <div class="row">
        @foreach($bestSellers as $product)
            @include('components.product-card', ['product' => $product])
        @endforeach
    </div>
</section>

{{-- ===== Block 3: Sản phẩm giá rẻ ===== --}}
<section class="mb-5">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0"><i class="bi bi-tag text-success"></i> Sản phẩm giá rẻ</h4>
        <span class="text-muted small">Giá sau giảm thấp nhất</span>
    </div>
    <div class="row">
        @foreach($cheapest as $product)
            @include('components.product-card', ['product' => $product])
        @endforeach
    </div>
</section>
@endsection
