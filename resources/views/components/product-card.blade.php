@props(['product'])

{{-- ===== Card sản phẩm dùng chung (trang chủ, tìm kiếm, danh mục) ===== --}}
<div class="col-6 col-md-4 col-lg-3 mb-4" {{ $attributes }}>
    <div class="card h-100 product-card">
        {{-- Ảnh sản phẩm hoặc placeholder nếu chưa có ảnh --}}
        <a href="{{ route('products.show', $product->id) }}" class="text-decoration-none">
            <div class="product-img-wrapper bg-light d-flex align-items-center justify-content-center">
                @if($product->image)
                    <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="card-img-top product-img">
                @else
                    <i class="bi bi-image text-secondary py-5" style="font-size:3rem"></i>
                @endif
            </div>
        </a>
        <div class="card-body d-flex flex-column">
            <span class="badge bg-secondary align-self-start mb-2">{{ $product->category->name ?? 'Khác' }}</span>
            <a href="{{ route('products.show', $product->id) }}" class="text-decoration-none text-dark">
                <h6 class="card-title">{{ $product->name }}</h6>
            </a>
            <p class="card-text text-wood fw-bold mt-auto mb-2">
                {{ number_format($product->price, 0, ',', '.') }} ₫
            </p>
            {{-- Nút thêm giỏ: chỉ hiện khi đăng nhập; hết hàng thì vô hiệu hóa --}}
            @auth
                @if($product->stock > 0)
                    <form method="POST" action="{{ route('cart.add') }}">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button class="btn btn-wood btn-sm w-100"><i class="bi bi-cart-plus"></i> Thêm vào giỏ</button>
                    </form>
                @else
                    <span class="btn btn-sm btn-outline-secondary disabled w-100">Hết hàng</span>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm w-100">
                    <i class="bi bi-box-arrow-in-right"></i> Đăng nhập để mua
                </a>
            @endauth
        </div>
    </div>
</div>
