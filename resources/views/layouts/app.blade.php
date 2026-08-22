<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Bootstrap 5 + Icons qua CDN — đồ án không cần npm build --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <title>@yield('title', 'Furniture Store — Nội thất trực tuyến')</title>
    <style>
        /* Ghi đè màu Bootstrap theo tông nội thất: nâu gỗ + be ấm */
        :root {
            --wood: #8b5e34;
            --wood-dark: #6f4a28;
            --cream: #faf6f0;
        }
        body { background-color: var(--cream); }
        .navbar-brand { color: var(--wood); font-weight: 700; }
        .btn-wood { background-color: var(--wood); border-color: var(--wood); color: #fff; }
        .btn-wood:hover { background-color: var(--wood-dark); border-color: var(--wood-dark); color: #fff; }
        .text-wood { color: var(--wood); }
        .card { border: none; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
    </style>
    @stack('styles')
</head>
<body>
{{-- ===== Navbar dùng chung toàn site ===== --}}
<nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}"><i class="bi bi-tree"></i> Furniture Store</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Trang chủ</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}">Sản phẩm</a></li>
            </ul>
            {{-- Thanh tìm kiếm nhanh (từ khóa) --}}
            <form class="d-flex me-lg-3" method="GET" action="{{ route('products.index') }}" role="search">
                <input class="form-control me-2" type="search" name="keyword" placeholder="Tìm nội thất..." value="{{ request('keyword') }}">
                <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
            </form>
            <ul class="navbar-nav">
                @auth
                    <li class="nav-item"><a class="nav-link" href="{{ route('cart.index') }}"><i class="bi bi-cart"></i> Giỏ hàng</a></li>
                    @if(auth()->user()->is_admin)
                        {{-- Menu quản trị: chỉ hiện với admin --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-wood" href="#" role="button" data-bs-toggle="dropdown">Quản trị</a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.categories.index') }}">Danh mục</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.products.index') }}">Sản phẩm</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}">Đơn hàng</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.users.index') }}">Người dùng</a></li>
                            </ul>
                        </li>
                    @endif
                    <li class="nav-item"><a class="nav-link" href="{{ route('orders.history') }}">Đơn của tôi</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">{{ auth()->user()->name }}</a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                {{-- Nút đăng xuất dùng form POST (bắt buộc của Laravel) --}}
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Đăng xuất</a>
                            </li>
                        </ul>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Đăng nhập</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Đăng ký</a></li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

<main class="container py-4">
    {{-- Thông báo thành công / lỗi từ session flash --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

<footer class="border-top mt-5 py-4 text-center text-muted bg-white">
    <div class="container">
        <p class="mb-0">© {{ date('Y') }} Furniture Store — Đồ án Website bán đồ nội thất trực tuyến</p>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
