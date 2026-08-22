@extends('layouts.app')

@section('title', 'Đơn hàng của tôi')

@section('content')
<h2 class="text-wood mb-4"><i class="bi bi-receipt"></i> Đơn hàng của tôi</h2>

@php
// Nhãn + màu badge cho từng trạng thái đơn
$statusMap = [
    'pending'   => ['Chờ xác nhận', 'warning'],
    'confirmed' => ['Đã xác nhận', 'info'],
    'shipping'  => ['Đang giao', 'primary'],
    'completed' => ['Hoàn tất', 'success'],
    'cancelled' => ['Đã hủy', 'danger'],
];
@endphp

@if($orders->isEmpty())
    <div class="text-center text-muted py-5">
        <i class="bi bi-journal-x" style="font-size:4rem"></i>
        <h5 class="mt-3">Bạn chưa có đơn hàng nào</h5>
        <a href="{{ route('products.index') }}" class="btn btn-wood mt-2">Mua sắm ngay</a>
    </div>
@else
    <div class="card">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr><th>Mã đơn</th><th>Ngày đặt</th><th>Tổng tiền</th><th>Trạng thái</th><th>Thanh toán</th><th></th></tr>
            </thead>
            <tbody>
            @foreach($orders as $o)
                @php [$label, $color] = $statusMap[$o->status] ?? [$o->status, 'secondary']; @endphp
                <tr>
                    <td class="fw-bold">#{{ $o->id }}</td>
                    <td>{{ $o->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ number_format($o->total, 0, ',', '.') }} ₫</td>
                    <td><span class="badge bg-{{ $color }}">{{ $label }}</span></td>
                    <td><span class="badge bg-secondary">COD</span></td>
                    <td><a href="{{ route('orders.show', $o->id) }}" class="btn btn-sm btn-outline-secondary">Chi tiết</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
