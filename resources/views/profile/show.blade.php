@extends('layouts.app')

@section('title', 'Tài khoản của tôi')

@section('content')
<h2 class="text-wood mb-4"><i class="bi bi-person-circle"></i> Tài khoản của tôi</h2>

<div class="row g-4">
    {{-- Cập nhật thông tin cơ bản --}}
    <div class="col-lg-6">
        <div class="card p-4 h-100">
            <h5><i class="bi bi-person"></i> Thông tin cá nhân</h5>
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Họ và tên</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="text-muted small mb-3">
                    <i class="bi bi-info-circle"></i> Vai trò: {{ $user->is_admin ? 'Quản trị viên' : 'Khách hàng' }}
                    · Thành viên từ {{ $user->created_at->format('d/m/Y') }}
                </div>
                <button class="btn btn-wood"><i class="bi bi-check2"></i> Lưu thay đổi</button>
            </form>
        </div>
    </div>

    {{-- Đổi mật khẩu: form riêng để validate độc lập --}}
    <div class="col-lg-6">
        <div class="card p-4 h-100">
            <h5><i class="bi bi-shield-lock"></i> Đổi mật khẩu</h5>
            <form method="POST" action="{{ route('profile.password') }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Mật khẩu hiện tại</label>
                    <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                    @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Mật khẩu mới</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required minlength="6">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Nhập lại mật khẩu mới</label>
                    <input type="password" name="password_confirmation" class="form-control" required minlength="6">
                </div>
                <button class="btn btn-outline-danger"><i class="bi bi-key"></i> Đổi mật khẩu</button>
            </form>
        </div>
    </div>
</div>
@endsection
