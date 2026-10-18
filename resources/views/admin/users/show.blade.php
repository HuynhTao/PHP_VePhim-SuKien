@extends('layouts.app')
@section('title', 'Chi tiết tài khoản')
@section('content')
<section class="form-card account-card"><a class="back-link" href="{{ route('admin.users.index') }}">← Danh sách tài khoản</a>
<h1>{{ $user->ho_ten }}</h1><span class="role-badge">{{ $user->isAdmin() ? 'Admin' : 'User' }}</span>
<dl><dt>Email</dt><dd>{{ $user->email }}</dd><dt>Số điện thoại</dt><dd>{{ $user->so_dien_thoai ?? 'Chưa cung cấp' }}</dd><dt>Ngày tạo</dt><dd>{{ $user->created_at?->format('d/m/Y H:i') }}</dd><dt>Số đơn hàng</dt><dd>{{ $user->don_hangs_count }}</dd></dl>
<a class="btn" href="{{ route('admin.users.edit', $user) }}">Chỉnh sửa tài khoản</a>
@if(!$user->is(auth()->user()) && $user->don_hangs_count === 0)
<details class="danger-zone"><summary>Xóa tài khoản</summary><p>Thao tác này xóa vĩnh viễn tài khoản {{ $user->email }}. Bạn chắc chắn muốn xóa?</p><form method="POST" action="{{ route('admin.users.destroy', $user) }}">@csrf @method('DELETE')<button class="btn btn-danger">Xác nhận xóa tài khoản</button></form></details>
@else<p class="muted form-foot">Không thể xóa tài khoản đang đăng nhập hoặc tài khoản đã có đơn hàng.</p>@endif
</section>
@endsection

