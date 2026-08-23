@extends('layouts.app')

@section('title', 'Quản lý người dùng')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-wood mb-0"><i class="bi bi-people"></i> Quản lý người dùng</h2>
    <span class="text-muted">Tổng: {{ $users->total() }} người dùng</span>
</div>

<div class="card shadow-sm">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>Tên</th>
                <th>Email</th>
                <th>Trạng thái</th>
                <th>Vai trò</th>
                <th style="width: 160px;">Hành động</th>
            </tr>
        </thead>
        <tbody>
        @forelse($users as $u)
            <tr>
                <td class="fw-bold">{{ $u->name }}</td>
                <td>{{ $u->email }}</td>
                <td>
                    @if($u->is_active)
                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Hoạt động</span>
                    @else
                        <span class="badge bg-danger"><i class="bi bi-slash-circle"></i> Đã khóa</span>
                    @endif
                </td>
                <td>
                    @if($u->is_admin)
                        <span class="badge text-white" style="background-color: var(--wood);">Admin</span>
                    @else
                        <span class="badge bg-light text-dark border">Khách hàng</span>
                    @endif
                </td>
                <td>
                    {{-- Khóa/Mở là hành động thay đổi dữ liệu -> phải POST --}}
                    <form method="POST" action="{{ route('admin.users.toggle', $u->id) }}" class="d-inline"
                          onsubmit="return confirm('Đổi trạng thái tài khoản {{ $u->email }}?')">
                        @csrf
                        @if($u->is_active)
                            <button class="btn btn-outline-danger btn-sm" title="Khóa tài khoản">
                                <i class="bi bi-lock"></i> Khóa
                            </button>
                        @else
                            <button class="btn btn-outline-success btn-sm" title="Mở khóa tài khoản">
                                <i class="bi bi-unlock"></i> Mở khóa
                            </button>
                        @endif
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center text-muted py-4">Chưa có người dùng nào.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-center mt-3">{{ $users->links() }}</div>
@endsection
