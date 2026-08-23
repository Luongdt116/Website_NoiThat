@extends('layouts.app')

@section('title', 'Giỏ hàng')

@section('content')
@php
    // Dòng giỏ vượt tồn kho hiện tại (có thể do khách khác mua mất một phần)
    $overStock = $items->filter(fn($i) => $i->product && $i->quantity > $i->product->stock);
    // Chỉ món được tick mới tính vào tóm tắt + đặt hàng
    $checked = $items->filter(fn($i) => $i->selected);
@endphp
<h2 class="text-wood mb-4"><i class="bi bi-cart3"></i> Giỏ hàng của bạn</h2>

@if($items->isEmpty())
    <div class="text-center text-muted py-5">
        <i class="bi bi-cart-x" style="font-size:4rem"></i>
        <h5 class="mt-3">Giỏ hàng trống</h5>
        <p>Hãy duyệt <a href="{{ route('products.index') }}">danh sách sản phẩm</a> để chọn món đồ ưng ý nhé!</p>
    </div>
@else
    @if($overStock->isNotEmpty())
        <div class="alert alert-warning d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div>Một số sản phẩm trong giỏ vừa giảm số lượng còn lại trong kho (khách khác đã mua trước). Hãy cập nhật lại số lượng trước khi đặt hàng.</div>
        </div>
    @endif
    {{-- Chọn tất cả --}}
    <div class="form-check mb-3 ms-1">
        <input class="form-check-input" type="checkbox" id="check-all"
               {{ $checked->count() === $items->count() ? 'checked' : '' }}>
        <label class="form-check-label fw-bold" for="check-all">
            Chọn tất cả ({{ $items->count() }} sản phẩm)
        </label>
    </div>
    <div class="row g-4">
        {{-- Danh sách sản phẩm trong giỏ --}}
        <div class="col-lg-8">
            @foreach($items as $i)
                <div class="card mb-3 {{ $i->product && $i->quantity > $i->product->stock ? 'border-danger' : '' }}">
                    <div class="card-body d-flex align-items-center gap-3">
                        {{-- Tick chọn món nào sẽ thanh toán --}}
                        <input class="form-check-input item-check flex-shrink-0" type="checkbox" style="width:1.25rem;height:1.25rem"
                               data-id="{{ $i->id }}" aria-label="Chọn {{ $i->product?->name }}" {{ $i->selected ? 'checked' : '' }}>
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
                            @if($i->quantity > $i->product->stock)
                                <span class="badge bg-danger"><i class="bi bi-exclamation-triangle"></i> Chỉ còn {{ $i->product->stock }} trong kho</span>
                            @endif
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

        {{-- Tóm tắt đơn hàng: chỉ tính món đang được tick --}}
        <div class="col-lg-4">
            <div class="card p-4 sticky-top" style="top:90px">
                <h5>Tóm tắt đơn hàng</h5>
                @php $total = $checked->sum(fn($i) => $i->product->price * $i->quantity); @endphp
                <div class="d-flex justify-content-between mt-3">
                    <span class="text-muted">Sản phẩm được chọn:</span>
                    <span>{{ $checked->sum('quantity') }} / {{ $items->sum('quantity') }}</span>
                </div>
                <div class="d-flex justify-content-between mt-2 fs-5">
                    <span class="fw-bold">Tạm tính:</span>
                    <span class="fw-bold text-wood">{{ number_format($total, 0, ',', '.') }} ₫</span>
                </div>
                <hr>
                @if($overStock->isNotEmpty())
                    {{-- Chặn đặt hàng khi giỏ có dòng vượt kho — tránh đặt xong lại bị từ chối --}}
                    <button class="btn btn-wood w-100" disabled title="Cập nhật số lượng sản phẩm vượt kho trước khi đặt">
                        <i class="bi bi-truck"></i> Tiến hành đặt hàng (COD)
                    </button>
                    <div class="text-danger small mt-1 text-center"><i class="bi bi-info-circle"></i> Cập nhật số lượng trước khi đặt hàng</div>
                @elseif($checked->isEmpty())
                    {{-- Chưa tick món nào thì không có gì để đặt --}}
                    <button class="btn btn-wood w-100" disabled>
                        <i class="bi bi-truck"></i> Tiến hành đặt hàng (COD)
                    </button>
                    <div class="text-muted small mt-1 text-center"><i class="bi bi-info-circle"></i> Hãy chọn ít nhất một sản phẩm để đặt</div>
                @else
                    <a href="{{ route('orders.checkout') }}" class="btn btn-wood w-100"><i class="bi bi-truck"></i> Tiến hành đặt hàng (COD)</a>
                @endif
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100 mt-2">← Tiếp tục mua sắm</a>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Gửi tick/bỏ tick lên server ngay khi bấm checkbox (AJAX, giữ nguyên vị trí trang)
        document.querySelectorAll('.item-check').forEach(function (box) {
            box.addEventListener('change', function () {
                fetch('{{ url('gio-hang') }}/' + this.dataset.id + '/chon', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ selected: this.checked ? 1 : 0 }),
                }).then(function (res) {
                    if (res.ok) { location.reload(); }   // reload để tóm tắt tiền cập nhật
                });
            });
        });
        // Nút "Chọn tất cả": tick/bỏ tick mọi dòng rồi lưu lên server lần lượt
        var checkAll = document.getElementById('check-all');
        if (checkAll) {
            checkAll.addEventListener('change', function () {
                var boxes = document.querySelectorAll('.item-check');
                var target = this.checked;
                boxes.forEach(function (b) { if (b.checked !== target) b.click(); });
            });
        }
    </script>
    @endpush
@endif
@endsection
