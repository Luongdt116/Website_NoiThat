@extends('layouts.app')

@section('title', 'Dashboard quản trị')

@section('content')
<h2 class="text-wood mb-4"><i class="bi bi-speedometer2"></i> Dashboard</h2>

{{-- ===== 4 thẻ thống kê chính ===== --}}
<div class="row g-4 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card text-center p-4 h-100">
            <i class="bi bi-receipt text-primary" style="font-size:2.5rem"></i>
            <h3 class="mt-2 mb-0">{{ $stats['orders'] }}</h3>
            <p class="text-muted mb-0">Đơn hàng</p>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card text-center p-4 h-100">
            <i class="bi bi-cash-stack" style="font-size:2.5rem;color:var(--wood)"></i>
            <h3 class="mt-2 mb-0 text-wood" style="font-size:1.5rem">{{ number_format($stats['revenue'], 0, ',', '.') }} ₫</h3>
            <p class="text-muted mb-0">Doanh thu (chưa tính đơn hủy)</p>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card text-center p-4 h-100">
            <i class="bi bi-box-seam text-success" style="font-size:2.5rem"></i>
            <h3 class="mt-2 mb-0">{{ $stats['products'] }}</h3>
            <p class="text-muted mb-0">Sản phẩm</p>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card text-center p-4 h-100">
            <i class="bi bi-people text-info" style="font-size:2.5rem"></i>
            <h3 class="mt-2 mb-0">{{ $stats['users'] }}</h3>
            <p class="text-muted mb-0">Người dùng</p>
        </div>
    </div>
</div>

{{-- ===== Liên kết nhanh tới các khu quản lý ===== --}}
<div class="card p-4">
    <h5>Quản trị nhanh</h5>
    <div class="d-flex flex-wrap gap-2 mt-3">
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary"><i class="bi bi-tags"></i> Danh mục</a>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary"><i class="bi bi-box-seam"></i> Sản phẩm</a>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary"><i class="bi bi-receipt"></i> Đơn hàng</a>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary"><i class="bi bi-people"></i> Người dùng</a>
    </div>
</div>
@endsection
