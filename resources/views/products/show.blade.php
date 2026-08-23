@extends('layouts.app')
@section('content')
<div class="container py-4">
  <div class="row g-4">
    <div class="col-md-6">
      {{-- Ảnh sản phẩm --}}
      <div class="product-img-wrapper bg-light d-flex align-items-center justify-content-center h-75">
        @if($product->image)
          <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="card-img-top product-img img-fluid" style="max-height: 400px; object-fit: contain;">
        @else
          <i class="bi bi-image text-secondary py-5" style="font-size:3rem"></i>
        @endif
      </div>
    </div>
    <div class="col-md-6">
      {{-- Thông tin sản phẩm --}}
      <h2 class="mb-3">{{ $product->name }}</h2>

      {{-- Thông tin phụ --}}
      <p class="text-muted mb-3">
        <i class="bi bi-tags me-1"></i> {{ $product->category->name ?? 'Khác' }}
      </p>

      {{-- Giá nổi bật: giá gốc gạch ngang + giá sau giảm khi có khuyến mãi --}}
      @if($product->hasDiscount())
        <div class="d-flex align-items-center gap-2 mb-2">
          <span class="badge bg-danger fs-6">-{{ $product->discount_percent }}%</span>
          <span class="text-decoration-line-through text-muted">{{ number_format($product->price, 0, ',', '.') }} ₫</span>
        </div>
        <h4 class="text-danger" style="font-size:1.8rem;">{{ number_format($product->final_price, 0, ',', '.') }} ₫
          <small class="text-success fs-6 fw-normal">(tiết kiệm {{ number_format($product->price - $product->final_price, 0, ',', '.') }} ₫)</small>
        </h4>
      @else
        <h4 style="color:var(--wood); font-size:1.8rem;">{{ number_format($product->price, 0, ',', '.') }} ₫</h4>
      @endif

      {{-- Tồn kho --}}
      <p class="mb-3">
        <i class="bi bi-box-seam me-1"></i> Tồn kho: {{ $product->stock }}
        @if($product->stock <= 5)
          <span class="badge bg-warning text-dark ms-2">Sắp hết hàng</span>
        @endif
      </p>

      {{-- Mô tả --}}
      <p class="mb-3">{{ $product->description }}</p>

      {{-- Chất liệu --}}
      @if($product->material)
        <p class="text-muted small mb-3"><i class="bi bi-capsule me-1"></i> Chất liệu: {{ $product->material }}</p>
      @endif

      {{-- Form thêm giỏ --}}
      @auth
        @if($product->stock > 0)
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label small">Số lượng</label>
              <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-6">
              <form method="POST" action="{{ route('cart.add') }}" class="d-inline">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <button class="btn btn-wood w-100 small">Thêm vào giỏ</button>
              </form>
            </div>
          </div>
        @else
          <p class="text-danger small fw-bold">Hết hàng</p>
        @endif
      @else
        <a href="{{ route('login') }}" class="btn btn-outline-secondary w-100 small">Đăng nhập để mua</a>
      @endauth
    </div>
  </div>
</div>
@endsection