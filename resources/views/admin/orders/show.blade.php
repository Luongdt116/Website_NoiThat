@extends('layouts.app')
@section('content')
<h2>Đơn #{{ $order->id }}</h2>
<p>Khách: {{ $order->user->name }} · {{ $order->phone }} · {{ $order->address }}</p>
<p>Trạng thái: {{ $order->status }}</p>
<table class="table">
  @foreach($order->items as $it)<tr><td>{{ $it->product->name }}</td><td>x{{ $it->quantity }}</td><td>{{ number_format($it->price) }} ₫</td></tr>@endforeach
</table>
<div class="d-flex gap-2 mt-3">
    {{-- Các nút thay đổi trạng thái phải dùng form POST (routes định nghĩa Route::post) --}}
    <form method="POST" action="{{ route('admin.orders.confirm', $order->id) }}" onsubmit="return confirm('Xác nhận đơn này?')">
        @csrf
        <button class="btn btn-success">✔ Xác nhận</button>
    </form>
    <form method="POST" action="{{ route('admin.orders.ship', $order->id) }}" onsubmit="return confirm('Chuyển sang giao hàng?')">
        @csrf
        <button class="btn btn-info">🚚 Giao hàng</button>
    </form>
    <form method="POST" action="{{ route('admin.orders.complete', $order->id) }}" onsubmit="return confirm('Đánh dấu hoàn tất?')">
        @csrf
        <button class="btn btn-primary">✅ Hoàn tất</button>
    </form>
    <form method="POST" action="{{ route('admin.orders.cancel', $order->id) }}" onsubmit="return confirm('Hủy đơn hàng này?')">
        @csrf
        <button class="btn btn-danger">✖ Hủy đơn</button>
    </form>
</div>
<a href="{{ route('admin.orders.index') }}" class="btn btn-secondary mt-3">← Về danh sách đơn</a>
@endsection
