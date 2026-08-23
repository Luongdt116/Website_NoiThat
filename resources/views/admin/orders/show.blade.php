@extends('layouts.app')

@section('title', 'Xử lý đơn #'.$order->id)

@section('content')
@php
// Nhãn + màu badge cho từng trạng thái đơn
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
    <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-wood mb-0"><i class="bi bi-receipt-cutoff"></i> Đơn hàng #{{ $order->id }}</h2>
            <span class="badge bg-{{ $color }} fs-6">{{ $label }}</span>
        </div>

        {{-- Thông tin khách hàng + giao hàng --}}
        <div class="card p-4 mb-4">
            <h5><i class="bi bi-person"></i> Khách hàng</h5>
            <p class="mb-1"><strong>Người đặt:</strong> {{ $order->user->name }} ({{ $order->user->email }})</p>
            <p class="mb-1"><strong>Điện thoại:</strong> {{ $order->phone }}</p>
            <p class="mb-1"><strong>Địa chỉ:</strong> {{ $order->address }}</p>
            <p class="mb-0"><strong>Đặt lúc:</strong> {{ $order->created_at->format('d/m/Y H:i') }} ·
                <strong>Thanh toán:</strong> <span class="badge bg-secondary">COD</span></p>
        </div>

        {{-- Danh sách sản phẩm trong đơn --}}
        <div class="card p-4 mb-4">
            <h5><i class="bi bi-box-seam"></i> Sản phẩm</h5>
            @foreach($order->items as $it)
                <div class="d-flex justify-content-between border-bottom py-2">
                    <span>{{ $it->product?->name ?? '(sản phẩm đã xóa)' }} × {{ $it->quantity }}
                        @if($it->product)
                            <a href="{{ route('products.show', $it->product->id) }}" class="text-muted small ms-1">xem</a>
                        @endif
                    </span>
                    <span>{{ number_format($it->price * $it->quantity, 0, ',', '.') }} ₫</span>
                </div>
            @endforeach
            <div class="d-flex justify-content-between pt-3 fs-5">
                <span class="fw-bold">Tổng cộng:</span>
                <span class="fw-bold text-wood">{{ number_format($order->total, 0, ',', '.') }} ₫</span>
            </div>
        </div>

        {{-- Hành động xử lý đơn — POST đúng chuẩn, ẩn nút không phù hợp trạng thái hiện tại --}}
        <div class="d-flex gap-2 flex-wrap mb-3">
            @if($order->status === 'pending')
                <form method="POST" action="{{ route('admin.orders.confirm', $order->id) }}"
                      onsubmit="return confirm('Xác nhận đơn này?')">@csrf
                    <button class="btn btn-success"><i class="bi bi-check2"></i> Xác nhận đơn</button>
                </form>
                <form method="POST" action="{{ route('admin.orders.cancel', $order->id) }}"
                      onsubmit="return confirm('Hủy đơn hàng này? Tồn kho sẽ được hoàn lại.')">@csrf
                    <button class="btn btn-outline-danger"><i class="bi bi-x-lg"></i> Hủy đơn</button>
                </form>
            @elseif($order->status === 'confirmed')
                <form method="POST" action="{{ route('admin.orders.ship', $order->id) }}"
                      onsubmit="return confirm('Chuyển sang giao hàng?')">@csrf
                    <button class="btn btn-primary"><i class="bi bi-truck"></i> Bắt đầu giao hàng</button>
                </form>
            @elseif($order->status === 'shipping')
                <form method="POST" action="{{ route('admin.orders.complete', $order->id) }}"
                      onsubmit="return confirm('Đánh dấu hoàn tất?')">@csrf
                    <button class="btn text-white" style="background-color: var(--wood);"><i class="bi bi-check2-circle"></i> Hoàn tất</button>
                </form>
            @else
                <span class="text-muted fst-italic">Đơn đã ở trạng thái cuối — {{ strtolower($label) }}.</span>
            @endif
        </div>

        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Về danh sách đơn</a>
    </div>
</div>
@endsection
