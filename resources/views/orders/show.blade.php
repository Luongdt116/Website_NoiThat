@extends('layouts.app')

@section('title', 'Đơn hàng #'.$order->id)

@section('content')
@php
$statusMap = [
    'pending'   => ['Chờ xác nhận', 'warning'],
    'confirmed' => ['Đã xác nhận', 'info'],
    'shipping'  => ['Đang giao', 'primary'],
    'completed' => ['Hoàn tất', 'success'],
    'cancelled' => ['Đã hủy', 'danger'],
];
[$label, $color] = $statusMap[$order->status] ?? [$order->status, 'secondary'];
@endphp

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-wood mb-0">Đơn hàng #{{ $order->id }}</h2>
            <span class="badge bg-{{ $color }} fs-6">{{ $label }}</span>
        </div>

        {{-- Thông tin giao hàng --}}
        <div class="card p-4 mb-4">
            <h5><i class="bi bi-geo-alt"></i> Thông tin nhận hàng</h5>
            <p class="mb-1"><strong>Người nhận:</strong> {{ $order->user->name }}</p>
            <p class="mb-1"><strong>Điện thoại:</strong> {{ $order->phone }}</p>
            <p class="mb-1"><strong>Địa chỉ:</strong> {{ $order->address }}</p>
            <p class="mb-0"><strong>Thanh toán:</strong> <span class="badge bg-secondary">COD</span> (trả tiền khi nhận hàng)</p>
        </div>

        {{-- Danh sách sản phẩm trong đơn --}}
        <div class="card p-4 mb-4">
            <h5><i class="bi bi-box-seam"></i> Sản phẩm</h5>
            @foreach($order->items as $it)
                <div class="d-flex justify-content-between border-bottom py-2">
                    <span>{{ $it->product->name }} × {{ $it->quantity }}</span>
                    <span>{{ number_format($it->price * $it->quantity, 0, ',', '.') }} ₫</span>
                </div>
            @endforeach
            <div class="d-flex justify-content-between pt-3 fs-5">
                <span class="fw-bold">Tổng cộng:</span>
                <span class="fw-bold text-wood">{{ number_format($order->total, 0, ',', '.') }} ₫</span>
            </div>
        </div>

        <a href="{{ route('orders.history') }}" class="btn btn-outline-secondary">← Đơn của tôi</a>
        <a href="{{ route('products.index') }}" class="btn btn-wood">Tiếp tục mua sắm</a>
    </div>
</div>
@endsection
