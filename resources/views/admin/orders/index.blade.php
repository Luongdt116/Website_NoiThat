@extends('layouts.app')

@section('title', 'Quản lý đơn hàng')

@section('content')
<h2 class="text-wood mb-4"><i class="bi bi-receipt-cutoff"></i> Quản lý đơn hàng</h2>

@php
$statusMap = [
    'pending'   => ['Chờ xác nhận', 'warning'],
    'confirmed' => ['Đã xác nhận', 'info'],
    'shipping'  => ['Đang giao', 'primary'],
    'completed' => ['Hoàn tất', 'success'],
    'cancelled' => ['Đã hủy', 'danger'],
];
@endphp

<table class="table bg-white shadow-sm align-middle">
    <thead class="table-light">
        <tr><th>Mã</th><th>Khách hàng</th><th>Ngày đặt</th><th>Tổng tiền</th><th>Trạng thái</th><th></th></tr>
    </thead>
    <tbody>
    @forelse($orders as $o)
        @php [$label, $color] = $statusMap[$o->status] ?? [$o->status, 'secondary']; @endphp
        <tr>
            <td class="fw-bold">#{{ $o->id }}</td>
            <td>{{ $o->user->name }}</td>
            <td>{{ $o->created_at->format('d/m/Y H:i') }}</td>
            <td>{{ number_format($o->total, 0, ',', '.') }} ₫</td>
            <td><span class="badge bg-{{ $color }}">{{ $label }}</span></td>
            <td><a href="{{ route('admin.orders.show', $o->id) }}" class="btn btn-sm btn-outline-secondary">Xem / Xử lý</a></td>
        </tr>
    @empty
        <tr><td colspan="6" class="text-center text-muted py-4">Chưa có đơn hàng nào.</td></tr>
    @endforelse
    </tbody>
</table>
<div class="d-flex justify-content-center">{{ $orders->links() }}</div>
@endsection
