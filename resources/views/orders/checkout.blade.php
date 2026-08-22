@extends('layouts.app')

@section('title', 'Thanh toán COD')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <h2 class="text-wood mb-4"><i class="bi bi-truck"></i> Thông tin nhận hàng (COD)</h2>

        {{-- Tóm tắt giỏ hàng trước khi đặt --}}
        <div class="card p-3 mb-4">
            <h6 class="text-muted">Đơn hàng của bạn</h6>
            @php $total = 0; @endphp
            @foreach($items as $i)
                @php $total += $i->product->price * $i->quantity; @endphp
                <div class="d-flex justify-content-between border-bottom py-2">
                    <span>{{ $i->product->name }} × {{ $i->quantity }}</span>
                    <span>{{ number_format($i->product->price * $i->quantity, 0, ',', '.') }} ₫</span>
                </div>
            @endforeach
            <div class="d-flex justify-content-between pt-3 fs-5">
                <span class="fw-bold">Tổng cộng:</span>
                <span class="fw-bold text-wood">{{ number_format($total, 0, ',', '.') }} ₫</span>
            </div>
        </div>

        {{-- Form địa chỉ + SĐT; thanh toán là COD mô phỏng theo spec --}}
        <form method="POST" action="{{ route('orders.store') }}" class="card p-4">
            @csrf
            <div class="mb-3">
                <label class="form-label">Địa chỉ nhận hàng <span class="text-danger">*</span></label>
                <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror" required placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành">{{ old('address') }}</textarea>
                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                <input name="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror" required placeholder="VD: 0901234567">
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="alert alert-info">
                <i class="bi bi-cash"></i> <strong>Thanh toán khi nhận hàng (COD)</strong> — bạn chỉ trả tiền cho người giao hàng.
            </div>
            <button class="btn btn-wood btn-lg"><i class="bi bi-check2-circle"></i> Xác nhận đặt hàng</button>
        </form>
    </div>
</div>
@endsection
