@extends('layouts.app')
@section('content')
<h2>Quản lý người dùng</h2>
<table class="table">
  <thead><tr><th>Tên</th><th>Email</th><th>Trạng thái</th><th></th></tr></thead>
  <tbody>@foreach($users as $u)<tr>
    <td>{{ $u->name }}</td><td>{{ $u->email }}</td>
    <td>{{ $u->is_active ? 'Hoạt động' : 'Khóa' }}</td>
    <td>
        {{-- Khóa/Mở là hành động thay đổi dữ liệu -> phải POST --}}
        <form method="POST" action="{{ route('admin.users.toggle', $u->id) }}" class="d-inline" onsubmit="return confirm('Đổi trạng thái tài khoản này?')">
            @csrf
            <button class="btn btn-sm {{ $u->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}">
                {{ $u->is_active ? '🔒 Khóa' : '🔓 Mở khóa' }}
            </button>
        </form>
        @if($u->is_admin)<span class="badge bg-dark">Admin</span>@endif
    </td>
  </tr>@endforeach</tbody>
</table>
@endsection
