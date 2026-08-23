@extends('layouts.app')

@section('title', 'Khuyến mãi hôm nay')

@section('content')
<div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
    <h2 class="mb-0"><i class="bi bi-percent text-danger"></i> Khuyến mãi hôm nay</h2>
    <span class="text-muted">Có <strong>{{ $products->total() }}</strong> sản phẩm đang giảm giá</span>
</div>

@if($products->isEmpty())
    <div class="alert alert-info">Hiện chưa có sản phẩm nào đang giảm giá. Quay lại sau nhé!</div>
@else
    {{-- Grid card sản phẩm: badge -N% + giá gốc gạch ngang tự hiện qua product-card --}}
    <div class="row">
        @foreach($products as $product)
            @include('components.product-card', ['product' => $product])
        @endforeach
    </div>
@endif

{{-- Phân trang giữ nguyên query string --}}
{{ $products->withQueryString()->links() }}
@endsection
