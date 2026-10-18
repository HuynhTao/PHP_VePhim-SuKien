@extends('layouts.app')
@section('title', 'Quản lý tài khoản')
@section('content')
<section class="admin-page">
<div class="section-heading"><div><span class="eyebrow">QUẢN TRỊ HỆ THỐNG</span><h1>Quản lý tài khoản</h1><p class="muted">{{ $users->total() }} tài khoản phù hợp</p></div><a class="btn" href="{{ route('admin.users.create') }}">+ Thêm tài khoản</a></div>
<form class="filter-bar" method="GET">
<label class="sr-only" for="q">Tìm tài khoản</label><input id="q" name="q" value="{{ request('q') }}" placeholder="Tìm theo tên hoặc email" maxlength="100">
<label class="sr-only" for="vai_tro">Vai trò</label><select id="vai_tro" name="vai_tro"><option value="">Tất cả vai trò</option><option value="admin" @selected(request('vai_tro') === 'admin')>Admin</option><option value="user" @selected(request('vai_tro') === 'user')>User</option></select>
<button class="btn">Lọc</button><a href="{{ route('admin.users.index') }}">Đặt lại</a></form>
<div class="table-wrap"><table><thead><tr><th>Họ tên</th><th>Email</th><th>Điện thoại</th><th>Vai trò</th><th>Ngày tạo</th><th>Thao tác</th></tr></thead><tbody>
@forelse($users as $user)<tr><td><a href="{{ route('admin.users.show', $user) }}">{{ $user->ho_ten }}</a>@if($user->is(auth()->user())) <small class="muted">(bạn)</small>@endif</td><td>{{ $user->email }}</td><td>{{ $user->so_dien_thoai ?? '—' }}</td><td><span class="role-badge {{ $user->isAdmin() ? 'role-admin' : '' }}">{{ $user->isAdmin() ? 'Admin' : 'User' }}</span></td><td>{{ $user->created_at?->format('d/m/Y') }}</td><td class="actions"><a href="{{ route('admin.users.show', $user) }}">Xem</a><a href="{{ route('admin.users.edit', $user) }}">Sửa</a></td></tr>
@empty<tr><td colspan="6" class="empty-state">Không có tài khoản phù hợp.</td></tr>@endforelse
</tbody></table></div>
@include('partials.pagination', ['paginator' => $users])
</section>
@endsection

