@extends('layouts.app')

@section('title', 'Giỏ hàng')

@section('content')
<h2 class="text-wood mb-4"><i class="bi bi-cart3"></i> Giỏ hàng của bạn</h2>

@if($items->isEmpty())
    <div class="text-center text-muted py-5">
        <i class="bi bi-cart-x" style="font-size:4rem"></i>
        <h5 class="mt-3">Giỏ hàng trống</h5>
        <p>Hãy duyệt <a href="{{ route('products.index') }}">danh sách sản phẩm</a> để chọn món đồ ưng ý nhé!</p>
    </div>
@else
    <div class="row g-4">
        {{-- Danh sách sản phẩm trong giỏ --}}
        <div class="col-lg-8">
            @foreach($items as $i)
                <div class="card mb-3">
                    <div class="card-body d-flex align-items-center gap-3">
                        {{-- Ảnh nhỏ hoặc icon placeholder --}}
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:80px;height:80px;flex-shrink:0">
                            @if($i->product->image)
                                <img src="{{ asset('storage/'.$i->product->image) }}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:.25rem">
                            @else
                                <i class="bi bi-image text-secondary" style="font-size:2rem"></i>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <a href="{{ route('products.show', $i->product->id) }}" class="text-decoration-none text-dark fw-bold">{{ $i->product->name }}</a>
                            <div class="text-muted small">{{ number_format($i->product->price, 0, ',', '.') }} ₫ / sản phẩm</div>
                        </div>
                        {{-- Form cập nhật số lượng (POST) --}}
                        <form method="POST" action="{{ route('cart.update', $i->id) }}" class="d-flex gap-2 align-items-center">
                            @csrf
                            <input type="number" name="quantity" value="{{ $i->quantity }}" min="1" max="{{ $i->product->stock }}" class="form-control" style="width:90px">
                            <button class="btn btn-outline-secondary btn-sm">Cập nhật</button>
                        </form>
                        <div class="text-wood fw-bold" style="min-width:120px;text-align:right">
                            {{ number_format($i->product->price * $i->quantity, 0, ',', '.') }} ₫
                        </div>
                        <a href="{{ route('cart.remove', $i->id) }}" class="btn btn-outline-danger btn-sm" title="Xóa"><i class="bi bi-trash"></i></a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Tóm tắt đơn hàng --}}
        <div class="col-lg-4">
            <div class="card p-4 sticky-top" style="top:90px">
                <h5>Tóm tắt đơn hàng</h5>
                @php $total = $items->sum(fn($i) => $i->product->price * $i->quantity); @endphp
                <div class="d-flex justify-content-between mt-3">
                    <span class="text-muted">Sản phẩm:</span>
                    <span>{{ $items->sum('quantity') }}</span>
                </div>
                <div class="d-flex justify-content-between mt-2 fs-5">
                    <span class="fw-bold">Tạm tính:</span>
                    <span class="fw-bold text-wood">{{ number_format($total, 0, ',', '.') }} ₫</span>
                </div>
                <hr>
                <a href="{{ route('orders.checkout') }}" class="btn btn-wood w-100"><i class="bi bi-truck"></i> Tiến hành đặt hàng (COD)</a>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100 mt-2">← Tiếp tục mua sắm</a>
            </div>
        </div>
    </div>
@endif
@endsection
