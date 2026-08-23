@extends('layouts.app')
@section('content')
<div class="container mt-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Quản lý người dùng</h2>
    <span class="text-muted small">Tổng: {{ count($users) }} {{ count($users) > 1 ? 'người' : 'người' }}</span>
  </div>
  <div class="card shadow-sm" style="max-width: 900px;">
    <div class="card-header bg-white border-bottom">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>Tên</th>
            <th>Email</th>
            <th>Trạng thái</th>
            <th style="width: 160px;">Hành động</th>
          </tr>
        </thead>
        <tbody>@foreach($users as $u)<tr>
          <td>{{ $u->name }}</td><td>{{ $u->email }}</td>
          <td>
            @if($u->is_active)
              <span class="badge bg-success">Hoạt động</span>
            @else
              <span class="badge bg-danger">Đã khóa</span>
            @endif
          </td>
          <td>
            <form method="POST" action="{{ route('admin.users.toggle', $u->id) }}" class="d-inline" onsubmit="return confirm('Đổi trạng thái tài khoản này?')">
              @csrf
              <button class="btn btn-sm {{ $u->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}" title="{{ $u->is_active ? 'Khóa' : 'Mở khóa' }}">
                  {{ $u->is_active ? '<i class="bi bi-lock"></i>' : '<i class="bi bi-unlock"></i>' }}
              </button>
            </form>
            @if($u->is_admin)<span class="badge bg-secondary ms-1">Admin</span>@endif
          </td>
        </tr>@endforeach</tbody>
      </table>
    </div>
  </div>
</div>
@endsection